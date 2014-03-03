<?
setlocale(LC_ALL,"C");
if (!isset($CONFIG)) {
    $CONFIG = Array();
}

function Config($name, $value = false) {
    global $CONFIG;
    if ($value === false) {
        if (!isset($CONFIG[$name])) {
            return false;
        }
        return $CONFIG[$name];
    } else {
        $CONFIG[$name] = $value;
    }
}

// Local settings, server specific stuff, loaded from another file.
include("localconfig.php");

Config("emailreg","LLOOGG Registration <noreply@lloogg.example>");
Config("emailcom","LLOOGG Message <noreply@lloogg.example>");
Config("emailfback","Feedback from LLOOGG user <you@your-domain.example>");
Config("adminuser","admin");

define("FREE_MAXTIME",60*5);
define("FREE_WAIT",60*10);

?>
