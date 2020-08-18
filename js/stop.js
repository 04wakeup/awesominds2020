/*
Creator: Adam Lowe
Purpose: This is the menu for the "STOP" button in the menu
Date: June 24th 2020
Note: anywhere where it says "game.state.getCurrentState()" refers to endOfMode.js
*/
var previousState = ""

var stopState = Object.create(endOfModeState);

stopState.getButtons = function(){
  //Background screen
  var pauseBG = game.add.graphics(0, 0);
  pauseBG.lineStyle(2, 0x000000, 1);
  pauseBG.beginFill(0x078EB7, 1);
  pauseBG.drawRoundedRect(game.world.x + 10, game.global.logoText.bottom, game.world.width - 20, game.world.height - game.global.logoText.height - 10, 10);
  game.global.pauseUI.add(pauseBG);

  //Displays the text
  game.global.pausedText = game.add.text(game.world.centerX, Math.floor(game.global.logoText.bottom), game.global.stopScreen.getStatLines(), game.global.whiteFont);
  // game.global.pausedText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5);  // James: Marty wants to remove
  game.global.pausedText.padding.x = 5;
  game.global.pausedText.x = Math.floor(game.global.pausedText.x - (game.global.pausedText.width/2));
  game.global.pauseUI.add(game.global.pausedText);

  // sets the current height for the first button
  var prevHeights = 220;
  console.log("this is previous height" + prevHeights);

  //List of buttons from top to bottom
  var btns = [{text: 'Continue', clickFunction: game.global.stopScreen.unpause},
    {text: 'Select Different Course', clickFunction: game.global.stopScreen.quitToCourseSelect},
    {text: 'Select Different Chapter/Section', clickFunction: game.global.stopScreen.quitToChapterSelect},
    {text: 'Select Different Task', clickFunction: game.global.stopScreen.quitToTaskSelect},
    {text: 'Log Out', clickFunction: game.global.stopScreen.logOut}];
  //every time it loops the next button gets set a few pixels lower
  for (var b in btns) {
      var btn = game.world.add(new game.global.SpeechBubble(game, game.world.centerX, prevHeights, game.world.width * .8, btns[b].text, false, true, btns[b].clickFunction));
      btn.x -= Math.floor(btn.bubblewidth/2);
      game.global.pauseUI.add(btn);
      game.input.onDown.add(btns[b].clickFunction, btn);
      prevHeights += btn.bubbleheight + 5;
  };
};
