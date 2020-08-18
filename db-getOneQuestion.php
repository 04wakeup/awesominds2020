<?php
  require('includes/conn.php');
  include('redir-notloggedin.php');

  /*
  * MADE BY: Walker Jones
  * PURPOSE: gets the info of a single question with the given ids.
  * currently only used to edit the contents
  */

  $course_id = $_POST["course_id"];
  $chapter_id = $_POST["chapter_id"];
  $question_id = $_POST["question_id"];

  $query = $dbcon->prepare("SELECT question, comment
  FROM question 
  WHERE course_id_fk = :course_id
  AND chapter_id_fk = :chapter_id
  AND question_id = :question_id");

$query->bindParam(":course_id", $course_id);
        $query->bindParam(":chapter_id", $chapter_id); 
        $query->bindParam(":question_id", $question_id); 
        $query->execute(); 
        $result = $query->fetchAll(PDO::FETCH_ASSOC);

        echo json_encode($result);

 
?>
