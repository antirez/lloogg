<html><body>
<h1>Hi! This is LLOOGG Redis server INFO output</h1>
As LLOOGG is now a project I and the <a href="http://3nt.it">other guy that created it with me</a> are running for free without any earning aim, we run the latest Redis unstable here, so that we get the latest benefits in terms of performances and memory usage, and we can use the service as a bedtest for Redis unstable releases.

This INFO output can be useful for people interested to see what happens to a Redis instance stressed with a big number of requests per second.
<pre style="font-weight:bold; color:#333; font-family:monospace; font-size:16px;">
<?
require("config.php");
require("redis.php");
require("dbapi.php");
$r = redisLink();
echo($r->info(true));
?>
</pre></body></html>
