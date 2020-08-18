var modes = [
  { name: 'Error', desc: 'Error', id: 0, enabled: false, point_value: 0 }, 
  { name: 'Error', desc: 'Error', id: 0, enabled: false, point_value: 0 },
  { name: 'Error', desc: 'Error', id: 0, enabled: false, point_value: 0 },
  { name: 'Error', desc: 'Error', id: 0, enabled: false, point_value: 0 }
];

var menuModeState = {
  create: function(){
    console.log(game.global.selectedChapter);
    //console.log("state: menu-mode", modes);
    $.ajax({
      type: "POST",
      url: "db-getChapterTasks.php",
      data: {
        course_id: game.global.selectedCourse,
        chapter_id: game.global.selectedChapter
      },
      dataType: "json",
      success: function(data) {
        //console.log("chapter ", game.global.selectedChapter, " tasks data: ", data);
        // Set the values for all modes but game show
        for (var i = 0; i < modes.length; i++){
          modes[i].name = data[i].task_name;
          modes[i].desc = data[i].description;
          modes[i].id = data[i].task_pk;
          modes[i].enabled = data[i].enabled;
          modes[i].point_value = data[i].point_value;
        }
        game.state.getCurrentState().createModeButtons();
      }
    });
    game.global.preamble = game.global.preambleArray[game.global.selectedChapter - 1].preamble;
    if (game.global.preamble == "" || game.global.preamble == null || game.global.preamble == undefined){
      game.global.preamble = " ";
    }
      // using regular expression to find the next white space after 90 characters to display the preamble properly
     game.global.preamble = game.global.preamble.replace(/(.{90}[^ ]*)/,"$1\n");
    
  },
  createModeButtons: function() {
    //preamble placement and text
    var preambleText = game.add.text(game.world.width + 1000, game.world.centerY, game.global.preamble, game.global.smallerBlackFont);
    // preambleText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
    preambleText.padding.x = 3;
    preambleText.anchor.setTo(0.5);
    console.log("we got this part here");

    var back = game.world.add(new game.global.SpeechBubble(game, game.world.x, game.world.y, game.world.width, 'Back', false, true, menuModeState.backButton));

    //setup course and chapter in top right
    var courseText = game.add.text(game.global.pauseButton.left, game.world.y, game.global.selectedCourseName, game.global.smallerBlackFont);
    courseText.x = Math.round(courseText.x - courseText.width - game.global.borderFrameSize);
    // courseText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
    courseText.padding.x = 5;

    var chapterText = game.add.text(game.global.pauseButton.left, Math.floor(courseText.bottom - 5), 'Chapter ' + game.global.selectedChapter, game.global.smallerBlackFont);
    chapterText.x = Math.round(chapterText.x - chapterText.width - game.global.borderFrameSize);
    // chapterText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
    chapterText.padding.x = 5;

    //setup header
    var header = game.add.text(game.world.centerX + 1000, Math.floor(chapterText.bottom), 'Select a task', game.global.BlackFont);
    game.add.tween(header).to({x: Math.floor(game.world.centerX - (header.width/2))}, 100, Phaser.Easing.Default, true, 0);
    // header.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
    header.padding.x = 5;
    header.style.wordWrap = true;
    header.style.wordWrapWidth = game.world.width - (game.global.borderFrameSize * 2);

    //create a button and text for each game mode
    var prevHeights = 10 * dpr;  
    for (var i = 0; i < modes.length; i++) { 
      if (modes[i].enabled == 1) {
        var b = game.world.add(new game.global.SpeechBubble(game, 
          game.world.width + 1000, 
          header.bottom, 
          game.world.width * .8, 
          modes[i].name, 
          false, 
          true,
          menuModeState.modeBtnClick));
              
        b.y += prevHeights;
        prevHeights += b.bubbleheight + (10 * dpr);
        b.data = modes[i];

        var taskDesc = modes[i].desc;

        //description of each game mode
        var t = game.add.text(game.world.width + 1000, b.y + b.bubbleheight, taskDesc, game.global.smallerBlackFont);
        // t.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
        t.padding.x = 3;
        prevHeights += t.height;

        //animate button coming in
        game.add.tween(b).to({x: Math.floor(game.world.centerX - b.bubblewidth/2)}, 350, Phaser.Easing.Default, true, 150 * i);
        game.add.tween(t).to({x: Math.floor(game.world.centerX - t.width/2)}, 350, Phaser.Easing.Default, true, 150 * i);
        game.add.tween(preambleText).to({x: Math.floor(game.world.centerX - t.width/2) + 250}, 350, Phaser.Easing.Default, true, 600);
      }
    }
    
  },
  modeBtnClick: function(){ 
    game.global.selectedMode = this.data;
    game.state.start("pregame", true, false, game.global.selectedMode); 
  },
  backButton: function(){ 
    game.state.start('menuChapter');
  }
}
