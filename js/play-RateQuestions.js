//copy original playState and then modify it to create the state for Rate Questions
var playStateRQ = Object.create(playState);

playStateRQ.timesAnswered = 0;

/*
* MADE BY: Walker Jones
* PURPOSE: the functionality when the question button is clicked, shows the choices, and then shows
* the right answer a second later
* NOTE: overrides questionBtnClick in play.js
*/
playStateRQ.questionBtnClick = function() {
  this.inputEnabled = false;
  game.state.getCurrentState().showChoices();
  game.state.getCurrentState().disableAnswers();
};

/*
* MADE BY: Walker Jones
* PURPOSE: Enables the button of the correct answer
* TODO/NOTE: upon re-enabling the button, hovering over it no longer changes the mouse icon to the hand
*/
playStateRQ.enableCorrectAnswer = function() {
  game.global.choiceBubbles.forEach(function (item) {
    //if the button is an incorrect answer, make it unclickable
    if (item.data.correct) {
      item.inputEnabled = true;
    }
  });
};

/*
* MADE BY: Walker Jones
* PURPOSE: Animates a check mark next to the correct answer.
* NOTE: overrides showCorrectAnswer in play.js, differences are:
* does not grey out the correct answer.
* does not disable input
*/
playStateRQ.showCorrectAnswer = function () {
  game.global.choiceBubbles.forEach(function (item) {
    if (item.data.correct) {
      // animate a check symbol for correct answer
      var check = game.add.sprite(game.world.x - game.world.width, item.centerY, 'check');
      check.height = check.width = game.global.borderFrameSize * 3;
      check.anchor.setTo(0.5, 0.5);
      game.global.questionUI.add(check);
      game.add.tween(check).to({ x: Math.floor(item.x - check.width / 3), y: Math.floor(item.y + item.bubbleheight / 2) }, 300, Phaser.Easing.Default, true, 0);
    }
  });
};

/*
* MADE BY: Walker Jones
* PURPOSE: since no submit button is needed, 
* makes a button that shows the answer when clicked instead
*/
playStateRQ.createSubmitButton = function() {
  game.state.getCurrentState().createShowAnswerButton();
};

/*
* MADE BY: Walker Jones
* PURPOSE: intentionally left empty so no submit button will be created
* NOTES: There are definitely better ways of doing this, not enough time to implement them.
*/
playStateRQ.createShowAnswerButton = function() {
  var bShowAnswer = game.world.add(new game.global.SpeechBubble(game,
    game.world.width + 1000,
    game.state.getCurrentState().prevHeights,
    game.width,
    "Show Answer",
    false,
    true,
    game.state.getCurrentState().showAnswerButtonClick));
  game.add.tween(bShowAnswer).to({ x: Math.floor(game.world.centerX - bShowAnswer.bubblewidth / 2) }, 500, Phaser.Easing.Default, true, 250);

  game.global.questionUI.add(bShowAnswer);
  game.global.questionShown = true;
};

/*
* MADE BY: Walker Jones
* PURPOSE: When the "Show Answer" button is clicked, show the correct answer and enabled it as a button
*/
playStateRQ.showAnswerButtonClick = function() {
  game.state.getCurrentState().setupCommentButton();
  game.state.getCurrentState().showCorrectAnswer();
  game.state.getCurrentState().enableCorrectAnswer();
}

/*
* MADE BY: Walker Jones
* PURPOSE: shows the rate question buttons
* NOTES: Overrides selectAnswer in play.js.
* SelectAnswer is a bad name, since the only option that can be selected is the correct answer.
*/
playStateRQ.selectAnswer = function () { 
  this.inputEnabled = false;
  game.state.getCurrentState().createRateButtons();
};

/*
* MADE BY: Walker Jones
* PURPOSE: creates and shows the 3 rating buttons, adds them to questionUI
*/
playStateRQ.createRateButtons = function() {
  game.global.choiceBubbles = game.add.group();
  //Create easy and medium button, as they serve the same purpose
  var prevHeights = game.global.prevHeights;

  //TODO: Convert to assoc array and for loop
  
  // create Easy
  var bEasy = game.world.add(new game.global.SpeechBubble(game, 
    game.world.width + 1000, 
    game.height * .7, 
    game.width, 
    "Easy", 
    false, 
    true, 
    game.state.getCurrentState().rateButtonClicked));
  bEasy.data = 0;
  game.global.choiceBubbles.add(bEasy);
  // animate button entrance
  var bTween = game.add.tween(bEasy).to({x: Math.floor(game.world.centerX - bEasy.bubblewidth/2 - 150)}, 500, Phaser.Easing.Default, true, 250 * 4);

  // create Medium
  var bMedium = game.world.add(new game.global.SpeechBubble(game, 
    game.world.width + 1000, 
    game.height * .7, 
    game.width, 
    "Medium", 
    false, 
    true, 
    game.state.getCurrentState().rateButtonClicked));
  bMedium.data = 1;
  game.global.choiceBubbles.add(bMedium);
  // animate button entrance
  var bTween = game.add.tween(bMedium).to({x: Math.floor(game.world.centerX - bMedium.bubblewidth/2)}, 500, Phaser.Easing.Default, true, 250 * 4);

  // create Hard
  var bHard = game.world.add(new game.global.SpeechBubble(game, 
    game.world.width + 1000,
    game.height * .7, 
    game.width, 
    "Hard", 
    false, 
    true, 
    game.state.getCurrentState().rateButtonClicked));
  bHard.data = 2;
  game.global.choiceBubbles.add(bHard);
  // animate button entrance
  var bTween = game.add.tween(bHard).to({x: Math.floor(game.world.centerX - bHard.bubblewidth/2 + 150)}, 500, Phaser.Easing.Default, true, 250 * 4);

  game.global.questionUI.add(game.global.choiceBubbles);
};

playStateRQ.rateButtonClicked = function() {

  //add points and update text
  game.global.totalStats.totalScore += game.state.getCurrentState().pointValue;
  game.state.getCurrentState().updatePointText();

  // 2 is the id for the "hard" button
  if (this.data == 2) { 
    game.global.hardQuestions.push(game.global.currentQuestion);
    console.log(game.global.hardQuestions);
  }
  // set question to null so a new one will be pulled

  game.global.choiceBubbles.forEach( function(item){ item.inputEnabled = false; } );
  /*game.global.timer.add(500, game.state.getCurrentState().animateOut, this);
  game.global.timer.add(1500, game.state.getCurrentState().setupNewQuestion, this);
  game.global.questionsAnswered++;*/
  game.state.getCurrentState().setupNewQuestion(); //TODO: put this in a timer
};