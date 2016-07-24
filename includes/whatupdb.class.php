<?php

class WhatupDb
{
    protected $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
    }

    public function getSites()
    {
        $sql = "SELECT site_id, address FROM site WHERE check_type='p' and is_active = 1";
        $sites = $this->dbh->query($sql);
        return $sites;
    }

    protected function getLiveCheckSites()
    {
        $sql = "SELECT site_id, address FROM site where check_type = 'l' and is_active = 1";
        $sites = $this->dbh->query($sql);
        return $sites;
    }

    protected function insertTestRun($upDown, $datetime = null)
    {
        if ($datetime == null)
        {
            $datetime = date("Y-m-d G:i", time());
        }
        $sql = "insert into test_run (up_down, run_ts) values (?, ?)";
        $params = array($upDown, $datetime);
        $this->dbh->exec($sql, $params);
    }


    protected function insertPing($siteId, $passFail, $datetime = null)
    {
        if ($datetime == null)
        {
            $datetime = date("Y-m-d G:i", time());
        }
        $sql = "INSERT INTO site_ping (site_id, test_value, ping_ts) VALUES (?,?,?)";
        $params = array($siteId, $passFail, $datetime);
        $this->dbh->exec($sql, $params);
    }

    public function getSiteStats($siteName, $fromDate, $toDate, $groupType)
    {
        $sql = "
        select
          address as `site`,
          test_value as `value`,
          ping_ts as `date`
        from
          site s
          inner join site_ping p on s.site_id = p.site_id
        where s.is_active = 1
          and p.ping_ts between ? and ?
          and s.address = ?
        order by ping_ts";
        $params = array(
            date("Y-m-d H:i", strtotime($fromDate)),
            date("Y-m-d H:i", strtotime($toDate)),
            $siteName
        );

        $data = $this->dbh->query($sql, $params);
        $r = [];
        foreach ($data as $item)
        {
            switch ($groupType)
            {
                case "week":
                    $groupId = date("Y W", strtotime($item['date']));
                    break;
                case "hour":
                    $groupId = date("m-d H", strtotime($item['date']));
                    break;
                default:
                    $groupId = date("m", strtotime($item['date']));
                    break;
            }
            
            if (!isset($r[$groupId]))
            {
                $r[$groupId] = array(
                    'numRuns' => 0,
                    'aggrValues' => 0,
                    'upTicks' => 0,
                    'downTicks' => 0,
                    'percent' => 0,
                    'avgTime' => 0
                );
            }
            $r[$groupId]['numRuns']++;
            $r[$groupId]['upTicks'] += ($item['value'] == 0) ? 0 : 1;
            $r[$groupId]['downTicks'] += ($item['value'] == 0) ? 0 : 1;
            $r[$groupId]['aggrValues'] += $item['value'];
            $r[$groupId]['percent'] =
                round(100 * $r[$groupId]['upTicks'] /
                    $r[$groupId]['numRuns'], 4);
            $r[$groupId]['avgTime'] =
                round($r[$groupId]['aggrValues'] /
                    $r[$groupId]['numRuns'], 0);

        }
        return $r;
    }

    public function getUpTimeGroup($fromDate, $toDate, $groupType)
    {

        $sql = "select up_down, run_ts as `date` from test_run where run_ts between ? and ? order by run_ts";
        $params = array(date("Y-m-d G:i", strtotime($fromDate)), date("Y-m-d G:i", strtotime($toDate)));
        $data = $this->dbh->query($sql, $params);
        $r = [];
        foreach ($data as $spec)
        {
            switch ($groupType)
            {
                case "week":
                    $groupId = date("Y W", strtotime($spec['date']));
                    break;
                case "hour":
                    $groupId = date("m-d H", strtotime($spec['date']));
                    break;
                default:
                    $groupId = date("m", strtotime($spec['date']));
                    break;
            }
            if (!isset($r[$groupId]))
            {
                $r[$groupId] = array(
                    'numOutages' => 0,
                    'numRuns' => 0,
                    'numPasses' => 0,
                    'percent' => 100.00
                );
            }
            $r[$groupId]['numRuns']++;
            $r[$groupId] ['numOutages'] += 1 - $spec['up_down'];
            $r[$groupId] ['numPasses'] += $spec['up_down'];

            $r[$groupId]['percent'] = round(100 * $r[$groupId] ['numPasses'] / $r[$groupId] ['numRuns'], 4);

        }
        return $r;
    }

}