<?
    require("lib.php");
    if (!isLoggedIn()) exit;

    if (isset($_POST['saveoptions'])) {
        $excludemyvisits = gi("excludemyvisits",0);
        $showuseragent = gi("showuseragent",0);
        $r = redisLink();
        $r->set("uid:".$User['id'].":excludemyvisits",$excludemyvisits);
        $r->set("uid:".$User['id'].":showuseragent",$showuseragent);
        userUpdateExcludeVisitsCookie();
        header("Location: /settings?s=1");
        exit;
    }

    Config("title","LLOOGG - web 2.0 tail -f access.log");
    include("header.php");
?>
<div id="uimain">
<p>
Cut and paste the following code in every page of your site for wich you are interested in seeing accesses in real time.
<strong>Important notes:</strong>
<ul>
<li>It's better to put the code just above the closing &lt;/body&gt; tag.</li>
<li>If you are using wordpress you can find the &lt;/body&gt; tag is in the <strong>footer.php</strong> page.</li>
</ul>
<p>
<?
$_js="<script type=\"text/javascript\">
lloogg_clientid = \"%%clientid%%\";
</script>
<script type=\"text/javascript\" src=\"%%urldomain%%/l.js?c=%%clientid%%\">
</script>";
$_js=str_replace("%%clientid%%",createSecureId(userId(),"userid"),$_js);
$_js=str_replace("%%urldomain%%",Config("urldomain"),$_js);
?>
<textarea rows=4 cols=80 id="jscode">
<?=htmlentities($_js)?>
</textarea>
<br/><br/>
<input type="button" value="Select" onclick="$('jscode').select()">
<h3>Status</h3>
<?
$r = redisLink();
$lastvisit = $r->lindex("last:".userId(),-1);
$statusok = false;
if ($lastvisit) {
    $row = unserialize($lastvisit);
    if ((time() - $row['time']) < 3600*24)
        $statusok = true;
}
if ($statusok) {
    echo("We are correctly receiving data");
} else {
    echo("We are NOT receiving data, make sure to install the javascript tag in your web site");
}
?>
<?
$excludechecked = userExcludeMyVisits() ? "checked" : "";
$showuseragent = userShowUserAgent() ? "checked" : "";
$savedhtml = gi("s",0) ? ' <span id="saved" style="color:red">(saved)</span>' : '';
?>
<h3>Options<?=$savedhtml?></h3>
<form method="post" action="usercode.php" id="optionsform">
Don't log my own visits <input type="checkbox" name="excludemyvisits" value="1" <?=$excludechecked?>><br/><br/>
Show clients user agent <input type="checkbox" name="showuseragent" value="1" <?=$showuseragent?>><br/><br/>
<input type="submit" name="saveoptions" value="Save options">
<br/><br/>
</form>
<h3>Give access</h3>
It is possible to give read-only access to your stats to other LLOOGG users. You can later remove the access if you want.<br/><br/>
Allow
<input id="allowusername" type="text" name="username">
to see my stats in read only
<input type="button" name="doit" value="Allow!" onclick="allowUser()">
<?
$allowed = getAllowing();
if (count($allowed)) {  
    echo("<h4>Allowed users</h4>");
    echo("<ul>");
    foreach($allowed as $id) {
        echo("<li>".utf8entities(getUsernameById($id))." <a href=\"rmallowed.php?username=".urlencode(getUsernameById($id))."\">remove</a></li>");
    }
    echo("</ul>");
}
?>
</div>
<script type="text/javascript">
function allowUserHandler(res) {
    if (res.indexOf("ERR:") != -1) {
        alert(res);
    } else {
        window.location.reload();
    }
}

function allowUser() {
    mfxGetRand("/ajax/allow.php?username="+mfxEscape($('allowusername').value), allowUserHandler);
}
</script>
<?
    include("footer.php");
?>
