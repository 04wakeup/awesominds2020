<!DOCTYPE html>
<html>

<!--
  Game plan: change inputs to numeric, copy "choose course then chapter" from manage courses
  when chapter is chosen get data from database and display it in the form. form on submit
  will take data and submit to proper tables. upon the round number being changed the 
  round table will add or subtract rows containing the rounds data or default data.

  make functions to get task data, get round data, and update number of rounds.
  the 2 latter ones could be one function.
-->

<head>
  <?php
  include('redir-notinstructor.php');
  include 'css/css.html';
  include 'inst-nav2.php';
  ?>
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.js"></script>
  <title>Task/Game Management - Awesominds 2020</title>
</head>

<body>
  <div id="selectCourseUI" class="container text-center">
    <h2>Manage Tasks/Games</h2><br>
    <div class="card">
      <div id='selectCourseDiv' class="container" style="max-width: 400px">
        <div class="input-group">
          <span class="input-group-addon">Course</span>
          <select class="form-control" id='courseDropdown'>
            <option value="null">Select an Existing Course</option>
          </select>
        </div>
      </div>
    </div>
  </div>
  <br>

  <div id="selectChapterUI" class="card container" style="max-width: 600px">
    <div class="input-group">
      <span class="input-group-addon">Chapter</span>
      <select class='form-control' id='chapterDropdown'>
        <option value="null">Select a Chapter/Game to Manage</option>
      </select>
    </div>
  </div><br>

  <!-- Game Challenges - More Information -->
  <div class="modal fade" id="TasksTypeInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Camosun College's <i>Awesominds 2020 For this one</i></h4>
        </div>
        <div class="modal-body text-center" style="font-size: 14px">

          <h5>Type of Tasks - More Information</h5>
          <table class="table table-sm text-left">
            <tr>
              <td><label style="font-weight: bold">Rate Questions:</label><br>Rate the difficulty of the questions.</td>
            </tr>
            <tr>
              <td><label style="font-weight: bold">Slide Cards:</label><br>Slide the question to reveal the answer.
              </td>
            </tr>
            <tr>
              <td><label style="font-weight: bold">Just Drills:</label><br>Just answer questions.</td>
            </tr>
            <tr>
              <td><label style="font-weight: bold">Game Show:</label><br>Each round is a different challenge. Can you beat your opponenets?</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <div id="manageTasksUI" class="container text-center card">
    <!-- TODO/NOTE: this form works, but should be updated to ajax -->
    <form style="font-size: 14px" action="db-submitTaskGameData.php" method="POST">

      <input type="hidden" id="courseID" name="course_id" value="0">
      <input type="hidden" id="chapterID" name="chapter_id" value="0">

      <div id="tasksMgmt">
        <div class="form-group">
          <div class="form-row" style="margin-top: 1.0em">
            <label for="taskMgmt" class="col-sm-3" style="font-weight: bold; font-size: 24px; margin-left: -2.6em">Tasks
            <a class="btn" href="" name="story" data-toggle="modal" data-target="#TasksTypeInfoModal">[?]</a>
            </label>
            
          </div>
          <div>
            <div class="form-row" style="margin-top: 0.5em">
              <label class="col-sm-2" style="font-weight: bold; text-align: left; margin-left: 3.0em">Rate Questions</label>
            </div>
            <div class="form-row">
              <div class="col-sm-1"></div>
              <div class="form-check">
                <label class="form-check-label mr-sm-4" for="form-check" style="margin-left: -1.5em">
                  Enabled:
                </label>
                <input id='rateQuestionsEnabled' name="rateQuestionsEnabled" class="form-check-input mr-sm-1" type="checkbox" value="0">
              </div>
              <label style="col-sm-2; margin-left: 2.0em">Point(s) per Question:</label>
              <input id='rateQuestionsPoints' name="rateQuestionsPoints" type="number" min="1" max="50" value="1" style="margin-left: 1em">
            </div>
          </div>

          <div>
            <div class="form-row" style="margin-top: 0.5em">
              <label class="col-sm-2" style="font-weight: bold; text-align: left; margin-left: 3.0em">Slide Cards</label>
            </div>
            <div class="form-row">
              <div class="col-sm-1"></div>
              <div class="form-check">
                <label class="form-check-label mr-sm-4" for="form-check" style="margin-left: -1.5em">
                  Enabled:
                </label>
                <input id='slideCardsEnabled' name="slideCardsEnabled" class="form-check-input mr-sm-1" type="checkbox" value="0">
              </div>
              <label style="col-sm-2; margin-left: 2.0em">Point(s) per Question:</label>
              <input id='slideCardsPoints' name="slideCardsPoints" type="number" min="1" max="50" value="2" style="margin-left: 1em">
            </div>
          </div>

          <div>
            <div class="form-row" style="margin-top: 0.5em">
              <label class="col-sm-2" style="font-weight: bold; text-align: left; margin-left: 3.0em">Just Drills</label>
            </div>
            <div class="form-row">
              <div class="col-sm-1"></div>
              <div class="form-check">
                <label class="form-check-label mr-sm-4" for="form-check" style="margin-left: -1.5em">
                  Enabled:
                </label>
                <input id='justDrillsEnabled' name="justDrillsEnabled" class="form-check-input mr-sm-1" type="checkbox" value="0">
              </div>
              <label style="col-sm-2; margin-left: 2.0em">Max Points per Question:</label>
              <input id='justDrillsPoints' name="justDrillsPoints" type="number" min="1" max="50" value="5" style="margin-left: 1em">
            </div>
          </div>

          <div>
            <div class="form-row" style="margin-top: 0.5em">
              <label class="col-sm-2" style="font-weight: bold; text-align: left; margin-left: 3.0em">Game Show</label>
            </div>
            <div class="form-row">
              <div class="col-sm-1"></div>
              <div class="form-check">
                <label class="form-check-label mr-sm-4" for="form-check" style="margin-left: -1.5em">
                  Enabled:
                </label>
                <input id='gameShowEnabled' name="gameShowEnabled" class="form-check-input mr-sm-1" type="checkbox" value="0">
              </div>
            </div>
          </div>
        </div>
      </div>

      <div id="gameAttributesMgmt">
        <div class="form-group">
          <div class="form-row" style="margin-top: 2.0em">
            <label for="gameAttributesMgmt" class="col-sm-3" style="font-weight: bold; font-size: 24px">Game Show Attributes</label>
          </div>
          <div class="form-row" style="margin-top: 1.0em">
            <div class="col-sm-1"></div>
            <label style="col-sm-2">Number of lives per game:</label>
            <input id='numOfLives' name="numOfLives" type="number" min="1" max="50" value="3" style="margin-left: 1em">
            <label class="col-sm-3">If you lose a round, you lose a life and have to repeat a
              round</label>
          </div>
          <div class="form-row" style="margin-top: 1.0em">
            <div class="col-sm-1"></div>
            <label style="col-sm-2">'In a row' bonus:</label>
            <input id='inARowBonus' name="inARowBonus" type="number" min="1" max="50" value="4" style="margin-left: 1em">
            <label class="col-sm-3">Correct answers 'In a row' earns:</label>
            <input id='inARowPoints' name="inARowPoints" type="number" min="1" max="50" value="10" style="margin-left: 1em">
            <label class="col-sm-1" style="margin-left: -20px">points</label>
          </div>
          <div class="form-row" style="margin-top: 1.0em">
            <div class="col-sm-1"></div>
            <label style="col-sm-2">Game theme:</label>
            <select id="gameTheme" name="gameTheme" class="custom-select mr-sm-4" style="margin-left: 1em">
              <option value="default" selected>
                default
              </option>
            </select>
          </div>
          <div class="form-row" style="margin-top: 1.0em">
            <div class="col-sm-1"></div>
            <label style="col-sm-2">Number of rounds/levels per game:</label>
            <input id='numOfRounds' name="numOfRounds" type="number" min="1" max="50" value="5" style="margin-left: 1em">
          </div>
        </div>
      </div>
      <div id="roundLevelMgmt">
        <div class="form-group">
          <div class="form-row" style="margin-top: 2.0em">
            <label for="taskSelection" class="col-sm-3" style="font-weight: bold; font-size: 24px">Rounds/Levels
            </label>
          </div>
          <table id="roundsTable" class="display table table-hover table-bordered">
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">Number of Questions</th>
                <th scope="col" title="Click for more information">Type of Challenge<a class="btn" href="" name="story" data-toggle="modal" data-target="#challengeTypeInfoModal">[?]</a></th>
                <th scope="col">Max Points Value per Question</th>
                <th scope="col">Goal</th>
                <th scope="col">Points to complete point goal</th>
              </tr>
            </thead>
            <tbody id=roundsTableBody></tbody>
          </table>
        </div>
        <input type="submit" value="Submit">
    </form>
  </div>
  </div>

  <br>

  <!-- Game Challenges - More Information -->
  <div class="modal fade" id="challengeTypeInfoModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
          <h4 class="modal-title text-center" id="myModalLabel">Camosun College's <i>Awesominds 2020</i></h4>
        </div>
        <div class="modal-body text-center" style="font-size: 14px">

          <h5>Type of Challenge - More Information</h5>
          <table class="table table-sm text-left">
            <tr>
              <td><label style="font-weight: bold">Keep Choosing:</label><br>Keep choosing until the right answer is selected. Fewer attempts =
                more points.</td>
            </tr>
            <tr>
              <td><label style="font-weight: bold">Choose 1, 2, or 3:</label><br>Choose up to 3 answers. Fewer selections = more points.
              </td>
            </tr>
            <tr>
              <td><label style="font-weight: bold">One Crack Time Bonus:</label><br>Race the clock and the opponents. Less time = more points.</td>
            </tr>
            <tr>
              <td><label style="font-weight: bold">Big Money:</label><br>Keep choosing until the right answer is selected. Game over on 4th attempt.
                Fewer selections = more points.</td>
            </tr>
            <tr>
              <td><label style="font-weight: bold">One Crack:</label><br>Choose 1 answer. Correct answer = earn points.</td>
            </tr>
          </table>
        </div>
      </div>
    </div>
  </div>

  <script>
    /* _______  _______  _______ _________ _______ _________
      (  ____ \(  ____ \(  ____ )\__   __/(  ____ )\__   __/
      | (    \/| (    \/| (    )|   ) (   | (    )|   ) (   
      | (_____ | |      | (____)|   | |   | (____)|   | |   
      (_____  )| |      |     __)   | |   |  _____)   | |   
            ) || |      | (\ (      | |   | (         | |   
      /\____) || (____/\| ) \ \_____) (___| )         | |   
      \_______)(_______/|/   \__/\_______/|/          )_(   
    */

    /* Variable descriptions. - Walker
    * challenges - contains the different type of challenges that can be applied to a round.
    * goals - contains the different goals to complete the round, either get enough points, beat the 
    * opponent(s), or complete the round.
    * defaultRoundTemplate - contains the data of what a default round looks like. 
    * e.g. when a round is added on top of the current rounds
    * roundData - contains all the round data the is currently in the database
    */

    var challenges = [];
    var goals = ["Points", "Beat opponent", "Complete round"];
    var defaultRoundTemplate = {
      questions: 10,
      max_point: 15,
      goal: "Points",
      point_goal: 100,
      challenge_fk: 1
    };
    var roundData = {};

    /*
    * NAME: getTaskData
    * MADE BY: Walker Jones
    * PARAMS: course_id - contains the id of the course.
    *       chapter_id - contains the id of the chapter.
    * PURPOSE: gathers data on tasks for selected chapter with db-getTaskData.php
    *   and insert the data into the input fields in the form.
    */
    var getTaskData = function(course_id, chapter_id) {
      $.ajax({
        type: 'POST',
        url: 'db-getTaskData.php',
        data: {
          course_id: course_id,
          chapter_id: chapter_id
        },
        success: function(data) {
          taskData = $.parseJSON(data);

          // Insert retrieved data into form. NOTE: if order in database is changed, values will be
          // inserted into wrong fields.
          $("#rateQuestionsEnabled").prop("checked", (taskData[0].enabled == 1) ? true : false);
          $("#rateQuestionsPoints").val(taskData[0].point_value);
          $("#slideCardsEnabled").prop("checked", (taskData[1].enabled == 1) ? true : false);
          $("#slideCardsPoints").val(taskData[1].point_value);
          $("#justDrillsEnabled").prop("checked", (taskData[2].enabled == 1) ? true : false);
          $("#justDrillsPoints").val(taskData[2].point_value);
          $("#gameShowEnabled").prop("checked", (taskData[3].enabled == 1) ? true : false);
        }
      });
    }

    /*
    * NAME: getGameShowData
    * MADE BY: Walker Jones
    * PARAMS: course_id - contains the id of the course.
    *       chapter_id - contains the id of the chapter.
    * PURPOSE: gathers data on game show settings for selected chapter with db-getGameShowData.php
    *   and insert the data into the input fields in the form.
    */
    var getGameShowData = function(course_id, chapter_id) {
      $.ajax({
        type: "POST",
        url: "db-getGameShowData.php",
        data: {
          course_id: course_id,
          chapter_id: chapter_id
        },
        success: function(data) {
          gameShowData = $.parseJSON(data);
          // insert retrieved data into form. NOTE: I can't figure out a way to make [0] not required
          $("#numOfLives").val(gameShowData[0].lives);
          $("#inARowBonus").val(gameShowData[0].in_a_row_number);
          $("#inARowPoints").val(gameShowData[0].in_a_row_point);
          $("#gameTheme").val(gameShowData[0].game_theme);
          $("#numOfRounds").val(gameShowData[0].num_of_rounds);
        }
      });
    }

    /*
    * NAME: getRoundData
    * MADE BY: Walker Jones
    * PARAMS: course_id - contains the id of the course.
    *       chapter_id - contains the id of the chapter.
    * PURPOSE: gathers data on rounds for selected chapter with db-getRoundData.php (not yet made)
    *    and insert the data into the input fields in the table in the form.
    */
    var getRoundData = function(course_id, chapter_id) {
      $.ajax({
        type: "POST",
        url: "db-getRoundData.php",
        data: {
          course_id: course_id,
          chapter_id: chapter_id
        },
        success: function(data) {
          console.log(data);
          roundData = $.parseJSON(data);
          updateRoundTable();
        }
      });
    }

    /*
    * NAME: updateRoundTable
    * MADE BY: Walker Jones
    * PARAMS: none
    * PURPOSE: checks the value of the #numOfRounds input and makes that many rows in the table, using
    * data from the database first, then the default template (defaultRoundTemplate) after running out.
    */
    var updateRoundTable = function() {
      $("#roundsTableBody").empty();
      var rounds = $("#numOfRounds").val();
      
      //If a new round is being added above the previous max rounds shown, add a default round to the array
      //roundData size will increase, but never decrease
      if (rounds > roundData.length) {
        //NOTE: JSON.parse(JSON.stringify()) is a simple way to get a copy of a json array instead
        //of a reference
        roundData.push(JSON.parse(JSON.stringify(defaultRoundTemplate)));
        roundData[roundData.length-1].round_id = roundData.length;
      }

      //delete the table and repopulate it with data from roundData and default rounds
      for (var i = 0; i < rounds; i++) {
        // there is data for this round in the database, display that instead of the default data
        var round_id = roundData[i].round_id;
        var questions = roundData[i].questions;
        var max_point = roundData[i].max_point;
        var goal = roundData[i].goal;
        var point_goal = roundData[i].point_goal;
        var challenge_fk = roundData[i].challenge_fk;

        var html = `
        <tr>
          <td>
            ${round_id}
          </td>
          <td>
            <input type="number" id="questions_${i}" name="questions_${i}" value="${questions}" min="1" max="50">
          </td>
          <td>
            <select id="challenge_id_${i}" name="challenge_id_${i}">
      `

        // For each different challenge found in the database, make it an option in the select dropdown
        challenges.forEach(function(item, index) {
          html += `<option value="${item.challenge_pk}"`;

          if (item.challenge_pk == challenge_fk) {
            // if the challenge pk matches the one belonging to the round data, select it.
            html += ` selected`;
          }

          html += `>${item.challenge_name}</option>`
        });

        html += `
            </select>
          </td>
          <td>
            <input type="number" id="max_point_${i}" name="max_point_${i}" value="${max_point}" min="1" max="500">
          </td>
          <td>
            <select id="goal_${i}" name="goal_${i}">
      `

        goals.forEach(function(item, index) {
          html += `<option value="${item}"`;

          if (goal === item) {
            // if the goal matches the one belonging to the round data, select it.
            html += ` selected`;
          }

          html += `>${item}</option>`
        });

        html += `
            </select>
          </td>
          <td>
            <input type="number" id="point_goal_${i}" name="point_goal_${i}" value="${point_goal}" min="1" max="1000>"
          </td>
        </tr>
      `;

        $("#roundsTableBody").append(html);
      }
    }

    /*
    * NAME: getChallenges
    * MADE BY: Walker Jones
    * PARAMS: none
    * PURPOSE: gets all the available challenges and puts them in variable "challenges to" be used in 
    * the round table challenge dropdown.
    */
    var getChallenges = function() {
      $.ajax({
        url: 'db-getChallenges.php',
        success: function(data) {
          challenges = $.parseJSON(data);
        }
      });
    }

    /*
    * NAME: getCourses
    * MADE BY: Previous team(s) and Walker Jones
    * PARAMS: none
    * PURPOSE: gets all the courses belonging to the logged in instructor and inserts them into 
    * select dropdown with id of #courseDropdown
    */
    var getCourses = function() {
      $.ajax({
        url: 'db-get-Instructor-Course.php',
        success: function(data) {
          $('#courseDropdown').empty();
          $('#courseDropdown').append('<option value="null">Select a Course</option>');
          courses = $.parseJSON(data);
          for (var i = 0; i < courses.length; i++) {
            $('#courseDropdown').append('<option value="' + courses[i].course_id + '">' + courses[i].course_id + ' - ' + courses[i].course_name + '</option>');
          }
        }
      });
    }

    /*
    * NAME: getChapters
    * MADE BY: previous team(s) and Walker Jones
    * PARAMS: course_id - contains the id of the course.
    * PURPOSE: gets the chapters belonging to the course and inserts them into select dropdown with id
    * #chapterDropdown
    */
    var getChapters = function(course_id) {
      $('#chapterDropdown').empty();
      $.ajax({
        type: "POST",
        url: 'db-getChapters.php',
        data: {
          course_id: course_id
        },
        dataType: "json",
        success: function(chapters) {
          // empty and append defualt value to the dropdown
          $('#chapterDropdown').empty();
          $('#chapterDropdown').append('<option value="null">Select a Chapter</option>');
          // append each chapter to the dropdown
          for (var i = 0; i < chapters.length; i++) {
            $('#chapterDropdown').append('<option value="' + chapters[i].chapter_id + '">' + chapters[i].chapter_id + ' - ' + chapters[i].chapter_name + '</option>');
          }
          $('#selectChapterUI').show();
        }
      });
    }

    /* 
    * NAME: #courseDropdown onChange
    * PURPOSE: save course_id and display selectChapterUI when a course is selected.
    */
    $("#courseDropdown").change(function() {
      selectedCourse = $('#courseDropdown').find(":selected").val();
      //because input types cannot have null, a string containing "null" is used instead
      if (selectedCourse != "null") {
        $('#selectChapterUI').show();

        $("#courseID").val(selectedCourse);
        getChapters(selectedCourse);
      } else {
        $('#selectChapterUI').hide();
      }
      $("#manageTasksUI").hide();
    });

    /* 
    * NAME: #chapterDropdown onChange
    * PURPOSE: save chapter_id and display manageTaskUI when a chapter is selected.
    */
    $("#chapterDropdown").change(function() {
      selectedChapter = $('#chapterDropdown').find(":selected").val();
      //because input types cannot have null, a string containing "null" is used instead
      if (selectedChapter != "null") {
        $("#manageTasksUI").show();
        $.ajax({
          type: 'POST',
          url: 'setchapter.php',
          data: {
            chapterid: selectedChapter
          },
          success: function(data) {
            $("#chapterID").val(selectedChapter);
            //NOTE: im actually unsure how selectedCourse isnt null here
            getTaskData(selectedCourse, selectedChapter);
            getGameShowData(selectedCourse, selectedChapter);
            getRoundData(selectedCourse, selectedChapter);
          }
        });
      } else {
        $("#manageTasksUI").hide();
      }
    });

    // drop down for number of rounds. if this ends up being simple enough (a call to one function)
    // put it in the html
    $('#numOfRounds').change(function() {
      //NOTE: there are better ways of doing this but there isn't the time currently - Walker
      //save the current values in the round fields so they dont get reset
      for (var i = 0; i < roundData.length; i++) {
        //if the questions field for this element exists, the other fields must exist too.
        //Take the values from those fields and update roundData
        if($("#questions_" + i).length != 0) {
          roundData[i].questions = $("#questions_" + i).val();
          roundData[i].max_point = $("#max_point_" + i).val();
          roundData[i].goal = $("#goal_" + i).val();
          roundData[i].point_goal = $("#point_goal_" + i).val();
          roundData[i].challenge_fk = $("#challenge_id_" + i).val();
        } else {
          i = roundData.length;
        }
      }
      updateRoundTable();
    });

    // "Main" function
    $(function() {
      $('#selectChapterUI').hide();
      $('#manageTasksUI').hide();
      getChallenges();
      getCourses();
    });
  </script>

</body>
</html>