<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * Class dubbo_provider_main
 * dubbo 服务统一继承的根类
 */
class dubbo_provider_main {
    /**
     * dubbo_provider_main constructor.
     * @param $args
     * @param $server
     * @param $fd
     * @param $reactor_id
     */
    public function __construct($class='',$method='',$args=[], $server=null, $fd=0, $reactor_id=0) {
        debug_log([['服务'=>$class,'方法'=>$method,'参数'=>$args],['worker_id'=>$server->worker_id,'taskworker'=>$server->taskworker,'connections'=>count($server->connections),'stats'=>$server->stats()],['fd'=>$fd],['reactor_id'=>$reactor_id]],"dubbo_provider_main",__CLASS__,__LINE__);
    }
}