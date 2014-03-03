<?
function logParseSearch($ref) {
    /* Google */
    if (preg_match('/http:\/\/([^.]+\.)?google(\.[^\/]+)\/(search|custom|m|url|ie).*(&|\?)(q|as_q)=([^&]+)/i',$ref,$m))
    {
        $res = Array(
            "brand"=>"Google",
            "nbrand"=>"google".$m[2],
            "query"=>$m[6]
        );
    }
    /* Bing */
    else if (preg_match('/http:\/\/([^.]+\.)?bing.com\/(search|spresults).*(&|\?)q=([^&]+)/',$ref,$m)) {
        $res = Array(
            "brand"=> "Bing",
            "nbrand"=> "bing.com",
            "query"=> $m[4]
        );
    }
    /* Yahoo */
    else if (preg_match('/http:\/\/([^.]+\.)?search.yahoo.com\/search.*(&|\?)p=([^&]+)/',$ref,$m)) {
        $res = Array(
            "brand"=> "Yahoo",
            "nbrand"=> $m[1]."search.yahoo.com",
            "query"=> $m[3]
        );
    }
    /* Microsoft Live */
    else if (preg_match('/http:\/\/search.live.com\/results.aspx.*(&|\?)q=([^&]+)/',$ref,$m)) {
        $res = Array(
            "brand"=> "MS LIVE",
            "nbrand"=> "search.live.com",
            "query"=> $m[2]
        );
    }
    /* Alice */
    else if (preg_match('/http:\/\/search.alice.it\/search\/cgi\/search.cgi.*(&|\?)qs=([^&]+)/',$ref,$m)) {
        $res = Array(
            "brand"=> "Alice",
            "nbrand"=> "search.alice.it",
            "query"=> $m[2]
        );
    }
    /* Virgilio */
    // http://ricerca.virgilio.it/ricerca?qs=collettivamente.com&Cerca=&lr=
    else if (preg_match('/http:\/\/ricerca.virgilio.it\/ricerca.*(&|\?)qs=([^&]+)/',$ref,$m)) {
        $res = Array(
            "brand"=> "Virgilio",
            "nbrand"=> "ricerca.virgilio.it",
            "query"=> $m[2]
        );
    }
    /* Arianna */
    else if (preg_match('/http:\/\/arianna.libero.it\/search\/abin\/integrata.cgi.*(&|\?)query=([^&]+)/',$ref,$m)) {
        $res = Array(
            "brand"=> "Arianna",
            "nbrand"=> "arianna.libero.it",
            "query"=> $m[2]
        );
    }
    /* MSN */
    else if (preg_match('/http:\/\/search.msn.([^.]+)\/results.asp.*(&|\?)q=([^&]+)/',$ref,$m)) {
        $res = Array(
            "brand"=> "MSN",
            "nbrand"=> "search.msn.".$m[1],
            "query"=> $m[3]
        );
    } else {
        return false;
    }
    $res['query'] = urldecode($res['query']);
    return $res;
}
?>
