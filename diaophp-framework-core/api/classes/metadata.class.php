<?php
defined('IN_CDO') or exit('illegal infiltration.');
    /**
     * 接口文档
     */
class metadata {
    /**
     * @var array
     */
    public static $info_status = array(
        '1'=>'未发布',
        '2'=>'已发布',
        '3'=>'发布异常',
        '4'=>'预处理',
        '5'=>'存档库',
    );
}