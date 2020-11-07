<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * 生成 dubbo url
 * @param $host
 * @param $port
 * @param $cp
 * @param $method
 * @param $timestamp13
 * @return string
 */
function dubbo_url($host, $port, $cp, $method, $timestamp13){
    $params = [
        'dubbo'=>cnf('SWOOLE_DUBBO_SERVICE','provider_dubbo_version'),
        'group'=>cnf('SWOOLE_DUBBO_SERVICE','provider_dubbo_group'),
        'version'=>"",
        'interface'=>$cp,
        'methods'=>$method,
        'timeout'=>0,
        'weight'=>1,
        'side'=>'provider',
        'application'=>cnf('SWOOLE_DUBBO_SERVICE','provider_name'),
        'anyhost'=>'true',
        'serialization'=>'hessian2',
        'timestamp'=>$timestamp13,
    ];
    return "dubbo://{$host}:{$port}/{$cp}?".http_build_query($params);
}

/**
 * dubbo 服务调用方法
 * @param $call_cp
 * @return \Dubbo\Common\Protocol\Dubbo\DubboRequest
 */
function call_dubbo_service($call_cp){
    static $registed_service_cps = [];
    if(empty($registed_service_cps)) {
        $registed_redis = new rds();
        $registed_redis->rds->select(0);
        $cps = $registed_redis->rds->keys("*");
        foreach ($cps as $cp) {
            foreach ($registed_redis->redis_smember($cp) as $dubbourl) {
                $arr_parse_url = parse_url($dubbourl);
                if($arr_parse_url['query']) {
                    parse_str($arr_parse_url['query'],$arr_parse_url['query']);
                    if($arr_parse_url['query']['methods']) {
                        $arr_methods = explode(",",$arr_parse_url['query']['methods']);
                        foreach ($arr_methods as $method) {
                            if($arr_parse_url['query']['interface']) $registed_service_cps[$arr_parse_url['query']['interface']][$method][] = urldecode($dubbourl);
                        }
                    }
                }
            }
        }
    }
    if($registed_service_cps[$call_cp]) {
        static $dubbo_requests;
        if(empty($dubbo_requests[$call_cp])) {
            $scps = [];
            foreach ($registed_service_cps[$call_cp] as $k=>$v) {
                foreach ($v as $url) {
                    if(!in_array($url,$scps)) $scps[] = $url;
                }
            }
            $dubbo_requests[$call_cp] = new Dubbo\Common\Protocol\Dubbo\DubboRequest($scps, [
                'retry'=>0,
                'timeout'=>3
            ]);
        }
        return $dubbo_requests[$call_cp];
    }
    return new Dubbo\Common\Protocol\Dubbo\DubboRequest(null,null);
}

/*
 *
 *  单机调用demo
 *
 * function call_dubbo_service_one($call_cp,$ip='127.0.0.1',$port='10389'){
    $providers = [
        'dubbo://192.168.0.101:1039/com.github.io3x.php.ip_service?dubbo=2.6.0&group=&version=&interface=com.github.io3x.php.ip_service&methods=swoole_get_local_ip&timeout=0&weight=1&side=provider&application=diaophp-provider-api&anyhost=true&serialization=hessian2&timestamp=16044551594163'
    ];
    $serviceConfig = [
        'retry'=>0,
        'timeout'=>3
    ];
    $request = new Dubbo\Common\Protocol\Dubbo\DubboRequest($providers, $serviceConfig);
    $r = $request->invoke('swoole_get_local_ip');
    return $r;

}
*/