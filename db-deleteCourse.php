<?php
  require('includes/db.php');
  include('redir-notinstructor.php');

  /*
  * MADE BY: Walker Jones
  * PURPOSE: delete the course with the given id. Every foreign key depending on a course_id has
  * on delete cascade and will get deleted automatically
  */

  $course_id = $_POST["course_id"];

  $query = "DELETE FROM course
    WHERE course_id = '$course_id'";

  if($result = $mysqli->query($query)){
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
    $result->close();
  }
  $mysqli->close();
?>