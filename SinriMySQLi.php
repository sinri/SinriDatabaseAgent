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
    
    public function __construct($params, &$error = null)
    {
        $error='';
        try {
            $this->mysqli=new \mysqli(
                $params['host'],
                $params['username'],
                $params['password'],
                $params['database'],
                $params['port']
            );
            if ($this->mysqli->connect_errno) {
                throw new \Exception("SinriMySQLi Connect failed: ".$this->mysqli->connect_error);
            }
            // 设置数据库编码
            if (!isset($params['charset'])) {
                $this->charset='utf8';
            } else {
                $this->charset=$params['charset'];
            }
            $this->mysqli->set_charset($this->charset);

            if (!empty($params['database']) && !$this->mysqli->select_db($params['database'])) {
                throw new \Exception("SinriMySQLi Connect failed: ".$this->mysqli->error);
            }
        } catch (\Exception $e) {
            $error=$e->getMessage();
        }
    }

    public function exportCSV($query, $csvpath, &$error, $charset = 'gbk')
    {
        $error = array();

        $csvfile = fopen($csvpath, 'w');
        // $mysqli = $this->_connect($db);

        $sqlIdx = 1;

        if (!($this->mysqli->multi_query($query)
            && ($result = $this->mysqli->store_result()))) {
            $error[$sqlIdx] = $this->mysqli->error;
            if (!empty($result)) {
                $result->free();
            }
            $this->mysqli->close();
            return false;
        }
        
        if ($row = $result->fetch_array(MYSQL_ASSOC)) {
            fputcsv($csvfile, array_keys($row));
            do {
                array_walk($row, 'self::transCharset', array($this->charset, $charset));
                fputcsv($csvfile, array_values($row));
            } while ($row = $result->fetch_array(MYSQL_ASSOC));
        }
        $result->free();
        $this->mysqli->close();
        return true;
    }

    private static function transCharset(&$item, $key, $charsets)
    {
        $srcCharset = $charsets[0];
        $dstCharset = $charsets[1];
        $item = mb_convert_encoding($item, $dstCharset, $srcCharset);
    }

    public function executeMulti($query, $type, &$affected, &$error)
    {
        $affected = array();
        $error = array();
    
        // $mysqli = $this->_connect($db);

        // 开启一个事务
        // 保证中途任何语句发生错误都完全回滚
        // note: 如果表的engine是INNODB，无法回滚
        $this->mysqli->autocommit(false);

        $sqlIdx = 1;
        if ($this->mysqli->multi_query($query)) {
            do {
                $affected[] = $this->mysqli->affected_rows;
                if ($type==0 && $this->mysqli->affected_rows <= 0) {
                    $error[$sqlIdx] = 'This statment has no effect';
                    $this->mysqli->rollback();
                    $this->mysqli->close();
                    return false;
                }
                $sqlIdx++;

                // $store_result=$this->mysqli->store_result();
                // if($store_result){
                //     $store_result->free();
                // }

                if ($type==3) {
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
}
