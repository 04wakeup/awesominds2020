<!DOCTYPE html>
<html>
<head>
  <?php
    session_start();
    if(!$_SESSION['logged_in'] || !$_SESSION['isInstructor']){ 
      header("location: index.php");
    }
    include 'css/css.html';
  ?>
</head>
<body>
  <?php include 'inst-nav2.php' ?>
  <div class="container text-center">
    <h2>Invite Instructor</h2>
    <p>To invite another instructor to create their own courses/games, enter their email address here.<br>They will be sent a link that will let them create their own Awesominds 2020 instructor account.</p>
    <form method="post" autocomplete="off" id="inviteForm">
      <div class="form-group container" style="max-width: 400px;">
        <label for="emailInput" class="form-label"><b>Email Address*</b></label>
        <div class="input-group">
          <input class="form-control" type="email" required autocomplete="off" name="email" id="emailInput"/>
          <span class="input-group-btn"><button class="btn btn-primary" name="invite" id="inviteBtn">Send Invite</button></span>  
          
        </div>    
        
      </div>  
    </form>
    <span style="margin-left: 5px; padding:1px;"><button class="btn btn-success btn-sm btn-ok" name="manualInvite" id="manualInviteBtn">View Code</button></span>

    <p id="output"></p>
    
  </div>

<script>
  $('#inviteForm').submit(function (e) {
    var submit_btn  = $('#inviteBtn'); 
    submit_btn.prop( "disabled", true); 
    e.preventDefault();
    $.ajax({
      type: 'POST',
      url: 'db-inviteinstructor.php',
      data: { email: $('#emailInput').val() 
            , task: 'send'},
      success: function(data) {   
        if(!(~data.indexOf("Fail"))){
          $('#output').html('<br>Invite sent! <hr>You can also share the URL code below:<br> <span style="color:blue">http://asm.camosun.bc.ca/awesominds2020/signup.php?invitecode=' +  data + '</span>');
        } else{
          $('#output').html('It fails. Contact with system administrator.');
        }
      }
    });
  });
 
 // James: show the codes in case email is not available.
 
  $('#manualInviteBtn').click(function (e) { 
    var submit_btn  = $('#manualInviteBtn'); 
    submit_btn.prop( "disabled", true); 
    if(!$('#emailInput').val()){
      $('#output').html('Check the e-mail address.');
    }else {
      e.preventDefault();
      $.ajax({
        type: 'POST',
        url: 'db-inviteinstructor.php',
        data: { email: $('#emailInput').val()
              , task: 'view' },
        success: function(data) {   
          if(!(~data.indexOf("Fail"))){
            $('#output').html('<br>Code is created! <hr>You can share the URL code below:<br> <span style="color:blue">http://asm.camosun.bc.ca/awesominds2020/signup.php?invitecode=' +  data + '</span>');
          } else { 
            $('#output').html('It fails. Contact with system administrator.');
          }
        }
      });
    }
   
  });
</script>

</body>
</html>
