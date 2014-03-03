/* Copyright(C) 2007-2008 Salvatore Sanfilippo
 * Copyright(C) 2007-2008 Fabio Pitrola
 * All Rights Reserved */

var logq = [];
var nextid = 0;
var pendingreq = false;
var ucounter = 0;
var rcounter = 0;
var logon=1;
var initialreqclicks = 4;
var reqclicks = initialreqclicks;
var uid = 0;
var ignorenextreq = 0;
var pendingclicks = 0;

function logDebug(msg) {
    mfxSpydivPush($('logsdiv'),'loginnerdiv','<b style="color:red">DEBUG:</b> '+htmlentities(msg));
}

function logDisplayEntry() {
    var nofade = undefined;
    var sd = mfxSpydivPush($('logsdiv'),'loginnerdiv',log2html(logq[0]));

    sd.id = 'l'+logq[0].uid;
    if (typeof(logq[0].ondisplay) == 'function')
        logq[0].ondisplay();
    var slice = [];
    for (var i = 1; i < logq.length; i++)
        slice[i-1] = logq[i];
    logq = slice;
    if (logq.length > 10) {
        nofade = true;
        ucounter = 0;
    } else if (logq.length > 4) {
        ucounter = 2;
    } else {
        ucounter = 4;
    }
    $('logsdiv').spyNoFade = nofade;
}

function logUpdate() {
    if (!logon) return;
    /* Pending request watchdog */
    if (pendingreq) {
        pendingclicks++;
        if (pendingclicks == 100) {
            pendingreq = false;
            pendingclicks = 0;
        }
    } else {
        pendingclicks = 0;
    }
    /* Update */
    if (ucounter == 0) {
        if (logq.length) {
            logDisplayEntry();
        }
        var displaymax=50;
        while(logq.length > 15 && displaymax--)
            logDisplayEntry();
        if (logq.length >= 50) {
            logShowProgressBar('rendering','');
            logUpdateProgressBar('rendering',"Rendering data, "+logq.length+" entries left");
        } else {
            logHideProgressBar('rendering');
        }
    } else {
        ucounter--;
    }
    /* Request */
    if (rcounter == 0) {
        if (!pendingreq) {
            pendingreq = true;
            $('activityled').src='/images/led-red-on.gif';
            mfxGetRand('/poll.php?minid='+nextid+"&proto=2",logReqHandler);
            rcounter = reqclicks;
        }
    } else {
        rcounter--;
    }
}

function logReqHandler(r) {
    pendingreq = false;
    logHideProgressBar('loadingdata');
    setTimeout(function() {
        $('activityled').src='/images/led-red-off.gif';
    },100);
    /* Ignore this request in case of a logReset() call with a pending request
     * running */
    if (ignorenextreq) {
        ignorenextreq = 0;
        return;
    }
    if (r == "NOAUTH") {
        mfxSpydivPush($('logsdiv'),'loginnerdiv','<b style="color:red;">You are no longer logged in</b>');
        reqclicks = 1000;
        return;
    } else if (r == "NODATA") {
        if (reqclicks < 20) reqclicks += 5;
        return;
    } else if (r.indexOf("TRYLATER") == 0) {
        var aux = r.split(":");
        var minutes = aux[1];
        logon=0;
        tryLater(minutes);
    } else {
        reqclicks = initialreqclicks;
    }
    var logs = eval(r);
    for (var i = 0; i < logs.length-3; i++) {
        if (!logMatchFilter(logs[i])) continue;
        logs[i].uid = uid++;
        processLog(logs[i]);
        logq[logq.length] = logs[i];
    }
    logRedrawModules();
    nextid = logs[logs.length-3];
    var visitors = logs[logs.length-2];
    var pageviews = logs[logs.length-1];
    $('todayvisitors').innerHTML = visitors.toString();
    $('todaypageviews').innerHTML = pageviews.toString();
}

function htmlentities(s) {
    if (typeof(s) != "string") return "";
    var chars = new Array ('\"','<', '>','©','®');
    var entities = new Array ('quot', 'lt','gt', 'copy','reg');
    for (var i = 0; i < chars.length; i++) {
        while(s.indexOf(chars[i]) != -1)
            s = s.replace (chars[i], '&' + entities[i] + ';');
    }
    return s;
}

