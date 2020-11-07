<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 *  global.func.php 公共函数库
 *  第一优先加载函数
 */


/**获取配置文件
 * @param $name
 * @return mixed
 */
function cnf($name,$value=''){
    static $config;
    if($config) {
    } else {
        $config  = include_once(APP_PATH."config.php");
    }
    return $value?$config[$name][$value]:$config[$name];
}

function inner_ip(){
    if(function_exists("swoole_get_local_ip")) {
        $ips = swoole_get_local_ip();
        return array_values($ips)[0];
    } else {
        return "127.0.0.1";
    }
}

function request($value){
    return isset($_REQUEST[$value])?addslashes(trim($_REQUEST[$value])):'';
}


function array2nr($d,&$str='') {
    if (is_array($d)) {
        foreach ($d as $k =>$v ) {
            if(is_array($v)) {
                $str .= "{$k}\n";
            } else {
                $str .= "{$k}:{$v}\n";
            }
            array2nr($v,$str);
        }
    }
    return $str;
}

function is_assoc($arr){
    return array_keys($arr) !== range(0, count($arr) - 1);
}

function array2line($arr,$last_k='',&$new_arr=[]){
    if(is_array($arr)) {foreach ($arr as $k=>$v) {
        if(is_assoc($arr)) {
            array2line($arr[$k],($last_k?$last_k.".":"").$k,$new_arr);
        } else {
            array2line($arr[$k],($last_k?"{$last_k}[$k]":"[$k]"),$new_arr);
        }
    }} else if(is_bool($arr)) {
        $new_arr[$last_k]=$arr;
    } else {
        if(empty($last_k)&&strval($last_k)!==0) {

        } else {
            $new_arr[$last_k]=strval($arr);
        }
    }
    return $new_arr;
}

function debug_log($logs=array(),$log_name="debug",$cp='',$cp_line=''){
    $logtext = '';
    if(is_array($logs)) {
        foreach (array2line($logs) as $k=>$text) {
            $text = str_replace("\n"," ",$text);
            $logtext .= "\n# \t\t\t\t\t\t{$k}={$text}";
        }
    } else {
        $logs = str_replace("\n"," ",$logs);
        $logtext .= " \n# \t\t\t\t\t\t".$logs;
    }

    $log_dir = CACHE_PATH."logs".DIRECTORY_SEPARATOR.date("Ym").DIRECTORY_SEPARATOR.date("md").DIRECTORY_SEPARATOR;
    if(!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
        chmod($log_dir,0777);
    }

    $txtf = $log_dir.$log_name."_".date("Ymd")."_log.php";
    if(!file_exists($txtf)) {
        $fh = <<<fh
<?php
defined('IN_CDO') or exit('illegal infiltration.');\n
fh;

        error_log($fh."# ".date("Y-m-d H:i:s")."\t".$cp."\t".$cp_line."\t".ROUTE_C."/".ROUTE_A."\t"."CREATE LOG FILE"."\n",3,$txtf);
        try {
            chmod($txtf, 0777);
        } catch (Exception $e) {
        }
    }

    error_log("# ".date("Y-m-d H:i:s")."\t".$cp."\t".$cp_line."\t".ROUTE_C."/".ROUTE_A."\t".$logtext."\n\n",3,$txtf);

}



/**
 * 返回经addslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_addslashes($string){
    if(!is_array($string)) return addslashes($string);
    foreach($string as $key => $val) $string[$key] = new_addslashes($val);
    return $string;
}

/**
 * 返回经stripslashes处理过的字符串或数组
 * @param $string 需要处理的字符串或数组
 * @return mixed
 */
function new_stripslashes($string) {
    if(!is_array($string)) return stripslashes($string);
    foreach($string as $key => $val) $string[$key] = new_stripslashes($val);
    return $string;
}

/**
 * 安全过滤函数
 *
 * @param $string
 * @return string
 */
function safe_replace($string) {
    $string = str_replace('%20','',$string);
    $string = str_replace('%27','',$string);
    $string = str_replace('%2527','',$string);
    $string = str_replace('*','',$string);
    $string = str_replace('"','&quot;',$string);
    $string = str_replace("'",'',$string);
    $string = str_replace('"','',$string);
    $string = str_replace(';','',$string);
    $string = str_replace('<','&lt;',$string);
    $string = str_replace('>','&gt;',$string);
    $string = str_replace("{",'',$string);
    $string = str_replace('}','',$string);
    $string = str_replace('\\','',$string);
    return $string;
}

/**
 * 获取当前页面完整URL地址
 */
function get_url() {
    $sys_protocal = isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == '443' ? 'https://' : 'http://';
    $php_self = $_SERVER['PHP_SELF'] ? safe_replace($_SERVER['PHP_SELF']) : safe_replace($_SERVER['SCRIPT_NAME']);
    $path_info = isset($_SERVER['PATH_INFO']) ? safe_replace($_SERVER['PATH_INFO']) : '';
    $relate_url = isset($_SERVER['REQUEST_URI']) ? safe_replace($_SERVER['REQUEST_URI']) : $php_self.(isset($_SERVER['QUERY_STRING']) ? '?'.safe_replace($_SERVER['QUERY_STRING']) : $path_info);
    return $sys_protocal.(isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '').$relate_url;
}
/**
 * 字符截取 支持UTF8/GBK
 * @param $string
 * @param $length
 * @param $dot
 */
