<?php
  require('includes/conn.php');
  include('redir-notloggedin.php');

    /*
  * MADE BY: James Choi
  * PURPOSE: extract the whole question information and covert it in Json
    NOTE: to future teams: you may be confused as to why this file grabs the data in the way it does.
    previous teams stored all the data retrieved into the "question" field below (so the question, comment, choices, and answer)
    in a single column in the database, (horrible awful idea, dont do it), since the phaser code is designed
    to handle the old version of the data, and we don't have time to fix it, this file converts the data in our new
    good database into the old bad version. - Walker
*/

  $query = $dbcon->prepare("SELECT s.question_id questionid, 
                    CASE WHEN COUNT_OF_ANSWER = 6 THEN CONCAT('{\"', 'question', '\":\"', s.question, '\",\"', 'comment', '\":\"', s.comment, '\",\"', 'choices', '\":{\"A\":\"', s.A, '\",\"B\":\"', s.B, '\",\"C\":\"', s.C, '\",\"D\":\"', s.D, '\",\"E\":\"', s.E, '\",\"F\":\"', s.F,'\"},', '\"answer\":\"', s.ANSWER_NUM, '\"}') 
                          WHEN COUNT_OF_ANSWER = 5 THEN CONCAT('{\"', 'question', '\":\"', s.question, '\",\"', 'comment', '\":\"', s.comment, '\",\"', 'choices', '\":{\"A\":\"', s.A, '\",\"B\":\"', s.B, '\",\"C\":\"', s.C, '\",\"D\":\"', s.D, '\",\"E\":\"', s.E, '\"},', '\"answer\":\"', s.ANSWER_NUM, '\"}') 
                          WHEN COUNT_OF_ANSWER = 4 THEN CONCAT('{\"', 'question', '\":\"', s.question, '\",\"', 'comment', '\":\"', s.comment, '\",\"', 'choices', '\":{\"A\":\"', s.A, '\",\"B\":\"', s.B, '\",\"C\":\"', s.C, '\",\"D\":\"', s.D, '\"},', '\"answer\":\"', s.ANSWER_NUM, '\"}') 
                          WHEN COUNT_OF_ANSWER = 3 THEN CONCAT('{\"', 'question', '\":\"', s.question, '\",\"', 'comment', '\":\"', s.comment, '\",\"', 'choices', '\":{\"A\":\"', s.A, '\",\"B\":\"', s.B, '\",\"C\":\"', s.C, '\"},', '\"answer\":\"', s.ANSWER_NUM, '\"}') 
                          WHEN COUNT_OF_ANSWER = 2 THEN CONCAT('{\"', 'question', '\":\"', s.question, '\",\"', 'comment', '\":\"', s.comment, '\",\"', 'choices', '\":{\"A\":\"', s.A, '\",\"B\":\"', s.B, '\"},', '\"answer\":\"', s.ANSWER_NUM, '\"}') 
                          ELSE 'ERROR NOT BETWEEN 2-6 ANSWERS' END AS question
              FROM (
                      SELECT q.question_id AS question_id, max(q.question) AS question, max(q.comment) AS comment,
                          max(if(a.answer_id = 1 , a.answer, NULL)) AS A, max(if(a.answer_id = 2 , a.answer, NULL)) AS B,
                          max(if(a.answer_id = 3 , a.answer, NULL)) AS C, max(if(a.answer_id = 4 , a.answer, NULL)) AS D, 
                          max(if(a.answer_id = 5 , a.answer, NULL)) AS E, max(if(a.answer_id = 6 , a.answer, NULL)) AS F,
                          max(if(a.correct = 1 , if(a.answer_id = 1, 'A', if(a.answer_id=2, 'B', if(a.answer_id=3, 'C', if(a.answer_id=4, 'D', if(a.answer_id=5, 'E', 'F'))))), NULL)) AS ANSWER_NUM,
                          count(1) AS COUNT_OF_ANSWER 
                        FROM question q, answer a 
                        WHERE q.course_id_fk = :course_id 
                        AND q.chapter_id_fk = :chapter_id
                        AND q.course_id_fk = a.course_id_fk
                        AND q.chapter_id_fk = a.chapter_id_fk
                        AND q.question_id = a.question_id_fk
                        GROUP BY q.question_id
                    ) s"); 
  $query->bindParam(':course_id', $_POST["course_id"]);
  $query->bindParam(':chapter_id', $_POST["chapter_id"]); //NOTE: make sure the ajax call in pregame uses these post variable names
  $query->execute();

  $result = $query->fetchAll(PDO::FETCH_ASSOC);
  echo json_encode($result);
?>
