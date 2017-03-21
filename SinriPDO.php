<?php
namespace sinri\SinriDatabaseAgent;

/**
 * SinriPDO
 * For CI Copyright 2016 Sinri Edogawa
 * Under MIT License
 **/
class SinriPDO extends SinriDatabaseAgent
{

    // protected $CI;

    private $pdo=null;

    // We'll use a constructor, as you can't directly call a function
    // from a property definition.
    public function __construct($params)
    {
        parent::__construct();

        // CodeIgniter ONLY: Assign the CodeIgniter super-object
        // $this->CI =& get_instance();

        $host=$this->readArray($params, 'host', 'no.database.desu');
        $port=$this->readArray($params, 'port', '3306');
        $username=$this->readArray($params, 'username', 'Jesus Loves You');
        $password=$this->readArray($params, 'password', 'God is Love.');
        $database=$this->readArray($params, 'database', 'test');
        $charset=$this->readArray($params, 'charset', 'utf8');

        try {
            $this->pdo = new \PDO(
                'mysql:host='.$host.';port='.$port.';dbname='.$database.';charset='.$charset,
                $username,
                $password,
                array(\PDO::ATTR_EMULATE_PREPARES => false)
            );
            $this->pdo->query("set names utf8");
            // var_dump($this->pdo->query("SELECT 1")->fetchAll(\PDO::FETCH_ASSOC));
        } catch (\PDOException $e) {
            throw new Exception("Connect Error: ".$e->getMessage(), 1);
        }
    }

    public function getAll($sql)
    {
        $stmt=$this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $rows;
    }

    public function getCol($sql)
    {
        $stmt=$this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        $rows=$stmt->fetchAll(\PDO::FETCH_BOTH);
        $col=array();
        if ($rows) {
            foreach ($rows as $row) {
                $col[]=$row[0];
            }
        }
        return $col;
    }

    public function getRow($sql)
    {
        $stmt=$this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);
        if ($rows) {
            return $rows[0];
        } else {
            return false;
        }
    }

    public function getOne($sql)
    {
        //FETCH_BOTH
        $stmt=$this->pdo->query($sql);
        $this->logSql($sql, $stmt);
        // $rows=$stmt->fetchAll(\PDO::FETCH_BOTH);//var_dump($rows);
        $rows=$stmt->fetchAll(\PDO::FETCH_ASSOC);//var_dump($rows);
        if ($rows) {
            $row = $rows[0];
            if ($row) {
                $row=array_values($row);
                return $row[0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function exec($sql)
    {
        $this->logSql($sql, true);
        $rows=$this->pdo->exec($sql);
        return $rows;
    }

    public function insert($sql)
    {
        $this->logSql($sql, true);
        $rows=$this->pdo->exec($sql);
        if ($rows) {
            return $this->pdo->lastInsertId();
        } else {
            return false;
        }
    }

    public function beginTransaction()
    {
        return $this->pdo->beginTransaction();
    }
    public function commit()
    {
        return $this->pdo->commit();
    }
    public function rollBack()
    {
        return $this->pdo->rollBack();
    }
    public function inTransaction()
    {
        return $this->pdo->inTransaction();
    }

    public function errorCode()
    {
        return $this->pdo->errorCode();
    }
    public function errorInfo()
    {
        return $this->pdo->errorInfo();
    }

    public function quote($string, $parameter_type = \PDO::PARAM_STR)
    {
        return $this->pdo->quote($string, $parameter_type);
    }

    // SPECAIL

    private function logSql($sql, $stmt)
    {
        if (!$stmt) {
            throw new \Exception("Failed to prepare SQL: ".$sql, 1);
        }
    }

    public function safeQueryAll($sql, $values = array())
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);
        $rows=$sth->fetchAll();
        return $rows;
    }
    public function safeQueryRow($sql, $values = array())
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);
        $row=$sth->fetch(\PDO::FETCH_ASSOC);
        return $row;
    }
    public function safeQueryOne($sql, $values = array())
    {
        $sth = $this->pdo->prepare($sql);
        $sth->execute($values);
        $col=$sth->fetchColumn(0);
        return $col;

        // 一个比较愚蠢的实现
        // $row=$sth->fetch(\PDO::FETCH_NUM);
        // if($row){
        //     return $row[0];
        // }else{
        //     return false;
        // }
    }
}
