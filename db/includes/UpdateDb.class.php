<?php

class UpdateDb
{
    private $dbh;

    public function __construct($dbh, $hulkSmash = false)
    {
        $this->dbh = $dbh;

        if ($hulkSmash) {
            $this->hulkSmash();
        }

        $this->processList($this->getFileList(), $this->getFilesRan());
    }

    private function hulkSmash()
    {
        $tables = $this->dbh->tableList();
        print "hulk smashing: \n";
        foreach ($tables as $table) {
            $sql = "drop table if exists " . $table;
            print "  " . $table . "\n";
            $this->dbh->exec($sql);
        }
        print "hulk done smashing\n\n";
    }

    private function processList($filesToRun, $filesRan)
    {

        foreach ($filesToRun as $file) {
            if (!in_array($file, $filesRan)) {
                $this->runSQLFile(DB_FILE_PATH . $file);
                $this->addFileToHeap($file);
            } else {
                print "skipping " . $file . "\n";
            }
        }
    }

    public function getFileList()
    {
        if (!file_exists(DB_FILE_PATH)) {
            print DB_FILE_PATH . "\nPath Not Found\n\n";
            die();
        }
        $list = array();
        foreach (scandir(DB_FILE_PATH) as $file) {
            if (substr($file, 0, 1) != "." && strtolower(substr($file, -4, 4)) == ".sql") {
                $list[] = $file;
            }
        }
        asort($list);
        return $list;
    }

    private function getFilesRan()
    {
        $this->primeTableCheck();
        $sql = "select file_name from db_schema_import order by file_name";
        $list = array();
        $files = $this->dbh->query($sql);

        foreach ($files as $file) {
            $list[] = $file["file_name"];
        }
        return $list;
    }

    private function primeTableCheck()
    {
        if (!$this->dbh->tableExists('db_schema_import')) {
            $this->createPrimeTable();
        }
    }

    private function addFileToHeap($file)
    {
        $fileName = basename($file);
        $sql = "insert into db_schema_import (file_name, import_date) values (?, NOW())";
        $params = array($fileName);

        $this->dbh->exec($sql, $params);

    }

    private function runSQLFile($file)
    {
        $cmd = MYSQL_BIN_PATH . " -h " . DBHOST . " -u" . DBUSERNAME . " -p" . DBPASSWORD . " " . DBNAME . " <" . $file;
        print "executing - " . basename($file) . "\n";
        //print $cmd . "\n\n\n";
        exec($cmd);
    }

    private function createPrimeTable()
    {
        print "creating table\n";
        $sql = "
            create table db_schema_import
            (
              import_id int AUTO_INCREMENT,
              file_name tinytext,
              import_date datetime,
              primary key(import_id)
            )
        ";
        $this->dbh->exec($sql);

    }
}