<?php include('redir-notloggedin.php'); ?>
<head>
  <title>Awesominds 2020</title>

  <script type="text/javascript" src="js/phaser.min.js"></script>
  <script type="text/javascript" src="js/moment.min.js"></script>
  <script type="text/javascript" src="https://code.jquery.com/jquery-3.2.1.min.js"></script>

  <?php
    if ($_SESSION['devmode']) {
      echo '<script type="text/javascript" src="js/devvars.js"></script>';
    } else {
      echo '<script>var devmode = false;</script>';
    }
  ?>

  <script type="text/javascript">var phpSession = <?php echo json_encode(array('play_name'=>$_SESSION['play_name'], 'avatarnum'=>$_SESSION['avatarnum'], 'user_volume'=>$_SESSION['user_volume'])); ?></script>
  <script type="text/javascript" src="js/menu-mode.js"></script>
  <script type="text/javascript" src="js/menu-chapter.js"></script>
  <script type="text/javascript" src="js/menu-course.js"></script>

  <script type="text/javascript" src="js/play.js"></script>
  <script type="text/javascript" src="js/play-RateQuestions.js"></script>
  <script type="text/javascript" src="js/play-SlideCards.js"></script>
  <script type="text/javascript" src="js/play-JustDrills.js"></script>
  <script type="text/javascript" src="js/play-MysteryMultiplier.js"></script>

  <script type="text/javascript" src="js/endofmode.js"></script>

  <script type="text/javascript" src="js/pregame.js"></script>

  <script type="text/javascript" src="js/gameshowUI.js"></script>   

  <script type="text/javascript" src="js/settings.js"></script>
  <script type="text/javascript" src="js/stop.js"></script>
  <script type="text/javascript" src="js/gameOver.js"></script>
  

  <script type="text/javascript" src="js/preload.js"></script>
  <script type="text/javascript" src="js/game.js"></script>

  <style type="text/css">
    body {
      margin: 0;
    }
    #wrapper {
      background: rgba(230,249,255,1);
      background: -moz-linear-gradient(top, rgba(230,249,255,1) 0%, rgba(230,249,255,1) 1%, rgba(230,249,255,1) 25%, rgba(230,249,255,1) 71%, rgba(230,249,255,1) 100%);
      background: -webkit-gradient(left top, left bottom, color-stop(0%, rgba(230,249,255,1)), color-stop(1%, rgba(230,249,255,1)), color-stop(25%, rgba(230,249,255,1)), color-stop(71%, rgba(230,249,255,1)), color-stop(100%, rgba(230,249,255,1)));
      background: -webkit-linear-gradient(top, rgba(230,249,255,1) 0%, rgba(230,249,255,1) 1%, rgba(230,249,255,1) 25%, rgba(230,249,255,1) 71%, rgba(230,249,255,1) 100%);
      background: -o-linear-gradient(top, rgba(230,249,255,1) 0%, rgba(230,249,255,1) 1%, rgba(230,249,255,1) 25%, rgba(230,249,255,1) 71%, rgba(230,249,255,1) 100%);
      background: -ms-linear-gradient(top, rgba(230,249,255,1) 0%, rgba(230,249,255,1) 1%, rgba(230,249,255,1) 25%, rgba(230,249,255,1) 71%, rgba(230,249,255,1) 100%);
      background: linear-gradient(to bottom, rgba(230,249,255,1) 0%, rgba(230,249,255,1) 1%, rgba(230,249,255,1)) 25%, rgba(230,249,255,1) 71%, rgba(230,249,255,1) 100%);
      filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#0085ad', endColorstr='#fcfcfc', GradientType=0 );
      margin: 0;
      padding: 0;
    }
    #gamediv {
      margin: 0;
      padding: 0;
      width: 100%;
      height: 100%;
    }
  </style>
</head>
<body>
<div id="wrapper">
  <div id="gameDiv"></div>
</div>


