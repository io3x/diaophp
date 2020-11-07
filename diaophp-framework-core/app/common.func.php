<?php
defined('IN_CDO') or exit('illegal infiltration.');

/**
 * 设置文件缓存
 * @param $cache_name
 * @param $data
 * @param string $cache_path
 */
function set_cache($cache_name,$data,$cache_path='caches_data'){
    if(!is_dir(CACHE_PATH.$cache_path)) {
        mkdir(CACHE_PATH.$cache_path, 0777, true);
        chmod(CACHE_PATH.$cache_path,0777);
    }
    file_put_contents(CACHE_PATH.$cache_path.DIRECTORY_SEPARATOR.md5($cache_name).'.php',serialize($data));
}

/**
 * 获取文件缓存
 * @param $cache_name
 * @param string $cache_path
 * @return array
 */
function get_cache($cache_name,$cache_path='caches_data'){
    $conent = @file_get_contents(CACHE_PATH.$cache_path.DIRECTORY_SEPARATOR.md5($cache_name).'.php');
    return unserialize($conent);
}



/**
 * curl 异步通知
 * @param string $url
 * @param array $data
 * @param string $logname
 * @return mixed
 */
function sec_api($url='',$data=array()){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_NOSIGNAL,true);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,1); //建立连接超时
    curl_setopt($curl, CURLOPT_TIMEOUT,1); //最大持续连接时间
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function sec_get($url=''){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_NOSIGNAL,true);
    curl_setopt($curl, CURLOPT_HTTPGET, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,1); //建立连接超时
    curl_setopt($curl, CURLOPT_TIMEOUT,1); //最大持续连接时间
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}

function http_post($url='',$data=array()){
    debug_log(array($url,$data),'cron');
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5); //建立连接超时
    curl_setopt($curl, CURLOPT_TIMEOUT,10); //最大持续连接时间
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}


function curl_post($url='',$data=array()){
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL,$url);
    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_POSTFIELDS,$data);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT,5);
    curl_setopt($curl, CURLOPT_TIMEOUT,100);
    $result = curl_exec($curl);
    curl_close($curl);
    return $result;
}



function json_encode_ex($value) {
    if (version_compare(PHP_VERSION,'5.4.0','<')) {
        $str = json_encode($value);
        $str = preg_replace_callback(
            "#\\\u([0-9a-f]{4})#i",
            function($matchs) {
                return iconv('UCS-2BE', 'UTF-8', pack('H4', $matchs[1]));
            },
            $str
        );
        $out =  $str;
    } else {
        $out =  json_encode($value, JSON_UNESCAPED_UNICODE);
    }
    return str_replace("\\/", "/",$out);
}





