<?php

class Notifications extends WhatupDb
{
    private $notificationTimes;

    public function __construct($dbh)
    {

        $this->dbh = $dbh;
        $this->notificationTimes = $this->getNotificationTimes();
        $this->processLastPings();
    }

    private function getNotificationTimes()
    {
        return array(
            0 => 10 * 60, //10 minutes,
            1 => 30 * 60, //30 minutes,
            2 => 2 * 60 * 60, //3 hours
            3 => 6 * 60 * 60, //6 hours
            4 => 24 * 60 * 60, //24 hours,
            5 => 2 * 24 * 60 * 60,// 2 days
            6 => 3 * 24 * 60 * 60, //3 days
            7 => 4 * 24 * 60 * 60, //4 days
            8 => 5 * 24 * 60 * 60, //5 days
            //after that... you just don't care anymore!
        );
    }

    private function shouldISendAMessage($messageCount, $timeDiff)
    {
        if (isset($this->notificationTimes[$messageCount])) {
            return (bool)($timeDiff > $this->notificationTimes[$messageCount]);
        } else {
            return false;
        }

    }

    private function processLastPings()
    {
        $lastPings = $this->getLastPings();
        foreach ($lastPings as $ping) {
            if (is_numeric($ping['note_id']) && $ping['test_value'] > 0) {
                $this->closeNotification($ping['note_id']);
            }
            if ($ping['test_value'] == 0) {
                $notification = $this->getNotification($ping['site_id'], $ping['note_id']);
                $lastGoodPing = $this->getLastGoodPing($notification['site_id']);
                $timeDown = strtotime($ping['ping_ts']) - strtotime($lastGoodPing);

                if ($this->shouldISendAMessage($notification['message_count'], $timeDown)) {
                    $this->sendEmailNotifications($notification['site_id'], $notification['address'], $timeDown);
                    $this->incrementNotifications($ping['note_id']);
                }
            }
        }

    }

    private function sendEmailNotifications($siteId, $address, $timeDown)
    {
        $subject = $address . " is down";
        $message = $address . " as been down for " . diffTimeToText($timeDown);
        $emails = $this->getSiteEmails($siteId);
        foreach ($emails as $email) {
            sendMail($email['email'], $subject, $message);
        }

    }
}