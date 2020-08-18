<head>
<meta charset="UTF-8" />
<title>Create Account - Awesominds 2020</title>
<?php 
  // require 'includes/db.php'; james
  require 'db/db.php'; 
  // require 'includes/conn.php'; james
  require 'db/conn.php'; 
  session_start();
  if($_SESSION["logged_in"]){
    header("location: index.php");
  }
  include 'css/css.html';
  include 'inst-nav2.php';
?>
</head>

<?php 
  if (isset($_GET['invitecode'])){  //check if invite code is valid for instructor registration
    $query = $dbcon->prepare("SELECT invite_code FROM invite WHERE invite_code = :invitecode");
    $query->bindParam(':invitecode', $_GET["invitecode"]);
    $query->execute();
    if($query->fetch(PDO::FETCH_ASSOC)){ //code exists, save it in session for now
      $_SESSION['invitecode'] = $_GET['invitecode'];
    }
  }
  if ($_SERVER['REQUEST_METHOD'] == 'POST') { // information is inputted? then go to register
    if (isset($_POST['register'])) { //user registering
      require 'registerstudent.php';
    }
  } 
?>

<body>
  <script src="js/displaynames.js"></script>
  <div class="container text-center" style="max-width: 400px;">
    <?php
      if (isset($_SESSION['invitecode'])){
        echo '<h2>Create Instructor Account</h2>';
      } else {
        echo '<h2>Create Account</h2>
        <p>Already registered? <a href="index.php">Log in</a></p>';
      }
      if( isset($_SESSION['message']) AND !empty($_SESSION['message']) ){
        echo $_SESSION['message'];
        unset($_SESSION['message']);
      }
    ?>
    <form action="signup.php" method="post" autocomplete="off" id="registerForm"> <!-- TODO: change action to registerstudent.php - Walker -->
      <label for="cnumberInput" class="form-label"><b>Camosun ID*</b></label>
      <input class="form-control" type="text" required autocomplete="off" name="cnumber" id="cnumberInput" pattern="[C][0-9]{7}"
      
      <?php if (isset($_SESSION['c_number_signup'])){
        echo 'value="' .$_SESSION['c_number_signup'].'"';
        unset($_SESSION['c_number_signup']);
      } ?>title="C + 7 numbers, eg 'C0654321'"/><br>

      <label id="displayNameLabel" for="displayNameField"><b>Choose Display Name</b></label>
      <div class="input-group">
        <span class="input-group-btn"><button class="btn btn-success" type="button" id="random">Generate</button></span>
        <input type="text" class="form-control" required autocomplete="off" name='fakename' id="displayNameField" readonly placeholder="Display Name"/>
      </div>
      <p><small>Click 'Generate' until you find a name you like</small></p>

      <label><b>Choose Avatar</b></label>
      <div class="avatars">
      <?php
        $numImages = 31;  // James: 31 avartas 
        for ($i=0; $i < $numImages; $i++) {
          echo '<img class="avatar-img-left float-left" src="assets/small/opp2/oppon' . ($i+1) . '.png" />';  // James: left  
          echo '<img class="avatar-img-right float-right" src="assets/small/opp2/oppon' . ($i+1) . '.png" />';  // James: right  
          echo '<img class="avatar-img img-thumbnail rounded border border-info" src="assets/small/opp2/oppon' . ($i+1) . '.png" id="choosenAvatar"/>'; 
        }
      ?>
      </div>
      <div class="btn-group" role="group">
        <button id="imgbtnminus" type="button" value="-" class="btn btn-info"><i class="fa fa-caret-square-o-left fa-lg" aria-hidden="true"></i></button>
        <button id="imgbtnplus" type="button" value="+" class="btn btn-info"><i class="fa fa-caret-square-o-right fa-lg" aria-hidden="true"></i></button>
      </div>
      <p><small>Click the <i class="fa fa-caret-square-o-left fa-lg text-info" aria-hidden="true"></i> and <i class="fa fa-caret-square-o-right fa-lg text-info" aria-hidden="true"></i> buttons until you find a character you like</small></p>
      <input type="hidden" name="avatarnum" value="1" />

      <?php
      if (isset($_SESSION['invitecode'])) {
        echo '<label for="passwordInput" class="form-label"><b>Password*<b></label>
              <input class="form-control" type="password" required autocomplete="off" name="password" id="passwordInput" pattern=".{8,}" title="Minimum 8 Characters"/><br>';
      } else {
        echo '<p><b>Remember your Display Name and Avatar!</b><br>You will need these to log in.</p>';
      }
      ?>
      <button type="submit" class="btn btn-primary" name="register" disabled id="register"/>Create Account</button>
    </form>
  </div>
</body>

<script>
  var first_NUM_AVATARS = 1;  // James: whole capital doesn't work as global(bug?)
  var end_NUM_AVATARS = 31; 
  var avatarNum = first_NUM_AVATARS;
  var avatarNumLeft = end_NUM_AVATARS;  // James: added
  var avatarNumRight = first_NUM_AVATARS + 1;  // James: added

  // Set the displayed avatar to the current avatarNum
  function updateAvatar() {
    
    $(".avatar-img").hide();  
    $(".avatar-img-left").hide(); // James: hide left except 31
    $(".avatar-img-right").hide(); // James: hide left except 2
    $('.avatar-img:eq(' + (avatarNum-1) + ')').show(); 
    $('.avatar-img-left:eq(' + (avatarNumLeft-1) + ')').show(); // James: added
    $('.avatar-img-right:eq(' + (avatarNumRight-1) + ')').show(); // James: added
   
    $('input[name="avatarnum"]').val(avatarNum);  // James: update avatarNum
  }

  // Generate a random user name
  function randomName() {
    $("#displayNameField").val(displayNames[Math.floor(Math.random() * displayNames.length)]);
  }

  $("#random").click(function(){
    randomName(); 
    document.getElementById("register").disabled = false;  // James: set enabled when name is generated once
    console.log(document.getElementById("register").disabled);
  });

  $("#registerForm").submit(function(){ 
    alert($("#cnumberInput").val() + " " + $("#displayNameField").val() + ", please remember your name and avatar for the next time you log in.");
  });

  $('#imgbtnplus').click(function() {
    // if (avatarNum < $(".avatars img").length) {
      avatarNum++;
      if (avatarNum > end_NUM_AVATARS){avatarNum = first_NUM_AVATARS};
      avatarNumLeft++;
      if (avatarNumLeft > end_NUM_AVATARS){avatarNumLeft = first_NUM_AVATARS};
      avatarNumRight++;
      if (avatarNumRight > end_NUM_AVATARS){avatarNumRight = first_NUM_AVATARS};
      updateAvatar();
    // }
  });

  $('#imgbtnminus').click(function() { 
      avatarNum--;
      if (avatarNum < first_NUM_AVATARS){avatarNum = end_NUM_AVATARS}; 
      avatarNumLeft--;
      if (avatarNumLeft < first_NUM_AVATARS){avatarNumLeft = end_NUM_AVATARS};
      avatarNumRight--;
      if (avatarNumRight < first_NUM_AVATARS){avatarNumRight = end_NUM_AVATARS};
      updateAvatar(); 
  });

  jQuery(document).ready(function($){
    updateAvatar();

    //$('form:first *:input[type!=hidden]:first').focus();
  });
</script>
