<?php

include_once 'functions.php';
require_once 'User.php';
require_once 'Scenario.php';

startSession();

$userstr = ' (Guest)';
$userPoints = 0;

if (User::IsLoggedIn())
{
    $user     = $_SESSION['user'];
    $loggedin = TRUE;
    $userstr  = " ($user)";
    $userPoints = User::GetPoints();
}
else
    $loggedin = FALSE;
?>

<!DOCTYPE html>
<html>
  <head>
    <script type='text/javascript' src='jquery-1.8.3.min.js'></script>
    <script type='text/javascript' src='tagmanager/bootstrap-tagmanager.js'></script>
    <script src='OSC.js'></script>
    <link rel='stylesheet' href='styles.css' type='text/css' />
    <link rel='stylesheet' href='tagmanager/bootstrap-tagmanager.css' type='text/css' />
    <title><?php echo $appname.$userstr; ?></title>
  </head>

  <body>


    <div id="image">

    
    <div id='appname'><?php echo $appname.$userstr.": "?> <span id='userPoints'> <?php echo $userPoints; ?></span> Evidence Points</div>
    <?php
    if ($loggedin)
    {
    ?>
      <div id='mainmenu'>
        <ul type='none'>
          <li><a href='members.php?view=<?php echo $user; ?>'>Home</a></li>
          <li><a href='members.php'>Rankings</a></li>
          <li><a href='messages.php'>Messages</a></li>
          <li><a href='profile.php'>Edit Profile</a></li>
          <li><a href='game.php'>Play</a></li>
          <?php if(User::GetHighestDifficulty() == 3)  { ?> <li><a href='builder.php'>Build</a></li> <?php } ?>
          <?php if(User::GetReviewFlag() == 1)  { ?> <li><a href='review.php'>Review</a></li> <?php } ?>
          <li><a href='matchingGame.php'>Matching</a></li>
          <li><a href='about.php'>About</a></li>
          <li><a href='logout.php'>Log Out</a></li>
          <li><a href='https://docs.google.com/forms/d/172SvirB4up7hUZ-IwNE_g4wy_cxXiKrb7eY8SJmD76g/viewform' target='blank'>Feedback</a></li>
          
        </ul>
      </div>
    <?php
    }
    else
    {
    ?>
    <div id='mainmenu'>
      <ul type='none'>
        <li><a href='index.php'>Home</a></li>
        <li><a href='signup.php'>Sign Up</a></li>
        <li><a href='login.php'>Log In</a></li>
      </ul>
    </div>
    <?php
    }
    ?>

    <div class='page-header'></div>
