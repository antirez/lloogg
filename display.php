<?
require('trycache.php');
    require("nook.php");
    Config("jsfile",Array('mfx.js','news.js'));
    Config("css",false);
	Config("title","OKNOise - cosa accade su oknotizie in tempo reale");
    include("header.php");
    $_spyevents = Array("ok","no","news","comment","user","karma");
    noiseJsEvents();
    noiseJsCategories();
?>
<table class="spycontrol"><tr>
<td><img src="/images/led-red-off.gif" id="activityled" /></td>
<td><span id="spylogo">OKNOise</span></td>
<?

$_spyeventstext = Array( "ok" => 'voti OK',
                        "no" => 'voti NO',
                        "news" => 'nuove notizie',
                        "comment" => 'commenti',
                        "user" => 'nuovi utenti',
                        "karma" => 'karma');
function noiseTypeSwitches() {
    global $_spyevents, $_spyeventstext;
    foreach($_spyevents as $e) {
        echo('<td><div class="spyeventswitch">');
        echo('<input type="checkbox" id="spyswitch'.$e.'" value="1" onclick="toolbarIconUpdate(\''.$e.'\');" checked="true">');
        echo('<br/><a href="javascript://" title="Visualizzazione '.$_spyeventstext[$e].'" onclick="toolbarUpdate(\''.$e.'\');"><img src="/images/spy'.htmlentities($e).'.gif" alt="Visualizzazione '.$_spyeventstext[$e].'" width="24" height="24" border="0" id="spytoolbaricon'.$e.'"/></a>');
        echo('</div></td>');
    }
}

function noiseJsEvents() {
    global $_spyevents;
    echo('<script type="text/javascript">var spyevents=[');
    for ($i = 0; $i < count($_spyevents); $i++) {
        echo('"'.$_spyevents[$i].'"');
        if ($i != count($_spyevents)-1) echo(",");
    }
    echo("];</script>\n");
}

function noiseJsCategories() {
    echo('<script type="text/javascript">var spycats=[');
    $cats = dbListCategories();
    for ($i = 0; $i < count($cats); $i++) {
        echo('"'.$cats[$i]['name_url'].'"');
        if ($i != count($cats)-1) echo(",");
    }
    echo("];</script>\n");
}

function noiseCategorySelect() {
    $cats = dbListCategories();
    echo("<table id=\"spycatsel\"><tr>");
    for ($i = 0; $i < count($cats); $i++) {
        echo('<td><input type="checkbox" id="cat'.$cats[$i]['name_url'].'" value="1" onclick="noiseSaveConfig()" checked>'.htmlentities($cats[$i]['name'])."</td> ");
        if ($i == 4) echo("</tr><tr>");
    }
    echo("</tr></table>");
}

noiseTypeSwitches();
?>
<td><select id="numitems">
<option value="5">5</option>
<option value="10" selected="1">10</option>
<option value="20">20</option>
<option value="30">30</option>
<option value="50">50</option>
<option value="100">100</option>
<option value="10000">10000</option>
</select></td>
<td><a href="javascript:void(0)" onClick="noiseToggleAdvconf()" title="Configurazione avanzata"><img src="/images/spyconfig.gif" border="0" alt="configurazione avanzata" width="32" height="32"/></a></td>
<td><input type="image" src="/images/spypulisci.gif" border="0" value="Pulisci" id="clearbut" title="Pulisci"/></td>
<td><input type="image" src="/images/spystop.gif" border="0" value="Stop" id="togglebut" title="Stop"/></td>
</tr></table>

<div id="advconf" style="visibility:hidden; display:none;">
<table>
<tr>
<td>Categorie:</td>
<td>
<? noiseCategorySelect(); ?>
</td>
</tr>

<tr>
<td>Visualizza voti anonimi:</td>
<td>
<input type="checkbox" id="showanonvote" onclick="noiseSaveConfig()" checked>
</td>
</tr>

<tr>
<td>Disabilita effetti grafici (per PC lenti):</td>
<td>
<input type="checkbox" id="nofx" onclick="noiseSetNoFx(); noiseSaveConfig()">
</td>
</tr>

<tr>
<td colspan="2">
<a style="font-size:12px;" href="javascript:void(0)" onclick="noiseToggleAdvconf()">[chiudi]</a>
</td>
</tr>
</table>
</div>

<div id="advremind" style="visibility:hidden; display:none;" class="lightbulb">
</div>

<div id="spymain">
</div>
<script type="text/javascript">
var noiseq = [];
var nextid = 0;
var pendingreq = 0;
var ucounter = 0;
var rcounter = 0;
var noiseon=1;
var initialreqclicks = 4;
var reqclicks = initialreqclicks;

function toolbarUpdate(e){
    if($('spyswitch'+e).checked){
        $('spyswitch'+e).checked=false;
    }else{
        $('spyswitch'+e).checked=true;
    }
    toolbarIconUpdate(e);
}

function toolbarIconUpdate(e){
     if($('spyswitch'+e).checked){
        $('spytoolbaricon'+e).src='/images/spy'+e+'.gif';
    }else{
        $('spytoolbaricon'+e).src='/images/spy'+e+'_off.gif';
    }
    noiseSaveConfig();
}

