<?php
include_once("../classes/class.users.php");
$obj = new users();
$photo = $obj->fetchPhoto();
echo json_encode($photo);

?>
