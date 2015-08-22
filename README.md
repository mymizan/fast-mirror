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
$icmp = new ICMPPing('www.google.com', 4); //timeout
$response = $icmp->sendPacket('Everything OK');
echo $icmp->analyzeRespond($response);

```