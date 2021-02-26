<?php
	
class Dbconnect
{
	public function __construct()
	{
		$this->connectToDb();
	}

	private function connectToDb()
	{
		$servername = "*****";
		$username = "*****";
		$password = "*****";
		$dbname = "pawpatrol";

		// Create connection
		$conn = mysqli_connect($servername, $username, $password, $dbname);

		// Check connection
		if (!$conn) {
	    	die("Connection failed: " . mysqli_connect_error());
		}
		return $conn;

	}

	private function executeSQL($sql)
	{
		//connect to data base 
		$conn = $this->connectToDb();

		//execute sql and save into result variable
		$result = mysqli_query($conn, $sql);

		return $result;
	}

	/*
	 * TASKS
	 */
	public function getTaskDetailsByNumber($number)
	{	
		//select all data where the id is the same as input number
		$sql = "SELECT * FROM Tasks WHERE id = " . $number;

		$result = $this->executeSQL($sql);
		if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	$row = mysqli_fetch_assoc($result);
	        return $row;
		 	
		} else {
    		return false;
		}

	}

	/*
	 * GROUPS
	 */
	public function getAllPlayersByGroupId($groupId)
	{	
		//select all data where the id is the same as input number
		$sql = "SELECT * FROM Player WHERE group_id = " . $groupId;
		
		$result = $this->executeSQL($sql);
		if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	while ($row = mysqli_fetch_assoc($result)) {
	    		$players[] = array(
	    			'id' => $row['id'],
	    			'type' => $row['type']
	    		);
	    	}

	    	if (!empty($players)) {
	    		return $players;
	    	} else {
	    		return false;
	    	}
		} else {
    		return false;
		}
	}

	/*
	 * PLAYERS
	 */
	public function getPlayerDetails($number)
	{	
		//select all data where the id is the same as input number
		$sql = "SELECT * FROM Player WHERE id = " . $number;
		
		$result = $this->executeSQL($sql);
		if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	$row = mysqli_fetch_assoc($result);
	        return $row;
		 	
		} else {
    		return false;
		}
	}

	/*
	 * ANSWERS
	 */
	public function insertAnswer($taskId, $playerID, $answer) {

		$sql = "INSERT INTO PlayerAnswers (timeStamp, taskID, playerID, answer)
		VALUES (now(), $taskId, $playerID, '$answer')";

		$result = $this->executeSQL($sql);
	}


