// James: to choose host sprite and name, use it for different hosts later
var challenges = [
    { challenge_name: 'Keep Choosing', hostSprite: 'jackie', hostName: 'Jackie'},
    { challenge_name: 'One Crack', hostSprite: 'jackie', hostName: 'Jackie' },
    { challenge_name: 'One Crack Time Bonus', hostSprite: 'jackie', hostName: 'Jackie'},
    { challenge_name: 'Choose 1, 2, or 3', hostSprite: 'jackie', hostName: 'Jackie'},
    { challenge_name: 'Big Money', hostSprite: 'jackie', hostName: 'Jackie'},
    { challenge_name: 'Mystery Multiplier', hostSprite: 'jackie', hostName: 'Jackie'}
];

// James: pre-defined host speech.
var hostComments = {
    right: ["That's correct", "Well done", "Good job", "Nice going", "Nice!", "Yes!", "You betcha", "Good guess", "Right!", "You got it!", "Impressive", "Exactly!", "Great", "Awesome choice!", "Excellent!", "Couldn’t be better!"],
    wrong: ["Oh no", " Not quite", "Sorry", "Incorrect", "That's a miss", "Too bad", "Unfortunate", "That's not it", "Nope", "Uh-uh", "Ouch", "You can do better!", "It’s almost", "Not exactly", "Not that"],
    firstMessage: ["Here comes your first question...", "Let’s get started", "This is the first one"],
    rehashRound: ["Welcome to the rehash round!\nThis is a second chance to earn some points on the questions you answered incorrectly.\nCorrect answers are worth 5 points, and your opponents are sitting out this round."],
    notMuchTime: ["Not much time left!", "A second left!", "Hurry up please!"],
    timeUp: ["Time's up!", "Oh no, time is over!"],
    selectOneAnswer: ["Please select at least one answer.", "Oh, you didn’t choose!"]
};

// James: KeepChoosing, Choose123, One Crack
var hostMindStates = [
    { min: 70, max: 100, mind: "Excellent!", label: "Excellent", gameOver: false, bonus: 50 },
    { min: 50, max: 69, mind: "Good!", label: "Good", gameOver: false, bonus: 0 },
    { min: 0, max: 49, mind: "Meh!", label: "Meh", gameOver: true, bonus: 0 }
];

// James: One crack TimeBonus
var hostMindStatesTB = [
    { min: 70, max: 100, mind: "You have earned a big bonus!", label: "Achievement", gameOver: false, bonus: 0 },
    { min: 50, max: 69, mind: "You may continue to the next village!", label: "Continue", gameOver: false, bonus: 0 },
    { min: 0, max: 49, mind: "You have been banished from the realm!", label: "Banishment", gameOver: true, bonus: 0 }
];

// James: Big Money
var hostMindStatesBM = [
    { min: 70, max: 100, mind: "You hit the jackpot - Big Money!", label: "Big Money", gameOver: false, bonus: 0 },
    { min: 50, max: 69, mind: "You earned some Big Money!", label: "Little Money", gameOver: false, bonus: 0 },
    { min: 0, max: 49, mind: "You're broke!", label: "No Money", gameOver: true, bonus: 0 }
];

