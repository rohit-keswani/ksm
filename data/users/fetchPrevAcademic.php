<?php
	include_once("../classes/class.users.php");

	$obj = new users();
	$data = $obj->fetchPrevAcademic();
  echo json_encode($data);

 ?>
