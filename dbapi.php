<?
function redisLink() {
    static $r = false;

    if ($r) return $r;
    $r = new Redis(Config('redishost'),Config('redisport'));
    $r->connect();
    return $r;
}
?>
