<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * Class task
 */
class http_task {
    public function __construct() {
        print "正在启动异步RPC服务... \n";
        (new swoole_process(function(swoole_process $worker) {
            //SWOOLE_BASE 兼容 windows下 4.2.12
            $serv = new swoole_http_server(cnf("SERVICE","host"), cnf("SERVICE","port"),SWOOLE_BASE);
            $serv->set(array(
                'worker_num' =>cnf("SERVICE","worker_num"),
                'task_worker_num' => cnf("SERVICE","task_worker_num"),
                'log_file'=>CACHE_PATH."logs".DIRECTORY_SEPARATOR."10381.log"
                /*'enable_coroutine'=>true,*/
            ));

            $serv->kvdb = new mem();

            $serv->addProcess(new swoole_process(function (swoole_process $worker) use ($serv) {
                swoole_timer_tick(150*1000, function () use ($serv) {
                    global $swol_mem;
                    $ss = [];
                    $ss['crontab'] = array(
                        'runing'=>$swol_mem->data(),
                        'runlist'=>load_crontab()
                    );
                    $ss['stats'] = $serv->stats();
                    $ssvar = json_encode_ex($ss);
                    print date("Y-m-d H:i:s")."\t".$ssvar.PHP_EOL;
                    debug_log($ss,"swoole_starter_server");
                });
            }, false, false));


            $serv->on('request', function ($req, $response) use($serv) {
                //print "request ".strlen($req->getData());
                print $req->getData();
                if($req->server['path_info'] == '/favicon.ico' || $req->server['request_uri'] == '/favicon.ico'){
                    return $response->end();
                }
                $_GET = $req->get;
                $_POST = $req->post;
                /*自动调用当期方法*/
                $method = str_replace('/','',$req->server['path_info']);

                /*异步任务*/
                if($method=='stats') {
                    return $response->end(json_encode(array_merge(["worker_id"=>$serv->worker_id],$serv->stats())));
                }

                /*后台执行的定时任务*/
                if($method=='crontab') {
                    global $swol_mem;
                    $r = array(
                        'runing'=>$swol_mem->data(),
                        'runlist'=>load_crontab()
                    );
                    return $response->end(json_encode_ex($r));
                }

                if($_POST['op']=='kvdb'&&$_POST['data']) {
                    $op = $_POST['op'];
                    $data = unserialize($_POST['data']);
                    $kvdb_method = $data[0];
                    print "处理 {$op}";
                    $r = call_user_func_array([$serv->kvdb,$kvdb_method],$data[1]);
                    return $response->end(json_encode_ex([$r]));
                }



                if($_POST['op']=='event'&&$_POST['data']) {
                    $task_id = $serv->task(array($_POST['op'],$_POST['data']));
                    return $response->end($task_id);
                }

                if(in_array($_POST['op'],['event_sync','event_callback'])&&$_POST['data']) {
                    $response->detach();
                    $serv->task(array($_POST['op'],$_POST['data'],$response->fd));
                } else {
                    return $response->end(-1);
                }


            });


            $serv->on('Task', function (swoole_server $serv, $task_id, $worker_id, $data) {
                $action = $data[0];
                $args = unserialize($data[1]);
                if(empty($action)||empty($args)) return 0;

                $a = $args[0];
                unset($args[0]);
                $param_args = array_values($args);

                if(in_array($action,['event_sync','event_callback'])) {
                    $fd =$data[2];
                    $response = swoole_http_response::create($fd);
                    $r =  call_user_func_array(sc_unserialize($a),$param_args);
                    return $response->end(serialize($r));
                } else {
                    call_user_func_array(sc_unserialize($a),$param_args);
                }


                $serv->finish($task_id);
            });

            $serv->on('Finish', function (swoole_server $serv, $task_id, $data) {
                echo "Task#$task_id finished ".PHP_EOL;
                return $data;
            });

            $serv->start();
        },false,false))->start();
    }
}