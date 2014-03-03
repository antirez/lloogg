function submitForm() {
	document.f.submit();
}
function registrationCheckUsername() {
    mfxGetRand("/ajax/checkusername.php?username="+document.f.usernameReg.value,
    function(c) {
        if (c.indexOf("OK") != -1) {
            submitForm();
        } else {
            warnField('usernameReg');
            regErrUsername();
            enableButton();
        }
    });
}
function regErrUsername() {
    var text='The username selected is already in use and can\'t be used again, please select a different username in order to continue.';
    regErr(text,"usernameReg");
}
function regErr(text,field){
   mfxShow('regerrdiv');
   $('regerrtxt').innerHTML=text;
   warnField(field);
}
function checkRegistrationForm() {
    disableButton();
    clearFields(new Array("email","reemail","usernameReg","passReg","repassReg"), new Array("regerrdiv"));
    if (!validate(document.f.usernameReg.value, "^[A-z0-9]+$", "Empty username or special characters in username are invalid.","usernameReg")){
        enableButton();
        return false;
    }
    if (!validate(document.f.passReg.value, "^.{5,}$", "Password too short! Minimal password length is 5 chars.","passReg")){
        enableButton();
        return false;
    }
    if (document.f.passReg.value != document.f.repassReg.value) {
        alert("The two passwords fields are not the same.");
        warnField("passReg");
        warnField("repassReg");
        enableButton();
        return false;
    }
    if (!isValidEmail(document.f.email.value)) {
        alert("Invalid email address.");
        warnField("email");
        enableButton();
        return false;
    }
    if(document.f.email.value != document.f.reemail.value){
        alert("The two email fields are not the same.");
        warnField("email");
        warnField("reemail");
        enableButton();
        return false;
    }
    registrationCheckUsername();
    return false;
}
function disableButton(){
    $("registerButton").disabled=true;
    $("registerButton").value="Wait...";
	
}
function enableButton(){
    $("registerButton").disabled=false;
    $("registerButton").value="Create my account";
}
