<?php 

include 'dbconnect.php';

  // Start the session
  session_start();
    
    $dbConnection = new Dbconnect();
    // extract task1 from file directory
    $currentTask = basename(__FILE__, '.php');

    // Get player's current timestamp and task
    $taskStatus = $dbConnection->getPlayerCurrentTask($_SESSION['playerId']);
      
    // Identify current date/time
    $currentTimestamp = date('Y-m-d H:i:s');
      
    // When page first loads $taskStatus['currentTask'] is an empty string
    // and $currentTask = task1
    if ((string)$taskStatus['currentTask'] != $currentTask) {
        $countDown = 540;
        $taskEndTime = strtotime($currentTimestamp) + $countDown;
        $dbConnection->setPlayerCurrentTask($_SESSION['playerId'], $currentTask, date('Y-m-d H:i:s', $taskEndTime));
    } else {
        $countDown = strtotime($taskStatus['taskEndTime']) - strtotime($currentTimestamp);
        
        if ($countDown < 0) {
            $countDown = 0;
        }
    }
      
    if ($countDown >= 0) {
        header("Refresh: " . $countDown . "; URL=task5.php");
    } else {
        header("Refresh: 0; URL=task5.php");
    }
    
  if ($_SESSION['playerRole'] == 'Saboteur') {

?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
  Convince your team members to put the turtle in the pen too.
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php
   
  }
    
  $taskID = 4;

  // $dbConnection->validateGroupAnswersTask4($taskID, $_SESSION['groupId']);
  $task4 = $dbConnection->getTaskDetailsByNumber($taskID);
  if (isset($_POST['submitbutton']) && !empty($_POST['answer'])) {
    // Add player's answer to DB
    $dbConnection->insertAnswer($taskID, $_SESSION['playerId'], trim($_POST['answer']));

    // Validate answer
    // if ($ans = $dbConnection->getPlayerAnswersFromATask($taskID, $_SESSION['playerId'])) {
    //   if ($ans[0]['answer'] == 'pass') {
    //     die('You have passed this round');
    //   } else {
    //     die('You loser');
    //   }
    // }
    
    //if (isset($ans['ans']))

  }

  ?>

<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="mystyle.css">
	<script defer src="fontawesome/js/all.js"></script>
	<title>Page Title</title>
</head>
<body>
<div id="welcome-header" class="jumbotron jumbotron-fluid">
  <div>
    <?php

    if ($dbConnection->validateGroupAnswersTask1(1, $_SESSION['groupId'])) {
      echo '<img class="paw-print-score" src="images/greenpaw.png">';
    }
    else{
      echo '<img class="paw-print-score" src="images/redpaw.png">';
    }

    if($dbConnection->validateGroupAnswersTask2(2, $_SESSION['groupId'])) {
      echo '<img class="paw-print-score" src="images/greenpaw.png">';
    }
    else{
      echo '<img class="paw-print-score" src="images/redpaw.png">';
    }

    if($dbConnection->validateGroupAnswersTask3(3, $_SESSION['groupId'])) {
      echo '<img class="paw-print-score" src="images/greenpaw.png">';
    }
    else{
      echo '<img class="paw-print-score" src="images/redpaw.png">';
    }

    ?>
    <img class="paw-print-score" src="images/whitepaw.png">
    <img class="paw-print-score" src="images/whitepaw.png">
  </div>
  <!-- <div class="container"> -->
    <p id="taskCD"></p>
    <h1 class="display-4">Mission 4</h1>
    <p class="lead"><?php echo $task4['description'];?></p>
 <!-- 	<form action="task4.php" method="post"> -->
<p> <i> Be aware of the paw!!!<i> <p>
     <img class="map-img" src="images/task4map.png" >

<!-- 
    <input name="answer" type="text"> -->
    <input id="taskCDHidden" name="taskCDHidden" type="hidden">
<!--     <button name="submitbutton" type="submit">
        <i class="far fa-paper-plane" type="submit"></i>
    </button> -->
	<!-- </form> -->
  <!-- </div> -->
</div>
<script src="jquery.js"></script>
<script src="bootstrap/dist/js/bootstrap.min.js"></script>



<script>

<?php

if (isset($_POST['taskCDHidden'])){
  //var_dump($_POST['taskCDHidden']); exit;
  $time = explode (":", $_POST['taskCDHidden']);
  $utcCountDownMins = $time[0];
  $utcCountDownSecs = $time[1];

  //die ("min: " . $utcCountDownMins . ", sec: " . $utcCountDownSecs);
  echo "var utcCountDownMins = $utcCountDownMins;";
  echo "var utcCountDownSecs = $utcCountDownSecs;";
  //taskCDMin = $taskCDMini;
  //taskCDSec = $taskCDSec;

} else {
    $countDownTimerFE = date('i:s', $countDown);
    $countDownTimerFE = explode(':', $countDownTimerFE);
    echo "var utcCountDownMins = " . (int)$countDownTimerFE[0] . ";";
    echo "var utcCountDownSecs = " . (int)$countDownTimerFE[1] . ";";
  //var utcCountDownMins = 7;
  //var utcCountDownSecs = 59;
    
}
?>

// Update the count down every 1 second
var x = setInterval(function() {

if (utcCountDownMins > 0){


  if (utcCountDownSecs > 0){

     utcCountDownSecs -= 1;
     if (utcCountDownSecs < 10) {
        utcCountDownSecs = '0' + utcCountDownSecs;
     }
  } 
  else if ( utcCountDownSecs == 0 ){

    utcCountDownMins -= 1;
    utcCountDownSecs = 59;
  }

} else {
  utcCountDownMins = 0;

  if (utcCountDownSecs > 0){

     utcCountDownSecs -= 1;
     if (utcCountDownSecs < 10) {
        utcCountDownSecs = '0' + utcCountDownSecs;
     }
  } else {
    utcCountDownSecs = '00';
  }

}

if (utcCountDownSecs == 0 && utcCountDownMins == 0){

  window.location.href = 'task5.php';
}

  // Display the result in the element with id="demo"
  document.getElementById("taskCD").innerHTML = utcCountDownMins + ":" + utcCountDownSecs;
  document.getElementById("taskCDHidden").value = utcCountDownMins + ":" + utcCountDownSecs;
 
}, 1000);
</script>

</body>
</html>
