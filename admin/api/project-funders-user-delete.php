<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/wp-load.php");

global $wpdb;
$currentUser = $_POST["user_id"];
$table = $wpdb->prefix . "flight_funders_users";
$getUser = $wpdb->get_results(
    "SELECT * FROM $table WHERE `id`='$currentUser'"
);
$userFound = count($getUser);

if($userFound > 0){
    $deleteUser = $wpdb->get_results(
        "DELETE FROM $table WHERE `id`='$currentUser'"
    );
        echo "User deleted";
}
else{
    echo "0";
}
?>