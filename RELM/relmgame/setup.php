<html><head><title>Setting up database</title></head><body>

<h3>Setting up...</h3>

<?php 

include_once 'functions.php';

createTable('members',
            'user VARCHAR(16),
            pass VARCHAR(16),
            ID INT UNSIGNED AUTO_INCREMENT KEY,
            reviewflag INT(1),
            highdiff INT(11),
            points INT(11),
            completed VARCHAR(9000),
            created VARCHAR(9000),
            INDEX(user(6))');

createTable('scenarios', 
            'ID INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            scenario_title VARCHAR(1000),
            patient_name VARCHAR(255),
            patient_age INT(3),
            patient_height FLOAT,
            patient_weight FLOAT,
            patient_bp VARCHAR(25),
            patient_heartRate INT(3),
            patient_respRate INT(3),
            patient_BMI INT(3),
            patient_currentMeds VARCHAR(5000),
            patient_medHistory VARCHAR(5000),
            patient_socHistory VARCHAR(5000),
            scenario_desc VARCHAR(1000),
            scenario_difficulty INT(1) UNSIGNED,
            scenario_hints VARCHAR(5000),
            scenario_answers VARCHAR(9000),
            scenario_answer_ranking VARCHAR(25),
            scenario_status INT(10) UNSIGNED,
            scenario_creator INT(10) UNSIGNED,
            scenario_searchterms VARCHAR(2000),
            scenario_time FLOAT UNSIGNED,
            scenario_bonus INT(10) UNSIGNED');

createTable('messages', 
            'id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            auth VARCHAR(16), 
            recip VARCHAR(16), 
            pm CHAR(1),
            time INT UNSIGNED, 
            message VARCHAR(4096),
            INDEX(auth(6)), 
            INDEX(recip(6))');

createTable('profiles',
            'user VARCHAR(16),
            text VARCHAR(4096),
            INDEX(user(6))');
?>

<br />...done.
</body></html>
