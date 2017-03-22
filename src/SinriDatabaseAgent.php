<?php
namespace sinri\SinriDatabaseAgent;

/**
*
*/
abstract class SinriDatabaseAgent
{
    
    public function __construct($params = array())
    {
        # code...
    }

    // shared methods

    protected function readArray($array, $key, $default = null)
    {
        if (!is_array($array)) {
            return $default;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }
        return $default;
    }

    public function quote($string, $parameter_type)
    {
        if (is_array($string)) {
            return array_map(__METHOD__, $string);
        }
        if (!empty($string) && is_string($string)) {
            return str_replace(
                array('\\', "\0", "\n", "\r", "'", '"', "\x1a"),
                array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'),
                $string
            );
        }
        return $string;
    }

    // abstract methods
    // SELECT RELATED
    abstract public function getAll($sql);
    abstract public function getCol($sql);
    abstract public function getRow($sql);
    abstract public function getOne($sql);
    // EXECUTE RELATE
    abstract public function exec($sql);
    abstract public function insert($sql);
    // TRANSACTION RELATED
    abstract public function beginTransaction();
    abstract public function commit();
    abstract public function rollBack();
    abstract public function inTransaction();
    // ERROR DEFINITION
    abstract public function errorCode();
    abstract public function errorInfo();

    // Optional
    public function safeQueryAll($sql, $values = array()){
        throw new \Exception(__METHOD__." is not implemented yet");
    }
    public function safeQueryRow($sql, $values = array()){
        throw new \Exception(__METHOD__." is not implemented yet");
    }
    public function safeQueryOne($sql, $values = array()){
        throw new \Exception(__METHOD__." is not implemented yet");
    }

}
