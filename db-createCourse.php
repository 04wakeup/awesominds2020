<?php
  require("includes/conn.php");
  include('redir-notinstructor.php');

  if($_SERVER["REQUEST_METHOD"] == "POST"){
    $course_id = test_input($_POST['courseID']);
    $course_name = test_input($_POST['courseName']);
    $regcode = sha1(uniqid($course_id, true));
    $c_number = $_SESSION['c_number'];

    $query = $query = $dbcon->prepare("INSERT INTO course (course_id, course_name, hidden, regcode, inst_c_number_fk)
      VALUES (:course_id, :course_name, false, :regcode, :c_number)");
    $query->bindParam(':course_id', $course_id);
    $query->bindParam(':course_name', $course_name);
    $query->bindParam(':regcode', $regcode);
    $query->bindParam(':c_number', $c_number);
    $query->execute();

    $result = $query->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
  }
?>
