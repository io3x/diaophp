<?php
defined('IN_CDO') or exit('illegal infiltration.');
class mysqlpdo {

    public   $pdo;

    public function __construct($db_host,$db_name,$db_username,$db_password,$db_characset)
    {
        try
        {
            $this->pdo = new PDO("mysql:host={$db_host};dbname={$db_name}", $db_username, $db_password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES '" . $db_characset . "';"));
            $this->pdo->exec("SET CHARACTER SET " . $db_characset);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->query("set names " . $db_characset);
        } catch (PDOException $e) {
            echo "error " . $e->getMessage();
            $this->log("error " . $e->getMessage(),'');
        }
    }

    /**
     * begin a transaction.
     */
    public function begin_transaction()
    {
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 0);
        $this->pdo->beginTransaction();
    }

    /**
     * commit the transaction.
     */
    public function commit()
    {
        $this->pdo->commit();
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }

    /**
     * rollback the transaction.
     */
    public function rollback()
    {
        $this->pdo->rollBack();
        $this->pdo->setAttribute(PDO::ATTR_AUTOCOMMIT, 1);
    }

    /**
     * @param $sql
     * @return array
     */
    public function fetch_all($sql)
    {

        $this->log($sql);
        $sel = $this->pdo->query($sql);
        $sel->execute();
        $result = $sel->fetchAll(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * @param $sql
     * @return mixed
     */
    public function fetch($sql)
    {
        $this->log($sql);
        $sel = $this->pdo->query($sql);
        $sel->execute();
        $result = $sel->fetch(PDO::FETCH_ASSOC);
        return $result;
    }

    /**
     * @param $sql
     * @param null $data
     * @return array
     */
    public function bind_query( $sql,$data=null) {
        if ($data!==null) {
            $dat=array_values($data);
        }
        $this->log($sql);
        $sel = $this->pdo->prepare( $sql );
        if ($data!==null) {
            $sel->execute($dat);
        } else {
            $sel->execute();
        }
        $result = $sel->fetchAll( PDO::FETCH_ASSOC );
        return $result;
    }

    /**
     * check if there is exist data
     * @param  string $table table name
     * @param  array $dat array list of data to find
     * @return true or false
     */
    public function check_exist($table,$dat) {
        $data = array_values( $dat );
        //grab keys
        $cols=array_keys($dat);
        $col=implode(', ', $cols);

        foreach ($cols as $key) {
            $keys=$key."=?";
            $mark[]=$keys;
        }
        $jum=count($dat);
        if ($jum>1) {
            $im=implode(' and  ', $mark);
            $sel = $this->pdo->prepare("SELECT $col from $table WHERE $im");
        } else {
            $im=implode('', $mark);
            $sel = $this->pdo->prepare("SELECT $col from $table WHERE $im");
        }
        $sel->execute( $data );
        $sel->setFetchMode( PDO::FETCH_OBJ );
        $jum=$sel->rowCount();
        if ($jum>0) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * search data
     * @param  string $table table name
     * @param  array $col   column name
     * @param  array $where where condition
     * @return array recordset
     */
    public function search($table,$col,$where) {
        $data = array_values( $where );
        foreach ($data as $key) {
            $val = '%'.$key.'%';
            $value[]=$val;
        }
        //grab keys
        $cols=array_keys($where);
        $colum=implode(', ', $col);

        foreach ($cols as $key) {
            $keys=$key." LIKE ?";
            $mark[]=$keys;
        }
        $jum=count($where);
        if ($jum>1) {
            $im=implode(' OR  ', $mark);
            $sel = $this->pdo->prepare("SELECT $colum from $table WHERE $im");
        } else {
            $im=implode('', $mark);
            $sel = $this->pdo->prepare("SELECT $colum from $table WHERE $im");
        }

        $sel->execute($value);
        $sel->setFetchMode( PDO::FETCH_OBJ );
        return  $sel;
    }
    /**
     * 执行sql查询
     */
    public function query($sql){
        try {
            $this->log($sql);
            $stmt = $this->pdo->query($sql);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $rows;
        } catch (PDOException $e) {
            print "Error!: " . $e->getMessage() . "";
            $this->log("Error!: " . $e->getMessage() . "",'');
        }
    }
    /**
     * 执行多条sql集查询
     */
    public function multi_query($sql){
        try {
            $this->log($sql);
            $stmt = $this->pdo->query($sql);
            $data = array();
            do {
                $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
                if ($rows) {
                    $data[] = $rows;
                } else {
                    $data[] = array();
                }
            } while ($stmt->nextRowset());
            return $data;
        } catch (PDOException $e) {
            //print "Error!: " . $e->getMessage() . "";
            $this->log("Error!: " . $e->getMessage() . "",'');
            return $data;
        }
    }

    /**
     * @param $table
     * @param $dat
     * @return string
     */
    public function insert($table,$dat) {
        try {
            if( $dat !== null )
                $data = array_values( $dat );
            //grab keys
            $cols=array_keys($dat);
            $col=implode(', ', $cols);

            //grab values and change it value
            $mark=array();
            foreach ($data as $key) {
                $keys='?';
                $mark[]=$keys;
            }
            $im=implode(', ', $mark);
            $sql = "INSERT INTO $table ($col) values ($im)";
            $this->log($sql,$data);
            $ins = $this->pdo->prepare($sql);
            $ins->execute( $data );
            return $this->pdo->lastInsertId();
        } catch (PDOException $e) {
            return 0;
        }

    }

    /**
     * 非pdo绑定方式批量写入
     * @param $table
     * @param $data
     * @return int
     */
    public function batch_insert($table,$data){
        try {
            $fieids = array();
            $vls = array();
            $m=0;
            foreach($data as $v){
                ++$m;
                $tmp = array();
                foreach($v as $x=>$y){
                    if($m==1) $fieids[] = $x;
                    $tmp[] = $y;
                }
                $vls[] = "'".implode("','",$tmp)."'";
            }
            $fieids = implode(',',$fieids);
            $vls = "(".implode("),(",$vls).")";
            if(count($vls)>1) {
                $sql = "insert into $table ($fieids) values $vls";
            } else {
                $sql = "insert into $table ($fieids) value $vls";
            }
            $this->log($sql,$data);
            $stmt = $this->pdo->query($sql);
            return $stmt->rowCount();
        } catch (PDOException $e) {
            return 0;
        }

    }

    /**
     * @param $table
     * @param $data
     * @param string $where
     * @return int
     */
    public function update($table,$data,$where='') {
        try {
            $sql = "UPDATE $table SET ";
            foreach($data as $k=>$v)
            {
                $sql .= '`'.$k.'`=:'.$k.',';
                $binds[':'.$k]=$v;
            }
            if($where) $where = ' WHERE '.$where;
            $sql = rtrim($sql,',').$where;
            $this->log($sql,$data);
            $ins = $this->pdo->prepare($sql);
            $ins->execute($binds);
            return $ins->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     * @param $table
     * @param $where
     * @return int
     */
    public function delete($table,$where) {
        try {
            $sql = "Delete from $table where $where";
            $this->log($sql,'');
            $sel = $this->pdo->prepare($sql);
            $sel->execute();
            return $sel->rowCount();
        } catch (PDOException $e) {
            return 0;
        }
    }

    /**
     *
     */
    public function __destruct() {
        $this->pdo = null;
    }

    /**
     * @param $sql
     * @param array $execute
     */
    public  function log($sql,$execute=array()){
        return '';
        error_log("<?php #".date("Y-m-d H:i:s",time())."\t"."SQL语句:".str_replace(array("\r","\n","\t"),"",$sql)."执行参数:".str_replace(array("\r","\n"),"",var_export($execute,true))." ?> \r\n",3,CACHE_PATH."logs/".date("Ymd").".pdosql.log.php");
    }
}