function strcutlen(s,len) {
    s=s.toString();
    if (s.length < len) return s;
    return s.substr(0,len)+"...";
}

function logFormatDate(t) {
    var d = new Date;
    var month=["Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","nov","Dec"];
    d.setTime(t*1000);
    var minutes = d.getMinutes();
    var seconds = d.getSeconds();
    if (minutes < 10) minutes = "0"+(minutes.toString());
    if (seconds < 10) seconds = "0"+(seconds.toString());
    return d.getHours()+":"+minutes+":"+seconds+" "+
            month[d.getMonth()]+" "+d.getDate();
}

var logColorHashSbox = [253,10,79,158,52,97,4,170,103,69,108,23,33,18,131,92,60,90,49,94,220,140,36,30,186,77,37,59,32,153,40,177,182,207,212,244,27,64,11,199,114,55,161,221,85,63,2,21,12,72,162,88,213,70,105,255,98,81,159,22,0,194,208,246,93,7,73,187,222,39,80,5,41,54,42,26,126,215,100,195,106,184,152,86,209,129,139,245,35,31,167,176,1,45,75,185,173,164,125,191,146,218,82,3,133,99,196,190,95,229,204,233,144,174,115,250,128,226,210,34,205,66,61,51,120,48,57,38,216,53,24,43,15,156,203,168,151,150,112,198,211,58,169,172,127,134,189,104,65,231,236,237,232,193,143,87,121,180,219,71,178,252,50,76,135,197,228,107,20,200,29,116,130,183,9,96,175,179,192,136,16,78,225,243,117,217,132,84,13,248,227,141,251,110,247,56,149,138,239,235,202,254,181,155,28,223,25,124,62,249,19,165,8,148,119,6,214,89,47,188,234,111,102,74,142,68,109,240,160,123,154,137,201,163,44,113,230,157,242,17,14,224,122,147,46,101,118,145,206,166,241,238,67,91,171,83];
function logColorHash(x) {
    return logColorHashSbox[x%256];
}

function logRef2html(l) {
    html = '';
    if (l.ref.length) {
        var si = l.searchinfo;
        html+=' <a target="_blank" href="'+(l.ref)+'">';
        if (si) {
            html+='<b>'+htmlentities(si.nbrand)+'</b>'+' searching for <b>'+htmlentities(si.query)+"</b>";
        } else {
            html+=htmlentities(l.ref);
        }
        html+='</a>';
    } else {
        html+=htmlentities(' direct url (no referer)');
    }
    return html;
}

