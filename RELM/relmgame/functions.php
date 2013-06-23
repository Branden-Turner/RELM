<?php
$dbhost  = 'localhost'; 
$dbname  = 'relmDB';
$dbuser  = 'relmUser';
$dbpass  = 'realMedicine2013';
$appname = "RELM";

mysql_connect($dbhost, $dbuser, $dbpass) or die(mysql_error());
mysql_select_db($dbname) or die(mysql_error());

function createTable($name, $query)
{
    queryMysql("CREATE TABLE IF NOT EXISTS $name($query)");
    echo "Table '$name' created or already exists.<br />";
}

function queryMysql($query)
{
    $result = mysql_query($query) or die(mysql_error());
	 return $result;
}

function startSession()
{
  // Minimalize the amount of times session_start is called
  // This does it safely to remove dual session issues.
  if(session_status() != 2)
    session_start();
}

function destroySession()
{
    startSession();
    
    if (isset($_SESSION['user']))
    {
      // This should reset the session id for you, 
      // doing a better cookie reset than what you have.
      session_regenerate_id(TRUE);
      
      // Instead of creating a new array, just free the memory.
      unset($_SESSION);
      
      // Kill the session.
      session_destroy();

      return TRUE;
    }
    else
      return FALSE;
}

function sanitizeString($var)
{
    $var = strip_tags($var);
    $var = htmlentities($var);
    $var = stripslashes($var);
    return mysql_real_escape_string($var);
}

function showProfile($user)
{
    if (file_exists("$user.jpg"))
        echo "<img src='$user.jpg' align='left' />";

    $result = queryMysql("SELECT * FROM profiles WHERE user='$user'");

    if (mysql_num_rows($result))
    {
        $row = mysql_fetch_row($result);
        echo stripslashes($row[1]) . "<br clear=left /><br />";
    }
}
?>
