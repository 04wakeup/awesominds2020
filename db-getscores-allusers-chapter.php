<?php
//get all scores for a particular course and chapter (currently regardless of game mode)
  require('includes/conn.php');
  include('redir-notinstructor.php');

  $query = $dbcon->prepare("select u.c_number, u.username, s.high_score, s.total_score, s.chapter_id_fk, s.task_fk, s.attempts
                            from user u, score s
                            where  u.c_number = s.c_number_fk
                            and s.course_id_fk = :courseid
                            and s.chapter_id_fk = :chapter");
  $query->bindParam(':courseid', $_GET["courseid"]);
  $query->bindParam(':chapter', $_GET["chapter"]);
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($result);
?>
