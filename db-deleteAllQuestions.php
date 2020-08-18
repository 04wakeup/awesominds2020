<?php
  require('includes/conn.php');
  include('redir-notinstructor.php');

  /*
  * MADE BY: Adam Lowe
  * PURPOSE: deletes all the questions for the given course and chapter for only the
  * Instructor's course choice
  */

  $query = $dbcon->prepare("DELETE FROM question 
    WHERE course_id_fk = :course_id
    AND chapter_id_fk = :chapter_id");

  $query->bindParam(':course_id', $_POST["course_id"]);
  $query->bindParam(':chapter_id', $_POST["chapter_id"]);
  $query->execute();
?>