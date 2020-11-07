<?php
defined('IN_CDO') or exit('illegal infiltration.');
class cost_time {
    private $point_time=0;
    private $r=[];
    public function __construct() {
        $this->point_time = microtime(true);
    }

    public function point_time($text=''){
        $point = microtime(true);
        $this->r[] = ($text?$text." ":"").(($point-$this->point_time)*1000)." ms";
        $this->point_time = $point;
    }

    public function result(){
        return $this->r;
    }
}