<?php
require __DIR__ . '/fast-mirror.php';

//ping a single host
php_ping('www.ubuntu.com', 5); //5 seconds timeout value

//ping a mirror list
fast_mirror('http://mirrors.ubuntu.com/mirrors.txt', true); //set false to return, true to echo output

//ping with ICMP class
$icmp = new ICMPPing('www.google.com', 4); //4=timeout
$response = $icmp->sendPacket('Everything OK');
echo $icmp->analyzeRespond($response);