function log2html(l) {
    var html='';
    if (l.type == 'pageview' || l.type == 'adclick') {
        var siteroot=getUrlDomain(l.location);
        var uid=l.uid;

        l.ondisplay=function() {
            var img=new Image(16,16);
            img.onload=function() {
                if (img.width != 0)
                    document.getElementById('favicon'+uid).appendChild(img);
            }
            img.src=siteroot+"/favicon.ico";
        }
        
        /* Show history */
        var lasthentry = false;
        if (modGeneral.clients[l.ip]) {
            var h = '';
            var hcount = 0;
            var iph = modGeneral.clients[l.ip];
            for (var j = 0; j < iph.length; j++) {
                var div = $('l'+(iph[j].uid));
                if (div && iph[j].uid < uid &&
                    div.firstChild.className == 'iph')
                {
                    div.firstChild.style.display = 'none';
                }
                if (iph[j].uid >= uid || iph[j].location == lasthentry)
                    continue;
                if (hcount == 3) {
                    h += '</ul>';
                    h += '<a href="#" onclick="mfxToggle(\'hm'+(iph[j].uid)+'\'); return false;">--- more ---</a><br/>';
                    h += '<ul class="iphistory" style="display:none; visibility:hidden;" id="hm'+(iph[j].uid)+'">';
                }
                h += '<li>'+iph[j].location+" &raquo;</li>";
                lasthentry = iph[j].location;
                hcount++;
            }
            if (lasthentry) {
                h = "<li>Originally from "+logRef2html(iph[0])+" &raquo;</li>"+h;
                h = '<div class="iph"><ul class="iphistory">'+h;
                h +='</ul></div>';
                html += h;
            }
        }
        /* Location */
        html+='<span class="location">';
        html+='<a target="_blank" href="'+(l.location)+'">';
        html+=htmlentities(l.location);
        html+='</a></span>';

        if (l.type != 'adclick') {
            /* Referer */
            if (lasthentry && lasthentry == l.ref) {
                html += '<br/>';
            } else {
                if (mfxIsIphone()) html += '<br/>';
                html+=' <span class="referer">from '+logRef2html(l);
                html+='</span><br/>';
            }
        } else {
            html += '<br/>';
        }

        /* Ip & Other info */
        var aux = l.ip.split('.');
        var r = logColorHash(Number(aux[3]));
        var g = logColorHash(Number(aux[2]+1));
        var b = logColorHash((Number(aux[1])+Number(aux[0]))+2);
        b %= 256;
        html+='<input type="button" style="background-color: rgb('+r+','+g+','+b+')" class="ipcolorbox"> ';
        html+='<span class="favicon" id="favicon'+uid+'"></span>';

        html+=' <span class="ipaddr">';
        html+='<a target="_blank" href="http://www.ripe.net/whois?form_type=simple&full_query_string=&searchtext='+mfxEscape(l.ip)+'&do_search=Search">';
        html+=htmlentities(l.ip);
        html+='</a></span>';

        html+=' <span class="country">';
        html+=htmlentities(l.country);
        html+='</span>';

        html+=' <span class="screenres">';
        html+=htmlentities(l.swidth+'x'+l.sheight);
        html+='</span>';

        var i = logGetOsBrowser(l.agent);
        if (i.browser && i.os) {
            html+=' <span class="browser">';
            html+=htmlentities(i.browser);
            if (mfxIsIphone())
                html+='</span> / ';
            else
                html+='</span> running on';
            html+=' <span class="os">';
            html+=htmlentities(i.os);
            html+='</span>';
        }
        if (!mfxIsIphone())
            html+='<br/>';
        else
            html+=' ';

        if (l.type == 'adclick') {
            /* Ad click */
            html += '<div id="adclick">';
            html += '<span style="color:#3962d8">A</span>';
            html += '<span style="color:#d6a900">d</span>';
            html += '<span style="color:#479f51">s</span>';
            html += '<span style="color:#d62408">e</span>';
            html += '<span style="color:#3962d8">n</span>';
            html += '<span style="color:#d6a900">s</span>';
            html += '<span style="color:#479f51">e</span>';
            html += ' click!';
            html += '</div>';
        }

        if (((!i.browser || !i.os) || Options.showuseragent) &&
            !mfxIsIphone())
        {
            html+='<span class="useragent">';
            html+=htmlentities(l.agent);
            html+='</span> ';
            html+='<br/>';
        }
        html+='<span class="date">'+logFormatDate(l.time)+'</span>';
        if (l.historylen > 0 && !mfxIsIphone()) {
            html+=' <span class="historyinfo"> - ';
            html+=htmlentities(String(l.historylen-1))+' urls previously visited in the same session/tab';
            html+='</span> ';
        }
    }
    return html;
}

function logGetOsBrowser(a) {
    var o=false,b=false,m;
    if (m = a.match(/linux[^;)]*/i)) {
        o = m[0];
    } else if (m = a.match(/windows [^;)]*/i)) {
        o = m[0];
        if (mfxIsIphone()) {
            var nt = {
                "NT 5.0": "Windows 2000",
                "NT 5.1\\+": "Windows Fundamentals for Legacy PCs",
                "NT 5.1": "XP",
                "NT 5.2": "XP x64",
                "NT 6.0": "Vista"
            };
        } else {
            var nt = {
                "NT 5.0": "Windows 2000",
                "NT 5.1\\+": "Windows Fundamentals for Legacy PCs",
                "NT 5.1": "Windows XP",
                "NT 5.2": "Windows XP x64",
                "NT 6.0": "Windows Vista"
            };
        }
        var v;
        for (v in nt) {
            if (o.match(v)) o = nt[v];
        }
    } else if (m = a.match(/iPhone/)) {
        o = m[0];
    } else if (m = a.match(/Mac_PowerPC/i)) {
        o = "Mac OS PowerPC";
    } else if (m = a.match(/(PPC |Intel )?Mac OS X/i)) {
        o = m[0];
    } else if (m = a.match(/J2ME.MIDP/i)) {
        o = m[0];
    }
    if (m = a.match(/(MSIE |Chrome\/|Safari\/|Opera\/|Firefox\/|Konqueror\/|SeaMonkey\/|Iceweasel\/)[0-9.]+/i)) {
        b = m[0];
    }
    return {os:o, browser:b};
}

