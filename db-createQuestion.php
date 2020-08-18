<?php
  require('includes/db.php');
  include('redir-notinstructor.php');

  /*
  * PURPOSE: insert a question with the next highest id. Afterwards, insert a 
  * answer for each item in the answers array
  */

  $course_id = $_POST["course_id"];
  $chapter_id = $_POST["chapter_id"];
  $question_id = 1; 
  $question = $_POST["question"];
  $comment = $_POST["comment"];
  $answers = $_POST["answers"]; // array
  $correctAnswer = $_POST["correctAnswer"];

  $query = "SELECT max(question_id) AS 'question_id'
    FROM question
    WHERE course_id_fk = '$course_id'
    AND chapter_id_fk = '$chapter_id'";

  if($result = $mysqli->query($query)){
    if ($result->num_rows > 0) {
      $row = $result->fetch_row();
      $question_id = $row[0];
      $question_id = $question_id + 1;
    }
    $result->close();
  }

  // Insert question info into question table
  $query = "INSERT INTO question
    VALUES ('$course_id', '$chapter_id', '$question_id', '$question', '$comment')";
  $mysqli->query($query);

  // id starts at one,
  $answer_id = 1;
  foreach ($answers as $answer) {
    $correct = 0;
    if ($correctAnswer == chr($answer_id + 64)) { // 65 is ascii for A.
      $correct = 1;
    }

    $query = "INSERT INTO answer
      VALUES ('$course_id', '$chapter_id', '$question_id', '$answer_id', '$answer', '$correct')";
    $mysqli->query($query);

    $answer_id = $answer_id + 1;
  }
  $mysqli->close();

  //insertIntoDB($questionBank, $insertChapter, $courseid, $dbcon, $question_id);
  //echo $question_id . " questions uploaded for chapter " . $insertChapter . " on course: " . $courseid;
?>
