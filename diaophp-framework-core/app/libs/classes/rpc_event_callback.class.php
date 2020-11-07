<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 */
class rpc_event_callback {
    private $op_datas = [];
    private $url;
    public function __construct() {
        $this->url = "http://".cnf("SERVICE","host").":".cnf("SERVICE","port")."/";
    }


    private function rolling_curl($ms_timeout=3000) {
        $delay = 5;
        $callback = function($data, $delay) {
            /*preg_match_all('/<h3>(.+)<\/h3>/iU', $data, $matches);
            usleep($delay);
            return compact('data', 'matches');*/
            usleep($delay);
            return $data;
        };
        $queue = curl_multi_init();
        $map = array();

        foreach ($this->op_datas as $k=>$item) {
            $rpc_data = array(
                'op'=>$item['op'],
                'data'=>serialize($item['args'])
            );
            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL,$this->url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS,$rpc_data);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS,1000);
            curl_setopt($ch, CURLOPT_TIMEOUT_MS,$ms_timeout);
            curl_setopt($ch, CURLOPT_NOSIGNAL, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

            curl_multi_add_handle($queue, $ch);
            $map[(string) $ch] = $k;
        }

        $responses = array();
        do {
            while (($code = curl_multi_exec($queue, $active)) == CURLM_CALL_MULTI_PERFORM) ;

            if ($code != CURLM_OK) { break; }

            // a request was just completed -- find out which one
            while ($done = curl_multi_info_read($queue)) {

                // get the info and content returned on the request
                $info = curl_getinfo($done['handle']);
                $error = curl_error($done['handle']);
                $result = $callback(curl_multi_getcontent($done['handle']), $delay);
                $responses[$map[(string) $done['handle']]] = compact('info', 'error', 'result');

                // remove the curl handle that just completed
                curl_multi_remove_handle($queue, $done['handle']);
                curl_close($done['handle']);
            }

            // Block for data in / output; error handling is done by curl_multi_exec
            if ($active > 0) {
                curl_multi_select($queue, 0.5);
            }

        } while ($active);

        curl_multi_close($queue);
        return $responses;
    }
    /**
     * @return $this
     */
    public function push_event($flag,...$args){
        if($args[0]) $args[0]=sc_serialize($args[0],$flag);
        $this->op_datas[] = [
            'op'=>'event_sync',
            'args'=>$args
        ];
        return $this;
    }

    public function exec($ms_timeout=3000){
        $rr =  $this->rolling_curl($ms_timeout);
        $r = [];
        foreach ($this->op_datas as $k=>$v) {
            if($rr[$k]['result']) {
                $r[$k] = unserialize($rr[$k]['result']);
            } else {
                $r[$k] = null;
            }
        };
        return $r;
    }
}