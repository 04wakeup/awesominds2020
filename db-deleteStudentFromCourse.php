<?php
  require('includes/db.php');
  include('redir-notinstructor.php');

  /*
  * MADE BY: Adam Lowe
  * PURPOSE: deletes all the students from a course
  */

  $course_id = $_POST["course_id"];


  $query = "DELETE FROM course_registration
    WHERE course_id_fk = '$course_id'";
  $mysqli->query($query);
  $mysqli->close();
?>