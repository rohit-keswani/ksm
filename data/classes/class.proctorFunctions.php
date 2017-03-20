<?php session_start();
include_once('class.dbinfo.php');

/**
* 	Name : Proctor Functions
*	Author :
*	Date : 25/1/2017
*	Version : 1.1
*	Application : ---KSM Phase 1---
*/
/**

*/


class proctor {
  public function __construct(){
    global $dsn;
    global $user;
    global $password;

    try{
      $this->pdo = new PDO($dsn,$user,$password);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e){
      echo 'Connection Failed'.$e->getMessage();
    }
  }

  public function getID($columns,$entryData){

    if($columns == 2){
      $class = $entryData->class;
      // $class = $entryData['class'];
      $sem = $entryData->sem;
      // $sem = $entryData['sem'];


      $stmt = $this->pdo->prepare("SELECT prim_id FROM PM010004 WHERE class = :class AND sem = :sem");
      $stmt->bindParam(":class",$class,PDO::PARAM_STR);
      $stmt->bindParam(":sem",$sem,PDO::PARAM_STR);
      $stmt->execute();
      $count = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_SESSION['classID'] = $count[0]['prim_id'];
      return $count;
      }
    if ($columns == 3) {
      $class = $entryData->class;
      // $class = $entryData['class'];
      $sem = $entryData->sem;
      // $sem = $entryData['sem'];
      $ut = $entryData->ut;
      // $ut = $entryData['ut'];

      $stmt = $this->pdo->prepare("SELECT prim_id FROM PM010004 WHERE class = :class AND sem = :sem AND unit_test =:unit_test");
      $stmt->bindParam(":class",$class,PDO::PARAM_STR);
      $stmt->bindParam(":sem",$sem,PDO::PARAM_STR);
      $stmt->bindParam(":unit_test",$ut,PDO::PARAM_STR);
      $stmt->execute();
      $count = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $_SESSION['classID'] = $count[0]['prim_id'];
      return $count[0]['prim_id'];
    }



  }

    public function checkEntry($table,$classId){
      $stmt = $this->pdo->prepare("SELECT prim_id,approved FROM ".$table." WHERE class_id = :class_id");
      $stmt->bindParam(":class_id",$classId,PDO::PARAM_STR);
      $stmt->execute();
      $dbData = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $count = $stmt->rowCount();
      if ($count == 1) {
        $_SESSION['approved'] = $dbData[0]['approved'];
      }
      echo json_encode($count);
    }
    public function checkEntryAttendance($table,$classId){
      $fnight = "f_".$classId[0]['fortnight'];
      $null = 0;
      $stmt = $this->pdo->prepare("SELECT prim_id,approved,".$fnight." FROM ".$table." WHERE class_id = :class_id");
      $stmt->bindParam(":class_id",$classId[0]['prim_id'],PDO::PARAM_STR);
      $stmt->execute();
      $dbData = $stmt->fetchAll(PDO::FETCH_ASSOC);
      $count = $stmt->rowCount();
      if ($count == 1) {
        $_SESSION['approved'] = $dbData[0]['approved'];
        if ($dbData[0][$fnight] == 0) {
          echo json_encode(0);
          $_SESSION['fnight_column'] = $fnight;
          $_SESSION['fnight_count'] = $count;
        }
        else {
          $_SESSION['fnight_column'] = $fnight;
          $_SESSION['fnight_count'] = $count;
          echo json_encode($count);
        }
      }
      else{
        $_SESSION['fnight_column'] = $fnight;
        $_SESSION['fnight_count'] = $count;
        echo json_encode($count);
      }
    }

    public function saveExtraEfforts($data,$classID){
    $arr = array();
    foreach ($data as $key => $value) {
        if ($data[$key]['type']==8 || $data[$key]['activity'] == 8 || $data[$key]['activity'] == 7 || $data[$key]['activity'] == 4) {
          $type = "0";
          $specs = $data[$key]['specs'];
        }
        else {
          $type = $data[$key]['type'];
          $specs = "0";
        }
        $stmt = $this->pdo->prepare("INSERT INTO PM010015 (student_id,class_id,type_of_activity,activity,specs,details_performance) VALUES (:student_id,:class_id,:type_of_activity,:activity,:specs,:details_performance)");
        $stmt->bindParam(":student_id",$_SESSION['user_plexus_id'],PDO::PARAM_STR);
        $stmt->bindParam(":class_id",$classID,PDO::PARAM_STR);
        $stmt->bindParam(":type_of_activity",$data[$key]['activity'],PDO::PARAM_STR);
        $stmt->bindParam(":activity",$type,PDO::PARAM_STR);
        $stmt->bindParam(":specs",$specs,PDO::PARAM_STR);
        $stmt->bindParam(":details_performance",$data[$key]['perform'],PDO::PARAM_STR);
        $stmt->execute();
      }
      echo json_encode($arr);
    }

