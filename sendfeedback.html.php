<?
    require("lib.php");
    Config("title","Feedbacks");
    include("header.php");

    if (isset($_POST['subject']) &&
        isset($_POST['body']) &&
        isset($_POST['email']))
    {
        $realbody = $_POST['body'];
        $realbody .= "\n\nEmail mittente: ".$_POST[email];
        $realbody .= "\nIndirizzo IP mittente: ".$_SERVER["REMOTE_ADDR"];
        $to = Config("emailfback");
        sendMailBody($_POST['email'], $to, "[LLOOGG] ".$_POST['subject'], $realbody);
    }
?>
<h4>Message successfully sent</h4>
Thanks for your message, we will try to reply soon.
<?
    include("footer.php")
?>
