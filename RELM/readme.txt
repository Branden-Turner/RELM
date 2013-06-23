Project: RELM 
Author(s): Branden Turner (brandencturner@gmail.com), Jake Leonard, David Evans

Notes:
Project Languages: Mainly PHP, with pieces of Javascript (with jQuery), HTML5, and MySQL as the database

Information for the datbase can be found in Setup.php ( for the structure ), or simply access the server ( using a Putty Generated RSA key ) and use the following information, or use this information to set up your local database for testing (using something like xampp)
dbhost  = 'localhost'; 
dbname  = 'relmDB';
dbuser  = 'relmUser';
dbpass  = 'realMedicine2013';
appname = "RELM";

Example queries can be found in relmgame/queries.txt

Game Logic:

Contained in Game.php, Game.js, and after the answers have been selected, GameEnd.php

Transaction Logic:

To veiw the database logic, Scenario.php and User.php contain most of the codebase for that.

TODO:
When testing is over, uncomment lines 201-203 in Scenario.php to get the intended progression logic out of the game.
Add touch support to the matching game ( matchingGame.php ), there are openly available touch/javascript plugins, I didn't have time to find a suitable match.
Fix the bug where the matching game resets on win.
Instead of a difficulty select at the beginning of the game, display a map of a hospital where each room is a different case, selecting a room causes information to popup
Make sure touch is supported throughout the game.  
Another UI pass, making the play button bigger and adding better, clearer site navigation