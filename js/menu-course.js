/*
* MADE BY: Previous Team(s)
* PURPOSE: Sets up and displays the menu to choose which course you would like to study.
*/


var menuCourseState = {
  create: function(){
    console.log('state: menuCourse');
    if(typeof(game.global.logoText) !== "undefined"){
      //if logotext exists, this state has happened before. eliminate some redundancy
      game.global.logoText.destroy();
      game.global.music.stop();
    }
    // Setup title at top 
    game.global.logoText = game.add.text(game.world.centerX, 0, (dpr == 1) ? 'Awesominds 2020' : '', game.global.blackFont);
 
    game.global.logoText.fontWeight = 'bold';
    game.global.logoText.fontSize = 26 * adjust_dpr; 
    game.global.logoText.x = Math.floor(game.global.logoText.x - game.global.logoText.width/2);
    // game.global.logoText.setShadow(3, 3, 'rgba(0,0,0,0.5)', 5);  // James remove shadow 
    game.global.logoText.padding.x = 5;
    game.stage.addChild(game.global.logoText);

    // Setup music
    game.global.music = game.add.audio('menu');
    game.global.music.volume = 0.5;
    game.global.music.loop = true;
    game.global.music.play();

    var text = game.add.text(game.world.centerX + 1000, Math.floor(game.global.logoText.bottom), 'Select a Course', game.global.blackFont);
    game.add.tween(text).to({x: Math.floor(game.world.centerX - (text.width/2))}, 100, Phaser.Easing.Default, true, 0);
    // text.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
    text.padding.x = 5;

    var courses = [];
    $(function (){
      $.ajax({ //get list of courses from the database, and create a button for each one
        url: 'db-getRegisteredCourses.php', 
        success: function(data){
          courses = $.parseJSON(data);
          var prevHeights = 10 * dpr;
          for (var i = 0; i < courses.length; i++) {
            var enabled = false;
            if (courses[i].hidden == 0) {
              enabled = true;
            }
            console.log(enabled);

            var b = game.world.add(new game.global.SpeechBubble(game, 
              game.world.width + 1500, 
              text.bottom, 
              game.world.width * .8, 
              courses[i].course_name, 
              false, 
              enabled, 
              menuCourseState.courseBtnClick));
            b.y += prevHeights;
            prevHeights += b.bubbleheight + 10 *dpr;
            b.data.course = courses[i];

            //Add "(hidden)" text under button if course is hidden
            if (courses[i].hidden == 1) {
              var courseText = "(hidden)";
              var courseDesc = game.add.text(b.x, 
                b.y + b.bubbleheight, 
                courseText, 
                game.global.smallerBlackFont);
              // courseDesc.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
              courseDesc.padding.x = 5;
              prevHeights += courseDesc.height;
              game.add.tween(courseDesc).to({x: Math.floor(game.world.centerX - courseDesc.width/2)}, 
                350, Phaser.Easing.Default, true, 150 * i);
            }

            //animate button coming in
            game.add.tween(b).to({x: Math.floor(game.world.centerX - b.bubblewidth/2)}, 200, Phaser.Easing.Default, true, 250 * i);
          }
        }
      });
    });
    if(typeof(game.global.pauseButton) == "undefined"){ //check if pause button already exists in case we're coming back to this state again; if it doesn't exist, create it here
      game.global.pauseButton = game.world.add(new game.global.SpeechBubble(game, game.width, 0, game.width, '\uE8B8', false, true, game.global.pauseMenu));
      game.global.pauseButton.x -= game.global.pauseButton.bubblewidth + game.global.borderFrameSize;
      game.stage.addChild(game.global.pauseButton);

      game.global.unpauseButton = game.world.add(new game.global.SpeechBubble(game, game.global.pauseButton.x, game.global.pauseButton.y, game.width, 'I>', false, true, game.global.unpause));
      game.global.unpauseButton.visible = false;
      game.stage.addChild(game.global.unpauseButton);

      game.global.pauseButton.visible = true;
    }
  },

  courseBtnClick: function(){  
    game.global.selectedCourse = this.data.course.course_id;
    game.global.selectedCourseName = this.data.course.course_name;
    console.log('selected course id: ' + game.global.selectedCourse);
    $(function (){
      $.ajax({
        type: 'POST',
        url: 'setcourse.php',
        data: { course: game.global.selectedCourse },
        success: function(data){
          //setcourse.php returns the session again with the course added
          game.global.session = $.parseJSON(data);
          game.state.start('menuChapter');
        }
      });
    });
  }
 }