var gameShowUIState = {
    init: function () {
        message: [];  // James: glbal in this state 

    }, 
    // James: host normal speech, some messages are picked randomly
    hostSpeech: function (messageInput) {
        if (game.global.hostSpeech) {
            game.global.hostSpeech.destroy();
        }
        // James: not tween effect, just put the message 
        if (hostComments[messageInput]) {
            var message = hostComments[messageInput][Math.floor(Math.random() * hostComments[messageInput].length)]
            game.global.hostSpeech = game.world.add(new game.global.SpeechBubble(game, game.global.host.right + (game.global.borderFrameSize * 2), game.global.chapterText.bottom, game.world.width - (game.global.host.width * 2), message, true, false, null, false, null, true));
        } else {
            game.global.hostSpeech = game.world.add(new game.global.SpeechBubble(game, game.global.host.right + (game.global.borderFrameSize * 2), game.global.chapterText.bottom, game.world.width - (game.global.host.width * 2), messageInput, true, false, null, false, null, true));
        }
    },
    // James: host tween speech, usually used for rule at the beginning of each round
    hostTweenSpeech: function (messageInput) {
        var tempMessage = [
            { msg: [messageInput] }
        ];
        var prevHeights = 0;
        var speechX = Math.floor(game.global.host.right + (game.global.borderFrameSize * 2));
        var speechWidth = Math.floor(game.world.width - (game.global.host.width * 1.5));
        var hostTweenSpeech = [];
        var bubbles = [];
        message = tempMessage[0].msg.slice();
        for (var i = 0; i < message.length; i++) {
            bubbles[i] = game.world.add(new game.global.SpeechBubble(game, speechX, game.global.chapterText.bottom + prevHeights, speechWidth, message[i], true, false, null, false, null, true));
            prevHeights += Math.floor(bubbles[i].bubbleheight + (10 * adjust_dpr));
            var w = bubbles[i].width;
            bubbles[i].width = 0;
            hostTweenSpeech[i] = game.add.tween(bubbles[i]).to({ width: w }, 500, Phaser.Easing.Default, false, (i == 0) ? 0 : 1000);
            if (i > 0) {
                hostTweenSpeech[i - 1].chain(hostTweenSpeech[i]);
            }
        }
        hostTweenSpeech[0].start();
    },
    // James: draw host image based on game type
    drawHost: function (challengename) {
        for (var i = 0; i < challenges.length; i++) {
            if (challengename == challenges[i].challenge_name) {  // test value 
                var hostSprite = challenges[i].hostSprite;
                var hostName = challenges[i].hostName;
                // instructLines = challenges[i].instructLines.slice(); 
            }
        };

        if (game.global.host) {
            game.global.host.destroy();
        }
        if (game.global.hostText) {
            game.global.hostText.destroy();
        }

        game.global.host = game.add.sprite(0, 0, hostSprite, 0);
        game.global.hostText = game.add.text(0, 0, hostName, game.global.smallerBlackFont);
        // game.global.hostText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
        game.global.hostText.padding.x = 5;

        if (dpr >= 2) game.global.host.scale.setTo(adjust_dpr / 4, adjust_dpr / 4);
        game.global.hostText.centerX = Math.floor(game.global.host.centerX);
        game.global.hostText.x = Math.floor(game.global.hostText.x);
        game.global.hostText.y = Math.floor(game.global.host.bottom);
    },
 
    // James: create and draw avartars, AI setting
    drawInitialAvartars: function (initYN) {  // James: (TODO) may use game.global.isRehash???
        // James: Player Avatars UI, should be separated from host part
        if (initYN == 'Y') { // James: check initial YN to use same avartars and names for a round
            game.global.chars = [];
            game.global.oppImageKeys = game.global.shuffleArray(game.global.oppImageKeys);
        }

        // James: set the AI's chance at the beginning of each round
        var winChances = [65, 75];  // temp 65, 75 is the original
        winChances = game.global.shuffleArray(winChances);
        winChances.unshift(0); //loop below doesn't use first index of winchances, so put garbage in there

        //Dirty fix for opponents being on screen for smaller devices
        game.global.imagecheck = game.add.sprite((game.width + game.width), (game.height + game.height), game.global.oppImageKeys[1].imageKey);
        if (dpr >= 2) game.global.imagecheck.scale.setTo(adjust_dpr / 4, adjust_dpr / 4);
        var image = game.global.imagecheck;

        for (var i = 0; i < 3; i++) { //create avatars and their score and name text 
            game.global.chars[i] = {};
            game.global.chars[i].name = game.add.text(0 - game.world.width, 0 - game.world.height, 'You', game.global.smallerWhiteFont);
            game.global.chars[i].name.fill = 0xffffff;
            game.global.chars[i].sprite = game.add.sprite(0 - game.world.width, (game.world.height - image.height - (game.global.chars[i].name.height)), (i == 0) ? 'opp' + game.global.session['avatarnum'] : game.global.oppImageKeys[i].imageKey);
            if (dpr >= 2) game.global.chars[i].sprite.scale.setTo(adjust_dpr / 4, adjust_dpr / 4);
            game.global.chars[i].score = 0;
            game.global.chars[i].scoreText = game.add.text(0 - game.world.width, 0 - game.world.height, '0', game.global.smallerWhiteFont);
            game.global.chars[i].scoreText.fill = 0xffffff; 
            if (game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") {
                // crown will be used for later
                // game.global.chars[i].crown = game.add.sprite(0 - game.world.width, Math.floor(game.global.chars[i].sprite.top - game.global.chars[i].sprite.height / 2), 'crown', 0);
                // if (dpr >= 2) game.global.chars[i].crown.scale.setTo(dpr / 4, dpr / 4);
                game.global.chars[i].numJewels = 0;
            }
            if (i != 0) { //more setup for non-player avatars 
                game.global.chars[i].name.text = game.global.oppImageKeys[i].name;  // name updated   
                game.global.chars[i].chance = winChances[i]; // James: AI chance to win 
                
            }
        }

        var avartartweens = [];
        for (var i = 0; i < game.global.chars.length; i++) {
            game.add.tween(game.global.chars[i].sprite).to({ x: Math.floor(((game.width / game.global.chars.length) * (i + 1) - game.width / game.global.chars.length) + (game.width / 25)) }, (initYN == 'Y') ? 800 : 1, Phaser.Easing.Default, true);

            // James: put Name and Crown on each Avatar
            game.global.chars[i].name.x = Math.floor(((game.width / game.global.chars.length) * (i + 1) - game.width / game.global.chars.length) + (game.width / 25)) + (10 * adjust_dpr) + game.global.chars[i].sprite.width;
            game.global.chars[i].name.y = Math.floor(game.global.chars[i].sprite.centerY + (10 * adjust_dpr));
            // if (game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") {
            //     game.global.chars[i].crown.centerX = Math.floor(((game.width / game.global.chars.length) * (i + 1) - game.width / game.global.chars.length) + (game.width / 25)) + game.global.chars[i].sprite.width / 2;
            // }
        }
 
    },
 
    // James: animates scores and keeps score text and names positioned near their respective avatars, should be called in update at play.js
    updateScoreUI: function () {
        for (var i = 0; i < game.global.chars.length; i++) {
            var n = parseInt(game.global.chars[i].scoreText.text); // James: match score text up to the latest points

            // James: animation effect but should be called at update
            if (n < game.global.chars[i].score) {
                n++;
                game.global.chars[i].scoreText.text = n;
            }

            game.global.chars[i].scoreText.x = Math.floor(((game.width / game.global.chars.length) * (i + 1) - game.width / game.global.chars.length) + (game.width / 25)) + (10 * adjust_dpr) + game.global.chars[i].sprite.width;
            //   game.global.chars[i].scoreText.x = Math.floor(game.global.chars[i].sprite.right + game.global.borderFrameSize); 
            game.global.chars[i].scoreText.y = Math.floor(game.global.chars[i].sprite.centerY + (11 * adjust_dpr));
            //   game.global.chars[i].name.x = Math.floor(game.global.chars[i].sprite.centerX - game.global.chars[i].name.width/2);
            game.global.chars[i].name.x = Math.floor(((game.width / game.global.chars.length) * (i + 1) - game.width / game.global.chars.length) + (game.width / 25)) + game.global.chars[i].sprite.width / 2 - game.global.chars[i].name.width / 2;
            game.global.chars[i].name.y = Math.floor(game.global.chars[i].sprite.bottom);
        }
    },
    // James: show current course at the top-right
    course_text: function () {
        game.global.courseText = game.add.text(game.global.pauseButton.left, game.world.y, game.global.selectedCourseName, game.global.smallerBlackFont);
        game.global.courseText.x = Math.round(game.global.courseText.x - game.global.courseText.width - game.global.borderFrameSize);
        // game.global.courseText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
        game.global.courseText.padding.x = 5;
    },
    // James: show current course & chapter at the top-right
    course_chapter_text: function () { // James: can be placed in previous step and divided into 2, chapter , course repectively for reuse 
        this.course_text();
        game.global.chapterText = game.add.text(game.global.pauseButton.left, Math.floor(game.global.courseText.bottom - 5), 'Chapter ' + game.global.selectedChapter, game.global.smallerBlackFont);
        game.global.chapterText.x = Math.round(game.global.chapterText.x - game.global.chapterText.width - game.global.borderFrameSize);
        // game.global.chapterText.setShadow(2, 2, 'rgba(0,0,0,0.5)', 5); // James: Marty wants to remove
        game.global.chapterText.padding.x = 5;
    },
    // James: display AI's answer based on correct value
    showAIAnswers: function () { 
        // if (!game.global.answersShown) {
          game.global.chars[0].timesAnswered++; 
            for (i = 1; i < game.global.chars.length; i++) { 
              if(!game.global.chars[i].isGetPoint){
                game.global.chars[i].timesAnswered++;  // count the trial
                if(game.global.chars[i].answerBubble){  // James: control AI answer  
                  game.global.chars[i].answerBubble.destroy(); 
                }
                if (game.global.chars[i].correct) {  // right then put real answer
                    game.global.chars[i].answerBubble = game.world.add(new game.global.SpeechBubble(game, Math.floor(((game.width / game.global.chars.length) * (i + 1) - game.width / game.global.chars.length) + (game.width / 25)) + (10 * adjust_dpr) + game.global.chars[i].sprite.width, Math.floor(game.global.chars[i].sprite.centerY - 40), game.world.width, game.global.currentQuestion.newAnswer, true, false));
                    game.global.chars[i].isGetPoint = true;   // James: to check whether AI gets the point on current question
                  } else {
                    var availChoices = [];
                    var j = 0;
                    for (var c in game.global.currentQuestion.choices) {
                        availChoices[j] = c;
                        j++;
                    }
                    var wrongChoice = availChoices[Math.floor(Math.random() * availChoices.length)];
                    var answer = game.global.currentQuestion.newAnswer;

                    //strip any whitespace so comparisons will work
                    answer = answer.replace(/(^\s+|\s+$)/g, "");
                    wrongChoice = wrongChoice.replace(/(^\s+|\s+$)/g, "");

                    //randomize answer so it isn't the correct one.
                    while (wrongChoice == answer) {
                        wrongChoice = availChoices[Math.floor(Math.random() * availChoices.length)];
                    }
                    console.log(wrongChoice);
                    game.global.chars[i].answerBubble = game.world.add(new game.global.SpeechBubble(game, Math.floor(((game.width / game.global.chars.length) * (i + 1) - game.width / game.global.chars.length) + (game.width / 25)) + (10 * adjust_dpr) + game.global.chars[i].sprite.width, Math.floor(game.global.chars[i].sprite.centerY - 40), game.world.width, wrongChoice, true, false));
                }
                //save width so we can set to 0 and tween to it later
                game.global.answerBubbleWidth = game.global.chars[i].answerBubble.width;
                game.global.chars[i].answerBubble.width = 0;
                game.global.answerBubbles.add(game.global.chars[i].answerBubble);

                game.add.tween(game.global.chars[i].answerBubble).to({ width: game.global.answerBubbleWidth }, 100, Phaser.Easing.Default, true, 250 * i);
                game.global.questionUI.add(game.global.chars[i].answerBubble);  // James: to destory before newQuestion();
              }
                
            }
        // }
        // game.global.answersShown = true;  when user gets right
    }, 
    // James: Bar during the game to show current score
    makeBars: function () {
      var prevHeightsBtns = game.global.host.bottom + 100;  // space to decide the top of the graph
        for (var i = 0; i < game.global.chars.length; i++) { 
 
            game.global.chars[i].scorePercentPoints = (game.global.chars[i].numCorrectQuestion / game.global.roundNumOfQuestions ) * 100;
            var y = game.global.mapNum(game.global.chars[i].scorePercentPoints, 0, 100, game.global.chars[i].sprite.y, prevHeightsBtns + 5);  
            game.add.tween(game.global.chars[i].barSprite).to({ height: Math.max(game.global.chars[i].sprite.y - y, 1) }, 1000, Phaser.Easing.Default, true, 0); 
        }
    },
    // James: Bar at the end of each round
    makeEndingRoundBar: function () {
        // James: this will draw the result bar for each round's result  
        var mindStates;
        if (game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") {
            mindStates = hostMindStatesTB.slice();
        } else if (game.global.gameSettings.currentChallengeName == "Big Money") {
            mindStates = hostMindStatesBM.slice();
        } else {
            mindStates = hostMindStates.slice();
        } 
        var mindStateToUse;
        // set up visual areas for score ranges
        for (var i = 0; i < mindStates.length; i++) {
            var percent = Math.floor((game.global.chars[0].numCorrectQuestion / game.global.roundNumOfQuestions ) * 100); 
         
            if (percent >= mindStates[i].min && percent <= mindStates[i].max) {
                mindStateToUse = mindStates[i];
                break;
            }
        }
        var winningScore = 0;
        for (var i = 0; i < game.global.chars.length; i++) { // calculate top score first for tie purposes
            game.global.chars[i].scorePercent = Math.floor((game.global.chars[i].numCorrectQuestion / game.global.roundNumOfQuestions ) * 100); // James: based one the correct question(first trial)
 
            winningScore = Math.max(winningScore, game.global.chars[i].scorePercent); // James: based on the first trial correct choice
 
        }
 
        var prevHeightsBtns = game.global.host.bottom + 100; // James: change it as using the last button.bottom;
        this.hostSpeech(mindStateToUse.mind); 

        // convert score + progress bars to percentage
        for (var i = 0; i < game.global.chars.length; i++) {
          // remove previous bars
            game.global.chars[i].barSprite.destroy();
            // James: set the sprite
            game.global.chars[i].gfx = game.add.graphics(0, 0);
            game.global.chars[i].gfx.visible = false;
            game.global.chars[i].gfx.beginFill(0x02C487, 1);
            // game.global.chars[i].gfx.drawRect(game.global.chars[i].sprite.x, (game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") ? game.global.chars[i].crown.y : game.global.chars[i].sprite.y, game.global.chars[i].sprite.width, 1);
            game.global.chars[i].gfx.drawRect(game.global.chars[i].sprite.x, game.global.chars[i].sprite.y, game.global.chars[i].sprite.width, 1);  // draw thin line
            // game.global.chars[i].barSprite = game.add.sprite(game.global.chars[i].sprite.x, (game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") ? game.global.chars[i].crown.y : game.global.chars[i].sprite.y, game.global.chars[i].gfx.generateTexture());
            game.global.chars[i].barSprite = game.add.sprite(game.global.chars[i].sprite.x, game.global.chars[i].sprite.y, game.global.chars[i].gfx.generateTexture());
            game.global.chars[i].barSprite.anchor.y = 1;
            
            // if you want to use bar progress with scores
            // var topBar = Math.min(game.global.chars[i].score, game.global.roundNumOfQuestions * game.global.gameSettings.pointValue);
            // var scorePercent = Math.floor(((topBar) / (game.global.roundNumOfQuestions * game.global.gameSettings.pointValue)) * 100);
            
            // James: guessing strarting y position for bar, refer to game.js
            // var y = game.global.mapNum(scorePercent, 0, 100, (game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") ? game.global.chars[i].crown.y : game.global.chars[i].sprite.y, prevHeightsBtns + 5);
            var y = game.global.mapNum(game.global.chars[i].scorePercent, 0, 100, game.global.chars[i].sprite.y, prevHeightsBtns + 5);
            // var y = game.global.mapNum(scorePercent, 0, 100, (game.global.selectedMode.id == 0) ? game.global.chars[i].crown.y : game.global.chars[i].sprite.y, 5);
            // scorePercentLabel = game.add.bitmapText(game.global.chars[i].sprite.centerX, (game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") ? game.global.chars[i].crown.y : game.global.chars[i].sprite.y, '8bitoperator', scorePercent + '%', 11 * dpr);
            scorePercentLabel = game.add.bitmapText(game.global.chars[i].sprite.centerX, game.global.chars[i].sprite.y, '8bitoperator', game.global.chars[i].scorePercent + '%', 11 * adjust_dpr);
            scorePercentLabel.x = Math.floor(game.global.chars[i].sprite.centerX - scorePercentLabel.width / 2);
            // scorePercentLabel.y = Math.floor(((game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") ? game.global.chars[i].crown.y : game.global.chars[i].sprite.y) - (scorePercentLabel.height * 2));
            scorePercentLabel.y = Math.floor(game.global.chars[i].sprite.y - scorePercentLabel.height * 2);  // game.global.chars[i].sprite.y is the top of green bar
            scorePercentLabel.tint = 0x000044; 
            game.add.tween(scorePercentLabel).to({ y: y }, 500, Phaser.Easing.Default, true, 250); //game.global.chars[i].numJewels
            // game.add.tween(game.global.chars[i].barSprite).to({ height: Math.max(((game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") ? game.global.chars[i].crown.y : game.global.chars[i].sprite.y) - y, 1) }, 500, Phaser.Easing.Default, true, 250);
           
            game.add.tween(game.global.chars[i].barSprite).to({ height: Math.max(game.global.chars[i].sprite.y - y, 1) }, 500, Phaser.Easing.Default, true, 250);
     
            if (game.global.chars[i].scorePercent >= winningScore && game.global.chars[i].scorePercent > 0) {   // James: must be the same result as with ==
                // var medal = game.add.sprite(game.global.chars[i].sprite.x, (game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") ? game.global.chars[i].crown.y : game.global.chars[i].sprite.y, 'medal');
                var medal = game.add.sprite(game.global.chars[i].sprite.x, game.global.chars[i].sprite.y, 'medal');
                medal.width = game.global.chars[i].sprite.width;
                medal.height = game.global.chars[i].sprite.height; 
                game.add.tween(medal).to({ y: y + (scorePercentLabel.height * 2) }, 500, Phaser.Easing.Default, true, 250); 

            }
            // James: if player wins, play music 
            if (game.global.chars[0].scorePercent >= winningScore && game.global.chars[0].scorePercent> 0 ) {
                game.global.playerWinSound.play();
            }
            else {
                game.global.endOfGameSound.play();
            }
        }

        var lineGfx = game.add.graphics(0, 0);
        // this.endGameUI.add(lineGfx);  
        lineGfx.lineStyle(1, 0x333333, 1);

        //loop mindstates again to add the labels on top of the progress bars ex) Excellent, Good, Meh
        for (var i = 0; i < mindStates.length; i++) {
            // var lineYposition = game.global.mapNum(mindStates[i].max, 0, 100, (game.global.gameSettings.currentChallengeName == "One Crack Time Bonus") ? game.global.chars[0].crown.y : game.global.chars[0].sprite.y, prevHeightsBtns + 5); // James: original
            var lineYposition = game.global.mapNum(mindStates[i].max, 0, 100, game.global.chars[0].sprite.y, prevHeightsBtns + 5); // James: original
            // var lineYposition = game.global.mapNum(mindStates[i].max, 0, 100, (game.global.selectedMode.id == 0) ? game.global.chars[0].crown.y : game.global.chars[0].sprite.y, 5);
            lineGfx.moveTo(0, lineYposition);
            lineGfx.lineTo(game.world.width, lineYposition);
            var label = game.add.text(game.world.centerX, lineYposition, mindStates[i].label, game.global.BlackFont);
            label.x -= label.width / 2; 
            label.padding.x = 5;
            label.z++; 
        }
    }
};

