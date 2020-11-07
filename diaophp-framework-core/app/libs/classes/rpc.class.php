<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * Class rpc 基于swoole简单RPC调用实现
 */
class rpc {
    /**
     *
     * 多线路同步推送任务
     */
    public static function send($op='',$args=array(),$timeout=20){
        $rpc_data = array(
            'op'=>$op,
            'data'=>serialize($args)
        );
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL,"http://".cnf("SERVICE","host").":".cnf("SERVICE","port")."/");
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS,$rpc_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5);
        curl_setopt($curl, CURLOPT_TIMEOUT,100);
        $result = curl_exec($curl);
        curl_close($curl);
        return $result;
    }

    /**
     * 异步任务推送
    $x = rpc::event(function($a,$b,$c){
    print_r(array($a,$b,$c));
    },11,222,333);
     * @param array ...$args
     * @return mixed
     */
    public static function event($flag,...$args){
        if($args[0]) $args[0]=sc_serialize($args[0],$flag);
        return self::send("event",$args);
    }

    public static function event_callback($flag,...$args){
        if($args[0]) $args[0]=sc_serialize($args[0],$flag);
        return unserialize(self::send("event_callback",$args));
    }

    public static function event_callbacks(){
        return new rpc_event_callback();
    }

    public static function kvdb($method,...$args){
        return self::send("kvdb",[$method,$args]);
    }


}