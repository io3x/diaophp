<?php
defined('IN_CDO') or exit('illegal infiltration.');
class test_service {
    public function a($var=''){
        return __CLASS__.__LINE__.PHP_EOL;
    }
    public function b(){
        return __CLASS__.__LINE__.PHP_EOL;
    }
}