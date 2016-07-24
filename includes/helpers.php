<?php
ini_set('date.timezone', 'America/Chicago');

require_once __DIR__ . "/../third_party/PHPMailer/PHPMailerAutoload.php";

function sendMail($to, $subject, $message)
{
    $mail = new PHPMailer;
    $mail->isSMTP();
    //Enable SMTP debugging
    // 0 = off (for production use)
    // 1 = client messages
    // 2 = client and server messages
    $mail->SMTPDebug = 0;

    $mail->Debugoutput = 'text';
    $mail->Host = SMTP_SERVER;
    $mail->Port = SMTP_PORT;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->setFrom(SMTP_USERNAME);
    $mail->addAddress($to);
    $mail->Subject = $subject;

    $mail->msgHTML($message);
    if (!$mail->send()) {
        return false;
    } else {
        return true;
    }


}

function print_rr()
{
    foreach (func_get_args() as $arg) {
        print "<pre>--------------------------\n";
        print_r($arg);
        print "\n--------------------------\n</pre>";
    }
}

function var_dumpr()
{
    foreach (func_get_args() as $arg) {
        print "<pre>--------------------------\n";
        var_dump($arg);
        print "\n--------------------------\n</pre>";
    }
}

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

function gimmie_curl($url)
{ // create curl resource
    $ch = curl_init();

    // set url
    curl_setopt($ch, CURLOPT_URL, $url);

    //return the transfer as a string
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

    // $output contains the output string
    $output = curl_exec($ch);

    // close curl resource to free up system resources
    curl_close($ch);
    return $output;
}

function ping($host, $timeout = 1)
{
    /* ICMP ping packet with a pre-calculated checksum */
    $package = "\x08\x00\x7d\x4b\x00\x00\x00\x00PingHost";
    $socket = socket_create(AF_INET, SOCK_RAW, 1);
    socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array('sec' => $timeout, 'usec' => 0));
    socket_connect($socket, $host, null);

    $ts = microtime(true);
    socket_send($socket, $package, strLen($package), 0);
    if (socket_read($socket, 255))
        $result = microtime(true) - $ts;
    else    $result = false;
    socket_close($socket);

    return $result;
}

function diffTimeToText($diffTime)
{
    $hours = 0;
    $days = 0;

    $minutes = floor($diffTime / 60);
    if ($minutes > 60) {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        if ($hours > 24) {
            $days = floor($hours / 24);
            $hours = $hours % 24;
        }
    }
    $text = $minutes . " minutes";
    if ($hours > 0) {
        $text = $hours . " hours - " . $text;
    }
    if ($days > 0) {
        $text = $days . " days - " . $text;
    }
    return $text;

}

