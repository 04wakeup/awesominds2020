<?php
  require('includes/conn.php');
  include('redir-notloggedin.php');

  $query = $dbcon->prepare("SELECT * FROM chapter WHERE chapter_id = :chapter AND course_id_fk = :course_id_fk");
  $query->bindParam(':chapter', $_GET["chapter"]);
  $query->bindParam(':course_id_fk', $_GET["course_id_fk"]);
  $query->execute();

  $result = $query->fetch(PDO::FETCH_ASSOC);
  echo json_encode($result);
?>
