<?php
  require('includes/conn.php');
  include('redir-notinstructor.php');

  /*
  * MADE BY: Walker Jones
  * PURPOSE: Toggles hidden status of course with given id. TODO: give file a more appropiate name
  */

  $course_id = $_POST["course_id"];

  $query = $dbcon->prepare("UPDATE course 
    SET hidden = not hidden
    WHERE course_id = :course_id");

  $query->bindParam(":course_id", $course_id); 
  $query->execute(); 
  $result = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);
   
?>