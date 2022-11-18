<?php
require_once(WP_SITE_ROOT . "/wp-load.php");
require_once(WP_SITE_ROOT . '/wp-admin/includes/upgrade.php');
function payment_execute()
{
    if (is_admin()) {
    } else {
        global $wpdb;
        $settingsTable = $wpdb->prefix . "flight_funders_settings";
        $settings = $wpdb->get_results(
            "SELECT * FROM $settingsTable"
        );
        $settings = json_decode(json_encode($settings),true);
        
        $_SESSION["flight_id"] = $_POST["flight_id"];
        $_SESSION["money_donator_name"] = $_POST["money_donator_name"];
        $_SESSION["money_donator_email_address"] = $_POST["money_donator_email_address"];
        $_SESSION["money_donator_mobile_number"] = $_POST["money_donator_mobile_number"];
        
        if($settings[1]["settings_value"] === "false"){
            $paypalUrl = "https://www.sandbox.paypal.com";
        }
        else{
            $paypalUrl = "https://www.paypal.com";
        }
?>

<form action="<?php echo $paypalUrl;?>" method="POST">
    <input type="hidden" name="business" value="<?php echo $settings[0]["settings_value"];?>">
    <input type="hidden" name="cmd" value="_xclick">
    <input type="hidden" name="lc" value="AU">
    <input type="hidden" name="rm" value="2">
    <input type="hidden" name="return" value="<?php echo "https://" . $settings[2]["settings_value"];?>">
    <input type="hidden" name="item_name" value="<?php echo $_POST["item_name"];?>">
    <input type="hidden" name="item_number" value="<?php echo $_POST["item_number"];?>">
    <input type="hidden" name="currency_code" value="<?php echo $_POST["currency_code"];?>">
    <input type="hidden" name="amount" value="<?php echo $_POST["amount"];?>">
    <input type="hidden" name="money_donator_name" value="<?php echo $_POST["money_donator_name"];?>">
    <input type="hidden" name="money_donator_email_address" value="<?php echo $_POST["money_donator_email_address"];?>">
    <input type="hidden" name="money_donator_mobile_number" value="<?php echo $_POST["money_donator_mobile_number"];?>">
    <input type="submit" name="donate--money--now" value="Donate">
</form>

<style>
input[type='submit'] {
    display: none;
}
</style>

<script>
$(document).ready(function() {
    $("input[type='submit']").click();
})
</script>

<?php
    }
}

add_shortcode("payment_execute", "payment_execute");
?>