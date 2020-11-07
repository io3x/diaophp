<?php
defined('IN_CDO') or exit('illegal infiltration.');
class crontab_demo {
    /**
     * @param string $crontab
     */
    public final function one($crontab="*/1 * * * *"){
        return date("Y-m-d H:i:s").$crontab;
    }

    function two($crontab="*/2 * * * *"){
        return date("Y-m-d H:i:s").$crontab;
    }

    function three($crontab="30 * * * *"){
        return date("Y-m-d H:i:s").$crontab;
    }

    function four($crontab="01,02,05,28,29,30,34,35,36,42,45,48,51 15,16,17 * * *"){
        return date("Y-m-d H:i:s").$crontab;
    }

    function testdb($crontab="*/1 * * * *"){
        $model = new common_model();
        $rid = $model->insert("content",array(
            "title"=>date("Y-m-d H:i:s")." ".$crontab,
            'inputtime'=>time()
        ));
        echo "testdb {$rid}".PHP_EOL;
    }
}