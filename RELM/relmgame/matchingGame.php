<?php
include_once 'header.php';
require_once 'functions.php';
include_once 'User.php';
include_once 'Scenario.php';

if (!$loggedin)
die("You must be logged in to view this page");

$patient = "";
$intervention = "";
$comparison = "";
$outcome = "";
$matchStr = "";

$patients = [];
$patients[] = "30 year old male with Bell's Palsy";
$patients[] = "65 year old male patient with hypertension";
$patients[] = "person with actinic keratosis and sun exposure";
$patients[] = "person with lower back pain of 10 weeks";
$patients[] = "65 year old male with episode of acute confusion";
$patients[] = "adult with social phobia";

$interventions = [];
$interventions[] = "corticosteroids";
$interventions[] = "a statin drug, rosuvastatin";
$interventions[] = "none";
$interventions[] = "physical therapy";
$interventions[] = "screening test for dementia";
$interventions[] = "paroxetine";


$comparisons = [];
$comparisons[] = "no corticosteroids";
$comparisons[] = "no statin drug";
$comparisons[] = "no sun exposure and no skin lesion";
$comparisons[] = "no physical therapy";
$comparisons[] = "none";
$comparisons[] = "no medication";

$outcomes = [];
$outcomes[] = "reduction of symptoms";
$outcomes[] = "prevention of myocardial infarction";
$outcomes[] = "squamous cell skin cancer";
$outcomes[] = "pain reduction and improved functionality";
$outcomes[] = "accurate diagnosis";
$outcomes[] = "improvement in social phobia symptoms";

$matchStrs = [];
$matchStrs[] = "Do corticosteroids aid in reduction of Bell's Paulsy symptoms in a 30 year old male?";
$matchStrs[] = "Does a statin drug aid in preventing myocardial infarction in a 65 year old hypertensive male?";
$matchStrs[] = "Does prolonged sun exposure lead to squamous cell skin cancer?";
$matchStrs[] = "Does physical therapy help patients with lower back pain?";
$matchStrs[] = "Which screen tool is best for a 66 year old man with an episode of acute confustion?";
$matchStrs[] = "In patients with social phobia, does paroxetine result in improvement of social phobia symptoms?";

$chosen = rand(0, 5);

$patient = $patients[$chosen];
$intervention = $interventions[$chosen];
$comparison = $comparisons[$chosen];
$outcome = $outcomes[$chosen];
$matchStr = $matchStrs[$chosen];

function EndGame($searchString)
{
  $matchingString = $GLOBALS['matchStr'];

  if( $searchString === $matchingString )
  {
    echo "You built your search string perfectly!  Enjoy the points!";
    User::SetPoints(-1, 50);

    ?>
    <script>
    O("searchEnter").remove();
    </script>
    <?php

  }
  else
  {
    echo "Sorry, the correct search string is: ";
    echo $matchingString;
  }
}

?>

<style type="text/css">
#WordBank
{
  width:200px;
  height:200px;
  border:1px solid #aaaaaa;
  border-radius: 3px;
  margin: auto;
  margin-top: 1cm;
  padding:10px;
}

#Patient, #Intervention, #Comparison, #Outcome
{
  float:left;
  width:200px;
  height:200px;
  margin:10px;
  margin-top: 1cm;
  padding:10px;
  border:1px solid #aaaaaa;
  border-radius: 3px;
}

#score
{
  width:100px;
  text-align: center;
  margin: auto;
  padding:10px;
}

#searchEnter
{
  width:400px;
  margin: 10px;
  padding:10px;
}
</style>

<script>

function log(message)
{
  if(typeof console == "object")
  {
    console.log(message);
  }
}

var dragDropLogic =
{
    matches: 0,
    numWords: 4,
    tries:0
}

var matchHashTable = {};

function allowDrop(event)
{
  event.preventDefault();
}

function drag(event)
{
  event.dataTransfer.setData("text/plain", event.target.getAttribute('id'));
}

