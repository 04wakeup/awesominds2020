<head>
  <meta charset="UTF-8" />
  <?php include('redir-notloggedin.php'); ?>
  <title>Welcome <?= $_SESSION['play_name'] ?></title>
  <?php include 'css/css.html'; ?>
  <?php include 'inst-nav2.php' ?>
</head>

<body>
  <div class="container text-center">
    <h2>Welcome to Awesominds 2020</h2>
    <p>
    <?php 
  
    // Display any set message only once, then remove it
    if ( isset($_SESSION['message']) ){
      echo $_SESSION['message'];
      unset( $_SESSION['message'] );
    }
    if(isset($_SESSION['regcode'])){ //if there's a registration code, register in the course, let them know, and remove the code from the session
      require('includes/conn.php');
      $stmt = $dbcon->prepare("SELECT course_id FROM course WHERE regcode = :regcode");  // James: use new col name
      $stmt->bindParam(':regcode', $_SESSION['regcode']);
      $stmt->execute();
      $result = $stmt->fetch(PDO::FETCH_ASSOC);
      if($result){
        // $stmt2 = $dbcon->prepare("INSERT INTO usercoursereg (c_number, courseid) VALUES(:c_number, :courseid)");
        $stmt2 = $dbcon->prepare("INSERT INTO course_registration (c_number_fk, course_id_fk) VALUES(:c_number, :courseid)"); // James: use new table
        $stmt2->bindParam(':c_number', $_SESSION['c_number']);
        $stmt2->bindParam(':courseid', $result['course_id']);
        if($stmt2->execute()){
          echo '<div class="alert alert-success" role="alert">
                  You have successfully registered for ' . $result['course_id'] .'
                </div>';
        }
      } else {
        echo '<div class="alert alert-danger" role="alert">
                Invalid registration link, please try again!
              </div>';
      }
      unset($_SESSION['regcode']);
    } 
    ?>
    <!-- Populate greeting page after user logs in, depending on instructor status -->
    <!-- Instructors have several options available to them, such as manage courses, manage tasks/games, etc. -->
    <?php if (!isset($_SESSION['isInstructor'])) { ?>
      <h4>You are logged in as</h4><br>
      <?php echo '<img src="assets/opp2/oppon' . $_SESSION['avatarnum'] . '.png" width=120/>'; ?>
      <h3><?php echo $_SESSION['play_name']; ?></h3><h5><?php echo $_SESSION['c_number']; ?></h5><br>
    <?php } else { ?>
      <h4 style="display: inline-block; font-size: 24px"><div style="display: inline-block"><div style="display: inline-block">You are logged in as <?php echo $_SESSION['play_name']; ?></div><div style="display: inline-block; margin-left: 0.4em"><?php echo $_SESSION['c_number']; ?></div>&nbsp;</div><div style="display: inline-block; text-align: right"><?php echo '<img src="assets/opp2/oppon' . $_SESSION['avatarnum'] . '.png" width=60 height=80/>'; ?></div></h4><br>
    <?php } ?>

    <!-- James: show last log on info and deadline chapters -->
    <?php
      // Error_reporting(E_ALL);
      // ini_set('display_errors',1);
      if (!isset($_SESSION['isUpdateLoginTime'])){
        require('includes/db.php');  
        $num = 0;
        $c_number = $_SESSION['c_number']; 
        $result = $mysqli->query("SELECT CONCAT('Last time you were here was ', if(CAST(NOW() as DATE) != u.last_on, CONCAT(datediff(NOW(), u.last_on),' day',if(datediff(NOW(), u.last_on) = 1, '', 's'),' ago.'), 'today.')) AS notice
                                    FROM user u 
                                  WHERE c_number = '$c_number'
                                    UNION ALL   
                                  SELECT concat(c.course_id_fk, ' chapter ', c.chapter_id, '(', c.chapter_name, '): ', c.due_date)
                                    FROM chapter c, course_registration cr 
                                  WHERE c.course_id_fk = cr.course_id_fk
                                    AND cr.c_number_fk = '$c_number'
                                    AND c.due_date BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY)"); 
        if ($result->num_rows > 0) { 
          while($row = $result->fetch_assoc()) {
            $num++;
            if($num == 2){
              echo "<br><h6>Chapters due in the upcoming week:</h6>";
            }
            echo "<h6>".$row['notice']."</h6>";
          }
        }  

        // James : update login time after getting notice
        $mysqli->query("UPDATE user SET last_on = now() WHERE c_number='$c_number'");
        $_SESSION['isUpdateLoginTime'] = true;
        $mysqli->close();
      }
      
    ?> 
  <?php
     if (isset($_SESSION['isInstructor']) && $_SESSION['isInstructor']){  // James: check isset to avoid an error just after student joined and login
      echo '
      </p>
         <div>
          <a href="inst-coursemgmt.php" class="btn btn-info" role="button">Manage Courses</a> <p>Create/hide/delete a course or chapter, add/edit/remove questions, invite students, etc. </p>
          <a href="inst-taskgamemgmt.php" class="btn btn-info" role="button">Manage Tasks/Games</a> <p>Customize students experience</p>
          <a href="inst-showcourses.php" class="btn btn-info" role="button">View Courses</a> <p>View your courses, their chapters, and dates</p>
          <a href="inst-inviteinstructor.php" class="btn btn-info" role="button">Invite Instructor</a> <p>Send an email to an instructor so they can use Awesominds 2020</p>
          <a href="inst-classlist.php" class="btn btn-info" role="button">Manage Classlist</a> <p>Manage your class list</p>
          <a href="inst-stats.php" class="btn btn-info" role="button">Student Scores</a> <p>View scores, export scores, remove scores, and analytics</p>
          <a href="resetfromoptions.php" class="btn btn-info" role="button">Change Password</a> <p>Change your password </p>
        </div>
      </div>
    </body>'; 
      } else {
        echo '
        </p>
          <p><a class="btn btn-success" href="questiongame.php">Begin</a></p>
        </div>
      </body>';
      }
  ?> 
