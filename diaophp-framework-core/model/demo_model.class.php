<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 * 控制层使用说明
 *
class test {
   public $db_demo;
   public function __construct(){
     $this->db_demo = new demo_model();
   }
   public function init(){
     print_r($this->db_demo->data());
   }
}
 */
class demo_model extends common_model {

    public function demo_one(){
        return $this->fetch("select * from log limit 1;");
    }

    public function data(){
        return $this->fetch_all("select * from log limit 100");
    }

    /**
     *
     */
    public function demo_add(){
        return $this->insert("log",array(
            "action"=>"测试添加 demo_model",
            "inputtime"=>date("Y-m-d H:i:s")
        ));
    }

    /**
     * 综合操作方法,多个sql处理过程
     */
    public function demo_update(){
        $r = $this->fetch("select * from log order by logid desc limit 1;");
        return $this->update("log",array(
            "action"=>"测试修改 demo_model",
            "inputtime"=>date("Y-m-d H:i:s")
        )," logid =  ".$r['logid']);
    }

    public function demo_delete(){
        return $this->delete("log","id = 1");
    }



}