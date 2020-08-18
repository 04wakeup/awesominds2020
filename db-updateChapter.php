<?php
  require("includes/db.php");
  include('redir-notinstructor.php');

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    $course_id = strtoupper($mysqli->escape_string($_POST["course_id"]));
    $chapter_id = $mysqli->escape_string($_POST["chapter_id"]);
    $chapter_name = $mysqli->escape_string($_POST["chapter_name"]);
    $preamble = $mysqli->escape_string($_POST["preamble"]);
    $start_date = $mysqli->escape_string($_POST["start_date"]);
    $due_date = $mysqli->escape_string($_POST["due_date"]);
    $end_date = $mysqli->escape_string($_POST["end_date"]);

    $query = "UPDATE chapter 
      SET chapter_name = '$chapter_name',
      preamble = '$preamble',
      start_date = '$start_date',
      due_date = '$due_date',
      end_date = '$end_date'
      WHERE course_id_fk = '$course_id'
      AND chapter_id = '$chapter_id'";

    if($result = $mysqli->query($query)){
      echo $chapter_id .':'. $chapter_name." successfully added to database.";
    }
    $mysqli->close();
  }
?>
