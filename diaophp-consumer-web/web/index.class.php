<?php
/*设置显示错误*/
/*ini_set("display_errors", "on");
error_reporting(E_ALL);*/
defined('IN_CDO') or exit('illegal infiltration.');
/**
 *
 */
class index {
    /**
     *
     */
    public function init(){
        $title = (new op())->hello();
        include template_auto(ROUTE_M,"test/index");
    }

    public function test2($a){
        echo $a;
    }
}
