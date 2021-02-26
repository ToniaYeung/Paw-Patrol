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

<h1>Create new game</h1>

<form method="post">
  <button name="submitbutton" type="submit" class="btn btn-primary">New game</button>
</form>

<?php

include '../dbconnect.php';
$dbConnection = new Dbconnect();

if (isset($_POST['submitbutton'])) {
	$dbConnection->createNewGroup();
	$groupId = $dbConnection->getLastGroupId();
	echo "<br><h2>Group $groupId</h2>";
	//echo "<pre>" . print_r($dbConnection->getAllGroups(), true) . "</pre>";
	$numberOfPlayers = 4;
	$lastPlayerId = $dbConnection->getLastPlayerId();
	$first = $lastPlayerId + 1;
	$last = $lastPlayerId + $numberOfPlayers;

	$saboteur = rand($first, $last);
	echo "<br>";
	$currentPlayerId = $first;
	for($playerPos = 1 ; $playerPos <= $numberOfPlayers; $playerPos++) {
		if($currentPlayerId === $saboteur) {
			$role = 'Saboteur';
		} else {
			$role = 'Normal';
		}

		$displayPlayerNumber = 'Player ' . $playerPos;

		//echo 'current player id: ' . $currentPlayerId . ', group id: ' . $groupId, ', role: ' . $role . ', display: ' . $displayPlayerNumber . "<br>";
		$dbConnection->createNewPlayer($currentPlayerId, $groupId, $role, $displayPlayerNumber);
		?>
			<p>Player ID: <?php echo $currentPlayerId . ' [' . $displayPlayerNumber . '] - ' . $role;?></p>
		<?php
		$currentPlayerId++;
	}
}
?>
<script src="../jquery.js"></script> 
<script src="../bootstrap/dist/js/bootstrap.min.js"></script> 
</body>
</html>