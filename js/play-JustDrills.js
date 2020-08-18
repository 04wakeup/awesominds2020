/*
*Creator: Adam Lowe
*Purpose:Makeing the graph that is shown in the "Just Drills" Task
*        and any other functions that apply to "Just Drills"
* Notes: graphLengthIncrease and graphXLength the values get assigned here because
*        When you answer question from game.global.questions they get poped off the
         array so to get the size of the array at the start they get assigned here
*/

//copy original playState and then modify it to create the state for Rate Questions
var playStateJD = Object.create(playState);
var graphX;
var graphY;
var lineArray

playStateJD.timesAnswered = 0;

playStateJD.init = function(){
  graphX = game.world.centerX * 2;
  graphY = game.world.centerY + 25;
  graphLengthIncrease = game.global.questions.length
  console.log(graphX)
  lineArray = [];
  lineArray.push(graphX);
  lineArray.push(graphY);
  graphXlength = 600 / game.global.questions.length;
}


playStateJD.setupNextButton = function() {
  var nextBtn = game.world.add(new game.global.SpeechBubble(game,
    game.width * .70,
    game.state.getCurrentState().prevHeights,
    game.width,
    "Next",
    false,
    true,
    game.state.getCurrentState().setUpGraph));
  game.global.questionUI.add(nextBtn);

  nextBtn.x -= Math.floor(nextBtn.bubblewidth / 2);
};

