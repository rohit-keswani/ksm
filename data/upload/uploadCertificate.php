<?php session_start();
include_once("../classes/class.users.php");
$obj = new users();
$studentId = $_SESSION['studentId'];
$studentKey = $_SESSION['user_plexus_id'];
$certificateId = $_SESSION['currentCertificate'];
if ( !empty( $_FILES ) ) {
    $tempPath = $_FILES[ 'file' ][ 'tmp_name' ];
    $temp = explode(".", $_FILES["file"]["name"]);
    $size = round($_FILES['file']['size']/1024);
    $length = 8;

    $randomString = substr(str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"), 0, $length);

    // echo $randomString;
    $newfilename =  $randomString. '.' . end($temp);

    	# code...
    	// echo json_encode($_FILES);

    $uploadPath = dirname( __FILE__ ) . DIRECTORY_SEPARATOR .$studentId. DIRECTORY_SEPARATOR . $newfilename;
    $toUpload = '/ksm/data/upload'.DIRECTORY_SEPARATOR.$studentId. DIRECTORY_SEPARATOR.$newfilename;
    move_uploaded_file($tempPath,$uploadPath );


  	$obj->saveCertificate($toUpload,$studentKey,$certificateId);



} else {
    echo 'No files';
}
?>
