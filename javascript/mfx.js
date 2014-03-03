// Javascript library for common web stuff written back in 2007.
// Should be replaced with something mordern ASAP probably... and put on fire.
//
// Copyright (C) 2007 Salvatore Sanfilippo (antirez at gmail dot com)

/* =============================================================================
 * UTILS
 * ========================================================================== */

/* Just a less verbose way to getElementById() */
function $(id) {
    if (typeof(id) == 'string')
        return document.getElementById(id);
    return id;
}

/* Return the innerHTML of the element with ID 'id' */
function $html(id) {
    id=$(id);
    return id.getElementById(id).innerHTML;
}

/* Set the innerHTML of th element with ID 'id' */
function $sethtml(id,html) {
    id=$(id);
    id.innerHTML = html;
}

/* Append HTML to innerHTML of element with ID 'id' */
function $apphtml(id,html) {
    id=$(id);
    id.innerHTML += html;
}

/* Handy way to test if typeof(o) is 'undefined' */
function isdef(o) {
    return typeof(o) != 'undefined';
}

/* encodeURIComponent() working with IE5.0 */
function mfxEscape(s) {
    try {
        return encodeURIComponent(s);
    } catch(e) {
        var e = escape(s);
        e = e.replace(/@/g,"%40");
        e = e.replace(/\//g,"%2f");
        e = e.replace(/\+/g,"%2b");
        return e;
    }
}

/* decodeURIComponent() working with IE5.0 */
function mfxUnescape(s) {
    try {
        s = s.replace(/\+/g,"%20");
        return decodeURIComponent(s);
    } catch(e) {
        var s = unescape(s);
        s = s.replace(/\+/g," ");
        return s;
    }
}

/* mfxGetUrlParam("http://www.google.com?foo=bar","foo") => "bar" */
function mfxGetUrlParam(url,name) {
    var re="(&|\\?)"+name+"=([^&]*)";
    if (m = url.match(re)) return mfxUnescape(m[2]);
    return false;
}

/* Preform an action every N milliseconds */
function mfxEvery(milliseconds,handler) {
    if (handler() !== false)
        setTimeout(function() {
            mfxEvery(milliseconds,handler);
        },milliseconds);
}

function mfxGetElementsByTagClass(tag,cname) {
    if (!document.getElementsByTagName) return [];
    var el = document.getElementsByTagName(tag);
    var res = [];
    for (var i = 0; i < el.length; i++) {
        var aux = ' '+el[i].className+' ';
        if (!cname || aux.indexOf(cname) != -1) {
            res[res.length] = el[i];
        }
    }
    return res;
}

function mfxGetElementsByClass(cname) {
    return mfxGetElementsByTagClass('*',cname);
}

function mfxSaveStyle(o,pname,defvalue) {
    o = $(o);
    if (!isdef(o.mfxSavedStyle)) o.mfxSavedStyle = {};
    var value = o.style[pname];
    if (!isdef(value)) value=defvalue;
    o.mfxSavedStyle[pname]=value;
}

function mfxRestoreStyle(o,pname) {
    o = $(o);
    if (!isdef(o.mfxSavedStyle) || !isdef(o.mfxSavedStyle[pname])) return;
    o.style[pname] = o.mfxSavedStyle[pname];
    delete o.mfxSavedStyle[pname];
}

function mfxMap(o,f) {
    var res = [];
    for(var i = 0; i < o.length; i++)
        res[res.length] = f(o[i]);
    return res;
}

/* =============================================================================
 * JSON
 * ========================================================================== */
function mfxJson(o) {
    if (typeof(o) == 'boolean') return String(o);
    if (typeof(o) == 'number') return String(o);
    if (typeof(o) == 'string') return mfxJsonString(o);
    if (typeof(o) == 'object') return mfxJsonArray(o);
    if (typeof(o) == 'undefined') return "undefined";
    return undefined;
}

/* The string to json conversion is taken from json.org */
function mfxJsonString(s) {
    if (typeof(s) != 'string') s = String(s);
    var m = {   '\b': '\\b', '\t': '\\t', '\n': '\\n', '\f': '\\f',
                '\r': '\\r', '"' : '\\"', '\\': '\\\\' };
    if (/["\\\x00-\x1f]/.test(s)) {
        return '"' + s.replace(/([\x00-\x1f\\"])/g, function(a, b) {
            var c = m[b];
            if (c) {
                return c;
            }
            c = b.charCodeAt();
            return '\\u00' +
                Math.floor(c / 16).toString(16) +
                (c % 16).toString(16);
        }) + '"';
    }
    return '"'+s+'"';
}

function mfxJsonArray(a) {
    var s = "[";
    for (var j = 0; j < a.length; j++) {
        s += mfxJson(a[j]);
        if (j != a.length-1) s += ",";
    }
    s += ']';
    return s;
}

/* =============================================================================
 * COOKIES
 * ========================================================================== */
function mfxSetCookie(name,value,expires,path,domain,secure)
{
    document.cookie= name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires.toGMTString() : "") +
        ((path) ? "; path=" + path : "") +
        ((domain) ? "; domain=" + domain : "") +
        ((secure) ? "; secure" : "");
}

function mfxGetCookie(name)
{
    var dc = document.cookie;
    var prefix = name + "=";
    var begin = dc.indexOf("; " + prefix);
    if (begin == -1) {
        begin = dc.indexOf(prefix);
        if (begin != 0) return null;
    } else {
        begin += 2;
    }
    var end = document.cookie.indexOf(";", begin);
    if (end == -1) end = dc.length;
    return unescape(dc.substring(begin + prefix.length, end));
}

function mfxDelCookie(name,path,domain)
{
    if (getCookie(name)) {
        document.cookie = name + "=" + 
            ((path) ? "; path=" + path : "") +
            ((domain) ? "; domain=" + domain : "") +
            "; expires=Thu, 01-Jan-70 00:00:01 GMT";
    }
}

/* =============================================================================
 * FORMS
 * ========================================================================== */

function mfxGetInput(i) {
    i = $(i);
    if (isdef(i.type)) {
        if (i.type == 'text' || i.type == 'password') {
            return i.value;
        } else if (i.type == 'select-one') {
            return String(i.selectedIndex);
        } else if (i.type == 'checkbox') {
            if (i.checked == true) return "1";
            return "0";
        }
    }
}

function mfxSetInput(i,v) {
    i = $(i);
    if (isdef(i.type)) {
        if (i.type == 'text' || i.type == 'password') {
            i.value = v;
            if (typeof(i.onchange) == 'function') i.onchange();
        } else if (i.type == 'select-one') {
            i.selectedIndex = Number(v);
            if (typeof(i.onchange) == 'function') i.onchange();
        } else if (i.type == 'checkbox') {
            if ((Number(v) == true && i.checked == false) ||
                (Number(v) == false && i.checked == true))
            i.click();
        }
    }
}

function mfxSaveInputs(idlist) {
    var a = [];
    for (var i = 0; i < idlist.length; i++) {
        a[a.length] = idlist[i];
        a[a.length] = mfxGetInput(idlist[i]);
    }
    return a;
}

function mfxRestoreInputs(a) {
    for (var i = 0; i < a.length; i += 2)
        mfxSetInput(a[i],a[i+1]);
}

function mfxSaveInputsInString(idlist) {
    return mfxJson(mfxSaveInputs(idlist));
}

function mfxRestoreInputsFromString(s) {
    mfxRestoreInputs(eval(s));
}

function mfxSaveInputsInCookie(cookiename,idlist) {
    var s = mfxSaveInputsInString(idlist);
    var now = new Date;
    t = now.getTime();
    now.setTime(t+(3600*24*1000*1000));
    mfxSetCookie(cookiename,s,now);
}

function mfxRestoreInputsFromCookie(cookiename) {
    var c = mfxGetCookie(cookiename);
    if (c == null) return;
    mfxRestoreInputsFromString(c);
}

/* =============================================================================
 * BROWSER detection
 * ========================================================================== */

/* Browser detection is uncool, but sometimes to test
 * for features is impossible */
function mfxIsGecko() {
    if (mfxIsKonqueror()) return false;
    return navigator.userAgent.toLowerCase().indexOf("gecko") != -1;
}

function mfxIsExplorer() {
    if (isdef(window.opera)) return false;
    return navigator.userAgent.toLowerCase().indexOf("msie") != -1;
}

function mfxIsOpera() {
    return isdef(window.opera);
}

function mfxIsSafari() {
    return  isdef(navigator.vendor) &&
            navigator.vendor.toLowerCase().indexOf("apple") != -1;
}

function mfxIsKonqueror() {
    return  isdef(navigator.vendor) &&
            navigator.vendor.indexOf("KDE") != -1;
}

function mfxIsIphone() {
    return  isdef(navigator.vendor) && isdef(navigator.userAgent) &&
            navigator.vendor.toLowerCase().indexOf("apple") != -1 &&
            navigator.userAgent.toLowerCase().indexOf("iphone") != -1;
}

/* =============================================================================
 * AJAX
 * ========================================================================== */

/* Browser compatibilty.
 * Tested with:
 *
 * Firefox 1.0 to 1.5
 * Konqueror 3.4.2
 * Internet Explorer 5.0
 * internet Explorer 6.0
 * internet Explorer 7.0
 *
 * It should work also in Opera and Safari without troubles. */

// Create the XML HTTP request object. We try to be
// more cross-browser as possible.
function mfxCreateXmlHttpReq(handler) {
  var xmlhttp = null;
  try {
    xmlhttp = new XMLHttpRequest();
  } catch(e) {
    try {
        xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
    } catch(e) {
        xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
    }
  }
  xmlhttp.onreadystatechange = handler;
  return xmlhttp;
}

// An handler that does nothing, used for AJAX requests that
// don't require a reply and are non-critical about error conditions.
function mfxDummyHandler() {
    return true;
}

// Shortcut for creating a GET request and get the reply
// This few lines of code can make Ajax stuff much more trivial
// to write, and... to avoid patterns in programs is sane!
function mfxGet(url,handler) {
    var a = new Array("placeholder");
    for (var j=2; j<arguments.length; j++) {
        a[a.length] = arguments[j];
    }
    var ajax_req = mfxCreateXmlHttpReq(mfxDummyHandler);
    var myhandler = function() {
        var content = mfxAjaxOk(ajax_req);
        if (content !== false) {
            a[0] = content;
            if (handler.apply) {
                return handler.apply(this, a);
            } else {
                return mfxApply(handler, a);
            }
        }
    }
    ajax_req.onreadystatechange = myhandler;
    ajax_req.open("GET",url);
    ajax_req.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
    ajax_req.send(null);
}

// IE 5.0 does not support the apply() method of the function object,
// we resort to this eval-based solution that sucks because it is not
// capable of preserving 'this' and is ugly as hell, but it works for us.
function mfxApply(funcname,args) {
    var e = "funcname(";
    for (var i = 0; i < args.length; i++) {
        e += "args["+i+"]";
        if (i+1 != args.length) {
            e += ",";
        }
    }
    e += ");"
    return eval(e);
}

// Add a random parameter to the get request to avoid
// IE caching madness.
function mfxGetRand(url,handler) {
    url += (url.indexOf("?") == -1) ? "?" : "&";
    url += "rand="+escape(Math.random());
    arguments[0] = url;
    if (mfxGet.apply) {
        return mfxGet.apply(this,arguments);
    } else {
        return mfxApply(mfxGet,arguments);
    }
}

function mfxAjaxOk(req) {
    if (req.readyState == 4 && req.status == 200) {
        return req.responseText;
    } else {
        return false;
    }
}

/* =============================================================================
 * POSITIONING
 * ========================================================================== */

function mfxGetElementSize(ele) {
    ele=$(ele);
    var o = {};
    if (isdef(ele.offsetHeight)) {
        /* IE ... */
        o.width = ele.offsetWidth;
        o.height = ele.offsetHeight;
    } else {
        /* W3C way, supported by Gecko */
        try {
            o.width = document.defaultView.getComputedStyle(ele,"").getPropertyValue("width");
            o.height = document.defaultView.getComputedStyle(ele,"").getPropertyValue("height");
        } catch(e) {
            o = false;
        }
    }
    return o;
}

/* INPUT -- e: the event object */
function mfxGetMousePos(e) {
    if (!e) e = window.event;
    var pos = {};
    if (isdef(e.pageX)) {
        pos.x = e.pageX;
        pos.y = e.pageY;
    } else {
        pos.x = e.clientX;
        pos.y = e.clientY;
    	pos.x += document.body.scrollLeft+document.documentElement.scrollLeft;
	pos.y += document.body.scrollTop+document.documentElement.scrollTop;
    }
    return pos;
}

function mfxMoveTo(e,x,y) {
    e=$(e);
    if (e.style.position != 'absolute')
        e.style.position = 'absolute';
    e.style.left = x+'px';
    e.style.top = y+'px';
}

function mfxDelete(e) {
    e=$(e);
    e.parentNode.removeChild(e);
}

function mfxFindPosition(e) {
    e = $(e);
    var curleft = curtop = 0;
    if (e.offsetParent) {
        curleft = e.offsetLeft
        curtop = e.offsetTop
        while (e = e.offsetParent) {
            curleft += e.offsetLeft
            curtop += e.offsetTop
        }
    }
    return {x: curleft, y: curtop};
}

/* =============================================================================
 * DRAG & DROP
 * ========================================================================== */
function mfxDragEnable(e) {
    e = $(e);
    e.mfxcandrag = true;
    e.onmousedown = mfxDragOrResizeStart;
}

function mfxDragDisable(e) {
    e = $(e);
    e.mfxcandrag = undefined;
    if (!isdef(e.mfxcanresize)) {
        /* We can disable the onmousedown event handler
           only if it is not used by the resize code */
        e.onmousedown = null;
    }
}

/* The onmousedown event is shared between drag&drop and resize features */
function mfxDragOrResizeStart(e) {
    var o = this;
    if (isdef(o.mfxcanresize)) {
        if (mfxResizeStart(e,o)) return false;
    }
    if (isdef(o.mfxcandrag)) {
        mfxDragStart(e,o);
    }
    return false;
}

function mfxDragStart(e,o) {
    /* Check if the object already has top/left, otherwise
     * try to find it and set it */
    var opos = {};
    opos.x = parseInt(o.style.left);
    opos.y = parseInt(o.style.top);
    if (isNaN(opos.x) || isNaN(opos.y)) {
        var realpos = mfxFindPosition(o);
        o.style.position = 'absolute';
        o.style.left = realpos.x+'px';
        o.style.top = realpos.y+'px';
    }
    /* Initialization stuff, the real work is done in mfxDragMove() */
    var pos = mfxGetMousePos(e);
    o.dragLastX = pos.x;
    o.dragLastY = pos.y;
    document.onmousemove = function(e) {
        mfxDragMove(e,o);
    };
    document.onmouseup = function(e) {
        mfxDragStop(e,o);
    }
    if (typeof(o.ondragstart) == 'function')
        o.ondragstart(e,o);
    mfxSaveStyle(o,'zIndex','0');
    o.style.zIndex = '1000';
}

function mfxDragMove(e,o) {
    var mpos = mfxGetMousePos(e);
    var opos = {};
    opos.x = parseInt(o.style.left);
    opos.y = parseInt(o.style.top);
    var dx = mpos.x-o.dragLastX;
    var dy = mpos.y-o.dragLastY;
    o.dragLastX = mpos.x;
    o.dragLastY = mpos.y;
    o.style.top = (opos.y+dy)+'px';
    o.style.left = (opos.x+dx)+'px';
    if (typeof(o.ondrag) == 'function')
        o.ondrag(o);
    return false;
}

function mfxDragStop(e,o) {
    document.onmousemove = null;
    document.onmouseup = null;
    if (typeof(o.ondragstop) == 'function')
        o.ondragstop(e,o);
    mfxRestoreStyle(o,'zIndex');
}

/* =============================================================================
 * RESIZE
 * ========================================================================== */
function mfxResizeEnable(e) {
    e = $(e);
    e.mfxcanresize = true;
    e.onmousedown = mfxDragOrResizeStart;
}

function mfxResizeDisable(e) {
    e = $(e);
    e.mfxcanresize = undefined;
    if (!isdef(e.mfxcandrag)) {
        /* We can disable the onmousedown event handler
           only if it is not used by the drag&drop code */
        e.onmousedown = null;
    }
}

function mfxResizeStart(e,o) {
    var mpos = mfxGetMousePos(e);
    var opos = mfxFindPosition(o);
    var osize = mfxGetElementSize(o);
    var corner = {x: opos.x+osize.width, y: opos.y+osize.height};
    var delta = 8;
    /* Check if the object is clicked in the right-bottom angle,
       otherwise return false. */
    if (mpos.x >= corner.x-delta && mpos.x <= corner.x &&
        mpos.y >= corner.y-delta && mpos.y <= corner.y) {
        /* Ok, setup the resize operation */
        o.resizeLastX = mpos.x;
        o.resizeLastY = mpos.y;
        o.resizeWidth = osize.width;
        o.resizeHeight = osize.height;
        document.onmousemove = function(e) {
            mfxResizeMove(e,o);
        };
        document.onmouseup = function(e) {
            mfxResizeStop(e,o);
        }
        if (typeof(o.onresizestart) == 'function')
            o.onresizestart(e,o);
        mfxSaveStyle(o,'zIndex','0');
        o.style.zIndex = '1000';
        return true;
    } else {
        return false;
    }
}

function mfxResizeMove(e,o) {
    var mpos = mfxGetMousePos(e);
    var dx = mpos.x-o.resizeLastX;
    var dy = mpos.y-o.resizeLastY;
    o.resizeLastX = mpos.x;
    o.resizeLastY = mpos.y;
    o.resizeWidth += dx;
    o.resizeHeight += dy;
    o.style.width = (o.resizeWidth)+'px';
    o.style.height = (o.resizeHeight)+'px';
    if (typeof(o.onresize) == 'function')
        o.onresize(o);
    return false;
}

function mfxResizeStop(e,o) {
    document.onmousemove = null;
    document.onmouseup = null;
    if (typeof(o.onresizestop) == 'function')
        o.onresizestop(e,o);
    mfxRestoreStyle(o,'zIndex');
}

/* =============================================================================
 * CLICKTIPS - balloon-style helps on click or hover
 * ========================================================================== */
function registerClicktip(e,_text) {
    e.clicktipActive = false;
    e.onclick = function (event) {
        if (!event) var event = window.event;
        return handleClicktip(event,_text,e);
    };
}

function registerClicktipId(id,text) {
    var e = document.getElementById(id);
    if (e) {
        registerClicktip(e,text);
    } else {
        alert("Clicktip error: no such element ID '"+id+"'");
    }
}

/* Set a clicktip to all the elements of a given type/class */
function registerClicktipBulk(type,classname,text) {
    var i;
    var e = mfxGetElementsByTagClass(type,classname);
    for (i = 0; i < e.length; i++)
        registerClicktip(e[i], text);
}

function registerOvertip(e,_text,showdelay,hidedelay) {
    e.clicktipActive = false;
    e.clicktipTimeout = false;
    e.onmouseover = function (event) {
        if (!event) var event = window.event;
        return handleOvertipOver(e,event,_text,showdelay,hidedelay);
    };
    e.onmouseout = function (event) {
        if (!event) var event = window.event;
        return handleOvertipOut(e,event,_text,showdelay,hidedelay);
    };
}

function registerOvertipId(id,text,showdelay,hidedelay) {
    var e = document.getElementById(id);
    if (e) {
        registerOvertip(e,text,showdelay,hidedelay);
    } else {
        alert("Clicktip error: no such element ID '"+id+"'");
    }
}

/* Set an overtip to all the elements of a given type/class */
function registerOvertipBulk(type,classname,text,showdelay,hidedelay) {
    if (isdef(document.getElementsByTagName)) {
        var e = document.getElementsByTagName(type);
        var i;
        for (i = 0; i < e.length; i++) {
            if (!classname || e[i].className == classname) {
                registerOvertip(e[i],text,showdelay,hidedelay);
            }
        }
    }
}

function delTip(div) {
    try {
        try { clearTimeout(div.clicktipTarget.clicktipTimeout); } catch(e) {};
        try { div.clicktipTarget.clicktipTimeout = false; } catch(e) {};
        try { div.clicktipTarget.clicktipActive = false; } catch(e) {};
        document.body.removeChild(div);
        delete(div);
    } catch(e) {};
}

function delTipOnClick() {
    delTip(this);
}

function createTipDiv(x,y,text,target) {
    /* Show a DIV with the right message */
    var div = document.createElement('div');
    div.className = 'clicktip';
    div.style.visibility = 'hidden';
    div.style.position = 'absolute';
    div.style.left = x+"px";
    div.style.top = y+"px";
    /* We set the DIV content usign innerHTML,
       If you are a purist append a text node instead ;) */
    div.innerHTML = text;

    /* When the clicktip gets clicked we hide it */
    div.clicktipTarget = target;
    div.onclick = delTipOnClick;
    document.body.appendChild(div);

    /* Try to fix the 'top' in order to display the div just over the pointer */
    var divsize = mfxGetElementSize(div);
    div.clicktipXDelta = 0;
    div.clicktipYDelta = 0;
    if (divsize) {
        /* Check if there is space on top to display the clicktip */
        if (divsize.height < y) {
            div.clicktipYDelta = -(divsize.height+2);
            div.clicktipXDelta = 2;
        } else {
            div.clicktipXDelta = 2;
            /* No space on top, display the tip on the bottom, i.e.
               just don't alter the current position. */
        }
    }
    if (div.clicktipXDelta || div.clicktipYDelta) {
        div.style.top = (y+div.clicktipYDelta)+"px";
        div.style.left = (x+div.clicktipXDelta)+"px";
    }
    div.style.visibility = 'visible';
    return div;
}

function handleClicktip(e,text,target) {
    /* A clicktip is already on screen for this object? Return */
    if (target.clicktipActive) return false;
    target.clicktipActive = true;

    /* The target object have a tipclick attribute? Use it as text */
    if (target.getAttribute('clicktip')) text=target.getAttribute('clicktip');
    if (!text) return; /* No text attribute nor one specified on registration */

    /* Get the mouse position */
    var mouse = mfxGetMousePos(e);

    /* Create/show the tip div */
    var div = createTipDiv(mouse.x,mouse.y,text,target);

    /* Compute how long the clicktip should be shown */
    var milliseconds = 2000; /* base time */
    var textlen = text.length;

    /* Add one second for every 50 characters */
    while(textlen > 30) {
        milliseconds += 1000;
        textlen -= 30;
    }

    /* Register a timer to remove the DIV after few seconds */
    setTimeout(function() {
        try {
            target.clicktipActive = false;
            document.body.removeChild(div);
            delete(div);
        } catch(e) {};
    }, milliseconds);
    return false;
}

function handleOvertipOver(target,e,text,showdelay,hidedelay)
{
    var mouse = mfxGetMousePos(e);

    target.clicktipX = mouse.x;
    target.clicktipY = mouse.y;

    /* An overtip is already scheduled or shown for this object? Return */
    if (target.clicktipTimeout !== false || target.clicktipActive) return false;

    /* Otherwise start the timer that will display the TIP */
    target.clicktipTimeout = setTimeout(function() {
        showAfterDelay(target,text,showdelay,hidedelay);
    }, showdelay);
}

function handleOvertipOut(target,e,text,showdelay,hidedelay)
{
    /* Clicktip scheduled but not yet shown, delete the timer */
    if (target.clicktipTimeout !== false && !target.clicktipActive) {
        try {
            clearTimeout(target.clicktipTimeout);
        } catch(e) {};
        target.clicktipTimeout = false;
        return;
    }

    /* Tip shown, register a timer to remove it */
    if (target.clicktipActive) {
        target.clicktipTimeout = setTimeout(function() {
            hideAfterDelay(target);
        }, hidedelay);
    }
}

function showAfterDelay(target,text,showdelay,hidedelay)
{
    var div = createTipDiv(target.clicktipX,target.clicktipY,text,target);
    target.clicktipActive = true;
    target.clicktipDiv = div;
    if (showdelay == 0) {
        target.onmousemove = function(event) {
            if (!event) var event = window.event;
            var mouse = mfxGetMousePos(event);
            tipFollowMouse(mouse,this);
        };
    }
}

function hideAfterDelay(target)
{
    delTip(target.clicktipDiv);
}

function tipFollowMouse(mouse,target) {
    var div = target.clicktipDiv;
    div.style.top = mouse.y + div.clicktipYDelta;
    div.style.left = mouse.x + div.clicktipXDelta;
}

/* =============================================================================
 * EFFECTS
 * ========================================================================== */

// Set object opacity in a cross-browser fashion
function mfxSetOpacity(o,val) {
    if (val == 1) val= mfxIsGecko() ? '' : 0.9999;
    o.style.opacity = val;
    try {
        o.style.filter = 'alpha(opacity='+Math.floor(val*100)+')';
    } catch(e) {};
}

// Fade the object 'o' from sval opacity to tval opacity
// i.e. mfxFade(o,0,1) will fade in
//      mfxFade(o,1,0) will fade out
function mfxFade(o,sval,tval,steps,delay) {
    o.style.zoom = '1'; // IE requires this to be set to 1 to set opacity
    if (isdef(o.fade)) {
        try {clearTimeout(o.fade.timeout);} catch(e) {}
        current = o.fade.current;
    } else {
        mfxSetOpacity(o,sval);
        current = sval;
    }
    o.fade = {};
    o.fade.steps = isdef(steps) ? steps : 20;
    o.fade.delay = isdef(delay) ? delay : 50;
    o.fade.sval = sval;
    o.fade.tval = tval;
    o.fade.incr = (tval-sval)/o.fade.steps;
    o.fade.current = current;
    mfxFadeTimeout(o);
}

function mfxFadeTimeout(o) {
    o.fade.current += o.fade.incr;
    if(o.fade.current < 0) o.fade.current = 0;
    else if(o.fade.current > 1) o.fade.current = 1;
    mfxSetOpacity(o,o.fade.current);
    if ((o.fade.incr > 0 && o.fade.current < o.fade.tval) ||
        (o.fade.incr < 0 && o.fade.current > o.fade.tval)) {
        o.fade.timeout = 
            setTimeout(function() {mfxFadeTimeout(o);}, o.fade.delay);
    } else {
        if (isdef(o.onfadedone)) o.onfadedone(o);
        o.fade = undefined;
    }
}

function mfxSpydivPush(div,classname,html) {
    var fade = isdef(div.spyNoFade) ? 0 : 1;
    var ele = document.createElement('div');
    var maxlen = isdef(div.spyMaxLen) ? div.spyMaxLen : 10;
    ele.className = classname;
    ele.innerHTML = html;
    if (fade) mfxSetOpacity(ele,0);
    if (!isdef(div.spyLastEle)) {
        div.appendChild(ele);
        div.spyLen = 1;
    } else {
        div.insertBefore(ele,div.spyLastEle);
        div.spyLen++;
    }
    div.spyLastEle = ele;
    if (fade) mfxFade(ele,0,1,3,50);
    while (div.spyLen > maxlen) {
        var nodes = div.childNodes;
        var last, i=0;
        while(1) {
            i++;
            last = nodes[nodes.length-i];
            if (!isdef(last.spyRemoved)) break;
        }
        if (fade && !mfxIsIphone()) {
            last.onfadedone = function(e) {
                div.removeChild(e);
            }
            last.spyRemoved = true;
            mfxFade(last,1,0,5,50);
        } else {
            div.removeChild(last);
        }
        div.spyLen--;
    }
    return ele;
}

function mfxSpydivClear(div,toleave) {
    toleave = isdef(toleave) ? toleave : 0;
    while (div.spyLen > toleave) {
        var nodes = div.childNodes;
        var last, i=0;
        while(1) {
            i++;
            if (i > nodes.length) return;
            last = nodes[nodes.length-i];
            if (!isdef(last.spyRemoved)) break;
        }
        if (div.spyLastEle == last) div.spyLastEle = undefined;
        div.removeChild(last);
        div.spyLen--;
    }
}

function mfxToggle(o) {
    o = $(o);
    if(!isdef(o.style.visibility) ||
       o.style.visibility=='' ||
       o.style.visibility=='visible') {
        mfxHide(o);
        return "hidden";
    } else {
        mfxShow(o);
        return "visible";
    }
}

function mfxShow(o) {
    o = $(o);
    o.style.visibility = 'visible';
    o.style.display = 'block';
}

function mfxHide(o) {
    o = $(o);
    o.style.visibility = 'hidden';
    o.style.display = 'none';
}
