<?
function parseurl($str) {
    if (!preg_match('/^([a-z]+):\/\/\/?([^\/]+)(.*)$/i', trim($str), $m))
        return false;
    $res['proto']=$m[1];
    $res['domain']=$m[2];
    if ($m[2] == '') $m[3] = '/';
    $res['path']=$m[3];
    $query = strstr($res['path'],"?");
    if ($query === false) {
        $res['query'] = '';
    } else {
        $res['query'] = $query;
        $plen = strlen($res['path'])-strlen($query);
        $res['path'] = substr($res['path'],0,$plen);
    }
    return $res;
}
?>
