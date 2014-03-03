/* Copyright(C) 2005-2007 Salvatore Sanfilippo <antirez@gmail.com>
 * All Rights Reserved. */

function validate(string, regexp, err, fieldName) {
    if (string.match(regexp) == null) {
        alert(err);
        warnField(fieldName);
        return false;
    }
    return true;
}
function validateEmpty(string,err,filedName) {
    return validate(string, "^.*[^ ]+.*$", err, filedName);
}
function isValidEmail(a)
{
    var at = a.indexOf("@");
    var name = a.substring(0, at);
    var isp = a.substring(at + 1, a.length);
    var dot = a.lastIndexOf(".");
    if (at == -1 || at == 0 || name == "" || isp == "" || dot == -1 ||
dot== (a.length - 1))
    {
        return false;
    }
    return true;
}
function warnField(fieldName)
{
    eval("document.f."+fieldName+".style.border='1px red solid'");
    eval("document.f."+fieldName+".focus();");
}
function clearFields(fields, hidelist) {
    for (i = 0; i < fields.length; i++) {
        eval("document.f."+fields[i]+".style.border='1px inset #ddd'");
    }
    if (hidelist != null) {
        for (i = 0; i < hidelist.length; i++) {
            mfxHide(hidelist[i]);
        }
    }
}
function areyousure(message) {
    return confirm(message + ": are you sure?");
}
