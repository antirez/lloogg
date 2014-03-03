<?
header("Content-Type:text/html; charset=utf-8");
include("lib.php");
if (!isLoggedIn()) exit;
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="shortcut icon" href="/favicon.ico">
<link rel="stylesheet" href="/css/style.css?v=4" type="text/css">
<script type="text/javascript" src="/javascript/log.js.php?v=3"></script>
<title>Trends</title>
</head>
<body>
<script type="text/javascript">
function changeperiod() {
    var s = $('graphperiod');
    var p = s.options[s.selectedIndex].value;
    var l = $('logscale').checked ? "1" : "0";
    window.location.href="/trendsframe.php?period="+mfxEscape(p)+"&logscale="+l;
}
</script>
<div id="trendsctrl">
<h2>Control</h2>
<a href="#" onclick="window.location.reload()">refresh graphs</a><br/>
<select id="graphperiod" onchange="changeperiod()">
<?
$logscale=gi("logscale",0);
$period=g("period","15days");
$periods=Array();
$periods[]="days";
$periods[]="weeks";
$periods[]="months";
$periods[]="years";
foreach($periods as $p) {
    $selected = ($period == $p) ? " selected " : "";
    echo("<option ".$selected."value=\"$p\">$p</option>\n");
}
?>
</select><br/>
<input onchange="changeperiod()" type="checkbox" id="logscale" value="1" <?if($logscale) echo("checked")?> /> Logarithmic
</div>
<?

function trimLongString($s,$max=30) {
    if (strlen($s) <= $max) return $s;
    return substr($s,0,$max)."...";
}

if ($period == "days") {
    $unit = "day";
    $days = 40;
} else if ($period == "weeks") {
    $unit = "week";
    $days = 32*7;
} else if ($period == "months") {
    $unit = "month";
    $days = 30*30;
} else if ($period == "years") {
    $unit = "year";
    $days = 365*3;
} else {
    $unit = "day";
    $days = 40;
}

$uid = reqUserId();
$days = intval($days);
$t = time();
$t = $t-($t%(3600*24));
$r = redisLink();
$rows = Array();
$nullrecord = 0;
for ($j = 0; $j < $days; $j++) {
    $row = Array(
        "basetime" => $t,
        "visitors" => $r->get("day:uv:$uid:$t"),
        "pageviews" => $r->get("day:pv:$uid:$t"),
        "retvisitors" => $r->get("day:rv:$uid:$t")
    );
    if ($row['visitors'] === null) {
        $nullrecord++;
        if ($nullrecord == 5) break;
    } else {
        $nullrecord = 0;
    }
    $t -= 3600*24;
    $rows[] = $row;
}
while(count($rows) && ($rows[count($rows)-1]['pageviews']) == null)
    array_pop($rows);
$numrows = count($rows);
$graph1=Array("title"=>"Unique visitors","logscale"=>$logscale);
$graph2=Array("title"=>"Pageviews","logscale"=>$logscale);
$graph3=Array("title"=>"Returning visitors (percentage)","logscale"=>$logscale);
$graph4=Array("title"=>"Pageviews per visitor","logscale"=>$logscale);
$data1 = $data2 = $data3 = $data4 = Array();
$vtot = 0;
$ptot = 0;
$rtot = 0;
$label = "";
$lastrow = false;

while(count($rows)) {
    # lookahead of one row
    $row = array_shift($rows);
    if (count($rows)) {
        $nextrow = $rows[0];
        $lastrow = false;
    } else {
        $lastrow = true;
    }

    $monthname = gmstrftime("%b",$row['basetime']);
    $weekname = gmstrftime("%a",$row['basetime']);
    $monthday = gmstrftime("%d",$row['basetime']);
    $year =  gmstrftime("%y",$row['basetime']);

    if ($unit == "day") {
        $label = $weekname[0];
        $vtot = $row['visitors'];
        $ptot = $row['pageviews'];
        $rtot = $row['retvisitors'];
    } else if ($unit == "week") {
        if (strtolower($weekname) == "sun") {
            $vtot = $ptot = $rtot = 0;
        }
        $vtot += $row['visitors'];
        $ptot += $row['pageviews'];
        $rtot += $row['retvisitors'];
        if (strtolower($weekname) != "mon" && !$lastrow) continue;
        $label = $monthname[0]." ".$monthday;
    } else if ($unit == "month") {
        if (!$lastrow) $nextmonthname = gmstrftime("%b",$nextrow['basetime']);
        if (isset($resetnext)) {
            $vtot = $ptot = $rtot = 0;
            unset($resetnext);
        }
        $vtot += $row['visitors'];
        $ptot += $row['pageviews'];
        $rtot += $row['retvisitors'];
        if (!$lastrow && $nextmonthname == $monthname) continue;
        $resetnext=true;
        $label = $monthname[0].$monthname[1];
    } else if ($unit == "year") {
        if (!$lastrow) $nextyear = gmstrftime("%y",$nextrow['basetime']);
        if (isset($resetnext)) {
            $vtot = $ptot = $rtot = 0;
            unset($resetnext);
        }
        $vtot += $row['visitors'];
        $ptot += $row['pageviews'];
        $rtot += $row['retvisitors'];
        if (!$lastrow && $nextyear == $year) continue;
        $resetnext=true;
        $label = $year;
    }
    $data1[] = Array("val"=>$vtot,"lab"=>$label);
    $data2[] = Array("val"=>$ptot,"lab"=>$label);
    if ($vtot == 0)
        $perc = 0;
    else
        $perc = round(($rtot*100)/$vtot,2);
    $tip = $perc."% ($rtot visitors)";
    $data3[] = Array("val"=>$perc,"lab"=>$label,"tip"=>$tip);
    if ($vtot)
        $pvpervisitor = round((float)$ptot/$vtot,2);
    else
        $pvpervisitor = 0;
    $data4[] = Array("val"=>$pvpervisitor,"lab"=>$label);
    if ($lastrow) break;
}
$data1 = array_reverse($data1);
$data2 = array_reverse($data2);
$data3 = array_reverse($data3);
$data4 = array_reverse($data4);

echo(logGraph("uv_graph",$data1,$graph1));
echo(logGraph("pv_graph",$data2,$graph2));
echo(logGraph("rv_graph",$data3,$graph3));
echo(logGraph("pvu_graph",$data4,$graph4));

?>
<script type="text/javascript">
registerClicktipBulk('div','bar','');
</script>
</body>
</html>
