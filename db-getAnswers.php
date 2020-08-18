<?php
  require('includes/conn.php');
  include('redir-notloggedin.php');
  /*
  * MADE BY: Walker Jones
  * NAME: api-getAnswers.php
  * PARAMS (POST): $course_id - the id of the course to get the answers from.
  *   $chapter_id - the id of the chapter to get the answers from.
  *   $question_id - the id of the question to get the answers from.
  * PURPOSE: Retrieves all data for the answers of the given course, chapter and question.
  */

  $query = $dbcon->prepare("SELECT question_id_fk, answer_id, answer, correct 
  FROM answer 
  WHERE course_id_fk = :course_id
    AND chapter_id_fk = :chapter_id
    AND question_id_fk = :question_id
  ORDER BY answer_id");
  $query->bindParam(':course_id', $_POST["course_id"]);
  $query->bindParam(':chapter_id', $_POST["chapter_id"]);
  $query->bindParam(':question_id', $_POST["question_id"]);
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);

?>