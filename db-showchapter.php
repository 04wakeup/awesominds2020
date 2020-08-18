<?php
  require("includes/conn.php");
  include('redir-notinstructor.php');

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    $courseid = strtoupper(test_input($_POST['course_id_fk']));
    $chapterid = test_input($_POST['chapter_id']);
    //$chaptername = test_input($_POST['chaptername']);
    $date_start = test_input($_POST['start_date']);
    $due_date = test_input($_POST['due_date']);
    $date_end = test_input($_POST['end_date']);

        $stmt = $dbcon->prepare("UPDATE chapter SET start_date = :date_start, due_date = :due_date, end_date = :end_date WHERE course_id_fk = :course_id_fk AND chapter_id = :chapter_id");

    $stmt->bindParam(':course_id_fk', $courseid);
    $stmt->bindParam(':chapter_id', $chapterid);
    //$stmt->bindParam(':chaptername', $chaptername);
    $stmt->bindParam(':date_start', $date_start);
    $stmt->bindParam(':due_date', $due_date);
    $stmt->bindParam(':end_date', $date_end);

    if($stmt->execute()){
      echo $chapterid .':'. $chaptername." successfully added to database.";
    }else{
      echo "Error inserting chapter into database.";
      print_r($stmt->errorInfo());
    }
  }

?>
