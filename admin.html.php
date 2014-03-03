<?
    require("lib.php");
    Config("title","LLOOGG Administration");
    include("header.php");
    if (!isAdmin()) exit;
?>
<h2>PRO accounts</h2>
<form method="post" action="mkpro.php">
Set PRO user: <input type="text" name="username">
<input type="submit" value="Set as PRO">
</form>
<h2>Last registered users</h2>
<? echo(adminLastRegistered()) ?>
<h2>Pro users</h2>
<? echo(adminProUsers()) ?>
<?
    include("footer.php")
?>
