<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * 接口文档
 */
class htmldoc {

    /**
     * 返回接口文档
     * @return array
     */
    public static  function config(){
        $bc  = self::bc();
        foreach($bc as $k=>$v){
            if($v['b_fds']) {
                $bc[$k]['b_fds'] = array();
                $a = explode("\n",$v['b_fds']);
                foreach($a as $x){
                    $tmp = explode("|",trim($x));
                    $bc[$k]['b_fds'][] = array(
                        'name'=>trim($tmp[0]),'value'=>trim($tmp[1]),'text'=>trim($tmp[2]),'type'=>trim($tmp[3])
                    );

                }
            }

            if($v['e_fds']) {
                $bc[$k]['e_fds'] = array();
                $a = explode("\n",$v['e_fds']);
                foreach($a as $x){
                    $tmp = explode("|",trim($x));
                    $bc[$k]['e_fds'][] = array(
                        'name'=>trim($tmp[0]),'value'=>trim($tmp[1]),'text'=>trim($tmp[2]),'type'=>trim($tmp[3])
                    );

                }
            }
        }
        return $bc;
    }

    /**
     *
     */
    private  static function bc(){
        $host_url = HOST_URL.ROUTE_M.'/';
        $doc = array();
        /*接口..........0............*/
        $doc['demotest']['title'] = '/demo/test';
        $doc['demotest']['url'] = '/demo/test';
        $doc['demotest']['b_fds'] = <<<f
access_token||授权码
tags[]||标签1
tags[]||标签2
tags[]||标签3
f;
        $doc['demotest']['e_fds'] = <<<f
isure||是否确定(11是,10否)
f;


        /*接口..........3............*/
        $hu = HOST_URL.'/'.ROUTE_M;
        $doc['image']['title'] = '图片上传接口';
        $doc['image']['url'] = HOST_URL.ROUTE_M.'/'.'upload2/image';
        $doc['image']['memo'] = <<<memo
如果直接返回图片地址URL,请使用 {$hu}upload2/imgurl 接口
memo;
        $doc['image']['b_fds'] = <<<f
img||上传图片|file
f;
        $doc['image']['e_fds'] = <<<f
callback||上传回调函数名
f;

        /*接口..........3............*/
        $doc['image2']['title'] = '图片上传回调接口';
        $doc['image2']['url'] = HOST_URL.ROUTE_M.'upload2/image2';
        $doc['image2']['b_fds'] = <<<f
refer|{$host_url}upload2/callback|回调地址
img||上传图片|file
f;



        return $doc;

    }
}