function str_cut($string, $length, $dot = '...') {
    $strlen = strlen($string);
    if($strlen <= $length) return $string;
    $string = str_replace(array(' ','&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), array('∵',' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), $string);
    $strcut = '';
    if(strtolower(CHARSET) == 'utf-8') {
        $length = intval($length-strlen($dot)-$length/3);
        $n = $tn = $noc = 0;
        while($n < strlen($string)) {
            $t = ord($string[$n]);
            if($t == 9 || $t == 10 || (32 <= $t && $t <= 126)) {
                $tn = 1; $n++; $noc++;
            } elseif(194 <= $t && $t <= 223) {
                $tn = 2; $n += 2; $noc += 2;
            } elseif(224 <= $t && $t <= 239) {
                $tn = 3; $n += 3; $noc += 2;
            } elseif(240 <= $t && $t <= 247) {
                $tn = 4; $n += 4; $noc += 2;
            } elseif(248 <= $t && $t <= 251) {
                $tn = 5; $n += 5; $noc += 2;
            } elseif($t == 252 || $t == 253) {
                $tn = 6; $n += 6; $noc += 2;
            } else {
                $n++;
            }
            if($noc >= $length) {
                break;
            }
        }
        if($noc > $length) {
            $n -= $tn;
        }
        $strcut = substr($string, 0, $n);
        $strcut = str_replace(array('∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'), array(' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'), $strcut);
    } else {
        $dotlen = strlen($dot);
        $maxi = $length - $dotlen - 1;
        $current_str = '';
        $search_arr = array('&',' ', '"', "'", '“', '”', '—', '<', '>', '·', '…','∵');
        $replace_arr = array('&amp;','&nbsp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;',' ');
        $search_flip = array_flip($search_arr);
        for ($i = 0; $i < $maxi; $i++) {
            $current_str = ord($string[$i]) > 127 ? $string[$i].$string[++$i] : $string[$i];
            if (in_array($current_str, $search_arr)) {
                $key = $search_flip[$current_str];
                $current_str = str_replace($search_arr[$key], $replace_arr[$key], $current_str);
            }
            $strcut .= $current_str;
        }
    }

    return $strcut.$dot;
}



/**
 * 获取请求ip
 *
 * @return ip地址
 */
function ip() {
    if(getenv('HTTP_CLIENT_IP') && strcasecmp(getenv('HTTP_CLIENT_IP'), 'unknown')) {
        $ip = getenv('HTTP_CLIENT_IP');
    } elseif(getenv('HTTP_X_FORWARDED_FOR') && strcasecmp(getenv('HTTP_X_FORWARDED_FOR'), 'unknown')) {
        $ip = getenv('HTTP_X_FORWARDED_FOR');
    } elseif(getenv('REMOTE_ADDR') && strcasecmp(getenv('REMOTE_ADDR'), 'unknown')) {
        $ip = getenv('REMOTE_ADDR');
    } elseif(isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], 'unknown')) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return preg_match ( '/[\d\.]{7,15}/', $ip, $matches ) ? $matches [0] : '';
}

function get_cost_time() {
    $microtime = microtime ( TRUE );
    return $microtime - SYS_START_TIME;
}
/**
 * 程序执行时间
 *
 * @return	int	单位ms
 */
function execute_time() {
    $stime = explode ( ' ', SYS_START_TIME );
    $etime = explode ( ' ', microtime () );
    return number_format ( ($etime [1] + $etime [0] - $stime [1] - $stime [0]), 6 );
}

function getmicrotime() {
    list($usec, $sec) = explode(" ",microtime());
    return ((float)$usec + (float)$sec);
}

/**
 * 产生随机字符串
 *
 * @param    int        $length  输出长度
 * @param    string     $chars   可选的 ，默认为 0123456789
 * @return   string     字符串
 */
function random($length, $chars = '0123456789') {
    $hash = '';
    $max = strlen($chars) - 1;
    for($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}

/**
 * 将字符串转换为数组
 *
 * @param	string	$data	字符串
 * @return	array	返回数组格式，如果，data为空，则返回空数组
 */
function string2array($data) {
    if($data == '') return array();
    @eval("\$array = $data;");
    return $array;
}
/**
 * 将数组转换为字符串
 *
 * @param	array	$data		数组
 * @param	bool	$isformdata	如果为0，则不使用new_stripslashes处理，可选参数，默认为1
 * @return	string	返回字符串，如果，data为空，则返回空
 */
function array2string($data, $isformdata = 1) {
    if($data == '') return '';
    if($isformdata) $data = new_stripslashes($data);
    return addslashes(var_export($data, TRUE));
}


/**
 * 高级序列化
 * @param $func
 * @return string
 */
function sc_serialize($func,$cache_flag=''){
    $serialized = "";
    static $caches;
    if($cache_flag) {
        if(!isset($caches[$cache_flag])||empty($caches[$cache_flag])) {
            $caches[$cache_flag] = get_cache($cache_flag,"caches_sc_serialize");
            if(empty($caches[$cache_flag])) {
                $caches[$cache_flag] = (new \SuperClosure\Serializer())->serialize($func);
            }
            set_cache(md5($cache_flag),$caches[$cache_flag],"caches_sc_serialize");
        }
        $serialized = $caches[$cache_flag];
    } else {
        $serialized = (new \SuperClosure\Serializer())->serialize($func);
    }
    return $serialized;
}

/**
 * 高级反序列化
 * @param $serialize
 * @return Closure
 */
function sc_unserialize($serialize){
    return (new \SuperClosure\Serializer())->unserialize($serialize);
}


function h404(){
    header("HTTP/1.1 404 Not Found");
    header("Status: 404 Not Found");
    $html = <<<html
<!DOCTYPE>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>HTTP 错误 404.0 - Not Found</title>
</head>

<body>
<h3>HTTP 错误 404.0 - Not Found</h3>
<h4>您要找的资源已被删除、已更名或暂时不可用。</h4>
</body>
</html>
html;
    echo $html;
    exit;
}







