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
        $arrdoc = htmldoc::config();
        $op = request('op');
        if(isset($arrdoc[$op])&&!empty($arrdoc[$op])) {
            $info = $arrdoc[$op];
            include template_auto(ROUTE_M,"api/doc");
        } else {
            include template_auto(ROUTE_M,"api/index");
        }
    }

    public function req(){
        debug_log($_POST);
        echo array2nr($_POST);
    }

    public function tmp(){
        $a =  asterisk2preg('/upload2/image/*');
    }
}
