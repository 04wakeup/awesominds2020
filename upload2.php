<?php
// Error_reporting(E_ALL);
// ini_set('display_errors',1); 
/* James: Notes 
   (1) we may change php.ini file then restart
    upload_max_filesize=100M (as per your need)
    post_max_size = 100M (as per your need)
   (2) /tmp can't be used(bug?), then use another path (ex. /var/www/html/uploads) and check the permissoin for apache
   (3) apache OS account  will manage the file control.
*/
require('includes/conn.php');
include('redir-notinstructor.php');
include 'readQuestions.php';
include 'parseDoc.php';
include 'db-insert.php';

$upload_path = '/tmp/';  // James: use accessible folder
// $temp_file = tempnam(sys_get_temp_dir(), 'qs');
$temp_file = tempnam($upload_path, 'qs');
$temp_file_text = $temp_file;

$target_file = basename($_FILES["fileToUpload"]["name"]);  // real file name
$filetype = pathinfo($target_file, PATHINFO_EXTENSION);  // doc or txt 

$goodToParse = 0;
$removeFile = 0;
$errorUpload = 0;

$target_file = $upload_path . $target_file; 
 
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    exit;  //try detect AJAX request, simply exist if no Ajax
}

if(!isset($_FILES['fileToUpload']) || !is_uploaded_file($_FILES['fileToUpload']['tmp_name'])){
   die('file is Missing!');
}

switch($filetype){
    case 'doc':
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){  // James: check 2nd(target file) is full path like /tmp/filename.doc
            read_doc($target_file, $temp_file); 
            $goodToParse = 1;
            $removeFile = 1;
        } else {
            $errorUpload = 1;
        }
        break;
    case 'txt':
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)){
            $temp_file = $target_file;
            $removeFile = 1;
            $goodToParse = 1; 
        } else {
            $errorUpload = 1;
        }
        break;
    default:
        die($filetype . "  formats are not currently supported");
        $goodToUpload = 0;
        break;
}
//on succesful upload call appropriate functions

if($errorUpload){
    die("Error uploading File");
}


if($goodToParse){ 
    tmpToDb($temp_file,$dbcon);
    if($removeFile){
      unlink($temp_file_text);
      unlink($temp_file);
    }
}
?>
