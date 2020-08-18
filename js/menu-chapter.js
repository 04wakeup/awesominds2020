var menuChapterState = {
  create: function(){
    console.log('state: menuChapter');

    var text = game.add.text(game.world.centerX + 1000, Math.floor(game.global.logoText.bottom), 'Select a Chapter', game.global.blackFont);
    game.add.tween(text).to({x: Math.floor(game.world.centerX - (text.width/2))}, 100, Phaser.Easing.Default, true, 0); 
    text.padding.x = 5;

    var back = game.world.add(new game.global.SpeechBubble(game, game.world.x, game.world.y, game.world.width, 'Back', false, true, menuChapterState.backButton));
    game.add.tween(game.global.logoText).to({x: Math.floor(game.world.x + back.bubblewidth + game.global.borderFrameSize)}, 60, Phaser.Easing.Default, true, 0);

    console.log(game.world.children);
    var courseText = game.add.text(game.global.pauseButton.left, game.world.y, game.global.selectedCourseName, game.global.smallerBlackFont);
    courseText.x = Math.round(courseText.x - courseText.width - game.global.borderFrameSize); 
    courseText.padding.x = 5;

    var chapters = [];
    $(function (){
      $.ajax({ //get list of chapters from the database and create a button for each one
        type: "POST",
        url: 'db-getChapters.php',
        data: { course_id: game.global.selectedCourse },
        success: function(data){
          //console.log(data);
          chapters = $.parseJSON(data);
          var prevHeights = 10 * dpr;
          var now = moment();
          for (var i = 0; i < chapters.length; i++) {
            var start = new Date(chapters[i].start_date);
            var due = new Date(chapters[i].due_date);
            var end = new Date(chapters[i].end_date);
            //check availability date start and end; 
            //if the current time is between the two and not hidden, the chapter should be available to play
            var enabled = (now.isBetween(moment(chapters[i].start_date), moment(chapters[i].end_date)) 
                && chapters[i].hidden == 0);
            //create speach bubble
            var b = game.world.add(new game.global.SpeechBubble(game, 
                game.world.width + 1000, 
                text.bottom, 
                game.world.width * .8, 
                chapters[i].chapter_id + ' - ' + chapters[i].chapter_name, 
                false, 
                enabled,
                menuChapterState.chapterBtnClick));

            b.y += prevHeights;
            prevHeights += b.bubbleheight + 10 * adjust_dpr;
            b.data.chapter = chapters[i];

            var chapterText = "Starts " + (start.getMonth() + 1)+ " / " + start.getDate() + " - " + start.getFullYear() 
            + " Due on " + (due.getMonth() + 1) + " / " + due.getDate() + " - " + due.getFullYear()
            + " ends on " + (end.getMonth() + 1) + " / " + end.getDate() + " - " + end.getFullYear();

            if (chapters[i].hidden == 1) {
              chapterText += " (hidden)";
            }
            
            var chapterDesc = game.add.text(b.x, 
              b.y + b.bubbleheight, 
              chapterText, 
              game.global.smallerBlackFont);
            // chapterDesc.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5);  James : Marty wants to remove
            chapterDesc.padding.x = 5;
            prevHeights += chapterDesc.height;
            
            //animate button and text coming in
            game.add.tween(b).to({x: Math.floor(game.world.centerX - b.bubblewidth/2)}, 350, 
                Phaser.Easing.Default, true, 150 * i);
            game.add.tween(chapterDesc).to({x: Math.floor(game.world.centerX - chapterDesc.width/2)}, 
                350, Phaser.Easing.Default, true, 150 * i);
            
          }
        }
      });
    });

    //this is for getthing the preamble to display before all the chapters
    $.ajax({
      type: 'POST',
      url: 'db-getPreambleForOneChapter.php',
      data: {course_id: game.global.selectedCourse},
      dataType: 'json',
      success:function(data){
        /*
        console.log(data[0].preamble.length);
        console.log(data[0].preamble.charAt(50));
        if (data[0].preamble == undefined || data[0].preamble == null || data[0].preamble == ""){
          game.global.preamble = "There is no Preamble for this chapter";
        }
        else{
          // using regular expression to find the next white space after 100 characters to display the preamble properly
         game.global.preamble = data[0].preamble.replace(/(.{100}[^ ]*)/,"$1\n");
        }
        */
       game.global.preambleArray = data
       console.log(data);
       console.log(data[0].preamble);
       console.log(game.global.preambleArray[0].preamble);
       
       
      }
    });
  },
  chapterBtnClick: function(){ 
    game.global.selectedChapter = this.data.chapter.chapter_id;
    console.log('selected chapter id: ' + game.global.selectedChapter);
    game.state.start('menuMode');
  },
  backButton: function(){ 
    game.state.start('menuCourse');
  }
}
