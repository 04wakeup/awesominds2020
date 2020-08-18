<!DOCTYPE html>
<html>
<head>
  <?php
    include('redir-notinstructor.php');
    include 'css/css.html';
  ?>
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.15/css/jquery.dataTables.css">
  <script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.15/js/jquery.dataTables.js"></script>
  <title>Student Scores - Awesominds 2020</title>
</head>
<body>
  <?php include 'inst-nav2.php' ?>
  <div class="container text-center">
    <h2>Student Scores</h2><br>
    <p>Select a course to see student results for the entire course <br> Select a chapter/section to see the results for just that chapter/section</p>
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
          <button id="deleteCoursePointsBtn" data-toggle="modal" data-target="#confirmDelete" class="btn btn-danger">Remove Points For Course</button> 
          <button id="removeStudentsBtn" data-toggle="modal" data-target="#confirmDelete" class="btn btn-danger">Remove Students</button> 
         <p>
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
          <!--Are you sure you want to delete this question? -->
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
      case 'coursePoints':

        $.ajax({ //set the selected course in the php session
          type: 'POST',
          url: 'setcourse.php',
          data: { course: $('#courseDropdown').find(":selected").val() },
          success: function(data){

            $.ajax({ //get the scores for the selected course from the database and output them to a table
              url: 'db-getscores-allusers-course.php',
              data: 'course_id=' + $('#courseDropdown').find(":selected").val(),
              success: function(data){
                var scores = $.parseJSON(data);
                for (var i = 0; i < scores.length; i++) {
              // this deletes all the entries for the course
              // it had to be done this way because it was the only way to get the 
              // c_number which was needed to delete info from the table
                  $.ajax({
                    type: 'POST',
                    url: 'db-deletePointsForCourse.php',
                    data: { course_id: selectedCourse, c_number: scores[i].c_number},
                    success: function(data){
                      location.reload();
                    }
                  });
                }
              }
            });
          }
       });
      break;
      
      case 'chapterPoints':
        $.ajax({ //get the scores for the selected chapter from the database and output them to a table
          url: 'db-getscores-allusers-chapter.php',
          data: 'courseid=' + $('#courseDropdown').find(":selected").val() + '&chapter=' + $('#chapterDropdown').find(":selected").val(),
          success: function(data){
            var scores = $.parseJSON(data);
            selectedChapter = $('#chapterDropdown').find(":selected").val();

            for (var i = 0; i < scores.length; i++) {
              // this deletes all the entries for the chapter
              // it had to be done this way because it was the only way to get the 
              // c_number which was needed to delete info from the table
              $.ajax({
                type: 'POST',
                url: 'db-deletePointsForChapter.php',
                data: { course_id: selectedCourse, chapter_id: selectedChapter , c_number: scores[i].c_number},
                success: function(data){
                  location.reload()
                }
              });
            }
          }
        });
      break;
      case 'students':
        $.ajax({
            type: 'POST',
            url: 'db-deleteStudentFromCourse.php',
            data: { course_id: selectedCourse},
            success: function(data){
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
// when "Remove Points for Course" button is clicked
// it will pass the current selected course to the function
// and it will delete all the score info for 
// that given course
  $(document.body).on( "click", "#deleteCoursePointsBtn", function(){
    console.log(selectedCourse);
    thingToDelete = "coursePoints";
    $('#deleteModal').html('Are you sure you want to remove all students points for ' + selectedCourse + '?');
  });

// when "Remove Points for Chapter" button is clicked
// it will pass the current selected course and selected chapter to the function
// and it will delete all the score info for
// that given course and chapter
$(document.body).on( "click", "#deleteChapterPointsBtn", function(){
    console.log(selectedCourse);
    console.log(selectedChapter);
    thingToDelete = "chapterPoints";
    $('#deleteModal').html('Are you sure you want to remove all students points for ' + selectedCourse + ' Chapter '+ selectedChapter + '?');
  });

  $(document.body).on( "click", "#removeStudentsBtn", function(){
    console.log(selectedCourse);
    console.log(selectedChapter);
    thingToDelete = "students";
    $('#deleteModal').html('Are you sure you want to remove all students from ' + selectedCourse + '?');
  });


// Create empty csvContent variable (to be generated and exported)
//let csvContent = "data:text/csv;charset=utf-8,";  // Use this version of csvContent if using downloadCSVRandom()
let csvContent = '';  // Use this version of csvContent if using exportToCSV() [Preferred]

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
        //console.log(data);
        $('.selectChapterUI').show();
        var sc = $.parseJSON(data);
        selectedCourse = sc.course; // gets the course in the dropdown menu
        console.log(selectedCourse);
        getChapters();
        $('.selectChapterUI').show();
        $.ajax({ //get the scores for the selected course from the database and output them to a table
          url: 'db-getscores-allusers-course.php',
          data: 'course_id=' + $('#courseDropdown').find(":selected").val(),
          success: function(data){
            var str = "<h2>Scores for " + $('#courseDropdown').find(":selected").val() + '</h2><p>Click a column heading to sort by that attribute</p><p style="text-align: center">Export this data as a: <button onclick="exportToCSV()">CSV File</button>&nbsp;<button onclick="exportTableToExcel()">Excel File</button></p><table id="table" class="display"><thead><tr><th>C Number</th><th>Display Name</th><th>Chapter</th><th>Mode/Game Challenge</th><th>High Score</th><th>Total Points Earned</th><th>Times Played</th></tr></thead><tbody>';
            var scores = $.parseJSON(data);
            console.log("scores: " , scores);

            // Map scores object into an array of arrays, each of which will become a row of data when exported
            var rows = scores.map(function (obj) {
              //console.log(obj);
              return ["C Number", obj.c_number, "Display Name", obj.username, "Chapter", obj.chapter_id_fk, "Mode/Game Challenge", obj.task_fk, "High Score", obj.high_score, "Total Points Earned", obj.total_score, "Times Played", obj.attempts];
            });
            //console.log("rows: " , rows);

            // Add each row of data to the csvContent variable (used for CSV only - Excel file is generated in it's own, separate function)
            rows.forEach(function(rowArray) {
                let row = rowArray.join(",");
                csvContent += row + "\r\n";
            });

            for (var i = 0; i < scores.length; i++) {
              str += '<tr><td>' + scores[i].c_number + '</td><td>' + scores[i].username + '</td><td>' + scores[i].chapter_id_fk + '</td><td>' + modes[scores[i].task_fk] + '</td><td>' + scores[i].high_score + '</td><td>' + scores[i].total_score + '</td><td>' + scores[i].attempts + '</td></tr>';
            }
            $('#output').html(str + '</tbody></table>');
            $('#table').DataTable({ paging: false, "order": [[1, 'asc']] }); //fancify the table with datatables.js, adding sorting and searching
          }
        });
      }
    });
  });

  $("#chapterDropdown").change(function(){ //whenever a chapter is selected from the dropdown, this function fires
    $('#output').empty();
    $('#output').show();
    $.ajax({ //get the scores for the selected chapter from the database and output them to a table
      url: 'db-getscores-allusers-chapter.php',
      data: 'courseid=' + $('#courseDropdown').find(":selected").val() + '&chapter=' + $('#chapterDropdown').find(":selected").val(),
      success: function(data){
        var str = "<h2>Scores for " + $('#courseDropdown').find(":selected").val() + ", Chapter " + $('#chapterDropdown').find(":selected").val() + ' </h2><p>Click a column heading to sort by that attribute</p><p style="text-align: center">Export this data as a: <button onclick="exportToCSV()">CSV File</button>&nbsp;<button onclick="exportTableToExcel()">Excel File</button> <br> <br> <button id="deleteChapterPointsBtn" data-toggle="modal" data-target="#confirmDelete" class="btn btn-danger">Remove Points For Chapter</button> </p><table id="table" class="display"><thead><tr><th>C Number</th><th>Display Name</th><th>Mode/Game Challenge</th><th>High Score</th><th>Total Points Earned</th><th>Times Played</th></tr></thead><tbody>';
        var scores = $.parseJSON(data);
        selectedChapter = $('#chapterDropdown').find(":selected").val();
        //console.log(selectedChapter);
        console.log("scores: " , scores);

        // Map scores object into individual arrays, which will become rows of data when exported
        var rows = scores.map(function (obj) {
          return ["C Number", obj.c_number, "Display Name", obj.username, "Mode/Game Challenge", obj.task_fk, "High Score", obj.high_score, "Total Points Earned", obj.total_score, "Times Played", obj.attempts];
        });
        //console.log("rows: " , rows);

        csvContent = '';

        rows.forEach(function(rowArray) {
            let row = rowArray.join(",");
            csvContent += row + "\r\n";
        });

        for (var i = 0; i < scores.length; i++) {
          str += '<tr><td>' + scores[i].c_number + '</td><td>' + scores[i].username + '</td><td>' + modes[scores[i].task_fk] + '</td><td>' + scores[i].high_score + '</td><td>' + scores[i].total_score + '</td><td>' + scores[i].attempts + '</td></tr>';
        }
        str += "Question Analytics coming soon. \n Student Analytics coming soon."
        $('#output').html(str + '</tbody></table>');
        $('#table').DataTable({ paging: false, "order": [[1, 'asc']] }); //fancify the table with datatables.js, adding sorting and searching
      }
    });
  });

  getCourses();

});

