<!DOCTYPE html>
<html>
<head>
	<link rel="stylesheet" href="bootstrap/dist/css/bootstrap.min.css">
	<link rel="stylesheet" href="mystyle.css">
	<script defer src="fontawesome/js/all.js"></script>
	<title>Page Title</title>
</head>
<body>

<div id= "rules"> 
  <h1> Game Rules: </h1>
  <p> ○ No revealing your roles!<br>
      ○ The team needs to pass 3 missions in order to win <br>
      ○ Can answer as many times as you like within the countdown- the <b> latest </b> answer will be accepted. <br>
     ○ No googling, you're in a library! <br>
     ○ Lastly, be suspicious of your answers! The saboteur is trying to make you fail and keep the animals loose >:c <br>

</p>
</div>
     

<?php

include 'dbconnect.php';
    
// Start the session
session_start();
    
if (isset($_POST['submitbutton']) && isset($_POST['playernum'])) {
  //die($_POST['playernum']);
  $dbConnection = new Dbconnect();
  $playerDetails = $dbConnection->getPlayerDetails($_POST['playernum']);
  $dbConnection->setGameStartChecker($playerDetails['group_id'], $_POST['playernum']);
  //echo "playerDetails:<pre>" . print_r($playerDetails, true) . "</pre>";
  //exit;
  $_SESSION['playerId'] = $_POST['playernum'];
  $_SESSION['groupId'] = $playerDetails['group_id'];
  $_SESSION['groupStarted'] = false;

  $_SESSION['currentTask'] = 'home';
  $currentTask = $dbConnection->getPlayerCurrentTask($_POST['playernum']);
    //echo basename(__FILE__, '.php');
    
  $playerRoleMsg = ' You are ';

  if ($playerDetails['type'] == "Saboteur") {
    $playerRoleMsg .= 'the Saboteur, you released the animals! Make sure they arent re-caputured by making your team fail 3 missions. (Beware: Dont get caught or you&apos;ll lose!)';
    $_SESSION['playerRole'] = 'Saboteur';
  } else {
    $playerRoleMsg .= 'a normal player, our saving grace! You must help us in capturing the animals! Your team needs to pass 3 missions in order to win. ';
    $_SESSION['playerRole'] = 'Normal';
  }
?>
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
  <strong>Holy guacamole!</strong><?php echo $playerRoleMsg; ?>
  <!-- <button type="button"> 
    <a href="task1.php" <span aria-hidden="true">Press to start</span></a>
  </button> -->
  <span id="gameStartCD"></span>
</div>
<div id= "countdown" >
</div>
<?php
  //die($_SESSION['playerId']);
  //$allPlayersStarted = false;
  if ($_SESSION['groupStarted'] === false) {
    //do {
    for ($i = 0; $i<30; $i++) {
      sleep(1);
      $checkGroupStart = $dbConnection->getGroupGameStartChecker($playerDetails['group_id']);
      //echo "group check:<pre>" . print_r($checkGroupStart, true) . "</pre>";
      $groupStarted = true;
      foreach($checkGroupStart as $index => $playerStarted) {
        if ($playerStarted['gameStart'] == 0) {
          $groupStarted = false;
          break;
        }
      }
      //echo "<br> groupstarted:" . $groupStarted;
      //if ($groupStarted === false) {
      //  sleep(5);
      //} else {
      if ($groupStarted === true) {
        //die('group started');

        $_SESSION['groupStarted'] = true;
        break;
        //header('refresh');
        
      }
    //} while ($_SESSION['groupStarted'] === false);
    }
  }

  if ($_SESSION['groupStarted'] === true) {
?>
<script type="text/javascript">

  var utcCountDownSecs = 16;

  // Update the count down every 1 second
  var x = setInterval(function() {

    if (utcCountDownSecs > 0) {
      utcCountDownSecs -= 1; 
      document.getElementById("countdown").innerHTML = "The game will start in " + utcCountDownSecs;
    } else {
      window.location.href = 'task1.php';
    }
  }, 1000);
</script>
  
<?php
  }
  //var_dump($playerDetails)
  //echo "<pre>" . print_r($playerDetails, true) . "</pre>";

  //echo $playerDetails['type'];

  


  /*if ($_SESSION['groupStarted'] === true) {
?>
    <script type="text/javascript">

    var utcCountDownSecs = 16;

    // Update the count down every 1 second
    var x = setInterval(function() {

      if (utcCountDownSecs > 0) {
        utcCountDownSecs -= 1; 
        document.getElementById("countdown").innerHTML = "The game will start in " + utcCountDownSecs;
      } else {
        window.location.href = 'task1.php';
      }
    }, 1000);
    </script>
<?php
  }*/
}
?>



<div id="welcome-header" class="jumbotron jumbotron-fluid">
  <div class="container">
    <h1 class="display-4"style="font-family:Dekko;">Paw Patrol</h1>
    <h3 style="font-family:Dekko;"> Welcome to the game </h3>
 	  <form action="" method="post">
    	<p class="lead">Please enter your number</p>
    	<input name="playernum" type="number"> 
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
