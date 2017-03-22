<?php
namespace sinri\SinriDatabaseAgent\test;

use sinri\SinriDatabaseAgent\SinriDatabaseAgent;

abstract class TestBase
{
    protected $config;
    protected $db;

    protected $foreground_colors;
    protected $background_colors;

    public function __construct()
    {
        $this->config=new TestDatabaseConfig();
        $this->connectDatabase();

        if (!is_subclass_of($this->db, 'sinri\SinriDatabaseAgent\SinriDatabaseAgent')) {
            $this->logError("DB Property is not instance of SinriDatabaseAgent but of ".get_class($this->db));
        }

        $this->prepareCLIColors();
    }

    abstract protected function connectDatabase();

    final protected function logInfo($message, $sql = "")
    {
        $this->log("INFO", $message, $sql);
    }
    final protected function logError($message, $sql = "")
    {
        $this->log("ERROR", $message, $sql);
    }
    final protected function log($level, $message, $sql = "")
    {
        $now=date('Y-m-d H:i:s');
        if ($level=='ERROR') {
            $level=$this->coloredCLIText($level, 'red');
        }
        echo "{$now} [{$level}] {$message}".PHP_EOL;
        if (!empty($sql)) {
            echo $this->coloredCLIText($sql, 'blue');
            echo PHP_EOL;
        }
    }
    final protected function coloredCLIText($text, $fore_color = 'auto', $back_color = 'auto')
    {

        if ($fore_color!='auto') {
            if (!isset($foreground_colors[$fore_color])) {
                $fore_color='white';
            }
            // then use fore set

            $colored_string = "\033[";
            $colored_string .= $this->foreground_colors[$fore_color];
            $colored_string .=  "m " . $text . " \033[0;39m";
        } else {
            if (!isset($background_colors[$back_color])) {
                $back_color='black';
            }
            // then use back only
            $colored_string = "\033[";
            $colored_string .= $this->background_colors[$back_color];
            $colored_string .=  "m " . $text . " \033[0;39m";
        }

//        $colored_string = "\033[";
//        $colored_string .= $this->background_colors[$back_color];
//        $colored_string .= ";";
//        $colored_string .= $this->foreground_colors[$fore_color];
//        $colored_string .=  "m " . $text . " \033[0;39m";

        return $colored_string;
    }
    final protected function prepareCLIColors()
    {
        // Set up shell colors
        $this->foreground_colors['black'] = '0;30';
        $this->foreground_colors['dark_gray'] = '1;30';
        $this->foreground_colors['blue'] = '0;34';
        $this->foreground_colors['light_blue'] = '1;34';
        $this->foreground_colors['green'] = '0;32';
        $this->foreground_colors['light_green'] = '1;32';
        $this->foreground_colors['cyan'] = '0;36';
        $this->foreground_colors['light_cyan'] = '1;36';
        $this->foreground_colors['red'] = '0;31';
        $this->foreground_colors['light_red'] = '1;31';
        $this->foreground_colors['purple'] = '0;35';
        $this->foreground_colors['light_purple'] = '1;35';
        $this->foreground_colors['brown'] = '0;33';
        $this->foreground_colors['yellow'] = '1;33';
        $this->foreground_colors['light_gray'] = '0;37';
        $this->foreground_colors['white'] = '1;37';

        $this->background_colors['black'] = '40';
        $this->background_colors['red'] = '41';
        $this->background_colors['green'] = '42';
        $this->background_colors['yellow'] = '43';
        $this->background_colors['blue'] = '44';
        $this->background_colors['magenta'] = '45';
        $this->background_colors['cyan'] = '46';
        $this->background_colors['light_gray'] = '47';
    }

