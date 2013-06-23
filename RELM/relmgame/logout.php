<?php
include_once 'functions.php';

$wasLoggedIn = destroySession();

include_once 'header.php';

if ($wasLoggedIn)
{
?>
  <script>
    setTimeout(function()
    {
      window.location = "index.php";
    }, 1000);
  </script>
  <div id='mainmenu'>You have been logged out. Please <a href='index.php'>click here</a> to refresh the screen.
<?php } else { ?>

<div id='mainmenu'>
You cannot log out because you are not logged in

<?php } ?>

</div></body></html>
