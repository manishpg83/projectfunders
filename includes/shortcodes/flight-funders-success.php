<?php
require_once ($_SERVER["DOCUMENT_ROOT"] . "/wp-load.php");
require_once ($_SERVER["DOCUMENT_ROOT"] . '/wp-admin/includes/upgrade.php');
date_default_timezone_set("Asia/Kolkata");
function payment_success()
{
    if (is_admin())
    {
    }
    else
    {
        global $wpdb;
        $flightId = $_SESSION("flight_id");
        $donorName = $_SESSION("money_donator_name");
        $donorEmail = $_SESSION("money_donator_email_address");
        $donorMobile = $_SESSION("money_donator_mobile_number");
        $donationAmount = $_POST["payment_gross"];
        $flightsTable = $wpdb->prefix . "flight_funders_flights";
        $usersTable = $wpdb->prefix . "flight_funders_users";
        $donationTable = $wpdb->prefix . "flight_funders_money_donations";
        $settingsTable = $wpdb->prefix . "flight_funders_money_settings";
        $getPayment = $wpdb->get_results("SELECT `flight_fund_gained` FROM `$flightsTable` WHERE `flight_id`='$flightId'");
        $crrFlightGainedFund = $getPayment[0]->flight_fund_gained;
        $updatedFlighedFund = $crrFlightGainedFund + $donationAmount;
        if ($_SESSION("flight_id") != "")
        {
            $insertDonation = $wpdb->query($wpdb->prepare("INSERT INTO $donationTable (`id`, `flight_id`, `donator_name`, `donator_email`, `donator_mobile`, `donator_donation_amount`) VALUES ('','$flightId','$donorName','$donorEmail','$donorMobile','$donationAmount')"));
            $insertUser = $wpdb->query($wpdb->prepare(
                "INSERT INTO $usersTable (`id`, `flight_user_name`, `flight_user_email`, `flight_user_mobile`) VALUES ('','$donorName','$donorEmail','$donorMobile')"
            ));
            $updateFlight = $wpdb->query($wpdb->prepare("UPDATE `$flightsTable` SET `flight_fund_gained`='$updatedFlighedFund' WHERE `flight_id`='$flightId'"));
        }

        if($insertDonation){
            $settings = $wpdb->get_results(
                "SELECT * FROM $settingsTable"
            )[0];
            $settings = json_decode(json_encode($settings),true);

            /* Sending mail to donor for prayer */
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
            $mail->Subject = $project_id;
            $mail->Body = $donorPrayerMessage;
            $mail->AddAddress($settings[7]["settings_value"]);
            $mail->AddAddress("malhotrahardikrocks@gmail.com");
            if(!$mail->Send()){
                echo "Not sent";
            }else{
                echo 'Sent';
            }
            session_destroy();
        }
    }
}
add_shortcode("payment_success", "payment_success");
?>