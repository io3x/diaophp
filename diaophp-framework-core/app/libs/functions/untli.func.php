<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * 转换字节数为其他单位
 *
 *
 * @param	string	$filesize	字节大小
 * @return	string	返回大小
 */
function sizecount($filesize) {
    if ($filesize >= 1073741824) {
        $filesize = round($filesize / 1073741824 * 100) / 100 .' GB';
    } elseif ($filesize >= 1048576) {
        $filesize = round($filesize / 1048576 * 100) / 100 .' MB';
    } elseif($filesize >= 1024) {
        $filesize = round($filesize / 1024 * 100) / 100 . ' KB';
    } else {
        $filesize = $filesize.' Bytes';
    }
    return $filesize;
}

/**
 * 查询字符是否存在于某字符串
 *
 * @param $haystack 字符串
 * @param $needle 要查找的字符
 * @return bool
 */
function str_exists($haystack, $needle)
{
    return !(strpos($haystack, $needle) === FALSE);
}

/**
 * 取得文件扩展
 *
 * @param $filename 文件名
 * @return 扩展名
 */
function fileext($filename) {
    return strtolower(trim(substr(strrchr($filename, '.'), 1, 10)));
}


/**
 * 判断字符串是否为utf8编码，英文和半角字符返回ture
 * @param $string
 * @return bool
 */
function is_utf8($string) {
    return preg_match('%^(?:
					[\x09\x0A\x0D\x20-\x7E] # ASCII
					| [\xC2-\xDF][\x80-\xBF] # non-overlong 2-byte
					| \xE0[\xA0-\xBF][\x80-\xBF] # excluding overlongs
					| [\xE1-\xEC\xEE\xEF][\x80-\xBF]{2} # straight 3-byte
					| \xED[\x80-\x9F][\x80-\xBF] # excluding surrogates
					| \xF0[\x90-\xBF][\x80-\xBF]{2} # planes 1-3
					| [\xF1-\xF3][\x80-\xBF]{3} # planes 4-15
					| \xF4[\x80-\x8F][\x80-\xBF]{2} # plane 16
					)*$%xs', $string);
}

/**
 * 生成随机字符串
 * @param string $lenth 长度
 * @return string 字符串
 */
function create_randomstr($lenth = 6) {
    return random($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}


/**
 * 检查id是否存在于数组中
 *
 * @param $id
 * @param $ids
 * @param $s
 */
function check_in($id, $ids = '', $s = ',') {
    if(!$ids) return false;
    $ids = explode($s, $ids);
    return is_array($id) ? array_intersect($id, $ids) : in_array($id, $ids);
}

/**
 * 对数据进行编码转换
 * @param array/string $data       数组
 * @param string $input     需要转换的编码
 * @param string $output    转换后的编码
 */
function array_iconv($data, $input = 'gbk', $output = 'utf-8') {
    if (!is_array($data)) {
        return iconv($input, $output, $data);
    } else {
        foreach ($data as $key=>$val) {
            if(is_array($val)) {
                $data[$key] = array_iconv($val, $input, $output);
            } else {
                $data[$key] = iconv($input, $output, $val);
            }
        }
        return $data;
    }
}
/**
 * Function dataformat
 * 时间转换
 * @param $n INT时间
 */
function dataformat($n) {
    $hours = floor($n/3600);
    $minite	= floor($n%3600/60);
    $secend = floor($n%3600%60);
    $minite = $minite < 10 ? "0".$minite : $minite;
    $secend = $secend < 10 ? "0".$secend : $secend;
    if($n >= 3600){
        return $hours.":".$minite.":".$secend;
    }else{
        return $minite.":".$secend;
    }
}

/**
 *
 * 获取远程内容
 * @param $url 接口url地址
 * @param $timeout 超时时间
 */
function pc_file_get_contents($url, $timeout=30) {
    $stream = stream_context_create(array('http' => array('timeout' => $timeout)));
    return @file_get_contents($url, 0, $stream);
}


function unquery_url($url){
    $parse_url = parse_url($url);
    if($parse_url['path']) {
        $query = isset($parse_url['query'])?$parse_url['query']:'';
        $arrq = explode("/",str_replace($query,'',$url));
        array_pop($arrq);
        return implode("/",$arrq);
    }
    return $url;
}


/**
 * 随机数组取值
 * @param array $arr
 * @return mixed
 */
function array_rand_v($arr=array()){
    $_k = array_rand($arr);
    return $arr[$_k];
}



if(!function_exists('hex2bin')) {
    function hex2bin($data) {
        $len = strlen($data);
        return pack("H" . $len, $data);
    }
}




/**
 * @param array $arr
 * @return mixed
 */
function randKey4array($arr = array()) {
    $key = array_rand($arr);
    return $arr[$key];
}

/**
 * 二维数组排序
 * @param $arr
 * @param $keys
 * @param string $type
 * @return array
 */
function array_sort($arr, $keys, $type = 'asc') {
    $keysvalue = $new_array = array();
    foreach ($arr as $k => $v) {
        $keysvalue[$k] = $v[$keys];
    }
    if ($type == 'asc') {
        asort($keysvalue);
    } else {
        arsort($keysvalue);
    }
    reset($keysvalue);
    foreach ($keysvalue as $k => $v) {
        $new_array[$k] = $arr[$k];
    }
    return $new_array;
}

/**
 * @param $arrays
 * @param $sort_key
 * @param int $sort_order
 * @param int $sort_type
 * @return array|bool
 */
function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
    if(is_array($arrays)){
        foreach ($arrays as $array){
            if(is_array($array)){
                $key_arrays[] = $array[$sort_key];
            }else{
                return false;
            }
        }
    }else{
        return false;
    }
    array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
    return $arrays;
}


