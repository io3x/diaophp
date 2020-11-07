<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * 内存存储操作
 */
class schedule_mem {
    public  $table;
    public function __construct($table){
        $this->table = $table;
    }
    public function push($a){
        if(isset($a['sec'])) $a['sec'] = intval($a['sec']);
        $this->table->set($a['key'],$a);
        return 1;
    }

    public function incr($n){
        foreach($this->table as $row){
            $this->table->incr($row['key'],'sec',$n);
        }
    }
    public function decr($n){
        foreach($this->table as $row){
            $rn = intval($row['sec'])-$n;
            if($rn<0) {
                /*如果为负数,则剔除队列*/
                $this->table->del($row['key']);
                continue;
            }
            $this->table->decr($row['key'],'sec',$n);
        }
    }
    public function counts(){
        return count($this->table);
    }
    public function data(){
        $tmp = array();
        foreach($this->table as $row){
            $tmp[] = $row;
        }
        return $tmp;
    }
    public function setempty(){
        foreach($this->table as $row){
            $this->table->del($row['key']);
        }
    }





    /**
     * 单个更新
     * @param $a
     * @return array
     */
    public function update($a){
        foreach($this->table as $row){
            if($row['key']==$a['key']) {
                $this->table->del($row['key']);
                break;
            }
        }
        if(isset($a['sec'])) $a['sec'] = intval($a['sec']);
        $this->table->set($a['key'],$a);
        return 1;
    }

    /**
     * 单个删除
     */
    public function delete($key=''){
        foreach($this->table as $row){
            if($row['key']==$key) {
                $this->table->del($row['key']);
                return 1;
                break;
            }
        }
        return 0;
    }
}