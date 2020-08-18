<?php
/*
* Creator: Adam Lowe
* Purpose: Gets the selectedcourse, selectedchapter, selectedtask for that user
*           and gets all the info for the system to determind to either insert or update score
* Date: July 20th 2020
*/
  require('includes/conn.php');
  include('redir-notloggedin.php');

  $query = $dbcon->prepare("SELECT * FROM score WHERE course_id_fk = :course_id_fk AND chapter_id_fk = :chapter_id_fk AND c_number_fk = :c_number_fk AND task_fk = :task_fk");
  $query->bindParam(':course_id_fk', $_GET["course_id_fk"]);
  $query->bindParam(':chapter_id_fk', $_GET["chapter_id_fk"]);
  $query->bindParam(':c_number_fk', $_SESSION["c_number"]);
  $query->bindParam(':task_fk', $_GET["task_fk"]);
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($result[0]);
?>
