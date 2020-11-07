<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * Class starter
 * 基于swoole服务器启动器模块,除非对swoole个版本及差异非常熟悉,否则不建议去修改
 */
class starter {
    /**
     *
     */
    public function init(){
        clean_cache("");
        define("SWOOLE_PRE_NAME",basename(ROOT_PATH));
        echo SWOOLE_PRE_NAME;
        /*ini_set('swoole.enable_coroutine','Off');
        \Swoole\Coroutine::set(['enable_coroutine' => false]);
        */
        swoole_async_set(['enable_coroutine' => false]);


        /*启动任务计划进程*/
        (new schedule());





        /**
         * 启动 http任务服务 兼容性最强
         */
        (new http_task());


        try {
            //swoole_set_process_name(SWOOLE_PRE_NAME."-schedule");
            /*注册回收进程事件,当子进程结束后,回收资源*/
            swoole_process::signal(SIGCHLD, function($sig) {
                //必须为false，非阻塞模式
                while($ret =  swoole_process::wait(false)) {
                    #sys_log("PID={$ret['pid']} stop","swol_crontab");
                    //echo "PID={$ret['pid']} stop\n";
                }
            });
            /*主进程代码执行完毕后不退出*/
            //swoole_process::daemon(true,false);
        } catch (Exception $e) {
            print $e->getMessage();
        }


    }
}