<?php

class WhatupDb
{
    public $dbh;

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
        if ($datetime == null) {
            $datetime = date("Y-m-d G:i", time());
        }
        $sql = "insert into test_run (up_down, run_ts) values (?, ?)";
        $params = array($upDown, $datetime);
        $this->dbh->exec($sql, $params);
    }


    protected function insertPing($siteId, $passFail, $datetime = null)
    {
        if ($datetime == null) {
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
        foreach ($data as $item) {
            switch ($groupType) {
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

            if (!isset($r[$groupId])) {
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
        foreach ($data as $spec) {
            switch ($groupType) {
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
            if (!isset($r[$groupId])) {
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

    public function getLastPings()
    {
        $sql = "
        select
          sp.site_id, sp.ping_ts, sp.test_value,
          n.note_id, n.message_order, n.open_date
        from
        site_ping sp
        inner join (
            select sp.site_id, max(sp.ping_ts) ping_ts
            from site_ping sp
            inner join site s on s.site_id = sp.site_id
            where s.is_active = 1
            group by sp.site_id
        ) s2 on s2.site_id = sp.site_id and s2.ping_ts = sp.ping_ts
        left outer join notification n on n.site_id = sp.site_id and n.close_date is null
        ";
        return $this->dbh->query($sql);
    }

    public function getLastGoodPing($siteId)
    {
        $sql = "
        select max(ping_ts) as ping_ts
        from site_ping
        where site_id = ?
        and test_value > 0;
        ";
        $params = array($siteId);
        $data = $this->dbh->query($sql, $params);
        if (isset($data[0])) {
            return $data[0]['ping_ts'];
        } else {
            return date("Y-m-d H:i", strtotime("2000-01-01"));
        }
    }

    protected function closeNotification($noteId)
    {
        $now = date("Y-m-d H:i", time());
        $sql = "update notification set close_date = ? where note_id = ?";
        $params = array($now, $noteId);
        $this->dbh->exec($sql, $params);
    }
    protected function getNotification($siteId, $noteId)
    {
        if(!is_numeric($noteId))
        {
            $now = date("Y-m-d H:i", time());
            $sql = "insert into notification (site_id, message_order, open_date) values (?, 0, ?)";
            $params = array($siteId, $now);
            $this->dbh->exec($sql, $params);
            $sql = "select max(note_id) note_id from notificaiton where site_id = ?";
            $params = array($siteId);
            $data = $this->dbh->query($sql, $params);
            $noteId = $data[0]['note_id'];
        }
        $sql = "
        select
          n.note_id, n.site_id, s.address,
          n.message_order, n.open_date, n.close_date
        from notification n
        inner join site s on s.site_id = n.site_id
        where n.note_id = ?
        ";
        $params = array($noteId);
        $data = $this->dbh->query($sql, $params);
        return $data[0];
    }

    protected function getSiteEmails($siteId)
    {
        $sql = "select email from site_config where site_id = ?";
        $params = array($siteId);
        return $this->dbh->query($sql, $params);
    }

    protected function incrementNotifications($noteId)
    {
        $sql = "update notification set message_count = message_count + 1 where ?";
        $params = array($noteId);
        $this->dbh->exec($sql, $params);

    }
}