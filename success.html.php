<?
    require("lib.php");
    Config("title","Registrastion succeeded");
    include("header.php");
?>
<p>
Your account has been created.
<p>
Go to the <a href="http://<?=Config("domain")?>/settings">setting page</a> and get the code for your pages.
<?
    include("footer.php")
?>
