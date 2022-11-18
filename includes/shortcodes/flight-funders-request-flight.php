<?php
require_once(WP_SITE_ROOT . "/wp-load.php");
require_once(WP_SITE_ROOT . "/wp-content/plugins/project-funders/assets/smtp/PHPMailerAutoload.php");

function request_project(){
    if(is_admin( )){}
    else {
            global $wpdb;   
            $settingsTable = $wpdb->prefix . "flight_funders_settings";
            $settings = $wpdb->get_results(
                "SELECT * FROM $settingsTable"
            ); 

            if(isset($_POST["request--project"])){
            $flightTitle = $_POST["project_title"];
            $flightDescription = $_POST["project_description"];
            $flightFundNeeded = $_POST["project_fund_needed"];
            $flightTimeline = date("F d, Y h:i A",strtotime($_POST["project_timeline"]));
            $flightUsername = $_POST["project_user_name"];
            $flightEmail = $_POST["project_user_email"];
            $flightMobile = $_POST["project_user_mobile"];
            $upload_dir = wp_upload_dir()["path"];
            $flightImageName = $_FILES["project_image"]["name"];
            $flightUploadedImage = $upload_dir . "/" . basename($flightImageName);
            $flightTmpImage = $_FILES["project_image"]["tmp_name"];
            move_uploaded_file($flightTmpImage,$flightUploadedImage);
            $addAttachment = [
        "post_mime_type" => $_FILES["project_image"]["type"],
        "post_title" => $_FILES["project_image"]["name"],
        "post_status" => "publish"
];
    $createAttachment = wp_insert_attachment($addAttachment, $flightUploadedImage);
    $flightImageUrl = wp_get_attachment_url($createAttachment);
    $flightsTable = $wpdb->prefix . "flight_funders_flights";
    $requestFlight = $wpdb->query(
        $wpdb->prepare(
            "INSERT INTO $flightsTable (`flight_id`, `flight_status`, `flight_title`, `flight_description`, `flight_image`, `flight_fund_needed`,`flight_fund_gained`, `flight_timeline`, `flight_user_name`, `flight_user_email`, `flight_user_mobile`) VALUES ('','to review','$flightTitle','$flightDescription','$flightImageUrl','$flightFundNeeded','0','$flightTimeline','$flightUsername','$flightEmail','$flightMobile')"
        )
);

if($requestFlight){
        $submissionStatus = "Your project request has been received.";
        global $wpdb;

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
        $mail->Subject = $notificationSubject;
        $mail->Body = $settings[11]->settings_value;
        $mail->addBCC($settings[9]->settings_value);
        $mail->addBCC($flightEmail);
        $mail->Send();
    }
else {
        $submissionStatus = "Something went wrong while submitting your request."; 
}
    }
    ?>
    
    <div class="request--flight--header">
				<div></div>
				<div>
					<p>Request A Project</p>
				</div>
				<div class="header--buttons">
					<button onclick="history.back()"><<  Back</button>
					<input type="submit" name="request--project" class="request--flight--button" value="Request Flight" form="request-flight">
				</div>
			</div>
			<div class="flight--request--inner">
					<div class="left--inner">
					<form action="" method="POST" id="request-flight" enctype="multipart/form-data">
            <input type="hidden" name="notify_subject" value="Your project request received - Projects Funder">
						<label>
							Title 
							<input type="text" name="project_title">
						</label>
						<label>
							Description 
							<textarea name="project_description"></textarea>
						</label>
						<label>
								<div class="image--container">
                                    <img src="" style="display: none;">
                                    <svg width="25" viewBox="0 0 81 72" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M56.1499 13.7504C56.5457 14.2254 57.132 14.5 57.7503 14.5H67.3337C69.7016 14.5 71.3524 14.5016 72.6374 14.6066C73.8982 14.7096 74.6224 14.9017 75.1712 15.1812C76.347 15.7804 77.3032 16.7365 77.9024 17.9125C78.182 18.4612 78.3741 19.1856 78.477 20.4463C78.582 21.7314 78.5837 23.3821 78.5837 25.75V36.5833C78.5837 38.9512 78.582 40.6021 78.477 41.8871C78.3741 43.1479 78.182 43.8721 77.9024 44.4208C77.3032 45.5967 76.347 46.5529 75.1712 47.1521C74.6224 47.4317 73.8982 47.6237 72.6374 47.7267C71.3524 47.8317 69.7016 47.8333 67.3337 47.8333H35.667C33.2991 47.8333 31.6482 47.8317 30.3633 47.7267C29.1026 47.6237 28.3782 47.4317 27.8295 47.1521C26.6535 46.5529 25.6974 45.5967 25.0982 44.4208C24.8187 43.8721 24.6266 43.1479 24.5236 41.8871C24.4186 40.6021 24.417 38.9512 24.417 36.5833V13.25C24.417 10.8821 24.4186 9.23142 24.5236 7.94633C24.6266 6.68558 24.8187 5.96121 25.0982 5.41254C25.6974 4.23654 26.6535 3.28042 27.8295 2.68121C28.3782 2.40167 29.1026 2.20962 30.3633 2.10662C31.6482 2.00162 33.2991 2 35.667 2H41.0887C43.497 2 44.3187 2.01675 45.0545 2.21463C45.7612 2.40475 46.4291 2.71758 47.0274 3.13883C47.6503 3.57717 48.1895 4.19783 49.7312 6.04792L56.1499 13.7504Z" stroke="black" stroke-width="3" stroke-linejoin="round"/>
                                        <path d="M14 70.75H9.83333C5.23096 70.75 1.5 67.0192 1.5 62.4167V58.25" stroke="black" stroke-width="2" stroke-miterlimit="1.41421"/>
                                        <path d="M43.167 70.75H47.3337C51.9362 70.75 55.667 67.0192 55.667 62.4167V58.25" stroke="black" stroke-width="2" stroke-miterlimit="1.41421"/>
                                        <path d="M34.833 70.75H22.333" stroke="black" stroke-width="2" stroke-miterlimit="1.41421"/>
                                        <path d="M1.5 37.417V49.917" stroke="black" stroke-width="2" stroke-miterlimit="1.41421"/>
                                    </svg>
								    <input type="file" name="project_image" id="flight--image">
                                </div>
						</label>
						<label>Fund Needed (In <?php echo $settings[4]->settings_value; ?>) <label>
								<input type="text" name="project_fund_needed">
							</label>
						</label>
						<label>
							Funding Deadling
							<input type="datetime-local" name="project_timeline">
						</label>
						<label>
							User Full Name 
							<input type="text" name="project_user_name">
						</label>
						<label>
							User Email 
							<input type="text" name="project_user_email">
						</label>
						<label>
							User Mobile 
							<input type="text" name="project_user_mobile">
						</label>
				</form>
					</div>
				</div>

    <style>
    @import url("https://fonts.googleapis.com/css2?family=Poppins");

body {
    font-family: Poppins;
}
    .request--flight--header {
  display: flex;
  align-items: center;
  justify-content: space-between;
}

.request--flight--header p {
  margin: 0;
  font-size: 16px;
  font-weight: 600;
}

.request--flight--header select {
  background-color: #00000010;
  border-radius: 6px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.request--flight--header .header--buttons {
  display: flex;
  gap: 10px;
}

.request--flight--header button {
  border: none;
  border-radius: 4px;
  background-color: #00000010;
  height: 40px;
  width: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  text-decoration: none !important;
}

.request--flight--header a {
  text-decoration: none !important;
}

.request--flight--header button {
  border: solid 1px #000000;
  color: #333333;
  font-size: 14px;
}

.request--flight--header input.request--flight--button {
  background-color: <?php echo $settings[5]->settings_value;?> !important;
  color: <?php echo $settings[6]->settings_value;?>;
  font-size: 14px;
  border: none;
  border-radius: 4px;
  height: 40px;
  width: 140px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  text-decoration: none !important;
}

.flight--request--inner {
  width: 100%;
  height: 100%;
  display: flex;
  gap: 80px;
  margin: 15px 0 0;
  padding: 10px;
  overflow-y: scroll;
}

.flight--request--inner::-webkit-scrollbar {
  width: 5px;
}

.flight--request--inner::-webkit-scrollbar-thumb {
  width: 5px;
  background-color: #4d0071;
}

.flight--request--inner .left--inner {
  width: 50%;
  margin: auto;
}

.flight--request--inner .left--inner form {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.flight--request--inner .left--inner form label {
  display: flex;
  flex-direction: column;
  gap: 5px;
  font-size: 15px;
  font-weight: 500;
}

.flight--request--inner .left--inner form label textarea {
  resize: none;
  height: 120px;
  font-size: 14px;
  padding: 0 10px;
}

.flight--request--inner .flight-image-container {
  padding: 20px;
  border: solid 1px #000000;
  border-radius: 12px;
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.flight--request--inner .flight-image-container button {
  background-color: #4d0071 !important;
  color: #ffffff;
  border-radius: 4px;
  height: 40px;
  width: 60%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: auto;
  cursor: pointer;
}

.image--container {
  height: 220px;
  width: 100%;
  border: dashed 2px;
  border-radius: 5px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

.image--container img {
  width: 100%;
  height: 220px;
  border-radius: 5px;
  object-fit: cover;
}

.flight--request--inner input {
    height: 30px;
    padding: 0 10px;
    font-size: 14px;
}

.flight--request--inner input[type="file"] {
  display: none;
}

    </style>
    <script type="text/javascript">
    $(document).ready(function() {
        $(".image--container input[type='file']").on("change",function(event){
            $(".image--container img").show();
            $(".image--container svg").hide();
            $(".image--container").css("border","none");
			$(".image--container img").prop("srcset",URL.createObjectURL(event.target.files[0]))
		})
    })

</script>
    <?php
    }
}add_shortcode("request_project","request_project");
    ?>