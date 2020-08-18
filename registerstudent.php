<?php 
/* Registration process, inserts user info into the database */

// Escape all $_POST variables to protect against SQL injections
$play_name = $mysqli->escape_string($_POST['fakename']);
$c_number = $mysqli->escape_string($_POST['cnumber']);
$avatarnum = $_POST['avatarnum'];

// Check if user with that c number already exists
$query = $dbcon->prepare("SELECT * FROM user WHERE c_number=:c_number");
$query->bindParam(':c_number', $c_number); 
$query->execute(); // execute always! it returns true/false;

// We know user id exists if the rows returned are more than 0 
if ( $query->fetch(PDO::FETCH_ASSOC) ) {
  $_SESSION['message'] = 'User with this Camosun ID already exists! Please log in.';
  header("location: index.php");
} else { // id doesn't already exist in a database, proceed... 
  // add last 2 digits of C number to the display name 
  $play_name .= ' ' . substr($c_number, -2); 
  // Set session variables to be used on profile.php page
  $_SESSION['c_number'] = $c_number;
  $_SESSION['play_name'] = $play_name;
  $_SESSION['avatarnum'] = $avatarnum;  

  if(isset($_SESSION['invitecode'])){ //register as instructor and consume invite 
    $query = $dbcon->prepare("SELECT * FROM invite WHERE invite_code = :invitecode");
    $query->bindParam(':invitecode', $_SESSION["invitecode"]);  
    $query->execute();
    $result = $query->fetch(PDO::FETCH_ASSOC); 
    if($result){ //code exists 
      $password = $mysqli->escape_string(password_hash($_POST['password'], PASSWORD_BCRYPT));
      $hash = $mysqli->escape_string( md5( rand(0,1000) ) ); // James: hash is used for forgot password url, it idendify the user 
      $email = $result['email'];  
      $sql = $dbcon->prepare("INSERT INTO user (c_number, username, avatar_fk, email, instructor, password, hash, volume)
                              VALUES (:c_number, :play_name, :avatarnum, :email, 1, :password, :hash, 0.2)"); 
      $sql->bindParam(':c_number', $c_number); 
      $sql->bindParam(':play_name', $play_name); 
      $sql->bindParam(':avatarnum', $avatarnum); 
      $sql->bindParam(':email', $email); 
      $sql->bindParam(':password', $password); 
      $sql->bindParam(':hash', $hash); 
      $result = $sql->execute();  

    } else { // code does not exist 
      $_SESSION['message'] = 'Invalid invite code!';
      header("location: index.php");
    }

  } else { //register as student
    $sql = $dbcon->prepare("INSERT INTO user (c_number, username, password, avatar_fk, instructor, volume) "
         . "VALUES (:c_number, :play_name, 'n/a', :avatarnum, 0, 0.2)");
    $sql->bindParam(':c_number', $c_number); 
    $sql->bindParam(':play_name', $play_name); 
    $sql->bindParam(':avatarnum', $avatarnum);  
    $result = $sql->execute();
   
  }
 
  // Add user to the database and log them in
  if ($result){
    $_SESSION['active'] = 1; //accounts no longer need email verification  
    $_SESSION['logged_in'] = 1; // So we know the user has logged in
    if(isset($_SESSION['invitecode'])){ //if we got this far with an invite code, it was valid and used to register an instructor. delete the invite
      $inviteCode = $_SESSION['invitecode'];
      $_SESSION['isInstructor'] = 1;
      // $mysqli->query("DELETE FROM invite WHERE invite_code='$inviteCode'");

      $sql = $dbcon->prepare("DELETE FROM invite WHERE invite_code=:inviteCode");
      $sql->bindParam(':inviteCode', $inviteCode);  
      $sql->execute();

      unset($_SESSION['invitecode']);
    }
    header("location: index.php");
  } else {
    // $_SESSION['message'] = "Registration failed! " . $mysqli->error; James: make it user friendly
    $_SESSION['message'] = "Registration failed!! Contact system administrator.";
  }
}
header("location: index.php");
