<?php

require_once 'functions.php';

class Scenario
{
  private static function CheckNumeric($number)
  {
    if(is_numeric($number) == false)
      die("<div class='centered'>Invalid entry!</div>");
  }

  // 0 - Live, 1 - In Review, 2 - Denied, 3 - NA
  public static function GetStatus($scenario_id)
  {
    $query = "SELECT scenario_status FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return (int)(mysql_fetch_row($result)[0]);
  }

  // Sets the review status
  public static function SetStatus($scenario_id, $newStatus, $msg)
  {
    Scenario::CheckNumeric($scenario_id);
    Scenario::CheckNumeric($newStatus);

    $creator = Scenario::GetCreator($scenario_id);
    User::SetReviewFlag($creator);

    /* Possibly sends out a message */
    mysql_query("UPDATE scenarios SET scenario_status=$newStatus WHERE ID=$scenario_id");
    $view = Scenario::GetCreatorName($creator);
    $user = $_SESSION['user'];
    $time = time();
    $msg .= "builder.php?edit=$scenario_id";
    $msg = sanitizeString($msg);

    mysql_query("INSERT INTO messages VALUES(NULL, '$user', '$view', '0', $time, '$msg')");
  }

  // Returns an int that matches with a user id
  public static function GetCreator($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);
    $query = "SELECT scenario_creator FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return (int)(mysql_fetch_row($result)[0]);
  }

  // Gives the actual name
  public static function GetCreatorName($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);

    $query = "SELECT scenario_creator FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return User::GetUsername((int)(mysql_fetch_row($result)[0]));
  }

  // Grabs the title
  public static function GetTitle($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);
    $query = "SELECT scenario_title FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return stripslashes(mysql_fetch_row($result)[0]);
  }

  // Grabs description
  public static function GetDesc($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);
    $query = "SELECT scenario_desc FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return stripslashes(mysql_fetch_row($result)[0]);
  }

  // Gives you a ranking
  // 0 - No Rank, 1 - Easy, 2 - Medium, 3 - Hard
  public static function GetDifficulty($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);
    $query = "SELECT scenario_difficulty FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return (int)(mysql_fetch_row($result)[0]);
  }

  public static function GetDifficultyText($scenario_id)
  {
    $diff = Scenario::GetDifficulty($scenario_id);
    if($diff == 3)
      return "Hard";
    else if($diff == 2)
      return "Medium";
    else
      return "Easy";
  }

  // Get Patient Data (Array - Name, Age, Height, Weight)
  public static function GetPatientInfo($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);
    $query = "SELECT patient_name, patient_age, patient_height, patient_weight, patient_bp, patient_heartRate, patient_respRate, patient_BMI, patient_currentMeds, patient_medHistory, patient_socHistory FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return ((mysql_fetch_row($result)));
  }

  // Array of Hints!
  public static function GetHints($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);
    $query = "SELECT scenario_hints FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return explode('|', stripslashes(mysql_fetch_row($result)[0]));
  }

  // Grabs Search Terms
  public static function GetSearch($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);
    $query = "SELECT scenario_searchterms FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return explode('|', stripslashes(mysql_fetch_row($result)[0]));
  }

  // Array of answers
  public static function GetAnswers($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);
    $query = "SELECT scenario_answers FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return explode('|', stripslashes(mysql_fetch_row($result)[0]));
  }

  // Sorted from best to worst
  public static function GetAnswerRankings($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);
    $query = "SELECT scenario_answer_ranking FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return explode('|', stripslashes(mysql_fetch_row($result)[0]));
  }

  // Gives you time and then the bonus time value stuff
  public static function GetBonusData($scenario_id)
  {
    Scenario::CheckNumeric($scenario_id);
    $query = "SELECT scenario_time, scenario_bonus FROM scenarios WHERE ID=$scenario_id";
    $result = mysql_query($query);
    if (mysql_num_rows($result) == 0)
      die("<div class='centered'>This scenario doesn't exist!</div>");
    else
      return ((mysql_fetch_row($result)));
  }

  // Looks for noncompleted, published, not own scenarios from difficulty
  // Returns a scenario int.
  public static function Find($userID=-1, $difficulty=0)
  {
    Scenario::CheckNumeric($userID);
    Scenario::CheckNumeric($difficulty);

    if($userID == -1)
      $userID = $_SESSION["ID"];

    $query = "SELECT ID FROM scenarios WHERE (scenario_creator<>$userID AND scenario_difficulty=$difficulty AND scenario_status=0";
    $getUserCompleted = User::GetScenariosCompleted($userID);
//    if(strlen($getUserCompleted) != 0)
//      $query .= " AND NOT FIND_IN_SET(ID, '$getUserCompleted'))";
//    else
      $query .= ")";

    $result = mysql_query($query);
    $resultNum = mysql_num_rows($result);
    if ($resultNum == 0)
      die("<div class='centered'>No scenarios left!</div>");
    else
    {
      // Get the chosen ID index
      $chosen = rand(0, $resultNum - 1);

      // Get all the case IDs
      $idArray = [];
      while( $row = mysql_fetch_assoc($result) )
        $idArray[] =  $row['ID'];

      // Get the chosen case ID and give it back to the caller
      $caseId = (int)($idArray[$chosen]);
      return $caseId;
    }

  }

  // Allows you to find your own scenarios
  public static function FindOwn($userID=-1, $includeNotPublished=0)
  {
    Scenario::CheckNumeric($userID);
    Scenario::CheckNumeric($includeNotPublished);
    if($userID == -1)
      $userID = $_SESSION["ID"];

    $query = "SELECT ID FROM scenarios WHERE (scenario_creator=$userID";
    if($includeNotPublished == 0)
     $query .= " AND scenario_status=1)";
    else
     $query .= ")";

    $result = mysql_query($query);
    $resultNum = mysql_num_rows($result);
    if ($resultNum == 0)
      die("<div class='centered'>No scenarios availible!</div>");
    else
      return (mysql_fetch_row($result));
  }

  // Finds nonpublished, not own scenarios
  public static function FindNotReviewed($userID=-1)
  {
    Scenario::CheckNumeric($userID);
    if($userID == -1)
      $userID = $_SESSION["ID"];

    $query = "SELECT ID FROM scenarios WHERE (scenario_creator<>$userID AND scenario_status=1)";
    $result = mysql_query($query);
    $resultNum = mysql_num_rows($result);
    if ($resultNum == 0)
      die("<div class='centered'>No scenarios availible!</div>");
    else
    {
      // Grab all the scenario ids and give them back as an array.
      $rows = array();

      while(($row = mysql_fetch_assoc($result))) {
          $rows[] =  $row["ID"];
      }

      mysql_free_result($result);
      return ($rows);
    }

  }

  public static function GetAllScenarioIDs($userID=-1)
  {
    Scenario::CheckNumeric($userID);
    if($userID == -1)
      $userID = $_SESSION["ID"];

    $query = "SELECT * FROM scenarios";
    $result = mysql_query($query);
    $resultNum = mysql_num_rows($result);
    if ($resultNum == 0)
      die("<div class='centered'>No scenarios availible!</div>");
    else
    {
      // Grab all the scenario ids and give them back as an array.
      $rows = array();

      while(($row = mysql_fetch_assoc($result))) {
          $rows[] =  $row["ID"];
      }

      mysql_free_result($result);
      return ($rows);
    }
  }

  public static function PushToDB($postStack)
  {
    if(isset($postStack["difficulty"]) == false || isset($postStack["desc"]) == false
      || isset($postStack["title"]) == false || isset($postStack["name"]) == false
      || isset($postStack["hidden-answers"]) == false || isset($postStack["prognosis-ratings"])  == false
      || isset($postStack["hidden-search"]) == false || isset($postStack["hidden-hints"]) == false)
    {
      die("<div class='centered'>Error! You must fill out all fields!</div>");
    }

    $isNew = ($postStack["updating"] == 0);
    $editID = $postStack["ID"];
    $creator = $postStack["creator"];

    $title = mysql_real_escape_string($postStack["title"]);
    $name = mysql_real_escape_string($postStack["name"]);
    $desc = mysql_real_escape_string($postStack["desc"]);

    $age = (int)($postStack["age"]);
    $height = (float)($postStack["height"]);
    $weight = (float)($postStack["weight"]);
    $bp = mysql_real_escape_string($postStack["bp"]);
    $heartRate = (int)($postStack["heartRate"]);
    $respRate = (int)($postStack["respRate"]);
    $bmi = (int)($postStack["bmi"]);
    $currentMeds = str_replace(",", "|", mysql_real_escape_string($postStack["hidden-currentMeds"]));
    $medHistory = mysql_real_escape_string($postStack["medHistory"]);
    $socHistory = mysql_real_escape_string($postStack["socHistory"]);
    $difficulty = (int)($postStack["difficulty"]);
    $timeLimit = (float)($postStack["timelimit"]);
    $timeBonus = (int)($postStack["bonusvalue"]);

    $answersStr = str_replace(",", "|", mysql_real_escape_string($postStack["hidden-answers"]));
    $answersRatingStr = str_replace(" ", "|", mysql_real_escape_string($postStack["prognosis-ratings"]));
    $searchTermsStr = str_replace(",", "|", mysql_real_escape_string($postStack["hidden-search"]));
    $hintsStr = str_replace(",", "|", mysql_real_escape_string($postStack["hidden-hints"]));

    if($isNew == false)
    {
      $result = mysql_query("UPDATE scenarios SET scenario_title='$title',patient_name='$name',patient_age=$age,patient_height=$height,patient_weight=$weight,patient_bp='$bp',patient_heartRate=$heartRate,patient_respRate=$respRate,patient_BMI=$BMI,patient_currentMeds='$currentMeds',patient_medHistory='$medHistory',patient_socHistory='$socHistory',scenario_desc='$desc',scenario_difficulty=$difficulty,scenario_hints='$hintsStr',scenario_answers='$answersStr',scenario_answer_ranking='$answersRatingStr',scenario_status=1,scenario_searchterms='$searchTermsStr',scenario_time=$timeLimit,scenario_bonus=$timeBonus WHERE ID=$editID");
      return $result;
    }
    else
    {
      $result = mysql_query("INSERT INTO scenarios VALUES($editID, '$title', '$name', $age, $height, $weight, '$bp', $heartRate, $respRate, $bmi, '$currentMeds','$medHistory','$socHistory','$desc', $difficulty, '$hintsStr', '$answersStr', '$answersRatingStr', 1, $creator, '$searchTermsStr', $timeLimit, $timeBonus)");
      if($result == TRUE)
      {
        $lastID = mysql_insert_id();
        User::AddScenariosWritten($creator, $lastID);
      }
      return $result;
    }

  }
}
