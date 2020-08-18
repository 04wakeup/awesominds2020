<?php
//get all scores for a particular course and chapter (currently regardless of game mode)
  require('includes/conn.php');
  include('redir-notloggedin.php');

  $query = $dbcon->prepare("SELECT u.username, s.high_score, s.total_score, s.task_fk, s.attempts
                            FROM users u, score s
                            WHERE s.course_id_fk = :courseid
                            AND s.chapter_id_fk = :chapter
                            AND u.c_number = s.c_number");
  $query->bindParam(':courseid', $_GET["courseid"]);
  $query->bindParam(':chapter', $_GET["chapter"]);
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($result);
?>
