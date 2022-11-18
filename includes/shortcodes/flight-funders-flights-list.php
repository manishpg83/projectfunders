<?php
require_once(WP_SITE_ROOT . "/wp-load.php");
require_once(WP_SITE_ROOT . "/wp-content/plugins/project-funders/assets/smtp/PHPMailerAutoload.php");

date_default_timezone_set("Asia/Kolkata");

function all_projects()
{
    if (is_admin()) {
    }

    //is_admin()
    else {
        if (isset($_GET) && $_GET["project_id"]) {
            global $wpdb;

            $project_id = $_GET["project_id"];

            $flight_table = $wpdb->prefix . "flight_funders_flights";

            $settings_table = $wpdb->prefix . "flight_funders_settings";

            $money_donation_table =
                $wpdb->prefix . "flight_funders_money_donations";

            $prayer_donation_table =
                $wpdb->prefix . "flight_funders_prayer_donations";

            $flight_found = count(
                $wpdb->get_results(
                    "SELECT * FROM $flight_table WHERE flight_id = '$project_id'"
                )
            );

            /* Posting and pushing donated prayer data to the database */

            if (isset($_POST["donate--prayer--now"])) {
                $settingsTable = $wpdb->prefix . "flight_funders_settings";
                $flightSettings = $wpdb->get_results(
                    "SELECT * FROM $settingsTable"
                );

                $project_id = $_POST["project_id"];
                $donorName = $_POST["prayer_donator_name"];
                $donorEmail = $_POST["prayer_donator_email_address"];
                $donorMobile = $_POST["prayer_donator_mobile_number"];
                $donorPrayerMessage = $_POST["prayer_message"];
                $insertPrayer = $wpdb->query(
                    $wpdb->prepare(
                        "INSERT INTO $prayer_donation_table (`id`, `flight_id`, `donator_name`, `donator_email`, `donator_mobile`, `donator_prayer_message`) VALUES ('','$project_id','$donorName','$donorEmail','$donorMobile','$donorPrayerMessage')"
                    )
                );

                $usersTable = $wpdb->prefix . "flight_funders_users";

                $insertUser = $wpdb->query(
                    $wpdb->prepare(
                        "INSERT INTO $usersTable (`id`, `flight_user_name`, `flight_user_email`, `flight_user_mobile`) VALUES ('','$donorName','$donorEmail','$donorMobile')"
                    )
                );

                if($insertUser){
                /* Sending mail to donor for prayer */
                $mail = new PHPMailer(); 
                $mail->IsSMTP(); 
                $mail->SMTPAuth = true; 
                $mail->SMTPSecure = 'tls'; 
                $mail->Host = $flightSettings[7]->settings_value;
                $mail->Port = $flightSettings[8]->settings_value; 
                $mail->IsHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Username = $flightSettings[9]->settings_value;
                $mail->Password = $flightSettings[10]->settings_value;
                $mail->SetFrom($flightSettings[9]->settings_value);
                $mail->Subject = $_POST["notification_subject"];
                $mail->Body = $flightSettings[12]->settings_value;
                $mail->addBCC($flightSettings[9]->settings_value);
                $mail->addBCC($donorEmail);
                $mail->Send();
            } 
        }
            //isset($_POST["donate--prayer--now"])

            if ($flight_found > 0) {

                $flight_details = json_decode(
                    json_encode(
                        $wpdb->get_results(
                            "SELECT * FROM $flight_table WHERE flight_id = '$project_id'"
                        )
                    ),
                    true
                );

                $money_donations = json_decode(
                    json_encode(
                        $wpdb->get_results(
                            "SELECT * FROM $money_donation_table WHERE `flight_id`='$project_id'"
                        )
                    ),
                    true
                );

                $prayer_donations = json_decode(
                    json_encode(
                        $wpdb->get_results(
                            "SELECT * FROM $prayer_donation_table WHERE flight_id = '$project_id'"
                        )
                    ),
                    true
                );

                $flight_settings = $wpdb->get_results(
                    "SELECT * FROM $settings_table"
                );

                /* Fetching data from money donations table for current flight id */

                $moneyDonationsCount = count(
                    $wpdb->get_results(
                        "SELECT * FROM $money_donation_table WHERE `flight_id`='$project_id'"
                    )
                );

                /* Fetching data from prayer donations table for current flight id */

                $prayerDonationsCount = count(
                    $wpdb->get_results(
                        "SELECT * FROM $prayer_donation_table WHERE `flight_id`='$project_id'"
                    )
                );

                /* Calculating total donations received for current flight */

                $totalDonations = $moneyDonationsCount + $prayerDonationsCount;

                $flight_currency = $flight_settings[4]->settings_value;

                $paymentGeneratedID = substr(
                    str_shuffle("QWERTYUIOPASDFGHJKLZXCVBNM1234567890"),
                    0,
                    10
                );

                /* Current flight details fetched from database */

                $project_id = $flight_details[0]["flight_id"];

                $flightImage = $flight_details[0]["flight_image"];

                $flightTitle = $flight_details[0]["flight_title"];

                $flightDescription = $flight_details[0]["flight_description"];

                $flightFundNeeded = $flight_details[0]["flight_fund_needed"];

                $flightFundRaised = $flight_details[0]["flight_fund_gained"];

                $fundGainedPercent =
                    ($flight_details[0]["flight_fund_gained"] /
                        $flight_details[0]["flight_fund_needed"]) *
                    100;
                $fundGainedPercent = number_format($fundGainedPercent, 2);

                $flightTimeline = date(
                    "d-m-y h:i:s A",
                    strtotime($flight_details[0]["flight_timeline"])
                );

                $currentTime = date("d-m-y h:i:s A");

                $currentProjectUrl =
                    "https://" .
                    $_SERVER["HTTP_HOST"] .
                    $_SERVER["REQUEST_URI"];

                $currentUrlParameter = "projects/?project_id=" . $project_id;

                $paymentUrl =
                    "https://" .
                    str_replace(
                        $currentUrlParameter,
                        "",
                        $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]
                    );

                /*Current flight money conversion */

                /* Converting flight fund needed */

                if ($flightFundNeeded < 1) {
                    $convertedFundNeeded = $flight_currency . " " . 0;
                }

                //$flightFundNeeded < 1
                elseif ($flightFundNeeded <= 1000) {
                    $convertedFundNeeded =
                        $flight_currency . " " . $flightFundNeeded;
                }

                //$flightFundNeeded >= 1000 && flightFundNeeded <= 99999
                else {
                    $convertedFundNeeded =
                        $flight_currency .
                        "$" .
                        $flightFundNeeded / 1000000 .
                        "M";
                }

                /* Converting flight fund raised */

                if ($flightFundRaised < 1) {
                    $convertedFundRaised = $flight_currency . " " . 0;
                }

                //$flightFundRaised < 1
                elseif ($flightFundRaised < 1000) {
                    $convertedFundRaised =
                        $flight_currency . " " . $flightFundRaised;
                }

                //$flightFundRaised >= 1000 && $flightFundRaised <= 99999
                else {
                    $convertedFundRaised =
                        $flight_currency .
                        " " .
                        $flightFundRaised / 1000000 .
                        "M";
                }

                /* Flight donations buttons based on funds raised conditions */

                if ($flightFundNeeded == $flightFundRaised) {
                    $flightDonationButtons = "";
                } elseif (
                    strtotime($currentTime) >= strtotime($flightTimeline)
                ) {
                    $flightDonationButtons = "";
                } else {
                    $flightDonationButtons = "
<button id='donate--money'>DONATE MONEY 💵</button>
<button id='donate--prayer'>DONATE PRAYER 🙏</button>";
                }

                /* Render current flight */

                $renderCurrentFlight = "";
                ?> <div class="flight--main">
    <div class="flight--left">
        <img src="
            <?php echo $flightImage; ?>" alt="">
        <div class="flight--status">
            <div class="flight--progress--bar">
                <div class="progress" style="width:<?php echo $fundGainedPercent; ?>%"></div>
            </div>
            <div class="status">
                <div>
                    <p>💸 FUND NEEDED</p>
                    <p> <?php echo $convertedFundNeeded; ?> </p>
                </div>
                <div>
                    <p> <?php echo $totalDonations; ?> Donations </p>
                </div>
                <div>
                    <p>💸 FUND RAISED</p>
                    <p> <?php echo $convertedFundRaised; ?> </p>
                </div>
            </div>
        </div>
    </div>
    <div class="flight--right">
        <div class="social-share" style="margin-left: auto; display: flex; align-items: center; gap: 10px">
        <a href="http://www.facebook.com/sharer.php?u=<?php echo "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];?>">
        <svg width="20" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M13 8.3V11.2H15.6C15.8 11.2 15.9 11.4 15.9 11.6L15.5 13.5C15.5 13.6 15.3 13.7 15.2 13.7H13V21H10V13.8H8.3C8.1 13.8 8 13.7 8 13.5V11.6C8 11.4 8.1 11.3 8.3 11.3H10V8C10 6.3 11.3 5 13 5H15.7C15.9 5 16 5.1 16 5.3V7.7C16 7.9 15.9 8 15.7 8H13.3C13.1 8 13 8.1 13 8.3V8.3Z" stroke="black" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round"/>
            <path d="M14 21H8C3 21 1 19 1 14V8C1 3 3 1 8 1H14C19 1 21 3 21 8V14C21 19 19 21 14 21Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
            </a>
        <a href="https://api.whatsapp.com/send?text=<?php echo "https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];?>">
        <svg width="20" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M5.9 19.6C7.4 20.5 9.2 21 11 21C16.5 21 21 16.5 21 11C21 5.5 16.5 1 11 1C5.5 1 1 5.5 1 11C1 12.8 1.5 14.5 2.3 16L1.44 19.306C1.39563 19.4767 1.39718 19.656 1.4445 19.8259C1.49181 19.9958 1.58321 20.1501 1.70941 20.2733C1.83562 20.3965 1.99215 20.4841 2.16313 20.5272C2.33411 20.5704 2.51347 20.5675 2.683 20.519L5.9 19.6Z" stroke="black" stroke-width="1.5" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
            <path d="M15.5 13.848C15.5 14.01 15.464 14.177 15.387 14.339C15.3084 14.5064 15.2053 14.6611 15.081 14.798C14.86 15.041 14.617 15.216 14.341 15.329C14.071 15.442 13.778 15.5 13.463 15.5C13.003 15.5 12.512 15.392 11.993 15.172C11.4443 14.9341 10.9243 14.6349 10.443 14.28C9.91949 13.8976 9.42543 13.4764 8.965 13.02C8.50946 12.5621 8.08926 12.0704 7.708 11.549C7.35762 11.0719 7.06115 10.5574 6.824 10.015C6.608 9.5 6.5 9.01 6.5 8.543C6.5 8.237 6.554 7.945 6.662 7.675C6.77 7.4 6.942 7.148 7.181 6.923C7.469 6.639 7.784 6.5 8.118 6.5C8.244 6.5 8.37 6.527 8.483 6.581C8.6 6.635 8.703 6.716 8.785 6.833L9.831 8.305C9.911 8.417 9.971 8.521 10.011 8.62C10.051 8.714 10.074 8.809 10.074 8.894C10.0727 9.00742 10.0398 9.11824 9.979 9.214C9.90956 9.33107 9.82482 9.43835 9.727 9.533L9.384 9.889C9.36014 9.91235 9.34141 9.94041 9.32902 9.97141C9.31662 10.0024 9.31082 10.0356 9.312 10.069C9.312 10.104 9.317 10.136 9.326 10.172C9.339 10.208 9.353 10.235 9.362 10.262C9.442 10.411 9.582 10.604 9.781 10.838C9.984 11.072 10.2 11.311 10.434 11.549C10.678 11.788 10.912 12.008 11.151 12.211C11.385 12.408 11.579 12.543 11.732 12.624L11.814 12.661C11.8499 12.6738 11.8879 12.6799 11.926 12.679C11.9605 12.6797 11.9948 12.6733 12.0266 12.66C12.0585 12.6468 12.0872 12.627 12.111 12.602L12.453 12.264C12.566 12.152 12.674 12.067 12.778 12.012C12.8736 11.9511 12.9846 11.9185 13.098 11.918C13.1927 11.9194 13.2861 11.9394 13.373 11.977C13.472 12.017 13.576 12.075 13.688 12.152L15.18 13.21C15.2881 13.2803 15.3741 13.3798 15.428 13.497C15.4747 13.6082 15.4992 13.7274 15.5 13.848V13.848Z" stroke="black" stroke-width="1.5" stroke-miterlimit="10"/>
        </svg>
            </a>
        </div>
        <h2> <?php echo $flightTitle; ?> </h2>
        <p> <?php echo $flightDescription; ?> </p>
        <div class="flight--buttons"> <?php echo $flightDonationButtons; ?> <div id="donate--money--popup">
                <div>   
                    <form action="<?php echo $paymentUrl.'payment?payment_id='.$paymentGeneratedID;?>" method="POST">
                        <input type="hidden" name="project_id" value="<?php echo $project_id;?>">
                        <input type="hidden" name="item_name" value="Donation for: <?php echo $flightTitle;?>">
                        <input type="hidden" name="item_number" value="1">
                        <input type="hidden" name="currency_code" value="<?php echo $flight_currency;?>">
                        <label class="donation--amount">Donation Amount <p> <?php echo $flight_currency;?> </p>
                            <input type="number" name="amount" placeholder="Enter an amount you wanna donate" required>
                        </label>
                        <label>Your Full Name <input type="text" name="money_donator_name" placeholder="Enter your full name" required>
                        </label>
                        <label>Your Email Address <input type="text" name="money_donator_email_address" placeholder="Enter your email addresss" required>
                        </label>
                        <label>Your Mobile Number <input type="text" name="money_donator_mobile_number" placeholder="Enter your mobile number with country code" required>
                        </label>
                        <input type="submit" name="donate--money--now" value="Donate">
                    </form>
                    <div class="close--popup"><p>Close</p></div>
                </div>
            </div>
            <div id="donate--prayer--popup">
                <div>
                    <form action="<?php echo $currentProjectUrl; ?>" method="POST">
                        <input type="hidden" name="project_id" value="<?php echo $project_id;?>">
                        <input type="hidden" name="notification_subject" value="Your donation received - Projects Funder">
                        <label>Your Full Name <input type="text" name="prayer_donator_name" placeholder="Enter your full name" required>
                        </label>
                        <label>Your Email Address <input type="text" name="prayer_donator_email_address" placeholder="Enter your email addresss" required>
                        </label>
                        <label>Your Mobile Number <input type="text" name="prayer_donator_mobile_number" placeholder="Enter your mobile number with country code" required>
                        </label>
                        <label>Prayer Message <textarea name="prayer_message" placeholder="Please type a prayer message" required></textarea>
                        </label>
                        <input type="submit" name="donate--prayer--now" value="Donate">
                    </form>
                    <div class="close--popup"><p>Close</p></div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="donors-list">
    <p>Donors</p>
    <div class="donors-tab">
        <p class="funders active-tab">Funders (<?php echo $moneyDonationsCount; ?>) </p>
        <p class="prayers">Prayers (<?php echo $prayerDonationsCount; ?>) </p>
    </div>
    <div class="money-donations active-donation"> <?php foreach (
     $money_donations
     as $moneyDonated
 ) { ?> <div class="money-donor">
            <p> <?php echo $moneyDonated["donator_name"]; ?> </p>
            <p> <?php echo $flight_currency .
       " " .
       $moneyDonated["donator_donation_amount"]; ?> </p>
        </div> <?php } ?> </div>
    <div class="prayer-donations"> <?php foreach (
     $prayer_donations
     as $prayerDonated
 ) { ?> <div class="prayer-donor">
            <p> <?php echo $prayerDonated["donator_name"]; ?> </p>
            <p> <?php echo $prayerDonated["donator_prayer_message"]; ?> </p>
        </div> <?php } ?> </div>
</div> <?php
$renderCurrentFlight .= '
                                                            <script>
                $(document).ready(function() {
                    $("#donate--money").on("click", function() {
                        $("#donate--money--popup").show();
                        $("#donate--prayer--popup").hide();
                    })
                
                    $("#donate--prayer").on("click", function() {
                        $("#donate--prayer--popup").show();
                        $("#donate--money--popup").hide();
                    })
                    $(".close--popup").on("click", function() {
                        $("#donate--money--popup").hide();
                        $("#donate--prayer--popup").hide();
                    })
                
                    $("#donate--prayer").on("click", function() {
                        $("#donate--prayer--popup").show();
                        $("#donate--money--popup").hide();
                    })
                    $(".funders").on("click",function(){
                        $(".money-donations").addClass("active-donation");
                        $(".prayer-donations").removeClass("active-donation");
                        $(".funders").addClass("active-tab")
                        $(".prayers").removeClass("active-tab")
                    })
                    $(".prayers").on("click",function(){
                        $(".money-donations").removeClass("active-donation");
                        $(".prayer-donations").addClass("active-donation");
                        $(".funders").removeClass("active-tab")
                        $(".prayers").addClass("active-tab")
                    })
                })
                </script>';

$renderCurrentFlight .=
    '
                                                            <style>
                @import url("https://fonts.googleapis.com/css2?family=Poppins");
                
                @media screen and (max-width: 420px){
                .flight--main {
                    width: 100%;
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                    padding: 20px;
                    margin: auto;
                }
                }   

                @media screen and (min-width: 721px){
                .flight--main {
                    width: 100%;
                    display: flex;
                    flex-direction: row;
                    gap: 20px;
                    padding: 20px;
                    margin: auto;
                }
                }  
                
                @media screen and (min-width: 721px){
                .flight--main .flight--left {
                    width: 600px;
                    min-width: 50%;
                    max-width: 50%;
                }
                }

                @media screen and (max-width: 720px){
                .flight--main .flight--left {
                    width: 100%;
                }
                }
                
                @media screen and (min-width: 721px){
                .flight--main .flight--left img {
                    width: 100%;
                    height: 420px;
                    border-radius: 15px !important;
                    object-fit: cover;
                }
                }

                @media screen and (max-width: 720px){
                .flight--main .flight--left img {
                    width: 100%;
                    height: 320px;
                    border-radius: 15px !important;
                    object-fit: cover;
                }
                }
                
                .flight--main .flight--right {
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                    width: 100%;
                }
                
                .flight--main .flight--right h2 {
                    margin: 0;
                    margin-top: -5px;
                    font-family: Poppins;
                    font-size: 20px;
                    font-weight: 700;
                }
                
                .flight--main .flight--right p {
                    margin: 0;
                    font-family: Poppins;
                    font-size: 14px;
                    font-weight: 400;
                    color: #00000080;
                }
                
                .flight--main .flight--left .flight--status {
                    display: flex;
                    flex-direction: column;
                    gap: 15px;
                    font-family: Poppins;
                    font-size: 14px;
                    font-weight: 400;
                    margin: 10px 0;
                }
                
                .flight--main .flight--left .flight--status .flight--progress--bar {
                    background-color: #00000025;
                    height: 5px;
                    width: 100%;
                    border-radius: 100px;
                }
                
                .flight--main .flight--left .flight--status .flight--progress--bar .progress {
                    background-color: '.$flight_settings[5]->settings_value.';
                    height: 5px;
                    border-radius: 100px;
                    animation: progressBar 1s ease-in;
                }
                
                @keyframes progressBar {
                    0% {
                        width: 0;
                    }
                
                    100% {}
                }
                
                .flight--main .flight--left .status {
                    display: flex;
                    justify-content: space-between;
                }
                
                .flight--main .flight--left .status div p:first-child {
                    font-size: 12px;
                    font-weight: 400;
                    color: #00000080;
                }
                
                .flight--main .flight--left .status div p:last-child {
                    font-size: 14px;
                    font-weight: 600;
                    color: #000;
                }
                
                .flight--main .flight--left .status div:nth-child(2) {
                    display: flex;
                    align-items: center;
                }
                
                .flight--main .flight--left .status div:nth-child(2) p {
                    color: #00000090;
                    font-family: Poppins;
                    font-size: 12px;
                    font-weight: 600 !important;
                }
                
                .flight--main .flight--right .flight--buttons {
                    position: relative;
                    display: flex;
                    gap: 30px;
                }
                
                .flight--main .flight--right .flight--buttons button {
                    background-color: ' .
    $flight_settings[5]->settings_value .
    ';
                    color: ' .
    $flight_settings[6]->settings_value .
    ';
                    font-family: Poppins;
                    font-size: 12px;
                    font-weight: 400 !important;
                    width: 200px;
                    height: 50px;
                    outline: none;
                    border: none;
                    border-radius: 10px;
                }

                .flight--main .flight--right .social-share svg {
                    cursor: pointer;
                }

                .flight--main .flight--right .social-share svg path {
                    stroke: ' .$flight_settings[5]->settings_value .';
                }

                #donate--money--popup,
                #donate--prayer--popup {
                    display: none;
                    height: 100vh;
                    width: 100vw;
                    background-color: #00000005;
                    backdrop-filter: blur(16px);
                    -webkit-backdrop-filter: blur(16px);
                    position: fixed;
                    top: 0;
                    left: 0;
                    z-index: 999;
                }
                
                @media screen and (min-width: 721px){
                #donate--money--popup div,
                #donate--prayer--popup div {
                    padding: 20px;
                    background-color: #FFF;
                    border-radius: 10px;
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    z-index: 999;
                    display: flex;
                    flex-direction: column;
                }
                #donate--money--popup div {
                    height: 58vh;
                    width: 30vw;
                }
                #donate--prayer--popup div {
                    height: 65vh;
                    width: 30vw;
                }
                }

                @media screen and (max-width: 720px){
                #donate--money--popup div,
                #donate--prayer--popup div {
                    background-color: #FFF;
                    border-radius: 10px;
                    height: 55vh;
                    width: 85vw;
                    position: fixed;
                    top: 50%;
                    left: 50%;
                    transform: translate(-50%, -50%);
                    padding: 0 40px;
                    z-index: 999;
                    display: flex;
                    flex-direction: column;
                }
                }

                @media screen and (max-width: 720px){
                #donate--prayer--popup div {
                    height: 60vh !important;
                    width: 85vw;
                }
                }
                
                #donate--money--popup div form,
                #donate--prayer--popup div form {
                    display: flex;
                    flex-direction: column;
                    gap: 20px;
                    width: 100%;
                    margin: auto;
                }
                
                #donate--money--popup div form input,
                #donate--prayer--popup div form input,
                #donate--prayer--popup div form textarea {
                    width: 100%;
                    height: 45px;
                    border: none;
                    outline: none;
                    border-radius: 8px !important;
                    background-color: #f5f7fa;
                    color: #00000095;
                    font-family: Poppins;
                    font-weight: 400;
                    font-size: 14px;
                    padding: 15px;
                    transition: all .2s;
                }
                
                #donate--prayer--popup div form textarea {
                    height: 100px !important;
                    resize: none;
                }
                
                #donate--money--popup div form input[type="submit"],
                #donate--prayer--popup div form input[type="submit"] {
                    background-color: ' .
                    $flight_settings[5]->settings_value .
                    ';
                    color: ' .
                    $flight_settings[6]->settings_value .
                    ';
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                }
                
                #donate--money--popup div form label,
                #donate--prayer--popup div form label {
                    display: flex;
                    flex-direction: column;
                    gap: 6px;
                    font-family: Poppins;
                    font-size: 14px;
                    font-weight: 600;
                }
                
                #donate--money--popup div form input[type="number"]::-webkit-outer-spin-button,
                #donate--money--popup div form input[type="number"]::-webkit-inner-spin-button,
                #donate--prayer--popup div form input[type="number"]::-webkit-outer-spin-button,
                #donate--prayer--popup div form input[type="number"]::-webkit-inner-spin-button {
                    display: none;
                }
                
                #donate--money--popup div form input[type="number"],
                #donate--prayer--popup div form input[type="number"] {
                    padding-left: 50px !important;
                    padding-right: 15px !important;
                }
                
                #donate--money--popup div form .donation--amount,
                #donate--prayer--popup div form .donation--amount {
                    position: relative;
                }
                
                #donate--money--popup div form .donation--amount p,
                #donate--prayer--popup div form .donation--amount p {
                    position: absolute;
                    top: 55%;
                    left: 15px;
                }

                #donate--money--popup div .close--popup,
                #donate--prayer--popup div .close--popup {
                    background-color: transparent;
                    position: absolute;
                    height: 10px;
                    width: 50px;
                    top: -25px;
                    left: 5px;
                }

                #donate--money--popup div .close--popup p,
                #donate--prayer--popup div .close--popup p {
                    cursor: pointer;
                }

                .donors-list {
                    padding: 20px;
                    width: 50%;
                }
                .donors-list p {
                    font-family: Poppins;   
                    font-size: 18px;
                    font-weight: 600;
                    color: #000;
                }
                .donors-tab {
                    display: flex;
                    gap: 30px;
                }
                .donors-tab .active-tab {
                    color: ' .
    $flight_settings[5]->settings_value .
    ' !important;
                }
                .donors-tab p {
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    color: #00000099;
                }
                .money-donations {
                    display: none;
                }
                .money-donations.active-donation {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;     
                }
                .money-donations .money-donor {
                    width: 100%;
                    background-color: #00000010;
                    border-radius: 6px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding: 10px;  
                }
                .money-donations .money-donor p {
                    margin: 0;  
                    font-size: 13px;
                    font-weight: 600;      
                }
                .prayer-donations {
                    display: none;  
                }
                .prayer-donations.active-donation {
                    display: flex;
                    flex-direction: column;
                    gap: 10px;   
                }
                .prayer-donations .prayer-donor {
                    width: 100%;
                    background-color: #00000010;
                    border-radius: 6px;
                    display: flex;
                    flex-direction: column;
                    gap: 5px;
                    padding: 10px;  
                }
                .prayer-donations .prayer-donor p {
                    margin: 0;  
                    font-size: 13px;
                    font-weight: 600;      
                }
                .prayer-donations .prayer-donor p:last-child {
                    font-weight: 500;
                }
                </style>';

