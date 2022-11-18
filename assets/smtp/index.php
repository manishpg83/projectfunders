<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/wp-content/plugins/project-funders/assets/smtp/PHPMailerAutoload.php");

$mail = new PHPMailer(); 
$mail->IsSMTP(); 
$mail->SMTPAuth = true; 
$mail->SMTPSecure = 'tls'; 
$mail->Host = "mumult1.hostarmada.net";
$mail->Port = 587; 
$mail->IsHTML(true);
$mail->CharSet = 'UTF-8';
$mail->Username = "hardik@cbd.golfclubsbag.com";
$mail->Password = "hArdikvIrender@$8512";
$mail->SetFrom("hardik@cbd.golfclubsbag.com");
$mail->Subject = "SMTP Testing 123";
$mail->Body = "SMTP testing";
$mail->AddAddress("beniharshwal@gmail.com");

if(!$mail->Send()){
	echo "Not sent";
}else{
	echo 'Sent';
}
?>