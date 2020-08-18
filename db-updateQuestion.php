<?php
  require('includes/db.php');
  include('redir-notinstructor.php');

  $course_id = $_POST["course_id"];
  $chapter_id = $_POST["chapter_id"];
  $question_id = $_POST["question_id"];
  $question = $_POST["question"];
  $comment = $_POST["comment"];
  $answers = $_POST["answers"];
  $correctAnswer = $_POST["correctAnswer"];


  $query = "UPDATE question 
    SET question = '$question',
      comment = '$comment'
    WHERE course_id_fk = '$course_id'
      AND chapter_id_fk = '$chapter_id'
      AND question_id = '$question_id'";
  $mysqli->query($query);

  // delete all answers for the question. this is easier than updating since if options are added,
  // they can't be updated since they don't exist in the database
  $query = "DELETE FROM answer
  WHERE course_id_fk = '$course_id'
    AND chapter_id_fk = '$chapter_id'
    AND question_id_fk = '$question_id'";
  $mysqli->query($query);

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
?>
