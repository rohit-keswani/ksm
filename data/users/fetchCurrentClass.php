<?php
	include_once("../classes/class.users.php");

	$obj = new users();
	$data = $obj->fetchCurrentClass();
  $class = $data[0]['class_id'];
  $returnObj = [];
    if ($class < 5){
      $returnObj['class'] = "First Year";
      $returnObj['division'] = $data[0]['division'];
    }
    elseif ($class > 4 && $class <9) {
      $returnObj['class'] = "Second Year";
      $returnObj['division'] = $data[0]['division'];

    }
    elseif ($class > 8 && $class <13) {
      $returnObj['class'] = "Third Year";
      $returnObj['division'] = $data[0]['division'];

    }
    elseif ($class > 12 && $class <17) {
      $returnObj['class'] = "Fourth Year";
      $returnObj['division'] = $data[0]['division'];

    }
  echo json_encode($returnObj);

 ?>
