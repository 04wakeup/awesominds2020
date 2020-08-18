<?php
  require('includes/conn.php');
  include('redir-notinstructor.php');

  /*
  * MADE BY: Adam Lowe
  * PURPOSE: deletes all the points for the given course and chapter
  */

  $course_id = $_POST["course_id"];
  $chapter_id = $_POST["chapter_id"];
  $c_number = $_POST["c_number"];


  $query = $dbcon->prepare("DELETE FROM score 
    WHERE course_id_fk = :course_id
    AND chapter_id_fk = :chapter_id
    and c_number_fk = :c_number");
    
    $query->bindParam(":course_id", $course_id);
    $query->bindParam(":chapter_id", $chapter_id); 
    $query->bindParam(":c_number", $c_number); 
    $query->execute(); 
?>