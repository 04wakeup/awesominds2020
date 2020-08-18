<?php
session_start();

// We could just include redir-notloggedin.php and then test if instructor over that - Walker

/*
* until users are made active upon logging in, leave this code commented as a failsafe in case active
* gets set to false somehow.
*/
if(!$_SESSION['logged_in'] /*|| !$_SESSION['active'] */ || !$_SESSION['isInstructor']){
  header("location: index.php");
}
?>
