<?php


class Testing
{
    private $dbh;

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

    private function insertPing($siteId, $passFail)
    {
        $sql = "INSERT INTO site_ping (site_id, test_value, ping_ts) VALUES (?,?,NOW())";
        $params = array($siteId, $passFail);
        $this->dbh->exec($sql, $params);
    }

    private function getSites()
    {
        $sql = "SELECT site_id, address FROM site WHERE check_type='p' and is_active = 1";
        $sites = $this->dbh->query($sql);
        return $sites;
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

    private function getLiveCheckSites()
    {
        $sql = "SELECT site_id, address FROM site where check_type = 'l' and is_active = 1";
        $sites = $this->dbh->query($sql);
        return $sites;
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

    private function insertTestRun($upDown)
    {
        $sql = "insert into test_run (up_down, run_ts) values (?, NOW())";
        $params = array($upDown);
        $this->dbh->exec($sql, $params);
    }

}