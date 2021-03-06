<?php
/**
 * Fast Mirror - find the fastest mirror with ICMP ping.
 *
 * A simple script to ping a list of mirrors and return
 * the fastest.
 *
 * @author  M Yakub Mizan <mizan@shabdvisuals.com>
 * @link    http://github.com/mymizan
 */

require __DIR__ . '/icmp.php';

/**
 * Ping host using the Unix standard ping command
 *
 * @param  string  $host    hostname
 * @param  integer $timeout timeout in seconds
 * @return float            time in miliseconds
 */
function php_ping($host, $timeout) {

	try {

		$icmp = new ICMPPing($host, $timeout);
		$start_time = microtime(true);
		$respond = $icmp->sendPacket('Everything OK');
		$ping_time = microtime(true) - $start_time;

		if (@$icmp->analyzeRespond($respond) == 'Everything OK') {
			return round($ping_time * 1000, 2);
		} else {
			return 0;
		}

	} catch (Exception $e) {
		return 0;
	}
}

/**
 *  Check if the script is being run with root privilege
 */
function is_root_user() {
	return posix_getuid() == 0;
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
function fast_mirror($mirror, $show = true) {
	if (!is_root_user()) {
		die("You must have root privilege" . PHP_EOL);
	}
	$hosts = mirror_list($mirror); //http://mirrors.ubuntu.com/mirrors.txt
	$ping_time = array();

	if ($show) {
		echo "Total: " . count($hosts) . "\n";
		echo "Pinging...      ";
	}

	foreach ($hosts as $key => $host) {
		if ($show) {
			echo "\033[5D";
			echo str_pad($key + 1, 5, ' ', STR_PAD_RIGHT);
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