function logParseSearch(l) {
    var m;
    /* Google */
    if (m = l.ref.match(/http:\/\/([^.]+\.)?google(\.[^\/]+)\/(search|custom|m|url|ie).*(&|\?)(q|as_q)=([^&]+)/i))
    {
        return {
            brand: "Google",
            nbrand: "google"+m[2],
            query: mfxUnescape(m[6].replace(/\+/g," ")),
            page: 0
        };
    }
    /* Bing */
    else if (m = l.ref.match(/http:\/\/([^.]+\.)?bing.com\/(search|spresults).*(&|\?)q=([^&]+)/)) {
        return {
            brand: "Bing",
            nbrand: "bing.com",
            query: mfxUnescape(m[4]),
            page: 0
        };
    }
    /* Yahoo */
    else if (m = l.ref.match(/http:\/\/([^.]+\.)?search.yahoo.com\/search.*(&|\?)p=([^&]+)/)) {
        return {
            brand: "Yahoo",
            nbrand: m[1]+"search.yahoo.com",
            query: mfxUnescape(m[3]),
            page: 0
        };
    }
    /* Microsoft Live */
    else if (m = l.ref.match(/http:\/\/search.live.com\/results.aspx.*(&|\?)q=([^&]+)/)) {
        return {
            brand: "MS LIVE",
            nbrand: "search.live.com",
            query: mfxUnescape(m[2]),
            page :0
        };
    }
    /* Alice */
    else if (m = l.ref.match(/http:\/\/search.alice.it\/search\/cgi\/search.cgi.*(&|\?)qs=([^&]+)/)) {
        return {
            brand: "Alice",
            nbrand: "search.alice.it",
            query: mfxUnescape(m[2]),
            page :0
        };
    }
    /* Virgilio */
    // http://ricerca.virgilio.it/ricerca?qs=collettivamente.com&Cerca=&lr=
    else if (m = l.ref.match(/http:\/\/ricerca.virgilio.it\/ricerca.*(&|\?)qs=([^&]+)/)) {
        return {
            brand: "Virgilio",
            nbrand: "ricerca.virgilio.it",
            query: mfxUnescape(m[2]),
            page :0
        };
    }
    /* Arianna */
    else if (m = l.ref.match(/http:\/\/arianna.libero.it\/search\/abin\/integrata.cgi.*(&|\?)query=([^&]+)/)) {
        return {
            brand: "Arianna",
            nbrand: "arianna.libero.it",
            query: mfxUnescape(m[2]),
            page :0
        };
    }
    /* MSN */
    else if (m = l.ref.match(/http:\/\/search.msn.([^.]+)\/results.asp.*(&|\?)q=([^&]+)/)) {
        return {
            brand: "MSN",
            nbrand: "search.msn."+m[1],
            query: mfxUnescape(m[3]),
            page :0
        };
    }
    /* Just a normal referer */
    else {
        return false;
    }
}

function logToggle() {
    if (logon == 0) {
        $('togglebut').src="/images/stop.gif";
        $('togglebut').title="Pause";
        logon = 1;
    } else {
        $('togglebut').src="/images/start.gif";
        $('togglebut').title="Play";
        logon = 0;
    }
}

function logSetNumItems() {
    var s = $('numitems');
    var numitems = s.options[s.selectedIndex].value;
    $('logsdiv').spyMaxLen = numitems;
    mfxSpydivClear($('logsdiv'),numitems);
}

function logGetNumItems() {
    var s = $('numitems');
    return s.options[s.selectedIndex].value;
}

function logClear() {
    mfxSpydivClear($('logsdiv'),1);
}

function logSetNoFx() {
    if ($('nofx').checked) {
        $('logsdiv').spyNoFade = true;
    } else {
        $('logsdiv').spyNoFade = undefined;
    }
}

function logSaveConfig() {
    if (!isdef(logcansave)) return;
    var a = ["numitems"];
    mfxSaveInputsInCookie("displaycfg",a);
}

function logRestoreConfig() {
    mfxRestoreInputsFromCookie("displaycfg");
}

