<?php
namespace sinri\SinriDatabaseAgent;

/**
 * SinriPDO
 * For CI Copyright 2016 Sinri Edogawa
 * Under MIT License
 **/
class SinriMySQLi extends SinriDatabaseAgent
{
    private $mysqli;
    private $charset;
    private $in_transaction;

    public function __construct($params)
    {
        parent::__construct($params);

        $host = $this->readArray($params, 'host', 'no.database');
        $port = $this->readArray($params, 'port', '3306');
        $username = $this->readArray($params, 'username', 'Jesus Loves You');
        $password = $this->readArray($params, 'password', 'God is Love.');
        $database = $this->readArray($params, 'database', 'test');
        $charset = $this->readArray($params, 'charset', 'utf8');

        $this->mysqli = new \mysqli($host, $username, $password, $database, $port);
        if ($this->mysqli->connect_errno) {
            throw new \Exception("SinriMySQLi Connect failed: " . $this->mysqli->connect_error);
        }
        // 设置数据库编码
        $this->charset = $charset;
        $this->mysqli->set_charset($this->charset);

        if (!empty($params['database']) && !$this->mysqli->select_db($params['database'])) {
            throw new \Exception("SinriMySQLi Initialize Scheme failed: " . $this->mysqli->error);
        }
        $this->in_transaction = false;
    }

    public function exportCSV($query, $csv_path, &$error, $charset = 'gbk')
    {
        $error = array();

        $csv_file = fopen($csv_path, 'w');

        $sqlIdx = 1;

        $multi_query_done = $this->mysqli->multi_query($query);
        if (!$multi_query_done) {
            return false;
        }
        $result = $this->mysqli->store_result();
        if (!($multi_query_done && $result)) {
            $error[$sqlIdx] = $this->mysqli->error;
            if (!empty($result)) {
                $result->free();
            }
            $this->mysqli->close();
            return false;
        }

        if ($row = $result->fetch_array(MYSQLI_ASSOC)) {
            fputcsv($csv_file, array_keys($row));
            do {
                array_walk($row, 'self::transCharset', array($this->charset, $charset));
                fputcsv($csv_file, array_values($row));
            } while ($row = $result->fetch_array(MYSQLI_ASSOC));
        }
        $result->free();
        $this->mysqli->close();
        return true;
    }

    public static function transCharset(&$item, $charsets)
    {
        $srcCharset = $charsets[0];
        $dstCharset = $charsets[1];
        $item = mb_convert_encoding($item, $dstCharset, $srcCharset);
    }

    public function executeMulti($query, $type, &$affected, &$error)
    {
        $affected = array();
        $error = array();

        // 开启一个事务
        // 保证中途任何语句发生错误都完全回滚
        // note: 如果表的engine是INNODB，无法回滚
        $this->mysqli->autocommit(false);

        $sqlIdx = 1;
        if ($this->mysqli->multi_query($query)) {
            do {
                $affected[] = $this->mysqli->affected_rows;
                if ($type == 0 && $this->mysqli->affected_rows <= 0) {
                    $error[$sqlIdx] = 'This statement has no effect';
                    $this->mysqli->rollback();
                    $this->mysqli->close();
                    return false;
                }
                $sqlIdx++;

                // $store_result=$this->mysqli->store_result();
                // if($store_result){
                //     $store_result->free();
                // }

                if ($type == 3) {
                    break;
                }
            } while ($this->mysqli->more_results() && $this->mysqli->next_result() && !$this->mysqli->errno);
        }

        if ($this->mysqli->errno) {
            $error[$sqlIdx] = $this->mysqli->error;
            $this->mysqli->rollback();
            $this->mysqli->close();
            return false;
        }

        $this->mysqli->commit();
        $this->mysqli->close();
        return true;
    }

    public function switchScheme($scheme)
    {
        return $this->mysqli->select_db($scheme);
    }

    public function getAll($sql)
    {
        $result = $this->mysqli->query($sql, MYSQLI_USE_RESULT);
        if (!$result) {
            return [];
        }
        $rows = [];
        do {
            $row = $result->fetch_array(MYSQLI_ASSOC);
            if ($row) {
                $rows[] = $row;
            }
        } while ($row);
        $result->free();
        return $rows;
    }

    public function getCol($sql)
    {
        $result = $this->mysqli->query($sql, MYSQLI_USE_RESULT);
        if (!$result) {
            return [];
        }
        $cols = [];
        do {
            $row = $result->fetch_row();
            if ($row && !empty($row)) {
                $cols[] = $row[0];
            }
        } while ($row);
        $result->free();
        return $cols;
    }

    public function getRow($sql)
    {
        $result = $this->mysqli->query($sql, MYSQLI_USE_RESULT);
        if (!$result) {
            return [];
        }
        $row = $result->fetch_array(MYSQLI_ASSOC);
        $result->free();
        return $row;
    }

    public function getOne($sql)
    {
        $result = $this->mysqli->query($sql, MYSQLI_USE_RESULT);
        if (!$result) {
            return [];
        }
        $row = $result->fetch_row();
        $result->free();
        if ($row) {
            return $row[0];
        } else {
            return false;
        }
    }

    public function exec($sql)
    {
        $result = $this->mysqli->query($sql);
        if ($result) {
            return $this->mysqli->affected_rows;
        } else {
            return $result;
        }
    }

    public function insert($sql)
    {
        $result = $this->mysqli->query($sql);
        if ($result) {
            return $this->mysqli->insert_id;
        } else {
            return $result;
        }
    }

    public function beginTransaction()
    {
        if ($this->in_transaction) {
            return false;
        }
        if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
            $this->in_transaction = $this->mysqli->begin_transaction();
        } else {
            $this->in_transaction = $this->mysqli->query("START TRANSACTION");
        }
        return $this->in_transaction;
    }

    public function commit()
    {
        if ($this->in_transaction) {
            if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
                $result = $this->mysqli->commit();
            } else {
                $result = $this->mysqli->query("COMMIT");
            }
            if ($result) {
                $this->in_transaction = false;
                return true;
            }
        }
        return false;
    }

    public function rollBack()
    {
        if ($this->in_transaction) {
            if (version_compare(PHP_VERSION, '5.5.0', '>=')) {
                $result = $this->mysqli->rollback();
            } else {
                $result = $this->mysqli->query("ROLLBACK");
            }
            if ($result) {
                $this->in_transaction = false;
                return true;
            }
        }
        return false;
    }

    public function inTransaction()
    {
        // MySQLi does not provided inTransaction() method.
        return $this->in_transaction;
    }

    public function errorCode()
    {
        return $this->mysqli->errno;
    }

    public function errorInfo()
    {
        return $this->mysqli->error;
    }
}
