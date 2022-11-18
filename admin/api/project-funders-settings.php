<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/wp-load.php");
global $wpdb;
$settingsTable = $wpdb->prefix . "flight_funders_settings";
$updationStatus = 0;
foreach ($_POST as $key => $val) {
    $wpdb->update("$settingsTable",
    array("settings_value" => "$val"),
    array("settings_type" => "$key")
);
}
?>