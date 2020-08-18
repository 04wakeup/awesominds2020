<?php
  /*
  * PURPOSE: Retrieves challenges information.
  * Encodes data in json and echos it for use in original file.
  */

	require("includes/conn.php");
  include('redir-notinstructor.php');
  
  $query = $dbcon->prepare("SELECT *
                            FROM challenge 
                            ORDER BY challenge_pk");

  $query->execute();
  $result = $query->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($result);
   
?>