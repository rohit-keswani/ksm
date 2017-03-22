<?php
include_once("../classes/class.proctorFunctions.php");
include_once("../classes/class.users.php");
$obj = new proctor();
$obj1 = new users();
$classId = $obj1->fetchCurrentClass();
$data = $obj->fetchCertificates($classId[0]['class_id']);
$cert = [];
$type = array(array("Workshop","Seminar","Project","Paper Presentation",0,0,0,"Other"),
          array("Sports","Gathering","Art Circle","Technical Events","Other"),
          array("Technical","Foreign Language","Softskills","Aptitiude","Stress Management Courses","Other"),
          "Certifications",
          array("Industrial","Sabbatical","Project Work","Workshops","Others"),
          array("GATE","GRE","CAT","TOEFL","GMAT","Others"),
          "Placements",
          "Others");
 $activity = array("Co-curricular","Extra-curricular","Add-on","Certifications","Training","Entrance Examinations","Placements","Others");

          foreach ($data as $key => $value) {
            if ($data[$key]['type_of_activity'] == 4) {
              $cert[$key]['name'] = $activity[3];
              $cert[$key]['id'] = $data[$key]['sr_no'];
              $cert[$key]['activity'] = $data[$key]['specs'];
              $cert[$key]['details'] = $data[$key]['details_performance'];
            }
            elseif ($data[$key]['type_of_activity'] == 7) {
              $cert[$key]['name'] = $activity[6];
              $cert[$key]['id'] = $data[$key]['sr_no'];
              $cert[$key]['activity'] = $data[$key]['specs'];
              $cert[$key]['details'] = $data[$key]['details_performance'];
            }
            elseif ($data[$key]['type_of_activity'] == 8) {
              $cert[$key]['name'] = $activity[7];
              $cert[$key]['id'] = $data[$key]['sr_no'];
              $cert[$key]['activity'] = $data[$key]['specs'];
              $cert[$key]['details'] = $data[$key]['details_performance'];
            }
            else {
              $cert[$key]['name'] = $activity[$data[$key]['type_of_activity']-1];
              if($data[$key]['activity'] == 8){
                  $cert[$key]['activity'] = $data[$key]['specs'];
              }
              else{
                  $cert[$key]['activity'] = $type[$data[$key]['type_of_activity']-1][$data[$key]['activity']-1];
              }
              $cert[$key]['id'] = $data[$key]['sr_no'];
              $cert[$key]['details'] = $data[$key]['details_performance'];

            }
          }
          echo json_encode ($cert);



?>
