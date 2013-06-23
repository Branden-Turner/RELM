<?php
include_once 'header.php';

if (!$loggedin)
  die("You need to be logged in to access this page");

if(User::GetHighestDifficulty($_SESSION["ID"]) != 3)
  die("You are not able to create scenarios yet!");

$title = "";
$desc = "";
$creator = $_SESSION["ID"];
$isUpdating = 0;
$scenID = 0;
$patientName = "";
$patientAge = "";
$patientHeight = "";
$patientWeight = "";
$patientBP = "";
$patientHeartRate = 0;
$patientRespRate = 0;
$patientBMI = 0;
$patientCurrentMeds = "";
$patientMedHistory = "";
$patientSocHistory = "";
$searchTerms = "";
$bonusTime = 0;
$bonusValue = 0;
$difficulty = 0;
$submitText = "Create Case";

if(isset($_GET["edit"]))
{
  if(Scenario::GetCreator($_GET["edit"]) != $_SESSION["ID"])
    die("You cannot edit other people's scenarios!");

  if(Scenario::GetStatus($_GET["edit"]) == 0)
    die("You cannot edit a status that is currently live!");

  $scenID = $_GET["edit"];
  $creator = Scenario::GetCreator($scenID);
  $isUpdating = 1;
  $submitText = "Update Scenario";
  $difficulty = Scenario::GetDifficulty($scenID);
  $title = Scenario::GetTitle($scenID);
  $desc = Scenario::GetDesc($scenID);
  $patientData = Scenario::GetPatientInfo($scenID);

  $patientName = $patientData[0];
  $patientAge = $patientData[1];
  $patientHeight = $patientData[2];
  $patientWeight = $patientData[3];
  $patientBP = $patientData[4];
  $patientHeartRate = $patientData[5];
  $patientRespRate = $patientData[6];
  $patientBMI = $patientData[7];
  $patientCurrentMeds = $patientData[8];
  $patientMedHistory = $patientData[9];
  $patientSocHistory = $patientData[10];

  $bonusInfo = Scenario::GetBonusData($scenID);
  $bonusTime = $bonusInfo[0];
  $bonusValue = $bonusInfo[1];
  $searchTerms = Scenario::GetSearch($scenID);
}
else if(isset($_POST["submit"]))
{
  if($_POST["creator"] != $_SESSION["ID"])
    die("You cannot make scenarios as other people!");

  if($_POST["ID"] != 0 && Scenario::GetStatus($_POST["ID"]) == 0)
    die("You cannot edit a status that is currently live!");

  $returnd = Scenario::PushToDB($_POST);
  $scenID = $_POST["ID"];

  if($returnd)
  {
    $wasUpdated = ($_POST["updating"]) ? "Scenario Updated!" : "Scenario Created!";
    die("$wasUpdated Return to the <a href='members.php'>main page</a>.");
  }
  else
  {
    if($_POST["updating"] == 1)
      die("Failed to save scenario. Please <a href='builder.php?edit=$scenID'>click here</a> to go back and try again.");
    else
      die("Failed to create scenario, please go back and double check your inputs.");
  }
}

?>

