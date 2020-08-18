<?php 

 /*
  * MADE BY: James Choi Jones
  * PURPOSE: send the reg link for instructor, Camosun may not allow to use email service, so view code added 
  * NOTES: if you want to use gmail as mail service, you need to change the password whereever on each server.
  */

  require("./PHPMailer/src/PHPMailer.php");
  require("./PHPMailer/src/SMTP.php");
  require('includes/conn.php');
  include('redir-notinstructor.php'); 
 
  $query = $dbcon->prepare("SELECT invite_code FROM invite WHERE email = :email_sentto"); 
  $query->bindParam(':email_sentto', $_POST["email"]);  
  $query->execute(); 
  $result = $query->fetchAll(PDO::FETCH_ASSOC); 
 
  //James: it exists, use it or create new one
  if ($result){
    $inviteCode = $result[0]['invite_code']; 
  }
  else {
    $inviteCode = sha1(uniqid($_POST["email"], true));

    $query = $dbcon->prepare("INSERT INTO invite (invite_code, email, c_number_fk) VALUES (:invitecode, :email_sentto, :c_number_sentby)");

    $query->bindParam(':invitecode', $inviteCode);
    $query->bindParam(':email_sentto', $_POST["email"]);
    $query->bindParam(':c_number_sentby', $_SESSION["c_number"]);

    $result = $query->execute();
  }
   
  // if($result){   // James: previous version using native mail service
  //   $subject = 'Account Verification (Awesominds 2020)';
  //   $headers = "From: Awesominds Registration <noreply@gbl.cs.camosun.bc.ca>" . "\r\n" .
  //              "Reply-To: noreply@gbl.cs.camosun.bc.ca" . "\r\n" .
  //              "X-Mailer: PHP/" . phpversion();
  //   $message_body = 'You have been invited to create an Awesominds 2020 instructor account!

  //                   Please click this link to create your account:

  //                   http://gbl.cs.camosun.bc.ca/awesominds/signup.php?invitecode='.$inviteCode;

  //   mail( $_POST["email"], $subject, $message_body, $headers );
    
  //   $output = $query->rowCount();
  //   echo json_encode($output);

  // } else {
  //   echo json_encode($query->errorInfo());
  // }
  if($_POST["task"] == 'send'){
      try{ 
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->IsSMTP(); // enable SMTP 
        // $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
        $mail->SMTPAuth = true; // authentication enabled
        $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
        $mail->Host = "smtp.gmail.com";
        $mail->Port = 465; // or 587
        $mail->IsHTML(true);
        $mail->Username = " @gmail.com";
        $mail->Password = " $ ";
        $mail->SetFrom(" @gmail.com");
        $mail->Subject = 'Account Verification (Awesominds 2020)';
        $mail->Body = 'You have been invited to create an Awesominds 2020 instructor account! 
                      Please click this link to create your account: 
                      http://asm.camosun.bc.ca/awesominds2020/signup.php?invitecode='.$inviteCode;
        $mail->AddAddress($_POST["email"]);  
        $sent = $mail->Send();  
        echo $inviteCode;   
    } catch(Exception $e){  // don't know how can handle 500 error, so new button is created for manual creation. 
      echo 'Fail';
    }
  } else {
    echo $inviteCode;   
  }
  
?>
