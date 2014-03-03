<?
require("lib.php");

$username = g('usernameReg');
$password = g("passReg");
$email = g("email");

if (g("passReg","") != g("repassReg",""))
    panic("Some var is emtpy or pass != repass after javascript test");

if (strlen($password) < 5)
    panic("Pass is shorter than 5 chars after javascript test");

$r = redisLink();
if ($r->get("username:$username:id"))
    panic("Username already in use");

# Everything is ok, Register the user!
$userid = $r->incr("global:nextUserId");
$r->set("username:$username:id",$userid);
$r->set("uid:$userid:username",$username);
$r->set("uid:$userid:hashpass",bcrypt_hash($password));
$r->set("uid:$userid:email",$email);
$r->set("uid:$userid:regtime",time());
$r->set("uid:$userid:excludemyvisits",'0');

$authsecret = getrand();
$r->set("uid:$userid:auth",$authsecret);
$r->set("auth:$authsecret",$userid);

# Manage a Set with all the users, may be userful in the future
$r->sadd("global:users",$userid);

# User registered! Login this guy
$now = time()+3600*24*365;
setCookie("secret",$authsecret,$now,"/");
setCookie("secret",$authsecret,$now,"/",Config("domain"));
setCookie("secret",$authsecret,$now,"/",".".Config("domain"));

# Send the email
sendMail("welcome.txt",Config("emailreg"),$email,"%password%",$password,"%username%",$username);

header("Location: /success.html.php");
?>
