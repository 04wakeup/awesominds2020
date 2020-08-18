<?php
if(!isset($_SESSION)) { 
    session_start(); 
} 

/*
* until users are made active upon logging in, leave this code commented as a failsafe in case active
* gets set to false somehow.
*/
if(!$_SESSION['logged_in'] /*|| !$_SESSION['active']*/ ){
  header("location: index.php");
}
?>
