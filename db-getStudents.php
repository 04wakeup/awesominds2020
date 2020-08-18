<?php
  require('includes/conn.php');
  include('redir-notinstructor.php');

  /*
  * MADE BY: Adam Lowe
  * PURPOSE: gets all the students from a course
  */

  $query = $dbcon->prepare("select * from course_registration where course_id_fk = :course");
  $query->bindParam(':course', $_POST["course"]);
  $query->execute();
  
  $result = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);