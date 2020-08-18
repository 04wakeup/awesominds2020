<?php
  require("includes/db.php");
  include('redir-notinstructor.php');

  /*
  *Creator: Adam Lowe
  *Purpose: Updates the database with the new course_name using the course_id
  */

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    $course_id = $mysqli->escape_string($_POST['courseID']);
    $course_name = $mysqli->escape_string($_POST['courseName']);

    $query = "UPDATE course 
    SET course_name = '$course_name'
    where course_id = '$course_id'";

    if($mysqli->query($query)){
      echo $course_id .':'. $course_name." successfully added to database.";
    }else{
      echo "Error inserting course into database." . $mysqli->error;
    }
  }

  $mysqli->close();
?>
