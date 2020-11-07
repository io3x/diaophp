<?php
defined('IN_CDO') or exit('illegal infiltration.');

/**
 * Class event_demo
 */
class event_demo {
    /**
     * 异步事件调用 不返回结果
     */
    public function event(){
        $tt = new cost_time();
        $tt->point_time("异步事件调用时间:");
        for($i=0;$i<10;$i++){
            rpc::event(md5_file(__FILE__).__LINE__,function($a,$b,$c){
                mt_srand();
                sleep(mt_rand(1,2));
                echo date("Y-m-d H:i:s");
                print_r(array($a,$b,$c));
            },11,222,$i);
            $tt->point_time("T:");
        }
        print_r($tt->result());
    }

    /**
     * 同步等待返回调用
     */
    public function event_back(){
        $tt = new cost_time();
        $tt->point_time("同步等待返回调用:");
        $r=[];
        for($i=0;$i<3;$i++){
            $r[] = rpc::event_callback(md5_file(__FILE__).__LINE__,function($a,$b,$c){
                mt_srand();
                sleep(mt_rand(1,2));
                echo date("Y-m-d H:i:s");
                return [date("Y-m-d H:i:s"),$a,$b,$c];
            },11,222,$i);
            $tt->point_time("T:");
        }
        print_r($r);
        print_r($tt->result());
    }

    /**
     * 并发同步等待返回
     */
    public function bulk_event_back(){
        $tt = new cost_time();
        $tt->point_time("并发同步等待返回:");
        $tt->point_time("T:");
        $event_callbacks = rpc::event_callbacks();
        for($i=0;$i<10;$i++){
            $event_callbacks->push_event(md5_file(__FILE__).__LINE__,function($a,$b,$c){
                mt_srand();
                sleep(mt_rand(1,2));
                echo date("Y-m-d H:i:s");
                return [date("Y-m-d H:i:s"),$a,$b,$c];
            },11,222,$i);
            $tt->point_time("T:");
        }
        $tt->point_time("T:");
        print_r($event_callbacks->exec(5000));
        $tt->point_time("T:");
        print_r($tt->result());
    }
}