return $renderCurrentFlight;
?> <?php
            }

            //$flight_found > 0
            else {
            }
        }

        //isset($_GET) && $_GET["flight_id"]
        else {
            global $wpdb;

            $table = $wpdb->prefix . "flight_funders_flights";

            $flights = $wpdb->get_results(
                "SELECT * FROM $table WHERE `flight_status` = 'live' ORDER BY `flight_id` DESC"
            );

            $settings_table = $wpdb->prefix . "flight_funders_settings";

            $flight_currency = $wpdb->get_results(
                "SELECT * FROM $settings_table WHERE settings_type ='currency'"
            );

            $settings = $wpdb->get_results("SELECT * FROM $settings_table");

            $flight_currency = $flight_currency[0]->settings_value;

            $renderAllFlights = '';

            $renderAllFlights .= '
            <div class="projects-container">
            <div>
            <p>Support a flight</p>
            </div>
            <div></div>
            <div>
            <svg width="35" height="27" viewBox="0 0 35 27" fill="none" xmlns="http://www.w3.org/2000/svg" class="projects-prev"><path d="M35 27L5 27C2.23858 27 0 24.7614 0 22V5C0 2.23858 2.23858 0 5 0L35 0V27Z" fill="white"/><path d="M34.7 26.7L5 26.7C2.40426 26.7 0.299999 24.5957 0.299999 22V5C0.299999 2.40426 2.40426 0.299999 5 0.299999L34.7 0.299999V26.7Z" stroke="#837C7C" stroke-opacity="0.2" stroke-width="0.6"/><path d="M20 6C20.2337 5.99954 20.4601 6.08092 20.64 6.23C20.7413 6.31395 20.825 6.41705 20.8863 6.5334C20.9477 6.64974 20.9855 6.77705 20.9975 6.90803C21.0096 7.039 20.9957 7.17107 20.9567 7.29668C20.9177 7.42228 20.8542 7.53895 20.77 7.64L16.29 13L20.61 18.37C20.6931 18.4723 20.7551 18.59 20.7925 18.7163C20.83 18.8427 20.8421 18.9752 20.8281 19.1062C20.8142 19.2372 20.7745 19.3642 20.7113 19.4798C20.6481 19.5955 20.5627 19.6975 20.46 19.78C20.3565 19.871 20.2354 19.9397 20.1041 19.9817C19.9729 20.0237 19.8343 20.0381 19.6972 20.024C19.5601 20.0099 19.4274 19.9676 19.3075 19.8998C19.1875 19.832 19.0828 19.7402 19 19.63L14.17 13.63C14.0229 13.4511 13.9425 13.2266 13.9425 12.995C13.9425 12.7634 14.0229 12.5389 14.17 12.36L19.17 6.36C19.2703 6.23898 19.3978 6.14332 19.542 6.08077C19.6862 6.01822 19.8431 5.99055 20 6Z" fill="black"/></svg>
            <svg width="35" height="27" viewBox="0 0 35 27" fill="none" xmlns="http://www.w3.org/2000/svg" class="projects-next"><path d="M0 0H30C32.7614 0 35 2.23858 35 5V22C35 24.7614 32.7614 27 30 27H0V0Z" fill="white"/><path d="M0.3 0.3H30C32.5957 0.3 34.7 2.40426 34.7 5V22C34.7 24.5957 32.5957 26.7 30 26.7H0.3V0.3Z" stroke="#837C7C" stroke-opacity="0.2" stroke-width="0.6"/><path d="M15 21C14.7663 21.0005 14.5399 20.9191 14.36 20.77C14.2587 20.6861 14.175 20.583 14.1137 20.4666C14.0523 20.3503 14.0145 20.223 14.0025 20.092C13.9904 19.961 14.0043 19.8289 14.0433 19.7033C14.0823 19.5777 14.1458 19.4611 14.23 19.36L18.71 14L14.39 8.63C14.3069 8.52771 14.2449 8.41002 14.2075 8.28368C14.17 8.15734 14.1579 8.02485 14.1719 7.89382C14.1858 7.76279 14.2255 7.63581 14.2887 7.52017C14.3519 7.40454 14.4373 7.30252 14.54 7.22C14.6435 7.12897 14.7646 7.0603 14.8959 7.01831C15.0271 6.97632 15.1657 6.96192 15.3028 6.976C15.4399 6.99009 15.5726 7.03236 15.6925 7.10016C15.8125 7.16796 15.9172 7.25983 16 7.37L20.83 13.37C20.9771 13.5489 21.0575 13.7734 21.0575 14.005C21.0575 14.2366 20.9771 14.4611 20.83 14.64L15.83 20.64C15.7297 20.761 15.6022 20.8567 15.458 20.9192C15.3138 20.9818 15.1569 21.0095 15 21Z" fill="black"/></svg>
            </div>
            </div>
            <div class="swiper mySwiper">
      <div class="swiper-wrapper">';

            foreach ($flights as $flight) {
                $flight_timeline = date_create($flight->flight_timeline);

                $current_time = date_create(date("F d, Y h:i:s A"));

                $interval = date_diff($current_time, $flight_timeline);

                $time_left = json_decode(json_encode($interval), true);

                $fundGainedPercent =
                    ($flight->flight_fund_gained /
                        $flight->flight_fund_needed) *
                    100;
                $fundGainedPercent = number_format($fundGainedPercent, 2);
                
                if ($flight->flight_fund_needed < 1) {
                    $cardConvertedFundNeeded = $flight_currency . " " . 0;
                }

                //$flight->flight_fund_needed < 1
                elseif ($flight->flight_fund_needed <= 10000) {
                    $cardConvertedFundNeeded =
                        $flight_currency . " " . $flight->flight_fund_needed;
                }

                //$flight->flight_fund_needed >= 1000 && $flight->flight_fund_needed <= 99999
                else {
                    $cardConvertedFundNeeded =
                        $flight_currency .
                        " " .
                        $flight->flight_fund_needed / 1000000 .
                        "M";
                }

                if ($flight->flight_fund_gained < 1) {
                    $cardConvertedFundRaised = $flight_currency . " " . 0;
                }

                //$flight->flight_fund_gained < 1
                elseif ($flight->flight_fund_gained < 10000) {
                    $cardConvertedFundRaised =
                        $flight_currency . " " . $flight->flight_fund_gained;
                }

                //$flight->flight_fund_gained >= 1000 && $flight->flight_fund_gained <= 99999
                else {
                    $cardConvertedFundRaised =
                        $flight_currency .
                        " " .
                        $flight->flight_fund_gained / 1000000 .
                        "M";
                }

                if (
                    strtotime($flight->flight_timeline) <=
                    strtotime(date("F d, Y h:i:s A"))
                ) {
                    $flightStatus = "Ended";
                }

                //strtotime($flight->flight_timeline) <= strtotime(date("F d, Y h:i:s A"))
                else {
                    $flightStatus =
                        $time_left["d"] .
                        "d " .
                        $time_left["h"] .
                        "h " .
                        $time_left["i"] .
                        "m ";
                }


                $renderAllFlights .= '<div class="swiper-slide">
                <img src="' .
                $flight->flight_image .
                '" alt="" style="width: 100%; height: 160px; object-fit: cover; border-radius: 4px">
<div class="flight--title">
    <p>' . $flight->flight_title . '</p>
    <div class="flight--progress--bar" style="margin: 10px 0;">
        <div class="progress" style="width:' .
                $fundGainedPercent .
                '%"></div>
    </div>
    <div class="flight--details">
        <div>
            <p>💸 FUND NEEDED</p>
            <p>' . $cardConvertedFundNeeded . '</p>
        </div>
        <div>
            <p>💸️ FUND RAISED</p>
            <p>' . $cardConvertedFundRaised . '</p>
        </div>
        <div>
            <p>' . $fundGainedPercent . '%</p>
        </div>
    </div>
    <p style="font-weight: 400; font-size: 15px; color: #0000009c; margin: 10px 0;">' . $flight->flight_description . '</p>
</div>
<a href="?project_id=' .
                $flight->flight_id .
                '">
    <button class="project-learn-more">Learn More</button>
</a>
                </div>';
            } //$flights as $flight

            $renderAllFlights .= '</div></div>';

            $renderAllFlights .='
            <style>
            @import url("https://fonts.googleapis.com/css2?family=Poppins");
            .projects-container {
                width: 100%;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 10px;
            }

            .projects-container div {
                width: 130px;
            }

            .projects-container div p {
                margin: 0;
                font-family: Poppins;
                font-weight: 600;
                font-size: 16px;
            }

            .projects-container div:nth-child(2) {
                width: 80%;
                height: 2px;
                background-color: #000000;
            }
            @media screen and (max-width: 420px){
                .projects-container div:nth-child(2) {
                    display: none !important;
                }
            }

            .projects-container div:nth-child(3) {
                display: flex;
                justify-content: flex-end;
            }

            .projects-container div:nth-child(3) .projects-prev,
            .projects-container div:nth-child(3) .projects-next {
                cursor: pointer;
            }

            .swiper {
                margin: 20px 0;
            }
            
            .swiper-slide {
                background-color: #F2F2F2;
                padding: 20px;
                display: flex;
                flex-direction: column;
                gap: 10px;
                border-radius: 10px;
                width: 33.33% !important;
                position: relative;
            }

            @media screen and (max-width: 420px) {
                .swiper-slide {
                    width: 100% !important;
                }
            }
            
            @media screen and (min-width: 721px) {
                .swiper-slide {
                    width: 32.2% !important;
                }
            }
            
            .swiper-slide p {
                margin: 0;
                font-family: Poppins;
            }
            
            .flight--title p {
                font-size: 18px;
                font-weight: 600;
                color: #000;
            }
            
            @media screen and (min-width: 721px){
            .flight--details {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            }

            @media screen and (max-width: 720px){
            .flight--details {
                display: flex;
                justify-content: space-between;
                gap: 10px;
                margin: 10px 0 auto;
            }
            }
            
            .flight--details div p:first-child {
                font-size: 12px;
                font-weight: 400;
                color: #616161;
            }
            
            .flight--details div p:last-child {
                font-size: 14px;
                font-weight: 600;
                color: #000;
            }

            .swiper-slide .flight--progress--bar {
                background-color: #00000025;
                height: 5px;
                width: 100%;
                border-radius: 100px;
            }
            
            .swiper-slide .flight--progress--bar .progress {
                background-color: '.$settings[5]->settings_value.';
                height: 5px;
                border-radius: 100px;
                animation: progressBar 1s ease-in;
            }
            
            @keyframes progressBar {
                0% {
                    width: 0;
                }
            
                100% {}
            }

            a {
                text-decoration: none;
            }

            a:hover {
                text-decoration: none;
            }

            .project-learn-more {
                width: 100%;
                background-color: ' .
                $settings[5]->settings_value .
                ';
                color: ' .
                $settings[6]->settings_value .
                ' !important;
                border: none;
                border-radius: 6px;
                font-family: Poppins;
                font-size: 14px;
                font-weight: 500;
            }

            .project-learn-more:hover {
                background-color: ' .
                $settings[5]->settings_value .
                ';
                color: ' .
                $settings[6]->settings_value .
                ' !important;
                border: none;
                border-radius: 6px;
            }

            </style>';

            $renderAllFlights .= "
            
                                                                <script>
            var swiper = new Swiper('.mySwiper', {
                breakpoints: {
                    // when window width is >= 320px
                    320: {
                      slidesPerView: 1,
                      spaceBetween: 20
                    },
                    // when window width is >= 480px
                    480: {
                      slidesPerView: 1,
                      spaceBetween: 20
                    },
                    // when window width is >= 640px
                    640: {
                      slidesPerView: 3,
                      spaceBetween: 20
                    }
                  },
                spaceBetween: 30,
                loop: false,
                navigation: {
                  nextEl: '.projects-next',
                  prevEl: '.projects-prev',
                },
              });
            </script>";

            return $renderAllFlights;
        } ?> <?php
    }
}

add_shortcode("all_projects", "all_projects");

?>