    public function saveCurrentClass($data,$classID){
      $activate = 1;
      $deactivate = 0;
      $stmt = $this->pdo->prepare("UPDATE PM010011 SET active = :active WHERE student_key_id = :student_key_id");
      $stmt->bindParam(":student_key_id",$_SESSION['user_plexus_id'],PDO::PARAM_STR);
      $stmt->bindParam(":active",$deactivate,PDO::PARAM_STR);
      $stmt->execute();

      $stmt = $this->pdo->prepare("INSERT INTO PM010011 (student_key_id,class_id,division,active) VALUES (:student_key_id,:class_id,:division,:active)");
      $stmt->bindParam(":student_key_id",$_SESSION['user_plexus_id'],PDO::PARAM_STR);
      $stmt->bindParam(":class_id",$classID,PDO::PARAM_STR);
      $stmt->bindParam(":division",$data->div,PDO::PARAM_STR);
      $stmt->bindParam(":active",$activate,PDO::PARAM_STR);
      $stmt->execute();
      echo " ";
    }
    public function storeAttendanceData($data){
      if ($_SESSION['fnight_count'] == 0) {
        $stmt = $this->pdo->prepare("INSERT INTO PM010007 (student_id,class_id,".$_SESSION['fnight_column'].") VALUES (:student_id,:class,:f_no)");
        $stmt->bindParam(":student_id",$_SESSION['studentId'],PDO::PARAM_STR);
        $stmt->bindParam(":class",$_SESSION['classID'],PDO::PARAM_STR);
        $stmt->bindParam(":f_no",$data->attendance,PDO::PARAM_STR);
        $stmt->execute();
      }
      else {
        $stmt = $this->pdo->prepare("UPDATE PM010007 SET ".$_SESSION['fnight_column']." =:attendance WHERE student_id = :student_id AND class_id = :class");
        $stmt->bindParam(":student_id",$_SESSION['studentId'],PDO::PARAM_STR);
        $stmt->bindParam(":class",$_SESSION['classID'],PDO::PARAM_STR);
        $stmt->bindParam(":attendance",$data->attendance,PDO::PARAM_STR);
        $stmt->execute();
      }

    }
    public function saveMeetingData($data){
        $count = sizeof($data);
        $_SESSION['student_id'] = "123123C2312";

        for ($i=0; $i < $count; $i++) {
          $stmt = $this->pdo->prepare("INSERT INTO PM010009 (student_id,class,date,discussion,remarks) VALUES (:student_id,:class,:date,:discussion,:remarks)");
          $stmt->bindParam(":student_id",$_SESSION['student_id'],PDO::PARAM_STR);
          $stmt->bindParam(":class",$_SESSION['classID'],PDO::PARAM_STR);
          $stmt->bindParam(":date",$data[$i]->dom,PDO::PARAM_STR);
          $stmt->bindParam(":discussion",$data[$i]->meetingDetails,PDO::PARAM_STR);
          $stmt->bindParam(":remarks",$data[$i]->remarks,PDO::PARAM_STR);
          $stmt->execute();
        }
    }

    public function storeUTData($approvalFlag,$data){
      $_SESSION['student_id'] = "123123C2312";
      if ($approvalFlag == 0) {
        $stmt = $this->pdo->prepare("INSERT INTO PM010005 (student_id,class_id,total_sub,appeared,passed,failed) VALUES (:student_id,:class_id,:total_sub,:appeared,:passed,:failed)");
        $stmt->bindParam(":student_id",$_SESSION['student_id'],PDO::PARAM_STR);
        $stmt->bindParam(":class_id",$_SESSION['classID'],PDO::PARAM_STR);
        $stmt->bindParam(":total_sub",$data->subjects,PDO::PARAM_STR);
        $stmt->bindParam(":appeared",$data->appeared,PDO::PARAM_STR);
        $stmt->bindParam(":passed",$data->passed,PDO::PARAM_STR);
        $stmt->bindParam(":failed",$data->failed,PDO::PARAM_STR);
        $stmt->execute();
        echo " ";
      }
      else if($approvalFlag == 2){
        $stmt = $this->pdo->prepare("UPDATE PM010005 SET total_sub = :total_sub ,appeared = :appeared,passed = :appeared ,passed = :passed AND failed = :failed WHERE student_id = :student_id AND class_id = :class_id");
        $stmt->bindParam(":total_sub",$data->subjects,PDO::PARAM_STR);
        $stmt->bindParam(":appeared",$data->appeared,PDO::PARAM_STR);
        $stmt->bindParam(":passed",$data->passed,PDO::PARAM_STR);
        $stmt->bindParam(":failed",$data->failed,PDO::PARAM_STR);
        $stmt->bindParam(":student_id",$_SESSION['student_id'],PDO::PARAM_STR);
        $stmt->bindParam(":class_id",$_SESSION['classID'],PDO::PARAM_STR);
        $stmt->execute();
        echo " ";
      }
    }

