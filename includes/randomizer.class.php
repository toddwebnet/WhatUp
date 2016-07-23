<?php

class Randomizer extends WhatupDb
{
    public function __construct($dbh)
    {
        $this->dbh = $dbh;
        $startTime = strtotime('2016-01-01');
        $endTime = time();
        $numRuns = 50000;
        print "generating runs...\n";
        $runs = $this->getDates($startTime, $endTime, $numRuns);

        $sites = $this->getSites();
        $x = 0;
        foreach($runs as $date=>$upDown)
        {
            $x++;
            print "run - " . $x . "\n";
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


    private function getDates($startTime, $endTime, $numRuns)
    {
        $return = array();
        $dates= [];
        for ($x = 0; $x < $numRuns; $x++) {
            $dates[] = date("Y-m-d G:i", $startTime + rand(0, $endTime - $startTime));
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