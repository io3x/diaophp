<?php
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

    public function test($aa){
        echo $aa;
    }
}
