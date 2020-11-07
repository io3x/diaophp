

# diaophp - 使用原生PHP语法、极简内核、内置异步任务、定时器、全自动配置的 php/java 双向RPC调用的php dubbo微服务框架

diaophp 不定义命名空间，不依赖composer包管理，几乎0封装，使用 AIC (All in Class) 编程模式架构，利用面向对象模式封装好，使用单例模式函数式编程调用，可以任意自由发挥，系统无任何约束。

框架仅仅只有基于原生PHP语法的结构，小白式代码，功能却十分强大，可以加入xxx.class.php,phpar包,composer包管理三种模式拓展应用功能,除第三方应用，内置代码都不定义命名空间。支持传统简单的单应用模式，单应用扩展模式(支持异步、定时器)，分布式dubbo协议微服务（核心）3种模式可根据需求自由选择。

默认基于文件目录路由,支持多种文本型(html,xml,md等)文档生成，参考phpcms模版引擎应用

多模统一调用入口:同一应用方法支持通过url地址、命令行cli、dubbo服务访问

分布式dubbo RPC远程调用无需任何定义服务配置项，支持php/java 双向RPC 4种模式调用；php dubbo服务支持异步调用，附带使用demo并提供java 服务端demo源码。详见具体文档。

![](https://php-images.oss-cn-qingdao.aliyuncs.com/uploadfile/md/202011/1108/395395E9B844EFB5910AA1FFF600EDB2.png)

# 快速应用示例
```php
<?php
defined('IN_CDO') or exit('illegal infiltration.');
class test_service {
    public function a($var=''){
        return __CLASS__.__LINE__.PHP_EOL;
    }
    public function b(){
        return __CLASS__.__LINE__.PHP_EOL;
    }
}
```

- URL模式访问: http://localhost/demo1/test_service/a?var=123456

- CLI命令行模式访问: php index.php /demo1/test_service/a 123456

- DUBBO微服务访问(只有在diaophp-provider-api已注册的应用下才支持): call_dubbo_service("com.github.io3x.php.test_service")->invoke('a',"123456");

# 应用部署

## 环境依赖
普通框架模式
- windows或linux php-5.6+即可

拓展框架模式
- windows或linux php-5.6+swoole-4.2.12

dubbo微服务模式
- 推荐 CentOS7+ php-7.1 redis-3.2 zookeerper-3.4.6
- php扩展 swoole-4.2.12、zookeeper-0.5.0
- windows 由于无zookeerper扩展，无法启动dubbo生产者和同步器。系统支持多模统一，在开发模式下可以使用命令行或url地址访问dubbo服务

## 快速部署
- CentOS7 可从 https://github.com/io3x/php7-env-bin 下载 php7-env-bin php7+apache 二进制bin文件直接运行包 绿色版Linux php运行环境 https://github.com/io3x/php7-env-bin/releases/download/a5728a7/io3x-env-php7299-apache2410-bin-v1.0.zip
- Windows 可下载 https://github.com/io3x/php7-env-bin/releases/download/46e9761/swoole_4.2.12-windows-x86_64.zip php集成swoole开发包

- ![](https://php-images.oss-cn-qingdao.aliyuncs.com/uploadfile/md/202011/1107/AA99FB057CC1BC9CAE55EC5D185732F7.png)

# 基础结构

## 文件结构树
```
bin为启动dubbo生产者服务、zookeerper到redis同步器、内置异步事件定时器服务脚本和一些演示脚本

├─bin
│      exter-starter-cmd.bat
│      exter-starter-sh.sh
│      provider-starter-sh.sh
│      rediszoo-starter-sh.sh

diaophp-consumer-web 为服务消费者的一些调用演示文件，可改名，可建立多个

├─diaophp-consumer-web
入口文件
│  │  index.php
│  │  
consumer 为一个模块名,任意命名即可
│  ├─consumer
│  │  │  cmer.class.php
│  │  │  demo.class.php
│  │  │  func.php
│  │  │  index.class.php
│  │  │  setting.php
│  │  │  
│  │  ├─classes
│  │  │      op.class.php
│  │  │      
│  │  └─templates
│  │      └─test
│  │              footer.html
│  │              header.html
│  │              index.html
│  │              
│  └─web
│      │  func.php
│      │  index.class.php
│      │  
│      ├─classes
│      │      op.class.php
│      │      
│      └─templates
│          └─test
│                  footer.html
│                  header.html
│                  index.html
│                  
diaophp框架核心文件,不同入口均可以引用
├─diaophp-framework-core
这里的 composer.json 只打包
│  │  composer.json
│  │  index.php
│  │  
一个接口文档模块例子，可删除，可从core目录移除
│  ├─api
│  │  │  func.php
│  │  │  index.class.php
│  │  │  test.class.php
│  │  │  
│  │  ├─classes
│  │  │      htmldoc.class.php
│  │  │      metadata.class.php
│  │  │      
│  │  └─templates
│  │      └─api
│  │              doc.html
│  │              header.html
│  │              index.html
│  │              
│  ├─app
│  │  │  common.func.php
│  │  │  config.php
init.php 自动加载配置文件
│  │  │  init.php
│  │  │  
│  │  └─libs
前置加载函数文件
│  │      │  global.func.php
│  │      │  
一些系统提供的class操作文件，可根据需要自动删减
│  │      ├─classes
│  │      │      alchemy_yaml.class.php
│  │      │      controller.class.php
│  │      │      cost_time.class.php
│  │      │      dubbo_provider_main.class.php
│  │      │      mysqlpdo.class.php
│  │      │      mysqlpdop.class.php
│  │      │      rds.class.php
│  │      │      rpc.class.php
│  │      │      rpc_event_callback.class.php
│  │      │      rurl.class.php
│  │      │      tpl_cache.class.php
│  │      │      
一些用到的其它函数库
│  │      └─functions
│  │              dubbo.func.php
│  │              my.func.php
│  │              tpl.func.php
│  │              untli.func.php
│  │              
系统缓存日志目录
│  ├─caches
│  │  ├─caches_tpl
│  │  ├─logs
│  │  └─tmp
系统配置文件目录
│  ├─config
│  │  │  default.php
│  │  │  USER-S5GQHFEU6H.php
│  │  │  
│  │  └─data
│  │          route.php
引用phar扩展或修改后的composer包
│  ├─extension
│  │  │  extension.php
│  │  │  index.html
│  │  │  
│  │  ├─crazyxman
│  │  │  └─dubbo-php-framework      
│  │  └─phar
│  │          super_closure-2.4.0.phar
│  │          
非传统意义的model文件,这里的文件是所有模块都可以调用的
│  ├─model
│  │  │  category_model.class.php
│  │  │  common_model.class.php
│  │  │  demo_model.class.php
│  │  │  md_model.class.php
│  │  │  
│  │  └─classes
内置异步任务服务模块，如不开启可删除整个目录
│  ├─service
│  │  │  crontab_demo.class.php
│  │  │  event_demo.class.php
│  │  │  func.php
│  │  │  starter.class.php
│  │  │  
│  │  └─classes
│  │          http_task.class.php
│  │          mem.class.php
│  │          parse_crontab.class.php
│  │          schedule.class.php
│  │          schedule_mem.class.php
│  │          
一个测试测试模块单元
│  ├─uploader
│  │  │  index.class.php
│  │  │  
│  │  └─templates
│  │          index_init.html
│  │          
第三方vendor 包，框架默认编译好，便于开箱即用
│  ├─vendor
│  │  │  autoload.php
│  │  │                          
│  │                  
另一个测试模块，可删除
│  └─web
│      │  func.php
│      │  index.class.php
│      │  
│      ├─classes
│      │      op.class.php
│      │      
│      └─templates
│          └─test
│                  footer.html
│                  header.html
│                  index.html
│                  
服务生产者
├─diaophp-provider-api
│  │  index.php
│  │  
│  ├─demo1
│  │  │  func.php
│  │  │  ip_service.class.php
│  │  │  test_service.class.php
│  │  │  
│  │  └─classes
│  │          op.class.php
│  │          
│  ├─demo2
│  │      shop_service.class.php
│  │      
│  └─service
│      │  func.php
│      │  provider.class.php
│      │  rediszoo.class.php
│      │  setting.php
│      │  sh-zoo-killd-restart.sh
│      │  
│      └─classes
java 服务生产者消费者
└─java-provider-api
    │  │  
    │  └─conf
    │      │  application-dev.yml
    │      │  application.yml
    │      │  
    │      └─templates
    │          │  doc.yaml
    │          │  
    │          ├─api
    │          │  └─doc
    │          │          doc.html
    │          │          header.html
    │          │          index.html
    │          │          widget.html
    │          │          
    │          └─default
    │                  表格样式-1.html
    │                  
    ├─logs
    └─src
        ├─main
        │  ├─java
        │  │  └─com
        │  │      └─github
.
.
.
java引用php dubbo服务定义的接口
        │  │              ├─php
        │  │              │      ip_service.java
        │  │              │      shop_service.java
        │  │              │      
        │  │                              
java配置文件(看起来很奇怪，经过测试这种模式才可以跑起来)
        │  └─resources
        │      │  application-dev.yml
        │      │  application.yml
        │      │  logback-spring.xml
        │                  
        └─test
            └─java


```
## 基础结构单元

### 应用单元
- diaophp-framework-core、diaophp-consumer-web、diaophp-provider-api都表示应用 diaophp-framework-core为核心应用，基础模式下只需要这个目录。每个应用下面有个index.php入口文件,该文件可由php-fpm 指定入口文件,也是cli命令行模式的入口文件

### 公用引用单元及依赖管理
- 如非十分必要，框架不推荐使用composer包管理，直接把引用的文件封装成class放到系统classes目录或模块classes目录即可，如alchemy_yaml.class.php 就是经过 composer包改造的
- 如果引用composer依赖，系统支持两种方式:1.在diaophp-framework-core下面 2.放到 diaophp-framework-core/extension 下面，可以是phpar包也可以是文件然后自行打包，如super_closure-2.4.0.phar 或 crazyxman/dubbo-php-framework (目录是为了修改第三方依赖不重新打包)
- diaophp-framework-core/app/libs 下的类为各应用各模块都可以直接调用(new 对应类即可)
- model模块功能和系统classes里面的一致，只是执行优先级没那么高

### 模块单元

- api、service、web 为应用下的模块 执行空间相对独立，跨模块下的classes里面的文件需要使用loader::load_module_classes("web","op");引入才行,跨应用模块文件调用需要自行引入
- 每个模块下面默认定义了一个 xxx.class.php入口文件;classes目录逻辑代码目录;func.php 一些当前模块的函数文件；setting.php 一些当前模块的常量

### AIC结构单元
- 在AIC开发模式下只需要定义入口，然后我们可以把代码逻辑写在任意位置，自由控制，做到应用隔离，模块隔离即可

## 框架加载顺序
- 应用入口 index.php > init.php > global.func.php > 配置文件 > 第三方composer包无修改 > common.func.php > 系统classes > 入口模块func.php > 入口模块setting.php >入口模块xxx.class.php > 系统函数库组 > 修改后的第三方应用 > 反射执行xxx.class.php的入口方法

## 配置文件
- 全部配置文件在 diaophp-framework-core/config 使用主机hostname来区分环境的，如无匹配，使用default.php

## 路由配置
- 默认路由支持 /模块名/入口文件名/方法名 参数访问 如core下面的web模块 http://localhost/web/index/init
- php-fpm调用模式下，直接在config/data/route.php 文件修改

## 多模统一

如 diaophp-provider-api 下面的应用 demo1模块下test_service的a方法
- URL模式访问: http://localhost/demo1/test_service/a?var=123456

- CLI命令行模式访问: php index.php /demo1/test_service/a 123456

- DUBBO微服务访问(只有在diaophp-provider-api已注册的应用下才支持): call_dubbo_service("com.github.io3x.php.test_service")->invoke('a',"123456");

# 基础单应用模式
基础应用模式只需要diaophp-framework-core应用下面的文件即可

## 入口文件index及 隐藏index.php URL改写配置

#### apache 规则
```
<IfModule mod_rewrite.c>
RewriteEngine on
RewriteRule ^index\.php$ - [L]
RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} !-f
RewriteCond %{DOCUMENT_ROOT}%{REQUEST_FILENAME} !-d
RewriteRule ^(.+)$ /index.php/$1 [L]
</IfModule>
```

#### nginx规则
```
if ( !-e $request_filename ) {
    rewrite  ^/(.*)$  /index.php/$1  last;
    break;
}
```

## AIC结构单元

```
web
│  func.php
│  index.class.php
│
├─classes
│      op.class.php
│
└─templates
    └─test
            footer.html
            header.html
            index.html
```

api、web、service、uploader均为独立模块，可自由删减
index.class.php 为控制层入口文件,可以直接new使用classes里面的文件
func.php 为当期模块函数

# 拓展框架模式

- 扩展架构模式需要php swoole扩展支持 推荐4.2.12
- 拓展模式不支持分布式调用,只作为单应用模式功能的补充
- 扩展服务的执行空间等效于service模块下的cli 命令行
- 扩展服务和dubbo服务无任何依赖关系,只是扩展php，是php支持 异步事件和定时器

## 开启扩展模式
修改配置文件 SERVICE 节点
开启服务,执行
cd bin && ./exter-starter-sh.sh

## 定时任务
```php
<?php
defined('IN_CDO') or exit('illegal infiltration.');
class crontab_demo {
    /**
     * @param string $crontab
     */
    public final function one($crontab="*/1 * * * *"){
        return date("Y-m-d H:i:s").$crontab;
    }
｝
```

只需要指定 $crontab 执行参数即可

## 异步事件调用不返回结果

```php
$tt = new cost_time();
        $tt->point_time("异步事件调用时间:");
        for($i=0;$i<10;$i++){
            rpc::event(md5_file(__FILE__).__LINE__,function($a,$b,$c){
                mt_srand();
                sleep(mt_rand(1,2));
                echo date("Y-m-d H:i:s");
                print_r(array($a,$b,$c));
            },11,222,$i);
            $tt->point_time("T:");
        }
        print_r($tt->result());
```

## 同步等待返回调用
```php
$tt = new cost_time();
        $tt->point_time("同步等待返回调用:");
        $r=[];
        for($i=0;$i<3;$i++){
            $r[] = rpc::event_callback(md5_file(__FILE__).__LINE__,function($a,$b,$c){
                mt_srand();
                sleep(mt_rand(1,2));
                echo date("Y-m-d H:i:s");
                return [date("Y-m-d H:i:s"),$a,$b,$c];
            },11,222,$i);
            $tt->point_time("T:");
        }
        print_r($r);
        print_r($tt->result());
```


## 并发同步等待返回结果

```php
$tt = new cost_time();
        $tt->point_time("并发同步等待返回:");
        $tt->point_time("T:");
        $event_callbacks = rpc::event_callbacks();
        for($i=0;$i<10;$i++){
            $event_callbacks->push_event(md5_file(__FILE__).__LINE__,function($a,$b,$c){
                mt_srand();
                sleep(mt_rand(1,2));
                echo date("Y-m-d H:i:s");
                return [date("Y-m-d H:i:s"),$a,$b,$c];
            },11,222,$i);
            $tt->point_time("T:");
        }
        $tt->point_time("T:");
        print_r($event_callbacks->exec(5000));
        $tt->point_time("T:");
        print_r($tt->result());
```

# dubbo微服务分布式应用模式

dubbo微服务基于 dubbo-php-framework和 crazyxman/dubbo-php-framework 修改而来，只保留了dubbo解析协议，做了如下变更:

- 移除yaml独立配置文件
- 移除php伪注解
- 移除服务配置声明,改为自动识别
- 增加异步调用支持:修改方法添加async_前缀即可
- 修改redis同步zookeeper模式,redis存储由list改为set模式 增加服务活跃检测

## 启动php dubbo微服务生产者
- 先运行 **bin/provider-starter-sh.sh** 启动服务

- 再运行 **bin/rediszoo-starter-sh.sh** 把已启动注册的服务同步到redis 便于读取服务信息

- 部署多个生产者时,bin/rediszoo-starter-sh.sh 只需要部署一个服务，只需要运行一次

- 由于dubbo服务使用swoole网络协议构建，需要遵循swoole规范，如服务代码中不要出现exit中断关键字、非异步调用不要出现sleep、随机前需要先使用mt_srand()等

## 4种调用模式

1. php 消费者调用 php dubbo服务(推荐)
2. php 消费者调用 java dubbo服务(推荐)
3. java 消费者调用 php dubbo服务（不推荐）
4. java 消费者调用 java dubbo服务(纯java开发范畴)

## 定义服务生产者

在 diaophp-provider-api 应用下面的模块新建一个 xxx_service.class.php 文件即可，如demo2/shop_service.class

```php
<?php
/**
 * Class demo2
 */
class shop_service extends dubbo_provider_main {
    private function m0(){
        echo "m0";
    }
    /**
     *
     */
    public function m1($var1,$var2,$var3="abc"){
        $r =  json_encode_ex(func_get_args()).__CLASS__.__METHOD__;
        echo $r;
        return $r;
    }

    /**
     * 异步m1服务
     */
    public function async_m1($var1,$var2,$var3="abc"){
        mt_srand();
        sleep(mt_rand(1,5));
        return $this->m1($var1,$var2,$var3);
    }
}
```


- diaophp-provider-api应用模块下_service.class.php为固定模式，系统会自动识别注册

- diaophp-provider-api应用模块下所有目录.class文件均可以直接new 调用,可以视为都是同一个目录命名空间下的文件，只是为了方便区分放到了demo1、demo2等目录，diaophp-provider-api应用下的模块不隔离

- 可以继承 dubbo_provider_main 也可以不继承

- 方法前加固定 async_ 表示异步调用，异步调用无返回

## 消费者调用生产者服务
- 直接使用 call_dubbo_service("com.github.io3x.php.shop_service")->invoke('m1',100,"我的店铺-{$i}",timestamp13());方法调用模式,由于call_dubbo_service为全局函数，任意位置均可调用

- 示例参考 consumer-web\consumer\cmer.class.php，m3 、m4为php调用java服务示例方法，m7为php调用php dubbo服务方法

- 启动服务后，可执行 bin\测试php调用java dubbo服务-cmd.bat，bin\测试php调用php dubbo服务-cmd.bat 直接查看效果

# php调用java-dubbo服务端

## 编译springboot dubbo集成源码
- 使用idea打开 java-provider-api源码

### 按照步骤修改配置

- 注意修改mysql连接和zookeerper连接
- 不要觉得配置很奇怪，经测试，这样配置才可以同时启动生产者服务和消费者


- ![](https://php-images.oss-cn-qingdao.aliyuncs.com/uploadfile/md/202011/1108/330444A3FAA348D558B691BB2CB7C632.png)

注意配置的dev有时候需要勾选两次才生效，然后在启动运行 BootApplication

## 启动应用
运行后打开 http://localhost:10388/

![](https://php-images.oss-cn-qingdao.aliyuncs.com/uploadfile/md/202011/1108/02D933C0750DCEB4E17BB0AF6F8ABBA4.png)

可查看测试服务调用接口

# 其它

## 常见问题
暂未整理好

## 问题交流
请到 https://gitee.com/io3x/diaophp 提问

