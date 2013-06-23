<?php
  require_once 'functions.php';
  include_once 'User.php';

  startSession();

  echo User::GetPoints();
?>
