<?
    require("lib.php");
    Config("title","LLOOGG Administration");
    include("header.php");
    if (!isAdmin()) exit;
    $username = $_POST['username'];
    $uid = getIdFromUsername($username);
    if ($uid == -1) {
        echo("User ".utf8entities($username)." not found.");
    } else {
        setPro($uid,1);
        echo("User ".utf8entities($username)." is now PRO.");
    }
    include("footer.php")
?>
