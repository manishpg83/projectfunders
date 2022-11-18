<?php

/*
Plugin Name:       Project Funders
Plugin URI:        https://devfizz,com
Description:       Donation plugin for Non Profit Organisations to pay and request for flights under natural calamities.
Version:           1.0
Requires at least: 5.2
Requires PHP:      7.2
Author:            Devfizz
Author URI:        https://devfizz.com/
License:           GPL v2 or later
License URI:       https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:       project-funders
Project Funders is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation, either version 2 of the License, or any later versi
Project Funders is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details. You should have received a copy of the GNU General Public License along with Project Funders. If not, see {URI to Plugin Licens
*/

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-load.php";

require_once $_SERVER["DOCUMENT_ROOT"] . "/wp-admin/includes/upgrade.php";

foreach (glob(__DIR__ . "/includes/shortcodes/*.php") as $shortcodes) {
    require_once $shortcodes;
}

foreach (glob(__DIR__ . "/admin/*.php") as $admin) {
    require_once $admin;
}

function header_scripts()
{
    echo '<script src="https://code.jquery.com/jquery-3.6.1.min.js" integrity="sha256-o88AwQnZB+VDvE9tvIXrMQaPlFFSUTR+nldQm1LuPXQ=" crossorigin="anonymous"></script><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css"/><script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>';
}
add_action("wp_head", "header_scripts");
add_action("admin_head", "header_scripts");

/* Defining Constants */

if (!define("PLUGIN_DIR")) {
    $plugin_directory = plugin_dir_url(__FILE__);

    define("PLUGIN_DIR", $plugin_directory);
}

function session_activate()
{
    if (!session_id()) {
        session_start();
    }
}

add_action("init", "session_activate");