function win()
{
  O("score").innerHTML = "You matched everything in " + dragDropLogic.tries + " tries!";
}

function updateScore()
{
  O("score").innerHTML = dragDropLogic.matches + " / " + dragDropLogic.numWords;

  // Check for a win.
  if( dragDropLogic.matches == dragDropLogic.numWords )
  {
      win();
  }
}

function dragLeave(event)
{
  var textId = event.dataTransfer.getData('text/plain');
  var text = O(textId);

  log(textId);

  // Check to see if we have a match. Also, were we dropped here?
  if( text.className == event.target.getAttribute('id') && matchHashTable[textId] == true )
  {
    dragDropLogic.matches--;
    matchHashTable[textId] = false;
  }

  event.preventDefault();
}


function drop(event)
{
  dragDropLogic.tries += 1;
  var textId = event.dataTransfer.getData("text/plain");
  var text = O(textId);

  // Are we dropping this into a match? Also, were we just dropped here?
  if( text.className == event.target.getAttribute('id') && matchHashTable[textId] != true )
  {
    dragDropLogic.matches++;
    matchHashTable[ textId ] = true;
  }

  event.target.appendChild(text);

  updateScore();
  event.preventDefault();
}

</script>

<div class='page-header'>PICO Matching</div>

<div id="WordBank" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)">
  <label>Word Bank</label>
            <li id="li1_c" class="Outcome" draggable = "true" style="cursor:move"  ondragstart="drag(event)" >
                <?php echo $outcome; ?>
            </li>
            <li id="li1_b" class="Intervention" draggable = "true" style="cursor:move"  ondragstart="drag(event)">
                <?php echo $intervention; ?>
            </li>
            <li id="li1_g" class="Comparison" draggable = "true" style="cursor:move"  ondragstart="drag(event)">
                <?php echo $comparison; ?>
            </li>
            <li id="li1_a" class="Patient" draggable="true" style="cursor:move"  ondragstart="drag(event)">
                <?php echo $patient; ?>
            </li>
</div>

<div id="Patient" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)"><label>Patient</label></div>
<div id="Intervention" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)"><label>Intervention</label></div>
<div id="Comparison" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)"><label>Comparison</label></div>
<div id="Outcome" ondrop="drop(event)" ondragover="allowDrop(event)" ondragleave="dragLeave(event)"><label>Outcome</label></div>

<div id="score">0 / 4</div>

<div id="searchEnter">
  <form action="" method="post">
      Question String:  <input type="text" name="searchStr" /><br />
      <input class='input-button' type="submit" name="submit" value="Submit Search String" />
  </form>
</div>

<div id='menu-block'>
  <div class="not-selectable" id='how-to-play'>How to Play</div>
</div>

<div id='menu-expanders'>
  <div class='ui-expander' id='how-to-play-expander'>
    <p>
    Doctor, the object of this game is to get you familiar with the PICO ( Patient, Intervention, Comparison, Outcome ) categorization of searching for evidence based medicine.
    After matching the words with their proper categories, try building a good question based on the terms you're given.  Type it in, and submit the string to see how close you were!  If you correctly construct the string, you'll get some points.
    </p>
  </div>
</div>

<script>
  $("#menu-block").children().each(function()
  {
    var expanderId = "#" + $(this).attr("id") + "-expander";
    $(this).bind("mouseleave", function()
    {
      $(expanderId).fadeOut(250);
      $(expanderId).data("visible", false);
    });

    $(this).bind("mouseenter", function()
    {
      $(expanderId).fadeIn(250);
      $(expanderId).data("visible", true);
    });
  });
</script>

<?php




  if(isset($_POST['submit']))
  {
    if(isset($_POST['searchStr']) && $_POST['searchStr'] != "")
    {
      EndGame($_POST['searchStr']);
    }
    else
    {
      echo "Please Enter a Question String after matching the items with their categories.";
    }
  }
?>


</div>
</body>
</html>

