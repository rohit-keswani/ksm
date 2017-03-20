<?php
include_once("../classes/class.proctorFunctions.php");
include_once("../classes/class.users.php");
$post = file_get_contents("php://input");
$entryData = json_decode($post,true);
$length = sizeof($entryData);
$obj1 = new users();
$classId = $obj1->fetchCurrentClass();
$obj = new proctor();

$obj->saveExtraEfforts($entryData,$classId[0]['class_id']);

?>
