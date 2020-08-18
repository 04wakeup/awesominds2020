console.log("game.js");
var dpr = Math.floor(window.devicePixelRatio);
// James: adjust dpr value
var adjust_dpr = dpr;
if(dpr > 1){
  adjust_dpr = dpr * 0.5;
}
var game = new Phaser.Game(Math.floor(window.innerWidth * dpr), Math.floor(window.innerHeight * dpr), Phaser.AUTO, 'gameDiv', null, true, true);
game.global = {}; // create global object we can add properties to and access from any state

//NOTE: no clue what this does, this is why you comment your code
game.global.mapNum = function (num, in_min, in_max, out_min, out_max) {
  return Math.floor((num - in_min) * (out_max - out_min) / (in_max - in_min) + out_min);
}

WebFontConfig = {
  google: {
    //add any google fonts here
    families: ['Roboto', 'Varela Round', 'Material Icons']
  }
}

// add game states
//menu states
game.state.add('menuCourse', menuCourseState);
game.state.add('menuChapter', menuChapterState);
game.state.add('menuMode', menuModeState);

//pregame and premode states with master being at the top
game.state.add('pregame', preGameState);

//game and mode states with masters being at the top
game.state.add('play', playState);
game.state.add('justDrills', playStateJD);
game.state.add('rateQuestions', playStateRQ);
game.state.add('slideCards', playStateSC);
game.state.add('mysteryMultiplier', playStateMM);

//end of game and mode state with masters being at the top
game.state.add('endOfMode', endOfModeState);
game.state.add('gameOver', gameOverState);

// James states 
game.state.add('gameshowUI', gameShowUIState);  

//Adam states
game.state.add('settings', settingState);
game.state.add('stop', stopState);
game.state.add('sameEndMode', endingModeState);

//Walkers states
//game.state.add('regularQuestions', regularQuestions);

game.state.add('preload', preloadState);

game.global.session = phpSession;
game.state.start('preload');
