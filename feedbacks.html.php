<?
    require("lib.php");
    Config("title","Feedbacks");
    include("header.php");
?>
<h4>Send feedbacks</h4>
<form name="f" method="POST" action="/sendfeedback">
<table border="0" cellpadding="4">
<tr><td align="right">Subject:</td><td>
<select name="subject">
<option value="Problemi con iscrizione/utilizzo">Help</option>
<option value="Richiesta funzionalita' o suggerimento">Feature request</option>
<option value="Segnalazione baco">Possible bug</option>
<option value="Altro">Something else</option>
</select>
</td></tr>
<tr><td align="right">Your email (for reply):</td><td><input type="text" class="inputtext" name="email" value="<?if(isLoggedIn()) {echo (userEmail());}?>"></td></tr>
<tr><td align="right" valign="top">Message:</td>
<td><textarea name="body" cols="55" rows="8" class="inputTextArea"></textarea></td></tr>
<tr><td colspan="2" align="right"><input type="button" name="doit" value="Send message" onClick="return checkFeedbacksForm()" class="inputbutton" id="sendButton"></td></tr>
</table>
</form>
<? include("footer.php") ?>
