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

$groupId = trim($_POST['groupId']);

// for($taskId = 1; $taskId < 6; $taskId++) {
// 	echo "<pre>";
// 	echo print_r($dbConnection->getAllPlayersAnswersFromTask($taskId, $groupId), true);
// 	echo "</pre>";
// }

$results = [];
for($taskId = 1; $taskId < 6; $taskId++) {
	$results[$taskId] = $dbConnection->getAllPlayersAnswersFromTask($taskId, $groupId);
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

<form method="post">
  Enter Group ID
  <input name="groupId" type="text">
  <button name="submitbutton" type="submit" class="btn btn-primary">Submit</button>
</form>

<h1>Results for Group <?php echo $groupId; ?></h1>

<?php
//echo "<pre>"  . print_r($results, true) . "</pre>";

foreach ($results as $taskId => $taskAnswers) {
	?>
	<h2>Task <?php echo $taskId;?></h2>
	<?php
	foreach ($taskAnswers as $playerId => $playerDetails) {
	?>
	<p><?php echo 'Player ID: ' . $playerId . ' - ' . $playerDetails['role'];?></p>
	<table class="table">	
		<thead class="thead-dark">
    		<tr>
    			<th scope="col">#</th>
      			<th scope="col">Timestamp</th>
			    <th scope="col">Answer</th>
    		</tr>
  		</thead>
  		<tbody>
	<?php
		foreach ($playerDetails['answers'] as $index => $playerAnswer) {
	?>
			<tr>
				<th scope="row"><?php echo $index;?></th>
				<td><?php echo $playerAnswer['timestamp'];?></td>
				<td><?php echo $playerAnswer['answer'];?></td>

			</tr>
	<?php
		}
	?>
		</tbody>
	</table>
	<br>
	<?php
	}
}
?>

<script src="../jquery.js"></script> 
<script src="../bootstrap/dist/js/bootstrap.min.js"></script> 
</body>
</html>