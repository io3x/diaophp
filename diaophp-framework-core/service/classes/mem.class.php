<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 *
 */
class mem {
    /**
     * @var swoole_table
     */
    public  $table;

    /**
     * @param int $n
     */
    public function __construct($n=1000){
        $table = new swoole_table($n);
        $table->column('key', swoole_table::TYPE_STRING, 64);
        $table->column('value', swoole_table::TYPE_STRING, 2048);
        $table->column('expire', swoole_table::TYPE_STRING, 32);
        $table->create();
        $this->table = $table;
    }

    /**
     * @param $key
     * @param $data
     * @return mixed
     */
    public function push($key,$data,$t=-1){
        if($t>0) {
            $this->delay($t,function() use($key) {
                $this->del($key);
            });
        }
        return $this->table->set($key,array(
            'key'=>$key,
            'value'=>$data,
            'expire'=>$t>0?date("Y-m-d H:i:s",time()+ceil($t/1000)):0
        ));
    }

    /**
     * @param $key
     * @return string
     */
    public function get($key){
        $value = $this->table->get($key);
        if($value) {
            return $value['value'];
        } else {
            return "";
        }
    }

    /**
     * @param $key
     * @return mixed
     */
    public function exist($key){
        return $this->table->exist($key);
    }

    /**
     * @param $key
     * @return mixed
     */
    public function del($key){
        return $this->table->del($key);
    }

    /**
     * @return int
     */
    public function counts(){
        return count($this->table);
    }

    /**
     * @return array
     */
    public function data(){
        $tmp = array();
        foreach($this->table as $row){
            $tmp[] = $row;
        }
        return $tmp;
    }

    /**
     *
     */
    public function setempty(){
        foreach($this->table as $row){
            $this->table->del($row['key']);
        }
    }

    /**
     * 延时执行方法
     * @param $n_time
     * @param $func
     */
    private function delay($ms,$func){
        swoole_timer_after($ms,$func);
    }
}