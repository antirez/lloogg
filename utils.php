<?
function vardump($a = false) {
    global $_GET, $_POST, $_FILES, $_SESSION;
    echo("<pre>");
    if ($a === false) {
        echo("<b>GET:</b>");
        var_dump($_GET);
        echo("<b>POST:</b>");
        var_dump($_POST);
        echo("<b>SESSION:</b>");
        var_dump($_SESSION);
        echo("<b>FILES:</b>");
        var_dump($_FILES);
    } else {
        var_dump($a);
    }
    echo("</pre>");
}

function panic($msg = "Unknown Error") {
    echo("$msg - contact the system administrator");
    exit(1);
}

function requireGetVarsOrPanic($vars) {
    global $_GET;
    for ($i = 0; $i < count($vars); $i++) {
        if (!isset($_GET[$vars[$i]])) {
            panic("(GET) Missing arguments");
        }
    }
}

function requireGetNonEmptyOrPanic($vars) {
    global $_GET;
    for ($i = 0; $i < count($vars); $i++) {
        if (!isset($_GET[$vars[$i]]) || strlen($_GET[$vars[$i]]) == 0) {
            panic("(GET) Empty arguments, $vars[$i]");
        }
    }
}

function requirePostVarsOrPanic($vars) {
    global $_POST;
    for ($i = 0; $i < count($vars); $i++) {
        if (!isset($_POST[$vars[$i]])) {
            panic("(POST) Missing arguments");
        }
    }
}

function requirePostNonEmptyOrPanic($vars) {
    global $_POST;
    for ($i = 0; $i < count($vars); $i++) {
        if (!isset($_POST[$vars[$i]]) || strlen($_POST[$vars[$i]]) == 0) {
            panic("(POST) Empty arguments, $vars[$i]");
        }
    }
}

function getrand() {
    $fd = fopen("/dev/urandom", "r");
    $data = fread($fd, 16);
    fclose($fd);
    return md5($data);
}

function fileGetContent($filename) {
    return implode("", file($filename));
}

//sendMail(filename,from,to,var1,value1, ....,varN,valueN);
function sendMail() {
    $argv = func_get_args();
    $argc = count($argv);
    if (($argc % 2) == 0 || $argc < 3) {
        die("sendMail() called with wrong argument count");
    }
    $from = $argv[1];
    $to = $argv[2];
    $email = fileGetContent("mail/$argv[0]");
    $email = str_replace("\r", "", $email);
    $lines = explode("\n", $email);
    $subject = $lines[0];
    array_shift($lines);
    $email = implode("\n", $lines);
    for ($i = 3; $i < count($argv); $i += 2) {
        $subject = str_replace($argv[$i], $argv[$i+1], $subject);
        $email = str_replace($argv[$i], $argv[$i+1], $email);
    }
    $e_hdr = "From: $from\nReply-To: $from\n";
    mail($to, $subject, $email, $e_hdr);
}

function sendMailBody($from, $to, $subject, $body) {
    $e_hdr = "From: $from\nReply-To: $from\n";
    mail($to, $subject, $body, $e_hdr);
}

function strelapsed($t) {
    $t = time()-$t;
    if ($t < 60) {
        return "$t seconds";
    }
    if ($t < 3600) {
        $m = (int)($t/60);
	if($m == 1)
        	return "$m minute";
	else
        	return "$m minutes";
    }
    if ($t < (3600*48)) {
        $h = (int)($t/3600);
	if($h == 1)
        	return "$h hour";
	else
	        return "$h hours";
    }
    $d = (int)($t/(3600*24)); if($d == 1)
        	return "$d day";
	else
		return "$d days";
}

function utf8entities($s) {
    return trim(htmlentities($s,ENT_QUOTES,'utf-8'));
}

function displayEmailNoSpam($email){
    echo emailNoSpam($email);
}

function emailNoSpam($email){
    if(empty($email)) return false;
    $array=explode("@",$email);
    if(!is_array($array) || count($array) != 2) return $email;
    return '<script type="text/javascript">document.write("'.$array[0].'");document.write("@");document.write("'.$array[1].'")</script>';
}

function first($a) {
    return $a[0];
}

function rest($a) {
    return array_slice($a,1);
}

function goHome() {
    header("Location: /");
    exit;
}

function timeBase($t = false) {
    if ($t === false) $t = time();
    $t = $t-($t%(3600*24));
    return $t;
}

?>
