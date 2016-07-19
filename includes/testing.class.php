<?php

class Testing
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
        if ($this->checkIfLive()) {
            $this->processSites($this->getSites());
        }
    }

    private function processSites($sites)
    {
        print "Testing Sites:\n";
        foreach ($sites as $site) {
            print "  " . $site["site_name"] . " - ";
            $ts1 = microtime_float();
            $content = gimmie_curl($site['url']);
            $ts2 = microtime_float();
            $ts = (int)round(1000 * ($ts2 - $ts1), 0);
            $passFail = (strpos("pass", $content) === false) ? 0 : 1;
            print ($passFail == 1) ? "pass" : "fail";
            print " - " . $ts . "\n";
            $this->insertPing($site["site_id"], $passFail, $ts);
        }
    }

    private function insertPing($siteId, $passFail, $ts)
    {
        $sql = "INSERT INTO site_ping (site_id, pass_fail, time_to_load, ping_ts) VALUES (?,?,?,NOW())";
        $params = array($siteId, $passFail, $ts);
        $this->dbh->exec($sql, $params);
    }

    private function getSites()
    {
        $sql = "SELECT site_id, site_name, url FROM site WHERE active_ind = 1";
        $sites = $this->dbh->query($sql);
        return $sites;
    }

    private function checkIfLive()
    {
        $sites = $this->getLiveCheckSites();
        foreach ($sites as $site) {
            print $site['url'] . "\n\n";
            $content = gimmie_curl($site['url']);
            if (strpos(strtolower($content), "html") > 0) {
                return true;
            }
        }
        return false;
    }

    private function getLiveCheckSites()
    {
        $sql = "SELECT site_id, site_name, url FROM local_upcheck_site WHERE active_ind = 1";
        $sites = $this->dbh->query($sql);
        return $sites;
    }
}