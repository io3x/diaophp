<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 *
 */
class category_model extends common_model {
    /**
     * @var array
     */
    public $categorys=array();

    /**
     *
     */
    public function __construct(){
        parent::__construct();
        $this->categorys = $this->categorys();

    }

    public function listree($pid=0){
        return $this->get_attr($this->categorys,$pid);
    }

    public function lists($pid=0){
        $data = $this->catlist($pid);
        return $data;
    }

    /** 查找父级菜单
     * @param int $catid
     * @return array
     */
    public function parents($catid=0){
        $parents = array();
        if($catid) {
            while($this->categorys[$catid]['parentid']>0) {
                $parents[] = $this->categorys[$catid]['parentid'];
                $catid=$this->categorys[$catid]['parentid'];
            }
            $parents[] = 0;
        }
        return $parents;
    }

    /**
     * @return array
     */
    private function categorys(){
        $data = $this->fetch_all("select catid,parentid,catname,description,listorder from category order by listorder desc,catid asc");
        $cats  = array();
        foreach($data as $k=>$v){
            $cats[$v['catid']] = $v;
        }
        return $cats;
    }

    private function catlist($pid=0,&$tmp=array()){
        foreach($this->categorys as $k=>$v){
            if($v['parentid']==$pid) {
                $tmp[] = $v;
                $this->catlist($v['catid'],$tmp);
            }
        }
        return $tmp;
    }

    private function get_attr($a,$pid=0){
        $tree = array();
        foreach($a as $v){
            if($v['parentid'] == $pid){
                $v['children'] = $this->get_attr($a,$v['catid']);
                if(empty($v['children'])){
                    unset($v['children']);
                }
                $tree[] = $v;
            }
        }
        return $tree;
    }

}