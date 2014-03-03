<?
header("Content-Type:text/html; charset=utf-8");
if (isLoggedIn()) userUpdateExcludeVisitsCookie();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<link rel="shortcut icon" href="/favicon.ico">
<?
if(strstr(strtolower($_SERVER['HTTP_USER_AGENT']),"iphone") !== false)
    $cssprefix="iphone_";
else
    $cssprefix="";
?>
<link rel="stylesheet" href="/css/<?=$cssprefix?>style.css?v=8" type="text/css">
<script type="text/javascript" src="/javascript/log.js.php?v=4"></script>
<title><?echo Config("title")?></title>
</head>
<body>

<div id="header">
<a href="/"><img src="/images/lloogg.png" alt="lloogg logo" border="0" /></a>
<div id="toplinks">
<a href="/">home</a> -
<?if(!isLoggedIn()) {?>
<a href="/register.html.php">register</a> -
<a href="/login.html.php">login</a>
<?} else {?>
<a href="/logout.php">logout</a>
<?}?>
</div>
<div id="slogan">
your web2.0 tail -f access.log
</div>
</div>

<div id="main">
<?if(isLoggedIn()) include("usernav.php");?>
