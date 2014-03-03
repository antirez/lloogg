<div id="trendmain" style="visibility:hidden; display:none;">
</div>
<div id="uimain" style="visibility:visible; display:block;">
<table class="logscontrol"><tr>
<td><img src="/images/led-red-off.gif" id="activityled" /></td>
<td><select id="numitems">
<option value="5">5</option>
<option value="10" selected="1">10</option>
<option value="20">20</option>
<option value="30">30</option>
<option value="50">50</option>
<option value="100">100</option>
<option value="1000">1000</option>
<option value="10000">10000</option>
</select></td>
<td><input type="image" src="/images/clear.gif" border="0" value="Clear" id="clearbut" title="Clear"/></td>
<td><input type="image" src="/images/stop.gif" border="0" value="Stop" id="togglebut" title="Pause"/></td>
<td><a class="ctrlbut" href="#" onclick="logLoadHistory(); return false;">load history</a></td>
<td><a id="openfilters" class="ctrlbut" href="#" onclick="logToggleFilters()">open filters</a></td>
</tr></table>
<div id="filters" style="visibility:hidden; display:none;">
<div class="notabene">
Set your filter, it will take effect immediately. Press the <i>load history</i> button to reload only visits matching your filter.
</div>
<form name="f">
<table>
<tr>
    <td>Match in url</td>
    <td><input name="filterurl" type="inputtext" size="20"></td>
    <td style="padding-left:20px;">Match in referrer</td>
    <td><input name="filterref" type="inputtext" size="20"></td>
</tr>
<tr>
    <td>Browser</td>
    <td>
        <select name="filterbrowser">
        <option value="">All</option>
        <option value="firefox">Firefox</option>
        <option value="msie ">Internet Explorer</option>
        <option value="safari">Safari</option>
        <option value="chrome">Google Chrome</option>
        <option value="opera">Opera</option>
        <option value="konqueror">Konqueror</option>
        </select>
    </td>
    <td style="padding-left:20px;">Operating System</td>
    <td>
        <select name="filteros">
        <option value="">All</option>
        <option value="windows">Windows</option>
        <option value="mac os">Mac OS</option>
        <option value="linux">Linux</option>
        <option value="iphone">iPhone OS</option>
        <option value="j2me">J2ME/MIDP</option>
        </select>
    </td>
</tr>
<tr>
    <td colspan="2"><input type="checkbox" name="filteronlyfirst"> Show only one pageview per client</td>
    <td style="padding-left:20px;" colspan="2"><input type="checkbox" name="filterinvert"> Invert filter</td>
</tr>
</table>
</form>
</div>
<div id="filterwarn" class="notabene" style="width:500px; visibility:hidden; display:none;">
Note: you have filters enabled. Some visits may not be shown.
</div>
<div id="logsdiv">
</div>
<div id="trendbox">
<h3>Today trend</h3>
<span id="todayvisitors"><?=logTodayVisitors()?></span> Visitors, <span id="todaypageviews"><?=logTodayPageviews()?></span> Pageviews<br/>
<a href="#" onclick="logShowTrends(); return false;">See history and graphs &raquo;</a>
</div>
<div id="statsdiv">
    <div class="statbox">
    <h5>Realtime stats</h5>
    <div id="statbox_general">
    </div>
    </div>

    <div class="statbox">
    <h5>Top referers</h5>
    <div id="statbox_topreferers">
    </div>
    </div>

    <div class="statbox">
    <h5>Top searches</h5>
    <div id="statbox_topsearches">
    </div>
    </div>

    <div class="statbox">
    <h5>Top pages</h5>
    <div id="statbox_toppages">
    </div>
    </div>

    <div class="statbox">
    <h5>Browsers war</h5>
    <div id="statbox_topbrowsers">
    </div>
    </div>
</div>
<script type="text/javascript" src="/javascript/usermain.js?v=9"></script>
<script type="text/javascript">
var Options = {};
Options.showuseragent = <?=(userShowUserAgent() ? "true;\n" : "false;\n")?>
</script>
</div>
