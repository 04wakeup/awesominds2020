<?php
 /*
  * MADE BY: James Choi Jones
  * PURPOSE:   Reset your password form, sends reset.php password link 
  */

require("./PHPMailer/src/PHPMailer.php");
require("./PHPMailer/src/SMTP.php");
require("includes/conn.php");
session_start();

// Check if form submitted with method="post"
if ( $_SERVER['REQUEST_METHOD'] == 'POST' )
{
    $c_number = test_input($_POST['c_number']);
    $query = $dbcon->prepare("SELECT * FROM user WHERE c_number=:c_number");
    $query->bindParam(':c_number', $c_number);
    $query->execute();

    if ( $query->rowCount() == 0 ) // User doesn't exist
    {
        $_SESSION['message'] = "User with that ID doesn't exist!";
        header("location: index.php");
    }
    else { // User exists (num_rows != 0)

        $user = $query->fetch(PDO::FETCH_ASSOC); // $user becomes array with user data
        // Session message to display on success.php
        $_SESSION['message'] = "<p>Please check your email for a link to complete your password reset!</p>";

        // Send registration confirmation link (reset.php) 
        // James: Email send
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

        $mail->Subject = 'Password Reset Link ( Awesominds 2020 )';
        $mail->Body = 'A password reset has been requested on your Awesominds 2020 account.<br>
                       Please click this link to reset your password:<br>
                       http://asm.camosun.bc.ca/awesominds2020/reset.php?c_number='.$c_number.'&hash='.$user['hash'];
        $mail->AddAddress($user['email']);  
        if(!$mail->Send()) {
            // echo "Mailer Error: " . $mail->ErrorInfo;
        } else {
            // echo "Message has been sent";
        } 
        header("location: index.php");
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Reset Your Password - Awesominds 2020</title>
  <?php include 'css/css.html'; ?>
</head>

<body>
  <?php include 'inst-nav2.php'; ?>
  <div class="container text-center">
    <h2>Reset Your Password</h2>
    <p>Enter your Camosun ID to receive a password reset link via the email address you registered from.</p>
    <form action="forgot.php" method="post">
      <div class="form-group container" id="loginPart1" style="max-width: 400px;">
        <label for="cnumberInput" class="form-label"><b>Camosun ID*</b></label>
        <div class="input-group">
          <input class="form-control" type="text" required autocomplete="off" name="c_number" id="cnumberInput"/>
          <span class="input-group-btn"><button class="btn btn-primary" id="resetBtn">Submit</button></span>
        </div>
      </div>
    </form>
  </div>
</body>

</html>
