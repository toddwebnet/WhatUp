<?php

class Mysql
{
    private $dbh;

    public function __construct()
    {
        $this->connect();
    }

    private function isCLI()
    {
        return (php_sapi_name() === 'cli');
    }

    private function br()
    {
        if ($this->isCLI()) {
            return "\n";
        } else {
            return "<BR>";
        }

    }

    private function connect()
    {

        $dsn = 'mysql:host=' . DBHOST . ';dbname=' . DBNAME;
        $username = DBUSERNAME;
        $password = DBPASSWORD;
        $options = array(
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        );

        try {
            $this->dbh = new PDO($dsn, $username, $password, $options);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            echo $this->br();
            echo 'Connection failed: ' . $e->getMessage();
            echo $this->br();
            echo $this->br();
            die();
        }
    }

    public function tableList()
    {
        $sql = "show tables";
        $list = array();
        $statement = $this->dbh->prepare($sql);
        $statement->execute(array());
        $tableList = $statement->fetchAll();
       foreach($tableList as $table)
       {
           $list[] = $table[0];
       }

        return $list;

    }

    public function tableExists($table)
    {
        $sql = "SHOW TABLES LIKE ?;";
        $statement = $this->dbh->prepare($sql);
        $statement->execute(array($table));
        $tableList = $statement->fetchAll();
        if (count($tableList) > 0) {
            if ($tableList[0][0] == $table) {
                return true;
            }
        }
        return false;

    }

    public function exec($sql, $params = array())
    {
        try {
            $statement = $this->dbh->prepare($sql);
            $statement->execute($params);
        } catch (PDOException $e) {
            echo $this->br();
            echo 'Query Execution failed: ' . $e->getMessage();
            echo $this->br();
            echo $this->br();
            die();
        }
        return $statement;
    }

    public function query($sql, $params = array())
    {
        $stmt = $this->exec($sql, $params);
        try {
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo $this->br();
            echo 'FetchAll Failed: ' . $e->getMessage();
            echo $this->br();
            echo $this->br();
            die();
        }
    }
}