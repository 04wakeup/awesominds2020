/*
Cretor: Adam Lowe
Purpose: These are the buttons for the settings button "Gear" in the top right hand corner
Date: June 24th 2020
*/
var previousState = "";
var settingState = {
    // the data passed through is the previous state before the
    // "gear" was pressed
    init : function(data){
        previousState = data;
        console.log(data);
    },
    pause: function() {
      console.log("paused state");
        //game.input.onDown.add(game.global.unpause, game.global.unpauseButton);
        var ismuted = game.sound.mute;
        this.visible = false;
        //game.global.unpauseButton.visible = true;
        game.paused = true;
        game.sound.mute = ismuted;
        game.global.pauseUI = game.add.group();

        var pauseBG = game.add.graphics(0, 0);
        pauseBG.lineStyle(2, 0x000000, 1);
        pauseBG.beginFill(0x078EB7, 1);
        pauseBG.drawRoundedRect(game.world.x + 10, game.global.logoText.bottom, game.world.width - 20, game.world.height - game.global.logoText.height - 10, 10);
        game.global.pauseUI.add(pauseBG);

        game.global.pausedText = game.add.text(game.world.centerX, Math.floor(game.global.logoText.bottom), 'Paused \n Sound', game.global.whiteFont);
        // game.global.pausedText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5);  // James: Marty wants to remove
        game.global.pausedText.padding.x = 5;
        game.global.pausedText.x = Math.floor(game.global.pausedText.x - (game.global.pausedText.width/2));
        game.global.pauseUI.add(game.global.pausedText);

        game.global.makeVolText();
        game.input.onDown.add(game.global.muteSound, game.global.muteText);
        var prevHeights = game.global.volText.y;

        //volume up button
        var volBtnUp = game.world.add(new game.global.SpeechBubble(game, game.global.volText.x + game.global.volText.bubblewidth + 10, prevHeights, game.world.width * .8, '+', false, true, game.global.volumeUp));
        game.global.pauseUI.add(volBtnUp);
        game.input.onDown.add(game.global.volumeUp, volBtnUp);
        //volume down button
        var volBtnDown = game.world.add(new game.global.SpeechBubble(game, game.global.volText.x, prevHeights, game.world.width * .8, '-', false, true, game.global.volumeDown));
        volBtnDown.x -= volBtnDown.bubblewidth + 10;
        game.global.pauseUI.add(volBtnDown);
        game.input.onDown.add(game.global.volumeDown, volBtnDown);
        prevHeights = game.global.muteText.y + ((game.global.muteText.bubbleheight + 5) *2);

        //buttons shown in the setting menu
        var btns = [
            {text: 'Resume Game', clickFunction: unpause},
            {text: 'Home', clickFunction: homeBtnClick},
            {text: 'Quit to Course Select', clickFunction: quitToCourseSelect},
            {text: 'Log Out', clickFunction: logOut}
        ];
        //setting up the layout of the buttons
        for (var b in btns) {
            var btn = game.world.add(new game.global.SpeechBubble(game, game.world.centerX, prevHeights, game.world.width * .8, btns[b].text, false, true, btns[b].clickFunction));
            btn.x -= Math.floor(btn.bubblewidth/2);
            game.global.pauseUI.add(btn);
            game.input.onDown.add(btns[b].clickFunction, btn);
            prevHeights += btn.bubbleheight + 5;
        };
        
        //once the "resume" button is clicked the volume settings get saved to the database
        //and returns to the previous screen
        function unpause(){
            if(game.paused && game.global.inputInside(this)){
              //save user volume
              game.global.session.user_volume = Math.round(game.sound.volume * 10) / 10;
              $.ajax({
                type: 'POST',
                url: 'db-setuservolume.php',
                data: game.global.session,
                success: function(data){
                  game.global.session = $.parseJSON(data);
                }
              });
              //game.global.unpauseButton.visible = false;
              game.global.pauseButton.visible = true;
              game.global.pauseUI.destroy();
              game.input.onDown.removeAll();
              game.paused = false;
              //game.state.start(previousState);
            }
          };
          
          // when this button is clicked you return to the course selection screen
          function quitToCourseSelect(){
            if(game.paused && game.global.inputInside(this)){
              this.data.func = function(){
                //game.global.unpauseButton.visible = false;
                game.global.pauseButton.visible = true;
                game.global.pauseUI.destroy();
                game.input.onDown.removeAll();
                game.paused = false;
                endOfModeState.chooseCourseClick.call(this);
              }
              areYouSure(this);
            }
          };

          // when this button is clicked you return to the login page
          function logOut(){
            if(game.paused && game.global.inputInside(this)){
              this.data.func = function(){
                alert("Thank you for using Awesominds");
                window.location.href = "logout.php";
              }
              areYouSure(this);
            }
          };
          
          // once this button is clicked you return to the Home screen
          function homeBtnClick(){
            if(game.paused && game.global.inputInside(this)){
              this.data.func = function(){
                window.location.href = "index.php";
              }
              areYouSure(this);
            }
          };
          
          // this sets up the functionality for the "are you sure" screen
          // this shows when pressing "Home", "Quit to course select" "Log Out"
          areYouSure = function(btn){
            var sureUI = game.add.group();
            var sureGfx = game.add.graphics(0, 0);
            sureGfx.lineStyle(2, 0x000000, 1);
            sureGfx.beginFill(0x078EB7, 1);
            sureGfx.drawRoundedRect(game.world.x + 10, game.global.logoText.y + game.global.logoText.height*2, game.world.width - 20, game.world.height - (game.global.logoText.y + game.global.logoText.height*2) - 10, 10);
            sureUI.add(sureGfx);
      
            var txt = game.add.text(game.world.centerX, Math.floor(game.global.logoText.y + game.global.logoText.height*2), btn.bitmapText.text, game.global.whiteFont);
            // txt.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5);  // James: Marty wants to remove
            txt.padding.x = 5;
            txt.x = Math.floor(txt.x - (txt.width/2));
            sureUI.add(txt);
      
            var txt2 = game.add.bitmapText(game.world.centerX, Math.floor(txt.y + txt.height), '8bitoperator', 'Are you sure?', 11 * dpr);
            txt2.x = Math.floor(txt2.x - (txt2.width/2));
            sureUI.add(txt2);
      
            var btnResult = function(btn){
              if(game.paused && game.global.inputInside(this)){
                var v = this.data.value;
                var b = this.data.btn;
                sureUI.destroy();
                if(v){
                  b.data.func.call(this.data.btn);
                }
              }
            };
      
            var yesBtn = game.world.add(new game.global.SpeechBubble(game, game.world.centerX, Math.floor(txt2.y + txt2.height + game.global.borderFrameSize), game.world.width * .8, 'Yes', false, true, btnResult));
            yesBtn.data.value = true;
            yesBtn.data.btn = btn;
            yesBtn.x = Math.floor(yesBtn.x - yesBtn.bubblewidth * 1.5);
            sureUI.add(yesBtn);
            game.input.onDown.add(btnResult, yesBtn);
      
            var noBtn = game.world.add(new game.global.SpeechBubble(game, game.world.centerX, yesBtn.y, game.world.width * .8, 'No', false, true, btnResult));
            noBtn.data.value = false;
            noBtn.x = Math.floor(noBtn.x + noBtn.bubblewidth/2);
            sureUI.add(noBtn);
            game.input.onDown.add(btnResult, noBtn);
          };
    }
      
}
