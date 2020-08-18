<?php
  /*
  * gets all chapters with a given chapter id.
  */
  require('includes/conn.php');
  include('redir-notloggedin.php');

  $query = $dbcon->prepare("SELECT * FROM chapter WHERE course_id_fk = :course_id ORDER BY chapter_id");
  $query->bindParam(':course_id', $_POST["course_id"]);
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);
?>
