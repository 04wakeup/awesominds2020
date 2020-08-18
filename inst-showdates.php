<!DOCTYPE html>
<html>
<head>
  <?php
    include('redir-notinstructor.php');
    include 'css/css.html';
  ?>
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.js"></script>
  <title>Edit Dates - Awesominds 2020</title>
</head>
<body>
  <?php include 'inst-nav2.php' ?>
  <div class="container text-center">
    <h2>Edit Dates</h2><br>
    <div class="card">
      <p id="selectCourseText">Select a course to view their start, end and due dates</p>
      <div id='selectCourseDiv' class="container" style="max-width: 400px">
        <div class="input-group">
          <span class="input-group-addon">Course</span>
          <select class="form-control" id='courseDropdown' method='GET'>
            <option value="null">Select an Existing Course</option>
          </select>
        </div>
      </div>
      <div id="selectedCourseOutput"></div>
    </div>
    <br>

  </div>
  
</div>
<div class="modal fade" id="showDates" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title text-center" id="myModalLabel4">Dates For This chapter</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form action="db-showchapter.php" method="post" id="editDates"> 
        <div class="modal-body text-center" id='modalBody2'>
          <!-- editing here-->
          <div class="form-group container" style="max-width: 400px;">
            <p>Start Date:</p>
            <input class="form-control" type="date-local" id="date_start_input" required><br>
            <p>Due Date:</p>
            <input class="form-control" type="date-local" id="date_due_input" required><br>
            <p>End Date:</p>
            <input class="form-control" type="date-local" id="date_end_input" required><br>
            <p id="createChapterOutput"></p>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-ok" id="updateChapterBtn">Save Changes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
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
              <textarea name="questionText" class="col-sm-12 form-control question" id="questionText" required rows="3"></textarea>
            </div>

            <p><small>Add up to 6 options and select the <i class="fa fa-check" aria-hidden="true"></i> next to the correct answer for this question.<br>
            Click the <i class="fa fa-trash" aria-hidden="true"></i> button next to an option to remove it.</small></p>
            <label class="col-form-label" for="optionText">Options</label>

            <div class="form-group input-group optionRow" id="optionRow0">
              <span class="input-group-btn"><button type="button" class="btn btn-danger deleteOptionBtn"><i class="fa fa-trash fa-lg" aria-hidden="true"></i></button></span>
              <span class="input-group-addon" id="optionLetter" value="A">A</span>
              <input name="optionLetterHidden" id="optionLetterHidden" type="hidden" value="A">

              <input name="optionText" type="text" class="form-control question" id="optionText">

              <span class="input-group-addon">
                <i class="fa fa-check fa-lg" aria-hidden="true"></i>
                <input type="radio" name="answer" id="answerRadio" value="A" checked>
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
          <button type="button" class="btn btn-primary btn-ok" data-dismiss="modal" id="saveQuestionBtn">Save Changes</button>
          <button type="button" class="btn btn-primary btn-ok" data-dismiss="modal" id="newQuestionBtn">Save Question</button>
          <button type="button" class="btn btn-warning" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>
  <div>
  
  </div>

<script>
var selectedCourse = "";
var courses = [];
var selectedChapter = 0;
var thingToDelete = "";
var thingToHide = "";
var questions = [];
var questionid = 0;
var table = null;
var start_date = "";
var end_date = "";
var chaptername = "";
var hidden = ["No", "Yes"]; //if "0" in database then it is not hidden if "1" in database then is hidden
function nextLetter(s){
  return s.replace(/([A-Z])[^A-Z]*$/, function(a){
    var c = a.charCodeAt(0);
    switch(c){
      case 90: return 'A';
      default: return String.fromCharCode(++c);
    }
  });
}

var optionLimit = 6
var numTotal = 1;


var getCourses = function(){
  $.ajax({
    //url: 'db-getcourses.php',
    url: 'db-get-Instructor-Course.php',
    success: function(data){
      $('#courseDropdown').empty();
      $('#courseDropdown').append('<option value="null">Select a Course</option>');
      $("#selectedCourseOutput").empty();
      courses = $.parseJSON(data);
      console.log(courses);
      for (var i = 0; i < courses.length; i++) {
        $('#courseDropdown').append('<option value="' + courses[i].course_id + '">' + courses[i].course_id + ' - ' + courses[i].course_name + '</option>');
      }
    }
  });
}



