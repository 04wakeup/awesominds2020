<?php
  require('includes/conn.php');
  include('redir-notloggedin.php');

$query = $dbcon->prepare("select * from course");
$query->execute();

$result = $query->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($result);
?>
