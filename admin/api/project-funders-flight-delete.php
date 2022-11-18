<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/wp-load.php");

global $wpdb;
$currentFlight = $_POST["flight_id"];
$table = $wpdb->prefix . "flight_funders_flights";
$getFlight = $wpdb->get_results(
    "SELECT * FROM $table WHERE `flight_id`='$currentFlight'"
);
$flightFound = count($getFlight);

if($flightFound > 0){
    $deleteFlight = $wpdb->get_results(
        "DELETE FROM $table WHERE `flight_id`='$currentFlight'"
    );
        echo "Flight deleted";
}
else{
    echo "0";
}
?>