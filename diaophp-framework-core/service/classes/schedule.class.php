<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * Class schedule
 * 基于swoole的PHP内置定时任务调度
 */
class schedule {
    public function __construct() {
        /*新建一个动态存储计划任务的内存表*/
        $table = new swoole_table(1024);
        $table->column('sec', swoole_table::TYPE_INT, 10);
        $table->column('cmd', swoole_table::TYPE_STRING,256);
        $table->column('key', swoole_table::TYPE_STRING,256);
        $table->create();
        global $swol_mem;
        $swol_mem = new schedule_mem($table);
        (new swoole_process(function(swoole_process $worker) {
            global $swol_mem;
            print "正在启动任务计划进程 \n";
            $demic_data = array();

            $runparse = function ($swol_mem){
                $crontabs = load_crontab();
                /*初始化加入*/
                foreach ($crontabs as $prf => $crontab) {
                    $parse = parse_crontab::parse("0 ".$crontab);
                    print "{$crontab}";
                    if(empty($parse)) continue;
                    $secs = array_values($parse);
                    $demic_data[$prf]["key"] = $prf;
                    $demic_data[$prf]["cmd"] = $crontab;
                    if(isset($secs[0])&&$secs[0]>=0) $demic_data[$prf]["sec"] = intval($secs[0]);
                }
                foreach($demic_data as $k=>$v){
                    $swol_mem->push($v);
                }
            };
            /*初始化记录一次*/
            $runparse($swol_mem);


            #每分钟检测60S内将要执行的任务加入队列
            swoole_timer_tick(60000,function($timer_id) use($runparse,$swol_mem) {
                $runparse($swol_mem);
            });

            #每秒检测队列处理需要执行的任务
            swoole_timer_tick(1000,function($timer_id)  use($swol_mem)  {
                if($swol_mem->counts()) {
                    foreach($swol_mem->table as $v){
                        #如果到了执行时间,创建子进程执行任务
                        if(strval($v['sec'])==='0') {
                            (new swoole_process(function(swoole_process $worker) use($v) {
                                try {
                                    list($class,$method,$param) = array_values(array_filter(explode("/",$v['key'])));
                                    $r = (new $class())->$method($v['cmd']);
                                } catch (Exception $e) {
                                    $r = $e->getMessage();
                                }
                                debug_log([$v['key'],$r],"schedule");
                            },false,0))->start();
                        }
                    }
                    $swol_mem->decr(1);
                }
            });


            /*自动回收子进程*/
            swoole_process::signal(SIGCHLD, function($sig) {
                while($ret =  swoole_process::wait(false)) {
                    //echo "PID={$ret['pid']}\n";
                    //debug_log(['自动回收子进程',$ret],"schedule");
                }
            });

        },false,false))->start();
    }
}