function processLog(log) {
    log.searchinfo = logParseSearch(log);
    for (var j = 0; j < statmodules.length; j++) {
        var module = statmodules[j];
        module.process(log);
    }
}

function logRedrawModules() {
    for (var j = 0; j < statmodules.length; j++) {
        var module = statmodules[j];
        if (module.dirty) {
            module.redraw($('statbox_'+module.name));
            module.dirty = false;
        }
    }
}

function SortableHash() {
    /* We have two views for our data. As an hash table
       and as an array. */
    function setMethod(key,val) {
        if (typeof(this.h[key]) == 'undefined') {
            var valobj = {key: key, value: val};
            this.h[key] = valobj;
            this.a[this.a.length] = valobj;
            return valobj;
        } else {
            return false;
        }
    }
    function getMethod(key) {
        return (typeof(this.h[key]) == 'undefined') ? false : this.h[key];
    }
    function sortbyMethod(f) {
        this.a.sort(f);
        return this.a;
    }
    this.h = {};
    this.a = [];
    this.set = setMethod;
    this.get = getMethod;
    this.sortby = sortbyMethod;
}

function getUrlDomain(url) {
    var m = url.match("[a-zA-Z]+:\\/\\/([^\\/]*)");
    return m ? m[1] : "";
}

/* Modules */
var modGeneral = {
    name: "general",
    dirty: false,
    enabled: true,
    visible: true
};

var modTopReferers = {
    name: "topreferers",
    dirty: false,
    enabled: true,
    visible: true
};

var modTopSearches = {
    name: "topsearches",
    dirty: false,
    enabled: true,
    visible: true
};

var modTopPages = {
    name: "toppages",
    dirty: false,
    enabled: true,
    visible: true
};

var modTopBrowsers = {
    name: "topbrowsers",
    dirty: false,
    enabled: true,
    visible: true
};

var statmodules = [modGeneral,modTopReferers,modTopSearches,modTopPages,modTopBrowsers];

/* Modules initialization, also used to reset modules for
 * server-side data loading */
function logInitModules() {
    for (var j=0; j<statmodules.length; j++) {
        var m = statmodules[j];
        m.init(m);
    }
}

function logReset() {
    logInitModules();
    mfxSpydivClear($('logsdiv'),0);
    reqclicks = initialreqclicks;
    uid = 0;
    nextid = 0;
    ucounter = rcounter = 0;
    logq = [];
    /* We want to ignore the next request if there is already a pending request
     * and LLOOGG must be reset */
    if (pendingreq)
        ignorenextreq = 1;
}

function logLoadHistory() {
    logReset();
    nextid = -(logGetNumItems());
    logShowProgressBar('loadingdata','Loading...');
}

pbardiv={};
pbarcount=0;
function logShowProgressBar(name,msg) {
    if (logIsActiveProgressBar(name)) return;
    var div = document.createElement('div');
    div.className = 'progressbar';
    div.style.position = 'absolute';
    div.style.left = '200px';
    div.style.top = (200+pbarcount*25)+'px';
    div.innerHTML = msg;
    document.body.appendChild(div);
    pbardiv[name] = div;
    pbarcount++;
}

function logUpdateProgressBar(name,msg) {
    if (!logIsActiveProgressBar(name)) return;
    pbardiv[name].innerHTML=msg;
}

function logHideProgressBar(name) {
    if (!logIsActiveProgressBar(name)) return;
    try {
        document.body.removeChild(pbardiv[name]);
        delete(pbardiv[name]);
    } catch(e) {}
    pbardiv[name]=false;
    pbarcount--;
}

function logIsActiveProgressBar(name) {
    if (typeof(pbardiv[name]) == 'undefined') return false;
    return pbardiv[name] !== false;
}

/*------------------------------------------------------------------------------
 * General Module
 *----------------------------------------------------------------------------*/
modGeneral.init = function(m) {
    m.starttime = (new Date).getTime();
    m.pageviews = 0;
    m.visitors = 0;
    m.clients = {};
}

modGeneral.process = function(log) {
    modGeneral.dirty = true;
    modGeneral.pageviews++;
    /* XXX: FIXME! clients should be uniquely indentified by
       ip+agent+target_site! The same IP visiting foo.org and bar.org
       shoud actually count for two different visits. */
    if (!modGeneral.clients[log.ip]) {
        modGeneral.clients[log.ip] = [];
        modGeneral.visitors++;
        modGeneral.newvisit = true;
    } else {
        modGeneral.newvisit = false;
    }
    var a = modGeneral.clients[log.ip];
    a[a.length] = log;
};

