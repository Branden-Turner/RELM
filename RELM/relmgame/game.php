<?php
include_once 'header.php';

if (!$loggedin)
  die("You must be logged in before you can play the game!");

if(isset($_POST["difficulty"]) == false)
{
?>
  <div class='page-header'>Difficulty Select</div>
  <form class='centered' method="post" action="game.php">
  <select name="difficulty">
    <option value="1">Easy</option>
    <option value="2">Medium</option>
    <option value="3">Hard</option>
  </select>

  <input class='input-button' type="submit" name="submit" value="Begin Case!" />
  </form>
<?php
  return;
}

$scenarioID = Scenario::Find($_SESSION["ID"], $_POST["difficulty"]);

// Load a scenario the player hasn't played
$scenarioName = Scenario::GetTitle($scenarioID); // string
$scenarioDesc = Scenario::GetDesc($scenarioID); // string
$scenarioCreator = Scenario::GetCreatorName($scenarioID); // string
$patientInfo = Scenario::GetPatientInfo($scenarioID); // array (Name, Age, Height, Weight)
$answers = Scenario::GetAnswers($scenarioID); // Strings
$hintsArray = Scenario::GetHints($scenarioID); // Strings
$bonusData = Scenario::GetBonusData($scenarioID); // Time then points
$searchTerms = Scenario::GetSearch($scenarioID); // Array of strings
$difficulty = $_POST["difficulty"];
$currentHint = 0;
shuffle($answers);

?>

<!-- start the game logic -->
<script src='game.js'></script>

<div>

  <div class='page-header'><?php echo $scenarioName ?></div>

  <div id='timer-and-treatment'>
  <div id='timer-container'>
    <div id='timer-text'>Time: </div>
    <div id='timer'>0</div>
  </div>

<?php
$overview =
"<div><span class='bold'> Name </span>".$patientInfo[0]."</div>".
"<div><span class='bold'> Age </span>".$patientInfo[1]."</div>".
"<div><span class='bold'> Height </span>".$patientInfo[2]." inches</div>".
"<div><span class='bold'> Weight </span>".$patientInfo[3]." lbs</div>".
"<div><span class='bold'> Blood Pressure </span>".$patientInfo[4]."mm Hg</div>".
"<div><span class='bold'> Heart Rate </span>".$patientInfo[5]."bpm</div>".
"<div><span class='bold'> Respiratory Rate </span>".$patientInfo[6]."bpm</div>".
"<div><span class='bold'> BMI </span>".$patientInfo[7]."</div>".
"<div><span class='bold'> Current Medications </span>".$patientInfo[8]."</div>".
"<div><span class='bold'> Medical History </span>".$patientInfo[9]."</div>".
"<div><span class='bold'> Social History </span>".$patientInfo[10]."</div>";
?>

  <div id='treatment' class='not-selectable'>
    <span>Select Treatment Plan</span>
    <ul id='treatment-options'>
    <?php
    for($i = 0; $i < sizeof($answers); ++$i)
      echo "<li class='treatment-option'>".$answers[$i]."</li>";
    ?>
    </ul>
  </div>

  <script>
    startBonusTimer(<?php echo $bonusData[0]; ?>);
    hintsArray = <?php echo json_encode($hintsArray); ?>;
    searchTerms = <?php echo json_encode($searchTerms); ?>;

    $(".treatment-option").bind("click", function()
    {
      var clicked = $(this);
      $.ajax(
      {
        type: "POST",
        url: "gameend.php",
        data:
        {
          "scenarioID": <?php echo $scenarioID; ?>,
          "difficulty": <?php echo $difficulty; ?>,
          "gotBonus": bonusTimer > 0 ? true : false,
          "usedHint": usedHint,
          "answer": clicked.text()
        }
      }).done(function(html)
      {
        $("#results").empty();
        $("#results").append(html);
        $("#treatment").remove();

        $.ajax(
        {
          type: "POST",
          url: "getpoints.php"
        }).done(function(points)
        {
          $("#userPoints").text(points);
        });
      });
    });
  </script>

</div>
<div id='menu-block'>
  <div class="not-selectable" id='how-to-play'>How to Play</div>
  <div class="not-selectable" id='similar-cases'>Similar Cases</div>
  <div class="not-selectable" id='get-hint'>Get Hint</div>
  <div class="not-selectable" id='patient-file'>Patient File</div>
</div>


<div id='menu-expanders'>
  <div class='ui-expander' id='patient-file-expander'>
    <?php
      echo $overview;
    ?>
  </div>

  <div class='ui-expander' id='get-hint-expander'>
    Click to reveal the first hint and lower your score.
  </div>

  <div class='ui-expander' id='similar-cases-expander'>
    <p>
    Here we could just have the creators add links to cases that would seem pertinent.  That could be fine for an easy difficulty, but in harder difficulties we could give them some search terms to use.
    </p>
  </div>

  <div class='ui-expander' id='how-to-play-expander'>
    <p>
    Doctor, your goal today is to treat the patient that has been presented to you.  In doing this, you're allowed to use the internet to research the best course of treatment.
    Read the patient's file, the case overview, get hints, and make sure to reference the helpful search terms to decide on a course of treatment.  Once you've decided, click "Select Treatment Plan" to choose your treatment from a list of options.
    </p>
  </div>
</div>

<script>
  $("#similar-cases-expander").empty();
  $("#similar-cases-expander").append("<div><a href='http://www.ncbi.nlm.nih.gov/pubmed/' target='blank'> PubMed </a></div>");
  $("#similar-cases-expander").append("<div><a href='https://dynamed.ebscohost.com/' target='blank'> Dynamed </a></div>");


  for (var i in searchTerms)
    $("#similar-cases-expander").append("<div>" + searchTerms[i] + "</div>");
  $("#menu-block").children().each(function()
  {
    var expanderId = "#" + $(this).attr("id") + "-expander";
    $(this).bind("mouseenter", function()
    {
      $(expanderId).fadeIn(250);
      $(expanderId).data("visible", true);
    });

    function HandleLeave()
    {
      if (!$(expanderId).is(":hover"))
      {
        $(expanderId).fadeOut(250);
        $(expanderId).data("visible", false);
      }
    }

    $(this).bind("mouseleave", HandleLeave);
    $(expanderId).bind("mouseleave", HandleLeave);
    $("#get-hint").bind("click", function()
    {
      if ($(this).attr("id") == "get-hint")
      {
        $("#get-hint-expander").empty();
        $("#get-hint-expander").append(hintsArray[currentHint]);
        ++currentHint;
        if (currentHint >= hintsArray.length)
          currentHint = 0;
        usedHint = true;
      }
    });
  });

  $("#treatment").bind("click", function()
  {
    $("#treatment-options").slideToggle(250);
    $("#treatment-options").data("visible", false);
  });
</script>

<div id='patient-overview'>
  <div>Patient Overview</div>
  <textarea id='scenario-desc-text' readonly='yes' name='text' cols='35' rows='12'><?php echo $scenarioDesc; ?></textarea>
</div>

<div id='results'>
</div>

</div>
</body>
</html>