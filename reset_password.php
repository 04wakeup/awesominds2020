<?php
 
 
/* Password reset process, updates database with new user password */
require("includes/db.php");
session_start(); 
// Make sure the form is being submitted with method="post"
if ($_SERVER['REQUEST_METHOD'] == 'POST') { 
  // We get $_POST['email'] and $_POST['hash'] from the hidden input field of reset.php form  
  $c_number = $mysqli->escape_string($_POST['c_number']);
  $hash = $mysqli->escape_string($_POST['hash']);  
  // Make sure the two passwords match
  if ( $_POST['newpassword'] == $_POST['confirmpassword'] ) { 
    $new_password = password_hash($_POST['newpassword'], PASSWORD_BCRYPT); 
    $sql = "UPDATE user SET password='$new_password' WHERE c_number='$c_number' and hash='$hash'";
 

    if ( $mysqli->query($sql) ) { 
      $_SESSION['message'] = "Your password has been reset successfully! Please log in with your new password.";
      header("location: index.php");
    } else { 
      $_SESSION['message'] = "Error, please try again!";
      header("location: reset.php?c_number=$c_number&hash=$hash"); 
    } 
  } else { 
    $_SESSION['message'] = "Two passwords you entered don't match, try again!"; 
    header("location: reset.php?c_number=$c_number&hash=$hash"); // James: it's for both(reset url, change password options)
    // header("location: resetfromoptions.php");   // James: go to change password page 
  }
  // echo( $_SESSION['message'] );
}
?>