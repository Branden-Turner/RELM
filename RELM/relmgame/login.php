<?php
include_once 'header.php';
echo "<div>";
$error = $user = $pass = "";

if (isset($_POST['user']))
{
    $user = sanitizeString($_POST['user']);
    $pass = sanitizeString($_POST['pass']);

    if ($user == "" || $pass == "")
    {
        $error = "Not all fields were entered<br />";
    }
    else
    {
      echo User::Login($user, $pass);
      return;
    }
}
?>

<div class='centered margin-top'>
<form method='post' action='login.php'><?php echo $error; ?>
<span class='fieldname'>Username </span><input type='text'
    maxlength='16' name='user' value='<?php echo $user; ?>' /><br />
<span class='fieldname'>Password </span><input type='password'
    maxlength='16' name='pass' value='<?php echo $pass; ?>' />

<br />
<span class='fieldname'>&nbsp;</span>
<input class='input-button' type='submit' value='Log In' />
</div>
</form><br /></div></body></html>
