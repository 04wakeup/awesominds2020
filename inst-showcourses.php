<!DOCTYPE html>
<html>
<head>
  <?php
    include('redir-notinstructor.php');
    include 'css/css.html';
  ?>
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.js"></script>
  <title>Show Courses - Awesominds 2020</title>
</head>
<body>
  <?php include 'inst-nav2.php' ?>
  <div class="container text-center">
    <h2>Show Courses</h2><br>
    <div class="card">
      <p id="selectCourseText">View your Courses Scheduled Dates</p>
      <div id='selectCourseDiv' class="container" style="max-width: 400px">
        <div class="input-group">
        </div>
      </div>
      <div id="selectedCourseOutput"></div>
    </div>
    <br>
  </div>

  <div class="modal fade" id="showCourseDates" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header text-center">
        <h4 class="modal-title text-center" id="myModalLabel4">Chapter</h4>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form action="db-showchapter.php" method="post" id="viewChapters"> 
        <div class="modal-body text-center" id='modalBody2'>
          <div class="form-group container" style="max-width: 400px;">
            <table id >
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary btn-ok" id="updateChapterBtn">Save Changes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
  
</div> 
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
    url: 'db-get-Instructor-Course.php',
    success: function(data){
        var courses = $.parseJSON(data)
        console.log(data);
        var htmlStr = '<table id="table" class="display table table-hover table-bordered text-left"><thead><tr><th>Course</th><th>Course Name</th><th>Hidden</th><th>Options</th></tr></thead><tbody>';
        for (var i = 0; i < courses.length; i++){
            console.log(courses[i].course_id);
            htmlStr += '<tr data-value="' + courses[i].course_id +'" id=row' + i + '"><td>' + courses[i].course_id + '</td><td>' + courses[i].course_name + '</td><td>' +  hidden[courses[i].hidden] + '</td><td><button id="' + i+1  + '" data-toggle="modal" data-target="#showCourseDates" data-value="' + courses[i].course_id + '"class="btn btn-info viewChapterBtn">View Chapters</button></td></tr></td>';
        }
        $("#selectedCourseOutput").html(htmlStr);
        
        //once the "view Chapters" button is pressed it will get the selected course and show all the chapters
        $(".viewChapterBtn").off('click');
        $('.viewChapterBtn').click(function(){
        selectedCourse = $(this).data('value');
        console.log(selectedCourse);
        $.ajax({
          type: 'GET',
          url: 'db-showonechapter.php', //used seperate api to get specific query
          data:{
            course: selectedCourse, //get the selected course
          },
          dataType: 'json',
          success: function(data){
              getChapters(selectedCourse)
          }
        });
      });
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
      console.log(chapters);
      console.log(course);
      $('#selectChapterDiv').show();
      var htmlStr = '<table id="table" class="display table table-hover table-bordered text-left"><thead><tr><th>Chapter#</th><th>Chapter Name</th><th>Start Date <br> (YYYY-MM-DD-HH:MM)</th><th>Due Date <br> (YYYY-MM-DD-HH:MM)</th><th>End Date <br>(YYYY-MM-DD-HH:MM)</th><th>Hidden</th><th>Options</th></tr></thead><tbody>';
      for (var i = 0; i < chapters.length; i++){ 
        htmlStr += '<tr id="row' + i + '"><td>' + chapters[i].chapter_id + '</td><td>' + chapters[i].chapter_name +'</td><td>' + chapters[i].start_date +'</td><td>' + chapters[i].due_date +'</td><td>' + chapters[i].end_date +'</td><td>' + hidden[chapters[i].hidden] + '<td><a href="inst-showdates.php" class="btn btn-info" id="'+ (i+1) + '">Edit Dates</a></button></td></tr></td>';
      } 
      $("#viewChapters").html(htmlStr);
      $(".showdatesBtn").off('click');
      $('.showdatesBtn').click(function(){
        selectedChapter = $(this).attr('id'); //the id in the specific showdates button is the same as the chapter numbers
        console.log(selectedChapter);
        $.ajax({
          type: 'GET',
          url: 'db-showonechapter.php', //used seperate api to get specific query
          data:{
            course_id_fk: course, //get the selected course
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
                    window.location.href = "inst-showdates.php?courseid=" + selectedCourse;
                  } else if(data.includes('Duplicate')){
                    $('#createChapterOutput').html('Error creating chapter - chapter number already exists!');
                  }
                }
              });
            });
          }
        });
      });
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
