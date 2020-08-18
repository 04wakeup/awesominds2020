var task_id = 0;
var rules = `rule template`;

var preGameState = {
  instructLines: [ // TODO: remove this
    `How to play:\n
    A question will appear.\n
    Click/tap the question to make the answer choices appear.\n
    Choose the right answer as quickly as possible.\n
    The faster you are, the more points you earn.\n \n
    Each round has 10 questions.\n \n
    Goal:\n
    To win the round, score more points than your opponents.\n
    The winner of each round earns a jewel for their crown.\n
    Be the first to complete your crown to win the game.`
  ],

  init: function (data) {
    console.log("pregame, data: ", data);
    // Setup variables 
    game.global.questions = []; // contains a certain number of questions for playing the game. 
    // should contain all questions for non-gameShow modes or the number of questions in a round for gameShow. 
    this.pregameUI = game.add.group();
    if (data) {
      console.log("first pregame") 
      game.global.hardQuestions = []; // contains all questions there were difficult for the player
      game.global.taskName = data.name;
      game.global.taskDesc = data.desc;
      // James: define one time to use for rounds
      game.global.questionBank = []; // contains ALL questions for the course chapter.   

      // Setup stats
      game.global.totalStats = {
        numRight: 0,
        numWrong: 0,
        totalScore: 0,
        roundScore: 0,
        winStreak: 0
      };

      //TODO: change stopAtZero to minPoints: Int
      game.global.gameSettings = {
        structureType: "play", //TODO change to a new file/state
        pointValue: parseInt(data.point_value), //The max amount of points you can earn from a question
        pointDecay: 2/5, //The percentage of points removes from the total on an incorrect answer
        attempts: 6, //the number of answers a question can have. 6 is the max.
        choicesAtOnce: 1, //How many choices a user can choose before hitting done. attempts should probably be 1 if this is greater than 1.
        timer: 0, //the timer for answering a question, 0 means no timer.
        stopAtZero: true, //whether or not the point decay should stop at 0 or go into negatives
        failOnOutOfAttempts: false, //whether or not the game should end if answer is wrong
        gameShow: false, //whether or not it is a game show. mostly for debugging.
        saveHardQuestions: true, // whether or not hard questions should be saved for review TODO: remove this and only use saveHardQuestionMethods
        saveHardQuestionMethods: ["failure", "timeUp", "incorrectAnswers"] // the difrerent ways a question
        // can be saved. Failure: if the correct answer is not chosen in the number of attempts given
        // timeUp: For timed questions, if the player runs out of time.
        // incorrectAnswers: if the correct answer is chosen but the player got it wrong TWICE 
        // (hardcoded in tempPlay) before getting it right.
      };
      game.global.timesAnswered = 0;
      /*game.global.gameShowSettings = {
        maxLives: 3,
        lives: 3,
        inARow: 5,
        inARowPoints: 15,
        numOfRounds: 3,
        currentRound: 1
      };*/

      //TODO: Ive changed the order in the database so the order here 
      //and task_ids will need to be changed when database gets updated

      if (data.name == "Rate Questions") {
        task_id = 1;
        rules = "Rate Questions \n\n" + 
        "You will be shown a question then possible answers.\n" +
        "Click/tap the ‘Show answer’ button to reveal the \n" + 
        "correct answer.\n" + 
        "Click on the correct option.\n" + 
        "Then rate how difficult you think the question is:\n" +
        "Easy Medium or Hard.\n" + 
        "Touch/click ‘Begin’ to begin";
        game.global.gameSettings.structureType = "rateQuestions";
      } else if (data.name == "Slide Cards") {
        task_id = 2;
        rules = "Slide Cards \n\n" + 
        "You will be shown a question on a card.\n" + 
        "Think of the correct answer.\n" + 
        "To reveal the answer, grab the card and slide it to \n" + 
        "the side. If you want to give yourself a hint,\n" + 
        "slide the card only part way to reveal only part of \n" + 
        "the answer. Indicate how well you did by selecting one \n" + 
        "of the buttons underneath the card.\n" +
        "Touch/click ‘Begin’ to begin";
        game.global.gameSettings.structureType = "slideCards";
      } else if (data.name == "Just Drills") {
        task_id = 3;
        rules = "Just Drills \n\n " + 
        "You will be shown a question then possible answers.\n" + 
        "Click/tap the correct answer to earn points.\n" + 
        "Keep selecting options until you get the right answer.\n" + 
        "Touch/click ‘Begin’ to begin";
        game.global.gameSettings.structureType = "justDrills";
      } else if (data.name == "Game Show") { 
        task_id = 4;
        rules = "GS template, to be changed based on challenge";
        game.global.gameSettings.gameShow = true;
      } else {
        alert("error!, unknown game detected, how did you do this?");
        console.log("error!, unknown game detected");
      }
      // TODO: make each ajax call a function for better readability and modularity

      // get a chapter of questions from the database and load them into the question bank
      $.ajax({
        type: 'POST',
        url: 'db-getquestion.php',
        data: { course_id: game.global.selectedCourse, chapter_id: game.global.selectedChapter },
        dataType: 'json',
        success: function(data){
          console.log(data);
          game.global.questions = [];
          game.global.questionBank = [];
          data = game.global.shuffleArray(data);
          for (var i = 0; i < data.length; i++) {
            game.global.questionBank[i] = JSON.parse(data[i].question); // TODO: add the additional fields in the question table, maybe can just delete .question          
          }
          // shuffle the questions
          //game.global.questionBank = game.global.shuffleArray(game.global.questionBank);
          //save a copy of the bank in case we run out for a round
          game.global.questionBankCopy = game.global.questionBank.slice(); 
          //if it's not a game show, load all questions from bank into active questions array
          if (!game.global.gameSettings.gameShow) {
            game.global.questions = game.global.questionBank;
            console.log(game.global.questions);
          }
          //once the questions are successfully loaded, move to the play state
          //game.state.getCurrentState().pregameUI.destroy();
          game.global.isRehash = false;
          game.global.rehashQuestions = [];
        }
      });

      if (game.global.gameSettings.gameShow) {
        $.ajax({
          type: 'POST',
          url: 'db-getGameShowData.php',
          data: { course_id: game.global.selectedCourse, chapter_id: game.global.selectedChapter },
          dataType: 'json',
          success: function (data) { //
            //console.log(data);
            game.global.gameShowSettings = { //the following is sample data, it will be replaced later
              maxLives: parseInt(data[0].lives),
              lives: parseInt(data[0].lives),
              inARow: parseInt(data[0].in_a_row_number),
              inARowPoints: parseInt(data[0].in_a_row_point),
              numOfRounds: parseInt(data[0].num_of_rounds),
              currentRound: 0
            };

            $.ajax({
              type: 'POST',
              url: 'db-getRoundData.php',
              data: { course_id: game.global.selectedCourse, chapter_id: game.global.selectedChapter },
              dataType: 'json',
              success: function (data) {
                game.global.rounds = data;
                game.state.getCurrentState().configureGameShowRound();
              }
            });
          }
        });
      }

      //var prevHeights = game.global.jinnySpeech.y + game.global.jinnySpeech.bubbleheight + 5; 
      //for (var i = 0; i < instructLines.length; i++) {
      //prevHeights += t.height;
      //game.state.getCurrentState().statsUI.add(t);
      //}
    } else {
      // if it is not the first time arriving at this page
      // this should only happen on game show rounds or upon reviewing questions that were hard.
      console.log("not first pregame");
      if (game.global.gameSettings.gameShow) {
        this.configureGameShowRound();
      } else if (game.global.gameSettings.saveHardQuestions) {
        game.global.gameSettings = {
          structureType: "play", //TODO: check with Marty if mode should change to default or stay
          // as current (e.g. slide cards remains slide cards or changes to regularQuestions)
          pointValue: 0,
          pointDecay: 0,
          attempts: 6,
          choicesAtOnce: 1,
          timer: 0,
          stopAtZero: true,
          failOnOutOfAttempts: false,
          gameShow: false,
          saveHardQuestions: false,
          saveHardQuestionMethods: []
        };

        console.log(game.global.hardQuestions);
        // insert hardQuestions into questions array for review
        game.global.questions = game.global.hardQuestions.slice();
        game.global.hardQuestions = [];

        rules = "review round template";
      }
    }
  },
  create: function () {
    console.log("state: pregame");
    game.global.question = null;
    // James: show Course and Chapter 
    gameShowUIState.course_chapter_text();

    if (!game.global.gameSettings.gameShow) {
      var pregameBG = game.add.graphics(0, 0);
      pregameBG.lineStyle(2, 0x000000, 1);
      pregameBG.beginFill(0x078EB7, 1);
      pregameBG.drawRoundedRect(game.world.x + 10, game.global.logoText.bottom, game.world.width - 20, game.world.height - game.global.logoText.height - 10, 10);
      this.pregameUI.add(pregameBG);

      //game.global.pregameText = game.add.text(game.world.centerX, Math.floor(game.global.logoText.bottom), 'Stopped', game.global.whiteFont);
      var pregameText = game.add.text(game.world.centerX, Math.floor(game.global.logoText.bottom), rules, game.global.whiteFont);
      // pregameText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
      pregameText.padding.x = 40;
      pregameText.x = Math.floor(pregameText.x - (pregameText.width / 2));
      this.pregameUI.add(pregameText);
      //TODO: setup intro card
      //var t = game.add.text(0, 50, rules, game.global.whiteFont);
      /*t.x -= t.width/2;
      t.y += t.height;
      t.x = Math.round(t.x);
      t.y = Math.round(t.y);
      t.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5);
      t.padding.x = 5;*/
      //this.pregameUI.add(t);
    } else {
    }

    // James: Mobile reposition(dpr > 1)
    var playBtn = game.world.add(new game.global.SpeechBubble(game,
      game.world.centerX,
      (dpr == 1) ? game.world.height * .7: game.world.height - 100 * adjust_dpr,
      100,
      "Begin",
      false,
      true,
      this.playFunction));
    playBtn.x -= Math.floor(playBtn.bubblewidth / 2);
    playBtn.y -= Math.floor(playBtn.bubbleheight + 50);
    //skip.x = Math.floor(skip.x - (skip.bubblewidth/2));
    //skip.y = Math.floor(bubbles[bubbles.length-1].y + bubbles[bubbles.length-1].bubbleheight + (10*dpr));
    this.pregameUI.add(playBtn);
    // console.log(this.pregameUI);
    // console.log(this.pregameUI.children);

    //preamble text should appear before this screen
    /*game.global.pregameText = game.add.text(game.world.centerX, game.world.centerY + 100, game.global.preamble,game.global.whiteFont);
    game.global.pregameText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5);
    game.global.pregameText.padding.x = 5;
    game.global.pregameText.x = Math.floor(game.global.pregameText.x - (game.global.pregameText.width/2));
    this.pregameUI.add(game.global.pregameText);*/
  },
  /*update: function(){ //keeps names and crowns positioned near their avatars TODO: keep avatars at bottom of screen
    for (var i = 0; i < game.global.chars.length; i++) {
      game.global.chars[i].name.x = Math.floor(game.global.chars[i].sprite.right + (10*dpr));
      game.global.chars[i].name.y = Math.floor(game.global.chars[i].sprite.centerY + (10*dpr));
      //game.global.chars[i].crown.centerX = Math.floor(game.global.chars[i].sprite.centerX);
    }
  },*/
  /*
  * MADE BY: Walker Jones
  * PURPOSE: Change the gameSettings for a new round in game show and retrieve the proper amount 
  * of questions for that round
  */
  configureGameShowRound: function () {
    //TODO: find a better way to do this
    if (game.global.gameShowSettings.currentRound > 0) {
      for (var i = 0; i < game.global.rounds.length; i++) {
        if (game.global.rounds[i].round_id == game.global.gameShowSettings.currentRound) {
          //Take the needed number of questions for the round from the question bank
          // James: copy it to keep
          //var copyOfQuestions = game.global.questionBank.slice(); 

          //ensure we get enough questions for the round, even if we have reuse old questions.
          while (game.global.questions.length < game.global.rounds[i].questions) {
            game.global.questions = game.global.questions.concat(game.global.questionBank.splice(0, game.global.rounds[i].questions - game.global.questions.length));
            //if there are no more questions in the bank, repopulate it and shuffle
            if (game.global.questionBank.length == 0) {
              game.global.questionBank = game.global.questionBankCopy.slice();
              game.global.questionBank = game.global.shuffleArray(game.global.questionBank);
            }
          }

          // James: I need the number of total questins for each round, let me know any variable I can use of I'll create
          game.global.roundNumOfQuestions = game.global.questions.length;
          // set up the pregame game show ui
          gameShowUIState.course_chapter_text();

          // Set semi-consistent config settings
          game.global.gameSettings.structureType = "play";
          game.global.gameSettings.pointValue = parseInt(game.global.rounds[i].max_point);
          game.global.gameSettings.choicesAtOnce = 1;
          game.global.gameSettings.timer = 0;
          game.global.gameSettings.stopAtZero = true;
          game.global.gameSettings.failOnOutOfAttempts = false; // NOTE: dont know when this would be true
          //game.global.gameSettings.gameShow = true; //should already be true
          game.global.gameSettings.currentChallengeName = game.global.rounds[i].challenge_name; // James: need challenge name at the beginning of each round to set the UI

          rules = "This is round " + game.global.gameShowSettings.currentRound + " of " +
            game.global.gameShowSettings.numOfRounds + ". It has " + game.global.rounds[i].questions + " questions.\n" +
            "Your goal is to: ";


          switch(game.global.rounds[i].goal) {
            case "Points":
              rules += "get more than " +
              game.global.rounds[i].point_goal + " points.\n";
              break;
            case "Beat opponent":
              rules += "beat your opponents score.\n";
              break;
            case "Complete round":
              rules += "simply complete this round\n";
              break;
            default:
              rules += "error, unknown goal\n";
              break;
          }

          //TODO/NOTE: the descriptions of challenges are different depending on where you view them
          //so its hard to have one column in the database for all descriptions
          rules += "The challenge this round is called " + game.global.rounds[i].challenge_name + ".\n";
            //+ game.global.rounds[i].challenge_description;

          // set challenge dependant config settings and add challenge instructions to rules
          switch (game.global.rounds[i].challenge_name) {
            case "Keep Choosing":
              rules += "Keep choosing until the right answer is selected\n";
              game.global.taskDesc = "Keep choosing until the right answer is selected\n";
              game.global.gameSettings.pointDecay = 1 / 3;
              game.global.gameSettings.attempts = 6;
              break;
            case "One Crack":
              rules += "You only get one choice\n";
              game.global.taskDesc = "You only get one choice\n";
              game.global.gameSettings.pointDecay = 1;
              game.global.gameSettings.attempts = 1;
              break;
            case "One Crack Time Bonus":
              rules += "The faster you respond, the more points you earn\n";
              game.global.taskDesc = "The faster you respond,\n the more points you earn\n";
              game.global.gameSettings.pointDecay = 1;
              game.global.gameSettings.attempts = 1;
              game.global.gameSettings.timer = 15;
              break;
            case "Choose 1, 2, or 3":
              rules += "Choose up to 3 of the options displayed. The fewer options you choose the more potential points\n";
              game.global.taskDesc = "Choose up to 3 of the options displayed\n";
              game.global.gameSettings.pointDecay = 2 / 5;
              game.global.gameSettings.attempts = 1;
              game.global.gameSettings.choicesAtOnce = 3;
              break;
            case "Big Money":
              rules += "Keep choosing until the right answer is selected, game  over on the 4th attempt\n";
              game.global.taskDesc = "Keep choosing until the right answer is selected,\n game  over on the 4th attempt\n";
              game.global.gameSettings.pointDecay = 1;
              game.global.gameSettings.attempts = 3;
              game.global.gameSettings.choicesAtOnce = 1;
              game.global.gameSettings.stopAtZero = false;
              game.global.gameSettings.failOnOutOfAttempts = true;
              break;
            case "Mystery Multiplier":
              rules += "Choose one of three multipliers after you answer the question to gain or lose points\n";
              game.global.taskDesc = "Choose one of three multipliers after\n you answer the question to gain or lose points\n";
              game.global.gameSettings.structureType = "mysteryMultiplier";
              game.global.gameSettings.attempts = 1;
              game.global.gameSettings.pointDecay = 1;
              break;
            deafult:
              rules = "error";
              console.log("error");
          }

          rules += "\nyou have " + game.global.gameShowSettings.lives + " live(s)\n";

          // get the host to speak the rules
          gameShowUIState.course_chapter_text(); // James: show Course and Chapter  
          gameShowUIState.drawHost(game.global.gameSettings.currentChallengeName); // draw a host for this round 
          gameShowUIState.drawInitialAvartars('N');
          gameShowUIState.hostTweenSpeech(rules);  // rules use tween speech for better UI
        }
      }
    } else {
      //This is the initial gameshow screen, telling info about the entire game, not just one round.
      rules = "Welcome to the game show.\n" +
        "There are " + game.global.gameShowSettings.numOfRounds + " round(s).\n" +
        "Each round is a different challenge.\n" +
        "Explanations appear at the beginning of each round,\n" +
        "describing how you will play that round.\n" +
        "You have " + game.global.gameShowSettings.lives + " lives. Each time you lose a round, you lose a life.\n" +
        "Your goal is to complete each round and win the game.\n" +
        "These are your opponents.";
      gameShowUIState.course_chapter_text();
      gameShowUIState.drawInitialAvartars('Y');
      gameShowUIState.drawHost("One Crack");
      gameShowUIState.hostTweenSpeech(rules);
      game.global.gameSettings.structureType = "pregame";
      game.global.gameShowSettings.currentRound++;
    }
  },
  playFunction: function () { 
    console.log("play button pressed ", game.global.gameSettings.structureType);
    /*game.global.totalStats = {
      numRight: 0,
      numWrong: 0,
      totalScore: 0,
      roundScore: 0,
      winStreak: 0
    };*/

    game.state.start(game.global.gameSettings.structureType, true, false); //directs to the specific structure needed
    // get a chapter of questions from the database and load them into the questions array
  }
};
