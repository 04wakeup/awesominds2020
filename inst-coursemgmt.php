<!DOCTYPE html>
<html>
<head>
  <?php
    include('redir-notinstructor.php');
    include 'css/css.html';
    include 'inst-nav2.php';
  ?>
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css">
  <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.min.js"></script>
  <title>Manage Courses - Awesominds 2020</title>
</head>
<body>
  <div class="container text-center">
    <h2>Manage Courses</h2><br>
    <div class="card">
      <!--<p id="selectCourseText">Select a course to manage, or create a new course.</p> -->
      <div id='selectCourseDiv' class="container" style="max-width: 400px">
        <p>
          <button class="btn btn-success" id='newCourseBtn' data-toggle="modal" data-target="#createCourseModal">Create New Course</button>
        </p>
        <p>
          Or select a course to manage.
        </p>
        <div class="input-group">
          <span class="input-group-addon">Course</span>
          <select class="form-control" id='courseDropdown'>
            <option value="null">Select an Existing Course</option>
          </select>
        </div>
      </div>
      <div id="selectedCourseOutput">
      <br>
      <p>
        <button id="inviteStudentsBtn" data-toggle="modal" data-target="#inviteStudentsModal" class="btn btn-primary">Invite Students</button>
        <button id="deleteCourseBtn" data-toggle="modal" data-target="#confirmDelete" class="btn btn-danger">Delete Course</button> 
        <button id="hideCourseBtn" data-toggle="modal" data-target="#confirmHide" class="btn btn-primary">Hide Course</button>
        <button id='editCourseBtn' data-toggle="modal" data-target="#editCourseModal" class="btn btn-info">Edit Course</button>
      </p>
      </div>
    </div>
    <br>

    <div id="selectChapterUI" class="card">
      <div id='selectChapterDiv' class="container" style="max-width: 600px">
        <p><button class="btn btn-success" id="newChapterBtn" data-toggle="modal" data-target="#createChapterModal"></button></p>
        <p> Or select a existing chapter/section to edit</p>
        <div class="input-group">
          <span class="input-group-addon">Chapter/Section</span>
          <select class='form-control' id='chapterDropdown'>
            <option value="null">Select a Chapter/Section to Manage</option>
          </select>
        </div>
        <div id="selectedChapterOutput">
          <br>
          <p>
            <button id="editChapterBtn" data-toggle="modal" data-target="#createChapterModal" class="btn btn-primary">Edit Chapter/Section</button> 
            <button id="deleteChapterBtn" data-toggle="modal" data-target="#confirmDelete" class="btn btn-danger">Delete Chapter/Section</button> 
            <button id="hideChapterBtn" data-toggle="modal" data-target="#confirmHide" class="btn btn-primary">Hide Chapter/Section</button>
          </p>
        </div>
      </div>
    </div><br>

    <div id="editQuestionsUI" class="card">
      <h3 id="questionsTitle"></h3> 
      <p>
        <button id="addQuestionBtn" class="btn btn-success" data-toggle="modal" data-target="#editModal">Add Question</button> 
        <button id="uploadQuestionsBtn" class="btn btn-success" data-toggle="modal" data-target="#uploadModal">Upload .doc File of Questions</button>
        <button id="deleteAllQuestionBtn" data-toggle="modal" data-target="#confirmDelete" class="btn btn-danger">Delete All Questions</button> 
      </p>
      <table id="table" class="display table table-hover table-bordered text-left">
        <thead>
          <tr>
            <th>ID#</th>
            <th>Question Text</th>
            <th>Comment</th>
            <th>Choices</th>
            <th>Answer</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody id="questionsTable"></tbody>
      </table>
    </div>
  </div>


  <!-- models -->

<!-- 
  this modal is for when you want to delete a single question
