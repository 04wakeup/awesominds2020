<?php
  require('includes/conn.php');
  include('redir-notinstructor.php');

  $query = $dbcon->prepare("INSERT INTO score 
  VALUES (:c_number_fk, :course_id_fk, :chapter_id_fk, :task_fk, 
    :total_score, :high_score, 1)
  ON DUPLICATE KEY UPDATE
  total_score = total_score + :total_score,
  high_score = GREATEST(high_score, :high_score),
  attempts = attempts + 1");

  $query->bindParam(':chapter_id_fk', $_POST["chapter_id_fk"]);
  $query->bindParam(':course_id_fk', $_POST["course_id_fk"]);
  $query->bindParam(':c_number_fk', $_SESSION["c_number"]);
  $query->bindParam(':high_score', $_POST["high_score"]);
  $query->bindParam(':total_score', $_POST["total_score"]);
  $query->bindParam(':task_fk', $_POST["task_fk"]);
  $result = $query->execute();

  if($result){
    echo json_encode($result);
  } else {
    echo json_encode($query->errorInfo());
  }
?>
