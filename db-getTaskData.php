<?php
  /*
  * PURPOSE: Retrieves task information and returns them sorted by task id
  */
  
	require("includes/conn.php");
	include('redir-notinstructor.php');

  if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $course_id = $_POST['course_id'];
    $chapter_id = $_POST['chapter_id'];
    
    $query = $dbcon->prepare("SELECT * 
      FROM chapter_task 
      WHERE course_id_fk = :course_id 
        AND chapter_id_fk = :chapter_id
      ORDER BY task_fk");

    $query->bindParam(":course_id", $course_id);
    $query->bindParam(":chapter_id", $chapter_id);
    $query->execute();
    
    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    if($result){
      echo json_encode($result);
    }
  } else {
    echo json_encode("error");
  } 
?>