-->
  <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h4 class="modal-title text-center" id="myModalLabel2">Are you sure?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body text-center" id='deleteModal'>
          Are you sure you want to delete this question?
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" id="deleteBtn">Delete</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="confirmHide" tabindex="-1" role="dialog" aria-labelledby="myModalLabel3">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h4 class="modal-title text-center" id="myModalLabel3">Are you sure?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body text-center" id='hideModal'>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" id="hideBtn">Hide</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

  <!--
  This block contains the first section seen on the 'course managment' screen with the course drop down and
  create new course button.
  -->
  <div class="modal fade" id="createCourseModal" tabindex="-1" role="dialog" aria-labelledby="createCourseModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h4 class="modal-title text-center" id="createCourseModalLabel">Create Course</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <form id="courseForm">
          <div class="modal-body text-center" id='createCourseModalBody'>

              <p>Enter a course code and name to create a new course.</p>
              <div class="form-group container" style="max-width: 400px;">
                <p>Course Code</p>
                <input class="form-control" type="text" name="courseID" id="courseIDinput" required placeholder="e.g. 'PSYC150'" pattern="[A-Za-z]{3,4}[0-9]{3}" title="3-4 letters followed by 3 numbers; no spaces."><br>
                <p>Course Name</p>
                <input class="form-control" type="text" name="courseName" id="courseNameinput" required placeholder="e.g. 'Development Psychology'"><br>
              </div>

          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-ok" id="createCourseBtn">Create Course</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="createChapterModal" tabindex="-1" role="dialog" aria-labelledby="createChapterModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h4 class="modal-title text-center" id="createChapterModalLabel">Create Chapter/Section</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form id="chapterForm">
          <div class="modal-body text-center" id='createChapterModalBody'>
            <p id="createChapterModalDesc">Enter a chapter/section number, chapter/section name, and availability dates to create a new chapter/section.</p>
            <div class="form-group container" style="max-width: 400px;"> <!-- TODO: give these better ids, change preamble to textarea -->
              <p>Chapter/Section Number</p>
              <input id="chapterIDinput" class="form-control" type="number" name="chapter_id" required value="1" min="1" max="999"><br>
              <p>Chapter/Section Name</p>
              <input id="chapterNameinput" class="form-control" type="text" name="chapter_name" required placeholder="eg. Intro to Topic"><br>
              <p>Preamble</p>
              <input id="chapterPreambleinput" class="form-control" type="text" name="preamble" placeholder="In this chapter, we will be studying..."><br>
              <p>Available from:</p>
              <p> This is when the students will first be able to access the chapter/section </p>
              <input id="date_start_input" class="form-control" type="date-local" required><br>
              <p>Score submission date:</p>
              <p> This is when students scores are emailed to the instructor </p>
              <input id="date_due_input" class="form-control" type="date-local" required><br>
              <p>End Date:</p>
              <p> This is when the students will no longer be able to access the chapter/section </p>
              <input id="date_end_input" class="form-control" type="date-local" required><br>
              <p id="createChapterOutput"></p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-ok" id="createChapterBtn">Create Chapter/Section</button>
            <button type="submit" class="btn btn-primary btn-ok" id="updateChapterBtn">Save Changes/Section</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="uploadModal" tabindex="-1" role="dialog" aria-labelledby="uploadModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h4 class="modal-title text-center" id="uploadModalLabel">Upload Questions</h4>
          

          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <form action="upload2.php" method="post" enctype="multipart/form-data" id="uploadForm">
        <div class="modal-body text-center" id='uploadModalBody'>

          <p>Upload a .doc file of questions to add them to this chapter/section. File must adhere to formatting rules:</p>
          <small><ol class="text-left">
            <li>Plain text only; no columns or tables</li>
            <li>The first line of the file must be a blank line</li>
            <li>Each question must be numbered, followed by a period or parenthesis (eg "1. How many..." or "2) Which...")</li>
            <li>Each question must be followed by 2 or more lettered choices, each signified by a letter followed by a period or parenthesis (eg. "A. True" or "B) False")</li>
            <li>Each question must have the letter of its correct answer listed after its choices, signified by "Ans:" or "Answer:" (eg "Answer: C")</li>
            <li>Or the answers must appear at the end of the file with the heading "Answer Key" and a numbered list with the answers</li>
            <li>File must be saved in .doc format (not .docx or any other filetype)</li>
          <ol></small><br>
          <input type="file" class="form-control-file" name="fileToUpload" id="fileToUpload"><br>
          <p id="uploadOutput" style="padding-left: 3rem"></p>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-ok" id="uploadSubmitBtn">Upload File</button> 
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div> 
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="inviteStudentsModal" tabindex="-1" role="dialog" aria-labelledby="inviteStudentsModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h4 class="modal-title text-center" id="inviteStudentsModalLabel">Invite Students</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body text-center" id='inviteStudentsModalBody'>
          <p>Copy the following link and paste it in a D2L post, or anywhere else that only students in your course will see it.</p>
          <textarea readonly id="studentReglink" class="form-control"></textarea>
          <p>Students can click the link to add this course to their Awesominds 2020 account, adding it to their in-game course list.</p>
          <p>Other instructors can click the link to add this course to their Awesominds 2020 account, giving them access to course management features for this course.</p>
          <p>1. Have the students create an account.</p>
          <p>2. Copy and paste the url into your browser.</p>
          <p>3. Enjoy and have fun with Awesominds</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-primary btn-ok" data-dismiss="modal">OK</button>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editCourseModal" tabindex="-1" role="dialog" aria-labelledby="editCourseModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h4 class="modal-title text-center" id="editCourseModalLabel">Create Course</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>

        <form id="editCourseForm">
          <div class="modal-body text-center" id='editCourseModalBody'>

              <p>Enter a course code and name to create a new course.</p>
              <div class="form-group container" style="max-width: 400px;">
                <p>Course Code</p>
                <input class="form-control" type="text" name="courseID" id="editCourseIDinput" readonly><br>
                <p>Course Name</p>
                <input class="form-control" type="text" name="courseName" id="editCourseNameinput" required placeholder="e.g. 'Development Psychology'"><br>
              </div>

          </div>
          <div class="modal-footer">
            <button type="submit" class="btn btn-primary btn-ok">Confirm Edit</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="editModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h4 class="modal-title text-center" id="editModalLabel">Editing Question</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body container text-center" id='editModalBody'>
          <form id='editQuestionForm'>
            
            <label class="col-form-label" for="questionText">Question Text</label>
            <div class="form-group row" id="questionRow">
              <textarea id="questionText" name="questionText" class="col-sm-12 form-control question" required maxlength="100" rows="3" placeholder= "Type question here"></textarea>
            </div>
            
            <label class="col-form-label" for="questionCommentText">Question Comment</label>
            <div class="form-group row" id="questionRow">
              <textarea id="questionCommentText" name="questionCommentText" class="col-sm-12 form-control question" maxlength="100" rows="3"></textarea>
            </div>

            <label class="col-form-label" for="slideCard">Should this question be shown in slide cards?(currently not functional)</label>
            <input id="slideCard" name="slideCard" type="checkbox">

            <p><small>Add up to 6 options and select the <i class="fa fa-check" aria-hidden="true"></i> next to the correct answer for this question.<br>
            Click the <i class="fa fa-trash" aria-hidden="true"></i> button next to an option to remove it.</small></p>
            <label class="col-form-label" for="optionText">Options</label>

            <div id="optionRow0" class="form-group input-group optionRow">
              <span class="input-group-btn">
                <button id="deleteOptionBtn0" type="button" class="deleteOptionBtn btn btn-danger">
                  <i class="fa fa-trash fa-lg" aria-hidden="true"></i>
                </button>
              </span>

              <span id="optionLetter0" class="input-group-addon" value="A">A</span>
              <input id="optionLetterHidden0" name="optionLetterHidden" type="hidden" value="A">

              <input id="optionText0" name="optionText" type="text" class="form-control question">

              <span class="input-group-addon">
                <i class="fa fa-check fa-lg" aria-hidden="true"></i>
                <input id="answerRadio0" type="radio" name="answer" value="A" checked>
              </span>
            </div>

            <div class="form-group" id="addOption">
              <button id="addOptionBtn" type="button" class="btn btn-success">+ Add Option</button>
              <div id="limitMessage"></div>
            </div>
            
          </form>
          <div id="editOutput"></div>
        </div>
        <div class="modal-footer">
          <button id="updateQuestionBtn" type="button" class="btn btn-primary btn-ok" data-dismiss="modal">Save Changes</button>
          <button id="createQuestionBtn" type="button" class="btn btn-primary btn-ok" data-dismiss="modal">Save Question</button>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
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
    var selectedCourse = ""; // rename to course_id
    var selectedChapter = 0; // rename to chapter_id
    var question_id = 0;
    var courses = [];
    var chapters = [];
    var questions = [];
    var thingToDelete = "";
    var thingToHide = "";
    var table = null;
    var optionLimit = 6
    var numOfOptions = 1;

    //configuration
    var max_file_size           = 1048576 * 3; //allowed file size. (1 MB = 1048576)
    var result_output           = '#uploadOutput'; //ID of an element for response output
    var total_files_allowed     = 1; //Number files allowed to upload

    /* _______           _        _______ __________________ _______  _        _______ 
      (  ____ \|\     /|( (    /|(  ____ \\__   __/\__   __/(  ___  )( (    /|(  ____ \
      | (    \/| )   ( ||  \  ( || (    \/   ) (      ) (   | (   ) ||  \  ( || (    \/
      | (__    | |   | ||   \ | || |         | |      | |   | |   | ||   \ | || (_____ 
      |  __)   | |   | || (\ \) || |         | |      | |   | |   | || (\ \) |(_____  )
      | (      | |   | || | \   || |         | |      | |   | |   | || | \   |      ) |
      | )      | (___) || )  \  || (____/\   | |   ___) (___| (___) || )  \  |/\____) |
      |/       (_______)|/    )_)(_______/   )_(   \_______/(_______)|/    )_)\_______)
    */

    function nextLetter(s){
      return s.replace(/([A-Z])[^A-Z]*$/, function(a){
        var c = a.charCodeAt(0);
        switch(c){
          case 90: return 'A';
          default: return String.fromCharCode(++c);
        }
      });
    }

    /*
    * NAME: setup
    * MADE BY: Walker Jones
    * PARAMS: None
    * PURPOSE: controller function, gets page ready for initial viewing. Hides content not ready to be 
    * viewed and retrieves courses
    */

    function setup() {
      $('#selectChapterUI').hide();
      $('#selectChapterDiv').hide();
      $('#editQuestionsUI').hide();
      $("#selectedCourseOutput").hide();

      getCourses();
    }

    /*
    * NAME: setLetters
    * MADE BY: Walker Jones
    * PARAMS: none
    * PURPOSE: ensures the answers have the correct letters from A-F when creating/editing a question
    */

    function setLetters() {
      //console.log("setLetters");
      for (var i = 0; i < optionLimit; i++) {
        var option = $("#optionRow" + i);
        //console.log(option);
        //console.log(String.fromCharCode(i+65));
        if (option.length == 0) {
          i = optionLimit;
          break;
        }

        option.find('#optionLetter' + i).val(String.fromCharCode(i + 65)); //NOTE: cant set value of spans in jquery (or anywhere i believe)
        option.find('#optionLetter' + i).html(String.fromCharCode(i + 65));
        option.find('#optionLetterHidden' + i).val(String.fromCharCode(i + 65));
        option.find('#answerRadio' + i).val(String.fromCharCode(i + 65));
      }
    }

    function addOption(){
      numOfOptions = $('div[id^="optionRow"]').length;
      if(numOfOptions < optionLimit){
        var oldID = numOfOptions - 1;
        var newID = numOfOptions;

        numOfOptions++;

        var $div = $('div[id^="optionRow"]:last');
        var newRow = $div.clone().prop('id', 'optionRow' + newID );
        var letter = newRow.find('#optionLetterHidden' + oldID).val().toUpperCase();

        newRow.find('#deleteOptionBtn' + oldID).attr("id", "deleteOptionBtn" + newID)
        newRow.find('#optionLetter' + oldID).attr("id", "optionLetter" + newID)
        newRow.find('#optionLetterHidden' + oldID).attr("id", "optionLetterHidden" + newID)
        newRow.find('#answerRadio' + oldID).attr("id", "answerRadio" + newID)
        newRow.find('#optionText' + oldID).attr("id", "optionText" + newID)

        newRow.find('#optionLetter' + newID).html(nextLetter(letter));
        newRow.find('#optionLetterHidden' + newID).val(nextLetter(letter));
        newRow.find('#answerRadio' + newID).val(nextLetter(letter));
        newRow.find('#optionText' + newID).val('');

        $("#addOption").before(newRow);
        if($('div[id^="optionRow"]').length > 1) $('.deleteOptionBtn').prop("disabled", false);
        //$(".deleteOptionBtn").on('click');
        if(numOfOptions >= optionLimit){
          $("#addOptionBtn").prop("disabled", true);
          $("#limitMessage").html('<p><small>Limit ' + optionLimit + ' options per question</small></p>');
        }
      }
      setLetters();
    }

    /*
    * NAME: getCourses
    * MADE BY: Previous team(s) and Walker Jones
    * PARAMS: none
    * PURPOSE: gets all the courses belonging to the logged in instructor and inserts them into 
    * select dropdown with id of #courseDropdown
    */
    function getCourses(){
      console.log("call to getCourses");
      $.ajax({
        url: 'db-get-Instructor-Course.php',
        dataType: "json",
        success: function(data){
          courses = data;
          $('#courseDropdown').empty();
          $('#courseDropdown').append('<option value="null">Select a Course</option>');
          //console.log("courses: " + data);
          console.log(courses);
          for (var i = 0; i < courses.length; i++) {
            $('#courseDropdown').append('<option value="' + courses[i].course_id + '">' + courses[i].course_id + ' - ' + courses[i].course_name + '</option>');
          }
        }
      });
    }

    /* NOTE: this is what i believe this function does - Walker
    * PARAMS: course - The id of a course to get the chapters for (e.g. Exam111)
    * PURPOSE: Retrieves the chapter
    */
    
    function getChapters(){
      $('#chapterDropdown').empty();
      $('#selectedChapterText').empty();
      $('#editQuestionsUI').hide();
      $.ajax({
        type: "POST",
        url: 'db-getChapters.php',
        data: { course_id: selectedCourse },
        dataType: "json",
        success: function(data){
          chapters = data;
          console.log(chapters);
          //var chapters = $.parseJSON(data);
          $('#chapterDropdown').empty();
          $('#chapterDropdown').append('<option value="null">Select a Chapter</option>');
          for (var i = 0; i < chapters.length; i++) {
            $('#chapterDropdown').append('<option value="' + chapters[i].chapter_id + '">' + chapters[i].chapter_id + ' - ' + chapters[i].chapter_name + '</option>');
          }
          $('#selectChapterDiv').show();
          $('#newChapterBtn').html('Create New Chapter/Section in Course "' + selectedCourse + '"');
        }
      });
    }

    /*
    * NAME: getQuestions
    * MADE BY: Previous team(s) with edits by Walker Jones
    * PARAMS: none
    * PURPOSE: gets all the questions for the current course and chapter 
    * and puts them in the question table
    * NOTE: The reason 2 for loops are used is because trying to mix them causes the code that is not 
    * dependant on the Ajax's completion call will get executed before the code dependant on the Ajax's
    * completion. resulting in a messed up table, in short.
    */
    function getQuestions(){
      $("#questionsTable").empty();
      $.ajax({
        type: 'POST',
        url: 'db-getQuestions.php',
        data: { course_id: selectedCourse, chapter_id: selectedChapter },
        dataType: 'json',
        success: function(questionData){
          if (questionData.length > 0) {
            console.log("question data: ", questionData);
            
            // Go through the data once to create the row structure 
            // and insert the data for the question id, question, and comment.
            for (var i = 0; i < questionData.length; i++) {
              console.log("I: ", i);
              var question_id = questionData[i].question_id;
              var question = questionData[i].question;
              var comment = questionData[i].comment;
              $("#questionsTable").append(`
                <tr id="row${question_id}">
                  <td>${question_id}</td>
                  <td>${question}</td>
                  <td>${comment}</td>
                  <td id="choices${question_id}"></td>
                  <td id="answer${question_id}"></td>
                  <td id="options${question_id}"></td>
                </tr>
              `);
            }

            // Go through data a second time to get the answer data for the questions and
            // append them onto the correct <td>
            for (var i = 0; i < questionData.length; i++) {
              var question_id = questionData[i].question_id;
              $.ajax({
                type: 'POST',
                url: 'db-getAnswers.php',
                data: { course_id: selectedCourse, chapter_id: selectedChapter, question_id: question_id },
                dataType: 'json',
                success: function(answerData) {
                  var trueQuestion_id = answerData[0].question_id_fk;

                  for (var i = 0; i < answerData.length; i++) {
                    var answer_id = answerData[i].answer_id;
                    var answer = answerData[i].answer;
                    var correct = answerData[i].correct;

                    // 65 is ASCII for A. Add 64 since answer_id starts at 1 and not 0.
                    var answerLetter = String.fromCharCode(Number(answer_id)+64);
                    $("#choices"+trueQuestion_id).append(`${answerLetter}:${answer}<br>`);

                    // If the current question is the correct one, append it to the answer cell
                    if (correct == '1') {
                      $("#answer"+trueQuestion_id).append(answerLetter);
                    }
                  }
                  
                  $("#options"+trueQuestion_id).append(`
                    <button value="${trueQuestion_id}" class="editQuestionBtn btn btn-primary" data-toggle="modal" data-target="#editModal">Edit</button>
                    <br>
                    <button value="${trueQuestion_id}" class="deleteQuestionBtn btn btn-danger" data-toggle="modal" data-target="#confirmDelete">Delete</button>
                  `);
                }
              })
            }
          }
          // I believe this was to make the table more interactive, being able to show a number of rows at once
          // and having different pages.
          // table = null
          // table = $('#table').DataTable({ paging: false, "order": [[0, 'asc']] });
        }
      });
    }

    /* _________ _______           _______  _______          
       \__    _/(  ___  )|\     /|(  ____ \(  ____ )|\     /|
          )  (  | (   ) || )   ( || (    \/| (    )|( \   / )
          |  |  | |   | || |   | || (__    | (____)| \ (_) / 
          |  |  | |   | || |   | ||  __)   |     __)  \   /  
          |  |  | | /\| || |   | || (      | (\ (      ) (   
       |\_)  )  | (_\ \ || (___) || (____/\| ) \ \__   | |   
       (____/   (____\/_)(_______)(_______/|/   \__/   \_/   
    */

    // BUTTON FUNCTIONALITY

    // Course Buttons

    $('#deleteCourseBtn').click(function(){
      $('#deleteModal').html('Are you sure you want to delete the course "' + selectedCourse + '"?');
      thingToDelete = 'course';
    });

    $('#hideCourseBtn').click(function(){ 
      if ($("#hideCourseBtn").html() == "Hide Course") {
        $('#hideModal').html('Are you sure you want to hide the course "' + selectedCourse + '"?');
        $('#hideBtn').html('Hide');
      } else {
        $('#hideModal').html('Are you sure you want to unhide the course "' + selectedCourse + '"?');
        $('#hideBtn').html('Unhide');
      }
      thingToHide = 'course';
    });

    // Get regcode of selected course on invite button click.
    $('#inviteStudentsBtn').click(function(){
      $('#inviteStudentsModalLabel').html('Invite Students to ' + selectedCourse);
      $.ajax({ 
        type: 'POST',
        url: 'db-getRegcode.php',
        data: { course_id: selectedCourse },
        dataType: 'json',
        success: function(data){
          var url = window.location.href.substring(0, window.location.href.indexOf("inst-coursemgmt.php"));
          $('#studentReglink').val(url + '?regcode=' + data.regcode);
        }
      });
    });

    // Chapter Buttons

    $("#deleteChapterBtn").click(function(){
      $('#deleteModal').html('Are you sure you want to delete Chapter/Section ' + selectedChapter + ' from ' + selectedCourse + '?');
      thingToDelete = 'chapter';
    });

    $('#hideChapterBtn').click(function(){
      if ($("#hideChapterBtn").html() == "Hide Chapter/Section") {
        $('#hideModal').html('Are you sure you want to hide Chapter/Section ' + selectedChapter + ' from ' +  selectedCourse + '?');
        $('#hideBtn').html('Hide');
      } else {
        $('#hideModal').html('Are you sure you want to unhide Chapter/Section ' + selectedChapter + ' from ' +  selectedCourse + '?');
        $('#hideBtn').html('Unhide');
      }
      
      thingToHide = 'chapter';
    });

    /*
    * PURPOSE: Opens the create chapter form when the "create new chapter" button is pressed,
    * And configure the data and labels to default values.
    */
    $('#newChapterBtn').click(function(){
      $('#createChapterBtn').show();
      $('#updateChapterBtn').hide();
      $('#chapterForm').trigger('reset');
      $('#chapterIDinput').prop('readonly', false);
      $('#createChapterModalLabel').html('Create Chapter/Section in Course "' + selectedCourse + '"');
      $('#createChapterModalDesc').html('Enter a chapter/section number, chapter/section name, and availability dates to create a new chapter/section.');
      $('#date_start_input').val(moment().format("YYYY-MM-DD"));
      $('#date_end_input').val(moment().add(14, 'days').format("YYYY-MM-DD"));
      $('#date_due_input').val(moment().add(7, 'days').format("YYYY-MM-DD"));
    });

    // Question buttons

    // When add button is clicked, show question modal. show only create button in modal, set values
    // to empty
    $("#addQuestionBtn").click(function(){
      $("#updateQuestionBtn").hide();
      $("#createQuestionBtn").show();
      $("#editModalLabel").html("Add Question");
      $("#questionText").val("");
      $("#questionCommentText").val("");
      $("#optionText0").val("");
      $("input[name='answer']:last").prop("checked", true);
      $(".deleteOptionBtn").prop("disabled", true);
      $("#addOptionBtn").prop("disabled", false);
      $("#limitMessage").empty();
    });

    // when delete question button is clicked, save id of question and set thingToDelete to question
    // use document.body to apply this affect to dynamically added buttons
    $(document.body).on( "click", ".deleteQuestionBtn", function(){
      question_id = $(this).val();
      thingToDelete = 'question';
      $('#deleteModal').html('Are you sure you want to delete question #' + question_id + '?');
      console.log("qi", question_id)
    });

  // when "delete all questions" button is clicked
  // it will pass the current selected course and chapter to
  // delete all the question in that chapter
  $(document.body).on( "click", "#deleteAllQuestionBtn", function(){
    console.log(selectedCourse);
    console.log(selectedChapter);
    thingToDelete = 'allQuestions';
    $('#deleteModal').html('Are you sure you want to delete question all the questions in this chapter?');
  });

    // When a questions edit button is clicked, pull up question form and fill in the fields
    // with the data of the question
    // use document.body to apply this affect to dynamically added buttons
    $(document.body).on( "click", ".editQuestionBtn", function(){
      $('#updateQuestionBtn').show();
      $('#createQuestionBtn').hide();
      question_id = $(this).val();
      console.log(question_id);
      $('#editModalLabel').html('Edit Question #' + question_id);
      $.ajax({
        type: "POST",
        url: "db-getOneQuestion.php",
        data: { 
          course_id: selectedCourse,
          chapter_id: selectedChapter,
          question_id: question_id 
        },
        dataType: "json",
        success: function(questionData){
          console.log(questionData);
          console.log(questionData.question);
          console.log(questionData.comment);
          $("#questionText").val(questionData.question);
          $("#questionCommentText").val(questionData.comment);
          
          $.ajax({
            type: "POST",
            url: "db-getAnswers.php",
            data: {
              course_id: selectedCourse,
              chapter_id: selectedChapter,
              question_id: question_id
            },
            dataType: "json",
            success: function(answerData) {
              for (var i = 0; i < answerData.length; i++) {
                if(i < answerData.length-1) addOption();
                $("#optionText" + i).val(answerData[i].answer);
                if(answerData[i].correct == "1") {
                  $("#answerRadio" + i).prop("checked", true);
                }
              }
            }
          });
        }
      });
    });

    $('#createQuestionBtn').click(function(){
      var question = $("#questionText").val();
      var comment = $("#questionCommentText").val();
      var correctAnswer = $("input[name ='answer']:checked").val();
      var answers = [];
      for (var i = 0; i < numOfOptions; i++) {
        answers.push($("#optionText" + i).val());
      }
      $.ajax({
        type: 'POST',
        url: 'db-createQuestion.php',
        data: { 
          course_id: selectedCourse,
          chapter_id: selectedChapter, 
          question: question, 
          comment: comment,
          answers: answers, 
          correctAnswer: correctAnswer
        },
        success: function(data) {
          console.log("new question data: ", data);
          getQuestions();
          $('div[id^="optionRow"]').not(':first').remove();
          numOfOptions = 1;
        }
      });
    });

    $('#updateQuestionBtn').click(function(){
      var question = $("#questionText").val();
      var comment = $("#questionCommentText").val();
      var correctAnswer = $("input[name ='answer']:checked").val();
      var answers = [];
      //console.log(question_id);
      //console.log(correctAnswer);
      for (var i = 0; i < numOfOptions; i++) {
        answers.push($("#optionText" + i).val());
        console.log($("#optionText" + i).val());
      }

      console.log(answers);
      $.ajax({
        type: 'POST',
        url: 'db-updateQuestion.php',
        data: { 
          course_id: selectedCourse,
          chapter_id: selectedChapter, 
          question_id: question_id,
          question: question, 
          comment: comment,
          answers: answers, 
          correctAnswer: correctAnswer
        },
        success: function(data) {
          getQuestions();
          $('div[id^="optionRow"]').not(':first').remove();
          numOfOptions = 1;
        }
      });
    });

    $("#addOptionBtn").click(function(){
      addOption();
    });

    // use .on instead of .click
    // .click will only apply it to buttons already existing
    $(document.body).on( "click", ".deleteOptionBtn", function(){
      if (numOfOptions > 1) {
        numOfOptions--;
        var id = this.id.slice(-1);
        $("#optionRow" + id).remove();
        for (var i = parseInt(id)+1; i < optionLimit; i++){
          var option = $("#optionRow" + i);
          console.log(option);
          var x = i - 1;
          if (option.length == 0){
            i = optionLimit;
            break;
          }

          option.attr("id", "optionRow" + x);
          option.find("#deleteOptionBtn" + i).attr("id", "deleteOptionBtn" + x)
          option.find("#optionLetter" + i).attr("id", "optionLetter" + x)
          option.find("#optionLetterHidden" + i).attr("id", "optionLetterHidden" + x)
          option.find("#answerRadio" + i).attr("id", "answerRadio" + x)
          option.find("#optionText" + i).attr("id", "optionText" + x)

          option.find("#answerRadio").prop("checked", true);
        }
      }

      setLetters();
    });

    // Chapter Buttons

    $('#editChapterBtn').click(function(){
      $('#createChapterBtn').hide();
      $('#updateChapterBtn').show();
      $('#chapterIDinput').prop('readonly', true);
      $('#createChapterModalLabel').html('Edit Chapter/Section ' + selectedCourse + ' - ' + selectedChapter);
      $('#createChapterModalDesc').html('You may edit the name and dates of the selected chapter here');
      $.ajax({
        type: 'POST',
        url: 'db-getOneChapter.php',
        data: { course_id: selectedCourse, chapter_id: selectedChapter },
        dataType: 'json',
        success: function(data){
          console.log(data);
          $('#chapterIDinput').val(data.chapter_id);
          $('#chapterNameinput').val(data.chapter_name);
          $("#chapterPreambleinput").val(data.preamble);
          $('#date_start_input').val(data.start_date);
          $('#date_end_input').val(data.end_date);
          $('#date_due_input').val(data.due_date);
        }
      });
    });

    $('#studentReglink').click(function(){ 
      this.select(); 
    });

    // The delete button of the delete modal.
    $('#deleteBtn').click(function(){
      switch (thingToDelete) {
        case 'course':
          $.ajax({
            type: 'POST',
            url: 'db-deleteCourse.php',
            data: { course_id: selectedCourse },
            success: function(data){
              getCourses();
              $('#selectChapterDiv').hide();
              $('#selectChapterUI').hide();
              selectedCourse = "";
            }
          });
          setup();
          break;
        case 'chapter':
          $.ajax({
            type: 'POST',
            url: 'db-deleteChapter.php',
            data: { course_id: selectedCourse, chapter_id: selectedChapter },
            success: function(data){
              $('#selectedChapterOutput').hide();
              selectedChapter = 0;
            }
          });
          getChapters();
          //setup();
          break;
        case 'question':
          $.ajax({
            type: 'POST',
            url: 'db-deleteQuestion.php',
            data: { course_id: selectedCourse, chapter_id: selectedChapter, question_id : question_id },
            success: function(data){
              question_id = 0;
            }
          });
          getQuestions();
          //setup();
          break;
          case 'allQuestions':
        $.ajax({
          type: 'POST',
          url: 'db-deleteAllQuestions.php',
          data: {course_id: selectedCourse, chapter_id: selectedChapter},
          success: function(data){
            getQuestions();
          }
        });
        default:
          break;
      }
    });

    // The hide button of the hide modal
    $('#hideBtn').click(function(){
      console.log("hide button clicked with " + thingToHide);
      switch (thingToHide) {
        case 'course':
          $.ajax({
            type: 'POST',
            url: 'db-hideCourse.php',
            data: { course_id: selectedCourse},
            success: function(data){
              alert("Successfully hid course: ", data);
              console.log("Successfully hid course")
            }
          });
          getCourses();
          $('#selectChapterUI').hide();
          $('#selectedCourseOutput').hide();
          $('#selectChapterDiv').hide();
          $('#editQuestionsUI').hide();
          //setup();
          break;
        case 'chapter':
          $.ajax({
            type: 'POST',
            url: 'db-hideChapter.php',
            data: { course_id: selectedCourse, chapter_id: selectedChapter },
            success: function(data){
              alert("Successfully hid chapter: ", data);
            }
          });
          getChapters();
          $("#selectedChapterOutput").hide();
          $('#editQuestionsUI').hide();
          //setup();
          break;
        default:
          break;
      }
    });

    $('#uploadQuestionsBtn').click(function(){
      $('#uploadModalLabel').html('Upload Questions to "' + selectedCourse + ' - '+ selectedChapter + '"'); 
      $('#uploadOutput').empty(); // James  : clear the output area on upload file
      
    });

    // FORM FUNCTIONALITY

    $('#courseForm').submit(function (e) {
      console.log("course", selectedCourse);
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: "db-createCourse.php",
        data: $('#courseForm').serialize(),
        success: function(data) { //TODO: get rid of get call to self and add confirmation
          $('#createCourseModal').modal('hide');
          getCourses();
       }
      });
    });

    $('#editCourseBtn').click(function (e){
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: "db-getCourseInfo.php",
        data: {course_id: selectedCourse},
        success: function(data){
          console.log(data);
          var course = $.parseJSON(data);
          console.log(course.course_id);
          $('#editCourseIDinput').val(course.course_id);
          $('#editCourseNameinput').val(course.course_name);
        }
      });
    });

    $('#editCourseForm').submit(function(e){
      e.preventDefault();
      $.ajax({
        type: "POST",
        url: "db-editCourse.php",
        data: $('#editCourseForm').serialize(),
        success: function(data){
          $('#editCourseModal').modal('hide');
          getCourses();
        }
      })
    });

    $('#chapterForm').submit(function (e) {
      e.preventDefault();
      var url = "db-createChapter.php";
      if(document.activeElement.id == 'updateChapterBtn') url = 'db-updateChapter.php';
      console.log(url);
      var data = {
          course_id: selectedCourse,
          chapter_id: $("#chapterIDinput").val(),
          chapter_name: $("#chapterNameinput").val(),
          preamble: $("#chapterPreambleinput").val(),
          start_date: $("#date_start_input").val(),
          end_date: $("#date_end_input").val(),
          due_date: $("#date_due_input").val()
        };
      console.log("data ", data);
      $.ajax({
        type: "POST",
        url: url,
        data: data,
        success: function(data) {
          console.log(data);
          //TODO: instead of reloading entire page, only reload chapter select
          //this also applied to many other places
          if(data){ 
            $('#createChapterModal').modal('hide');
            getChapters();
          } else {
            $('#createChapterOutput').html('Error creating chapter/section - chapter/section number already exists!');
          }
        }
      });
    });

    // On form submit for uploading docs of questions
    $('#uploadForm').submit(function(e) {
      e.preventDefault();
      var proceed = true; //set proceed flag
      var error = []; //errors
      var total_files_size = 0;

      if(!window.File && window.FileReader && window.FileList && window.Blob){ //if browser doesn't supports File API
        error.push("Your browser does not support new File API! Please upgrade."); //push error text
      }else{
        var total_selected_files = this.elements['fileToUpload'].files.length; //number of files

        //limit number of files allowed
        if(total_selected_files > total_files_allowed){
          error.push( "You have selected "+total_selected_files+" file(s), " + total_files_allowed +" is maximum!"); //push error text
          proceed = false; //set proceed flag to false
        }
        //iterate files in file input field
        $(this.elements['fileToUpload'].files).each(function(i, ifile){
          if(ifile.value !== ""){ //continue only if file(s) are selected
            total_files_size = total_files_size + ifile.size; //add file size to total size
          }
        });

        //if total file size is greater than max file size
        if(total_files_size > max_file_size){
          error.push( "You have "+total_selected_files+" file(s) with total size "+total_files_size+", Allowed size is " + max_file_size +", Try smaller file!"); //push error text
          proceed = false; //set proceed flag to false
        }

        var submit_btn  = $('#uploadSubmitBtn'); //form submit button

        //if everything looks good, proceed with jQuery Ajax
        if(proceed){
          submit_btn.val("Please Wait...").prop( "disabled", true); //disable submit button
          var form_data = new FormData(this); //Creates new FormData object
          var post_url = $(this).attr("action"); //get action URL of form
      
          //jQuery Ajax to Post form data
          $.ajax({
            url: post_url,
            type: "POST",
            data: form_data,
            contentType: false,
            cache: false,
            processData: false,
            mimeType: "multipart/form-data"
          }).done(function(res){ 
            $('#uploadForm')[0].reset(); //reset form
            // $(result_output).html(res); //output response from server
            $(result_output).html(res + '<br><br><button type="button" class="btn btn-success btn-sm" data-dismiss="modal">View Question</button>'); //output response from server
            submit_btn.val("Upload file").prop( "disabled", false); //enable submit button once ajax is done
          });
        }
      }

      $(result_output).empty(); //reset output

    });

    $('#editModal').on('hide.bs.modal', function () {
      $('div[id^="optionRow"]').not(':first').remove();
      numOfOptions = 1;
    })

    $('#uploadModal').on('hide.bs.modal', function () {
      getQuestions();
    })

    // DROPDOWN CHANGES

    $("#courseDropdown").change(function(){
      selectedCourse = $('#courseDropdown').find(":selected").val();
      console.log(courses);
      $('#selectedChapterOutput').hide();

      if(selectedCourse != 'null'){
        $('#selectChapterUI').show();
        $('#selectedCourseOutput').show();
        $("#hideCourseBtn").html("Hide Course");
        
        $.ajax({  // James: needs this for setting SESSION to use upload question
          type: 'POST',
          url: 'setcourse.php',
          data: { course: selectedCourse },
          success: function(data){
            getChapters();
          }
        });

        // Check if the selected course is hidden change the text on the hide button 
        // and hide modal appropiately. NOTE: there might be a better way to do this
        for (var i = 0; i < courses.length; i++) {
          if (courses[i].course_id == selectedCourse && courses[i].hidden == "1") {
            $("#hideCourseBtn").html("Unhide Course");
            break;
          }
        }

        // getChapters();
      } else {
        $('#selectChapterUI').hide();
        $('#selectedCourseOutput').hide();
        $('#selectChapterDiv').hide();
        $('#editQuestionsUI').hide();
      }
    });

    $("#chapterDropdown").change(function(){
      selectedChapter = $('#chapterDropdown').find(":selected").val();
      if(selectedChapter != 'null'){
        $("#editQuestionsUI").show();
        $("#questionsTitle").html(selectedCourse + ' - Chapter/Section ' + selectedChapter + ' Questions');
        $("#hideChapterBtn").html("Hide Chapter/Section");

        for (var i = 0; i < chapters.length; i++) {
          if (chapters[i].chapter_id == selectedChapter && chapters[i].hidden == "1") {
            $("#hideChapterBtn").html("Unhide Chapter/Section");
            break;
          }
        }

        $.ajax({   // James: needs it for SESSION to use at uploading
        type: 'POST',
        url: 'setchapter.php',
        data: { chapterid: selectedChapter },
        success: function(data){
          getQuestions();
        }
      });

        $('#selectedChapterOutput').show();
        // getQuestions();
      } else {
        $("#selectedChapterOutput").hide();
        $('#editQuestionsUI').hide();
      }
    });

    // Main function
    $(function (){
      setup();
    });
  </script>

</body>
</html>
