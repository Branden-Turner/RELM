<?php

include_once 'header.php';

if (!$loggedin)
  die("You must be logged in to view this!");

echo "<div class='page-header'>Case Review</div>";

if(User::GetReviewFlag() == 0)
  die("You do not have the proper permissions to review cases");

if(isset($_POST["msg"]) && isset($_POST["submit"]))
{
  $curId = $_POST["id"];

  $newStatus = (isset($_POST["status"])) ? 0 : 3;
  if($_SESSION['ID'] == Scenario::GetCreator($curId))
    die("You cannot review your own case!");

  Scenario::SetStatus($curId, $newStatus, $_POST["msg"]);

  echo "Case has been updated! <a href='review.php'>Go back to the review list</a>";
  return;
}

/* Print out list */
if(isset($_GET["id"]) == false)
{
  $nonReviewed = Scenario::FindNotReviewed();
  ?>
  <table>
  <tr>
  <th>ID</th>
  <th>Case Name</th>
  <th>Creator</th>
  <th>Link</th>
  </tr>

<?php

  for($i = 0; $i < sizeof($nonReviewed); ++$i)
  {
    $curId = $nonReviewed[$i];
    $creatorUserName = Scenario::GetCreatorName($curId);
    echo "<tr><td>$curId</td><td>".Scenario::GetTitle($curId)."</td><td><a href='members.php?view=".$creatorUserName."'>".$creatorUserName."</a></td><td><a href='review.php?id=$curId'>Click</a></td></tr>";
  }

  echo "</table>";
  return;
}
else /* Info page */
{
  $curId = $_GET["id"];

  if($_SESSION['ID'] == Scenario::GetCreator($curId))
    die("You cannot review your own scenario!");

  $hints = Scenario::GetHints($curId);
  $patientInfo = Scenario::GetPatientInfo($curId);
  $bestAnswers = Scenario::GetAnswerRankings($curId);
  $answers = Scenario::GetAnswers($curId);
  $creatorUserName = Scenario::GetCreatorName($curId);
  $bonusData = Scenario::GetBonusData($curId);

?>
  <table>
  <tr><th>Title:</th><td><?php echo Scenario::GetTitle($curId); ?></td></tr>
  <tr><th>Difficulty:</th><td><?php echo Scenario::GetDifficultyText($curId); ?></td></tr>
  <tr><th>Creator:</th><td><a href="members.php?view=<?php echo $creatorUserName; ?>"><?php echo $creatorUserName; ?></a></td></tr>
  <tr><th>Desc:</th><td><?php echo Scenario::GetDesc($curId); ?></td></tr>
  <tr><th>Patient Info:</th><td>Name: <?php echo $patientInfo[0]; ?><br />Age: <?php echo $patientInfo[1]; ?><br />Height & Weight: <?php echo $patientInfo[2]." ".$patientInfo[3]; ?><br />Blood Pressure: <?php echo $patientInfo[4]; ?><br />Heart Rate: <?php echo $patientInfo[5]; ?><br />Respiratory Rate: <?php echo $patientInfo[6]; ?><br />BMI: <?php echo $patientInfo[7]; ?><br />Current Medications: <?php echo $patientInfo[8]; ?><br />Medical History: <?php echo $patientInfo[9]; ?> <br />Social History: <?php echo $patientInfo[10]; ?></td></tr>
  <tr><th>Search Terms:</th><td><?php echo implode(Scenario::GetSearch($curId)); ?></td></tr>
  <tr><th>Hints:</th><td><?php

  for($g = 0; $g < sizeof($hints); ++$g)
    echo $hints[$g]."<br />";

  ?></td></tr>
  <tr><th>Best Answer:</th><td><?php echo $answers[$bestAnswers[0]]; ?></td></tr>
  <tr><th>2nd Best Answer:</th><td><?php echo $answers[$bestAnswers[1]]; ?></td></tr>
  <tr><th>3rd Best Answer:</th><td><?php echo $answers[$bestAnswers[2]]; ?></td></tr>
  <tr><th>Other Answers:</th><td><?php

  for($h = 0; $h < sizeof($hints); ++$h)
  {
    if($h != $bestAnswers[0] && $h != $bestAnswers[1] && $h != $bestAnswers[2])
      echo $answers[$h]."<br />";
  }
  ?></td></tr>
  <tr><th>Bonus Time</th><td><?php echo $bonusData[0]; ?></td></tr>
  <tr><th>Bonus Points</th><td><?php echo $bonusData[1]; ?></td></tr>
  </table>
<form action="review.php" method="POST">
<input type="hidden" name="id" value="<?php echo $_GET["id"]; ?>" />
Approve this case? <input type="checkbox" name="status" /><br />
Input a message: <input type="textbox" name="msg" /><br />
<input type="submit" name="submit" value="Submit!" />
</form>
<?php } ?>
