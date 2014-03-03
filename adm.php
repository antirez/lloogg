<?
function uid2html($r,$uid) {
    $keys = Array("uid:$uid:username","uid:$uid:email","uid:$uid:regtime");
    $user = $r->mget($keys);
    if (!$user[0]) return;
    echo("<tr>");
    echo("<td>".utf8entities($user[0])."</td><td>".utf8entities($user[1])."</td>"."<td>".strelapsed($user[2])."</td>");
    echo("</tr>");
}

function adminLastRegistered($count=20) {
    $r = redisLink();
    $id = $r->get("global:nextUserId");
    echo("<table>");
    for ($i = 0; $i < 50; $i++) {
        $uid = $id-$i;
        uid2html($r,$uid);
    }
    echo("</table>");
}

function adminProUsers() {
    $r = redisLink();
    $prousers = $r->smembers("global:prousers");
    echo("<table>");
    foreach($prousers as $uid) {
        uid2html($r,$uid);
    }
    echo("</table>");
}
?>
