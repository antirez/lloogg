function tryLogin() {
	clearFields(new Array("username","pass"));
	if (!validate(document.f.username.value, "^[A-z0-9]+$", "Invalid username","username")){
		return false;
	}
	// Use AJAX to check if the user/password are valid.
	mfxGetRand("/ajax/login.php?username="+mfxEscape(document.f.username.value)+"&pass="+mfxEscape(document.f.pass.value)+"&rememberme="+(document.f.rememberme.checked?'1':'0'), loginHandler);
}
function loginHandler(res) {
    if (res.indexOf("OK:") != -1) {
        // Login success.
        var l = window.location.toString();
        var i = l.indexOf("?goto=");
        if (i == -1) {
            if (typeof(window.opera) != 'undefined') {
                window.location = '/?l=1';
            } else {
                window.location = '/';
            }
        } else {
            i+=5;
            l = unescape(l.substring(i,l.length+1));
            window.location = l;
        }
    } else {
        // Login failed. Show an error.
        warnField("username");
        warnField("pass");
            alert('The username and password you entered don\'t match a valid account, please verify and retry.');
    }
}
