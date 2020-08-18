<!DOCTYPE html>
<html>
<head>
  <?php
    include('redir-notinstructor.php');
    include 'css/css.html';
  ?>
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.js"></script>
  <title>View Student Progress - Awesominds 2020</title>
</head>
<body>
  <?php include 'inst-nav2.php' ?>
  <div class="container text-center">
    <h2>Manage Classlist</h2><br>
    <p>Select a course to view its classlist.</p>
    <div class="card">
      <p>Select a course:</p>
      <div id='selectCourseDiv' class="container" style="max-width: 400px">
        <div class="input-group">
          <span class="input-group-addon">Course</span>
          <select class="form-control" id='courseDropdown'>
            <option value="null">Select a Course</option>
          </select>
        </div>
      </div>
      <div id="courseOutput">
        <br>
         <p>
          <button id="removeStudentsBtn" data-toggle="modal" data-target="#confirmDelete" class="btn btn-danger">Remove Students</button> 
         <p>
      </div>
    </div>


    <div id="output" class="card"></div>

  <!--
    Modal
    -->

  <div class="modal fade" id="confirmDelete" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header text-center">
          <h4 class="modal-title text-center" id="myModalLabel2">Are you sure?</h4>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        </div>
        <div class="modal-body text-center" id='deleteModal'>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-danger btn-ok" data-dismiss="modal" id="deleteBtn">Yes</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
      </div>
    </div>
  </div>

<script>
var selectedCourse = "";
var selectedChapter = "";
var thingToDelete = "";

$('#deleteBtn').click(function(){
    switch (thingToDelete) {
      case 'students':
        $.ajax({
            type: 'POST',
            url: 'db-deleteStudentFromCourse.php',
            data: { course_id: selectedCourse},
            success: function(data){
              console.log(selectedCourse)
              location.reload()
            }
          });
      default:
        break;
    }
  });

var getCourses = function(){ //loads list of courses from the database and populates the course dropdown
$('#courseOutput').hide();
  $.ajax({
    url: 'db-getcourses.php',
    success: function(data){
      $('#courseDropdown').empty();
      $('#courseDropdown').append('<option value="null">Select a Course</option>');
      var courses = $.parseJSON(data);
      console.log(courses);
      for (var i = 0; i < courses.length; i++) {
        $('#courseDropdown').append('<option value="' + courses[i].course_id + '">' + courses[i].course_id + ' - ' + courses[i].course_name + '</option>');
      }
    }
  });
}

  $(document.body).on( "click", "#removeStudentsBtn", function(){
    console.log(selectedCourse);
    thingToDelete = "students";
    $('#deleteModal').html('Are you sure you want to remove all students from ' + selectedCourse + '?');
  });


$(function (){
  $('.selectChapterUI').hide();
  $('#output').hide();
  // Set the modes based on the game mode ID (see game_mode in score table / id value in menu-games.js and menu-mode.js)
  var modes = ['Keep Choosing', 'Choose 1, 2, 3', 'One Crack Time Bonus', 'Big Money', 'One Crack', 'Just Drills', 'Rate Questions', 'Slide Cards' ];

  $("#courseDropdown").change(function(){ //whenever a course is selected from the dropdown, this function fires
    $('#output').empty();
    $('#output').show();
    $.ajax({ //set the selected course in the php session
      type: 'POST',
      url: 'setcourse.php',
      data: { course: $('#courseDropdown').find(":selected").val() },
      success: function(data){
        console.log(data);
        $('.selectChapterUI').show();
        var sc = $.parseJSON(data);
        selectedCourse = sc.course; // gets the course in the dropdown menu
        console.log(selectedCourse);
        $('#courseOutput').show();
        $('.selectChapterUI').show();
        $.ajax({ 
            type: 'POST',
            url: 'db-getStudents.php',
            //url: 'db-getcourses.php',
            data: {course: selectedCourse },
            success: function(data){
              //console.log(data);
              var sd = $.parseJSON(data);
              console.log(sd);
              var str = "<h2>Scores for " + $('#courseDropdown').find(":selected").val() + '</h2><table id="table" class="display"><thead><tr><th>C Number</th><th>Course ID</th></tr></thead><tbody>';
              for (var i = 0; i < sd.length; i++){
                str += '<tr><td>' + sd[i].c_number_fk + '</td><td>' + sd[i].course_id_fk + '</td>';
              }
              
            $('#output').html(str + '</tbody></table>');
            $('#table').DataTable({ paging: false, "order": [[1, 'asc']] }); //fancify the table with datatables.js, adding sorting and searching
          }
        });
      }
    });
  });

  getCourses();

});



</script>

</body>
</html>
