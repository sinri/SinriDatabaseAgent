<?php
require_once(__DIR__.'/../autoload.php');

require_once(__DIR__.'/config.php');
require_once(__DIR__.'/TestBase.php');
require_once(__DIR__.'/TestPDO.php');
require_once(__DIR__.'/TestMySQLi.php');
use sinri\SinriDatabaseAgent\test\TestPDO;
use sinri\SinriDatabaseAgent\test\TestMySQLi;

date_default_timezone_set("Asia/Shanghai");

// PDO
echo "---- PDO ----".PHP_EOL;
$test_pdo=new TestPDO();
$test_pdo->generalTest();

// MySQLi
echo "---- MySQLi ----".PHP_EOL;
$test_mysqli=new TestMySQLi();
$test_mysqli->generalTest();
