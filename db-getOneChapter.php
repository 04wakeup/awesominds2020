<?php
  require('includes/db.php');
  include('redir-notloggedin.php');

  /*
  * MADE BY: Walker Jones
  * PURPOSE: gets the info of a single chapter with the given ids.
  * currently only used to edit the contents
  */

  $course_id = $_POST["course_id"];
  $chapter_id = $_POST["chapter_id"];

  $query = "SELECT * 
  FROM chapter 
  WHERE course_id_fk = '$course_id'
  AND chapter_id = '$chapter_id'";

  if($result = $mysqli->query($query)){
    echo json_encode($result->fetch_assoc());
    $result->close();
  }
  $mysqli->close();
?>
