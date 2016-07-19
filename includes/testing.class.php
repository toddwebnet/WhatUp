<?php

class Testing
{
    private $dbh;

    public function __construct($dbh)
    {
        $this->dbh = $dbh;
        $this->processSites($this->getSites());
    }

    private function processSites($sites)
    {
        print "Testing Sites:\n";
        foreach($sites as $site)
        {
            print "  " . $site["site_name"] . " \n";
            $ts1 =microtime_float();
            $content =  gimmie_curl($site['url']);
            $ts2 = microtime_float();
            $ts =(int)round( 1000* ($ts2-$ts1), 0);
            var_dumpr($ts1,$ts2,$ts);
            $passFail = (strpos("pass", $content) === false)?0:1;
            $this->insertPing($site["site_id"], $passFail, $ts);
        }
    }

    private function insertPing($siteId, $passFail, $ts)
    {
        $sql = "insert into site_ping (site_id, pass_fail, time_to_load, ping_ts) values (?,?,?,NOW())";
        $params = array($siteId, $passFail, $ts);
        $this->dbh->exec($sql, $params);
    }

    private function getSites()
    {
        $sql = "select site_id, site_name, url from site where active_ind = 1";
        $sites = $this->dbh->query($sql);
        return $sites;
    }
}