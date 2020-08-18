var endingModeState = "";
var previousState = "";
var highScore = 0;
var totalScore = 0;
var endOfModeState = {
  // the set up process of how the whole menu looks
  init : function(data){
    console.log("in endOfMode" + data);
    previousState = data;
  },
  create:function(){
    this.pause();
  }, 

  pause: function(){
    console.log("endOfMode state");
    game.global.stopScreen = this;
    game.global.stopScreen.visible = false;
    game.paused = true;
    game.global.pauseUI = game.add.group();
    console.log(game.global.questionsAnswered);
    console.log(game.global.timesAnswered);
    console.log(this);
    game.global.stopScreen.getScores(); 
  },
  // this function goes into db-getscore.php and gets the score info for
  // the student's attempt for the chapter and course
  getScores:function(){
    $.ajax({
      url: 'db-getscore.php',
      data: 'course_id_fk=' + game.global.selectedCourse + '&chapter_id_fk=' + game.global.selectedChapter + '&task_fk=' + task_id,
      success: function(data){
        console.log("getscore successful", game.global.totalStats);
        game.global.scoreData = $.parseJSON(data);
        console.log(game.global.scoreData);
        //if no data is returned, set up new data and insert it
        if(game.global.scoreData == null){
          console.log("no score");
          /*game.global.scoreData = {
            chapter_id_fk: game.global.selectedChapter,
            course_id_fk: game.global.selectedCourse,
            high_score: game.global.totalStats.totalScore,
            total_score: game.global.totalStats.totalScore,
            task_fk: task_id,
            attempts: 1,
          };*/
          highScore = game.global.totalStats.totalScore;
          totalScore = game.global.totalStats.totalScore;
        }else{
          console.log("got score");
          //if we got data, it's in game.global.scoreData and can be updated
          //game.global.scoreData["total_score"] = parseInt(game.global.scoreData["total_score"]) + game.global.totalStats.totalScore;
          //game.global.scoreData["high_score"] = Math.max(parseInt(game.global.scoreData["high_score"]), game.global.totalStats.totalScore);
          //game.global.scoreData["attempts"] = parseInt(game.global.scoreData["attempts"]) + 1;
          totalScore = parseInt(game.global.scoreData["total_score"]) + game.global.totalStats.totalScore;
          highScore = Math.max(parseInt(game.global.scoreData["high_score"]), game.global.totalStats.totalScore);
          // this updates the entry if there is entry that already exists
          // it updates the points and attempts to the current course and chapter
          // for the student
        }
        //insert the new score or update the existing score
        game.global.stopScreen.updatescore();
      }
    }); 
  },

  /*insertScore:function(prevData){
    $.ajax({
      type: 'POST',
      url: 'insertscore.php',
      data: game.global.scoreData,
      success: function(data){
        console.log("insert successful");
        console.log(game.global.scoreData);
        game.global.stopScreen.getButtons(); //shows the UI with the correct info
      }
    });

  },*/

  updatescore:function(){
    var scoreData = {
      chapter_id_fk: game.global.selectedChapter,
      course_id_fk: game.global.selectedCourse,
      high_score: game.global.totalStats.totalScore,
      total_score: game.global.totalStats.totalScore,
      task_fk: task_id,
    };
    console.log(scoreData);
    $.ajax({
      type: 'POST',
      url: 'db-updateScore.php',
      data: scoreData,
      success: function(data){
        console.log("update successful");
        game.global.stopScreen.getButtons(); //shows the UI with the correct info
      }
    });
  },

  getButtons:function(){
    var pauseBG = game.add.graphics(0, 0);
    pauseBG.lineStyle(2, 0x000000, 1);
    pauseBG.beginFill(0x078EB7, 1);
    pauseBG.drawRoundedRect(game.world.x + 10, game.global.logoText.bottom, game.world.width - 20, game.world.height - game.global.logoText.height - 10, 10);
    game.global.pauseUI.add(pauseBG);

    //game.global.pausedText = game.add.text(game.world.centerX, Math.floor(game.global.logoText.bottom), 'Stopped', game.global.whiteFont);
    game.global.pausedText = game.add.text(game.world.centerX, Math.floor(game.global.logoText.bottom), game.global.stopScreen.getStatLines(), game.global.whiteFont);
    //game.global.pausedText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
    game.global.pausedText.padding.x = 5;
    game.global.pausedText.x = Math.floor(game.global.pausedText.x - (game.global.pausedText.width/2));
    game.global.pauseUI.add(game.global.pausedText);


    // sets the current height for the first button
    var prevHeights = 220;
    console.log("this is previous height" + prevHeights);

    //List of buttons from top to bottom
    var btns = [ {text: 'Select Different Course', clickFunction: game.global.stopScreen.quitToCourseSelect},
      {text: 'Select Different Chapter/Section', clickFunction: game.global.stopScreen.quitToChapterSelect},
      {text: 'Select Different Task', clickFunction: game.global.stopScreen.quitToTaskSelect},
      {text: 'Log Out', clickFunction: game.global.stopScreen.logOut}];
    //if hard questions were being saved, have an option to review them TODO: Marty might want this not
    //to be the lowest button
    if (game.global.gameSettings.saveHardQuestions) {
      btns.push({text: 'Review Hard Ones', clickFunction: game.global.stopScreen.reviewHardQuestions});
    }
    //every time it loops the next button gets set a few pixels lower
    for (var b in btns) {
        var btn = game.world.add(new game.global.SpeechBubble(game, game.world.centerX, prevHeights, game.world.width * .8, btns[b].text, false, true, btns[b].clickFunction));
        btn.x -= Math.floor(btn.bubblewidth/2);
        game.global.pauseUI.add(btn);
        game.input.onDown.add(btns[b].clickFunction, btn);
        prevHeights += btn.bubbleheight + 5;
    };
  },

  getStatLines:function(){
    var percentage;
    if (game.global.questionsAnswered == 0 || game.global.timesAnswered == 0){
      percentage = 0;
    } else {
      percentage = Math.floor((game.global.questionsAnswered / game.global.timesAnswered) * 100);
    }
    //temp numbers to show user, not added to total in database
    game.global.tempTotalScore = game.global.tempTotalScore + game.global.totalStats.totalScore;
    game.global.tempHighScore =  Math.max(game.global.tempHighScore, game.global.totalStats.totalScore);
    var statLines = [
      game.global.session.play_name,
      "\nPercentage: " + parseInt(percentage) + "%",
      "\nScore This Round: " + game.global.totalStats.totalScore,
      "\nTotal Points Earned: " + parseInt(totalScore),
    ];
    game.global.tempTotalScore = game.global.tempTotalScore - game.global.totalStats.totalScore;
    return statLines;
  },
  
  unpause:function(){
    if(game.paused && game.global.inputInside(this)){
      game.global.pauseButton.visible = true;
      game.global.pauseUI.destroy();
      game.input.onDown.removeAll();
      game.paused = false;
    }
  },
  // when this button is clicked you return to the course selection screen 
  quitToCourseSelect: function(){
    if(game.paused && game.global.inputInside(this)){
      this.data.func = function(){
        game.global.pauseButton.visible = true;
        game.global.pauseUI.destroy();
        game.input.onDown.removeAll();
        game.paused = false;
        game.global.stopScreen.chooseCourseClick(this);
      }
      game.global.stopScreen.areYouSure(this);
    }
  },

  chooseCourseClick: function(){
    game.global.music.stop();
    game.state.start('menuCourse');
  },

  quitToChapterSelect:function(){
    if(game.paused && game.global.inputInside(this)){
      this.data.func = function(){
        game.global.pauseButton.visible = true;
        game.global.pauseUI.destroy();
        game.input.onDown.removeAll();
        game.paused = false;
        game.global.stopScreen.chooseChapterClick(this);
      }
      game.global.stopScreen.areYouSure(this);
    }
  },

  chooseChapterClick: function(){
    game.global.music.stop();
    game.global.music = game.add.audio('menu');
    game.global.music.volume = 0.5;
    game.global.music.play();
    game.state.start('menuChapter');
  },

  quitToTaskSelect: function(){
    if(game.paused && game.global.inputInside(this)){
      this.data.func = function(){
        //game.global.unpauseButton.visible = false;
        game.global.pauseButton.visible = true;
        game.global.pauseUI.destroy();
        game.input.onDown.removeAll();
        game.paused = false;
        game.state.start('menuMode');
      }
      game.global.stopScreen.areYouSure(this);
    }
  },

  // when this button is clicked you return to the login page
  logOut:function(){
    if(game.paused && game.global.inputInside(this)){
      this.data.func = function(){
        window.location.href = "logout.php";
        alert("Thank you for using Awesominds");
      }
      game.global.stopScreen.areYouSure(this);
    }
  },

  reviewHardQuestions: function() {
    if(game.paused && game.global.inputInside(this)){
      this.data.func = function(){
        game.global.pauseButton.visible = true;
        game.global.pauseUI.destroy();
        game.input.onDown.removeAll();
        game.paused = false;
        game.state.start('pregame');
      }
      game.global.stopScreen.areYouSure(this);
    }
  },
  
  // this sets up the functionality for the "are you sure" screen
  // this shows when pressing "Home", "Quit to course select" "Log Out"
  areYouSure:function(btn){
    var sureUI = game.add.group();
    var sureGfx = game.add.graphics(0, 0);
    sureGfx.lineStyle(2, 0x000000, 1);
    sureGfx.beginFill(0x078EB7, 1);
    sureGfx.drawRoundedRect(game.world.x + 10, game.global.logoText.y + game.global.logoText.height*2, game.world.width - 20, game.world.height - (game.global.logoText.y + game.global.logoText.height*2) - 10, 10);
    sureUI.add(sureGfx);

    var txt = game.add.text(game.world.centerX, Math.floor(game.global.logoText.y + game.global.logoText.height*2), btn.bitmapText.text, game.global.whiteFont);
    // txt.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5);  // James: Marty wants to remove
    txt.padding.x = 5;
    txt.x = Math.floor(txt.x - (txt.width/2));
    sureUI.add(txt);

    var txt2 = game.add.bitmapText(game.world.centerX, Math.floor(txt.y + txt.height), '8bitoperator', 'Are you sure?', 11 * dpr);
    txt2.x = Math.floor(txt2.x - (txt2.width/2));
    sureUI.add(txt2);

    var btnResult = function(btn){
      if(game.paused && game.global.inputInside(this)){
        var v = this.data.value;
        var b = this.data.btn;
        sureUI.destroy();
        if(v){
          b.data.func.call(this.data.btn);
        }
      }
    };

    var yesBtn = game.world.add(new game.global.SpeechBubble(game, game.world.centerX, Math.floor(txt2.y + txt2.height + game.global.borderFrameSize), game.world.width * .8, 'Yes', false, true, btnResult));
    yesBtn.data.value = true;
    yesBtn.data.btn = btn;
    yesBtn.x = Math.floor(yesBtn.x - yesBtn.bubblewidth * 1.5);
    sureUI.add(yesBtn);
    game.input.onDown.add(btnResult, yesBtn);

    var noBtn = game.world.add(new game.global.SpeechBubble(game, game.world.centerX, yesBtn.y, game.world.width * .8, 'No', false, true, btnResult));
    noBtn.data.value = false;
    noBtn.x = Math.floor(noBtn.x + noBtn.bubblewidth/2);
    sureUI.add(noBtn);
    game.input.onDown.add(btnResult, noBtn);
  },
}



// all have the tasks same "endingMode" except game show
// that gets executed here
endingModeState = Object.create(endOfModeState);