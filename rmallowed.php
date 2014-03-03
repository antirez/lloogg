<?
    require("lib.php");
    if (!isLoggedIn()) exit;
    if (g("username") === false || strlen(trim(g("username"))) == 0) {
        exit;
    }
    $username = g("username");
    if (($id = getIdFromUsername($username)) != -1) {
        delAllowed(userId(),$id);
    }
    header("Location: /settings");
?>
