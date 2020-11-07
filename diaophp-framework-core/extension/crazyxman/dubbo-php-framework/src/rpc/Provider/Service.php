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

namespace Dubbo\Provider;

use Dubbo\Common\DubboException;
use Dubbo\Common\Protocol\Dubbo\DubboProtocol;
use Dubbo\Common\Protocol\Dubbo\DubboResponse;

class Service
{

    private $_serviceTable;


    public function invoke(DubboProtocol $protocol, $decoder, $server, $fd, $reactor_id)
    {

        $serviceName = $decoder->getServiceName();
        /*$service = $this->_serviceTable[$serviceName] ?? '';
        if (!$service) {
            throw new DubboException("Can't find '{$serviceName}' service");
        }*/
        $cps = explode(".",$decoder->getServiceName());
        $class = end($cps);

        if(empty($class)) {
            throw new DubboException("Can't find '{$serviceName}' service");
        }

        $method = $decoder->getMethod();
        if(!method_exists($class, $method)) {
            throw new DubboException("Can't find '{$method}' method");
        }



        /*$version = $decoder->getServiceVersion();
        if ($version != '0.0.0' && $version != $dubboUrl->getVersion()) {
            throw new DubboException("Can't find '{$version}' version service");
        }

        $attachments = $decoder->getAttachments();

        if ($dubboUrl->getGroup() != ($attachments['group'] ?? '')) {
            throw new DubboException("Can't find '{$attachments['group']}' group service");
        }
        */
        $arguments = $decoder->getArguments();

        if(method_exists($class, '__construct')) {
            $result = call_user_func_array([new $class($class,$method,$arguments,$server, $fd, $reactor_id), $method],$arguments);
        } else {
            $result = call_user_func_array([new $class(), $method],$arguments);
        }
        $protocol->setStatus(DubboResponse::STATUS_OK);
        $protocol->setVariablePartType(DubboResponse::RESPONSE_VALUE);
        return $protocol->packResponse($result);
    }

    public function returnHeartBeat(DubboProtocol $protocol,$r='')
    {
        $protocol->setStatus(DubboResponse::STATUS_OK);
        $protocol->setVariablePartType(DubboResponse::RESPONSE_VALUE);
        return $protocol->packResponse($r);
    }

    public function returnException(DubboProtocol $protocol, $message)
    {
        $protocol->setStatus(DubboResponse::SERVICE_ERROR);
        $protocol->setVariablePartType(DubboResponse::RESPONSE_WITH_EXCEPTION);
        return $protocol->packResponse($message);
    }


    public function getServiceTable()
    {
        return $this->_serviceTable;
    }

}