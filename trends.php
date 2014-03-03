<?
function logTodayTrend() {
    $uid = reqUserId();
    $r = redisLink();
    $t = time()-(time()%(3600*24));
    $pageviews = $visitors = 0;
    $n = $r->get("day:pv:$uid:$t");
    if ($n) $pageviews = $n;
    $n = $r->get("day:uv:$uid:$t");
    if ($n) $visitors = $n;
    return Array("pageviews"=>$pageviews,"visitors"=>$visitors);
}

function logTodayVisitors() {
    $trend = logTodayTrend();
    return $trend['visitors'];
}

function logTodayPageviews() {
    $trend = logTodayTrend();
    return $trend['pageviews'];
}

function logGraphMaxValue($data) {
    if (count($data) == 0) return 0;
    $max = $data[0]['val'];
    foreach ($data as $v) {
        $v = $v['val'];
        $max = ($v > $max) ? $v : $max;
    }
    return $max;
}

function logGraphBarHeight($logscale,$max,$value) {
    if ($max == 0 || $value == 0) return "0%";
    if ($logscale) {
        $logrange=10;
        $value = ($logrange*$value)/$max;

        $value = log(1+$value);
        $max = log(1+$logrange);
    }
    $h = (int)floor((80*$value)/$max);
    return "$h%";
}

function logGraph($id,$data,$conf = Array()) {
    $logscale = isset($conf['logscale']) ? $conf['logscale'] : 0;
    $width = isset($conf['width']) ? $conf['width'] : 600;
    $height = isset($conf['height']) ? $conf['height'] : 140;
    # Compute some value
    $slots = max(count($data),12);
    $w = floor((($slots > 15) ? 90 : 80)/$slots);
    $lstep = floor(90/$slots);
    # Our graph lives inside a main div having the given id
    $html = '<div id="'.$id.'" class="graph" style="width: '.$width.'px; ';
    $html .= 'height: '.$height.'px;">';
    # Output the div for the title if any
    if (isset($conf['title'])) {
        $html .= '<div class="graphtitle">'.utf8entities($conf['title'])."</div>\n";
    }
    $max = logGraphMaxValue($data);
    # Output the div for the max line
    if ($max > 0) {
        $h = logGraphBarHeight($logscale,$max,$max);
        $html .= '<div class="barmax" style="bottom: '.$h.'">'.$max."</div>\n";
    }
    # Output a div for every bar
    $pos = 0;
    for($i = 0; $i < $slots-count($data); $i++) {
        $l = $pos*$lstep;
        $html .= '<div class="bar" style="width:'.$w.'%; bottom:0px; height:1px; left:'.$l.'%; background-color:#dddddd; border:none;"><!-- --></div>'."\n";
        $pos++;
    }
    $tot = 0;
    for($i = 0; $i < count($data); $i++) {
        $tot += $data[$i]['val'];
        $h = logGraphBarHeight($logscale,$max,$data[$i]['val']);
        $l = $pos*$lstep;
        if ($i==count($data)-1) {
            $class = "bar lastbar";
        } else if ($data[$i]['lab'] == 'S') {
            $class = "bar webar";
        } else {
            $class = "bar stdbar";
        }
        $tip = isset($data[$i]['tip']) ? $data[$i]['tip'] : $data[$i]['val'];
        $html .= '<div class="'.$class.'" clicktip="'.$tip.'" style="bottom:0px; height:'.$h.'; left:'.$l.'%; width:'.$w.'%;">'.($data[$i]['lab']).'</div>'."\n";
        $pos++;
    }
    $avg = count($data) ? $tot/count($data) : 0;
    if ($avg > 10) {
        $avg = floor($avg);
    } else {
        $avg = round($avg,2);
    }
    # Output the div for the average line
    if ($avg > 0 && count($data) > 1) {
        if ($logscale) {
            if ($max < 10) $gridstep = .01;
            else if ($max < 100) $gridstep = 1;
            else if ($max < 5000) $gridstep = 100;
            else if ($max < 10000) $gridstep = 1000;
            else if ($max < 100000) $gridstep = 10000;
            if ($gridstep) {
                $step = 0;
                $lasth = "0%";
                while(1) {
                    $step += $gridstep;
                    if ($step > $max) break;
                    $h = logGraphBarHeight($logscale,$max,$step);
                    $hmax = logGraphBarHeight($logscale,$max,$max);
                    if (trim($h,"%")-trim($lasth,"%") > 10) {
                        if (trim($h,"%") < 70) $html .= '<div class="baravg" style="bottom: '.$h.'">'.round($step,2)."</div>\n";
                        $lasth = $h;
                    }
                }
            }
        } else {
            $h = logGraphBarHeight($logscale,$max,$avg);
            $hmax = logGraphBarHeight($logscale,$max,$max);
            if (trim($hmax,"%")/trim($h,"%") <= 1.15) $avg="";
            else $avg = "avg ".$avg;
            $html .= '<div class="baravg" style="bottom: '.$h.'">'.$avg."</div>\n";
        }
    }
    # Done, almost.
    $html .= '</div>';
    return $html;
}
?>
