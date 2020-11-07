<?php
/*
  +----------------------------------------------------------------------+
  | dubbo-php-framework                                                        |
  +----------------------------------------------------------------------+
  | This source file is subject to version 2.0 of the Apache license,    |
  | that is bundled with this package in the file LICENSE, and is        |
  | available through the world-wide-web at the following url:           |
  | http://www.apache.org/licenses/LICENSE-2.0.html                      |
  +----------------------------------------------------------------------+
  | Author: Jinxi Wang  <crazyxman01@gmail.com>                              |
  +----------------------------------------------------------------------+
*/

namespace Dubbo\Provider\Server;

use Dubbo\Common\Logger\LoggerFacade;
use Dubbo\Provider\Service;
use Dubbo\Common\Protocol\Dubbo\DubboProtocol;
use Dubbo\Monitor\MonitorFilter;
use Swoole\Lock;
use Swoole\Process;
use Swoole\Server;
use Swoole\Table;
use Swoole\Coroutine;

class SwooleServer
{
    private $_swServer;
    private $_service;
    private $_monitorFilter;
    private $_pidHandle;

    public function __construct()
    {
        $this->_service = new Service();
    }

    public function startUp()
    {
        if(strstr(PHP_OS,"WIN")) {
            $this->_swServer = new Server('0.0.0.0', cnf('SWOOLE_DUBBO_SERVICE','port'),SWOOLE_BASE);
        } else {
            $this->_swServer = new Server('0.0.0.0', cnf('SWOOLE_DUBBO_SERVICE','port'));
        }

        $this->_swServer->set(cnf('SWOOLE_DUBBO_SERVICE','provider_service_set'));
        $this->onStart();
        $this->onManagerStart();
        $this->onWorkerStart();
        $this->onReceive();
        $this->onTask();
        $this->onFinish();
        $this->_swServer->start();
    }

    public function onStart()
    {
        $this->_swServer->on('Start', function (Server $server) {
            try {
                swoole_set_process_name("php-dubbo-".cnf("SWOOLE_DUBBO_SERVICE","provider_name")."-master");
            } catch (\Exception $e) {
                $e->getMessage();
            }
            echo "Server start......\n";
        });
    }

    public function onManagerStart()
    {
        $this->_swServer->on('ManagerStart', function (Server $server) {
            try {
                swoole_set_process_name("php-dubbo-".cnf("SWOOLE_DUBBO_SERVICE","provider_name")."-manager");
            } catch (\Exception $e) {
                $e->getMessage();
            }


            //$this->registerService();

            echo "Start providing services\n";
        });
    }

    public function onWorkerStart()
    {
        $this->_swServer->on('WorkerStart', function (Server $server, int $worker_id) {
            try {
                if($server->taskworker) {
                    swoole_set_process_name("php-dubbo-".cnf("SWOOLE_DUBBO_SERVICE","provider_name")."-taskworker-{$worker_id}");
                } else {
                    swoole_set_process_name("php-dubbo-".cnf("SWOOLE_DUBBO_SERVICE","provider_name")."-worker-{$worker_id}");
                }

            } catch (\Exception $e) {
                $e->getMessage();
            }

            //$this->_service->load();
        });

    }

    public function onReceive()
    {
        $this->_swServer->on('Receive', function (Server $server, int $fd, int $reactor_id, string $data) {
            $monitorKey = '';
            $startTime = getMillisecond();
            try {
                $protocol = new DubboProtocol();
                $decoder = $protocol->unpackRequest($data);
                if ($protocol->getHeartBeatEvent()) {
                    $result = $this->_service->returnHeartBeat($protocol);
                    goto _result;
                }
                if ($this->_monitorFilter) {
                    $monitorKey = $decoder->getServiceName() . '/' . $decoder->getMethod();
                }

                $method = $decoder->getMethod();

                /*如果是异步方法*/
                if(substr($method,0,6)=='async_') {
                    /*发送异步任务*/
                    $server->task([$protocol,$decoder,$fd,$reactor_id]);
                    $result = $this->_service->returnHeartBeat($protocol,$reactor_id);
                } else {
                    $result = $this->_service->invoke($protocol, $decoder, $server, $fd, $reactor_id);
                }

                if ($monitorKey) {
                    goto _success;
                }
            } catch (\Exception $exception) {
                LoggerFacade::getLogger()->error('Service Exception. ', $exception);
                $result = $this->_service->returnException($protocol, (string)$exception);
                if ($monitorKey) {
                    goto _failure;
                }
            }
            if (false) {
                _success:
                $this->_monitorFilter->normalCollect($monitorKey, $startTime, $protocol);
                goto _result;
                _failure:
                $this->_monitorFilter->failureCollect($monitorKey, $startTime);
            }
            _result:
            $server->send($fd, $result);
        });
    }

    public function onTask()
    {
        $this->_swServer->on('Task',function (Server $server, $task_id, $worker_id, $data) {
            try {
                list($protocol,$decoder,$fd,$reactor_id) = $data;
                $result = $this->_service->invoke($protocol, $decoder, $server, $fd, $reactor_id);
                $server->finish($result);
            } catch (\Exception $e) {
                LoggerFacade::getLogger("dubbo-server",__CLASS__,__LINE__)->error('Service Exception. ', $e->getMessage());
            }
        });
    }

    public function onFinish()
    {
        $this->_swServer->on('Finish', function (Server $server, $task_id, $data) {
            echo "Task#$task_id finished ".PHP_EOL;
        });
    }

    public function setPidHandle($fp)
    {
        $this->_pidHandle = $fp;
    }

}