    public function generalTest()
    {
        $table_name='sda_test';
        // 1. drop table
        $sql="DROP TABLE IF EXISTS `{$table_name}`;";
        $this->logInfo("1. drop table `{$table_name}`", $sql);
        $result=$this->db->exec($sql);
        if ($result===false) {
            $this->logError("EXEC FAILED. Line ".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        } else {
            $this->logInfo("EXEC Done. {$result} row(s) affected.");
        }
        // 2. create table
        $sql="CREATE TABLE `{$table_name}` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
            `name` varchar(64) NOT NULL,
            `size` int(11) NOT NULL,
            `create_date` datetime DEFAULT NULL,
            `update_date` datetime DEFAULT NULL,
            PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
        ";
        $this->logInfo("2. create table `{$table_name}`", $sql);
        $result=$this->db->exec($sql);
        if ($result===false) {
            $this->logError("EXEC FAILED. Line ".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        } else {
            $this->logInfo("EXEC Done. {$result} row(s) affected.");
        }
        // 3.1 insert
        $sql="INSERT INTO `{$table_name}` (
            `id`,`name`,`size`,`create_date`,`update_date`
        ) VALUES (
            NULL,'Ein','1',now(),now()
        )";
        $this->logInfo("3.1 insert into `{$table_name}`", $sql);
        $new_id=$this->db->insert($sql);
        if ($new_id) {
            $this->logInfo("INSERTED as ".$new_id);
        } else {
            $this->logError("INSERT FAILED. Line".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        }
        // 3.2 insert
        $sql="INSERT INTO `{$table_name}` (
            `id`,`name`,`size`,`create_date`,`update_date`
        ) VALUES (
            NULL,'Zwie','2',now(),now()
        )";
        $this->logInfo("3.2 insert into `{$table_name}`", $sql);
        $new_id=$this->db->insert($sql);
        if ($new_id) {
            $this->logInfo("INSERTED as ".$new_id);
        } else {
            $this->logError("INSERT FAILED. Line".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        }
        // 4. select all
        $sql="SELECT * FROM `{$table_name}`";
        $this->logInfo("4. select * from `{$table_name}`", $sql);
        $rows=$this->db->getAll($sql);
        if ($rows && count($rows)===2) {
            $this->logInfo("SELECT ROW: ".json_encode($rows, JSON_UNESCAPED_UNICODE));
        } else {
            $this->logError("GOT ".json_encode($rows, JSON_UNESCAPED_UNICODE));
            $this->logError("SELECT ALL FAILED. Line ".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        }
        // 5. update
        $sql="UPDATE `{$table_name}` SET size=10, update_date=now() WHERE name='Ein';";
        $this->logInfo("5. update `{$table_name}`", $sql);
        $result=$this->db->exec($sql);
        if ($result===false || $result!=1) {
            $this->logError("EXEC FAILED. Line ".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        } else {
            $this->logInfo("EXEC Done. {$result} row(s) affected.");
        }
        // 6. select row
        $sql="SELECT `id`,`name`,`size` FROM `{$table_name}` WHERE size=10";
        $this->logInfo("6. select row `{$table_name}`", $sql);
        $row=$this->db->getRow($sql);
        if ($rows && count($row)===3) {
            $this->logInfo("SELECT ROW: ".json_encode($row, JSON_UNESCAPED_UNICODE));
        } else {
            $this->logError("SELECT ROW FAILED. Line ".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        }
        // 7. delete row
        $sql="DELETE FROM `{$table_name}` WHERE `name`='Zwie'";
        $this->logInfo("7. delete row `{$table_name}`", $sql);
        $result=$this->db->exec($sql);
        if ($result===false || $result!=1) {
            $this->logError("EXEC FAILED. Line ".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        } else {
            $this->logInfo("EXEC Done. {$result} row(s) affected.");
        }
        // 8. select one
        $sql="SELECT `size` FROM `{$table_name}` WHERE name='Ein'";
        $this->logInfo("8. select one `{$table_name}`", $sql);
        $result=$this->db->getOne($sql);
        if ($result==10) {
            $this->logInfo("SELECT ROW: ".json_encode($row, JSON_UNESCAPED_UNICODE));
        } else {
            $this->logError("SELECT ROW FAILED. Line ".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        }
        // 9.1 transactions rollback
        $this->logInfo("9.1 transactions rollback `{$table_name}`");
        $this->db->beginTransaction();
        $sql="DELETE FROM `{$table_name}` WHERE `name`='Ein'";
        $result=$this->db->exec($sql);
        $inTransaction=$this->db->inTransaction();
        if (!$inTransaction) {
            $this->logError("inTransaction status error");
        }
        $this->logInfo("Deleted Ein with affected rows: ".$result, $sql);
        $rollback_done=$this->db->rollBack();
        if (!$rollback_done) {
            $this->logError("Rollback Error as returned false");
        }
        $sql="SELECT count(*) FROM `{$table_name}` WHERE `name`='Ein'";
        $count=$this->db->getOne($sql);
        if ($count==1) {
            $this->logInfo("After rollback Ein still exists", $sql);
        } else {
            $this->logError("Rollback failed Ein not exists. Line ".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        }
        // 9.2 transactions commit
        $this->logInfo("9.2 transactions commit  `{$table_name}`");
        $this->db->beginTransaction();
        $sql="DELETE FROM `{$table_name}` WHERE `name`='Ein'";
        $result=$this->db->exec($sql);
        $this->logInfo("Deleted Ein with affected rows: ".$result, $sql);
        $inTransaction=$this->db->inTransaction();
        if (!$inTransaction) {
            $this->logError("inTransaction status error");
        }
        $commit_done=$this->db->commit();
        if (!$commit_done) {
            $this->logError("Commit Error as return false");
        }
        $sql="SELECT count(*) FROM `{$table_name}` WHERE `name`='Ein'";
        $count=$this->db->getOne($sql);
        if ($count==0) {
            $this->logInfo("After commit Ein disappeared", $sql);
        } else {
            $this->logError("GOT ".json_encode($count, JSON_UNESCAPED_UNICODE));
            $this->logError("Commit failed Ein still exists. Line ".__LINE__);
            $this->logError("ERROR CODE = ".json_encode($this->db->errorCode(), JSON_UNESCAPED_UNICODE));
            $this->logError("ERROR INFO: ".json_encode($this->db->errorInfo(), JSON_UNESCAPED_UNICODE));
            return false;
        }

        // FIN
        $this->logInfo("All General Test Cases Passed");
        return true;
    }
}
