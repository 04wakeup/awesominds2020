<?php 
/* User login process part 2, checks password and logs user in if correct */ 
require 'includes/conn.php';
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST'){
  $c_number = $_SESSION['c_number'];
  $query = $dbcon->prepare("SELECT * FROM user WHERE c_number=:c_number");
  $query->bindParam(':c_number', $c_number);
  $query->execute();
  if ( $query->rowCount() == 0 ){ // User doesn't exist; shouldn't happen if reached part 2 but keeping the logic here just in case
      $_SESSION['message'] = '<small class="error">User with Camosun ID "'.$c_number.'" doesn\'t exist. Try again or <a href="signup.php">create an account</a>.</small>';
  } else { // User exists
    
    $user = $query->fetch(PDO::FETCH_ASSOC);
    
    if($user['instructor']){ //instructor, check password, James: use new column
      if (password_verify($_POST['password'], $user['password'])) {  
      
        $_SESSION['c_number'] = $user['c_number'];
        $_SESSION['play_name'] = $user['username'];
        $_SESSION['avatarnum'] = $user['avatar_fk'];
        // $_SESSION['active'] = $user['active']; James: not used anymore
        $_SESSION['isInstructor'] = $user['instructor'];
        $_SESSION['user_volume'] = $user['volume'];

        // This is how we'll know the user is logged in
        $_SESSION['logged_in'] = true;

      } else {
        $_SESSION['message'] = 'Incorrect password, try again!<br>Logging in as ' . $user['c_number'] .'. <a href="">Cancel</a>';
        $_SESSION['loginpart2'] = 2;
      }
    } else { //not an instructor, do the student login process
      //check avatar and name of student to log them in
      if( $_POST['avatarSelect'] == $user['avatar_fk'] && $_POST['nameSelect'] == substr($user['username'], 0, -3)){
        $_SESSION['c_number'] = $user['c_number'];
        $_SESSION['play_name'] = $user['username'];
        $_SESSION['avatarnum'] = $user['avatar_fk'];
        // $_SESSION['active'] = $user['active'];  James: not used anymore
        $_SESSION['isInstructor'] = $user['instructor'];
        $_SESSION['user_volume'] = $user['volume'];

        // This is how we'll know the user is logged in
        $_SESSION['logged_in'] = true; 
        
      } else {
        $_SESSION['message'] = 'Incorrect login, try again!';
      }

    }
  }
}
header("location: index.php");