modGeneral.redraw = function(div) {
    div.innerHTML = '';
    var html = 'Pageviews: '+modGeneral.pageviews+'<br/>';
    html += 'Unique visitors: '+modGeneral.visitors+'<br/>';
    div.innerHTML = html;
};
 
/*------------------------------------------------------------------------------
 * TopReferers Module
 *----------------------------------------------------------------------------*/
modTopReferers.init = function(m) {
    m.sh = new SortableHash();
    m.lowerbound = 0;
}

modTopReferers.process = function(log) {
    var m = modTopReferers;
    var sh = m.sh;

    if (!modGeneral.newvisit) return;
    if (!log.ref.length) return;
    /* We are not interested in internal referers here */
    if (getUrlDomain(log.ref) == getUrlDomain(log.location)) return;
    if (log.searchinfo) {
        var key = log.searchinfo.nbrand;
        var name = key;
        var link = 'http://'+key;
    } else {
        var key = log.ref;
        var name = getUrlDomain(key);
        if (name.length == 0) name=key;
        var link = key;
    }
    var v = sh.get(key);
    if (!v) {
        var entry = {};
        entry.count = 0;
        entry.name = name;
        entry.link = link;
        v = sh.set(key,entry);
    }
    v.value.count++;
    if (v.value.count >= modTopReferers.lowerbound) m.dirty = true;
};

function logTopGenericRedraw(module,div) {
    var sh = module.sh;
    var a = sh.sortby(function(a,b) {
        return b.value.count-a.value.count;
    });
    var html = "";
    module.lowerbound = 0;
    for (j = 0; j < 15; j++) {
        if (a.length == j) break;
        var count = a[j].value.count;
        var link = a[j].value.link;
        if (link.toString().length==0) link="#";
        var name = a[j].value.name;
        if (mfxIsExplorer()) name = strcutlen(name,30);
        html += '<a target="_blank" href="'+htmlentities(link)+'">'+htmlentities(name)+' ('+count+')</a><br/>';
        if (module.lowerbound == 0 ||
            module.lowerbound > count)
            module.lowerbound = count;
    }
    div.innerHTML = html;
}

modTopReferers.redraw = function(div) {
    logTopGenericRedraw(modTopReferers,div);
};

/*------------------------------------------------------------------------------
 * TopSearches Module
 *----------------------------------------------------------------------------*/
modTopSearches.init = modTopReferers.init;

modTopSearches.process = function(log) {
    var m = modTopSearches;
    var sh = m.sh;

    if (!modGeneral.newvisit) return;
    if (!log.searchinfo) return;
    var key = log.searchinfo.brand+": "+log.searchinfo.query;
    var v = sh.get(key);
    if (!v) {
        var entry = {};
        entry.count = 0;
        entry.name = key;
        entry.link = log.ref;
        v = sh.set(key,entry);
    }
    v.value.count++;
    if (v.value.count >= modTopSearches.lowerbound) m.dirty = true;
};

modTopSearches.redraw = function(div) {
    logTopGenericRedraw(modTopSearches,div);
};

/*------------------------------------------------------------------------------
 * TopPages Module
 *----------------------------------------------------------------------------*/
modTopPages.init = modTopReferers.init;

modTopPages.process = function(log) {
    var m = modTopPages;
    var sh = m.sh;

    var key = log.location;
    var v = sh.get(key);
    if (!v) {
        var entry = {};
        entry.count = 0;
        entry.name = key;
        entry.link = log.location;
        v = sh.set(key,entry);
    }
    v.value.count++;
    if (v.value.count >= modTopPages.lowerbound) m.dirty = true;
};

modTopPages.redraw = function(div) {
    logTopGenericRedraw(modTopPages,div);
};

/*------------------------------------------------------------------------------
 * TopBrowsers Module
 *----------------------------------------------------------------------------*/
modTopBrowsers.init = modTopReferers.init;

