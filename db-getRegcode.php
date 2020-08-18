<?php
  require('includes/conn.php');
  include('redir-notinstructor.php');

  /*
  * MADE BY: Walker Jones
  * PURPOSE: Get the regcode for the course with the given id.
  */

  $course_id = $_POST["course_id"];

  $query = $dbcon->prepare("SELECT regcode
    FROM course
    WHERE course_id = :course_id");

  $query->bindParam(":course_id", $course_id); 
  $query->execute(); 
  $result = $query->fetch(PDO::FETCH_ASSOC);

  echo json_encode($result);
   
?>