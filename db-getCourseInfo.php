
<?php
  require('includes/db.php');
  include('redir-notloggedin.php');

  /*
  * MADE BY: Adam Lowe
  * PURPOSE: gets the course_id and course_name and fills the field for the course id.
  */

  $course_id = $_POST["course_id"];

  $query = "SELECT course_id, course_name
  FROM course
  WHERE course_id = '$course_id'";

  if($result = $mysqli->query($query)){
    echo json_encode($result->fetch_assoc());
    $result->close();
  }
  $mysqli->close();
?>