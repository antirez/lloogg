function __lloogg__() {
    var dc = document.cookie;
    var nv = 0;
    var rv = 1;
    if (dc.indexOf('__llooggrvc__=') == -1) {
        document.cookie = "__llooggrvc__=1; expires=Sun, 18 Jan 2038 00:00:00 GMT; path=/";
        rv = 0;
    }
    if (dc.indexOf('__lloogguvc__=') == -1) {
        nv = 1;
        var now = new Date;
        now.setTime(now.getTime()+(3600*24*1000));
        document.cookie = "__lloogguvc__=1; expires="+now.toGMTString()+"; path=/";
    }
    var u = lloogg_clientid;
    var l = document.location;
    var r = (typeof(document.referrer) == 'undefined') ? '' : document.referrer;
    var w = screen.width;
    var h = screen.height;
    var a = navigator.userAgent;
    var hl = 0;
    var c = 'na';
    if (typeof(navigator.cookieEnabled) != 'undefined')
        c = navigator.cookieEnabled ? 'y' : 'n';
    if (typeof(history) != 'undefined' && typeof(history.length) != 'undefined')
        hl = history.length;
    var e = function (s) {
        try {
            return encodeURIComponent(s);
        } catch(e) {
            var e = escape(s);
            e = e.replace(/@/g,"%40");
            e = e.replace(/\//g,"%2f");
            e = e.replace(/\+/g,"%2b");
            return e;
        }
    };
    var args='';
    var img=new Image(1,1);
    args += '?u='+e(u); args += '&l='+e(l); args += '&r='+e(r);
    args += '&w='+e(w); args += '&h='+e(h); args += '&a='+e(a);
    args += '&c='+e(c); args += '&hl='+e(hl); args += "&nv="+e(nv);
    args += '&rv='+e(rv);
    img.src='http://lloogg.com/recv.php'+args;
    img.onload = function() { return; };
}
__lloogg__();
