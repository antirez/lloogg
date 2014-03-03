function checkFeedbacksForm() {
    clearFields(new Array("email","body"), null);
    if (!isValidEmail(document.f.email.value)) {
        alert("Invalid email");
        warnField("email");
        return false;
    }
    var t = document.f.body.value;
    t = t.replace(/\r/g,"");
    t = t.replace(/\n/g,"");
    if (!validate(t, "^.*[^ ]+.*$", "Message can't be empty","body"))
        return false;
    document.f.submit();
}
