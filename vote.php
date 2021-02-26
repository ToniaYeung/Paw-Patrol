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
      $countDown = 90;
      $taskEndTime = strtotime($currentTimestamp) + $countDown;
      $dbConnection->setPlayerCurrentTask($_SESSION['playerId'], $currentTask, date('Y-m-d H:i:s', $taskEndTime));
  } else {
      $countDown = strtotime($taskStatus['taskEndTime']) - strtotime($currentTimestamp);
    
      if ($countDown < 0) {
          $countDown = 0;
      }
  }
    
  $taskID = 6;
  $task6 = $dbConnection->getTaskDetailsByNumber($taskID);

  // echo "<pre>" . print_r($_POST, true) . "</pre>";
  // echo "<pre>" . print_r($_SESSION, true) . "</pre>";

  if (isset($_POST['submitbutton']) && $_POST['answer'] !== '') {
    $dbConnection->insertAnswer($taskID, $_SESSION['playerId'], trim($_POST['answer']));
    $dbConnection->getVotes($taskID, $_SESSION['groupId']);
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

    if ($ans = $dbConnection->getPlayerAnswersFromATask(4, $_SESSION['playerId'])) {
        if ($ans[0]['answer'] == 'pass') {
          echo '<img class="paw-print-score" src="images/greenpaw.png">';
        } else {
          echo '<img class="paw-print-score" src="images/redpaw.png">';
        }
    }

    if ($dbConnection->validateGroupAnswersTask5(5, $_SESSION['groupId'])) {
      echo '<img class="paw-print-score" src="images/greenpaw.png">';
    }
    else{
          echo '<img class="paw-print-score" src="images/redpaw.png">';
    }

    ?>
  </div>
  <!-- <div class="container"> -->
    <p id="taskCD"></p>
    <h1 class="display-4">The Saboteur</h1>
    <img class="saboteur-img" src="images/saboteur.png">
    <?php
      if (isset($_POST['taskCDHidden']) && $_POST['taskCDHidden'] == '0:00') {
        echo "<div>";
        $dbConnection->getVotes($taskID, $_SESSION['groupId']);
        echo "</div>";
      } else {
    ?>
        <p class="lead"><?php echo $task6['description'];?></p>
        <form id="votingForm" action="vote.php" method="post">
          <input id="taskCDHidden" name="taskCDHidden" type="hidden">
          <div class="form-check2">
            <input class="form-check-input" type="radio" name="answer" id="1" value="Player1" checked>
            <label class="form-check-label" for="exampleRadios1">
              Player 1
            </label>
          </div>
          <div class="form-check2">
            <input class="form-check-input" type="radio" name="answer" id="2" value="Player2">
            <label class="form-check-label" for="exampleRadios2">
              Player 2
            </label>
          </div>
          <div class="form-check2">
            <input class="form-check-input" type="radio" name="answer" id="3" value="Player3">
            <label class="form-check-label" for="exampleRadios3">
              Player 3
            </label>
          </div>
          <div class="form-check2">
            <input class="form-check-input" type="radio" name="answer" id="4" value="Player4">
            <label class="form-check-label" for="exampleRadios3">
              Player 4
            </label>
          </div>
          <button class="submitbutton" name="submitbutton" type="submit">
            <i class="far fa-paper-plane" type="submit"></i>
          </button>
        </form>
    <?php
      }
    ?>
  <!-- </div> -->
</div>

<script src="jquery.js"></script>
<script src="bootstrap/dist/js/bootstrap.min.js"></script>
<script>

<?php 

if (isset($_POST['taskCDHidden']) && $_POST['taskCDHidden'] != '0:00'){
  // var_dump(isset($_POST['taskCDHidden']));
  // var_dump($_POST['taskCDHidden']);
  // var_dump(isset($_POST['taskCDHidden']) && $_POST['taskCDHidden'] != '0.00');
  // exit;
  $time = explode (":", $_POST['taskCDHidden']);
  $utcCountDownMins = $time[0]; 
  $utcCountDownSecs = $time[1];

  //die ("min: " . $utcCountDownMins . ", sec: " . $utcCountDownSecs);
  echo "var utcCountDownMins = $utcCountDownMins;";
  echo "var utcCountDownSecs = $utcCountDownSecs;";
  //taskCDMin = $taskCDMini;
  //taskCDSec = $taskCDSec; 

} else {
    //var_dump($countDown);
    $countDownTimerFE = date('i:s', $countDown);
    $countDownTimerFE = explode(':', $countDownTimerFE);
    echo "var utcCountDownMins = " . (int)$countDownTimerFE[0] . ";";
    echo "var utcCountDownSecs = " . (int)$countDownTimerFE[1] . ";";
}
?>


  // Update the count down every 1 second
  var x = setInterval(function() {

  if (!(utcCountDownMins === 0 && utcCountDownSecs === 0)) {
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

    // Display the result in the element with id="demo"
    document.getElementById("taskCD").innerHTML = utcCountDownMins + ":" + utcCountDownSecs;
    document.getElementById("taskCDHidden").value = utcCountDownMins + ":" + utcCountDownSecs;

    <?php
      if ($_POST['taskCDHidden'] !== '0.00') {
    ?>
        if (utcCountDownSecs == 0 && utcCountDownMins == 0){
          /*<?php //die('hi'); ?> */
          document.getElementById("votingForm").submit();
        }
    <?php
      }
    ?>

    }
   
  }, 1000);

</script>

</body>
</html>
