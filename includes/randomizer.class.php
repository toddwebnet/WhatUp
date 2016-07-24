<?php

class Randomizer extends WhatupDb
{
    public function __construct($dbh)
    {
        $this->dbh = $dbh;
        $startTime = strtotime('2016-01-01');
        $endTime = time();
        $numRuns = 50000;
        print "flushing existing data\n";
        $this->dbh->exec("delete from site_ping");
        $this->dbh->exec("delete from test_run");
        print "generating runs...\n";
        //$runs = $this->getDates($startTime, $endTime, $numRuns);
        $runs = $this->getDates2(date("Y-m-d H:i", $startTime), date("Y-m-d H:i",$endTime));

        $sites = $this->getSites();
        $x = 0;
        $y = count($runs);
        foreach($runs as $date=>$upDown)
        {
            $x++;
            print "run - " . $x . " / " . $y . "\n";
            $this->insertTestRun($upDown, $date);
            if($upDown == 1)
            {
                foreach($sites as $site)
                {
                    $pingTime = (rand(0,10) == 0)?0:rand(0,2000);
                    $this->insertPing($site['site_id'], $pingTime, $date);
                }
            }
        }
    }

    private function getDates2($startTime, $endTime)
    {
        $runningDate = $startTime;
        $return = array();
        while($runningDate < $endTime)
        {
            $return[$runningDate] = $this->getInternetUpDown();
            $runningDate = date("Y-m-d H:i", strtotime($runningDate . " + 5 minutes"));

        }
        return $return;

    }


    private function getDates($startTime, $endTime, $numRuns)
    {
        $return = array();
        $dates= [];
        for ($x = 0; $x < $numRuns; $x++) {
            $dates[] = date("Y-m-d H:i", $startTime + rand(0, $endTime - $startTime));
        }
        asort($dates);
        foreach($dates as $date)
        {
            $return[$date] = $this->getInternetUpDown();
        }
        return $return;

    }

    private function getInternetUpDown()
    {
        return (rand(0,8)%100 == 0)?0:1;
    }
    private function play()
    {
        $t1 = strtotime('2016-01-01');
        $t2 = time();
        $values = [];
        for ($x = 0; $x < 100; $x++) {
            $values[] = rand(0, $t2 - $t1);
        }

        asort($values);
        foreach (array_values($values) as $value) {
            $date = date("Y-m-d g:i a", $t1 + $value);
            print $date . "\n";
        }
    }
}