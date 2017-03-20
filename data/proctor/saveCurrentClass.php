<?php
include_once("../classes/class.proctorFunctions.php");
$post = file_get_contents("php://input");
$entryData = json_decode($post);
$length = sizeof($entryData);
$obj = new proctor();
$class = $obj->getID(2,$entryData);
$obj->saveCurrentClass($entryData,$class[0]['prim_id']);

?>
