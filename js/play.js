/* MADE BY: Walker Jones
* PURPOSE: the basic structure for all tasks
*/

//the bubble for the comments
var commentBubble;
// the boolean visible for the comment
var visibleComment = false;
var playState = {
  //Variable declaration
  /*gameShowUI: null, //if gameShow is enabled, this will be set to the ui state, otherwise remains null
  attempts: game.global.gameSettings.attempts,
  pointValue: game.global.gameSettings.pointValue,
  timer: game.global.gameSettings.timer,*/

  /*
  *sets up number of questions/game
  *sets up the game NPC's and assigns win % to each
  */
  create: function () {

    
    console.log(game.global.gameSettings);
    gameShowUIState.course_chapter_text(); // James: this is common UI for all types

    // James: GameShow - create Host & Name & Start message
    if (game.global.gameSettings.gameShow) { 
     


      gameShowUIState.hostSpeech('firstMessage'); 
      gameShowUIState.drawHost(game.global.gameSettings.currentChallengeName);  // James: put the parameter challenge type of name  
      
      gameShowUIState.drawInitialAvartars('N'); // James: only shfulle avartars one time at pregame
      //reset round score to compare against goal at end
      game.global.totalStats.roundScore = 0; 
    } else {
      //setup point text, not needed in game show since it is shown in the game show ui
      // James: Mobile reposition
      this.pointText = game.add.text((dpr == 1) ? 700 : game.world.centerX - 80, 0, "Points: " + game.global.totalStats.totalScore, game.global.blackFont);
    }

    
    //setup variables
    this.gameShowUI = null; //if gameShow is enabled, this will be set to the ui state, otherwise remains null
    this.selectedAnswers = []; //Array that contains the answers to be submitted on done button click 
    this.timer = null; //declare here to hopefully prevent undeclared bug
 
    // James: reset the num of correct questions(get the answer at the first try) for all and create initial bar to set it lower layer than buttons. this is important
    if (game.global.gameSettings.gameShow) { 
      for (i = 0; i < game.global.chars.length; i++) {
          game.global.chars[i].numCorrectQuestion = 0;  

          game.global.chars[i].gfx = game.add.graphics(0, 0);
          game.global.chars[i].gfx.visible = false;
          game.global.chars[i].gfx.beginFill(0x02C487, 1);
          game.global.chars[i].gfx.drawRect(game.global.chars[i].sprite.x, game.global.chars[i].sprite.y, game.global.chars[i].sprite.width, 1);
          game.global.chars[i].barSprite = game.add.sprite(Math.floor(((game.width / game.global.chars.length) * (i + 1) - game.width / game.global.chars.length) + (game.width / 25)) , game.global.chars[i].sprite.y, game.global.chars[i].gfx.generateTexture());
          game.global.chars[i].barSprite.anchor.y = 1;  
      }
    } 

    game.global.questionUI = game.add.group();

    // Rehash round seems to be the review of going over questions that were hard. 
    // We should change this to make it less dependant of tempPlay - Walker
    console.log('state: play');
    /*if (game.global.isRehash) { //if in rehash round, use the array of questions that were answered incorrectly in the previous round
      game.global.questions = game.global.rehashQuestions;
    }*/
    //console.log('rehash: ' + game.global.isRehash);
    this.ticks = game.add.group();
    //game.global.numQuestions = Math.min( (devmode ? devvars.numQ : 10), game.global.questions.length);
    game.global.questionsAnswered = 0;
    //game.global.questionShown = false;
    //game.global.answeredBeforeAI = false;
    //if(!game.global.isRehash){
    //game.global.numOrigQuestions = game.global.numQuestions;
    
    game.global.answerBubbles = game.add.group();
    //}

    // MUSIC
    game.global.music.stop();
    game.global.music = game.add.audio('play');
    game.global.music.loop = true;
    game.global.music.play();
    this.enterSound = game.add.audio('question');
    this.enterSound.volume = 0.2; 
    this.questionDisappearSound = game.add.audio('questionDisappear');  // James: question disappears
    game.global.playerWinSound = game.add.audio('playerWin');  //James: player wins at game show
    game.global.endOfGameSound = game.add.audio('endGame');  // James: player doesn't win at game show
    
    //Temporary math fixers. NOTE: unsure what these are for - Walker
    // game.global.answersShown = false;   // James: this control to show AI's answer one time at each question 1/2
    game.global.winStreak = 1; // James: variable to adjust AI's possibility (TODO) which file is the best to have this? gameshowUI, play.js
    game.global.loseStreak = 1;
    /*game.global.answersShown = false;
    game.global.numCor = 0;
    game.global.numWro = 0;
    game.global.lXOffset = 16;
    game.global.rXOffset = 16;
    game.global.winStreak = 1;
    game.global.loseStreak = 1;
    */

    // Stop button
    // James: Mobile reposition
    game.global.stopBtn = game.world.add(new game.global.SpeechBubble(game, 
        (dpr == 1) ? 900 : game.global.pauseButton.left - game.global.pauseButton.bubblewidth - game.global.borderFrameSize - 22 * adjust_dpr, 
        0, 
        100, 
        "Stop", 
        false, 
        true, 
        this.stopBtnClick));
  
    // James: Mobile -> reposition right top buttons Stop -> Gear
    if(game.global.pauseButton && (dpr > 1)){    
        game.global.courseText.x = Math.round(game.global.stopBtn.left - game.global.courseText.width - game.global.borderFrameSize);
        game.global.chapterText.x = Math.round(game.global.stopBtn.left - game.global.courseText.width - game.global.borderFrameSize);
    }

    // Text of task name and description
    // James: Mobile displays simply(dpr > 1)
    this.taskNameText = game.add.text((dpr == 1) ? 500 : (game.global.gameSettings.gameShow) ? game.global.host.right + 10 : 100, 0, (game.global.gameSettings.gameShow) ? game.global.gameSettings.currentChallengeName : game.global.taskName, game.global.blackFont);
    this.taskDescText = game.add.text(game.world.centerX, (dpr == 1) ? 75 : game.global.pauseButton.bottom + game.global.pauseButton.bubbleheight*4 + 5 + 1 * adjust_dpr, game.global.taskDesc, game.global.blackFont);
    this.taskDescText.anchor.setTo(0.5);
 
    //point text
    //this.pointText = game.add.text(900, 0, "Points: " + game.global.totalStats.totalScore, game.global.blackFont);
    //show the rehash splash or the first question
    /*if(game.global.isRehash){
      function playBtnClick(){
        this.destroy();
        this.setupNewQuestion();
      }
      var playBtn = game.world.add(new game.global.SpeechBubble(game, game.world.centerX, game.height, game.width, "Play", false, true, playBtnClick));
      playBtn.x = Math.floor(playBtn.x - (playBtn.bubblewidth/2));
      playBtn.y = Math.floor(game.global.jinnySpeech.y + game.global.jinnySpeech.bubbleheight + (10*dpr));
    } else {*/
      //set up first question
      this.setupNewQuestion();
      //this.setUpGraph();
    //}
  },
  createStopBtn:function () {
    
    // Stop button
    var stopBtn = game.world.add(new game.global.SpeechBubble(game, 
      500, 
      0, 
      100, 
      "Stop", 
      false, 
      true, 
      this.stopBtnClick));
      
  },

  /*
  *updates ai and characters score on screen
  */
  update: function () {
    // James: GameShow - updating scores for animation effect
    if (game.global.gameSettings.gameShow) {
      gameShowUIState.updateScoreUI();
    }

    //if a timer is set, constantly update the visual timer
    if (this.timer != null) {
      if (this.timer.duration > 0) {
        this.updateTimerGraphic(Math.ceil(this.timer.duration / 1000));
      }
    }
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: redirect to the stop screen when the stop button is clicked, passing the key of the current
  *   state so the stop screen can return to this state.
  */
  stopBtnClick: function () { 
    console.log("stop button");
    console.log(game.state.getCurrentState().key);
    stopState.pause();
    //game.state.start("stop", true, false, game.state.getCurrentState().key);
  },

    /*
  * MADE BY: Walker Jones
  * PURPOSE: Is a controller function to set up a new question, removes any existing question before
  * doing so.
  */
  setupNewQuestion: function(){ 
    visibleComment = false;
    // if there are no more questions left in backlog and none saved in global, redirect to either pregame or endOfGame
    //TODO: check if the hard questions were being saved and if so, set that bool to false and redirect to pregame.
    if (game.global.questions.length == 0) { 
      if (game.global.gameSettings.gameShow) { // if it is a game show, check if there are more rounds 

        game.global.stopBtn.destroy();

        // James: GameShow, darw a Bar on current points 
        game.state.getCurrentState().removeQuestion();
 
        gameShowUIState.makeEndingRoundBar();

        switch(game.global.rounds[game.global.gameShowSettings.currentRound-1].goal) {
          case "Points":
            if (game.global.totalStats.roundScore < 
                game.global.rounds[game.global.gameShowSettings.currentRound-1].point_goal) {
              game.global.gameShowSettings.lives--;
              gameShowUIState.hostTweenSpeech("You didn't get enough points this round,\n you lost a life.");
            }
            break;
          case "Beat opponent":
            //player must beat both opponents to not lose a life.
            if (game.global.totalStats.roundScore < game.global.chars[1].score && 
                game.global.totalStats.roundScore < game.global.chars[2].score) {
              game.global.gameShowSettings.lives--;
              gameShowUIState.hostTweenSpeech("You didn't beat your opponents this round,\n you lost a life.");
            }
            break;
          default:
        }

        function nextRoundBtnClick() { 
          game.global.gameShowSettings.currentRound++;
          if (game.global.gameShowSettings.currentRound > game.global.gameShowSettings.numOfRounds) {
            game.state.start('sameEndMode', true, false);
          } else if (game.global.gameShowSettings.lives <= 0) {
            game.state.start('gameOver', true, false);
          } else {
            game.state.start('pregame', true, false);
          }
        }

        function goToCourseSelect() {

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
              game.state.start('menuCourse', true, false);
            }
          });
        }

        var nextRoundText = "Next Round";

        if (game.global.gameShowSettings.lives <= 0) {
          game.state.getCurrentState().taskNameText.setText("Game Over");
          nextRoundText = "Game Over";
        }

        // Make buttons
        // James: Mobile(dpr > 1)
        var nextRoundBtn = game.world.add(new game.global.SpeechBubble(game,
          0,  //x
          (dpr == 1) ? 50 : game.global.pauseButton.bottom + game.global.pauseButton.bubbleheight + 5 + 5 * adjust_dpr,  //y
          200, //width
          nextRoundText,
          false,
          true,
          nextRoundBtnClick
        )
        );
        nextRoundBtn.x = game.world.width - (nextRoundBtn.bubblewidth + 10);

        // James: Mobile adjustment
        var quitBtn = game.world.add(new game.global.SpeechBubble(game,
           0,  //x
           (dpr == 1) ? 100 : game.global.pauseButton.bottom + game.global.pauseButton.bubbleheight * 3 + 5 + 5 * adjust_dpr, //y
           200, //width
          "Quit",
          false,
          true,
          goToCourseSelect
        )
        );
        quitBtn.x = game.world.width - (quitBtn.bubblewidth + 10);
      } else { 
        game.state.start('sameEndMode', true, false);
      }
    } else { 
      //remove current question and load new one 
      game.state.getCurrentState().removeQuestion();
      game.state.getCurrentState().addQuestion();
    }
  },
  setUpGraph:function(){
    //this function is empty because all the functionality is only for just drills
  },

  updateGraph:function(){
    //this is empty because all the functionality is only for just drills
  },

  /*
  * MADE BY: Previous Teams and Walker Jones
  * PURPOSE: Removes and destroys the ui elements for the question, sets reference to null
  * so addQuestion will retrieve a new one.
  */
  removeQuestion: function () {
    this.questionDisappearSound.play(); // James: question disappear sounds
    game.global.music.volume = 0.5; // resume the BGM
    game.global.questionUI.removeAll(true);
    game.global.questionShown = false;
    game.global.questionUI.x = 0; // x is not 0 after it animates out
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: Adds button that displays question from either the current saved question or from the
  * question bank
  */
  addQuestion: function () {
    // If not question is saved, load and save a new question. questions are saved in case
    // stop or settings menu is opened, thus a new question will not override the displayed
    // one when coming back from a menu - Walker

    console.log(game.global.questions);
    // game.global.answersShown = false;   // James: this control to show AI's answer one time at each question 2/2
    var question = game.global.questions.shift();
    console.log("new question!");
    // reset values to defaults
    if (game.global.gameSettings.gameShow) {
      for (i = 0; i < game.global.chars.length; i++) {  // James: check AI answered once for a question
        game.global.chars[i].didFirstAnswer = false; 
        game.global.chars[i].correct = false; 
        game.global.chars[i].timesAnswered = 0;  // how many answered for a question
      }
    }

    game.state.getCurrentState().attempts = game.global.gameSettings.attempts;
    game.state.getCurrentState().pointValue = game.global.gameSettings.pointValue;
    game.state.getCurrentState().timer = game.global.gameSettings.timer;
    game.state.getCurrentState().wrongAnswers = 0;
    console.log("single question: " + question);
    game.global.currentQuestion = question;

    // James: reset AI's chance to answer
    if (game.global.gameSettings.gameShow) {
      for (i = 1; i < game.global.chars.length; i++) { 
          game.global.chars[i].isGetPoint = false;   // James: to check whether AI gets the point on current question
      }
    }

    // If a timer is set, create a phaser timer
    // NOTE: currently this method has a bug, the time spent on the question "screen" will carry over 
    // into the answers "screen", but the actual amount of time will remain the same
    // E.g the timer is 10 seconds, the user spends 3 seconds before clicking the question button
    // the timer starts at 7 and counts down, when it hits 0 it jumps back up to 3 and continues to 
    // count down, upon hitting 0 again, it works normally
    game.state.getCurrentState().prevHeights = 125 * dpr;

    var questionBtn = game.world.add(new game.global.SpeechBubble(game,
      game.world.centerX,  //x
      game.state.getCurrentState().prevHeights,  //y
      500, //width
      game.global.currentQuestion.question,
      false,
      true,
      this.questionBtnClick));
    game.state.getCurrentState().prevHeights += questionBtn.bubbleheight + 20 * dpr;
    questionBtn.x -= Math.floor(questionBtn.bubblewidth / 2); //perfectly center button
    questionBtn.question = game.global.currentQuestion;
    game.global.questionUI.add(questionBtn);
    game.global.questionBtn = questionBtn; //need to save in case of timer

    // Text under question button
    game.global.promptText = game.add.text(0,
      Math.floor(questionBtn.y + questionBtn.bubbleheight),
      '^ Click/Tap Question To Show Options ^',
      game.global.smallerBlackFont);
    game.global.promptText.x = Math.floor(game.world.centerX - game.global.promptText.width / 2);
    // game.global.promptText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
    game.global.promptText.padding.x = 5;

    // if there is a timer, set up the graphic
    if (game.global.gameSettings.timer > 0) {
      this.createTimer();
      //this.updateTimerGraphic(game.global.gameSettings.timer);
    }

    //timer - the phaser way
    game.global.timer = game.time.create(false);
    game.global.timer.start();

    // James: play the sound when a question appears
    this.enterSound.play();
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: connecting funcion for the question button, here to make inheretence easier
  */
  questionBtnClick: function() {
    this.inputEnabled = false;
    game.state.getCurrentState().showChoices();
  },

  showChoices: function () {
    // James: task34 - speech will diappear soon
    if (game.global.hostSpeech) {
      game.global.hostSpeech.destroy();
    }
    game.global.promptText.destroy();

    if (game.global.gameSettings.timer > 0) {
      console.log("timer created");
      game.state.getCurrentState().timer = game.time.create(true); //true auto destroys itself when done
      game.state.getCurrentState().timer.stop();
      game.state.getCurrentState().timer.add(game.global.gameSettings.timer * 1000, game.state.getCurrentState().timeUp, game);
      game.state.getCurrentState().timer.start();
    }

    //Create a button for each choice, and put some data into it in case we need it
    game.global.choiceBubbles = game.add.group();
    var i = 0;
    //array to store available letter choices for ai to choose from for this question
    //var availChoices = [];
    var tweens = [];
    var question = game.global.currentQuestion;
    var shuffChoices = [];
    var answerText = '';

    // convert associative array to numeric array
    for (var c in question.choices) {
      shuffChoices.push(question.choices[c]);
    }

    // Get the letter of the correct answer
    answerText = question.choices[question.answer[0]];
    shuffChoices = game.global.shuffleArray(shuffChoices);
    i = 0;
    //console.log("btnclick", game.state.getCurrentState().btnClick);
    for (var c in question.choices) { //create buttons for each choice from the question
      var cb = game.world.add(new game.global.SpeechBubble(game,
        game.world.width + 1000, // The starting x position of button, should be offscreen
        game.state.getCurrentState().prevHeights, 
        500,
        shuffChoices[i],
        false,
        true,
        game.state.getCurrentState().selectAnswer, //NOTE: using "this.selectAnswer" doesn't work for some reason, potentionally because showChoices is called from a button
        true,
        c));
      game.state.getCurrentState().prevHeights += cb.bubbleheight + 10 * dpr;
      // the animation to the resting point of the buttons
      game.add.tween(cb).to({ x: Math.floor(game.world.centerX - cb.bubblewidth / 2) }, 500, Phaser.Easing.Default, true, 250 * i);
      if (shuffChoices[i] == answerText) question.newAnswer = c;
      cb.data = { // text and fullQuestion might be able to be deleted - Walker
        letter: c,
        text: c + '. ' + shuffChoices[i],
        correct: (shuffChoices[i] == answerText),
        fullQuestion: question
      };
      game.global.choiceBubbles.add(cb);
      //availChoices[i] = c;
      i++;
    }

    game.global.questionUI.add(game.global.choiceBubbles);
    game.state.getCurrentState().createSubmitButton();
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: creates the submit button for the current question
  */
  createSubmitButton: function() {
    var bConfirm = game.world.add(new game.global.SpeechBubble(game,
      game.world.width + 1000,
      game.state.getCurrentState().prevHeights,
      game.width,
      "Confirm",
      false,
      true,
      game.state.getCurrentState().submitAnswers));
    game.add.tween(bConfirm).to({ x: Math.floor(game.world.centerX - bConfirm.bubblewidth / 2) }, 500, Phaser.Easing.Default, true, 1500);

    game.global.questionUI.add(bConfirm);
    game.global.questionShown = true;
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: triggered by Submit button. adjusts points for incorrect answers, shows check or x
  * for chosen answers and calls setupNewQuestion if correct answer was chosen.
  * IDEA: change name to submitButton and have it call confirmAnswers, so additional functionality
  * needed in modes like rateQuestion can be added with their own function which would also be 
  * called from here
  */
  submitAnswers: function () {
    // James: set the sound mute temporalily
    // game.global.tempVolumne = game.global.music.volume;
    game.global.music.volume = 0;

    //set cursor back to default; gets stuck as 'hand' otherwise
    game.canvas.style.cursor = "default";

    var correct = false; // whether or not the correct answer was chosen

    // POINT HANDLING

    // ensure user has selected at least one answer
    if (game.state.getCurrentState().selectedAnswers.length == 0) {
      if (game.global.gameSettings.gameShow) {
        // var comment = "Please select at least one answer.";// TODO display this text -> James placed it with below
        gameShowUIState.hostSpeech('selectOneAnswer'); // James: call host normal sppech with code or any messages
      }
    } else {
      game.global.timesAnswered++;
      if (game.global.gameSettings.gameShow) {
        // James: Handling AI answer AND display their Answer
        game.state.getCurrentState().setAIAnswer();
        gameShowUIState.showAIAnswers();
      }

      //disable this button (Confirm button)
      //this.inputEnabled = false;
      game.state.getCurrentState().attempts--;
      console.log(game.state.getCurrentState().attempts, " attempts remaining");
      //Point calculation
      for (var i = 0; i < game.state.getCurrentState().selectedAnswers.length; i++) {
        // check if any of the selected answers are correct
        if (game.state.getCurrentState().selectedAnswers[i].correct) {
          correct = true;
          game.global.questionsAnswered++;
        } else { // decrease point value for every wrong answer 
          game.global.totalStats.winStreak = 0;
          game.state.getCurrentState.wrongAnswers++;
          // game.state.getCurrentState().pointValue -= Math.floor(game.global.gameSettings.pointValue * game.global.gameSettings.pointDecay);
          game.state.getCurrentState().pointValue = game.global.gameSettings.pointValue * ((game.global.chars[0].timesAnswered + 1  == 1) ? 1 : (game.global.chars[0].timesAnswered + 1 == 2) ? 0.67 : (game.global.chars[0].timesAnswered + 1  == 3) ? 0.33: 0);

          // If points value is below 0 and and shouldnt be, set to 0
          if (game.state.getCurrentState().pointValue < 0 && game.global.gameSettings.stopAtZero) {
            game.state.getCurrentState().pointValue = 0;
          }
        }
      }

      // James: check the user get the answer at the first trial
      if (game.global.gameSettings.gameShow) {
        if(!game.global.chars[0].didFirstAnswer && correct){
          game.global.chars[0].numCorrectQuestion++;
        }
        game.global.chars[0].didFirstAnswer = true; 
      }
      // ANIMATION HANDLING

      //Go through all answer bubbles and show an X or check for each one chosen
      game.global.choiceBubbles.forEach(function (item) {
        // if the button is chosen, animate either check or x and disable the button
        if (item.alpha < 1 && item.inputEnabled == true) {
          item.inputEnabled = false; //disable input on button after choice is confirmed
          if (item.data.correct) {
            // animate a check symbol
            var check = game.add.sprite(game.world.x - game.world.width, item.centerY, 'check');
            check.height = check.width = game.global.borderFrameSize * 3;
            check.anchor.setTo(0.5, 0.5);
            game.global.questionUI.add(check);
            game.add.tween(check).to({ x: Math.floor(item.x - check.width / 3), y: Math.floor(item.y + item.bubbleheight / 2) }, 300, Phaser.Easing.Default, true, 0);
          } else {
            // animate an X symbol
            var arrow = game.add.sprite(game.world.x - game.world.width, item.centerY, 'x');
            arrow.height = arrow.width = game.global.borderFrameSize * 3;
            arrow.anchor.setTo(0.5, 0.5);
            game.global.questionUI.add(arrow);
            game.add.tween(arrow).to({ x: Math.floor(item.x - arrow.width / 3), y: Math.floor(item.y + item.bubbleheight / 2) }, 300, Phaser.Easing.Default, true, 0);
          }
        }
      });

      //play "correct" or "incorrect" sound
      var sounds = correct ? game.global.rightsounds : game.global.wrongsounds;
      sounds[0].play();

      //remove all selected answers
      game.state.getCurrentState().selectedAnswers = [];

      game.global.timer.stop();
      //game.global.timer.add(100, btnClickShowAnswers, this);
      //game.global.timer.add(100, btnClickSymbolFeedback, this);
      //game.global.choiceBubbles.forEach( function(item){ item.inputEnabled = false; } )

      // if the correct answer was chosen, set global question to null
      // so a new question will be pulled, add pooints, update point text, and setup new question
      if (correct) {
        game.global.choiceSize = Object.keys(game.global.currentQuestion.choices).length;
        //disable confirm button
        this.inputEnabled = false;
        game.state.getCurrentState().correctAnswerChosen();
      } else if (game.state.getCurrentState().attempts == 0) {
        game.global.totalStats.winStreak = 0;
        // James: decrease the AI's win possibility
        if (game.global.gameSettings.gameShow) {
          game.global.loseStreak += 1;
          game.global.winStreak = 1;
          gameShowUIState.hostSpeech('wrong');
        }

        //stop the timer
        if (game.global.gameSettings.timer > 0) {
          game.state.getCurrentState().timer.stop();
        }
        // if answer was incorrect and there are no attempts remaining,
        // show correct answer and set up new question
 
        if (game.global.gameSettings.saveHardQuestion &&
            game.global.gameSettings.saveHardQuestionMethods.includes("failure")) { 
          game.global.hardQuestions.push(game.global.currentQuestion);
        }

        game.state.getCurrentState().setupNextButton();
        game.state.getCurrentState().setupCommentButton();
        game.global.timer.add(500, game.state.getCurrentState().showCorrectAnswer, game.state.getCurrentState());
      } else {
        //the wrong answer was chosen, set win streak to 0
        game.global.totalStats.winStreak = 0;
      }

      game.global.timer.start();
    }
    // James: display the bar during loadign new question, should be called after pointing calculation
    if (game.global.gameSettings.gameShow) {
      gameShowUIState.makeBars();
    }
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: connecting function for when player chooses the right answer, made so other files
  * using this as a template can alter it easier
  */
  correctAnswerChosen: function() {
    game.state.getCurrentState().correctAnswerChosenPrimaryFunction();
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: Disables click function on all buttons
  */
  disableAnswers: function() {
    game.global.choiceBubbles.forEach(function (item) {
      item.inputEnabled = false;
    });
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: the basic functionality that executes when the player chooses the correct answer
  * adds points and updates points graphic. displays "next" button. saves question in hard question
  * bank if student struggled with it
  */
  correctAnswerChosenPrimaryFunction: function() {
    //disable remaining choice buttons
    game.state.getCurrentState().disableAnswers();
    game.state.getCurrentState().setupCommentButton();
    //stop the timer and add the remaining seconds as points
    if (game.global.gameSettings.timer > 0) {
      game.state.getCurrentState().pointValue += Math.ceil(this.timer.duration / 1000);
      game.state.getCurrentState().timer.stop();

      // The parse/stringify is a simple way to get a copy instead of a reference
      var font = JSON.parse(JSON.stringify(game.global.blackFont));
      font.fontSize = font.fontSize * 3;
      //generate text with the points earned to fly in
      var timerPointText = game.add.text(10000, game.world.centerY, game.state.getCurrentState().pointValue, font);
      timerPointText.anchor.setTo(0.5, 0.5);
      game.add.tween(timerPointText).to({ x: game.world.centerX }, 300, Phaser.Easing.Default, true, 0);
      game.global.questionUI.add(timerPointText);
    }
    //TODO: if needed, check if the number of attempts is equal to max attempts and if not
    //dont increase win streak
    game.global.totalStats.winStreak++;

    // Add point value to the score and update the points graphics
    game.global.totalStats.totalScore += game.state.getCurrentState().pointValue;
    // James: update Player's point; points animation is excuted in update
    if (game.global.gameSettings.gameShow) {
      //add points to round score as well to check goal at end
      game.global.totalStats.roundScore += game.state.getCurrentState().pointValue;

      // if the player got enough correct questions in a row, add bonus points
      if (game.global.totalStats.winStreak > 0 && 
          game.global.totalStats.winStreak % game.global.gameShowSettings.inARow == 0) {
        game.global.totalStats.totalScore += game.global.gameShowSettings.inARowPoints;
        game.global.totalStats.roundScore += game.global.gameShowSettings.inARowPoints;
        game.global.chars[0].score += game.global.gameShowSettings.inARowPoints;
        gameShowUIState.hostSpeech("You've earned bonus points for your streak");
      } else {
        gameShowUIState.hostSpeech('right');  // James: (TODO)consider C123
      }
      // James: increase the AI's win possibility
      game.global.loseStreak = 1;
      game.global.winStreak += 1;
      game.global.chars[0].score += game.state.getCurrentState().pointValue; // James: this is round's current total score not totalStats score 
    } else {
      //update points graphic
      game.state.getCurrentState().updatePointText();
    }

    if (game.global.gameSettings.saveHardQuestion &&
        game.global.gameSettings.saveHardQuestionMethods.includes("incorrectAnswers")
        && game.state.getCurrentState.wrongAnswers >= 2) { 
      game.global.hardQuestions.push(game.global.currentQuestion);
    }

    game.state.getCurrentState().setupNextButton();
  },

  /*
    Creator: Adam Lowe
    Purpose: To show a comment when there is a comment to be shown
    Note: If there is a no comment so "null" or "undefinded" it will be changed to 
          "No comment for this question"
  */
  getComment: function(){
    visibleComment = !visibleComment;
    console.log("visible " + visibleComment);
    if(visibleComment){
      var comment = game.global.currentQuestion.comment;
      if (comment == "" || comment == null || comment == undefined){
        comment = "No comment for this question";
      }
      commentBubble = game.world.add(new game.global.SpeechBubble(game, 
        175, 
        100, 
        game.world.width, 
        comment,
        true,
        false));
      game.global.questionUI.add(commentBubble);
    }
    else{
      commentBubble.kill();
    }
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: adds or removes an answer from selected answers list. dims or undims the button clicked
  * NOTES: Executes when a choice is clicked
  */
  selectAnswer: function () { 
    // set cursor back to default; gets stuck as 'hand' otherwise
    game.canvas.style.cursor = "default";

    // update selected options, alpha is 1 if it is unselected
    // check if button is unselected
    if (this.alpha == 1) {
      // if the current selected options exceed the options limit, remove the topmost one
      if (game.state.getCurrentState().selectedAnswers.length >= game.global.gameSettings.choicesAtOnce) {
        var foundItem = false; //foundItem is mostly for C123 so all answers aren't removed at once
        game.global.choiceBubbles.forEach(function (item) {
          if (item.alpha != 1 && item.inputEnabled == true && !foundItem) {
            console.log("found chosen answer");
            item.alpha = 1;
            var i = game.state.getCurrentState().selectedAnswers.indexOf(item.data);
            game.state.getCurrentState().selectedAnswers.splice(i, 1);
            foundItem = true;
          }
        });
      }
      // if the answer is being chosen, add answer to answers array
      game.state.getCurrentState().selectedAnswers.push(this.data);
      //dim button
      this.alpha = 0.25;
      //this.tint = 0xffffaa;
    } else {
      // If answer is being unchosen, remove answer from answers array
      var i = game.state.getCurrentState().selectedAnswers.indexOf(this.data);
      game.state.getCurrentState().selectedAnswers.splice(i, 1);
      //undim button
      this.alpha = 1;
    }
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: clears selected answers, shows correct answer and sets up new question.
  * NOTES: called upon the question timer expiring
  */
  timeUp: function () {
    console.log("time up!");
    // James: GameShow - Host speaks time up
    if (game.global.gameSettings.gameShow) {
      gameShowUIState.hostSpeech('timeUp');
      // James: wrong sound plays
      game.global.wrongsounds[0].play();
    }
    game.global.questionsAnswered++;

    //remove all selected answers
    game.state.getCurrentState().selectedAnswers = [];
    //save the question for review if needed
    if (game.global.gameSettings.saveHardQuestion &&
        game.global.gameSettings.saveHardQuestionMethods.includes("timeUp")) { 
      game.global.hardQuestions.push(game.global.currentQuestion);
    }

    game.state.getCurrentState().showCorrectAnswer();
    game.state.getCurrentState().setupNextButton();
    game.state.getCurrentState().setupCommentButton();
 },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: creates a "next" button, which will remove the current question and setup a new one
  * TODO: make questionui animate out on click, maybe in setupNewQuestion
  * TODO: change onclick function from setupnewquestion to a function specific to next button
  * to make inheretence easier.
  */
  setupNextButton: function() {
    var nextBtn = game.world.add(new game.global.SpeechBubble(game,
      game.width * .70,
      game.state.getCurrentState().prevHeights,
      game.width,
      "Next",
      false,
      true,
      game.state.getCurrentState().setupNewQuestion));
    game.global.questionUI.add(nextBtn);

    nextBtn.x -= Math.floor(nextBtn.bubblewidth / 2);
  },

  // Comment Button
  setupCommentButton:function(){
  var bComment = game.world.add(new game.global.SpeechBubble(game,
    100,
    100,
    game.width,
    "?",
    false,
    true,
    game.state.getCurrentState().getComment));
    

    game.global.questionUI.add(bComment);
    //game.global.questionShown = true;
  },


  /*
  * MADE BY: Walker Jones
  * PURPOSE: updates the point text graphic with the current number of points
  */
  updatePointText: function () { //TODO: change to round score for game show if needed
    game.state.getCurrentState().pointText.setText("Points: " + game.global.totalStats.totalScore);
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: updates the timer text graphic with the seconds remaining
  * NOTES: Calling this when there is no timer will cause an error TODO: shift timer checking
  * from function calling to here
  * TODO: improve this to use existing graphic that changes colour as time goes down
  */
  updateTimerGraphic: function (time) {
    //change colour as time runs out
    if (time <= Math.floor(game.global.gameSettings.timer * (1 / 3))) {
      //make bar red after 2/3 of the time is up
      this.gfx = game.add.graphics(game.world.x - 1000, game.world.y - 1000);
      this.gfx.lineStyle(1, 0x000000, 1);
      this.gfx.beginFill(0xf70e0e, 1);
      this.gfx.drawRoundedRect(this.gfx.x, this.gfx.y, game.global.questionBtn.bubblewidth, 8 * dpr, 5);
      this.timerBar.loadTexture(this.gfx.generateTexture());
    } else if (time <= Math.floor(game.global.gameSettings.timer * (2 / 3))) {
      //make bar yellow after 1/3 of the time is up
      this.gfx = game.add.graphics(game.world.x - 1000, game.world.y - 1000);
      this.gfx.lineStyle(1, 0x000000, 1);
      this.gfx.beginFill(0xebf442, 1);
      this.gfx.drawRoundedRect(this.gfx.x, this.gfx.y, game.global.questionBtn.bubblewidth, 8 * dpr, 5);
      this.timerBar.loadTexture(this.gfx.generateTexture());
    }
    this.timeLabel.text = time;
    this.timeLabel.centerX = Math.floor(game.global.questionBtn.x + game.global.questionBtn.bubblewidth / 2);
    this.timeLabel.y = Math.floor(game.global.questionBtn.y - (this.timeLabel.height * 2.5)); //remove?

    this.timerBar.width = game.global.questionBtn.bubblewidth - game.global.mapNum((game.global.gameSettings.timer * 1000) - game.state.getCurrentState().timer.duration,
      0,
      game.global.gameSettings.timer * 1000,
      0,
      game.global.questionBtn.bubblewidth);

    this.timerBar.centerX = Math.floor(game.global.questionBtn.x + game.global.questionBtn.bubblewidth / 2);
    this.timerBar.centerY = game.global.questionBtn.y;
  },

  /*
  * MADE BY: Walker Jones
  * PURPOSE: Animates a check mark next to the correct answer and disables any more input from 
  * the user.
  * NOTES: Usually called when the player fails to choose the correct answer in time/in the amount
  * of attempts given
  * TODO: should ONLY display correct answer, other function like disabling should be another function
  * like in play-rateQuestions.php
  */
  showCorrectAnswer: function () {
    game.global.choiceBubbles.forEach(function (item) {
      item.inputEnabled = false; //disable input on each button
      if (item.data.correct) {
        // dim the correct answer to make it stand out
        item.alpha = 0.25;
        // animate a check symbol for correct answer
        var check = game.add.sprite(game.world.x - game.world.width, item.centerY, 'check');
        check.height = check.width = game.global.borderFrameSize * 3;
        check.anchor.setTo(0.5, 0.5);
        game.global.questionUI.add(check);
        game.add.tween(check).to({ x: Math.floor(item.x - check.width / 3), y: Math.floor(item.y + item.bubbleheight / 2) }, 300, Phaser.Easing.Default, true, 0);
      }
    });
  },

  animateOut: function () {
    game.add.tween(game.global.questionUI).to({ x: game.world.x - game.world.width }, 300, Phaser.Easing.Default, true, 0);

    //game.global.timer.stop();
    //game.global.timer.add(200, removeAnswers, this);
    //game.global.timer.add(600, makeBars, this, this.data.correct, didntAnswer);
    //game.global.timer.add(2000, this.nextQuestion, this);
    //game.global.timer.start();
  },

  /*
      * MADE BY: James
      * PURPOSE: Set AI's answer based on win % 
      * NOTES: showAIAnswers() will draw their Answer
      */
  setAIAnswer: function () {

    // James: AI win % setting, result and call show Answer function
    if (game.global.winStreak % 4 == 0) {
      for (i = 1; i < game.global.chars.length; i++) {
        if (game.global.chars[i].chance >= 80) {
          game.global.chars[i].chance = 80;
        }
        else { game.global.chars[i].chance += 5; }
      }
    } else if (game.global.loseStreak % 4 == 0) {
      for (i = 1; i < game.global.chars.length; i++) {
        if (game.global.chars[i].chance <= 25) {
          game.global.chars[i].chance = 25;
        }
        else { game.global.chars[i].chance -= 5; }
      }
    }
    // James: decide AI WILL get correct with points or wrong  
    for (i = 1; i < game.global.chars.length; i++) { 
      var tempPoints = 0;
      game.global.winThreshold = Math.floor(Math.random() * 100) + 1;
      game.global.chars[i].correct = (game.global.winThreshold <= game.global.chars[i].chance);

      if(!game.global.chars[i].didFirstAnswer && game.global.chars[i].correct){  // the 1st trial is right
        game.global.chars[i].numCorrectQuestion++; 
      }
      game.global.chars[i].didFirstAnswer = true;  // To check the first answer to decide AI got current one
     
      if (game.global.chars[i].correct && !game.global.isRehash && !game.global.chars[i].isGetPoint) {
        // James: if you want to use decay point but not this phase 
        // tempPoints = game.global.gameSettings.pointValue - Math.floor(game.global.gameSettings.pointValue * game.global.gameSettings.pointDecay) * (game.global.chars[i].timesAnswered); 
        // if(tempPoints < 0) tempPoints = 0;
        tempPoints = game.global.gameSettings.pointValue * ((game.global.chars[i].timesAnswered + 1  == 1) ? 1 : (game.global.chars[i].timesAnswered + 1 == 2) ? 0.67 : (game.global.chars[i].timesAnswered + 1  == 3) ? 0.33: 0);
        game.global.chars[i].score += tempPoints // James: AI can get less point from 2nd trial at KC, BM show
      }
    }
  },
  createTimer: function () { //KEEP: i believe this creates the timer in a bar fashion
    this.timeLabel = game.add.bitmapText(0,
      0,
      '8bitoperator',
      game.global.gameSettings.timer,
      11 * dpr);
    this.timeLabel.y = Math.floor(game.global.questionBtn.y - (this.timeLabel.height * 2.5));
    this.timeLabel.centerX = Math.floor(game.global.questionBtn.x + game.global.questionBtn.bubblewidth / 2);
    this.timeLabel.tint = 0x000000;
    this.gfx = game.add.graphics(game.world.x - 1000, game.world.y - 1000);
    this.gfx.lineStyle(1, 0x000000, 1);
    this.gfx.beginFill(0x02C487, 1);
    this.gfx.drawRoundedRect(game.global.questionBtn.x, game.global.questionBtn.y, game.global.questionBtn.bubblewidth, 8 * dpr, 5);
    this.timerBar = game.add.sprite(game.global.questionBtn.x, game.global.questionBtn.y, this.gfx.generateTexture());
    this.timerBar.width = game.global.questionBtn.bubblewidth;
    this.timerBar.centerX = Math.floor(game.global.questionBtn.x + game.global.questionBtn.bubblewidth / 2);
    this.timerBar.centerY = game.global.questionBtn.y;

    game.global.questionUI.add(this.timeLabel);
    game.global.questionUI.add(this.timerBar);
  },

  /*updateTimer : function(){//KEEP: i believe updates the timer bar
    if(this.timerOn){
      var currentTime = new Date();
      var timeDiff = this.startTime.getTime() - currentTime.getTime();
      //time elapsed in seconds
      this.timeElapsed = Math.abs(timeDiff / 1000);
      this.timeRemaining = this.totalTime - this.timeElapsed;
      this.minutes = Math.floor(this.timeRemaining/60);
      this.seconds = Math.floor(this.timeRemaining) - (60 * this.minutes);
      // display minutes, add 0 if under 10
      var result = (this.minutes < 10) ? "0" + this.minutes : this.minutes;
      // add seconds
      result += (this.seconds < 10) ? ":0" + this.seconds : ":" + this.seconds;
      console.log(result);
      // update text; use 'result' if you want minutes:seconds
      this.timeLabel.text = this.seconds;
      this.timeLabel.centerX = Math.floor(game.global.bubble.x + game.global.bubble.bubblewidth/2);
      this.timeLabel.y = Math.floor(game.global.bubble.y - (this.timeLabel.height*2.5));

      this.timerBar.width = game.global.bubble.bubblewidth - 
      game.global.mapNum(this.timeElapsed, 0, this.totalTime, 0, game.global.bubble.bubblewidth);
      this.timerBar.centerX = Math.floor(game.global.bubble.x + game.global.bubble.bubblewidth/2);
      this.timerBar.centerY = game.global.bubble.y;
    }
  },*/
 
};
 