// // Download csvContent as a .csv file with a random name (output has same content as exportToCSV()'s output) and create download prompt
// function downloadCSVRandom() {
//   var encodedUri = encodeURI(csvContent);
//   window.open(encodedUri);
// }

// Export csvContent as a .csv file and create download prompt
function exportToCSV() {
    var csvFile;
    var downloadLink;

    // CSV file
    csvFile = new Blob([csvContent], {type: "text/csv"});

    // Download link
    downloadLink = document.createElement("a");

    // File name
    downloadLink.download = 'awesominds2020_studentprogress.csv';

    // Create a link to the file
    downloadLink.href = window.URL.createObjectURL(csvFile);

    // Hide download link
    downloadLink.style.display = "none";

    // Add the link to DOM
    document.body.appendChild(downloadLink);

    // Click download link
    downloadLink.click();
}

// Export entire table (including titles, buttons, and search) to Microsoft Excel (.xls) format and create download prompt
function exportTableToExcel(){
    var downloadLink;
    var dataType = 'application/vnd.ms-excel';
    var tableSelect = document.getElementById('output');
    var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
    
    // Specify file name
    filename = 'awesominds2020_studentprogress.xls';
    
    // Create download link element
    downloadLink = document.createElement("a");
    
    document.body.appendChild(downloadLink);
    
    if(navigator.msSaveOrOpenBlob){
        var blob = new Blob(['\ufeff', tableHTML], {
            type: dataType
        });
        navigator.msSaveOrOpenBlob( blob, filename);
    }else{
        // Create a link to the file
        downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
    
        // Set file name
        downloadLink.download = filename;
        
        //triggering the function
        downloadLink.click();
    }
}

</script>

</body>
</html>