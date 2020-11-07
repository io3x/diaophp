<?php
defined('IN_CDO') or exit('illegal infiltration.');
define('CHARSET','utf-8');
//来源
define('HTTP_REFERER', isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '');

define('APP_PATH',DIAOPHP_FRAMEWORK_CORE_PATH."app".DIRECTORY_SEPARATOR);
define('CACHE_PATH',DIAOPHP_FRAMEWORK_CORE_PATH.'caches'.DIRECTORY_SEPARATOR);
define('EXT_PATH',DIAOPHP_FRAMEWORK_CORE_PATH."extension".DIRECTORY_SEPARATOR);

include_once APP_PATH.DIRECTORY_SEPARATOR.'libs'.DIRECTORY_SEPARATOR.'global.func.php';
if(cnf('VENDOR','IS_VENDOR')) {
    include_once DIAOPHP_FRAMEWORK_CORE_PATH."vendor".DIRECTORY_SEPARATOR."autoload.php";
}

$sapi = php_sapi_name();
if($sapi=='cli') {
    $arr_path = array_values(array_filter(explode('/',$argv[1])));
    define("DIAOPHP_CLI_ARGV",$argv);
    define("CRON_INFO",isset($argv[2])?$argv[2]:"");
    list($route_m,$route_c,$route_a) = $arr_path;
    if(isset($route_m)&&!empty($route_m)) {
        define("ROUTE_M",$route_m);
    } else {
        exit("ROUTE_M ERROR");
    }
    if(isset($route_c)&&!empty($route_c)) {
        define("ROUTE_C",$route_c);
    } else {
        define("ROUTE_C",'index');
    }
    if(isset($route_a)&&!empty($route_a)) {
        define("ROUTE_A",$route_a);
    } else {
        define("ROUTE_A",'init');
    }
} else {
    define('HOST_URL',"http://{$_SERVER['HTTP_HOST']}/");
    /*加载系统路由文件*/
    include_once DIAOPHP_FRAMEWORK_CORE_PATH.'config'.DIRECTORY_SEPARATOR.'data'.DIRECTORY_SEPARATOR.'route.php';
    header('Content-type: text/html; charset="utf-8"');
}

/*屏蔽访问*/
if(in_array(ROUTE_M,array("model","config","app","extension","vendor","caches"))) {
    exit("the system using directory Keeped !");
}






#程序路由 结束

/**
 *自动装载
 */
class loader{

    /**
     * 自动加载autoload目录下函数库
     * @param string $func 函数库名
     */
    public static function auto_load_func($path='') {
        self::_auto_load_func($path);
    }

    /**
     * 加载函数库
     * @param string $func 函数库名
     * @param string $path 地址
     */
    private static function _auto_load_func($path = '') {
        if (empty($path)) $path = APP_PATH.'libs'.DIRECTORY_SEPARATOR.'functions';
        $path .= DIRECTORY_SEPARATOR.'*.func.php';
        $auto_funcs = glob($path);
        if(!empty($auto_funcs) && is_array($auto_funcs)) {
            foreach($auto_funcs as $func_path) {
                include $func_path;
            }
        }
    }

    /**
     * @param $auto_classname
     */
    public static function load_sys_classes($auto_classname){
        $func_file = APP_PATH.'common.func.php';
        if (is_file($func_file)) include_once($func_file);

        $class_file = APP_PATH."libs".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR.$auto_classname.'.class.php';
        if (is_file($class_file)) require_once($class_file);
    }

    /**
     * @param $controller_name
     */
    public static function load_controller($controller_name){
        /*加载当前模块函数*/
        $func_file = ROOT_PATH.ROUTE_M.DIRECTORY_SEPARATOR.'func.php';
        if (is_file($func_file)) include_once($func_file);
        /*加载当前模块设置*/
        $setting_file = ROOT_PATH.ROUTE_M.DIRECTORY_SEPARATOR.'setting.php';
        if (is_file($setting_file)) include_once($setting_file);
        /*加载当前模块类库*/
        $class_file = ROOT_PATH.ROUTE_M.DIRECTORY_SEPARATOR.$controller_name.'.class.php';
        if (is_file($class_file)) require_once($class_file);
    }

