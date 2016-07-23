<?php


class Testing extends WhatupDb
{

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
        $checkIfLive = $this->checkIfLive();
        $this->insertTestRun($checkIfLive);
        if ($checkIfLive) {
            $this->processSites($this->getSites());
        }
    }

    private function processSites($sites)
    {
        print "Testing Sites:\n";
        foreach ($sites as $site) {
            print "  " . $site["address"] . " - ";
            $passFail=  $this->pingDomain( $site["address"]);
            print ($passFail >= 0) ? "pass" : "fail";
            print "\n";
            $this->insertPing($site["site_id"], $passFail);
        }
    }

    private function checkIfLive()
    {
        $sites = $this->getLiveCheckSites();
        foreach ($sites as $site) {
            print $site['address'] . "\n\n";
            $content = gimmie_curl($site['address']);
            if (strpos(strtolower($content), "html") > 0) {
                return 1;
            }
        }
        return 0;
    }


    private function pingDomain($domain)
    {
        $starttime = microtime(true);
        $errno = null;
        $errstr = null;
        $file = fsockopen($domain, 80, $errno, $errstr, 10);
        $stoptime = microtime(true);
        $status = 0;
        if (!$file) $status = -1;  // Site is down
        else {
            fclose($file);
            $status = ($stoptime - $starttime) * 1000;
            $status = floor($status);
        }
        return $status;
    }



}