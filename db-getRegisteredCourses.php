<?php
  require('includes/conn.php');
  include('redir-notloggedin.php');

  /*
  * MADE BY: Walker Jones
  * PURPOSE: Gets all the courses the current user is registered in
  */

  $c_number = $_SESSION["c_number"];

  $sql = "SELECT c.course_id, c.course_name, c.hidden
    FROM course_registration cr, course c 
    WHERE cr.c_number_fk = :c_number
    AND c.course_id = cr.course_id_fk";

  $query = $dbcon->prepare($sql);
  $query->bindParam(":c_number", $c_number);
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);
?>
