<?php 
/* User login process, checks if user exists and whether they are instructor or not*/
require 'includes/conn.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $c_number = test_input($_POST['cnumber']);
  $query = $dbcon->prepare("SELECT * FROM user WHERE c_number=:c_number");
  $query->bindParam(':c_number', $c_number);
  $query->execute();

  if ( $query->rowCount() == 0 ){ // User doesn't exist
    $_SESSION['message'] = "Looks like it's your first time here, " . $c_number . ". Please create an account.";
    $_SESSION['c_number_signup'] = $c_number;
    header("location: signup.php");
  } else { // User exists
    $user = $query->fetch(PDO::FETCH_ASSOC);
    $_SESSION['message'] = 'Logging in as ' . $user['c_number'] .'. <a href="">Cancel</a>';
    $_SESSION['c_number'] = $user['c_number'];
 
    if($user['instructor']){  // James: replace with new column
      $_SESSION['loginpart2'] = 2; 
    } else {
      $_SESSION['loginpart2'] = 1;

      $avatarKeys = range(1, 31);  // James : 31 avartars
      // unset($avatarKeys[array_search($user['avatarnum'],$avatarKeys)]);  // James: replace with avatar_fk
      unset($avatarKeys[array_search($user['avatar_fk'],$avatarKeys)]);   
      shuffle($avatarKeys);
      $avatarKeys = array_slice($avatarKeys, 0, 2);
      // array_push($avatarKeys, $user['avatarnum']);  // James: replace with avatar_fk
      array_push($avatarKeys, $user['avatar_fk']);  
      shuffle($avatarKeys);
      $_SESSION['avatarKeys'] = $avatarKeys;
 
      // $names = array($_POST['name1'], $_POST['name2'], $_POST['name3'], substr($user['play_name'], 0, -3)); // James: replace with username
      $names = array($_POST['name1'], $_POST['name2'], $_POST['name3'], substr($user['username'], 0, -3));
      shuffle($names);
      $_SESSION['names'] = $names;
    }
    header("location: index.php");
  }
}
