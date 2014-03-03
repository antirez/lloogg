<?
    require("../lib.php");
    if (!isLoggedIn()) exit;
    if (g("username") === false || strlen(trim(g("username"))) == 0) {
        echo("ERR: Please specify the username to allow");
        exit;
    }
    if (isPro() || count(getAllowing()) < 1) {
        $uid = getIdFromUsername(g("username"));
        if ($uid == -1) {
            echo("ERR: The username specified does not exist");
        } else {
            addAllowed(userId(),$uid);
            echo("OK");
        }
    } else {
        echo("ERR: Free accounts can only allow a single user. Upgrade to PRO in order to allow infinite users.");
    }
?>
