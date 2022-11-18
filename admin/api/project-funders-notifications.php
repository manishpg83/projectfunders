<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/wp-load.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/wp-content/plugins/project-funders/assets/smtp/PHPMailerAutoload.php");

global $wpdb;
$settingsTable = $wpdb->prefix . "flight_funders_settings";
$settings = $wpdb->get_results(
    "SELECT * FROM $settingsTable"
);

$emails = [];
$flightId = $_POST['flight_id'];
$prayerTable = $wpdb->prefix . "flight_funders_prayer_donations";
$moneyTable = $wpdb->prefix . "flight_funders_money_donations";
$prayers = $wpdb->get_results(
    "SELECT * FROM $prayerTable WHERE `flight_id`= $flightId"
);
$moneys = $wpdb->get_results(
    "SELECT * FROM $moneyTable WHERE `flight_id`= $flightId"
);
foreach($prayers as $prayer){
    $emails[] = $prayer->donator_email;
}

foreach($moneys as $money){
    $emails[] = $money->donator_email;
}

unset($_POST["flight_id"]);
$emailDatas = [];
foreach($_POST as $emailData){
    $emailDatas[] = $emailData;
}

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
$mail->Subject = $emailDatas[0];
$mail->Body = $emailDatas[1];
$mail->addBCC($settings[9]->settings_value);
foreach($emails as $email){
    $mail->addBCC($email);
}
if(!$mail->Send()){
    echo "Sorry";
}
else{
    $flightTable = $wpdb->prefix . "flight_funders_flights";
    $flights = $wpdb->get_results(
        "SELECT * FROM $flightTable WHERE `flight_id`=$flightId"
    )[0];
    $flight_title = $flights->flight_title;
    $notificationTable = $wpdb->prefix . "flight_funders_notifications";
    $insertNotification = $wpdb->query(
        "INSERT INTO $notificationTable (`flight_title`, `notification_subject`, `notification_message`) VALUES ('$flight_title','$emailDatas[0]','$emailDatas[1]')"
    );
    echo "Sent";
}
?>