<?php
  require('includes/conn.php');
  include('redir-notloggedin.php');
  /*
  * MADE BY: Walker Jones
  * NAME: api-getQuestions.php
  * PARAMS (POST): $course_id - the id of the course to get the questions from.
  *   $chapter_id - the id of the chapter to get the questions from.
  * PURPOSE: Retrieves all data for the questions of the given course and chapter.
  */
  $query = $dbcon->prepare("SELECT question_id, question, comment 
  FROM question 
  WHERE course_id_fk = :course_id
    AND chapter_id_fk = :chapter_id
  ORDER BY question_id");

  $query->bindParam(':course_id', $_POST["course_id"]);
  $query->bindParam(':chapter_id', $_POST["chapter_id"]);
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);
?>