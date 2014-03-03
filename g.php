<?
function g($name,$default=false) {
    global $_GET, $_POST;
    if (!isset($_GET[$name]) && !isset($_POST[$name])) {
        return $default;
    }
    if (isset($_GET[$name]))
        return $_GET[$name];
    return $_POST[$name];
}

function gi($name,$default=false) {
    return intval(g($name,$default));
}
?>
