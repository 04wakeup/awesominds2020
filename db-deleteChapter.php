<?php
  require('includes/db.php');
  include('redir-notinstructor.php');

  /*
  * MADE BY: Walker Jones
  * PURPOSE: delete the chapter with the given id. Every foreign key depending on a chapter_id has
  * on delete cascade and will get deleted automatically
  */

  $course_id = $_POST["course_id"];
  $chapter_id = $_POST["chapter_id"];

  $query = "DELETE FROM chapter
    WHERE course_id_fk = '$course_id'
    AND chapter_id = '$chapter_id'";

  if($result = $mysqli->query($query)){
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    $result->close();
  }
  $mysqli->close();
?>