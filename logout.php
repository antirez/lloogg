<?
require("lib.php");

setcookie("secret","",time()-3600*48,"/");
setcookie("secret","",time()-3600*48,"/",Config("domain"));
setcookie("secret","",time()-3600*48,"/",".".Config("domain"));

if (isLoggedIn()) {
    $r = redisLink();
    $newauthsecret = getrand();
    $userid = $User['id'];
    $oldauthsecret = $r->get("uid:$userid:auth");

    $r->set("uid:$userid:auth",$newauthsecret);
    $r->set("auth:$newauthsecret",$userid);
    $r->delete("auth:$oldauthsecret");
}
header("Location: /");
exit;
?>
