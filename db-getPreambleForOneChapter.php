<?php
  /*
  * MADE BY: Adam Lowe
  * PURPOSE: Get the preamble for the course
  */ 

  $course_id = $_POST["course_id"];
  //$chapter_id = $_POST["chapter_id"];

  require('includes/conn.php');
  include('redir-notloggedin.php');

  $query = $dbcon->prepare("SELECT preamble
    FROM chapter
    WHERE course_id_fk = :course_id");
  $query->bindParam(":course_id", $course_id);
  //$query->bindParam(":chapter_id", $chapter_id);

  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($result);
?>