<div>

  <div class='page-header'>Case Builder</div>

  <form action='builder.php' method='post'>
  <?php /* Don't you dare touch this */ ?>
  <input type="hidden" name="creator" value="<?php echo $creator; ?>"/>
  <input type="hidden" name="ID" value="<?php echo $scenID; ?>" />
  <input type="hidden" name="updating" value="<?php echo $isUpdating; ?>" />

    <div class='input-block'>
      <div class='input-header'>If you have any issues with any of the items, simply hover over the item to get some information.</div>
      </br>
      <div class='input-header' title="This should be a name that attempts to summarize the point of your case, while not giving away any key details as to the method of treatment.">Title of Case</div>
      <div><input type='text' name='title' maxlength='124' value='<?php echo $title; ?>' /></div>
    </div>

    <div class='input-block'>
      <div class='input-header' title="Provide a succinct description of the situation and give some details that will start the player's searches.">Brief description of case</div>
      <textarea style='height: 120px;' name='desc' rows='65' cols='75'><?php echo $desc; ?></textarea>
    </div>

    <div class='input-block'>
      <div class='input-header' title="This is the patient's basic information so that the players can figure out more about their possible treatment.">Patient Information</div>
      <div>Name <input type='text' name='name' maxlength='124' value='<?php echo $patientName; ?>' /></div>
      <div>Age <input type='text' name='age' maxlength='124' value='<?php echo $patientAge; ?>' /> years</div>
      <div>Height <input type='text' name='height' maxlength='124' value='<?php echo $patientHeight; ?>' /> inches</div>
      <div>Weight <input type='text' name='weight' maxlength='124' value='<?php echo $patientWeight; ?>' /> lbs</div>
      <div>Blood Pressure <input type='text' name='bp' maxlength='124' value='<?php echo $patientBP; ?>' /> mm Hg</div>
      <div>Heart Rate <input type='text' name='heartRate' maxlength='124' value='<?php echo $patientHeartRate; ?>' /> bpm</div>
      <div>Respiratory Rate <input type='text' name='respRate' maxlength='124' value='<?php echo $patientRespRate; ?>' /> bpm</div>
      <div>BMI <input type='text' name='bmi' maxlength='124' value='<?php echo $patientBMI; ?>' /></div>
      <div>Add Current Medications (press enter to add)</div>
      <div><input id='currentMeds' name='currentMeds' cols='25' rows='12'></input></div>
      <div>Past Medical History</div>
      <textarea style='height: 120px;' name='medHistory' rows='65' cols='75'><?php echo $patientMedHistory; ?></textarea>
      <div>Social History</div>
      <textarea style='height: 120px;' name='socHistory' rows='65' cols='75'><?php echo $patientSocHistory; ?></textarea>

    </div>

    <div id='prognosis-block' class='input-block'>
      <div class='input-header' title="Try to give between 4 and 7 treatment options.  You want them to sound plausible so that a doctor who isn't paying attention might make a mistake and learn.">Add Treatment Plan Choices ( Press Enter to add items to list )</div>
      <div>After all plans are added, click them and decide which is your best answer, and which is a good answer.</div>
      <input id='prognosis-bank' name='answers' cols='25' rows='12'></textarea>
      <input name='prognosis-ratings' id='prognosis-ratings' type="hidden" />
      <div id='prognosis-options'>
        <span>Best<input id='prognosis-best' name='prognosis-rating' type="radio" /></span>
        <span>Good<input id='prognosis-good' name='prognosis-rating' type="radio" /></span>
      </div>
    </div>

    <div class='input-block'>
      <div class='input-header' title="Players just starting will play Easy to get a few points, where intermediate and advanced cases should be worth considerably more points.">Select Difficulty</div>
      <?php
      if($difficulty == 1)
        echo '<input type="radio" name="difficulty" value="1" checked>Easy</input>';
      else
        echo '<input type="radio" name="difficulty" value="1">Easy</input>';

      if($difficulty == 2)
        echo '<input type="radio" name="difficulty" value="2" checked>Medium</input>';
      else
        echo '<input type="radio" name="difficulty" value="2">Medium</input>';

      if($difficulty == 3)
        echo '<input type="radio" name="difficulty" value="3" checked>Hard</input>';
      else
        echo '<input type="radio" name="difficulty" value="3">Hard</input>';
      ?>
    </div>


    <div class='input-block'>
      <div class='input-header' title="This is for search terms that might be used at a website like PubMed or DynaMed.  This should get the player pointed in the right direction.">Add Helpful Search Terms/Links</div>
      <input id='search-terms' name='search' cols='25' rows='12'></input>
    </div>

    <div class='input-block'>
      <div class='input-header' title="These should really help the player narrow down a treatment option as they're penalized for using hints.">Add Hints</div>
      <input id='hints' name='hints' cols='25' rows='12'></input>
    </div>

    <div class='input-block'>
      <div class='input-header'>Bonus Time Limit ( in seconds )</div>
      <input type='text' name='timelimit' maxlength='124' value='<?php echo $bonusTime; ?>'/>
      <div class='input-header'>Bonus Point Value</div>
      <input type='text' name='bonusvalue' maxlength='124' value='<?php echo $bonusValue; ?>' />
    </div>

    <div class='input-block'>
      <input class='input-button' type='submit' name='submit' value='<?php echo $submitText; ?>' />
    </div>
  </form>

</div>

