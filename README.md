# Fast Mirror

A simple PHP script to find the fastest mirror based on ICMP ping.

## Usage
To echo the output

```
fast_mirrors('http://mirrors.ubuntu.com/mirrors.txt', true)

```

To return the output

```
fast_mirrors('http://mirrors.ubuntu.com/mirrors.txt', false)

```

Ping a single host with 5 seconds timeout value

```
php_ping('www.ubuntu.com', 5); //5 seconds timeout value

```

ping with ICMP class (requires root privilege)

```
$ping = new ICMPPingProcesser('http://www.google.com');
echo $ping->ping();

```