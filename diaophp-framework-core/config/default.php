<?php
defined('IN_CDO') or exit('illegal infiltration.');
//error_reporting(0);
/*ini_set("display_errors", "on");
error_reporting(E_ALL);*/
return array(
    # mysql 数据库连接
    'DB_HOST' => '127.0.0.1',
    'DB_PORT' => '3306',
    'DB_USER' => 'root',
    'DB_PWD' => 'root',
    'DB_NAME' => 'testdb',


    # 路由绑定模块配置说明,如果存在,如果开启路由绑定,默认3级入口变成2级入口形式
    'ROUTE'=>array(
        'books.diaophp.loc'=>['m'=>'books','c'=>'index','a'=>'init'],
    ),

    /*redis 数据库*/
    'REDIS_HOST'=>'192.168.0.105',
    'REDIS_AUTH'=>'',

    # 内置异步事件、crontab服务配置
    'SERVICE'=>[
        'host'=>'127.0.0.1',
        'port'=>10381,
        'worker_num'=>4,
        'task_worker_num'=>16
    ],

    /*相关内置服务配置*/
    'SWOOLE_DUBBO_SERVICE'=>[
        'provider_service_set'=>['reactor_num' => 2,'worker_num' => 16,'task_worker_num'=>16,'daemonize' => 0],

        # 配置 zookeeper 的ip地址和端口号
        'registry_zookeeper'=>['protocol' => 'zookeeper','address' => '192.168.0.105:2181'],
        'provider_name'=>'diaophp-provider-api',
        # 默认的版本号和组,主要针对java dubbo服务做相应的修改,默认不设置组
        'provider_dubbo_version'=>'2.6.0',
        'provider_dubbo_group'=>'',
        # 注册PHP服务别名前缀,主要用于java调用php需要抽象定义类
        'provider_prefix'=>'com.github.io3x.php',

        # DUBBO 服务指定ip和端口
        'host'=>inner_ip(),
        'port'=>10389,
    ],

    # 是否引用composer包
    'VENDOR'=>[
        'IS_VENDOR'=>1
    ]
);