<?php

require_once 'functions.php';

class User
{
  private static function CheckNumeric($number)
  {
    if(is_numeric($number) == false)
      die("Invalid entry!");
  }
  public static function Login($user, $pass)
  {
    $query = "SELECT * FROM members WHERE user='$user' AND pass='$pass'";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      return "<span class='error'>Username/Password invalid</span><br /><br />";
    else
    {
        $userData = mysql_fetch_row($result);
        $_SESSION['user'] = $user;
        $_SESSION['pass'] = $pass;
        $_SESSION['ID'] = (int)($userData[2]);
        $_SESSION['review'] = (int)($userData[3]);
        $_SESSION['highdiff'] = (int)($userData[4]);
        return "<div class='page-header'> You are now logged in. Please <a href='members.php?view=$user'> click here</a> to continue.<br /><br /> </div>";
    }
  }

  public static function IsLoggedIn()
  {
    return (isset($_SESSION['user']));
  }

  public static function GetUsername($userID=-1)
  {
    User::CheckNumeric($userID);
    if(($userID == -1 && isset($_SESSION['user']) == false) || ($userID != (int)($_SESSION['ID'])))
    {
      $query = "SELECT user FROM members WHERE ID=$userID";
      $result = mysql_query($query);

      if(mysql_num_rows($result) == 0)
        return "Guest";
      else
        return mysql_fetch_row($result)[0];
   }
   else if(isset($_SESSION['user']) == true)
    return $_SESSION['user'];
   else
    return "Guest";
  }

  public static function GetID($username)
  {
    $query = "SELECT ID FROM members WHERE user='$username'";
    $result = mysql_query($query);
    if(mysql_num_rows($result) == 0)
      return -1;
    else
      return (int)mysql_fetch_row($result)[0];
  }

  public static function GetPoints($userID=-1)
  {
    User::CheckNumeric($userID);
    if($userID == -1)
      $userID = ($_SESSION['ID']);

    $query = "SELECT points FROM members WHERE ID=$userID";
    $result = mysql_query($query);

    if(mysql_num_rows($result) == 0)
      return -1;
    else
      return (int)(mysql_fetch_row($result)[0]);
  }

  public static function SetPoints($userID=-1, $addPoints=0)
  {
    User::CheckNumeric($userID);
    User::CheckNumeric($addPoints);
    if($userID == -1)
      $userID = ($_SESSION['ID']);

    $curPoints = User::GetPoints($userID);
    if($curPoints == -1)
      die("Something horrible went wrong");
    else
    {
      $curPoints += $addPoints;
      mysql_query("UPDATE members SET points=$curPoints where ID=$userID");
    }
  }

  public static function GetReviewFlag($userID=-1)
  {
    User::CheckNumeric($userID);
    if($userID == -1)
      $userID = ($_SESSION['ID']);

    $query = "SELECT reviewflag FROM members WHERE ID=$userID";
    $result = mysql_query($query);

    if(mysql_num_rows($result) == 0)
      return 0;
    else
    {
      $numRes = (int)(mysql_fetch_row($result)[0]);
      if($userID == (int)($_SESSION['ID']))
        $_SESSION['review'] = $numRes;
      return $numRes;
    }
  }

  public static function SetReviewFlag($userID=-1)
  {
    User::CheckNumeric($userID);
    if($userID == -1)
      $userID = ($_SESSION['ID']);

    mysql_query("UPDATE members SET reviewflag=1 WHERE ID=$userID");
  }

  public static function GetHighestDifficulty($userID=-1)
  {
    User::CheckNumeric($userID);
    if($userID == -1)
      $userID = ($_SESSION['ID']);

    $query = "SELECT highdiff FROM members WHERE ID=$userID";
    $result = mysql_query($query);

    if(mysql_num_rows($result) == 0)
      return 0;
    else
    {
      $numRes = (int)(mysql_fetch_row($result)[0]);
      if($userID == (int)($_SESSION['ID']))
        $_SESSION['highdiff'] = $numRes;
      return $numRes;
    }
  }

  public static function SetHighestDifficulty($userID=-1, $highdiff=0)
  {
    User::CheckNumeric($userID);
    User::CheckNumeric($highdiff);
    if($userID == -1)
      $userID = ($_SESSION['ID']);

    if(User::GetHighestDifficulty($userID) > $highdiff)
      return;

    mysql_query("UPDATE members SET highdiff=$highdiff WHERE ID=$userID");
  }

  public static function GetScenariosWritten($userID=-1)
  {
    User::CheckNumeric($userID);
    if($userID == -1)
      $userID = ($_SESSION['ID']);

    $query = "SELECT created FROM members WHERE ID=$userID";
    $result = mysql_query($query);

    if(mysql_num_rows($result) == 0)
      return "";
    else
      return stripslashes(mysql_fetch_row($result)[0]);
  }

  public static function GetScenariosCompleted($userID=-1)
  {
    User::CheckNumeric($userID);
    if($userID == -1)
      $userID = ($_SESSION['ID']);

    $query = "SELECT completed FROM members WHERE ID=$userID";
    $result = mysql_query($query);

    if(mysql_num_rows($result) == 0)
      return "";
    else
      return stripslashes(mysql_fetch_row($result)[0]);
  }

  public static function AddScenariosWritten($userID=-1, $newID=0)
  {
    User::CheckNumeric($userID);
    User::CheckNumeric($newID);
    if($userID == -1)
      $userID = ($_SESSION['ID']);

    if($newID == 0)
      die("You must specify a valid written scenario ID");

    $getScenarioCSV = User::GetScenariosWritten($userID);

    if(strlen($getScenarioCSV) == 0)
      $getScenarioCSV = "$newID";
    else
      $getScenarioCSV .= ",$newID";

    mysql_query("UPDATE members SET created='$getScenarioCSV' WHERE ID=$userID");
  }

  public static function AddScenariosCompleted($userID=-1, $completedID=0)
  {
    User::CheckNumeric($userID);
    User::CheckNumeric($completedID);

    if($userID == -1)
      $userID = ($_SESSION['ID']);

    if($completedID == 0)
      die("You must specify a valid completed scenario ID");

    $getScenarioCSV = User::GetScenariosCompleted($userID);
    $getDifficulty = Scenario::GetDifficulty($completedID);

    User::SetHighestDifficulty($userID, $getDifficulty);

    if(strlen($getScenarioCSV) == 0)
      $getScenarioCSV = "$completedID";
    else
      $getScenarioCSV .= ",$completedID";

    mysql_query("UPDATE members SET completed='$getScenarioCSV' WHERE ID=$userID");
  }
}
