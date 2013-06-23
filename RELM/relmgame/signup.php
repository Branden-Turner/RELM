<?php

// TODO: MD5 hash on passwords and add email verification
include_once 'functions.php';

// Checks to see if a session is already in process, and if so, kills it.
destroySession();

include_once 'header.php';
?>

<script>
function checkUser(user)
{
    if (user.value == '')
    {
        O('info').innerHTML = ''
        return
    }

    params  = "user=" + user.value
    request = new ajaxRequest()
    request.open("POST", "checkuser.php", true)
    request.setRequestHeader("Content-type", "application/x-www-form-urlencoded")
    request.setRequestHeader("Content-length", params.length)
    request.setRequestHeader("Connection", "close")

    request.onreadystatechange = function()
    {
        if (this.readyState == 4)
            if (this.status == 200)
                if (this.responseText != null)
                    O('info').innerHTML = this.responseText
    }
    request.send(params)
}

function ajaxRequest()
{
    try { var request = new XMLHttpRequest() }
    catch(e1) {
        try { request = new ActiveXObject("Msxml2.XMLHTTP") }
        catch(e2) {
            try { request = new ActiveXObject("Microsoft.XMLHTTP") }
            catch(e3) {
                request = false
    }   }   }
    return request
}
</script>
<div>
<?php

$error = $user = $pass = "";

if (isset($_POST['user']))
{
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);

    if ($user == "" || $pass == "")
        $error = "Not all fields were entered<br /><br />";
    else
    {
        if (mysql_num_rows(queryMysql("SELECT * FROM members
		      WHERE user='$user'")))
            $error = "That username already exists<br /><br />";
        else
		  {
            queryMysql("INSERT INTO members VALUES('$user', '$pass', 0, 0, 0, 0, '', '')");
            die("<h4>Account created</h4>Please Log in.<br /><br />");
        }
    }
}
?>

<div class='centered margin-top'>
<form method='post' action='signup.php'><?php echo $error; ?>
<span class='fieldname'>Create Username</span>
<input type='text' maxlength='16' name='user' value='<?php echo $user; ?>'
    onBlur='checkUser(this)'/><span id='info'></span><br />
<span class='fieldname'>Create Password</span>
<input type='text' maxlength='16' name='pass'
    value='<?php echo $pass; ?>' /><br />


<span class='fieldname'>&nbsp;</span>
<input class='input-button' type='submit' value='Sign Up' />
</div>
</form></div><br /></body></html>