function project_funders_activated()
{
    /* Creating necessary tables in databse after plugin activation */

    global $wpdb;

    $settingsTable = $wpdb->prefix . "flight_funders_settings";
    $flightsTable = $wpdb->prefix . "flight_funders_flights";
    $usersTable = $wpdb->prefix . "flight_funders_users";
    $moneyDontationTable = $wpdb->prefix . "flight_funders_money_donations";
    $prayerDontationTable = $wpdb->prefix . "flight_funders_prayer_donations";
    $notificationsTable = $wpdb->prefix . "flight_funders_notifications";
    $tables = [
        "flight_funders_settings" => "CREATE TABLE $settingsTable(
            id INT(50) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            settings_type varchar(250) NOT NULL,
            settings_value varchar(250) NOT NULL
        )",

        "flight_funders_flights" => "CREATE TABLE $flightsTable(
            flight_id INT(50) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            flight_status varchar(250) NOT NULL,
            flight_title varchar(250) NOT NULL,
            flight_description varchar(250) NOT NULL,
            flight_image TEXT(250) NOT NULL,
            flight_fund_needed INT(250) NOT NULL,
            flight_fund_gained INT(250) NOT NULL,
            flight_timeline VARCHAR(250) NOT NULL,
            flight_user_name VARCHAR(250) NOT NULL,
            flight_user_email VARCHAR(250) NOT NULL,
            flight_user_mobile VARCHAR(250) NOT NULL
        )",

        "flight_funders_users" => "CREATE TABLE $usersTable(
            id INT(50) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            flight_user_name VARCHAR(250) NOT NULL,
            flight_user_email VARCHAR(250) NOT NULL,
            flight_user_mobile VARCHAR(250) NOT NULL
        )",

        "flight_funders_money_donations" => "CREATE TABLE $moneyDontationTable(
            id INT(50) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            flight_id INT(50) NOT NULL,
            donator_name VARCHAR(250) NOT NULL,
            donator_email VARCHAR(250) NOT NULL,
            donator_mobile VARCHAR(250) NOT NULL,
            donator_donation_amount INT(250) NOT NULL,
            donation_timestamp TIMESTAMP NOT NULL
        )",

        "flight_funders_prayer_donations" => "CREATE TABLE $prayerDontationTable(
            id INT(50) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            flight_id INT(50) NOT NULL,
            donator_name VARCHAR(250) NOT NULL,
            donator_email VARCHAR(250) NOT NULL,
            donator_mobile VARCHAR(250) NOT NULL,
            donator_prayer_message VARCHAR(250) NOT NULL 
        )",
        "flight_funders_notifications" => "CREATE TABLE $notificationsTable(
            id INT(50) PRIMARY KEY NOT NULL AUTO_INCREMENT,
            flight_title VARCHAR(250) NOT NULL,
            notification_subject VARCHAR(250) NOT NULL,
            notification_message TEXT NOT NULL,
            notification_emails TEXT NOT NULL,
            notification_timestamp TIMESTAMP NOT NULL
        )",
    ];

    foreach ($tables as $createTable) {
        dbDelta($createTable);
    }

    /* Inserting flight page */

    $defaultPages = [
        [
            "post_type" => "page",
            "post_title" => "Projects",
            "post_content" => "[all_projects]",
            "post_status" => "publish",
        ],
        [
            "post_type" => "page",
            "post_title" => "Request Project",
            "post_content" => "[request_project]",
            "post_status" => "publish",
        ],
        [
            "post_type" => "page",
            "post_title" => "Payment",
            "post_content" => "[payment_execute]",
            "post_status" => "publish",
        ],
        [
            "post_type" => "page",
            "post_title" => "Success",
            "post_content" => "[payment_success]",
            "post_status" => "publish",
        ],
    ];

    foreach ($defaultPages as $page) {
        if (!post_exists(strtolower($page["post_title"]))) {
            wp_insert_post($page);
        }
    }

    /* Inserting demo flight in database once plugin gets activated */

    $demoFlightTimeline = date("F d, Y h:i A", strtotime("+5 hours"));
    $flightTable = $wpdb->prefix . "flight_funders_flights";
    $flightImage =
        plugin_dir_url(__FILE__) . "assets/images/flight-placeholder.png";

    $createDemoFlights = $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO $flightTable (`flight_id`, `flight_status`, `flight_title`, `flight_description`, `flight_image`, `flight_fund_needed`, `flight_fund_gained`, `flight_timeline`, `flight_user_name`, `flight_user_email`, `flight_user_mobile`) VALUES ('','live','This is a demo flight. You can delete this anytime.','This is a demo flight. You can definitely delete this anytime but make sure once you deactivate and then again reactivate the plugin this demo flight will again come back.','$flightImage','100000','0','$demoFlightTimeline','Hardik Malhotra','hardikmalhotra2000@gmail.com','+91 8512010563')"
        )
    );

    /* Inserting Paypal Random API in database once plugin gets activated */

    $queryPaypal = "SELECT * FROM $settingsTable";
    $getSettings = $wpdb->get_results($queryPaypal);
    $settingsCount = count($getSettings);
    if ($settingsCount < 1) {
        $successPage = $_SERVER["HTTP_HOST"] . "/success";
        $failurePage = $_SERVER["HTTP_HOST"] . "/failed";
        $insertSettings = [
            "paypal_email_address" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','paypal_email_address','flightfunderspaypal@gmail.com')",
            "paypal_sandbox_status" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','paypal_sandbox_status','false')",
            "payment_success_page" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','payment_success_page','$successPage')",
            "payment_failure_page" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','payment_failure_page','$failurePage')",
            "currency" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','currency','USD')",
            "branding_accent_color" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','branding_accent_color','#000000')",
            "branding_text_color" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','branding_text_color','#ffffff')",
            "smtp_host" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','smtp_host','localhost')",
            "smtp_port" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','smtp_port','529')",
            "smtp_email" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','smtp_email','flights@smtp.com')",
            "smtp_password" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','smtp_password','flights@$2022')",
            "submission_message" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','submission_message','Thank you for your submission. We will review and get back to you with any questions.  Upon approval, your Project will be made Live on our site.')",
            "donation_message" => "INSERT INTO $settingsTable (`id`, `settings_type`, `settings_value`) VALUES ('','donation_message','Thank you for your submission. Your Donation has been received.  Thank you!  If you have opted in to receiving project updates, we will keep you posted on reaching the funding goal and send you a post-project update.')"
        ];

        foreach ($insertSettings as $paypal) {
            $wpdb->query($wpdb->prepare($paypal));
        }
    }
}

register_activation_hook(__FILE__, "project_funders_activated");

?>