<?php
  require('includes/conn.php');
  include('redir-notinstructor.php');

  /*
  * MADE BY: Walker Jones
  * PURPOSE: Toggles hidden status of chapter with given ids. TODO: give file a more appropiate name
  */

  $course_id = $_POST["course_id"];
  $chapter_id = $_POST["chapter_id"];

  $query = $dbcon->prepare("UPDATE chapter 
    SET hidden = not hidden
    WHERE course_id_fk = :course_id
    AND chapter_id = :chapter_id");

  $query->bindParam(":course_id", $course_id);
  $query->bindParam(":chapter_id", $chapter_id); 
  $query->execute(); 
  $result = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);
   
?>