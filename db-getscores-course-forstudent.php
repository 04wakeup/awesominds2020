<?php
//get all scores for a particular course and chapter (currently regardless of game mode)
  require('includes/conn.php');
  include('redir-notloggedin.php');

  $query = $dbcon->prepare("SELECT u.username, s.high_score, s.total_score, s.chapter_id_fk, s.task, s.attempts
                            FROM users u, score s
                            WHERE s.course_id_fk = :course_id_fk
                            AND u.c_number = s.c_number");
  $query->bindParam(':course_id_fk', $_GET["course_id_fk"]);
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($result);
?>
