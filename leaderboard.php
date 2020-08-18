<!DOCTYPE html>
<html>
<head>
  <?php
    include('redir-notloggedin.php');
    include 'css/css.html';
  ?>
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.js"></script>
  <title>Leaderboards - Awesominds 2020</title>
</head>
<body>
  <?php include 'inst-nav2.php' ?>
  <div class="container text-center">
    <h2>Leaderboards</h2><br>
    <p>Select a course to view its leaderboard.<br> You may then select a chapter/game for its specific leaderboard.</p>
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
    </div>

    <div class='card selectChapterUI'>
      <p>Select a chapter/game:</p>
      <div id='selectChapterDiv' class="container" style="max-width: 400px">
        <div class="input-group">
          <span class="input-group-addon">Chapter</span>
          <select class='form-control' id='chapterDropdown'>
            <option value="null">Select a Chapter/Game</option>
          </select>
        </div>
        <div id="selectedChapterOutput"></div>
      </div>
    </div>
    <div id="output" class="card"></div>
  </div>

<script>
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

var getChapters = function(){ //loads list of chapters for the selected course from the database and populates the chapter dropdown
  $('#chapterDropdown').empty();
  $('#chapterDropdown').append('<option value="null">Select a Chapter</option>');
  $.ajax({
    url: 'db-getchapters-forstats.php',
    //url: 'db-getchapter-chaptertable.php',
    data: 'course_id=' + $('#courseDropdown').find(":selected").val(),
    success: function(data){
      $('#courseOutput').show();
      $('.selectChapterUI').show();
      var chapters = $.parseJSON(data);
      //console.log(chapters);
      for (var i = 0; i < chapters.length; i++) {
        $('#chapterDropdown').append('<option value="' + chapters[i].chapter_id_fk + '">' + chapters[i].chapter_id_fk + '</option>');
      }
    }
  });
}

$(function (){
  $('.selectChapterUI').hide();
  $('#output').hide();
  var modes = ['Keep Choosing', 'Choose 1, 2, 3', 'One Crack Time Bonus', 'Big Money', 'One Crack', 'Just Drills', 'Rate Questions', 'Slide Cards' ];

  $("#courseDropdown").change(function(){ //whenever a course is selected from the dropdown, this function fires
    $('#output').empty();
    $('#output').show();
    $.ajax({ //set the selected course in the php session
      type: 'POST',
      url: 'setcourse.php',
      data: { course: $('#courseDropdown').find(":selected").val() },
      success: function(data){
        getChapters();
        $('.selectChapterUI').show();
        $.ajax({ //get the scores for the selected course from the database and output them to a table
          url: 'db-getscores-course-forstudent.php',
          data: 'course_id_fk=' + $('#courseDropdown').find(":selected").val(),
          success: function(data){
            var str = "<h2>Scores for " + $('#courseDropdown').find(":selected").val() + '</h2><p>Click a column heading to sort by that attribute</p><table id="table" class="display"><thead><tr><th>Display Name</th><th>Chapter</th><th>Mode/Game Challenge</th><th>High Score</th><th>Total Points Earned</th><th>Times Played</th></tr></thead><tbody>';
            var scores = $.parseJSON(data);
            console.log(scores);
            for (var i = 0; i < scores.length; i++) {
              str += '<tr><td>' + scores[i].username+ '</td><td>' + scores[i].chapter_id + '</td><td>' + modes[scores[i].game_mode] + '</td><td>' + scores[i].high_score + '</td><td>' + scores[i].total_score + '</td><td>' + scores[i].times_played + '</td></tr>';
            }
            $('#output').html(str + '</tbody></table>');
            $('#table').DataTable({ paging: false, "order": [[0, 'asc']] }); //fancify the table with datatables.js, adding sorting and searching
          }
        });
      }
    });
  });

  $("#chapterDropdown").change(function(){ //whenever a chapter is selected from the dropdown, this function fires
    $('#output').empty();
    $('#output').show();
    $.ajax({ //get the scores for the selected chapter from the database and output them to a table
      url: 'db-getscores-chapter-forstudent.php',
      data: 'courseid=' + $('#courseDropdown').find(":selected").val() + '&chapter=' + $('#chapterDropdown').find(":selected").val(),
      success: function(data){
        var str = "<h2>Scores for " + $('#courseDropdown').find(":selected").val() + ", Chapter " + $('#chapterDropdown').find(":selected").val() + ' </h2><p>Click a column heading to sort by that attribute</p><table id="table" class="display"><thead><tr><th>Display Name</th><th>Mode/Game Challenge</th><th>High Score</th><th>Total Points Earned</th><th>Times Played</th></tr></thead><tbody>';
        var scores = $.parseJSON(data);
        for (var i = 0; i < scores.length; i++) {
          str += '<tr><td>' + scores[i].play_name + '</td><td>' + modes[scores[i].game_mode] + '</td><td>' + scores[i].high_score + '</td><td>' + scores[i].total_score + '</td><td>' + scores[i].times_played + '</td></tr>';
        }
        $('#output').html(str + '</tbody></table>');
        $('#table').DataTable({ paging: false, "order": [[0, 'asc']] }); //fancify the table with datatables.js, adding sorting and searching
      }
    });
  });

  getCourses();

});
</script>

</body>
</html>
