<?php
 /*
  * MADE BY: James
  * PURPOSE: Sending CSV files to proper instructor. It will update sent time and flag on success.
             It will retry to send a failed email until it succeeds for 3 days.
  * NOTES: 
     - --secure-file-priv option must be disabled!
     - This file is enrolled in crontab using mysql OS account
     - can be separated into 2 phps for exporting CSV and sending an email
     - SMTP will be changed to use CAMOSUN server
     - credential will be changed to use OS account private variable
     - some configurations maybe needed on server
     - execute this php with mysql OS account or permission error occour
     - enroll crontab like this:
        00 * * * * php /var/www/html/send-email-score.php
     - DB model will be modified:
      ALTER TABLE `awesominds`.`chapter` 
      ADD COLUMN `score_sent_date` DATETIME NULL AFTER `num_of_rounds`,
      ADD COLUMN `score_sent` CHAR(1) NULL DEFAULT '0' AFTER `score_sent_date`;
      
      create index chapter_score_sent on chapter (score_sent);

  * CONDITIONS(Option): set it, if you have problem
     getsebool httpd_can_sendmail is Off then turn On it
     sudo setsebool -P httpd_can_sendmail 1
     may need sudo 'setsebool -P httpd_can_network_connect 1'
     disable_function = ...... delete exec for exec() in php.ini
  */

  require('includes/conn.php');  
  require("/var/www/html/awesominds2020/PHPMailer/src/PHPMailer.php");  // full path because it's called by crontab
  require("/var/www/html/awesominds2020/PHPMailer/src/SMTP.php");
  $query = $dbcon->prepare("SELECT cs.inst_c_number_fk, cs.course_id, cs.course_name, ct.chapter_id, ct.chapter_name
                  ,ct.due_date, DATE_FORMAT(ct.due_date, '%H:%i:%s, %W %M %D, %Y') due_date2
                  ,u.username, u.email
              FROM course cs, chapter ct, user u
             WHERE cs.course_id = ct.course_id_fk 
               AND ct.score_sent = '0' 
               AND ct.due_date between DATE_ADD(now() , INTERVAL -3 DAY) AND now()   -- resend a failed eamil before success for 3 days 
               AND cs.inst_c_number_fk = u.c_number
               AND u.instructor = '1'");
   $query->execute();
   if($result = $query->fetchAll(PDO::FETCH_ASSOC)){  
      foreach($result as $value){     // repeat based on each chapter  
         $inst_c_number_fk = $value['inst_c_number_fk'];
         $course_id = $value['course_id'];      // don't use json_encode
         $course_name = $value['course_name'];
         $chapter_id = $value['chapter_id']; 
         $chapter_name = $value['chapter_name'];
         $due_date = $value['due_date'];
         $due_date2 = $value['due_date2'];
         $username = $value['username'];
         $email = $value['email']; 
         $filename = '/tmp/'.'('.$course_id.'-'.'Chapter '.$chapter_id.') '.$course_name.'_'.$chapter_name.'.csv';

         exec("rm -rf '$filename'", $output, $return); // try delete it first
         
         $query = $dbcon->prepare("SELECT 'Student ID', 'Total Points', 'End-of-Line Indicator'   -- create CSVs
                        UNION ALL 
                     SELECT CONCAT('#', s.c_number_fk) , CONVERT(SUM(s.total_score), CHAR) total_score, '#' 
                       FROM score s, chapter ct, course cs
                      WHERE s.course_id_fk = ct.course_id_fk 
                        AND s.chapter_id_fk = ct.chapter_id 
                        AND s.course_id_fk = cs.course_id  
                        AND s.course_id_fk = :course_id
                        AND s.chapter_id_fk = :chapter_id 
                      GROUP BY CONCAT('#', s.c_number_fk), '#'
                       INTO OUTFILE '$filename' fields terminated by ',' lines terminated by '\n'");

        $query->bindParam(':course_id', $course_id);
        $query->bindParam(':chapter_id', $chapter_id);
      
 
         if ($query->execute()){  // 1: created  
               $mail = new PHPMailer\PHPMailer\PHPMailer();
               $mail->IsSMTP(); // enable SMTP 
               //  $mail->SMTPDebug = 1; // debugging: 1 = errors and messages, 2 = messages only
               $mail->SMTPAuth = true; // authentication enabled
               $mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
               $mail->Host = "smtp.gmail.com";
               $mail->Port = 465; // or 587
               $mail->IsHTML(true);
               $mail->Username = " @gmail.com";
               $mail->Password = " $ ";
               $mail->SetFrom(" @gmail.com");
               $mail->Subject = "[".substr($due_date, 0, 10)."]".$course_id."-Chapter ".$chapter_id.": ".$chapter_name." Score Result";
               $mail->Body = "<b>".$course_id."-Chapter ".$chapter_id.", ".$chapter_name."</b> was due to <u>".$due_date2."</u><br> 
                              Please check the score result attached.<br><br>
                              AWESOMINDS 2020";
               $mail->AddAddress($email);
               $mail->addAttachment($filename);

               if($mail->Send()) { 
                   // echo "Message has been sent";
                   exec("rm -rf '$filename'", $output, $return); // delete garbage file
                   // Update success log on table 
                   $date = date("Y-m-d H:i:s"); 
                   $query = $dbcon->prepare("UPDATE chapter 
                                SET score_sent_date = '$date',
                                    score_sent = '1'
                              WHERE course_id_fk = :course_id
                                AND chapter_id = :chapter_id");

                  $query->bindParam(':course_id', $course_id);
                  $query->bindParam(':chapter_id', $chapter_id);

                   if($query->execute()){
                     //  echo $chapter_id .':'. $chapter_name." successfully updated to database."; 
                    } 
               } else {
                  // echo "Mailer Error: " . $mail->ErrorInfo;
               }

         }else{
               // echo "Error." . $mysqli->error;
         }  
      } 
    
    }   
     
?>