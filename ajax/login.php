<?
require("../lib.php");
if (g('username') === false || g('pass') === false || g('rememberme') === false) {
    echo("ERR");
    exit;
}
$user = g('username');
$pass = g('pass');
$r = redisLink();
$userid=$r->get("username:$user:id");
if(!$userid){
    echo("ERR");
    exit;
}

if(bcrypt_check($pass,$r->get("uid:$userid:hashpass"))) {
    $secret=$r->get("uid:$userid:auth");
    if (gi('rememberme',0) == 1) {
        $now = time()+3600*24*365;
        setCookie("secret",$secret,$now,"/");
        setCookie("secret",$secret,$now,"/",Config("domain"));
        setCookie("secret",$secret,$now,"/",".".Config("domain"));
    } else {
        // Just for this session.
        setCookie("secret",$secret,0,"/");
        setCookie("secret",$secret,0,"/",Config("domain"));
        setCookie("secret",$secret,0,"/",".".Config("domain"));
    }
    echo("OK:AUTHENTICATED");
} else {
    echo "ERR";
}
?>
