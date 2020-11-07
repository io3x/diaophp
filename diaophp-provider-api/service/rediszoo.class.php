<?php
defined('IN_CDO') or exit('illegal infiltration.');
use Dubbo\Common\Logger\LoggerFacade;
use Dubbo\Registry\Client\ZookeeperClient;
class rediszoo {
    public function starter(){
        /*新建一个维护连接的进程池——异常连接就会重启*/
        (new swoole_process(function(swoole_process $worker) {
            swoole_set_process_name("php-rediszoo-master");
            $pool = new swoole_process_pool(2);
            /*创建一个mysql连接池 为每个进程创建好数据库链接*/
            $pool->redis=[];
            $pool->swoole_client=[];
            $pool->registry[] = [];
            $pool->on("WorkerStart", function ($pool, $workerId) {
                echo $workerId.PHP_EOL;
                if($workerId%2==0) {
                    $this->zookeerperd2redis($pool,$workerId);
                } else {
                    $this->registry_provider($pool,$workerId);
                }
            });

            $pool->on("WorkerStop", function ($pool, $workerId) {
                echo "Worker#{$workerId} is stopped\n";
            });
            $pool->start();
        },false,false))->start();
    }

    private function zookeerperd2redis($pool, $workerId){
        swoole_set_process_name("php-rediszoo-zoodata2redis-worker-{$workerId}");
        try {
            $pool->redis[$workerId] = new rds();
        } catch (Exception $e) {
            echo $e->getMessage().PHP_EOL;
        }
        try {
            $pool->registry[$workerId] = new ZookeeperClient();
            $pool->registry[$workerId]->connect();
        } catch (Exception $e) {
            echo $e->getMessage().PHP_EOL;
        }
        swoole_timer_tick(3000,function($timer_id) use ($pool,$workerId) {
            try {
                $pingstr=$pool->redis[$workerId]->rds->ping();
                $pool->redis[$workerId]->rds->select(0);
                $registryed_services = [];
                $zoo_dubbo = $pool->registry[$workerId]->getChildren('/dubbo');
                foreach ($zoo_dubbo as $dubbo_root_path) {
                    if($dubbo_root_path=='config') continue;
                    $tmp = $pool->registry[$workerId]->getChildren("/dubbo/{$dubbo_root_path}/providers");
                    foreach ($tmp as $dubbo_url) {
                        $registryed_services[$dubbo_root_path][] = rawurldecode($dubbo_url);
                    }
                }

                foreach ($registryed_services as $k=>$urls) {
                    foreach ($urls as $dubbo_url) {
                        /*判断是否存在url*/
                        if($pool->redis[$workerId]->redis_sismember($k,$dubbo_url)) {

                        } else {
                            /*dubbo 服务可连接则同步到redis */
                            $parse_url = parse_url($dubbo_url);
                            $swoole_client = new swoole_client(SWOOLE_SOCK_TCP);
                            if ($swoole_client->connect($parse_url['host'],intval($parse_url['port']),3)) {
                                $pool->redis[$workerId]->redis_sadd($k,$dubbo_url);
                            }
                            $swoole_client->close();
                        }
                    }
                }

                /*如果redis节点不在zookeeper里面,则删除*/
                $cps = $pool->redis[$workerId]->rds->keys("*");
                foreach ($cps as $cp) {
                    foreach ($pool->redis[$workerId]->redis_smember($cp) as $dubbourl) {
                        if(!$pool->registry[$workerId]->exists("/dubbo/{$cp}/providers/".rawurlencode($dubbourl))){
                            $pool->redis[$workerId]->redis_srem($cp,$dubbourl);
                        }
                    }
                }
            } catch (Exception $e) {
                $pingstr = "";
                echo $e->getMessage().PHP_EOL;
            }
            if(strstr($pingstr,"PONG")) {
                echo $pingstr;
            } else {
                swoole_event_exit();
            }
            echo time();
        });
        swoole_event_wait();
        echo "Worker#{$workerId} is started\n";
    }

    private function registry_provider($pool, $workerId){
        swoole_set_process_name("php-rediszoo-registry-provider-worker-{$workerId}");
        try {
            $pool->redis[$workerId] = new rds();
            $pool->redis[$workerId]->rds->select(0);
        } catch (Exception $e) {
            echo $e->getMessage().PHP_EOL;
        }
        try {
            $pool->registry[$workerId] = new ZookeeperClient();
            $pool->registry[$workerId]->connect();
        } catch (Exception $e) {
            echo $e->getMessage().PHP_EOL;
        }

        $timestamp13 = timestamp13();
        $load_once = function($timer_id) use ($pool,$workerId,$timestamp13) {
            //swoole_event_exit();
            foreach (load_dubbo_service() as $class_name=>$methods) {
                if(substr($class_name,-8)=='_service') {
                    if(isset($methods['__construct'])) unset($methods['__construct']);
                    $method = implode(",",array_keys($methods));
                    $cp = cnf('SWOOLE_DUBBO_SERVICE','provider_prefix').".{$class_name}";


                    $dubbourl = dubbo_url(cnf("SWOOLE_DUBBO_SERVICE","host"),cnf("SWOOLE_DUBBO_SERVICE","port"),$cp,$method,$timestamp13);
                    $parse_url = parse_url($dubbourl);
                    /*检测服务是否开启*/
                    try {
                        $swoole_client = new swoole_client(SWOOLE_SOCK_TCP);
                        if (!$swoole_client->connect($parse_url['host'],intval($parse_url['port']),3)) {
                            echo $dubbourl."连接失败";
                            //$pool->registry[$workerId]->unregisterService($cp,rawurlencode($dubbourl));
                            if($pool->redis[$workerId]->redis_sismember($cp,$dubbourl)) {
                                $pool->redis[$workerId]->redis_srem($cp,$dubbourl);
                            }
                        } else {
                            /*先注册到zookeeper*/
                            $pool->registry[$workerId]->registerService($cp,rawurlencode($dubbourl));
                        }
                        $swoole_client->close();
                    } catch (Exception $e) {
                        echo $e->getMessage().PHP_EOL;
                    }
                }
            }



        };
        $load_once(0);
        swoole_timer_tick(3000,$load_once);
        swoole_event_wait();
    }
}