    /**
     * @param $controller_name
     */
    public static function load_app_classes($class_name){
        $class_file = ROOT_PATH.ROUTE_M.DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR.$class_name.".class.php";
        if (is_file($class_file)) require_once($class_file);
    }

    public static function load_module_classes($module,$class_name){
        $class_file = ROOT_PATH.$module.DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR.$class_name.".class.php";
        if (is_file($class_file)) require_once($class_file);
    }

    public static function load_model_classes($class_name){
        $model_file = DIAOPHP_FRAMEWORK_CORE_PATH."model".DIRECTORY_SEPARATOR.$class_name.".class.php";
        if (is_file($model_file)) require_once($model_file);
        $class_file = DIAOPHP_FRAMEWORK_CORE_PATH."model".DIRECTORY_SEPARATOR."classes".DIRECTORY_SEPARATOR.$class_name.".class.php";
        if (is_file($class_file)) require_once($class_file);
    }

}

/**
 *应用程序入口
 */
class app {
    /**
     *
     */
    public static function app_spl_autoload(){
        spl_autoload_register(array('loader','load_sys_classes'));
        spl_autoload_register(array('loader','load_controller'));
        spl_autoload_register(array('loader','load_app_classes'));
        spl_autoload_register(array('loader','load_model_classes'));
        loader::auto_load_func();
    }
    /**
     *
     */
    public static function init(){
        $controller = ROUTE_C;
        if (class_exists($controller)&&method_exists($controller, ROUTE_A)) {
            if (preg_match('/^[_]/i', ROUTE_A)) {
                exit('You are visiting the action is to protect the private action');
            } else {
                $controller_module = new $controller;
                $method =   new ReflectionMethod($controller_module, ROUTE_A);
                if($method->isPublic()) {
                    $class  =   new ReflectionClass($controller);
                    // URL参数绑定检测
                    if($method->getNumberOfParameters()>0){
                        switch(isset($_SERVER['REQUEST_METHOD'])?$_SERVER['REQUEST_METHOD']:"") {
                            case 'POST':
                                $vars    =  array_merge($_GET,$_POST);
                                break;
                            case 'PUT':
                                parse_str(file_get_contents('php://input'), $vars);
                                break;
                            default:
                                $vars  =  $_GET;
                        }
                        $params =  $method->getParameters();
                        foreach ($params as $param){
                            $name = $param->getName();
                            if(isset($vars[$name])) {
                                $args[] =  $vars[$name];
                            }elseif($param->isDefaultValueAvailable()){
                                $args[] = $param->getDefaultValue();
                            }else{
                                $args[] = '';
                            }
                        }

                        /*CLI命令行参数通过反射注入方法*/
                        if(defined('DIAOPHP_CLI_ARGV')&&!empty(DIAOPHP_CLI_ARGV)) {
                            $argv = DIAOPHP_CLI_ARGV;
                            $args=[];
                            if(isset($argv)&&is_array($argv)&&count($argv)>2) {
                                $tmp = $argv;
                                unset($tmp[0],$tmp[1]);
                                $args  =  array_values($tmp);
                            }
                        }
                        $method->invokeArgs($controller_module,$args);
                    }else{
                        $method->invoke($controller_module);
                    }
                }
                //call_user_func(array(new $controller, ROUTE_A));
            }
        } else {
            h404();
            //exit('Class or Action does not exist.');
        }
    }


    /**
     *
     */
    public static function run(){
        #session_start();
        app::app_spl_autoload();

        /*启动内置修改后的第三方扩展*/
        if(is_file(EXT_PATH.'extension.php')) require_once EXT_PATH.'extension.php';

        app::init();
    }
}