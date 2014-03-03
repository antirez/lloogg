<?
    require("lib.php");
    Config("title","LLOOGG - web 2.0 tail -f access.log");
    include("header.php");
    if (!isLoggedIn()) {
        include("synopsys.php");
    } else {
        include("usermain.php");
    }
    include("footer.php")
?>
