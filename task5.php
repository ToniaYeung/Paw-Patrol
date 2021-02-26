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
      
    if ($countDown >= 0) {
        header("Refresh: " . $countDown . "; URL=vote.php");
    } else {
        header("Refresh: 0; URL=vote.php");
    }
    
  if ($_SESSION['playerRole'] == 'Saboteur') {

?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
  Convince another player to put an incorrect answer (0 is correct).
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php
   
  }

  $taskID = 5;
  $task5 = $dbConnection->getTaskDetailsByNumber($taskID);

  // echo "<pre>" . print_r($_POST, true) . "</pre>";
  if (isset($_POST['submitbutton']) && $_POST['answer'] !== '') {
      $dbConnection->insertAnswer($taskID, $_SESSION['playerId'], trim($_POST['answer']));
      //$dbConnection->validateGroupAnswersTask5($taskID, $_SESSION['groupId']);
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
<div id="welcome-header" class="jumbotron jumbotron-fluid ">
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
    ?>
    <img class="paw-print-score" src="images/whitepaw.png">
  </div>
  <div class="container">
    <p id="taskCD"></p>
    <h1 class="display-4">Mission 5</h1>
    <img class="animals-img" src="images/animals.png">
    <p class="lead"><?php echo $task5['description'];?></p>
    <form action="task5.php" method="post">
      <input id="taskCDHidden" name="taskCDHidden" type="hidden">
      <div class="form-check">
        <input class="form-check-input" type="radio" name="answer" id="1" value="0" checked>
        <label class="form-check-label" for="exampleRadios1">
          0
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="answer" id="2" value="35000">
        <label class="form-check-label" for="exampleRadios2">
          35000
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="answer" id="3" value="1">
        <label class="form-check-label" for="exampleRadios3">
          One of each animal
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="answer" id="4" value="2">
        <label class="form-check-label" for="exampleRadios3">
          Two of each animal
        </label>
      </div>
      <button class="submitbutton" name="submitbutton" type="submit">
        <i class="far fa-paper-plane" type="submit"></i>
      </button>
    </form>
  </div>
</div>

<script src="jquery.js"></script>
<script src="bootstrap/dist/js/bootstrap.min.js"></script>
</body>
</html>


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

  window.location.href = 'vote.php';
}

  // Display the result in the element with id="demo"
  document.getElementById("taskCD").innerHTML = utcCountDownMins + ":" + utcCountDownSecs;
  document.getElementById("taskCDHidden").value = utcCountDownMins + ":" + utcCountDownSecs;
 
}, 1000);
</script>

</body>
</html>
