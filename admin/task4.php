<?php

include '../dbconnect.php';
$dbConnection = new Dbconnect();

// echo "<pre>";
// echo print_r($dbConnection->getAllPlayersAnswersFromTask(1, 2), true);
// echo "</pre>";
// $dbConnection->getAllPlayersAnswersFromTask(2, 2);
// $dbConnection->getAllPlayersAnswersFromTask(3, 2);
// $dbConnection->getAllPlayersAnswersFromTask(4, 2);
// $dbConnection->getAllPlayersAnswersFromTask(5, 2);

if ((isset($_POST['passbutton']) || isset($_POST['failbutton'])) && $_POST['groupId'] !== '') {
  if (isset($_POST['passbutton'])) {
    $result = 'pass';
  } else {
    $result = 'fail';
  }
  $dbConnection->setGroupResultTask4(trim($_POST['groupId']), $result);
}

?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="../bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="../mystyle.css">
	<script defer src="../fontawesome/js/all.js"></script>
	<title>Page Title</title>
</head>
<body>
<button> <a href="newGame.php"> New Game</a> </button>
<button> <a href="results.php"> Results </a> </button>
<button> <a href="task4.php"> Task 4 </a> </button>

<h1>Task 4 Team Result</h1>

<form method="post">
  Enter Group ID
  <input name="groupId" type="text">
  <button name="passbutton" type="submit" class="btn btn-success" value="pass">PASS</button>
  <button name="failbutton" type="submit" class="btn btn-danger" value="fail">FAIL</button>
</form>
<script src="../jquery.js"></script> 
<script src="../bootstrap/dist/js/bootstrap.min.js"></script> 
</body>
</html>