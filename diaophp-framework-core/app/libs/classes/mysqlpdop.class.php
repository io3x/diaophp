<?php
defined('IN_CDO') or exit('illegal infiltration.');
/**
 */
abstract class mysqlpdop extends mysqlpdo {
    /**
     * 重写构造函数，使用长连接模式
     * @param $db_host
     * @param $db_name
     * @param $db_username
     * @param $db_password
     * @param $db_characset
     */
    public function __construct($db_host,$db_name,$db_username,$db_password,$db_characset) {
        try {
            $this->pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_username, $db_password, array(
                PDO::ATTR_PERSISTENT => true,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . $db_characset . "';"
            ));
            $this->pdo->exec("SET CHARACTER SET " . $db_characset);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
            $this->pdo->query("set names " . $db_characset);
        } catch (PDOException $e) {
            echo "error " . $e->getMessage();
            $this->log("error " . $e->getMessage(),'');
        }
    }

    /**
     * 析构函数不进行任何操作 保留pdo对象
     */
    public function __destruct() {

    }
}