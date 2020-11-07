<?php
defined('IN_CDO') or exit('illegal infiltration.');
class ip_service {
    /**
     *
     */
    public function swoole_get_local_ip(){
        echo inner_ip();
        return inner_ip();
    }
}