//** ANSWER VALIDATION **//


	public function getAnswersByTaskID($taskId) {

		$sql = "SELECT * FROM TaskAnswers WHERE taskID = " . $taskId;
		
		$result = $this->executeSQL($sql);
	    if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	$answers = array();
	    	while ($row = mysqli_fetch_assoc($result)) {
	    		if ($row['answerGivenByRole'] != '') {
	    			$answers[$row['answerGivenByRole']][] = $row['answer'];
	    		} else {
	    			$answers[] = $row['answer'];
	    		}
	    	}

	    	if (!empty($answers)) {
	    		return $answers;
	    	} else {
	    		return false;
	    	}
		} else {
    		return false;
		}
	}


	public function getPlayerAnswersFromATask($taskId, $playerId) {
		$sql = "SELECT * FROM PlayerAnswers WHERE taskID = $taskId AND playerID = $playerId ORDER BY id DESC";

		$result = $this->executeSQL($sql);
	    if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	$answers = array();
	    	while ($row = mysqli_fetch_assoc($result)) {
	    		$answers[] = array(
	    			'timestamp' => $row['timeStamp'],
	    			'answer' => $row['answer']
	    		);
	    	}

	    	if (!empty($answers)) {
	    		return $answers;
	    	} else {
	    		return false;
	    	}
		} else {
    		return false;
		}
	}

	public function getAllPlayersAnswersFromTask($taskId, $groupId) {
		
		//get all the players in each group
		$players = $this->getAllPlayersByGroupId($groupId);
		//put answers in an array
		$answers = array();

		// Cycle through player list and get each player's answer
		foreach ($players as $player) {
			//check the players type during the comparison
			$answers[$player['id']]['role'] = $player['type'];
			$answers[$player['id']]['answers'] = $this->getPlayerAnswersFromATask($taskId, $player['id']);
		}

		if ($answers) {
			return $answers;
		} else {
			return false;
		}
	}

	// validating for task 1
	public function validateGroupAnswersTask1($taskId, $groupId)
	{
		$allPlayersAnswers = $this->getAllPlayersAnswersFromTask($taskId, $groupId);
		$taskAnswers = $this->getAnswersByTaskID($taskId);
		
		//echo "<pre>" . print_r($taskAnswers, true) . "</pre>"; exit;
		//echo "<pre>" . print_r($allPlayersAnswers, true) . "</pre>"; exit;
		$numOfPlayers = count($allPlayersAnswers);

		$normalPlayers = $numOfPlayers - 1;
		$passTask = false;

		$counter = 0;

		//Looking through all players answers, one player at a time 
		// playerId represents a player's ID and this points to that
		// player's list of answers and their role
		foreach ($allPlayersAnswers as $playerId => $playerAnswers) {
			if ($playerAnswers['role'] == 'Normal') {
				//if a players answer is existant and in the array of task answers, increase the correct answer by
				if (isset($playerAnswers['answers'][0]['answer']) && in_array($playerAnswers['answers'][0]['answer'], $taskAnswers)) {
					$counter+=1;
				}
			} else {
				continue;
			}
		}

		//die('normal players: ' . $normalPlayers . ', counter: ' . $counter);

//verdict based on whether all normal players have the correct task answer
		if ($counter == $normalPlayers) {
			// die('PASS');
			return true;
		} else {
			// die('FAIL');
			return false;
		}
	}	

	public function validateGroupAnswersTask2($taskId, $groupId)
	{
		$allPlayersAnswers = $this->getAllPlayersAnswersFromTask($taskId, $groupId);
		$taskAnswers = $this->getAnswersByTaskID($taskId);
		
		$groupAnswers = [];

		//echo "<pre>" . print_r($allPlayersAnswers, true) . "</pre>";
		//echo "<pre>" . print_r($taskAnswers, true) . "</pre>";
		//$numOfPlayers = count($allPlayersAnswers);

		//$normalPlayers = $numOfPlayers - 1;
		$passTask = false;
		$wrongAnswerEnteredByNormal = false;
		//$counter = 0;

		//Looking through all players answers, one player at a time 
		// playerId represents a player's ID and this points to that
		// player's list of answers and their role
		foreach ($allPlayersAnswers as $playerId => $playerAnswers) {
			if (!empty($playerAnswers['answers'])) {
				foreach ($playerAnswers['answers'] as $key => $enteredAnswer) {
					if ($wrongAnswerEnteredByNormal) {
						break;
					} else if (in_array($enteredAnswer, $taskAnswers)) {
						if (!in_array($enteredAnswer, $groupAnswers)) {
							$groupAnswers[] = $enteredAnswer;
						}
					} else {
						if ($playerAnswers['role'] == 'Normal') {
							$wrongAnswerEnteredByNormal = true;
							break;
						}
					}

					//echo "<pre>" . print_r($groupAnswers, true) . "</pre>"; exit;
				}
			}

			if ($wrongAnswerEnteredByNormal) {
				break;
			}
		}


		sort($taskAnswers);
		sort($groupAnswers);

		if ($taskAnswers == $groupAnswers && !$wrongAnswerEnteredByNormal) {
			$passTask = true;
		}
	//verdict based on whether all normal players have the correct task answer
		if ($passTask) {
			// echo 'PASS';
			//echo "<pre>" . print_r($taskAnswers, true) . "</pre>";
			// echo "<pre>" . print_r($groupAnswers, true) . "</pre>";
			// exit;
			return true;
		} else {
			// echo 'FAIL';
			//echo "<pre>" . print_r($taskAnswers, true) . "</pre>";
			// echo "<pre>" . print_r($groupAnswers, true) . "</pre>";
			// exit;
			return false;
		}

	}

	public function validateGroupAnswersTask3($taskId, $groupId)
	{
		$allPlayersAnswers = $this->getAllPlayersAnswersFromTask($taskId, $groupId);
		$taskAnswers = $this->getAnswersByTaskID($taskId);
		
		$groupAnswers = [];

		//echo "<pre>" . print_r($allPlayersAnswers, true) . "</pre>";
		//echo "<pre>" . print_r($taskAnswers, true) . "</pre>";
		//$numOfPlayers = count($allPlayersAnswers);

		//$normalPlayers = $numOfPlayers - 1;
		$passTask = false;
		$normalPlayersWinsRound = false;
		$saboteurWinsRound = false;
		$normalPlayersTime = [];
		$saboteurTime = [];

		//$counter = 0;

		//Looking through all players answers, one player at a time 
		// playerId represents a player's ID and this points to that
		// player's list of answers and their role
		foreach ($allPlayersAnswers as $playerId => $playerAnswers) {
			if (!empty($playerAnswers['answers'])) {
				foreach ($playerAnswers['answers'] as $key => $enteredAnswer) {
					//echo "<pre>" . print_r($taskAnswers, true) . "</pre>"; exit;
					//echo "<pre>" . print_r($enteredAnswer, true) . "</pre>"; exit;
					if ($playerAnswers['role'] == 'Normal') {
						/*if (in_array($enteredAnswer['answer'], $taskAnswers['Normal'])) {
							$normalPlayersTime[] = $enteredAnswer['timestamp'];
						}*/
						if (preg_match('/' . $taskAnswer['Normal'][0] . '/i', $enteredAnswer['answer'])) {
							$normalPlayersTime[] = $enteredAnswer['timestamp'];
						}
					} else {
						/*if (in_array($enteredAnswer['answer'], $taskAnswers['Saboteur'])) {
							$saboteurTime[] = $enteredAnswer['timestamp'];
						}*/
						if (preg_match('/' . $taskAnswer['Saboteur'][0] . '/i', $enteredAnswer['answer'])) {
							$saboteurTime[] = $enteredAnswer['timestamp'];
						}
					}
				}
			}
		}

		if (!empty($normalPlayersTime) && !empty($saboteurTime)) {
		    if ($normalPlayersTime[0] > $saboteurTime[0]) {
		        $saboteurWinsRound = true;
		    } else if ($normalPlayersTime[0] < $saboteurTime[0]) {
		        $normalPlayersWinsRound = true;
		    }
		} else if (empty($normalPlayersTime) && !empty($saboteurTime)) {
		    $saboteurWinsRound = true;
		} else if (empty($saboteurTime) && !empty($normalPlayersTime)) {
		    $normalPlayersWinsRound = true;
		}
	//verdict based on whether all normal players have the correct task answer

		//echo "<pre>" . print_r($saboteurTime, true) . "</pre>";
		//echo "<pre>" . print_r($normalPlayersTime, true) . "</pre>";
		if ($saboteurWinsRound) {
			//die ('saboteur won');
			return 'Saboteur';
		} else if ($normalPlayersWinsRound) {
			//die ('normal players won');
			return 'Normal';
		} else {
			//die ('no one won this round');
			return 'Neither';
		}
	}

	public function validateGroupAnswersTask5($taskId, $groupId)
	{
		$allPlayersAnswers = $this->getAllPlayersAnswersFromTask($taskId, $groupId);
		$taskAnswers = $this->getAnswersByTaskID($taskId);
		
		//echo "<pre>" . print_r($taskAnswers, true) . "</pre>"; exit;
		// echo "<pre>" . print_r($allPlayersAnswers, true) . "</pre>"; exit;
		$numOfPlayers = count($allPlayersAnswers);

		$normalPlayers = $numOfPlayers - 1;
		$passTask = false;

		$counter = 0;

		//Looking through all players answers, one player at a time 
		// playerId represents a player's ID and this points to that
		// player's list of answers and their role
		foreach ($allPlayersAnswers as $playerId => $playerAnswers) {
			if ($playerAnswers['role'] == 'Normal') {
				//echo "<pre>" . print_r($playerAnswers, true) . "</pre>";
				//echo "<pre>" . print_r($taskAnswers, true) . "</pre>"; exit;
				//if a players answer is existant and in the array of task answers, increase the correct answer by
				if (isset($playerAnswers['answers'][0]['answer']) && $playerAnswers['answers'][0]['answer'] == $taskAnswers[0]) {
					$counter+=1;
				}
			} else {
				continue;
			}
		}

		//die('normal players: ' . $normalPlayers . ', counter: ' . $counter);

//verdict based on whether all normal players have the correct task answer
		if ($counter == $normalPlayers) {
			//die('PASS');
			return true;
		} else {
			//die('FAIL');
			return false;
		}
	}

	public function getVotes($taskID, $groupId){
		$allPlayersAnswers = $this->getAllPlayersAnswersFromTask($taskID, $groupId);
		
		$taskAnswers = $this->getAnswersByTaskID($taskId);

		$numOfPlayers = count($allPlayersAnswers);

		// $numOfPlayers = 6;

		// if ($numOfPlayers % 2 === 0) {
		// 	$majorityVotesCounter = $numOfPlayers / 2;
		// } else {
		// 	$majorityVotesCounter = round($numOfPlayers / 2);
		// }

		//echo "<pre>" . print_r($allPlayersAnswers, true) . "</pre>";
		//$vals = array_count_values($allPlayersAnswers);
		//echo 'Number: ' .count($vals) ;
		//print_r($vals);

		$votes = [];

		foreach ($allPlayersAnswers as $playerId => $playerAnswers) {
			if (isset($playerAnswers['answers'][0]['answer'])) {
			// ){
				$votes[] = $playerAnswers['answers'][0]['answer'];
			}
		}

		$votesTally = array_count_values($votes);
		
		//echo "<pre>" . print_r($votesTally, true) . "</pre>";

		foreach ($votesTally as $player => $noOfVotes) {
			echo "Votes for " . $player . ': ' . $noOfVotes . "<br>";
		}

	}

	public function getAllGroups() {
		//get the latest id
		$sql = "SELECT * FROM Groups ORDER BY id DESC";
		$groups = array();

		$result = $this->executeSQL($sql);
		if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	while ($row = mysqli_fetch_assoc($result)) {
	    		$groups['groups'][] = $row['id'];
	    	}	
		}
		return $groups;
	}

	public function getLastGroupId() {
		//get the latest id
		$sql = "SELECT id FROM Groups ORDER BY id DESC LIMIT 1";

		$result = $this->executeSQL($sql);
		if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	$row = mysqli_fetch_assoc($result);
	    	return $row['id'];	
		}
	}

	public function getLastPlayerId() {
		//get the latest id
		$sql = "SELECT id FROM Player ORDER BY id DESC LIMIT 1";

		$result = $this->executeSQL($sql);
		if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	$row = mysqli_fetch_assoc($result);
	    	return $row['id'];	
		}
	}

	public function createNewGroup()
	{
		$sql = "INSERT INTO Groups VALUES (NULL)";
		$result = $this->executeSQL($sql);
	}

	public function createNewPlayer($id, $groupId, $role, $displayPlayerNumber)
	{
		$sql = "INSERT INTO Player (id, group_id, type, displayPlayerNumber) VALUES ($id, $groupId, '$role', '$displayPlayerNumber')";
		$result = $this->executeSQL($sql);
	}

	public function getGroupResultTask4($groupId)
	{
		//get the latest id
		$sql = "SELECT * FROM `PlayerAnswers` as `pa`
			LEFT JOIN `Player` as `pl` on `pa`.playerID = `pl`.id
			WHERE `pl`.group_id = $groupId AND `pa`.taskID = 4";
		$task4Result = array();

		$result = $this->executeSQL($sql);
		if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	while ($row = mysqli_fetch_assoc($result)) {
	    		$task4Result[] = $row;
	    	}	
		}
		return $task4Result;
	}

	public function setGroupResultTask4($groupId, $result)
	{
		$hasResultAlready = false;
		if (!empty($this->getGroupResultTask4($groupId))) {
			$hasResultAlready = true;
		}
		//get all the players in each group
		$players = $this->getAllPlayersByGroupId($groupId);

		// Cycle through player list and get each player's answer
		foreach ($players as $player) {
			if ($hasResultAlready) {
				$sql = "UPDATE PlayerAnswers SET answer='$result' WHERE taskID=4 AND playerID=" . $player['id'];
				echo $sql . "<br>";
		    } else {
				$sql = "INSERT INTO PlayerAnswers (taskID, playerID, answer) VALUES (4, " . $player['id'] . ", '$result')";
			}
			$this->executeSQL($sql);
		}
	}

	public function setGameStartChecker($groupId, $playerId)
	{
		$sql = "UPDATE Player SET gameStart = 1 WHERE group_id = $groupId AND id = $playerId";
		$this->executeSQL($sql);
	}

	public function getGroupGameStartChecker($groupId)
	{
		$sql = "SELECT id, gameStart FROM Player WHERE group_id = $groupId";
		$groupGameStartChecker = array();

		$result = $this->executeSQL($sql);
		if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	while ($row = mysqli_fetch_assoc($result)) {
	    		$groupGameStartChecker[] = $row;
	    	}	
		}
		return $groupGameStartChecker;
	}

	public function getPlayerCurrentTask($playerId) {
		//get the latest id
		$sql = "SELECT currentTask, taskEndTime FROM Player WHERE id = $playerId";

		$result = $this->executeSQL($sql);
		if (mysqli_num_rows($result) > 0) {
	    	// output data of each row
	    	$row = mysqli_fetch_assoc($result);
            return $row;
		}
	}

	public function setPlayerCurrentTask($playerId, $currentTask, $timestamp) {
		$sql = "UPDATE Player SET currentTask='$currentTask', taskEndTime='$timestamp' WHERE id = $playerId";
        //die($sql);
		$this->executeSQL($sql);
	}

}
