<?php
defined('IN_CDO') or exit('illegal infiltration.');
use Dubbo\Common\Protocol\Dubbo\DubboParam;
/**
 * Class cmer_demo1
 */
class cmer {
    public function m1(){
    }


    public function m3(){
        $tt = new cost_time();
        $tt->point_time("开始记录时间");
        echo call_dubbo_service("com.github.io3x.provider.hello.IHelloService")->invoke('hello2',"a1").PHP_EOL;
        $tt->point_time("T:");
        echo call_dubbo_service("com.github.io3x.provider.hello.IHelloService")->invoke('hello2',"a2").PHP_EOL;
        $tt->point_time("T:");
        print_r($tt->result());
    }

    public function m4(){
        $tt = new cost_time();
        $tt->point_time("开始记录时间");
        try {
            $r = call_dubbo_service("com.github.io3x.provider.php.IPHPService")->invoke('m1',time(),"PHP调用java方法示例",["a","b","c"],array_keys($_SERVER),$_SERVER,DubboParam::type('java.lang.Object',"obj作为字符串"));
            print_r($r);
            $tt->point_time("T:");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        try {
            $r = call_dubbo_service("com.github.io3x.provider.php.IPHPService")->invoke('m1',time(),"PHP调用java方法示例",["a","b","c"],array_keys($_SERVER),$_SERVER,DubboParam::type('java.lang.Object',"obj作为字符串"));
            print_r($r);
            $tt->point_time("T:");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
        try {
            $r =  call_dubbo_service("com.github.io3x.provider.php.IPHPService")->invoke('m2',strval(time()));
            print_r($r);
            $tt->point_time("T:");
        } catch (Exception $e) {

        }

        try {
            $r =  call_dubbo_service("com.github.io3x.provider.php.IPHPService")->invoke('m3');
            print_r($r);
            $tt->point_time("T:");
        } catch (Exception $e) {

        }

        print_r($tt->result());
    }

    public function m5(){
        $tt = new cost_time();
        $tt->point_time("开始记录时间");
        echo call_dubbo_service("com.github.io3x.php.ip_service")->invoke('swoole_get_local_ip').PHP_EOL;
        $tt->point_time("T:");
        echo call_dubbo_service("com.github.io3x.php.ip_service")->invoke('swoole_get_local_ip').PHP_EOL;
        $tt->point_time("T:");
        print_r($tt->result());
    }

    public function m6(){
        debug_log("这是简单的日志","debug",__CLASS__,__LINE__);
        debug_log($_SERVER,"debug",__CLASS__,__LINE__);
        debug_log(['dd',[2222222],['a'=>'b',["c1","c2"]]],"debug",__CLASS__,__LINE__);
    }

    public function m7(){
        $tt = new cost_time();
        $tt->point_time("开始记录时间");
        for ($i=0;$i<20;$i++) {
            echo call_dubbo_service("com.github.io3x.php.shop_service")->invoke('m1',100,"我的店铺-{$i}",timestamp13()).PHP_EOL;
            $tt->point_time("T:");
            echo call_dubbo_service("com.github.io3x.php.shop_service")->invoke('async_m1',100,"异步-我的店铺-{$i}",timestamp13()).PHP_EOL;
            $tt->point_time("T:");
        }
        print_r($tt->result());
    }
}