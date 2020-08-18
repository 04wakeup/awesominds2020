<?php
//NOTE: i dont get the point of this and setchapter.php
// James: when a course or a chapter thes phps executed to set it on SESSION which is used upload question bank;
/*
  include('redir-notloggedin.php');
  if(isset($_POST["course"])){
    $_SESSION["course"] = $_POST["course"];
    echo json_encode($_SESSION);
  }else{
    http_response_code(404);
  }
  */
  include('redir-notloggedin.php');
  $_SESSION["course"] = $_POST["course"];
  echo json_encode($_SESSION);
?>
