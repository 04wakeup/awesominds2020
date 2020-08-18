var playStateMM = Object.create(playState);

playStateMM.correctAnswerChosen = function() {
  game.state.getCurrentState().disableAnswers();
  game.state.getCurrentState().showMultipliers();
};

playStateMM.showMultipliers = function() {
  game.global.multiplierBubbles = game.add.group();
  //game.state.getCurrentState().removeQuestion();
  //the different possible multipliers
  var multipliers = [3, 2, 1, .5, .1];
  multipliers = game.global.shuffleArray(multipliers);
  var xOffset = -150; 
  for (var i = 0; i < 3; i++) {
    var multiplierBtn = game.world.add(new game.global.SpeechBubble(game,
      game.world.width + 1000, // The starting x position of button, should be offscreen
      game.height * .7, 
      500,
      "???",
      false,
      true,
      game.state.getCurrentState().multiplierBtnClick));
    //add multiplier to buttons data and animate it in
    multiplierBtn.data = multipliers[i];
    var bTween = game.add.tween(multiplierBtn).to({x: Math.floor(game.world.centerX - multiplierBtn.bubblewidth/2 + xOffset)}, 500, Phaser.Easing.Default, true, 1000);
    game.global.multiplierBubbles.add(multiplierBtn);

    //text behind button
    var multiplierText = game.add.text(game.world.width + 1000, 
      game.height * .7, 
      "x" + multipliers[i].toString(10),
      game.global.blackFont);
    multiplierText.anchor.set(0.5, 0);
    var tTween = game.add.tween(multiplierText).to({x: Math.floor(game.world.centerX + xOffset)}, 500, Phaser.Easing.Default, true, 1000);
    game.global.questionUI.add(multiplierText);
    xOffset += 150;
  }
  game.global.questionUI.add(game.global.multiplierBubbles);
};

playStateMM.multiplierBtnClick = function() {
  //TODO: disable all bubbles
  var multiplier = this.data;
  game.state.getCurrentState().pointValue = Math.ceil(game.state.getCurrentState().pointValue * multiplier);
  game.global.multiplierBubbles.removeAll(true);
  game.global.timer.stop();
  // TODO/NOTE:change this to a create next button if marty wants
  game.global.timer.add(2000, game.state.getCurrentState().correctAnswerChosenPrimaryFunction, game.state.getCurrentState());
  game.global.timer.start();
}