playStateJD.setupNextQuestionButton = function(){
  console.log("nextQuestion")
  var nextQuestionsBtn = game.world.add(new game.global.SpeechBubble(game,
    game.width * .70,
    game.height,
    game.width,
    "Next Question",
    false,
    true,
    game.state.getCurrentState().setupNewQuestion));
    game.global.questionUI.add(nextQuestionsBtn);

    nextQuestionsBtn.x -= Math.floor(nextQuestionsBtn.bubblewidth / 2);
    nextQuestionsBtn.y -= Math.floor(nextQuestionsBtn.bubbleheight + 10 * dpr);


};
  /*
  Creator: Adam Lowe
  Purpose: Shows a graph of how the students progress 
  */
 
  playStateJD.setUpGraph = function(){
    game.state.getCurrentState().removeQuestion();
    console.log("this actually got called");
    //graphics for the stats graph in between just drill rounds
    var textInput;
    var graphics = game.add.graphics(0, 0);

    //Makes the text sent to the front
    //Also Makes the "Awesome!"Bar
    textInput = game.add.text(game.world.centerX + 250,game.world.centerY - 175,"Awesome!", 0x000000);
    graphics.addChild(textInput);
    graphics.beginFill(0x00FF00,.5);
    graphics.lineStyle(5, 0xE6F9FF, 1);
    graphics.drawRect(game.world.centerX - 400,game.world.centerY - 200,600,75);
    

    //Orange and the "Good!" Bar
    textInput = game.add.text(game.world.centerX + 250,game.world.centerY - 100,"Good!", 0x000000);
    graphics.addChild(textInput);
    graphics.beginFill(0xffa501, .5);
    graphics.lineStyle(5, 0xE6F9FF, 1);
    graphics.drawRect(game.world.centerX - 400,game.world.centerY - 125,600,75);

    //Blue and the "OK!" Bar
    textInput = game.add.text(game.world.centerX + 250,game.world.centerY - 25,"OK!", 0x000000);
    graphics.addChild(textInput);
    graphics.beginFill(0x078EB7,.5);
    graphics.lineStyle(5, 0xE6F9FF, 1);
    graphics.drawRect(game.world.centerX - 400,game.world.centerY - 50,600,75);

    //Grey and "Not so Good" Bar
    textInput = game.add.text(game.world.centerX + 250,game.world.centerY + 50,"Not So Good", 0x000000);
    graphics.addChild(textInput);
    graphics.beginFill(0x808080,.5);
    graphics.lineStyle(5, 0xE6F9FF, 1);
    graphics.drawRect(game.world.centerX - 400,game.world.centerY + 25,600,75);

    //Pink and "Yuk!" Bar
    textInput = game.add.text(game.world.centerX + 250,game.world.centerY + 125,"Yuk!", 0x000000);
    graphics.addChild(textInput);
    graphics.beginFill(0xFFC0CB,.5);
    graphics.lineStyle(5, 0xE6F9FF, 1);
    graphics.drawRect(game.world.centerX - 400,game.world.centerY + 100,600,75);

    game.state.getCurrentState().updateGraph(game.state.getCurrentState().attempts);
    game.global.questionUI.add(textInput);
    game.global.questionUI.add(graphics);

    game.state.getCurrentState().setupNextQuestionButton();
    
  };
  /*
  Creator: Adam Lowe
  Purpose: updates the graph of the students progress depending on how many times it takes for them to get it correct
  */
 
  playStateJD.updateGraph = function(attempts){
    console.log(game.global.choiceSize);
    console.log(graphLengthIncrease)
    var maxY = game.world.centerY + 175;// min height
    var minY = game.world.centerY - 200;// max height
    var graphics = game.add.graphics(0, 0);
    console.log(graphX);
    console.log(graphY);
    console.log(graphXlength);
    // graphics.moveTo(graphX,graphY);  // James
    console.log("the remaining attempts are " + attempts);
    graphics.lineStyle(5, 0x000000, 1);
    // evaluates which grading system to use depending on how many choices there are
    if(game.global.choiceSize == 6){
      game.state.getCurrentState().evaluateGraphForSixChoices(attempts);
    }
    else if(game.global.choiceSize == 5){
      game.state.getCurrentState().evaluateGraphForFiveChoices(attempts);
    }
    else if(game.global.choiceSize == 4){
      game.state.getCurrentState().evaluateGraphForFourChoices(attempts);
    }
    else if(game.global.choiceSize == 3){
      game.state.getCurrentState().evaluateGraphForThreeChoices(attempts);
    }
    else if(game.global.choiceSize == 2){
      game.state.getCurrentState().evaluateGraphForTwoChoices(attempts);
    }
    //if it goes too high
    if(graphY < minY){
      graphY = minY
    }
    //if it goes too low
    else if(graphY > maxY){
      graphY = maxY
    } 
    
   //draws the line
    //graphics.lineTo(graphX + graphXlength,graphY);
    graphX += graphXlength;
    // graphics.moveTo(graphX,graphY);  // James
    
    graphics.moveTo((lineArray[0]-1),lineArray[1]);  //James
    lineArray.push(graphX);	
    lineArray.push(graphY);
    for (var i = 0; i < lineArray.length;i++){
     
      graphics.lineTo(lineArray[i],lineArray[i+1]);
      console.log(i);
      game.global.questionUI.add(graphics);
      game.add.tween(graphics).to({x: game.world.centerX - (game.world.width + 400)}, 350, Phaser.Easing.Default, true, 100);
      i++;
    }
    //game.global.questionUI.add(graphics);
    console.log(lineArray);
    //console.log(lineArray[0].currentPath.shape.points[0]);
  };

  playStateJD.evaluateGraphForSixChoices = function(attempts){
    //sharply goes up
    if (attempts == 5){
      graphY -= (150 / graphLengthIncrease) * 2
    }
    //stays flat
    else if(attempts == 4){
      //graphY -= (150 / graphLengthIncrease) used to go slightly up but changed per Marty's request
      graphY -= 0
    }
    //slightly goes down
    else if(attempts == 3){
      // graphY -= 0 //used to stay flat but changed per Marty's request
      graphY += (150 / graphLengthIncrease) 
    }
    //sharply goes down
    else if(attempts == 2){
      // graphY += (150 / graphLengthIncrease) used to slightly go down but changed per Mary's request
      graphY += (150 / graphLengthIncrease) * 2
    }
    //sharply goes down
    else if(attempts == 1){
      graphY += (150 / graphLengthIncrease) * 2
    }
    //ran out of attempts and got the question completely wrong
    else{
      graphY += 75
    }
  };

  playStateJD.evaluateGraphForFiveChoices = function(attempts){
    //sharply goes up
    if (attempts == 5){
      graphY -= (150 / graphLengthIncrease) * 2
    }
    //stays flat
    else if(attempts == 4){
      //graphY -= (150 / graphLengthIncrease) used to go slightly up but changed per Marty's request
      graphY -= 0
    }
    //stays flat
    else if(attempts == 3){
      // graphY -= 0 //used to stay flat but changed per Marty's request
      graphY += (150 / graphLengthIncrease) 
    }
    //sharply goes down
    else if(attempts == 2){
      // graphY += (150 / graphLengthIncrease) used to slightly go down but changed per Mary's request
      graphY += (150 / graphLengthIncrease) * 2
    }
    //sharply goes down
    else if(attempts == 1){
      graphY += (150 / graphLengthIncrease) * 2
    }
    else {
      graphY += 75
    }
  };

  playStateJD.evaluateGraphForFourChoices = function(attempts){
    //sharply goes up
    if (attempts == 5){
      graphY -= (150 / graphLengthIncrease) * 2
    }
    //stays flat
    else if(attempts == 4){
      //graphY -= (150 / graphLengthIncrease) used to go slightly up but changed per Marty's request
      graphY -= 0
    } 
    //slightly goes down
    else if(attempts == 3){
      graphY += (150 / graphLengthIncrease) 
    }
    //sharply goes down
    else if(attempts == 2){
      graphY += (150 / graphLengthIncrease) * 2
    }
    else {
      graphY += 75
    }
  };

  playStateJD.evaluateGraphForThreeChoices = function(attempts){
    //sharply goes up
    if (attempts == 5){
      graphY -= (150 / graphLengthIncrease) * 2
    }
    //stays flat
    else if(attempts == 4){
      graphY -= 0
    }
    //slightly goes down
    else if(attempts == 3){
      //graphY += (150 / graphLengthIncrease) * 2 used to sharply go down but changed per Marty's request
      graphY += (150 / graphLengthIncrease)
    }
    else{
      graphY += 75
    }
  };

  playStateJD.evaluateGraphForTwoChoices = function(attempts){
    //sharply goes up for the size of the whole graph from top to bottom
    if (attempts == 5){
      graphY -= (150 / graphLengthIncrease) * 2 
    }
    //stays flat
    else if (attempts == 4){
      //graphY += (150 / graphLengthIncrease) * 2 changed to stay flat per Marty's request
      graphY -= 0
    }
    //something went wrong
    else{
      graphY += 75
    }
  };
  