    public function checkAcademicData(){

      $stmt = $this->pdo->prepare("SELECT * FROM PM010008 WHERE student_id = :student_id");
      $stmt->bindParam(":student_id",$_SESSION['studentId'],PDO::PARAM_STR);
      $stmt->execute();
      $count = $stmt->rowCount();
      echo json_encode($count);
    }

    public function submitAcademicInfo($data){
      $ssc = 1;
      $hsc = 2;
      $stmt = $this->pdo->prepare("INSERT INTO PM010008 (student_id,class,yop,institute,percentage,subjects,achievements,problems) VALUES (:student_id,:class,:yop,:institute,:percentage,:subjects,:achievements,:problems)");
      $stmt->bindParam(":student_id",$_SESSION['studentId'],PDO::PARAM_STR);
      $stmt->bindParam(":class",$ssc,PDO::PARAM_STR);
      $stmt->bindParam(":yop",$data->yop,PDO::PARAM_STR);
      $stmt->bindParam(":institute",$data->institute,PDO::PARAM_STR);
      $stmt->bindParam(":percentage",$data->percentage,PDO::PARAM_STR);
      $stmt->bindParam(":subjects",$data->subjects,PDO::PARAM_STR);
      $stmt->bindParam(":achievements",$data->achievements,PDO::PARAM_STR);
      $stmt->bindParam(":problems",$data->problems,PDO::PARAM_STR);
      $stmt->execute();

      $stmt = $this->pdo->prepare("INSERT INTO PM010008 (student_id,class,yop,institute,percentage,subjects,achievements,problems) VALUES (:student_id,:class,:yop,:institute,:percentage,:subjects,:achievements,:problems)");
      $stmt->bindParam(":student_id",$_SESSION['studentId'],PDO::PARAM_STR);
      $stmt->bindParam(":class",$hsc,PDO::PARAM_STR);
      $stmt->bindParam(":yop",$data->yop1,PDO::PARAM_STR);
      $stmt->bindParam(":institute",$data->institute1,PDO::PARAM_STR);
      $stmt->bindParam(":percentage",$data->percentage1,PDO::PARAM_STR);
      $stmt->bindParam(":subjects",$data->subjects1,PDO::PARAM_STR);
      $stmt->bindParam(":achievements",$data->achievements1,PDO::PARAM_STR);
      $stmt->bindParam(":problems",$data->problems1,PDO::PARAM_STR);
      $stmt->execute();

      echo "";

    }
    public function storeUnivData($data){

      $count = sizeof($data);

      for ($i=0; $i < $count ; $i++) {
        if ($data[$i]->name->result == 1) {
          $data[$i]->name->subjects = 0;
        }
        $stmt = $this->pdo->prepare("INSERT INTO PM010010 (student_id,exam,mAndy,seat_no,result,no_of_subjects,reason,suggestion,aggregate) VALUES (:student_id,:exam,:mAndy,:seat_no,:result,:no_of_subjects,:reason,:suggestion,:aggregate)");
        $stmt->bindParam(":student_id",$_SESSION['studentId'],PDO::PARAM_STR);
        $stmt->bindParam(":exam",$data[$i]->name->exam,PDO::PARAM_STR);
        $stmt->bindParam(":mAndy",$data[$i]->name->mAndy,PDO::PARAM_STR);
        $stmt->bindParam(":seat_no",$data[$i]->name->seat,PDO::PARAM_STR);
        $stmt->bindParam(":result",$data[$i]->name->result,PDO::PARAM_STR);
        $stmt->bindParam(":no_of_subjects",$data[$i]->name->subjects,PDO::PARAM_STR);
        $stmt->bindParam(":reason",$data[$i]->name->reason,PDO::PARAM_STR);
        $stmt->bindParam(":suggestion",$data[$i]->name->suggestion,PDO::PARAM_STR);
        $stmt->bindParam(":aggregate",$data[$i]->name->agg,PDO::PARAM_STR);
        $stmt->execute();
      }

    }

}

?>