function noiseUpdate() {
    if (!noiseon) return;
    if (ucounter == 0) {
        if (noiseq.length) {
            var showthis = 1;
            for (var j = 0; j < spyevents.length; j++) {
                if (!$('spyswitch'+spyevents[j]).checked &&
                    noiseq[0].indexOf('<!--spyeventname:'+
                    spyevents[j]+'-->') != -1)
                        showthis = 0;
            }
            if (showthis)
                mfxSpydivPush($('spymain'),'spyinnerdiv',noiseq[0]);
            var slice = [];
            for (var i = 1; i < noiseq.length; i++)
                slice[i-1] = noiseq[i];
            noiseq = slice;
            ucounter = 3;
        }
    } else {
        ucounter--;
    }

    if (rcounter == 0) {
        if (pendingreq == 0) {
            $('activityled').src='/images/led-red-on.gif';
            var q='&type=';
            var c=0;
            for (var i = 0; i < spyevents.length; i++) {
                if ($('spyswitch'+spyevents[i]).checked) {
                    c++;
                    q += spyevents[i];
                    if (i != spyevents.length-1) q += '.';
                }
            }
            if (c == spyevents.length) q='';
            else if (c == 0) q += 'none';
            mfxGetRand('/spypolling.php?minid='+nextid+q,noiseReqHandler);
            rcounter = reqclicks;
        }
    } else {
        rcounter--;
    }
}

function noiseReqHandler(r) {
    setTimeout(function() {
        $('activityled').src='/images/led-red-off.gif';
    },100);
    pendingreq = 0;
    if (r == "NODATA") {
        if (reqclicks < 20) reqclicks += 5;
        return;
    } else {
        reqclicks = initialreqclicks;
    }
    var elist = r.split('<>');
    for (var i = 0; i < elist.length-1; i++) {
        var e = elist[i];
        if (!($('showanonvote').checked) &&
            e.indexOf('<!--spyanonvote:1-->') != -1) continue;
        var goodcat = 1;
        for (var j=0; j < spycats.length; j++) {
            if (!($('cat'+spycats[j]).checked)) {
                if (e.indexOf('<!--spynewscat:'+spycats[j]+'-->') != -1)
                    goodcat = 0;
            }
        }
        if (!goodcat) continue;
        noiseq[noiseq.length] = e;
    }
    nextid = elist[elist.length-1];
}

function noiseToggle() {
    if (noiseon == 0) {
        $('togglebut').src="/images/spystop.gif";
        $('togglebut').title="Stop";
        noiseon = 1;
    } else {
        $('togglebut').src="/images/spystart.gif";
        $('togglebut').title="Start";
        noiseon = 0;
    }
}

function noiseSetNumItems() {
    var s = $('numitems');
    var numitems = s.options[s.selectedIndex].value;
    $('spymain').spyMaxLen = numitems;
    mfxSpydivClear($('spymain'),numitems);
}

function noiseClear() {
    mfxSpydivClear($('spymain'),1);
}

function noiseSetNoFx() {
    if ($('nofx').checked) {
        $('spymain').spyNoFade = true;
    } else {
        $('spymain').spyNoFade = undefined;
    }
}

function noiseSaveConfig() {
    if (!isdef(noisecansave)) return;
    var a = ["numitems","showanonvote","nofx"];
    mfxMap(spyevents,function(x) {
        a[a.length]='spyswitch'+x;
    });
    mfxMap(spycats,function(x) {
        a[a.length]='cat'+x;
    });
    mfxSaveInputsInCookie("oknoisecfg",a);
}

function noiseRestoreConfig() {
    mfxRestoreInputsFromCookie("oknoisecfg");
}

function noiseAdvDefConf() {
    var defcfg = '["showanonvote","1","nofx","0","catesteri","1","catcronaca","1","catpolitica","1","cateconomia","1","catscienze_e_tecnologie","1","catsport","1","catarte_e_cultura","1","catspettacoli","1","catsalute_e_alimentazione","1","cataltro","1"]';
    var a = ["showanonvote","nofx"];
    mfxMap(spycats,function(x) {
        a[a.length]='cat'+x;
    });
    return (mfxSaveInputsInString(a) == defcfg);
}

function noiseAdvReminder() {
    if ($('advconf').style.visibility == 'hidden' && !noiseAdvDefConf()) {
        var html = "<b>Avanzate:</b>";
        if (!$('showanonvote').checked) html += " no anonimi,";
        if ($('nofx').checked) html += " no effetti grafici,";
        var c = 0;
        mfxMap(spycats,function(x) {
            if ($('cat'+x).checked) c++;
        });
        if (c == spycats.length) c= "tutte le";
        else c = c+" su "+spycats.length+" ";
        html += " "+c+" categorie selezionate";
        $('advremind').innerHTML = html;
        mfxShow('advremind');
    } else {
        mfxHide('advremind');
    }
}

function noiseToggleAdvconf() {
    mfxToggle('advconf');
    noiseAdvReminder();
}

<?
foreach($_spyevents as $e){
    echo "toolbarIconUpdate('$e');\n";
}
?>
/* noisecansave is used to protect the restore process
  (that is designed to fire onclick and onchange events)
  from saving the configuration while it is going to be
  restored. */
noiseRestoreConfig();
var noisecansave = 1;
noiseSetNumItems();
noiseSetNoFx();
noiseAdvReminder();
$('togglebut').onclick = noiseToggle;
$('clearbut').onclick = noiseClear;
$('numitems').onchange = function() {
    noiseSetNumItems();
    noiseSaveConfig();
}

mfxEvery(500,noiseUpdate);
</script>
</div>
</html>
