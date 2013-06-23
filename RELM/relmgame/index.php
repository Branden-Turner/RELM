<?php
include_once 'header.php';
?>

<br/><div id='mainmenu'>Welcome to RELM<br/><br/>

<?php
if ($loggedin) echo " $user, you are logged in.";
else           echo ' Please sign up and/or log in to play.';
?>
</div>

</body>
</html>
