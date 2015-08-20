<?php

/**
 * A simple script to "ping" a list of mirrors and return
 * the fastest. You can optionally update your
 * apt sources.list with the mirror
 */
function php_ping($host, $port, $timeout) {
	$shell_output = shell_exec("ping -c1 -t{$timeout} {$host} | tail -1| awk '{print $4}' | cut -d '/' -f 2");
	return $shell_output;
}

//http://mirrors.ubuntu.com/mirrors.txt
function mirror_list($mirror_url) {
	$mirror_list = explode("\n", file_get_contents($mirror_url));
	$mirrors = array();
	if (count($mirror_list) > 0) {
		foreach ($mirror_list as $key => $mirror) {
			$mirrors[] = trim(parse_url($mirror)['host']);
		}
	}
	return $mirrors;
}

$hosts = mirror_list("http://mirrors.ubuntu.com/mirrors.txt");
$ping_time = array();

echo "Total Hosts: " . count($hosts) . "\nPinging: ";
foreach ($hosts as $key => $host) {
	echo $key . ' |';
	$time = trim(php_ping($host, '', 4));

	if (intval($time) > 0) {
		$ping_time[$time] = $host;
	}

	if ($key > 0) {
		break;
	}
}

echo "\n";

ksort($ping_time);
$fastest_mirror = array_keys($ping_time)[0];
echo "The Fastest Mirror: " . $ping_time[$fastest_mirror] . "({$fastest_mirror}ms)" . PHP_EOL;