<?
# Check if the user is logged in. If not, and $redirect is true,
# The user is redirected to the login page.
# If the user is logged in an array User is populated with
# user information.
#
# The function returns 1 if the user is logged in, otherwise 0 is returned.

function userId() {
    global $User;
    if(empty($User['id'])) return -1;
    else return $User['id'];
}

function userName() {
    global $User;
    return $User['username'];
}

function userPass() {
    global $User;
    return $User['password'];
}

function userEmail() {
    global $User;
    return $User['email'];
}

function userExcludeMyVisits() {
    global $User;
    return $User['excludemyvisits'];
}

function userShowUserAgent() {
    global $User;
    return $User['showuseragent'];
}

function isLoggedIn() {
    global $User, $_COOKIE;

    if (isset($User)) return true;

    if (isset($_COOKIE['secret'])) {
        $r = redisLink();
        $authcookie = $_COOKIE['secret'];
        if ($userid = $r->get("auth:$authcookie")) {
            if ($r->get("uid:$userid:auth") != $authcookie) return false;
            loadUserInfo($userid);
            return true;
        }
    }
    return false;
}

function loadUserInfo($userid) {
    global $User;
    $r = redisLink();
    $keys=Array('username','password',"email",'excludemyvisits','regtime','showuseragent');
    $uidkeys = Array();
    foreach($keys as $k) {
        $uidkeys[] = "uid:$userid:$k";
    }
    $User['id'] = $userid;
    $values=$r->mget($uidkeys);
    for($i = 0; $i < count($keys); $i++) {
        $User[$keys[$i]] = $values[$i];
    }
}

function isAdmin() {
    global $User;
    if (!isLoggedIn()) return 0;
    if (Config("adminuser") && $User['username'] == Config("adminuser")) return 1;
    return 0;
}

function getUsernameById($id) {
    if ($id == -1) return "Utente anonimo";
    $r = redisLink();
    $username = $r->get("uid:$id:username");
    if ($username) {
            return $username;
    } else {
            return "Utente rimosso"; // Should never happen. Here as sentinel.
    }
}

// Get the ID from the username, if the username does not exists
// -1 is returned.
function getIdFromUsername($username) {
    $username = str_replace(" ","_",$username);
    $r = redisLink();
    $id = $r->get("username:$username:id");
    if (!$id) {
        return -1;
    } else {
        return $id;
    }
}

function handleMissingUser($username){
    $id=getIdFromUsername($username);
    if($id==-1){
        Config("title","Utente sconosciuto");
        include("header.php");
        echo '<div style="margin-left:20px;">L\'utente <strong>'.htmlentities($username).'</strong> non esiste nel sistema.';
        include("footer.php");
        exit;
    }
    return;
}

function userUpdateExcludeVisitsCookie() {
    global $_COOKIE;

    if (!isLoggedIn()) return;
    if (userExcludeMyVisits()) {
        if (!isset($_COOKIE['excludemyvisits'])) {
            $exclid = createSecureId(userId(),"excludeid");
            setCookie("excludemyvisits",$exclid,2147483647,"/");
        }
    } else {
        if (isset($_COOKIE['excludemyvisits'])) {
            setCookie("excludemyvisits","",time()-3600*48,"/");
        }
    }
}
?>
