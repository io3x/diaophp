<?php
defined('IN_CDO') or exit('illegal infiltration.');
use Dubbo\Provider\Server\SwooleServer;

/**
 * Class provider
 */
class provider {
    /**
     *
     */
    public function test(){
        print_r(load_dubbo_service());
    }

    /**
     * 启动器
     */
    public function starter(){
        $swoole_dubbo_provider_server = new SwooleServer();
        $swoole_dubbo_provider_server->startUp();
    }
}