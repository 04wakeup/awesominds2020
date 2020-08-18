<?php
  require('includes/conn.php');
  include('redir-notloggedin.php');

  $query = $dbcon->prepare("SELECT DISTINCT chapter_id_fk FROM score WHERE course_id_fk = :course_id ORDER BY chapter_id_fk");
  $query->bindParam(':course_id', $_GET["course_id"]);
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);
?>
