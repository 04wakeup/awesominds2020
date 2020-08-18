<?php
  /*
  * PURPOSE: Retrieves round information for the given course and chapter.
  * Encodes data in json and echos it for use in original file.
  */

	require("includes/conn.php");
  include('redir-notloggedin.php');
  
  if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $course_id = $_POST["course_id"];
    $chapter_id = $_POST["chapter_id"];

    //TODO: change to dbcon
    
    $query = $dbcon->prepare("SELECT r.round_id, r.questions, r.max_point, r.goal, r.point_goal, r.challenge_fk, c.challenge_name, c.challenge_description
                              FROM round r, challenge c
                              WHERE r.course_id_fk = :course_id
                                AND r.chapter_id_fk = :chapter_id
                                AND r.challenge_fk = c.challenge_pk
                              ORDER BY round_id");

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