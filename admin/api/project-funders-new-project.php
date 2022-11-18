<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/wp-load.php");
global $wpdb;
if(isset($_FILES["project_image"]) && $_FILES["project_image"]["name"] != ""){

$upload_dir = wp_upload_dir()["path"];
            $projectImageName = $_FILES["project_image"]["name"];
            $projectUploadedImage = $upload_dir . "/" . basename($projectImageName);
            $projectTmpImage = $_FILES["project_image"]["tmp_name"];
            move_uploaded_file($projectTmpImage,$projectUploadedImage);
            $addAttachment = [
        "post_mime_type" => $_FILES["project_image"]["type"],
        "post_title" => $_FILES["project_image"]["name"],
        "post_status" => "publish"
];
    $createAttachment = wp_insert_attachment($addAttachment, $projectUploadedImage);
    $projectImageAttachmentUrl = wp_get_attachment_url($createAttachment);
}


// $projectStatus = $_POST['project_status'];
$projectTitle = $_POST['project_title'];
$projectDescription = $_POST['project_description'];
$projectFundNeeded = $_POST['project_fund_needed'];
$projectTimeline = $_POST['project_timeline'];
$projectUsername = $_POST['project_user_name'];
$projectUserEmail = $_POST['project_user_email'];
$projectUserMobile = $_POST['project_user_mobile'];

$flightsTable = $wpdb->prefix . "flight_funders_flights";
$insertProject = $wpdb->query(
    "INSERT INTO $flightsTable (`flight_id`, `flight_status`, `flight_title`, `flight_description`, `flight_image`, `flight_fund_needed`, `flight_fund_gained`, `flight_timeline`, `flight_user_name`, `flight_user_email`, `flight_user_mobile`) VALUES ('','to review','$projectTitle','$projectDescription','$projectImageAttachmentUrl','$projectFundNeeded',0,'$projectTimeline','$projectUsername','$projectUserEmail','$projectUserMobile')"
);

if($insertProject){
    echo "Project Inserted";
}
?>