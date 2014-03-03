function switchUser() {
    var su = $('seluser').options[$('seluser').selectedIndex].value;
    var now = new Date;
    var t = now.getTime();
    now.setTime(t+(3600*24*1000*1000));
    mfxSetCookie("requser",su,now,"/");
    document.location.reload();
}
