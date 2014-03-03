<?
function isPro($id=false) {
    global $__procache;
    if (isset($__procache)) return $__procache !== false;

    if ($id === false) {
        $id=userId();
        if ($id == -1) return false;
    }

    $r = redisLink();
    $pro = $r->get("uid:$id:pro");
    if (!$pro) {
        $__procache = false;
        return $__procache;
    }
    $__procache = $pro;
    return true;
}

function setPro($uid,$level,$duration=3600) {
    $r = redisLink();
    if ($level == 0) {
        $r->del("uid:$uid:pro");
        $r->del("uid:$uid:pro.since");
        $r->del("uid:$uid:pro.until");
        $r->srem("global:prousers",$uid);
    } else {
        $r->set("uid:$uid:pro",$level);
        $r->set("uid:$uid:pro.since",time());
        $r->set("uid:$uid:pro.until",time()+$duration);
        $r->sadd("global:prousers",$uid);
    }
}

function getAllowing($uid=false) {
    if ($uid === false) $uid = userId();
    $r = redisLink();
    return $r->smembers("uid:$uid:allowing");
}

function getAllowed($uid=false) {
    if ($uid === false) $uid = userId();
    $r = redisLink();
    return $r->smembers("uid:$uid:allowed");
}

function addAllowed($allowing_id,$allowed_id) {
    $r = redisLink();
    $r->sadd("uid:$allowing_id:allowing",$allowed_id);
    return $r->sadd("uid:$allowed_id:allowed",$allowing_id);
}

function delAllowed($allowing_id,$allowed_id) {
    $r = redisLink();
    $r->srem("uid:$allowing_id:allowing",$allowed_id);
    return $r->srem("uid:$allowed_id:allowed",$allowing_id);
}

function reqUserId() {
    global $_COOKIE;

    $r = redisLink();
    if (!isset($_COOKIE['requser'])) return userId();
    $username = $_COOKIE['requser'];
    if (($id = getIdFromUsername($username)) == -1) {
        return userId();
    }
    if ($r->sismember("uid:$id:allowing",userId())) {
        return $id;
    } else {
        return userId();
    }
}

function getProLevel() {
    global $__procache;
    return $__procache;
}
?>
