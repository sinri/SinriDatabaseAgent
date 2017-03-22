<?php
namespace sinri\SinriDatabaseAgent;

/**
 * Class SinriDatabaseAgent
 * @package sinri\SinriDatabaseAgent
 */
abstract class SinriDatabaseAgent
{

    /**
     * SinriDatabaseAgent constructor.
     * @param array $params Receive an array of parameters of certain keys.
     */
    public function __construct($params = array())
    {
        # code...
    }

    /**
     * @param mixed $string Field value or such array to be quoted.
     * @param mixed $parameter_type Extensible parameter type
     * @return array|mixed
     */
    public function quote($string, $parameter_type = 0)
    {
        if ($parameter_type) {
            // DO NOTHING
        }
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

    /**
     * @param $sql
     * @return mixed [[KEY=>VALUE,...],...]
     */
    abstract public function getAll($sql);

    /**
     * @param $sql
     * @return mixed [C0_OF_R0, C0_OF_R1, ...]
     */
    abstract public function getCol($sql);

    /**
     * @param $sql
     * @return mixed [C0_OF_R0, C1_OF_R0, ...]
     */
    abstract public function getRow($sql);

    /**
     * @param $sql
     * @return mixed C0_OF_R0
     */
    abstract public function getOne($sql);

    /**
     * @param $sql
     * @return mixed Affected rows when success, FALSE when error.
     */
    abstract public function exec($sql);

    /**
     * @param $sql
     * @return mixed Last inserted AI PK when success, FALSE when error.
     */
    abstract public function insert($sql);

    /**
     * @return boolean
     */
    abstract public function beginTransaction();

    // TRANSACTION RELATED

    /**
     * @return boolean
     */
    abstract public function commit();

    /**
     * @return boolean
     */
    abstract public function rollBack();

    /**
     * @return boolean
     */
    abstract public function inTransaction();

    /**
     * @return mixed MySQL Error Code
     */
    abstract public function errorCode();
    // ERROR DEFINITION

    /**
     * @return mixed MySQL Error Info
     */
    abstract public function errorInfo();

    /**
     * @param $sql
     * @param array $values
     * @throws \Exception
     * To be implemented in sub classes optionally
     */
    public function safeQueryAll($sql, $values = array())
    {
        throw new \Exception(__METHOD__." is not implemented yet");
    }

    /**
     * @param $sql
     * @param array $values
     * @throws \Exception
     * To be implemented in sub classes optionally
     */
    public function safeQueryRow($sql, $values = array())
    {
        throw new \Exception(__METHOD__." is not implemented yet");
    }

    /**
     * @param $sql
     * @param array $values
     * @throws \Exception
     * To be implemented in sub classes optionally
     */
    public function safeQueryOne($sql, $values = array())
    {
        throw new \Exception(__METHOD__." is not implemented yet");
    }

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
}
