<?
    require("lib.php");
    Config("title","LLOOGG Login");
    include("header.php");
?>
<?if(!isLoggedIn()){?>
    <form name="f" method="POST" onsubmit="tryLogin(); return false;">
    <table border="0" cellpadding="6">
    <tr><td align="right">Username</td><td><input type="text" class="inputtext" name="username"></td></tr>
    <tr><td align="right">Password</td><td><input type="password" class="inputtext" name="pass"></td></tr>
    <tr><td align="right" colspan="2"><small>Remember me</small>&nbsp;<input type="checkbox" name="rememberme" value="1" checked></td></tr>
    <tr><td colspan="2" align="right"><input type="submit" name="login" value="Enter" id="enterButton" class="inputbutton"></td></tr>
    </table>
    </form>
<?}else{?>
    <p>You are already logged in as
    <strong><?=htmlentities(username())?></strong>!
    <p>To switch user <a href="/logout.php">logout</a> first.
<?}?>
<?
    include("footer.php")
?>
