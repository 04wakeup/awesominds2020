<?php
  /*
  * PURPOSE: Submits All information in inst-taskgamemgmt.php into database
  * TODO: change to update if exists
  * TODO: make this work regardless of order of tasks
  */

	require("includes/db.php");
  include('redir-notinstructor.php');

  if ($_SERVER["REQUEST_METHOD"] == "POST"){
    // general data
    $course_id = $_POST["course_id"];
    $chapter_id = $_POST["chapter_id"];

    // game show (chapter) data
    $lives = $_POST["numOfLives"];
    $in_a_row_number = $_POST["inARowBonus"];
    $in_a_row_point = $_POST["inARowPoints"];
    $game_theme = null; // replace with post[gameTheme] when implemented
    $num_of_rounds = $_POST["numOfRounds"];

    // chapter_task data
    $RQEnabled = (isset($_POST["rateQuestionsEnabled"]) ? 1 : 0); // 1
    $RQPoints = $_POST["rateQuestionsPoints"];
    $SCEnabled = (isset($_POST["slideCardsEnabled"]) ? 1 : 0); // 2
    $SCPoints = $_POST["slideCardsPoints"];
    $JDEnabled = (isset($_POST["justDrillsEnabled"]) ? 1 : 0); // 3
    $JDPoints = $_POST["justDrillsPoints"];
    $GSEnabled = (isset($_POST["gameShowEnabled"]) ? 1 : 0); // 4

    // Delete existing rounds to prepare for inserting new rounds
    $query = "DELETE FROM round
      WHERE course_id_fk = '$course_id'
      AND chapter_id_fk = '$chapter_id'";

    $result = $mysqli->query($query);

    // insert a row into rounds for each row submitted
    for ($i = 0; $i < $num_of_rounds; $i++) {
      // round data
      $round_id = $i + 1;
      $questions = $_POST["questions_$i"];
      $max_point = $_POST["max_point_$i"];
      $goal = $_POST["goal_$i"];
      $point_goal = $_POST["point_goal_$i"];
      $challenge_id = $_POST["challenge_id_$i"];

      $query = "INSERT INTO round 
        VALUES ( '$course_id', $chapter_id, $round_id, $questions, $max_point, '$goal', $point_goal, $challenge_id )";

      $result = $mysqli->query($query);
    }
    
    // Update game show data within chapter
    $query = "UPDATE chapter
      SET lives = $lives, in_a_row_number = $in_a_row_number, 
          in_a_row_point = $in_a_row_point, game_theme = '$game_theme', num_of_rounds = $num_of_rounds
      WHERE course_id_fk = '$course_id' AND chapter_id = $chapter_id";
    
    $result = $mysqli->query($query);

    // update each tasks settings for this chapter
    $query = "UPDATE chapter_task
      SET enabled = $RQEnabled, point_value = $RQPoints
      WHERE course_id_fk = '$course_id' AND chapter_id_fk = $chapter_id AND task_fk = 1";

    $result = $mysqli->query($query);

    $query = "UPDATE chapter_task
      SET enabled = $SCEnabled, point_value = $SCPoints
      WHERE course_id_fk = '$course_id' AND chapter_id_fk = $chapter_id AND task_fk = 2";

    $result = $mysqli->query($query);

    $query = "UPDATE chapter_task
      SET enabled = $JDEnabled, point_value = $JDPoints
      WHERE course_id_fk = '$course_id' AND chapter_id_fk = $chapter_id AND task_fk = 3";

    $result = $mysqli->query($query);

    $query = "UPDATE chapter_task
      SET enabled = $GSEnabled
      WHERE course_id_fk = '$course_id' AND chapter_id_fk = $chapter_id AND task_fk = 4";

    $result = $mysqli->query($query);

  } else {
    echo json_encode("error");
  }
  $mysqli->close();
  header("location: inst-taskgamemgmt.php");
?>