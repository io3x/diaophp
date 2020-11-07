<?php
defined('IN_CDO') or exit('illegal infiltration.');

/**
 * Class md_model
 */
class md_model {
    /**
     * @var
     */
    private $mdb;

    /**
     * md_model constructor.
     * @param $mdb
     */
    public function __construct($mdb) {
        $this->mdb = $mdb;
    }
}