<?php 
 /*
  * MADE BY: James Choi Jones
  * PURPOSE: question data in json is inserted into question & answer table
  */
function insertIntoDB(&$questionBank,$insertChapter,$courseid,$dbcon, &$index){   
    $insertQuestion =  $questionBank["question"];
    $choices = $questionBank["choices"];
    $correct = $questionBank["answer"]; 
    $stmt = $dbcon->prepare('INSERT INTO question (course_id_fk, chapter_id_fk, question_id, question) 
                             SELECT :courseid, :chapter, (SELECT COUNT(1)+1 FROM question
                                                    WHERE course_id_fk = :courseid 
                                                     AND chapter_id_fk = :chapter)
                                                  , :question');
 
	$stmt->bindParam(':question', $insertQuestion); 
	$stmt->bindParam(':chapter', $insertChapter);
	$stmt->bindParam(':courseid', $courseid);
	if($stmt->execute()){ // questions is ok then insert data into child 
          foreach($choices as $key => $value){  
            // James: move into above 
            $stmt = $dbcon->prepare('INSERT INTO answer (course_id_fk, chapter_id_fk, question_id_fk, answer_id, answer, correct)
                                    SELECT :courseid, :chapter, (SELECT COUNT(1) 
                                                                    FROM question
                                                                  WHERE course_id_fk = :courseid 
                                                                    AND chapter_id_fk = :chapter), 
                                                                (SELECT COUNT(1)+1 
                                                                    FROM answer
                                                                    WHERE course_id_fk = :courseid 
                                                                    AND chapter_id_fk = :chapter 
                                                                    AND question_id_fk = (SELECT COUNT(1) 
                                                                                            FROM question
                                                                                            WHERE course_id_fk = :courseid 
                                                                                              AND chapter_id_fk = :chapter)), :answer , :isCorrect');

            $stmt->bindParam(':answer', $choices[$key]); 
            $stmt->bindParam(':chapter', $insertChapter);
            $stmt->bindParam(':courseid', $courseid);
            if($correct == $key){
                $isCorrect = 1;
            } else {
                $isCorrect = 0; 
            }
            $stmt->bindParam(':isCorrect', $isCorrect);

            if($stmt->execute()){ 
            }else{
                print_r($stmt->errorInfo()); 
            }  
        }
        // if the transaction is done , then reset 
        $index += 1;
        unset($questionBank); 
        $questionBank = array();
    }else{
        print_r($stmt->errorInfo()); 
    } 
   
}
?>