var getChapters = function(course){
  $('#selectedChapterText').empty();
  $('#output').empty();
  $('#output').hide();
  $.ajax({
    type: "POST",
    url: 'db-getChapters.php',
    data: { course_id: course },
    success: function(data){
      var chapters = $.parseJSON(data);
      //console.log(chapters);
      //console.log(course);
      $('#selectChapterDiv').show();
      var htmlStr = '<table id="table" class="display table table-hover table-bordered text-left"><thead><tr><th>Chapter#</th><th>Chapter Name</th><th>Start Date <br> (YYYY-MM-DD-HH:MM)</th><th>Due Date <br> (YYYY-MM-DD-HH:MM)</th><th>End Date <br>(YYYY-MM-DD-HH:MM)</th><th>Hidden</th><th>Options</th></tr></thead><tbody>';
      for (var i = 0; i < chapters.length; i++){ 
        htmlStr += '<tr id="row' + i + '"><td>' + chapters[i].chapter_id + '</td><td>' + chapters[i].chapter_name +'</td><td>' + chapters[i].start_date +'</td><td>' + chapters[i].due_date +'</td><td>' + chapters[i].end_date +'</td><td>' + hidden[chapters[i].hidden] + '</td><td><button id="' + chapters[i].chapter_id + '" data-toggle="modal" data-target="#showDates" class="btn btn-info showdatesBtn">Edit Dates</button></td></tr></td>';
      }
      $("#selectedCourseOutput").html(htmlStr);

      //Changes made as of May 27th 2020
      $(".showdatesBtn").off('click');
      $('.showdatesBtn').click(function(){
        selectedChapter = $(this).attr('id'); //the id in the specific showdates button is the same as the chapter numbers
        $.ajax({
          type: 'GET',
          url: 'db-showonechapter.php', //used seperate api to get specific query
          data:{
            course_id_fk: selectedCourse, //get the selected course
            chapter: selectedChapter, // gets the selected chapter
          },
          dataType: 'json',
          success: function(data){
            //alert(selectedCourse);
            //alert(selectedChapter);
            console.log(data);
            $('#date_start_input').val(data.start_date);
            $('#date_due_input').val(data.due_date); 
            $('#date_end_input').val(data.end_date);
            var updateDates = $('#editDates');
            updateDates.submit(function(e){
              e.preventDefault();
              //post data gets all the values in the form and sends the data to the database for updating
              var postData = {
                course_id_fk : selectedCourse,
                chapter_id : selectedChapter,
                //add due date when new database is in
                start_date: $('#date_start_input').val(),
                due_date: $('#date_due_input').val(),
                end_date: $('#date_end_input').val()
              };
              var url = updateDates.attr('action');
              if(document.activeElement.id == 'updateChapterBtn') url = 'db-showchapter.php';
              $.ajax({
                type: updateDates.attr('method'),
                url: url,
                data: postData,
                success: function(data) {
                  console.log(postData);
                  if(data.includes('successfully')){
                    window.location.href = "inst-showdates.php?course_id=" + selectedCourse;
                  } else if(data.includes('Duplicate')){
                    $('#createChapterOutput').html('Error creating chapter - chapter number already exists!');
                  }
                }
              });
            });
          }
        });
      });
      //end of changes made as of May27th 2020
    }
  });
}

$(function (){
  $('.selectChapterUI').hide();
  $('#selectChapterDiv').hide();
  // $('#selectChapterText').hide();
  $('#output').hide();

  $("#courseDropdown").change(function(){
    $('#output').empty();
    if($('#courseDropdown').find(":selected").val() != 'null'){
      $('.selectChapterUI').show();
      selectedCourse = $('#courseDropdown').find(":selected").val();
      $.ajax({
        type: 'POST',
        url: 'setcourse.php',
        data: { course: selectedCourse },
        success: function(data){
          getChapters(selectedCourse);
        }
      });
    } else {
      $('.selectChapterUI').hide();
      $('#selectedCourseOutput').empty();
      $('#selectChapterDiv').hide();
      // $('#selectChapterText').hide();
      $('#output').hide();
    }
    $('#selectedChapterOutput').hide();
  });

  getCourses();
});
</script>

</body>
</html>
