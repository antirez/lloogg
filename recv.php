<?
include("config.php");
include("localconfig.php");
include("secureid.php");
include("eventcodes.php");
include("pro.php");
include("redis.php");
include("dbapi.php");
include("g.php");
include("search.php");
include("url.php");

function recv() {
    global $_SERVER;
    $ip = $_SERVER['REMOTE_ADDR'];

    # get data
    $type=g('type','pageview');
    $uid=readSecureId(g('u',''),"userid");
    if ($uid == -1) return;
    $location=trim(g('l',''));
    if (strlen($location) == 0) return;
    $referer=trim(g('r',''));
    $width=gi('w',0);
    $height=gi('h',0);
    $historylen =gi('hl',0);
    $agent=g('a','');
    $cookies=strtolower(trim(g('c','na')));
    if ($cookies != 'na' && $cookies != 'y' && $cookies != 'n')
        $cookies = 'na';
    # split domain info
    $url=parseurl($location);
    if ($url === false) return;
    $time = time();
    $ispro = isPro($uid);
    $eventid = LOG_EVENT_PAGEVIEW;
    if ($type == 'adclick') $eventid = LOG_EVENT_ADCLICK;
    if (!$ispro && $eventid != LOG_EVENT_PAGEVIEW) return;

    # Log the visit. We assign a new event ID to every logged event.
    # Note that PHP running on 32 bit systems will overflow the ID soon
    # or later.
    $r = redisLink();
    $logid = $r->incr("global:nextLogId");
    $aux = Array(   "time" => $time,
                    "user_id" => $uid,
                    "event_id" => $eventid,
                    "location" => $location,
                    "proto" => $url['proto'],
                    "domain" => $url['domain'],
                    "path" => $url['path'],
                    "query" => $url['query'],
                    "ref" => $referer,
                    "swidth" => $width,
                    "sheight" => $height,
                    "cookies" => $cookies,
                    "ip" => $ip,
                    "agent" => $agent,
                    "historylen" => $historylen,
                    "id" => $logid);
    # Insert new
    $r->push("last:$uid",serialize($aux),false);

    # History length is different for PRO / non PRO user.
    $r->ltrim("last:$uid",0,$ispro ? 1000 : 50);

    if ($eventid != LOG_EVENT_PAGEVIEW) return;
    # Update stats
    $nv = gi('nv',0);
    $rv = gi('rv',0);
    $t = time();
    $t = $t-($t%(3600*24));

    # Update unique visits
    if ($nv) {
        $r->incr("day:uv:$uid:$t");
        if ($rv) {
            $r->incr("day:rv:$uid:$t");
        }
    }
    $r->incr("day:pv:$uid:$t");
}

function outputGif() {
    header("Content-Type: image/gif");
    include("images/1x1.gif");
}

$exclude = 0;
if (isset($_COOKIE['excludemyvisits'])) {
    $uid=readSecureId(g('u',''),"userid");
    $xid=readSecureId($_COOKIE['excludemyvisits'],"excludeid");
    if($uid==$xid) $exclude=1;
}
if (!$exclude) recv();
outputgif();
?>
