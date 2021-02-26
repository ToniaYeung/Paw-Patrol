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
        $countDown = 480;
        $taskEndTime = strtotime($currentTimestamp) + $countDown;
        $dbConnection->setPlayerCurrentTask($_SESSION['playerId'], $currentTask, date('Y-m-d H:i:s', $taskEndTime));
    } else {
        $countDown = strtotime($taskStatus['taskEndTime']) - strtotime($currentTimestamp);
        
        if ($countDown < 0) {
            $countDown = 0;
        }
    }
      
    if ($countDown >= 0) {
        header("Refresh: " . $countDown . "; URL=task3.php");
    } else {
        header("Refresh: 0; URL=task3.php");
    }
    
  if ($_SESSION['playerRole'] == 'Saboteur') {
    //if (!isset($_SESSION['saboteur_msg_shown'])) {
?>
<div class="alert alert-warning alert-dismissible fade show" role="alert">
   Make another player scan the QR code with clawed paw prints. 
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<?php
    //  $_SESSION['saboteur_msg_shown'] = true; 
    //}
  }
  
  $taskID = 2;
  $task2 = $dbConnection->getTaskDetailsByNumber($taskID);

  if (isset($_POST['submitbutton']) && !empty($_POST['answer'])) {
    $dbConnection->insertAnswer($taskID, $_SESSION['playerId'], trim($_POST['answer']));

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

    ?>
    <img class="paw-print-score" src="images/whitepaw.png">
    <img class="paw-print-score" src="images/whitepaw.png">
    <img class="paw-print-score" src="images/whitepaw.png">
    <img class="paw-print-score" src="images/whitepaw.png">
  </div>
  <p id="taskCD"></p>
  <h1 class="display-4">Mission 2</h1>
  <img class="paw-img"  src="images/pawprint.png" >
  <p class="lead"><?php echo $task2['description'];?></p>
  <p> All QR codes are on the First Floor, in the book collection/indivudal study space room. <p> <br>
<p><i> Beware of the claw!!!<i> <p>
  <form action="task2.php" method="post">
    <input type="text" name="answer" size=16 placeholder="QR Code" class="qrcode-text">
    <label class="qrcode-text-btn">
      <input type="file" accept="image/*" capture=environment onchange="openQRCamera(this);" tabindex=-1>
    </label> 
    <input id="taskCDHidden" name="taskCDHidden" type="hidden">
    <button class="submitbutton" name="submitbutton" type="submit">
        <i class="far fa-paper-plane" type="submit"></i>
    </button>
  </form>
</div>
<script src="jquery.js"></script>
<script src="bootstrap/dist/js/bootstrap.min.js"></script>
<script src="qr_packed.js"></script>



<script type="text/javascript">
function openQRCamera(node) {
  var reader = new FileReader();
  reader.onload = function() {
    node.value = "";
    qrcode.callback = function(res) {
      if(res instanceof Error) {
        alert("No QR code found. Please make sure the QR code is within the camera's frame and try again.");
      } else {
        node.parentNode.previousElementSibling.value = res;
      }
    };
    qrcode.decode(reader.result);
  };
  reader.readAsDataURL(node.files[0]);
}
</script>

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

  window.location.href = 'task3.php';
}

  // Display the result in the element with id="demo"
  document.getElementById("taskCD").innerHTML = utcCountDownMins + ":" + utcCountDownSecs;
  document.getElementById("taskCDHidden").value = utcCountDownMins + ":" + utcCountDownSecs;
 
}, 1000);
</script>

</body>
</html>
