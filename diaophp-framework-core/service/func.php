<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * 扫描含有定时任务的方法
 *
 */
function load_crontab(){
    static $crontabs;
    if($crontabs) return $crontabs;
    $arr_scan_class = glob(DIAOPHP_FRAMEWORK_CORE_PATH."service".DIRECTORY_SEPARATOR."*",GLOB_NOSORT);
    $crontabs = array();
    foreach($arr_scan_class as $f){
        if(strstr($f,".class.php")) {
            $classname = explode(".",basename($f))[0];
            $ref = new ReflectionClass($classname);
            foreach ($ref->getMethods() as $method) {
                /*if($method->isPublic()&&$method->isFinal()) {

                }*/
                if($method->getNumberOfParameters()==1) {
                    foreach ($method->getParameters() as $param) {
                        if($param->isDefaultValueAvailable()) {
                            if($param->getName()=="crontab") $crontabs["{$classname}/{$method->getName()}/{$param->getName()}"] = $param->getDefaultValue();
                        }
                    }
                }
            }
        }
    }
    return $crontabs;
}

/**
 * 异步进程任务推送
 * 区别event是 支持异步swoole_timer 等
 */
function crontab_process(...$args){
    if($args[0]) $args[0]=sc_serialize($args[0]);
    $fp = stream_socket_client("tcp://".cnf("SERVICE","host").":".cnf("SERVICE","port_pool"), $errno, $errstr);
    if($fp) {
        $msg = serialize($args);
        fwrite($fp, pack('N', strlen($msg)).$msg);
        fclose($fp);
        return 1;
    } else {
        debug_log(['process',$errno,$errstr],"app.prc");
        return 0;
    }
}

/**
 * 清空某个缓存目录下的文件
 * @param string $dir
 * @return int
 */
function clean_cache($dir=''){
    if(empty($dir)) return 0;
    $delfs = glob(CACHE_PATH.$dir.DIRECTORY_SEPARATOR."*",GLOB_NOSORT);
    array_map('unlink',$delfs);
    return 1;
}