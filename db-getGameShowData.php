<?php
  /*
  * PURPOSE: Retrieves game show information for the given course and chapter.
  * Encodes data in json and echos it for use in original file.
  */

	require("includes/conn.php");
	include('redir-notloggedin.php');
  if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $course_id = $_POST["course_id"];
    $chapter_id = $_POST["chapter_id"];
    
    $query = $dbcon->prepare("SELECT lives, in_a_row_number, in_a_row_point, game_theme, num_of_rounds 
      FROM chapter 
      WHERE course_id_fk = :course_id 
        AND chapter_id = :chapter_id");

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