/**
 * @param $str
 * @return mixed
 */
function mytrim($str) {
    return str_replace(array("\r\n", "\n", "\r", "&nbsp;"),'',trim($str));
}

if(!function_exists('array_column')){
    /**
     * @param $arr
     * @param $key
     * @return array
     */
    function array_column($arr,$key){
        return array_map(function($val) use ($key){return $val[$key];},$arr);
    }
}

/**
 * @return string
 */
function create_sn(){
    mt_srand((double )microtime() * 1000000 );
    return date("YmdHis" ).str_pad( mt_rand( 1, 99999 ), 5, "0", STR_PAD_LEFT );
}


/**
 * @param $d
 * @return array|float|int|string
 */
function intstrval($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = intstrval($v);
        }
    } elseif (is_string ($d)) {
        $d = trim($d);
        if(preg_match('/^[0-9]{1,10}$/',$d)) {
            return intval($d);
        } elseif(preg_match('/^[0-9]{1,}\.[0-9]{1,}$/',$d)){
            return floatval($d);
        } else {
            return strval($d);
        }
    } elseif (is_null($d)){
        return "";
    }
    return $d;
}

/**
 * 数组字符串格式化
 * @param $d
 * @return array|string
 */
function arr2strval($d) {
    if (is_array($d)) {
        foreach ($d as $k => $v) {
            $d[$k] = arr2strval($v);
        }
    } else if (is_string ($d)) {
        return strval($d);
    } else if (is_null($d)){
        return "";
    }
    return $d;
}

/**
 * @param array $arr
 * @param $data
 * @return array
 */
function filter_arr($arr=array(),&$data) {
    if(empty($data)) return array();
    foreach($data as $_k=>$_v){
        if(!in_array($_k,$arr)) unset($data[$_k]);
    }
}

/**
 *
 * @param array $a
 * @param array $b
 * @return string
 */
function arr3d($a=array(),$b=array()) {
    $data = array();
    foreach($a as $vk){
        if($b[$vk]) $data[] = $b[$vk];
    }
    return implode(',',$data);
}


function array2br($data=array()){
    $data = array_merge("trim",$data);
    return implode("\n",$data);
}

function br2array($br=''){
    $data = explode("\n",$br);
    return array_map("trim",$data);
}

function timestamp13(){
    list($usec, $sec) = explode(" ", microtime());
    $timestamp =  ((float)$usec + (float)$sec);
    return str_replace('.','',$timestamp);
}

/**
 * @param $key
 * @param $value
 * @param $url
 * @return string
 */
function url_set_value($key,$value,$url='') {
    if(empty($url)) $url = get_url();
    $a=explode('?',$url);
    $url_f=$a[0];
    $query=$a[1];
    parse_str($query,$arr);
    $arr[$key]=$value;
    if($arr['page']) unset($arr['page']);
    return $url_f.'?'.http_build_query($arr);
}
function id_auth($id,$type=1){
    $rarray = function($arr){
        if(is_array($arr)&&!empty($arr)) {
            $key = array_rand($arr);
            return $arr[$key];
        }
        return '';
    };
    $divisor = <<<divisor
9|62,17,36,89,54
8|61,43,28,90,55
7|13,77,20,86,59
6|14,88,00,39,25
5|16,24,70,03,58
4|81,69,33,72,04
3|41,30,07,63,85
2|15,74,66,80,23
1|19,34,78,56,02
0|91,99,51,82,73,84,35,32,27,10,71,42,47,31,44,48,49,05,52,75,92,40,87,09,12,79,60,95,11,76,29,50,26,83,45,37,01,67,93,68,08,38,98,46,65,94,96,18,22,53,57,64,21,06,97
divisor;
    if($type) {
        while(strlen($id)<8){
            $id = "0$id";
        }
        $num = strrev($id);
        $data = array();
        foreach(explode("\n",$divisor) as $line){
            $arr_line = explode('|',trim($line));
            $data[$arr_line[0]] = explode(',',$arr_line[1]);
        }
        $arr_new = array();
        foreach(str_split($num) as $item){
            $arr_new[] = $rarray($data[$item]);
        }
        return implode('',$arr_new);
    } else {
        $data = array();
        foreach(explode("\n",$divisor) as $line){
            $arr_line = explode('|',trim($line));
            foreach(explode(',',$arr_line[1]) as $item){
                $data[$item] = $arr_line[0];
            }
        }
        $arr_prev = array();
        foreach(str_split($id,2) as $v){
            $arr_prev[] = $data[$v];
        }
        return intval(strrev(implode('',$arr_prev)));
    }
}