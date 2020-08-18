//copy original playState and then modify it to create the state for Slide Cards
var playStateSC = Object.create(playState);


playStateSC.timesAnswered = 0;

/* 
* MADE BY: Walker Jones
* PURPOSE: Keeps question and answer text in the centre of their cards
* NOTES: overrides update in play-regularQuestions.js
*/
playStateSC.update = function(){ 
  questionCard.x = Math.floor(sprite.x + sprite.width / 2);
  questionCard.y = Math.floor(sprite.y + sprite.height / 2);
  answerCard.x = Math.floor(sprite2.x + sprite2.width / 2);
  answerCard.y = Math.floor(sprite2.y + sprite2.height / 2); 
};

/* 
* MADE BY: Walker Jones
* PURPOSE: adds question and answer in "slide card" format
* NOTES: overrides addQuestion in play-regularQuestions.js
*/
playStateSC.addQuestion = function(){
  // If not question is saved, load and save a new question. questions are saved in case
  // stop or settings menu is opened, thus a new question will not override the displayed
  // one when coming back from a menu - Walker

  // game.global.answersShown = false;   // James: this control to show AI's answer one time at each question 2/2
  
  var question = game.global.questions.shift();
  // reset values to defaults
  game.global.currentQuestion = question;
  game.state.getCurrentState().pointValue = game.global.gameSettings.pointValue;
  /*var comment = game.global.currentQuestion.comment;
  game.state.getCurrentState().pointValue = game.global.gameSettings.pointValue;
  if (comment == "" || comment == null || comment == undefined){
    comment = "No comment for this question";
  }*/
  console.log("single question: " + question);
  //Create a button for each choice, and put some data into it in case we need it
  var i = 0;
  //array to store available letter choices for ai to choose from for this question
  var availChoices = [];

  //I believe most of this is useless in slide cards
  console.log('question retrieved');
  var shuffChoices = [];
  var answerText = '';
  var question = game.global.currentQuestion;

  for (var c in question.choices) {
    availChoices[i] = c;
    shuffChoices[i] = question.choices[c];
    console.log(question.choices[c]);
    if(c == question.answer[0]){
      answerText = question.choices[c];

      console.log("answer is " + answerText);
      //game.global.answerText = answerText;
    }
    i++
  }
  //load images to place sprites properly in game world
  var TmpImg = game.cache.getImage('yellow');
  

  // set up for answer text ('yellow' is an asset and can be switched out as needed)
  sprite2 = game.add.sprite(game.world.centerX - TmpImg.width/2.0,game.world.centerY - TmpImg.height/2.0 - 100, 'yellow');
  game.global.questionUI.add(sprite2);

  var style2 = { font: "16px Arial", fill: "#000000", wordWrap: true, wordWrapWidth: sprite2.width - 50, align: "center" };
  answerCard = game.add.text(game.world.centerX - TmpImg.width/2.0, 
    game.world.centerY - TmpImg.height/2.0 - 100, 
    answerText, 
    style2);
  answerCard.anchor.set(0.5);
  game.global.questionUI.add(answerCard);
  game.global.answerCard = answerCard;
  game.global.sprite2 = sprite2;
  
  //set up for question card text ('yellow' is an asset and can be switched out as needed)
  
  sprite = game.add.sprite(game.world.centerX - TmpImg.width/2.0,game.world.centerY - TmpImg.height/2.0 - 100, 'yellow');
  sprite.inputEnabled = true;
  sprite.input.enableDrag();
  sprite.input.allowVerticalDrag = false;
  game.global.questionUI.add(sprite);
  
  var style = { font: "16px Arial", fill: "#000000", wordWrap: true, wordWrapWidth: sprite.width - 50, align: "center" };

  console.log(question.question);
  questionCard = game.add.text(game.world.centerX - TmpImg.width/2.0,game.world.centerY - TmpImg.height/2.0 - 100, question.question, style);
  questionCard.anchor.set(0.5);
  game.global.questionUI.add(questionCard);
  game.global.questionCard = questionCard;
  game.global.sprite = sprite;

  //animation 
  this.enterSound.play();

  //timer - the phaser way
  game.global.timer = game.time.create(false);
  game.global.timer.add(50, game.state.getCurrentState().showChoices, this);
  game.global.timer.start();
  //game.state.getCurrentState().showChoices;
};

