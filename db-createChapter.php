<?php
  require("includes/conn.php");
  include('redir-notinstructor.php');

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    $course_id = strtoupper(test_input($_POST["course_id"]));
    $chapter_id = test_input($_POST["chapter_id"]);
    $chapter_name = test_input($_POST["chapter_name"]);
    $preamble = test_input($_POST["preamble"]);
    $start_date = test_input($_POST["start_date"]);
    $due_date = test_input($_POST["due_date"]);
    $end_date = test_input($_POST["end_date"]);

    $query = $dbcon->prepare("INSERT INTO chapter (course_id_fk, chapter_id, chapter_name, preamble, start_date, due_date, end_date)
      VALUES (:course_id, :chapter_id, :chapter_name, :preamble, :start_date, :due_date, :end_date)");
      
    $query->bindParam(':course_id', $course_id);
    $query->bindParam(':chapter_id', $chapter_id);
    $query->bindParam(':chapter_name', $chapter_name);
    $query->bindParam(':preamble', $preamble);
    $query->bindParam(':start_date', $start_date);
    $query->bindParam(':due_date', $due_date);
    $query->bindParam(':end_date', $end_date);
    
    $result = $query->execute();
    echo json_encode($result);
  }
?>
