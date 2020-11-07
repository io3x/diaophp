<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * 扫描目录下所有php class文件
 * @param $pattern
 * @param int $flags
 * @return array
 */
function rglob($pattern, $flags = 0) {
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern).DIRECTORY_SEPARATOR.'*', GLOB_ONLYDIR|GLOB_NOSORT) as $dir) {
        $files = array_merge($files, rglob($dir.DIRECTORY_SEPARATOR.basename($pattern), $flags));
    }
    return $files;
}
function scan_provider_classes(){
    static $new_ctrls;
    if(empty($new_ctrls)) {
        $new_ctrls = array();
        $arr_scan_class = rglob(ROOT_PATH."*.class.php");
        foreach($arr_scan_class as $f){
            $bname = basename($f);
            if(isset($new_ctrls[$bname])) exit($bname."自检失败:provider-api 下所有层级目录下的.class.php 不能重名");
            $new_ctrls[$bname] = $f;
        }
    }
    return $new_ctrls;
}
/**
 *
 */
function load_dubbo_service(){
    static $dubbos;
    if($dubbos) return $dubbos;
    $arr_scan_class_parents = glob(ROOT_PATH."*",GLOB_NOSORT);
    $arr_scan_class = [];
    foreach ($arr_scan_class_parents as $scan_dir) {
        if(is_dir($scan_dir)) {
            $dirs = glob($scan_dir.DIRECTORY_SEPARATOR."*",GLOB_NOSORT);
            $arr_scan_class = $arr_scan_class?array_merge($arr_scan_class,$dirs):$dirs;
        }
    }
    $dubbos = array();
    foreach($arr_scan_class as $f){
        if(strstr($f,".class.php")) {
            $classname = explode(".",basename($f))[0];
            $ref = new ReflectionClass($classname);
            foreach ($ref->getMethods() as $method) {
                if($method->isPublic()) {
                    $params = $method->getParameters();
                    if($params) {
                        foreach ($params as $param) {
                            if($param->isDefaultValueAvailable()) {
                                $dubbos[$classname][$method->getName()][$param->getName()] = $param->getDefaultValue();
                            } else {
                                $dubbos[$classname][$method->getName()][$param->getName()] = '';
                            }
                        }
                    } else {
                        $dubbos[$classname][$method->getName()] = [];
                    }
                }
            }
        }
    }
    return $dubbos;
}
