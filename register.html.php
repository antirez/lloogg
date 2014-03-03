<?
    require("lib.php");
    if (isLoggedIn()) {
        header("Location: /");
        exit;
    }
    Config("title","Register");
    include("header.php");
?>
<h4>Create account</h4>
    <div id="regerrdiv" style="width:490px;margin:20px;visibility:hidden; display:none;">
    <span style="color:#FE5000;font-size:16px;">Warning!</span>
    <div align="justify" id="regerrtxt"></div>
    </div>

<form name="f" action="/register.php" method="post">
    <table border="0" cellpadding="4">
    <tr>
            <td align="right">Username:</td>
            <td align="left" colspan="3"><input type="text" class="inputtext" name="usernameReg"></td>
    </tr>
    <tr>
        <td colspan="4" align="right"><small>Your email address is only used for registration and to resend lost passwords, <b>we'll not send any spam message to you</b></small></td>
    </tr>
    <tr>
            <td align="right">Email:</td>
            <td><input type="text" class="inputtext" name="email"></td>
            <td align="right">Retype email:</td>
            <td><input type="text" class="inputtext" name="reemail"></td>
    </tr>
    <tr>
            <td align="right">Password:</td>
            <td><input type="password" class="inputtext" name="passReg"></td>
            <td align="right">Retype password:</td>
            <td><input type="password" class="inputtext" name="repassReg"></td></tr>
    </tr>
    <tr>
            <td colspan="4" align="right"><input type="button" id="registerButton" name="doit" value="Create my account" onClick="checkRegistrationForm()" class="inputbutton" ></td>
    </tr>
    </table>
<input type="hidden" name="s" value="<?=g("s")?>">
</form>
<?
    include("footer.php")
?>
