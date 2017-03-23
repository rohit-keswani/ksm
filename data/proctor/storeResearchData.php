<?php
include_once("../classes/class.proctorFunctions.php");
include_once("../classes/class.users.php");
$post = file_get_contents("php://input");
$entryData = json_decode($post,true);
$obj1 = new users();
$class = $obj1->fetchCurrentClass();
$obj = new proctor();

$obj->saveResearchData($entryData[0],$entryData[1],$entryData[2],$class[0]['class_id']);
?>
