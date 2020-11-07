<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 *
 */
class rurl {
    /**
     * 自动返回当前url前缀,自动判断是否带index.php
     */
    public static function url_ref(){
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        $a = $_SERVER['REQUEST_URI'];
        if(strpos($a,"index.php")) {
            return $sys_protocal.$_SERVER['HTTP_HOST']."/index.php";
        } else {
            return $sys_protocal.$_SERVER['HTTP_HOST'];
        }
    }

    /**
     * 返回关键路径
     * @return mixed
     */
    public static function url_sef(){
        $a = explode("?",get_url());
        $p = str_replace(self::url_ref(),'',$a[0]);
        $pi = array_values(array_filter(explode("/",$p)));
        return "/{$pi[0]}/{$pi[1]}/{$pi[2]}";
    }

    /**
     * 跳转页面
     * @param string $pef
     * @return string
     */
    public static function url_go($pef=''){
        return self::url_ref().$pef;
    }

    public static function res(){
        $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
        return $sys_protocal.$_SERVER['HTTP_HOST']."/".ROUTE_M."/";
    }
}