/* 
* MADE BY: Walker Jones
* PURPOSE: creates and displays the 7 options for slide cards
* NOTES: overrides showChoices in play-regularQuestions.js
*/
playStateSC.showChoices = function(){
  var TmpImg = game.cache.getImage('yellow'); //  used to get the middle height of the screen
  var prevHeights = 300 * dpr;   //Fix this later. NOTE: this comment is not mine - Walker
  //console.log('create rate buttons')
  // NOTE: the x and y values were taken from previous code, no idea how they were determined
  // i'm guessing trial and error - Walker
 
var buttonTextValues = [
    {text: "Had No Idea", X: -110, Y: 210}, 
    {text: "Got It Wrong", X: -110, Y: 260}, 
    {text: "Partially Right", X: -115, Y: 310}, 
    {text: "Lucky Guess", X: 110, Y: 210}, 
    {text: "Got It Right", X: 105, Y: 260}, 
    {text: "Too Easy", X: 93, Y: 310}, 
    {text: "Doesn't Work", X: -110, Y: 360},
  ];

  game.global.choiceBubbles = game.add.group();
  for (var i = 0; i < buttonTextValues.length; i++) {
    //TODO: shift X and Y calculations from here to buttonTextValues above if possible
    var choice = game.world.add(new game.global.SpeechBubble(game, 
      game.world.width + 1000, 
      game.height, 
      game.width, 
      buttonTextValues[i].text, 
      false, 
      true, 
      game.state.getCurrentState().submitAnswers));
   
  
    choice.y = Math.floor(game.world.centerY - TmpImg.height/2.0 + buttonTextValues[i].Y); 
    choice.data = buttonTextValues[i].text; 
    // animate button entrance
    var bTween = game.add.tween(choice).to({x: Math.floor(game.world.centerX - choice.bubblewidth/2 + buttonTextValues[i].X)}, 500, Phaser.Easing.Default, true, 1000);
    //bTween.start();
    game.global.choiceBubbles.add(choice);
  }
  game.global.questionUI.add(game.global.choiceBubbles);
}

playStateSC.createStopBtn = function(){
  var stopBtn = game.world.add(new game.global.SpeechBubble(game, 
    1725, 
    685, 
    100, 
    "Stop", 
    false, 
    true, 
    this.stopBtnClick));

    var bTween = game.add.tween(stopBtn).to({x: Math.floor(game.world.centerX + 40)}, 500, Phaser.Easing.Default, true, 1000);
}

playStateSC.submitAnswers = function(){
  game.state.getCurrentState().disableAnswers();
  // dim selected button
  this.alpha = 0.25;
  
  var buttonTextValues = [
    {text: "Had No Idea", X: -110, Y: 30}, 
    {text: "Got It Wrong", X: -110, Y: 80}, 
    {text: "Partially Right", X: -115, Y: 130}, 
    {text: "Lucky Guess", X: 110, Y: 30}, 
    {text: "Got It Right", X: 105, Y: 80}, 
    {text: "Too Easy", X: 93, Y: 130}, 
    {text: "Doesn't Work", X: -110, Y: 180},
  ];

  //if the user wasn't correct, reinsert the question to a spot in the queue based on how wrong they were
  switch (this.data) {
    case "Had No Idea":
      game.global.questions.splice(2, 0, game.global.currentQuestion);
      break;
    case "Got It Wrong":
      game.global.questions.splice(5, 0, game.global.currentQuestion);
      break;
    case "Partially Right":
      game.global.questions.splice(8, 0, game.global.currentQuestion);
      break;
    case "Lucky Guess":
      game.global.questions.splice(12, 0, game.global.currentQuestion);
      break;
    case "Got It Right":
      game.global.questions.splice(17, 0, game.global.currentQuestion);
      break;
  }

  //game.global.timer.add(2500, game.state.getCurrentState().animateOut, this, false);
  //add points and update text
  game.global.totalStats.totalScore += game.state.getCurrentState().pointValue;
  game.state.getCurrentState().updatePointText();

  // set up next question
  game.global.timer.add(2500, game.state.getCurrentState().setupNewQuestion, game.state.getCurrentState()); 
};