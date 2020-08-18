<?php
  /*
  * MADE BY: Walker Jones
  * PURPOSE: 
  */ 

  $course_id = $_POST["course_id"];
  $chapter_id = $_POST["chapter_id"];

  require('includes/conn.php');
  include('redir-notloggedin.php');

  $query = $dbcon->prepare("SELECT t.task_pk, t.task_name, t.description, ct.enabled, ct.point_value
    FROM chapter_task ct, task t 
    WHERE ct.course_id_fk = :course_id
    AND ct.chapter_id_fk = :chapter_id
    AND t.task_pk = ct.task_fk
    ORDER BY ct.task_fk");
  $query->bindParam(":course_id", $course_id);
  $query->bindParam(":chapter_id", $chapter_id);

  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($result);
?>