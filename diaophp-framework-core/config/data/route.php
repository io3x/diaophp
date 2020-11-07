<?php
defined('IN_CDO') or exit('illegal infiltration.');
/*
 * 模块访问控制
 *
 * 如果配置了二级域名则走二级域名模式,否则走路径模式
*/
/*根据REQUEST_URI来判断*/
$path_info = array_values(array_filter(explode("/",preg_replace('/\.(html|htm|md)$/','',str_replace('/index.php','',$_SERVER['PHP_SELF'])))));

$ROUTE = cnf('ROUTE');
if(isset($ROUTE[$_SERVER['HTTP_HOST']])&&preg_match('/^[0-9a-zA-Z-_]+$/i',$ROUTE[$_SERVER['HTTP_HOST']]['m'])) {
    define("ISHH",1);
    $HRTE = $ROUTE[$_SERVER['HTTP_HOST']];
    if($HRTE['m']) {
        $_GET['m'] = $HRTE['m'];
        if(isset($path_info[0])&&!empty($path_info[0])) {
            $_GET['c'] = $path_info[0];
        } else {
            $_GET['c'] = $HRTE['c'];
        }
        if(isset($path_info[1])&&!empty($path_info[1])) {
            $_GET['a'] = $path_info[1];
        } else {
            $_GET['a'] = $HRTE['a'];
        }

        /*自定义路由*/
        if($_GET['m']=='books') {
            define("BOOKS_CAT_ID",$path_info[0]);
            define("BOOKS_VIEW_ID",$path_info[1]);
            define("BOOKS_P2",end($path_info));
            $_GET['c'] = "index";
            $_GET['a'] = "init";
        }

    } else {
        if(isset($path_info[0])&&!empty($path_info[0])) $_GET['m'] = $path_info[0];
        if(isset($path_info[1])&&!empty($path_info[1])) $_GET['c'] = $path_info[1];
        if(isset($path_info[2])&&!empty($path_info[2])) $_GET['a'] = $path_info[2];
    }
} else {
    define("ISHH",0);
    if(isset($path_info[0])&&!empty($path_info[0])) $_GET['m'] = $path_info[0];
    if(isset($path_info[1])&&!empty($path_info[1])) $_GET['c'] = $path_info[1];
    if(isset($path_info[2])&&!empty($path_info[2])) $_GET['a'] = $path_info[2];
}


if (isset($_GET['m']) && !empty($_GET['m'])) {
    @define(ROUTE_M, $_GET['m']);
} else {
    @define(ROUTE_M, 'admindui');
}

if (isset($_GET['c']) && !empty($_GET['c'])) {
    @define(ROUTE_C, $_GET['c']);
} else {
    @define(ROUTE_C, 'login');
}

if (isset($_GET['a']) && !empty($_GET['a'])) {
    @define(ROUTE_A, $_GET['a']);
} else {
    @define(ROUTE_A, 'login');
}

