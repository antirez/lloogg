<div id="usernav">
<span class="username"><?=htmlentities(userName())?>
<?if(isPro()) echo(" [pro]");?>
</span> |
<?
function userNav() {
    global $_COOKIE;

    $n['View realtime logs'] = "/";
    $n['Settings'] = "/settings";
    $n['Feedbacks'] = "/feedbacks";
    if (isAdmin()) {
        $n['Admin'] = "/admin";
    }
    $c = 1;
    foreach($n as $title => $link) {
        if ($_SERVER['REQUEST_URI'] == $link ||
            strpos($_SERVER['REQUEST_URI'],$link."?") === 0) {
            echo("<strong>".htmlentities($title)."</strong> ");
        } else {
            echo("<a href=\"$link\">".htmlentities($title)."</a> ");
        }
        if ($c++ != count($n)) echo(" | ");
    }
    $allowed = getAllowed();
    if (count($allowed)) {
        $ru = isset($_COOKIE['requser']) ? $_COOKIE['requser'] : userName();
        echo(" | <select id=\"seluser\" onchange=\"switchUser()\">");
        echo("<option value=\"\">".utf8entities(userName())."</option>");
        foreach($allowed as $id) {
            $name = getUsernameById($id);
            $selected = ($ru == $name) ? "selected" : "";
            echo("<option $selected value=\"".urlencode($name)."\">".utf8entities($name)."</option>");
        }
        echo("</select>");
    }
}
userNav();
?>
</div>
