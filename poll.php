<?
require("lib.php");
require("json.php");
require("geoip.inc");

if (!isLoggedIn()) {
    echo("NOAUTH");
    exit;
}

function poll() {
    $numres = 1;
    $r = redisLink();
    $uid = reqUserId();
    $minid = gi("minid",0);
    $proto = gi("proto",1);
    $loadhistory = 0;
    if ($minid == 0) {
        # First client request. Just return the latest entry
        $latest = unserialize($r->lindex("last:$uid",0));
        $minid = $latest['id'];
    } else if ($minid < 0) {
        # minid < 0 means: load history, with abs(minid) elements
        $numres = abs($minid);
        if ($numres > 1000) $numres = 1000;
        $loadhistory = 1;
    }
    $rows = $r->lrange("last:$uid",0,$numres-1);

    # Handle free accounts timeouts.
    # NOTE: this is actually disabled, left here for historical reasons
    # and because the javascript still handles it.
    $ispro = isPro($uid);
    if (0 && !$ispro) {
        $startpoll = $r->get("startpoll:$uid");
        if (!$startpoll) {
            $r->set("startpoll:$uid",time());
        } else {
            $delta = time()-$r->get("startpoll:$uid");
            if ($delta > FREE_MAXTIME+FREE_WAIT) {
                $r->delete("startpoll:$uid");
            } else if ($delta > FREE_MAXTIME) {
                echo("TRYLATER:".floor(1+((FREE_MAXTIME+FREE_WAIT-$delta)/60)));
                exit;
            }
        }
    }

    # Empty list?
    if (count($rows) == 0) {
        echo("NODATA");
        exit;
    }

    # Check if even the most recent element (the first one)
    # is still too old. If so, no new data to return.
    $latest = unserialize($rows[0]);
    if ($latest['id'] < $minid) {
        echo("NODATA");
        exit;
    }

    # Try to get all the data required. Up to 50 elements for request
    while(!$loadhistory) {
        $oldest = unserialize($rows[count($rows)-1]);
        if ($oldest['id'] > $minid && $numres < 50) {
            # We need more data
            $numres = ($numres+1)*2;
            if ($numres > 50) $numres=50;
            $rows = $r->lrange("last:$uid",0,$numres-1);
        } else {
            break;
        }
    }

    # Ok now reverse the array to sort ascending and return data to ajax.
    $rows = array_reverse($rows);
    $gi = geoip_open("geoipdata/GeoIP.dat",GEOIP_STANDARD);
    foreach($rows as $srow) {
        $row = unserialize($srow);
        if ($row['id'] < $minid) continue;
        $keys = Array("time","location","domain","ref","swidth","sheight","cookies","ip","agent","historylen");
        $aux = Array();
        foreach($keys as $k)
            $aux[$k] = $row[$k];
        $aux['country'] = geoip_country_name_by_addr($gi,$aux['ip']);
        $aux['type'] = 'pageview';

        # At some point LLOOGG supported the ability to display user clicks
        # adsense ADs. Now the javascript we inject no longer support this
        # but the support inside LLOOGG itself remains.
        if ($row['event_id'] == LOG_EVENT_ADCLICK) $aux['type'] = 'adclick';
        $t[] = $aux;
        if($row['id'] > $maxid) $maxid = $row['id'];
    }
    geoip_close($gi);
    $t[] = $maxid+1;
    if ($proto >= 2) {
        $t[] = logTodayVisitors();
        $t[] = logTodayPageviews();
    }
    $json = new Services_JSON();
    $output = $json->encode($t);
    echo($output);
}
header("Content-Type: text/html; charset=UTF-8");
poll();
?>
