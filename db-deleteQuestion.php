<?php
  require('includes/conn.php');
  include('redir-notinstructor.php');

  /*
  * MADE BY: Walker Jones
  * PURPOSE: delete the question with the given id and shift all ids above it one down
  * to prevent gaps in the question_id. The answer table has cascade on delete and update so no delete 
  * or update statement for the answer table is necessary.
  */

  $course_id = $_POST["course_id"];
  $chapter_id = $_POST["chapter_id"];
  $question_id = $_POST["question_id"];

  $query = $dbcon->prepare("DELETE FROM question 
    WHERE course_id_fk = :course_id
    AND chapter_id_fk = :chapter_id
    AND question_id = :question_id");
  $query->bindParam(':course_id', $_POST["course_id"]);
  $query->bindParam(':chapter_id', $_POST["chapter_id"]);
  $query->bindParam(':question_id', $_POST["question_id"]);
  $query->execute();

  $query = $dbcon->prepare("UPDATE question 
    SET question_id = question_id - 1
    WHERE course_id_fk = :course_id
    AND chapter_id_fk = :chapter_id
    AND question_id >= :question_id");
  $query->bindParam(':course_id', $_POST["course_id"]);
  $query->bindParam(':chapter_id', $_POST["chapter_id"]);
  $query->bindParam(':question_id', $_POST["question_id"]);
  $query->execute();
?>
