<?php
  require_once 'functions.php';
  include_once 'User.php';
  include_once 'Scenario.php';

  startSession();

  $scenarioID = $_POST["scenarioID"];
  $gotBonus = $_POST["gotBonus"] == "true" ? true : false;
  $usedHint = $_POST["usedHint"] == "true" ? true : false;
  $answers = Scenario::GetAnswers($scenarioID);
  $ratings = Scenario::GetAnswerRankings($scenarioID);
  $bonusData = Scenario::GetBonusData($scenarioID);

  $answer = $_POST["answer"];
  $best = $answers[$ratings[0]];
  $good = $answers[$ratings[1]];
  $correct = false;

  if ($answer == $best)
    $correct = true;
  else if ($answer == $good)
    $correct = true;

  $result = "That was not an acceptable treatment.";
  if ($correct && $answer == $best)
    $result = "You chose the correct treatment!";
  else if ($correct && $answer == $good)
    $result = "You chose an acceptable treatment.";

  if ($correct && $gotBonus)
  {
    $result = $result . " + Time bonus!";

    if ($answer == $best)
      User::SetPoints(-1, $bonusData[1]);
    if ($answer == $good)
      User::SetPoints(-1, $bonusData[1] * 0.5);
  }
  if ($correct)
  {
    if ($answer == $best)
      User::SetPoints(-1, 750);
    else if ($answer == $good)
      User::SetPoints(-1, 500);

    if ($usedHint)
      User::SetPoints(-1, -200);
    User::AddScenariosCompleted(-1, $scenarioID);
  }
?>

<div class='page-header'><?php echo $result; ?></div>