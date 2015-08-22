<?php

/**
 * Fast Mirror - find the fastest mirror using the ping command
 *
 * A simple script to ping a list of mirrors and return
 * the fastest.
 *
 * @author  M Yakub Mizan <mizan@shabdvisuals.com>
 * @link    http://github.com/mymizan
 */

/**
 * Ping host using the Unix standard ping command
 *
 * @param  string  $host    hostname
 * @param  integer $timeout timeout in seconds
 * @return float            time in miliseconds
 */
function php_ping($host, $timeout) {
	return shell_exec("ping -c1 -t{$timeout} {$host} | tail -1| awk '{print $4}' | cut -d '/' -f 2");
}

/**
 * Return a list of mirrors
 *
 * @param  string $mirror_url URL/file path containing url list
 * @return array              return list of mirrors
 */
function mirror_list($mirror_url) {
	$mirror_list = explode("\n", file_get_contents($mirror_url));
	$mirrors = array();
	if (count($mirror_list) > 0) {
		foreach ($mirror_list as $key => $mirror) {
			$m = parse_url($mirror);
			array_map('trim', $m); //trim whitespaces
			$mirrors[] = $m;
		}
	}
	return $mirrors;
}

/**
 * Return the fastest mirror
 * @param  string $mirror URL to a mirror list or file path
 * @return string         return the url of the fastest mirror
 */
function fast_mirrors($mirror, $show = true) {
	$hosts = mirror_list("http://mirrors.ubuntu.com/mirrors.txt");
	$ping_time = array();

	if ($show) {
		echo "Total hosts to ping: " . count($hosts) . "\n";
		echo "Pinging ... ";
	}

	foreach ($hosts as $key => $host) {
		if ($show) {
			echo ($key + 1) . ' |';
		}

		$time = trim(php_ping($host['host'], 4));

		if (intval($time) > 0) {
			$ping_time[$time] = $host;
		}

	}

	ksort($ping_time); //short the keys

	$fastest_mirror = array_keys($ping_time)[0];

	if ($show) {
		echo "\n";
		echo "Fastest Mirror({$fastest_mirror}ms): " .
		$ping_time[$fastest_mirror]['scheme'] .
		'://' . $ping_time[$fastest_mirror]['host'] .
		$ping_time[$fastest_mirror]['path'] . PHP_EOL;
	}
	return $ping_time[$fastest_mirror]['scheme'] . '://' . $ping_time[$fastest_mirror]['host'] . $ping_time[$fastest_mirror]['path'];
}