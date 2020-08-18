<?php
  /*
  * MADE BY: Walker Jones
  * PURPOSE: Selects and returns all scores for a given course and possibly chapter
  * with usernames and task names. Used for inst-stats.php
  */ 

  require('includes/conn.php');
  include('redir-notinstructor.php');

  $sql = "SELECT s.c_number_fk, u.username, s.chapter_id_fk, 
      t.task_name, s.high_score, s.total_score, s.attempts
    FROM score s, user u, task t, chapter_task ct
    WHERE s.c_number_fk = u.c_number
    AND s.course_id_fk = ct.course_id_fk
    AND s.chapter_id_fk = ct.chapter_id_fk
    AND s.task_fk = ct.task_fk
    AND ct.task_fk = t.task_pk
    AND s.course_id_fk = :course_id";
  
  // The chapter_id may or may not be set, if it is, add a where clause to the sql. use isset()?
  if ($_POST["chapter_id"] != 0) {
    $sql .= " AND s.chapter_id_fk = :chapter_id";
  }

  $query = $dbcon->prepare($sql);
  $query->bindParam(':course_id', $_POST["course_id"]);

  if ($_POST["chapter_id"] != 0) {
    $query->bindParam(':chapter_id', $_POST["chapter_id"]);
  }
  
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($result);
?>