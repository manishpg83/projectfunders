<?php
require_once(WP_SITE_ROOT . "/wp-load.php");
require_once(WP_SITE_ROOT . "/wp-content/plugins/project-funders/assets/smtp/PHPMailerAutoload.php");
global $wpdb;

$settingsTable = $wpdb->prefix . "flight_funders_settings";
$settings = $wpdb->get_results(
    "SELECT * FROM $settingsTable"
);

$mail = new PHPMailer(); 
$mail->IsSMTP(); 
$mail->SMTPAuth = true; 
$mail->SMTPSecure = 'tls'; 
$mail->Host = $settings[7]->settings_value;
$mail->Port = $settings[8]->settings_value; 
$mail->IsHTML(true);
$mail->CharSet = 'UTF-8';
$mail->Username = $settings[9]->settings_value;
$mail->Password = $settings[10]->settings_value;
$mail->SetFrom($settings[9]->settings_value);
$mail->Subject = $_POST["subject"];
$mail->Body = $settings[11]->settings_value;
$mail->AddAddress($settings[9]->settings_value);
$mail->AddAddress($_POST["requester_email"]);

if(!$mail->Send()){
	echo "Not sent";
}else{
	echo 'Sent';
}
?>