<?php
defined('IN_CDO') or exit('illegal infiltration.');
/*
 * */
class common_model extends mysqlpdop {
    /**
     * 初始化构造连接
     */
    public function __construct(){
        parent::__construct(cnf('DB_HOST'),cnf('DB_NAME'),cnf('DB_USER'),cnf('DB_PWD'),'utf8');
    }
}