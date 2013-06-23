var bonusTimer = 0;
var currentHint = 0;
var usedHint = false;
var hintsArray = [];
var searchTerms = [];

function startBonusTimer(startingValue)
{
  bonusTimer = startingValue;
  $("#timer").text(bonusTimer);

  setInterval(function()
  {
    if (bonusTimer > 0)
      --bonusTimer;

    $("#timer").text(bonusTimer);
  }, 1000);
}

function ajaxRequest()
{
    try { var request = new XMLHttpRequest() }
    catch(e1) {
        try { request = new ActiveXObject("Msxml2.XMLHTTP") }
        catch(e2) {
            try { request = new ActiveXObject("Microsoft.XMLHTTP") }
            catch(e3) {
                request = false
    }   }   }
    return request
}