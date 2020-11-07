<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * Class demo2
 */
class shop_service extends dubbo_provider_main {
    private function m0(){
        echo "m0";
    }
    /**
     *
     */
    public function m1($var1,$var2,$var3="abc"){
        $r =  json_encode_ex(func_get_args()).__CLASS__.__METHOD__;
        echo $r;
        return $r;
    }

    /**
     * 异步m1服务
     */
    public function async_m1($var1,$var2,$var3="abc"){
        mt_srand();
        sleep(mt_rand(1,5));
        return $this->m1($var1,$var2,$var3);
    }
}