<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * 提取内容部分
 * @param $s_tag
 * @param $e_tag
 * @param $content
 * @return string
 */
function cut_content($s_tag,$e_tag,$content){
    $tmp = explode($s_tag,$content);
    if($tmp[1]) {
        $tmp2 = explode($e_tag,$tmp[1]);
        if($tmp2[0]) return $tmp2[0];
    }
    return '';
}

/**
 * 移除内容部分
 * @param $s_tag
 * @param $e_tag
 * @param $content
 * @return mixed
 */
function out_content($s_tag,$e_tag,$content){
    $tmp1 = explode($s_tag,$content);
    if($tmp1[1]) {
        $tmp2 = explode($e_tag,$tmp1[1]);
        if($tmp2[0]) {
            return str_replace("{$s_tag}{$tmp2[0]}{$e_tag}","",$content);
        }
    }
    return $content;
}


function fillder_html($data){
    /*过滤表格标签*/
    $data = preg_replace("/<table([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/table>/i", "", $data);
    $data = preg_replace("/<tr([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/tr>/i", "", $data);
    $data = preg_replace("/<td([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/td>/i", "", $data);
    $data = preg_replace("/<tbody([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/tbody>/i", "", $data);

    /*过滤属性*/
    $data = preg_replace('/data-(weight|width|height|type|src)=["\']{1}.*?["\']{1}/isum','',$data);
    $data = preg_replace('/img_(weight|width|height|type|src)=["\']{1}.*?["\']{1}/isum','',$data);
    $data = preg_replace('/(id|class|style|width|height|type|data-ke-src)=["\']{1}.*?["\']{1}/isum','',$data);


    /*通用标签过滤*/
    $data = preg_replace("/<figure([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/figure>/i", "", $data);
    $data = preg_replace("/<div([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/div>/i", "", $data);
    $data = preg_replace("/<a([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/a>/i", "", $data);

    $data = preg_replace("/<html([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/html>/i", "", $data);

    $data = preg_replace("/<head([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/head>/i", "", $data);

    $data = preg_replace("/<body([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/body>/i", "", $data);

    $data = preg_replace("/<article([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/article>/i", "", $data);

    $data = preg_replace("/<font([^>]+)?>/i", "", $data);
    $data = preg_replace("/<\/font>/i", "", $data);

    $data = preg_replace("/<![^>]*?>/i","", $data);

    /*过滤标签及内容*/
    $data = preg_replace("/<script[\s]*[^>]*?>.*?<\/script>/isum", "", $data);
    $data = preg_replace("/<style[\s]*[^>]*?>.*?<\/style>/isum", "",$data);
    /*$content = preg_replace("/<pre[\s]*[^>]*?>.*?<\/pre>/isum", "",$content);*/
    $data = preg_replace("/<form[\s]*[^>]*?>.*?<\/form>/isum", "",$data);
    $data = preg_replace("/<iframe[\s]*[^>]*?>.*?<\/iframe>/isum", "",$data);
    $data = preg_replace("/<link[\s]*[^>]*?>.*?<\/link>/isum", "",$data);
    $data = preg_replace("/<link[^>]*?\/>/isum", "",$data);
    $data = preg_replace("/<ol[\s]*[^>]*?>.*?<\/ol>/isum", "",$data);

    /*处理图片链接*/
    $data = str_replace(array('="//',"='//"),array('="http://',"='http://"),$data);

    $data = htmlspecialchars_decode($data);

    $data = str_replace("'",'"',$data);

    /*html小写
    $data = strtolower($data);*/

    return trim($data);
}


/**
 * 通配符*转正则表达式
 * @param $exp
 */
function asterisk2preg($exp){
    $exp = str_replace("*","ℹ0",$exp);
    $pq =  preg_quote($exp);
    $pq = str_replace('ℹ0','(.*?)',$pq);
    $preg = "#".$pq."$#Uim";
    return $preg;
}


function date_tian($startdate,$enddate){
    $days=round((strtotime($enddate)-strtotime($startdate))/86400)+1;
    return $days;
}

/**
 * 智能UTF8转码
 * @param $data
 * @return string
 */
function utf8content($data){
    if(!empty($data)){
        $fileType = mb_detect_encoding($data) ;
        if( $fileType != 'UTF-8'){
            $data = iconv("gbk","utf-8//IGNORE",$data);
        }
    }
    return $data;
}
