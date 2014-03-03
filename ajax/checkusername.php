<?
require("../lib.php");

$username=trim(g("username"));
// Check if the username is reserved.
if (in_array(strtolower($username), Config("reservedusers"))) {
    echo("ERROR");
    exit(1);
}

// Check if the username is available.
$r = redisLink();
if ($r->get("username:$username:id")) {
    echo("ERROR");
} else {
    echo("OK");
}

?>
