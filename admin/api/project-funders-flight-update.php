<?php
require_once(WP_SITE_ROOT . "/wp-load.php");
global $wpdb;
$flightsTable = $wpdb->prefix . "flight_funders_flights";
$flighID = $_POST["flight_id"];
$getFlight = $wpdb->get_results(
    "SELECT * FROM $flightsTable WHERE `flight_id`=$flighID"
)[0];

if(isset($_FILES["flight_image"]) && $_FILES["flight_image"]["name"] != ""){
$uploadDir = wp_upload_dir()["path"];
$flightImage = $_FILES["flight_image"]["name"];
$flightUpload = $uploadDir . "/" . basename($flightImage);
move_uploaded_file($_FILES["tmp_name"],$flightUpload);
$flightImageArgs = [
    "post_mime_type" => $_FILES["flight_image"]["type"],
    "post_title" => $_FILES["flight_image"]["name"],
    "post_status" => "inherit"
];
$flightImageAttachment = wp_insert_attachment($flightImageArgs,$flightUpload);
$flightImageAttachmentUrl = wp_get_attachment_url($flightImageAttachment);
}
else {
    $flightImageAttachmentUrl = $getFlight->flight_image;
}

$flightStatus = $_POST['flight_status'];
$flightTitle = $_POST['flight_title'];
$flightDescription = $_POST['flight_description'];
$flightImage = $_POST['flight_image'];
$flightFundNeeded = $_POST['flight_fund_needed'];
$flightFundGained = $_POST['flight_fund_gained'];
$flightTimeline = $_POST['flight_timeline'];
$flightUsername = $_POST['flight_user_name'];
$flightUserEmail = $_POST['flight_user_email'];
$flightUserMobile = $_POST['flight_user_mobile'];

$updateFlight = $wpdb->update(
    "$flightsTable",

    [
        "flight_status" => "$flightStatus",
        "flight_title" => "$flightTitle",
        "flight_description" => "$flightDescription",
        "flight_image" => "$flightImageAttachmentUrl",
        "flight_fund_needed" => "$flightFundNeeded",
        "flight_fund_gained" => "$flightFundGained",
        "flight_timeline" => "$flightTimeline",
        "flight_user_name" => "$flightUsername",
        "flight_user_email" => "$flightUserEmail",
        "flight_user_mobile" => "$flightUserMobile",

    ],

    ["flight_id" => "$flighID"]
);

if($updateFlight){
    echo "Project Updated";
}
?>