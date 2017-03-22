<?php session_start();
$post = file_get_contents("php://input");
$entryData = json_decode($post);
$_SESSION['currentCertificate'] = $entryData->certificate;
echo json_encode ($entryData);
 ?>
