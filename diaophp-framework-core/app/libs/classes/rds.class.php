<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 *
 */
class rds {
    /**
     * @var Redis
     */
    public $rds;
    /**
     *
     */
    function __construct(){
        $this->rds = new Redis();
        $this->rds->connect(cnf('REDIS_HOST'),'6379');
        $this->rds->auth(cnf('REDIS_AUTH'));
        $this->rds->select(2);
    }

    /**
     * @param $key
     * @return mixed 1 or 0
     */
    public function redis_exists($key) {
        $result = $this->rds->exists($key);
        return $result;
    }

    /**
     * @param $key
     * @return int|mixed
     */
    public function redis_get($key) {
        $result = $this->rds->get($key);
        if ($result) return json_decode($result,1);
        return 0;
    }

    /**
     * @param $key
     * @param $obj
     * @return mixed
     */
    public function redis_set($key, $obj) {
        return $this->rds->set($key,json_encode($obj));
    }

    /**
     * @param $key
     * @param $t
     * @param $obj
     * @return mixed
     */
    public function redis_setex($key,$t, $obj){
        return $this->rds->setex($key,$t,json_encode($obj));
    }

    /**
     * @param $key
     * @return mixed
     */
    public function del($key){
        return $this->rds->delete($key);
    }

    /**
     * 入列
     * @param $key
     * @return mixed
     */
    public function queue_push($key,$val){
        return $this->rds->rpush($key,$val);
    }

    /**
     * 出列
     * @param $key
     * @return mixed
     */
    public function queue_pop($key){
        return $this->rds->lpop($key);
    }

    public function queue_len($key){
        return $this->rds->llen($key);
    }

    public function redis_hget($key,$hkey){
        return $this->rds->hget($key,$hkey);
    }

    public function redis_hset($key,$hkey,$value){
        return $this->rds->hset($key,$hkey,$value);
    }

    public function redis_hincr($key,$hkey){
        return $this->rds->hIncrBy($key,$hkey,1);
    }
    
    public function redis_hgetall($key){
        return $this->rds->hGetAll($key);
    }

    public function redis_hscan($key){
    }
    
    public function redis_hlen($key){
        return $this->rds->hLen($key);
    }

    public function redis_hdel($key,$hkey){
        return $this->rds->hDel($key,$hkey);
    }

    public function redis_expire($key,$t){
        return $this->rds->expire($key,$t);
    }

    public function redis_expire_at($key,$t){
        return $this->rds->expireAt($key,$t);
    }

    public function redis_expire_after($key,$n=0){
        $endday = date("Y-m-d 23:59:59",strtotime("+$n day"));
        return $this->rds->expireAt($key,strtotime($endday));
    }

    public function redis_sadd($key,$value){
        return $this->rds->sAdd($key,$value);
    }

    public function redis_smember($key){
        return $this->rds->sMembers($key);
    }

    public function redis_sismember($key,$value){
        return $this->rds->sismember($key,$value);
    }

    public function redis_smember_rand($key,$n=1){
        return $this->rds->sRandMember($key,$n);
    }

    public function redis_srem($key,$value){
        return $this->rds->srem($key, $value);
    }

    /**
     * 添加一个有序集合
     * @param $key
     * @param $boj
     * @param $socre
     * @return mixed
     */
    public function redis_zadd($key,$value,$socre){
        return $this->rds->zAdd($key,$socre,$value);
    }

    /**
     * 查询score
     */
    public function redis_zscore($key,$value){
        return $this->rds->zScore($key,$value);
    }

    /**
     * 返回总个数
     */
    public function redis_zcount($key,$start='-inf',$end='+inf'){
        return $this->rds->zCount($key,$start,$end);
    }

    /**
     * 通过索引从小到大取数据
     */
    public function redis_zrange($key,$start=0,$end=-1){
        return $this->rds->zRange($key,$start,$end,false);
    }

    /**
     * 通过权重值score从小到大取数据
     */
    public function redis_zrange_score($key,$skip=1000,$start='-inf',$end='+inf'){
        return $this->rds->zRangeByScore($key,$start,$end,array(
            'withscores'=>true,
            'limit'=>array(0,$skip)
        ));
    }

    /**
     *
     * 通过权重值score从小到大取【第一条】数据
     */
    public function redis_zrange_fetch($key){
        $r = $this->rds->zRangeByScore($key,'-inf','+inf',array('withscores'=>false,'limit'=>array(0,1)));
        if($r[0]) return $r[0];
        return "";
    }
}