<script>
  $("#prognosis-bank").tagsManager({"tagClass": "bad-tag prognosis-tag"});
  $("#search-terms").tagsManager();
  $("#hints").tagsManager();
  $("#currentMeds").tagsManager();

  var selectedAnswer = null;
  var bestAnswer = null;
  var goodAnswer = null;

  function SetRating(tag, rating)
  {
    tag.removeClass("best-tag");
    tag.removeClass("good-tag");
    tag.removeClass("bad-tag");

    tag.addClass(rating + "-tag");
    tag.data("answerQuality", rating);

    if (rating === "best" && bestAnswer)
    {
      bestAnswer.removeClass("best-tag");
      bestAnswer.addClass("bad-tag");
      bestAnswer.data("answerQuality", "bad");
    }
    else if (rating === "good" && goodAnswer)
    {
      goodAnswer.removeClass("good-tag");
      goodAnswer.addClass("bad-tag");
      goodAnswer.data("answerQuality", "bad");
    }

    if (rating === "best")
      bestAnswer = tag;
    else if (rating === "good")
      goodAnswer = tag;

    UpdateRatings();
  }

  $("#prognosis-block").bind("click", function(e)
  {
    var clicked = $(e.target);
    if (clicked.hasClass("myTagRemover"))
    {
      setTimeout(UpdateRatings, 10);
      return;
    }

    if ($(e.target).parent().hasClass("myTag"))
      clicked = $(e.target).parent();
    if (clicked.hasClass("myTag"))
    {
      selectedAnswer = clicked;
      $(".myTag").removeClass("selected-tag");
      clicked.addClass("selected-tag");
    }
    else
    {
      selectedAnswer = null;
    }

    if (selectedAnswer)
    {
      $("#prognosis-options").css("display", "block");
      if (selectedAnswer.data("answerQuality") === "best")
        $("#prognosis-best").attr("checked", true);
      else
        $("#prognosis-best").attr("checked", false);

      if (selectedAnswer.data("answerQuality") === "good")
        $("#prognosis-good").attr("checked", true);
      else
        $("#prognosis-good").attr("checked", false);
    }
    else
    {
      $("#prognosis-options").css("display", "none");
      $(".myTag").removeClass("selected-tag");
    }
  });

  function UpdateRatings()
  {
    $("#prognosis-ratings").val("");
    $(".prognosis-tag").each(function(i)
    {
      if ($(this).data("answerQuality") === "best")
        $("#prognosis-ratings").val(i);
    });

    $(".prognosis-tag").each(function(i)
    {
      if ($(this).data("answerQuality") === "good")
        $("#prognosis-ratings").val($("#prognosis-ratings").val() + " " + i);
    });

    $(".prognosis-tag").each(function(i)
    {
      if ($(this).data("answerQuality") !== "good" && $(this).data("answerQuality") !== "best")
        $("#prognosis-ratings").val($("#prognosis-ratings").val() + " " + i);
    });
  }

  $("#prognosis-best").bind("change", function()
  {
    SetRating(selectedAnswer, "best");
  });
  $("#prognosis-good").bind("change", function()
  {
    SetRating(selectedAnswer, "good");
  });
  </script>
  <script>
  <?php
    if($isUpdating == 1)
    {
      echo "function loadOptions() {\n";
      echo "\n";
      $answers = Scenario::GetAnswers($scenID);
      echo "var answerString = '';";
      foreach($answers as $answer)
      {
        echo "$('#prognosis-bank').tagsManager('pushTag',".json_encode($answer).");\n";
        echo "answerString += " . json_encode($answer) . ";";
        echo "answerString += ' ';";
      }
      echo "$('#prognosis-bank').val(answerString);";

      $hints = Scenario::GetHints($scenID);
      echo "var hintString = '';";
      foreach($hints as $hint)
      {
        echo "$('#hints').tagsManager('pushTag',".json_encode($hint).");\n";
        echo "hintString += " . json_encode($hint) . ";";
        echo "hintString += ' ';";
      }
      echo "$('#hints').val(hintString);";

      $searchs = Scenario::GetSearch($scenID);
      echo "var searchString = '';";
      foreach($searchs as $search)
      {
        echo "$('#search-terms').tagsManager('pushTag',".json_encode($search).");\n";
        echo "searchString += " . json_encode($search) . ";";
        echo "searchString += ' ';";
      }
      echo "$('#search-terms').val(searchString);";

      $patientInfo = Scenario::GetPatientInfo($scenID);
      $curMeds = $patientInfo[8];
      echo "var medString = '';";
      foreach($curMeds as $curMed)
      {
        echo "$('#currentMeds').tagsManager('pushTag',".json_encode($curMed).");\n";
        echo "medString += " . json_encode($curMed) . ";";
        echo "medString += ' ';";
      }
      echo "$('#currentMeds').val(medString);";

      $ratingsVal = Scenario::GetAnswerRankings($scenID);
      $best = $ratingsVal[0];
      $good = $ratingsVal[1];
      echo "SetRating($('.prognosis-tag:eq($best)'), 'best');\n";
      echo "SetRating($('.prognosis-tag:eq($good)'), 'good');\n";
      echo "\n}\n";
      echo "setTimeout(loadOptions, 10);";
    }
  ?>
</script>

</body>
</html>
