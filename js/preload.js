/* 
* MADE BY: Previous Team(s)
* PURPOSE: 
*/

var preloadState = {
  preload: function() {
    console.log("preload");
    //vars
    var background;
    var questionCard;
    var answerCard;
    //prevents game breaking when zoomed below 100%
    if(dpr<=0){ dpr = 1};
    game.load.script('webfont', '//ajax.googleapis.com/ajax/libs/webfont/1.4.7/webfont.js');
    game.scale.scaleMode = Phaser.ScaleManager.RESIZE;

    //function to check if a click occurs inside a SpeechBubble
    //necessary for any input while the game is paused
    game.global.inputInside = function(item){
      return (game.input.x > item.x && game.input.x < item.x + item.bubblewidth + game.global.borderFrameSize && game.input.y > item.y && game.input.y < item.y + item.bubbleheight + game.global.borderFrameSize);
    };

    game.scale.pageAlignHorizontally = true;
    game.scale.pageAlignVertically = false;
    game.scale.windowConstraints.bottom = "visual";
    game.stage.disableVisibilityChange = true;

    var assetPath = (dpr >= 2) ? 'assets/' : 'assets/small/'; //use big assets on screens with high dpr, small assets otherwise

    game.load.image('right', 'assets/right.png');
    game.load.image('wrong', 'assets/wrong.png');
    game.load.image('check', assetPath + 'check2.png');
    game.load.image('arrow', assetPath + 'arrow.png');
    game.load.image('x', assetPath + 'x.png');
    game.load.image('logo', assetPath + 'logo2.png');
    game.load.image('pts10', 'assets/pts10.png');
    game.load.image('pts25', 'assets/pts25.png');
    game.load.image('5pts', 'assets/5pts.png');
    game.load.image('15pts', 'assets/15pts.png');
    game.load.image('25pts', 'assets/25pts.png');
    game.load.image('medal', assetPath + 'medal.png');
    game.load.image('yellow', 'assets/yellow.png');
    game.load.image('yellowSlider','assets/yellowSlider.png');
    

    game.load.audio('play',['assets/music/Mushroom.m4a','assets/music/Mushroom.ogg']);
    game.load.audio('menu',['assets/music/Crystal.m4a','assets/music/Crystal.ogg']);
    game.load.audio('wrong1',['assets/music/WrongAns1.m4a','assets/music/WrongAns1.ogg']);
    game.load.audio('question',['assets/music/QuestionEnters.m4a','assets/music/QuestionEnters.ogg']);
    game.load.audio('endGame',['assets/music/EndOFGame.m4a','assets/music/EndOFGame.ogg']);
    // game.load.audio('drums',['assets/music/DrumsAndWhoo.m4a','assets/music/DrumsAndWhoo.ogg']); James: not used, remove it later
    game.load.audio('correct',['assets/music/CorrectAns.m4a','assets/music/CorrectAns.ogg']);
    game.load.audio('playerWin',['assets/music/playerWins.m4a','assets/music/PlayerWins.ogg']); 
    game.load.audio('click',['assets/music/click.ogg']); 
    game.load.audio('questionDisappear',['assets/music/questionDisappear.m4a','assets/music/questionDisappear.ogg']); 


    var jinSheetDim = (dpr >= 2) ? [264, 364] : [66, 91]; //sprite sheet sizes vary with dpr
    game.load.spritesheet('jin', assetPath + 'jin.png', jinSheetDim[0], jinSheetDim[1]);
    var crownSheetDim = (dpr >= 2) ? [397, 332] : [83, 69];
    game.load.spritesheet('crown', assetPath + 'crownsheet.png', crownSheetDim[0], crownSheetDim[1]);
    game.load.image('jackie', assetPath + 'jackie.png');

    game.load.start();

    game.global.wrongsounds = [];
    game.global.rightsounds = [];

    //use db-setuservolume.php to get a fresh copy of the session in case volume or anything changed since last login
    $.ajax({
      type: 'POST',
      url: 'db-setuservolume.php',
      data: game.global.session,
      success: function(data){
        game.global.session = $.parseJSON(data);
      }
    });

    var numOppImages = 31;  // James: now 31
    game.global.oppImageKeys = [];
    //this sets the name for all the characters, in order of the image numbers (plus 'zero' just for index fixing)
    var charNames = ['Zero', 'Jamar', 'Bruno', 'Edward', 'Sofia'
                   , 'Dahra', 'Manu', 'Jira', 'Chandi', 'Dimbo'
                   , 'Lamar', 'Seadog', 'Kit', 'Pablo', 'Fernanda'
                   , 'Mickey', 'Rose', 'Harpo', 'Geraldine', 'Honey'
                   , 'Roger', 'Milk', 'Max', 'Mr. Toad', 'Sirius'
                   , 'Hedwig', 'Coco', 'Babar', 'Jemima', 'Pingu'
                   , 'Jaws', 'Python'];
    for (var i = 1; i <= numOppImages; i++) {
      game.load.image('opp' + i, assetPath + 'opp2/oppon' + i + '.png');
      if(i != game.global.session['avatarnum']){
        var opp = {
          imageKey: 'opp' + i,
          name: charNames[i]
        }
        game.global.oppImageKeys.push(opp);
      }
    }

    game.global.borderFrameSize = 9 * adjust_dpr;
    game.load.spritesheet('bubble-border','assets/bubbleborder' + dpr + '.png', game.global.borderFrameSize, game.global.borderFrameSize);
    if (dpr <= 1){  // James: adjust Tail size
      game.load.image('bubble-tail', 'assets/bubbletailleft' + dpr + '.png');
    } else {
      game.load.image('bubble-tail', 'assets/bubbletailleft0.5.png');
    }
    
    game.load.bitmapFont('8bitoperator', 'assets/8bitoperator.png', 'assets/8bitoperator.xml');
  },

	create: function() { 
    //can use any font that was listed in the WebFontConfig in game.js
    game.global.mainFont = { font: 'Varela Round', fontSize: 20 * adjust_dpr, align: 'left'};
    game.global.jinFont = { font: 'Varela Round', fontSize: 20 * adjust_dpr, align: 'left', fill: '#a50010'};
    game.global.whiteFont = { font: 'Varela Round', fontSize: 24 * adjust_dpr, fill: 'white', align: "center"};
    game.global.smallerWhiteFont = { font: 'Varela Round', fontSize: 17 * adjust_dpr, fill: 'white', align: 'center'};
    game.global.blackFont = { font: 'Varela Round', fontSize: 24 * adjust_dpr, fill: 'black', align: "center"};
    game.global.smallerBlackFont = { font: 'Varela Round', fontSize: 17 * adjust_dpr, fill: 'black', align: 'center'}; 
    game.global.wrongsounds.push(game.add.audio('wrong1'));
    game.global.rightsounds.push(game.add.audio('correct'));
    game.global.music = game.add.audio('menu');
    game.global.clickButtonSound = game.add.audio('click');  // James: button click sound 

    game.sound.volume = Math.round(game.global.session.user_volume * 10) / 10;

    game.global.shuffleArray = function(array) { //function to randomize any array
      for (var i = array.length - 1; i > 0; i--) {
        var j = Math.floor(Math.random() * (i + 1));
        var temp = array[i];
        array[i] = array[j];
        array[j] = temp;
      }
      return array;
    };

    /*
    * NAME: SpeechBubble
    * MADE BY: Previous team(s)
    * PARAMS: game - game - the game itself
    *   x - int - the x coordinate to start drawing the bubble. NOTE: changing this value has inconsistent 
    *     results.
    *   y - int - the y coordinate to start drawing the bubble
    *   width - int - how wide the bubble should be
    *   text - string - the text to display in the bubble
    *   withTail - boolean - if true, adds a small "tail", as if it is a characters speech bubble to the left
    *   asButton - boolean - if true the button is clickable. e.g. menu buttons
    *   clickFunction - function - a reference for the function to execute when the button
    *     is clicked. (I assume)
    *   isAnswerText - unknown
    *   choice - unknown
    *   isJin - super unknown
    */
    game.global.SpeechBubble = function(game, x, y, width, text, withTail, asButton, clickFunction, isAnswerText, choice, isJin) {
      //console.log(clickFunction);
      Phaser.Sprite.call(this, game, x, y);
      x = Math.floor(x);
      y = Math.floor(y);

      // Some sensible minimum defaults
      width = width || game.global.borderFrameSize * 3;
      var height = game.global.mainFont.fontSize + game.global.borderFrameSize;

      // Set up our text and run our custom wrapping routine on it
      var prefix = isAnswerText ? choice + '. ' : '';
      var fontStyle = game.global.mainFont;
      if(isJin){
        // fontStyle = game.global.jinFont;
      } else if (text =='\uE8B8') { //settings icon
        fontStyle = {font: 'Material Icons', fontSize: 22 * adjust_dpr, align: 'center'};
      }
      this.bitmapText = game.add.text(Math.floor(x + game.global.borderFrameSize + 5), Math.floor(y + (game.global.borderFrameSize/3) + 5), prefix + text, fontStyle);
      // set width for wrapping and let phaser figure out where it should wrap the lines
      this.bitmapText.wordWrapWidth = width - game.global.mainFont.fontSize - 10;
      var prewrapped = this.bitmapText.precalculateWordWrap(prefix + text);
      var wrapFixed = "";
      for (var i = 0; i < prewrapped.length; i++) {
        //phaser seems to sometimes add lines that are just a space; ignore them
        if(prewrapped[i] != " "){
          //add newline if more than 1 line
          if(i>0){
            wrapFixed += "\n";
          }
          //take out the space at the end of the line that phaser's word wrap seems to add
          wrapFixed += prewrapped[i].slice(0,-1);
        }
      }
      //change text to the newly wrapped text
      this.bitmapText.text = wrapFixed;

      // Calculate the width and height needed for the edges
      var bounds = this.bitmapText.getBounds();
      // use set width for answer choices, and variable width based on the text size for everything else
      if(isAnswerText){
        bounds.width = Math.floor(width + game.global.mainFont.fontSize + 10);
      }else{
        width = Math.floor(bounds.width + game.global.mainFont.fontSize + 10);
      }
      height = Math.floor(Math.max(height, bounds.height) + 10);

      var bubbleGfx = game.add.graphics(0, 0);
      bubbleGfx.lineStyle(2 * adjust_dpr, 0x000000, 1);
      bubbleGfx.beginFill(0xffffff, 1);
      //can tell this is an unavailable chapter, course or modebutton, dim the button if so
      if((clickFunction == menuChapterState.chapterBtnClick || 
        clickFunction == menuCourseState.courseBtnClick ||
        clickFunction == menuModeState.modeBtnClick) && !asButton){ 
        bubbleGfx.beginFill(0x888888, 1);
      }
      bubbleGfx.drawRoundedRect(x, y, width, height, 8*adjust_dpr);
      this.addChild(bubbleGfx)

      if(withTail){
        // Add the tail
        var tail = game.cache.getImage("bubble-tail"); //TODO graphics-draw this too if doable and remove the image
        this.tail = this.addChild(game.make.image(Math.floor(this.x - tail.width*.95), Math.floor(y + tail.height/3), 'bubble-tail'));  
        // this.tail.angle = 90;
      }

      // Add our text last so it's on top
      this.addChild(this.bitmapText);
      this.pivot.set(x, y);

      //make some properties public for positioning purposes
      this.bounds = bounds;
      this.bubbleheight = height;
      this.bubblewidth = width;

      if(asButton){
        this.isButton = true;
        //enable input if this is a button
        this.inputEnabled = true;
        this.input.useHandCursor = true;
        //functions to be used if this is a button
        this.over = function(){
          bubbleGfx.tint = 0x5AC5E8;
        };

        this.out = function(){
          bubbleGfx.tint = 0xffffff;
        };

        this.click = function(thing, pointer, isOver){
          // James: any button clicks make sounds 
          game.global.clickButtonSound.play();

          if(isOver){
            bubbleGfx.tint = 0xffffaa;
            clickFunction.call(this);
          }
          else{
            for (var i = 0; i < this.parent.children.length; i++) {
              var item = this.parent.children[i];
              if((typeof item == 'object') && (item.hasOwnProperty('isButton')) && (pointer.x > item.x && pointer.x < item.x + item.bubblewidth + game.global.borderFrameSize && pointer.y > item.y && pointer.y < item.y + item.bubbleheight + game.global.borderFrameSize)){
                bubbleGfx.tint = 0xffffaa;
                clickFunction.call(item);
              }
            }
          }
        }

        this.events.onInputOver.add(this.over, this);
        this.events.onInputOut.add(this.out, this);
        this.events.onInputDown.add(this.over, this);
        this.events.onInputUp.add(this.click, this);
      }
    };

    game.global.SpeechBubble.prototype = Object.create(Phaser.Sprite.prototype);
    game.global.SpeechBubble.prototype.constructor = game.global.SpeechBubble;

    // raise volume for all sound
    game.global.volumeUp = function(){
      if(game.paused && game.global.inputInside(this)){
        if(game.sound.volume < 0.9){
          if(game.sound.mute){
            game.global.muteSound.call(this);
          }
          game.sound.volume += 0.1;
          game.global.volText.kill();
          game.global.muteText.kill();
          game.global.makeVolText();
        }
      }
    };

    // lower volume for all sound
    game.global.volumeDown = function(){
      if(game.paused && game.global.inputInside(this)){
        if(game.sound.volume > 0.0){
          if(game.sound.mute){
            game.global.muteSound.call(this);
          }
          game.sound.volume -= 0.1;
          game.global.volText.kill();
          game.global.muteText.kill();
          game.global.makeVolText();
        }
      }
    };

    // mute or unmute all sound
    game.global.muteSound = function(){
      if(game.paused && game.global.inputInside(this)){
        if(game.sound.mute) {
          game.sound.mute = !game.sound.mute;
          game.sound.volume = game.global.prevVolume;
        } else {
          game.global.prevVolume = game.sound.volume;
          game.sound.volume = 0.0;
          game.sound.mute = !game.sound.mute;
        }
        game.global.volText.kill();  // drop
        game.global.muteText.kill();
        game.global.makeVolText();  // recreate quickly
      }
    };

    game.global.btnOver = function(){
      if(game.paused && game.global.inputInside(this)){
        this.over.call(this);
      }
    };

    game.global.makeVolText = function(){
      game.global.volText = game.world.add(new game.global.SpeechBubble(game, game.world.centerX, game.global.pausedText.bottom, game.world.width * .8, 'Volume: ' +  Math.round( game.sound.volume * 10), false, false));
      game.global.volText.x -= Math.floor(game.global.volText.bubblewidth/2);
      game.global.pauseUI.add(game.global.volText);

      var t = (game.sound.mute || game.sound.volume == 0.0) ? 'Unmute' : 'Mute';
      game.global.muteText = game.world.add(new game.global.SpeechBubble(game, game.world.centerX, Math.floor(game.global.volText.y + game.global.volText.bubbleheight + 5), game.world.width * .8, t, false, false));
      game.global.muteText.x -= Math.floor(game.global.muteText.bubblewidth/2);
      game.global.pauseUI.add(game.global.muteText);
    };

    // functionality found in "settings.js"
    game.global.pauseMenu = function(){
      settingState.pause();
    };

    //PROTOTYPE SPLASHSCREEN
    logo = game.add.sprite(game.world.centerX, game.world.centerY, 'logo');
    if(dpr >=2 ) logo.scale.setTo(adjust_dpr/2, adjust_dpr/2);
    logo.x = Math.floor(logo.x - logo.width/2);
    logo.y = Math.floor(logo.y - logo.height/2);
    this.progress = 0;
    this.loader = game.add.graphics(0,0);
    this.loader.beginFill(0x02c487,1);
    this.loader.anchor.set(.5);
    this.loadText = game.add.text(game.world.centerX, logo.bottom, this.progress + '%', game.global.mainFont);
    this.loadText.centerX = game.world.centerX;
  },
  //Mock loading bar. It's a masterpiece.
  update: function(){
    if(this.progress <= 99){
      this.progress+=1;
      this.loadText.setText(this.progress + '%');
      this.loadText.centerX = game.world.centerX;
      this.loader.drawRect(game.world.centerX - 100, logo.bottom, this.progress*2, 20);
    }else{
      this.startGame();
    }
  },
  startGame: function(){
    console.log("startGame");
    game.state.start('menuCourse');
  }
};
