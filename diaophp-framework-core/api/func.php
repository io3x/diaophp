<?php
defined('IN_CDO') or exit('illegal infiltration.');

function request_log($log_name="request"){
    $d = array();
    $d['_UA'] = $_SERVER['HTTP_USER_AGENT'];
    $d['_POST'] = str_replace("\n","",var_export($_POST,1));
    $d['_GET'] = str_replace("\n","",var_export($_GET,1));
    global $body;
    $d['_PUT'] = str_replace("\n","",$body);
    $log_dir = CACHE_PATH."logs".DIRECTORY_SEPARATOR.date("Ym").DIRECTORY_SEPARATOR.date("md").DIRECTORY_SEPARATOR;
    if(!is_dir($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    $logs_str = "";
    foreach($d as $k=>$v){
        $logs_str.="\n\t\t\t{$k}:\t{$v}";
    }
    $error_log = date("Y-m-d H:i:s")."\t".get_url()."\t".ip()."\t".$logs_str."\n";
    error_log($error_log,3,$log_dir.$log_name."_".date("Ymd")."_log.txt");
}


/**
 * 接口信息返回
 * @param string $ret
 * @param string $msg
 * @param array $info
 * @return mixed|string
 */
function r_info($msg='',$info=array(),$ret='no',$eal=true){
    $arr = intstrval(array(
        'ret'=>$ret,
        'msg'=>$msg,
        'info'=>$info,
    ));
    if($eal) {
        header('Access-Control-Allow-Origin: *');
        //header('Content-type: plain/json');
        $json =   json_encode_ex($arr);
    } else {
        $json =   json_encode_ex($arr);
    }
    $callback = addslashes($_REQUEST['callback']);
    if(!empty($callback)&&preg_match('/[a-z0-9A-Z-_]+/i',$callback)) {
        $out  = "{$callback}({$json})";
        exit($out);
    } else {
        exit($json);
    }
}
