/*
* MADE BY: Walker Jones
* PURPOSE: This game over screen is displayed when the player runs out of lives in game show
*/
var previousState = ""

var gameOverState = Object.create(endOfModeState);

gameOverState.getButtons = function(){
  //Background screen
  var pauseBG = game.add.graphics(0, 0);
  pauseBG.lineStyle(2, 0x000000, 1);
  pauseBG.beginFill(0x078EB7, 1);
  pauseBG.drawRoundedRect(game.world.x + 10, game.global.logoText.bottom, game.world.width - 20, game.world.height - game.global.logoText.height - 10, 10);
  game.global.pauseUI.add(pauseBG);

  //Displays the text
  game.global.pausedText = game.add.text(game.world.centerX, Math.floor(game.global.logoText.bottom), game.global.stopScreen.getStatLines(), game.global.whiteFont);
  // game.global.pausedText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
  game.global.pausedText.padding.x = 5;
  game.global.pausedText.x = Math.floor(game.global.pausedText.x - (game.global.pausedText.width/2));
  game.global.pauseUI.add(game.global.pausedText);

  // sets the current height for the first button
  var prevHeights = 220;
  console.log("this is previous height" + prevHeights);

  //List of buttons from top to bottom
  var btns = [{text: 'Play this chapter/section again', clickFunction: game.global.stopScreen.replay},
    {text: 'Select Different Chapter/Section', clickFunction: game.global.stopScreen.quitToChapterSelect},
    {text: 'Select Different Task', clickFunction: game.global.stopScreen.quitToTaskSelect},
    {text: 'Select Different Course', clickFunction: game.global.stopScreen.quitToCourseSelect},
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

gameOverState.getStatLines = function() {
  var statLines = [
    "Game Over!\nYou have lost all of your lives."
  ];
  return statLines;
},

gameOverState.replay = function() {
  if(game.paused && game.global.inputInside(this)){
    this.data.func = function(){
      game.global.pauseButton.visible = true;
      game.global.pauseUI.destroy();
      game.input.onDown.removeAll();
      game.paused = false;
      game.state.start('pregame', true, false, game.global.selectedMode);
    }
    game.global.stopScreen.areYouSure(this);
  }
}