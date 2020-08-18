<?php
/*
Creator: Adam Lowe
Purpose: To show only instructors courses in "inst-showdates.php"
Date: June 7th 2020
*/
  require('includes/conn.php');
  include('redir-notloggedin.php');
    $c_number = $_SESSION['c_number'];
    $query = $dbcon->prepare("SELECT * FROM course WHERE inst_c_number_fk = :c_number");
    // instructor or student can play that course
    /*
    $query = $dbcon->prepare("SELECT c.course_id AS courseid, c.course_name AS name, c.*
                                FROM course c, course_registration cr 
                               WHERE c.course_id = cr.course_id_fk 
                                 AND cr.c_number_fk = :c_number
                                UNION ALL 
                              SELECT c.course_id AS courseid, c.course_name AS name, c.*
                                FROM course c 
                               WHERE c.inst_c_number_fk = :c_number"); 
                               */
    $query->bindParam(':c_number', $_SESSION["c_number"]);
    $query->execute();

    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
?>