modTopBrowsers.process = function(log) {
    var m = modTopBrowsers;
    var sh = m.sh;

    if (!modGeneral.newvisit) return;
    var ob = logGetOsBrowser(log.agent);
    if (!ob.browser) return;
    var obs = ob.browser.split(".");
    ob.browser = obs[0];
    var key = ob.browser;
    var v = sh.get(key);
    if (!v) {
        var entry = {};
        entry.count = 0;
        entry.name = key;
        entry.link = '';
        v = sh.set(key,entry);
    }
    v.value.count++;
    if (v.value.count >= m.lowerbound) m.dirty = true;
};

modTopBrowsers.redraw = function(div) {
    logTopGenericRedraw(modTopBrowsers,div);
};

/*------------------------------------------------------------------------------
 * Trends
 *----------------------------------------------------------------------------*/
function logShowTrends() {
    mfxToggle('uimain');
    mfxToggle('trendmain');
    html="";
    html += '<a style="font-weight:normal;" href="#" onclick="logHideTrends(); return false;">Back to realtime logs &raquo;</a><br/>';
    html += '<iframe frameborder="0" style="border:none" src="/trendsframe.php" width="100%" height="700"></iframe>';
    $('trendmain').innerHTML = html;
}

function logHideTrends() {
    mfxToggle('trendmain');
    mfxToggle('uimain');
    $('trendmain').innerHTML = '';
}

/*------------------------------------------------------------------------------
 * Filters
 *----------------------------------------------------------------------------*/
function logToggleFilters() {
    var state = mfxToggle('filters');
    if (state == "visible") {
        $('openfilters').innerHTML = 'close filters';
        mfxHide('filterwarn');
    } else {
        $('openfilters').innerHTML = 'open filters';
        if (logFilterIsSet()) mfxShow('filterwarn');
    }
}

function logFilterMatchString(str,keywords) {
    var ka = keywords.split(' ');
    var j;

    if (keywords.length == 0) return true;
    for (j = 0; j < ka.length; j++) {
        if (ka[j].length == 0) continue;
        if (str.indexOf(ka[j]) == -1) return false;
    }
    return true;
}

function logMatchFilter(log) {
    if (document.f.filteronlyfirst.checked && modGeneral.clients[log.ip]) return false;
    var res = true;
    if (!logFilterMatchString(log.location,document.f.filterurl.value)) res = false;
    if (!logFilterMatchString(log.ref,document.f.filterref.value)) res = false;
    var ob = logGetOsBrowser(log.agent);
    if (document.f.filterbrowser.value != "") {
        if (ob.browser == false || ob.browser.toLowerCase().indexOf(document.f.filterbrowser.value) == -1) res = false;
    }
    if (document.f.filteros.value != "") {
        if (ob.os == false || ob.os.toLowerCase().indexOf(document.f.filteros.value) == -1) res = false;
    }
    if (document.f.filterinvert.checked) {
        return !res;
    } else {
        return res;
    }
}

function logFilterIsSet() {
    if (document.f.filterurl.value != "" ||
        document.f.filterref.value != "" ||
        document.f.filterbrowser.value != "" ||
        document.f.filteros.value != "" ||
        document.f.filteronlyfirst.checked ||
        document.f.filterinvert.checked)
        return true;
    return false;
}

function tryLater(minutes) {
    var div = document.createElement('div');
    div.className = 'trylater';
    div.innerHTML = "<h2>This is a free account!</h2> Free accounts are limited to 5 minutes of real time logs every 15 minutes of time. Try reloading the page in "+minutes+" minutes.<br><br><b><a href=\"/pro\">UPGRADE TO PRO ACCOUNT FOR JUST 30$/year</a></b> to view real time logs for as long as you like. Also a 10000 lines history length and other goodies are included in the free account. <a href=\"/pro\">Learn more about the PRO account</a>.<br><br><div style=\"text-align:center\"><a href=\"/\">RETRY NOW</a></div>";
    document.body.appendChild(div);
}

/* -------------------------------------------------------------------------- */

logInitModules();
/* logcansave is used to protect the restore process
  (that is designed to fire onclick and onchange events)
  from saving the configuration while it is going to be
  restored. */
logRestoreConfig();
var logcansave = 1;
logSetNumItems();
//logSetNoFx();
$('togglebut').onclick = logToggle;
$('clearbut').onclick = logClear;
$('numitems').onchange = function() {
    logSetNumItems();
    logSaveConfig();
}

mfxEvery(500,logUpdate);
