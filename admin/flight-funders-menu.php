<?php

require_once WP_SITE_ROOT . "/wp-config.php";

require_once WP_SITE_ROOT . "/wp-load.php";

/* Creating Flight Funders menu, submenus & pages in WordPress admin panel sidebar */

add_action("admin_menu", "project_funders_menu");

function project_funders_menu()
{
    add_menu_page(
        "Project Funders",
        "Project Funders",
        "manage_options",
        "project-funders",
        "project-funders_menu_content",
        "dashicons-money-alt",
        50
    );

    add_submenu_page(
        "project-funders",
        "Projects",
        "Projects",
        "manage_options",
        "project-funders/project-manager",
        "project_funders_project_manager"
    );
	
    add_submenu_page(
        "project-funders",
        "Notifications",
        "Notifications",
        "manage_options",
        "project-funders/notification-manager",
        "project_funders_notification_manager"
    );

    add_submenu_page(
        "project-funders",
        "Users",
        "Users",
        "manage_options",
        "project-funders/user-manager",
        "project_funders_user_manager"
    );

    add_submenu_page(
        "project-funders",
        "Settings",
        "Settings",
        "manage_options",
        "project-funders/settings",
        "project_funders_settings"
    );
}

/* Flight Manager Page */

function project_funders_project_manager()
{
    global $wpdb;

    $table = $wpdb->prefix . "flight_funders_flights";

    $allFlights = $wpdb->get_results("SELECT * FROM $table");

    $toReviewFlights = $wpdb->get_results(
        "SELECT * FROM $table WHERE `flight_status` = 'to review'"
    );

    $liveFlights = $wpdb->get_results(
        "SELECT * FROM $table WHERE `flight_status` = 'live'"
    );

    $fullyFundedFlights = $wpdb->get_results(
        "SELECT * FROM $table WHERE `flight_fund_needed` = `flight_fund_gained`"
    );

    $completedFlights = $wpdb->get_results(
        "SELECT * FROM $table WHERE `flight_status` = 'completed'"
    );

    $settingsTable = $wpdb->prefix . "flight_funders_settings";

    $getSettings = $wpdb->get_results("SELECT * FROM $settingsTable");
    ?> 
	
	<?php 
	if (isset($_GET["view_flight"]) && $_GET["view_flight"] != "") {
     	$currentFlightId = $_GET["view_flight"];

	if (isset($_POST["update_flight"])) {
     $flightTitle = $_POST["flight_title"];

     $flightDescription = $_POST["flight_description"];

     $flightImage = $_POST["flight_image"];

     $flightFundNeeded = $_POST["flight_fund_needed"];

     $flightTimeline = $_POST["flight_timeline"];

     $flightStatus = $_POST["flight_status"];

     $updateFlight = $wpdb->update(
         "$table",

         [
             "flight_title" => "$flightTitle",
             "flight_description" => "$flightDescription",
             "flight_image" => "$flightImage",
             "flight_fund_needed" => "$flightFundNeeded",
             "flight_timeline" => "$flightTimeline",
             "flight_status" => "$flightStatus",
         ],

         ["flight_id" => "$currentFlightId"]
     );

     if ($updateFlight) {
         $flightUpdationStatus =
             "Current project details has been updated " . ✅;
     }
 }

 $currentFlightDetails = $wpdb->get_results(
     "SELECT * FROM $table WHERE `flight_id`='$currentFlightId'"
 );
 ?>
<!-- Rendering view current user in admin panel -->
<div class="flight--manager--container">
	<div class="flight--manager-inner">
		<div class="flight">

		<div class="update--flight--header">
				<div>
				<select name="flight_status" id="flight-status">
					<option value="live" <?php 
					if ($currentFlightDetails[0]->flight_status === "live") {
						echo "selected";
					} 
					?>>
					Live 
				</option>
				<option value="to review" <?php 
					if ($currentFlightDetails[0]->flight_status === "to review") {
						echo "selected";
					} 
					?>>
					To Review 
				</option>
				<option value="completed" <?php 
					if ($currentFlightDetails[0]->flight_status === "completed") {
						echo "selected";
					} 
					?>>
					Completed 
				</option>
			</select>
				</div>
				<div>
					<p>View Project</p>
				</div>
				<div class="header--buttons">
					<a href="<?php $flightManagerUrl = "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; $flight = "&view_flight=".$_GET["view_flight"]; echo str_replace($flight,"",$flightManagerUrl);?>"><button><<  Back</button></a>
					<input type="submit" class="update--flight" value="Save" form="update-flight">
				</div>
			</div>
			<div class="flight--update--inner">
					<div class="left--inner">
					<form action="" method="POST" id="update-flight" enctype="multipart/form-data">
						<input type="hidden" name="flight_id" value="<?php echo $currentFlightDetails[0]->flight_id; ?>">
						</label>
						<label>
							Title 
							<input type="text" name="flight_title" value="<?php echo $currentFlightDetails[0]->flight_title; ?>">
						</label>
						<label>
							Description 
							<textarea name="flight_description"><?php echo $currentFlightDetails[0]->flight_description; ?></textarea>
						</label>
						<label>
								<img src="<?php echo $currentFlightDetails[0]->flight_image; ?>" alt="">
								<input type="file" name="flight_image" id="flight--image">
						</label>
						<label>Fund Needed (In <?php echo $getSettings[4]->settings_value; ?>) <label>
								<input type="number" name="flight_fund_needed" value="<?php echo $currentFlightDetails[0]->flight_fund_needed; ?>">
							</label>
						</label>
						<label>
							Fund Raised (In <?php echo $getSettings[4]->settings_value; ?>) 
							<input type="text" name="flight_fund_gained" value="<?php echo $currentFlightDetails[0]->flight_fund_gained; ?>">
						</label>
						<label>
							Funding Deadling
							<input type="datetime-local" name="flight_timeline" value="<?php echo date("Y-m-d\TH:i",strtotime($currentFlightDetails[0]->flight_timeline));?>">
						</label>
						<label>
							User Full Name 
							<input type="text" name="flight_user_name" value="<?php echo $currentFlightDetails[0]->flight_user_name; ?>">
						</label>
						<label>
							User Email 
							<input type="text" name="flight_user_email" value="<?php echo $currentFlightDetails[0]->flight_user_email; ?>">
						</label>
						<label>
							User Mobile 
							<input type="text" name="flight_user_mobile" value="<?php echo $currentFlightDetails[0]->flight_user_mobile; ?>">
						</label>
				</form>
					</div>
				</div>
			
			<p> <?php echo $flightUpdationStatus; ?> </p>
		</div>
	</div>
</div>

<style>
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');
	#wpbody {
		position: relative;
		margin-left: -20px;
		background-color: #ffffff;
		font-family: Poppins;
	}

	.flight {
		height: 680px;
		width: 95%;
		margin: auto;
		padding: 15px;
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.update--flight--header {
		display: flex;
		align-items: center;
		justify-content: space-between;
	}

	.update--flight--header p {
		margin: 0;
		font-size: 16px;
		font-weight: 600;
	}

	.update--flight--header select {
		background-color: #00000010;
		border-radius: 6px;
		display: flex;
		align-items: center;
		justify-content: center;
	}

	.update--flight--header .header--buttons {
		display: flex;
		gap: 10px;
	}

	.update--flight--header button {
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

	.update--flight--header a {
		text-decoration: none !important;
	}

	.update--flight--header button:nth-child(1) {
		border: solid 1px #000000;
	}

	.update--flight--header input.update--flight {
		background-color: #4D0071 !important;
		color: #ffffff;
		border: none;
		border-radius: 4px;
		height: 40px;
		width: 100px;
		display: flex;
		align-items: center;
		justify-content: center;
		cursor: pointer;
		text-decoration: none !important;
	}

	.flight--update--inner {
		width: 100%;
		height: 100%;
		display: flex;
		gap: 80px;
		margin: 15px 0 0;
		padding: 10px;
		overflow-y: scroll;
	}

	.flight--update--inner::-webkit-scrollbar {
		width: 5px;
	}
	
	.flight--update--inner::-webkit-scrollbar-thumb {
		width: 5px;
		background-color: #4D0071;
	}

	.flight--update--inner .left--inner {
		width: 50%;
		margin: auto;
	}

	.flight--update--inner .left--inner form {
		display: flex;
		flex-direction: column;
		gap: 20px;
	}

	.flight--update--inner .left--inner form label {
		display: flex;
		flex-direction: column;
		gap: 5px;
	}

	.flight--update--inner .left--inner form label textarea {
		resize: none;
		height: 120px;
	}

	.flight--update--inner .flight-image-container {
		padding: 20px;
		border: solid 1px #000000;
		border-radius: 12px;
		width: 100%;
		display: flex;
		flex-direction: column;
		gap: 10px;
}

.flight--update--inner .flight-image-container button {
		background-color: #4D0071 !important;
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

	.flight--update--inner img {
		width: 100%;
		height: 260px;
		object-fit: cover;
		border-radius: 6px;
		margin: auto;
	}

	.flight--update--inner input[type='file'] {
		display: none;
	}

</style> 

<script>

	$(document).ready(function(){
		$("#update-flight").on("submit",function(event){
			event.preventDefault();
			var url = window.location.origin + "/wp-content/plugins/project-funders/admin/api/project-funders-flight-update.php";
			var payload = new FormData(this)
			payload.append("flight_status",$("#flight-status").val());
			$.ajax({
				type: "POST",
				url: url,
				data: payload,
				contentType: false,
				processData: false,
				success: function (response) {
					if(response === "Project Updated"){
						alert("Current project has been updated");
						window.location.href = window.location.href;
					}
				}
			});
		})

		$("#flight--image").on("change",function(event){
			$(".left--inner form img").prop("srcset",URL.createObjectURL(event.target.files[0]))
		})
	})

</script>

<?php
 } else {
      ?>
<!-- Flight Manager Html Code -->
<div class="flights--manager--container">
	<div class="flights--manager-inner">
		<h1>Projects</h1>
		<div class="projects-filters">
			<p class="all-projects active-filter">All(<?php echo count($allFlights); ?>)</p>
			<p class="to-review-projects">To Review(<?php echo count(
       $toReviewFlights
   ); ?>)</p>
			<p class="live-projects">Live(<?php echo count($liveFlights); ?>)</p>
			<p class="fully-funded-projects">Fully Funded(<?php echo count(
       $fullyFundedFlights
   ); ?>)</p>
			<p class="archived-projects">Archived(#)</p>
			<p class="completed-projects">Completed(<?php echo count(
       $completedFlights
   ); ?>)</p>
		</div>
		<div class="flights active-flights-tab" id="all-projects">
			<table cellspacing="0" cellpadding="0">
				<th>Status</th>
				<th>Title</th>
				<th>Description</th>
				<th>Time Left</th>
				<th>Amount</th>
				<th># Donors</th>
				<th># Prayers</th>
				<th>Action</th> <?php foreach ($allFlights as $flight) {
        if ($flight->flight_fund_needed < 1) {
            $convertedFundGained = $getSettings[4]->settings_value . " " . 0;
        }

        //$flightFundNeeded < 1
        elseif ($flight->flight_fund_needed <= 10000) {
            $convertedFundGained =
                $getSettings[4]->settings_value .
                " " .
                $flight->flight_fund_needed;
        }

        //$flightFundNeeded >= 1000 && flightFundNeeded <= 99999
        else {
            $convertedFundGained =
                $getSettings[4]->settings_value .
                " " .
                $flight->flight_fund_needed / 1000000 .
                "M";
        }

        /* Fetching Prayers Table For Current Flight ID */

        $prayersTable = $wpdb->prefix . "flight_funders_prayer_donations";

        $currentPrayers = $wpdb->get_results(
            "SELECT * FROM $prayersTable WHERE `flight_id` = $flight->flight_id"
        );

        /* Fetching Money Table For Current Flight ID */

        $moneyTable = $wpdb->prefix . "flight_funders_money_donations";

        $currentMoney = $wpdb->get_results(
            "SELECT * FROM $moneyTable WHERE `flight_id` = $flight->flight_id"
        );
        ?> <tr class='solid'>
					<td class="flight--status">
						<p><?php echo $flight->flight_status; ?></p>
					</td>
					<td> <?php echo $flight->flight_title; ?> </td>
					<td> <?php echo substr($flight->flight_description, 0, 60) . "[...]"; ?> </td>
					<td> <?php
     $currentTime = date("d-m-Y H:i:s");

     $flightEndDate = date("d-m-Y H:i:s", strtotime($flight->flight_timeline));

     $flightFundingDeadline = date_diff(
         date_create($currentTime),
         date_create($flightEndDate)
     );

     if (strtotime($currentTime) >= strtotime($flightEndDate)) {
         echo "Ended";
     } else {
         echo $flightFundingDeadline->d .
             "D " .
             " " .
             $flightFundingDeadline->h .
             "H" .
             " " .
             $flightFundingDeadline->i .
             "M";
     }
     ?> </td>
					<td> <?php echo $convertedFundGained; ?> </td>
					<td> <?php echo count($currentMoney); ?> </td>
					<td> <?php echo count($currentPrayers); ?> </td>
					<td>
						<div class="action-buttons">
							<a href="<?php echo "https://" .
           $_SERVER["HTTP_HOST"] .
           $_SERVER["REQUEST_URI"] .
           "&view_flight=" .
           $flight->flight_id; ?>">
								<svg width="20" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M20 5.24002C20.0008 5.10841 19.9756 4.97795 19.9258 4.85611C19.876 4.73427 19.8027 4.62346 19.71 4.53002L15.47 0.290017C15.3766 0.197335 15.2658 0.12401 15.1439 0.0742455C15.0221 0.0244809 14.8916 -0.000744179 14.76 1.67143e-05C14.6284 -0.000744179 14.4979 0.0244809 14.3761 0.0742455C14.2543 0.12401 14.1435 0.197335 14.05 0.290017L11.22 3.12002L0.290017 14.05C0.197335 14.1435 0.12401 14.2543 0.0742455 14.3761C0.0244809 14.4979 -0.000744179 14.6284 1.67143e-05 14.76V19C1.67143e-05 19.2652 0.105374 19.5196 0.29291 19.7071C0.480446 19.8947 0.7348 20 1.00002 20H5.24002C5.37994 20.0076 5.51991 19.9857 5.65084 19.9358C5.78176 19.8858 5.90073 19.8089 6.00002 19.71L16.87 8.78002L19.71 6.00002C19.8013 5.9031 19.8757 5.79155 19.93 5.67002C19.9397 5.59031 19.9397 5.50973 19.93 5.43002C19.9347 5.38347 19.9347 5.33657 19.93 5.29002L20 5.24002ZM4.83002 18H2.00002V15.17L11.93 5.24002L14.76 8.07002L4.83002 18ZM16.17 6.66002L13.34 3.83002L14.76 2.42002L17.58 5.24002L16.17 6.66002Z" fill="black" />
								</svg>
							</a>
								<svg width="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="delete--flight" data-flight-id="<?php echo $flight->flight_id;?>">
									<path d="M7 16C7.26522 16 7.51957 15.8946 7.70711 15.7071C7.89464 15.5196 8 15.2652 8 15V9C8 8.73478 7.89464 8.48043 7.70711 8.29289C7.51957 8.10536 7.26522 8 7 8C6.73478 8 6.48043 8.10536 6.29289 8.29289C6.10536 8.48043 6 8.73478 6 9V15C6 15.2652 6.10536 15.5196 6.29289 15.7071C6.48043 15.8946 6.73478 16 7 16ZM17 4H13V3C13 2.20435 12.6839 1.44129 12.1213 0.87868C11.5587 0.316071 10.7956 0 10 0H8C7.20435 0 6.44129 0.316071 5.87868 0.87868C5.31607 1.44129 5 2.20435 5 3V4H1C0.734784 4 0.48043 4.10536 0.292893 4.29289C0.105357 4.48043 0 4.73478 0 5C0 5.26522 0.105357 5.51957 0.292893 5.70711C0.48043 5.89464 0.734784 6 1 6H2V17C2 17.7956 2.31607 18.5587 2.87868 19.1213C3.44129 19.6839 4.20435 20 5 20H13C13.7956 20 14.5587 19.6839 15.1213 19.1213C15.6839 18.5587 16 17.7956 16 17V6H17C17.2652 6 17.5196 5.89464 17.7071 5.70711C17.8946 5.51957 18 5.26522 18 5C18 4.73478 17.8946 4.48043 17.7071 4.29289C17.5196 4.10536 17.2652 4 17 4ZM7 3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H10C10.2652 2 10.5196 2.10536 10.7071 2.29289C10.8946 2.48043 11 2.73478 11 3V4H7V3ZM14 17C14 17.2652 13.8946 17.5196 13.7071 17.7071C13.5196 17.8946 13.2652 18 13 18H5C4.73478 18 4.48043 17.8946 4.29289 17.7071C4.10536 17.5196 4 17.2652 4 17V6H14V17ZM11 16C11.2652 16 11.5196 15.8946 11.7071 15.7071C11.8946 15.5196 12 15.2652 12 15V9C12 8.73478 11.8946 8.48043 11.7071 8.29289C11.5196 8.10536 11.2652 8 11 8C10.7348 8 10.4804 8.10536 10.2929 8.29289C10.1054 8.48043 10 8.73478 10 9V15C10 15.2652 10.1054 15.5196 10.2929 15.7071C10.4804 15.8946 10.7348 16 11 16Z" fill="black" />
								</svg>
							</a>
							<svg width="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px" class="notify">
									<path d="M21 8.5V13.5C21 17 19 18.5 16 18.5H6C3 18.5 1 17 1 13.5V6.5C1 3 3 1.5 6 1.5H13M6 7L9.13 9.5C10.16 10.32 11.85 10.32 12.88 9.5L14.06 8.56M18.5 6C19.163 6 19.7989 5.73661 20.2678 5.26777C20.7366 4.79893 21 4.16304 21 3.5C21 2.83696 20.7366 2.20107 20.2678 1.73223C19.7989 1.26339 19.163 1 18.5 1C17.837 1 17.2011 1.26339 16.7322 1.73223C16.2634 2.20107 16 2.83696 16 3.5C16 4.16304 16.2634 4.79893 16.7322 5.26777C17.2011 5.73661 17.837 6 18.5 6Z" stroke="black" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<div class="notify-popup">
								<div class="popup-form">
									<p style="margin: 0; font-size: 16px; font-weight: 600;">Send Notification</p>
									<form action="" method="post">
									<input type="hidden" name="flight_id" value="<?php echo $flight->flight_id;?>">
										<label>Notification Subject
											<input type="text" name="notify_subject" id="">
										</label>
										<label>Notification Message
											<textarea name="notify_message" rows="5" id=""></textarea>
										</label>
										<input type="submit" name="send_notification" value="Send Notification" style="background-color: #4D0071; color: #ffffff; height: 40px;">
									</form>
								</div>
								<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" class="close--notify" style="position: absolute; right: 20px;">
									<path d="M8.17 13.83L13.83 8.17M13.83 13.83L8.17 8.17M11 21C16.5 21 21 16.5 21 11C21 5.5 16.5 1 11 1C5.5 1 1 5.5 1 11C1 16.5 5.5 21 11 21Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
						</div>
						</div>
					</td>
				</tr> <?php
    } ?>
			</table>
		</div>
		<!-- To Review Projects Html -->


		<div class="flights" id="to-review-projects">


			<table cellspacing="0" cellpadding="0">


				<th>Status</th>


				<th>Title</th>


				<th>Description


				<th>Time Left


				<th>Amount


				<th># Donors


				<th># Prayers


				<th>Action</th>


				<?php foreach ($toReviewFlights as $toReviewFlight) {

        if ($toReviewFlight->flight_fund_needed < 1) {
            $convertedFundGained = $getSettings[4]->settings_value . " " . 0;
        }

        //$flightFundNeeded < 1
        elseif ($toReviewFlight->flight_fund_needed <= 10000) {
            $convertedFundGained =
                $getSettings[4]->settings_value .
                " " .
                $toReviewFlight->flight_fund_needed;
        }

        //$flightFundNeeded >= 1000 && flightFundNeeded <= 99999
        else {
            $convertedFundGained =
                $getSettings[4]->settings_value .
                " " .
                $toReviewFlight->flight_fund_needed / 1000000 .
                "M";
        }

        /* Fetching Prayers Table For Current Flight ID */

        $prayersTable = $wpdb->prefix . "flight_funders_prayer_donations";

        $currentPrayers = $wpdb->get_results(
            "SELECT * FROM $prayersTable WHERE `flight_id` = $toReviewFlight->flight_id"
        );

        /* Fetching Money Table For Current Flight ID */

        $moneyTable = $wpdb->prefix . "flight_funders_money_donations";

        $currentMoney = $wpdb->get_results(
            "SELECT * FROM $moneyTable WHERE `flight_id` = $toReviewFlight->flight_id"
        );
        ?>


				<tr class='solid'>


					<td class="flight--status">
						<p><?php echo $toReviewFlight->flight_status; ?></p>
					</td>


					<td> <?php echo $toReviewFlight->flight_title; ?> </td>


					<td> <?php echo substr($toReviewFlight->flight_description, 0, 60) .
         "[...]"; ?> </td>


					<td>


						<?php
      $currentTime = date("d-m-Y H:i:s");

      $flightEndDate = date(
          "d-m-Y H:i:s",
          strtotime($toReviewFlight->flight_timeline)
      );

      $flightFundingDeadline = date_diff(
          date_create($currentTime),
          date_create($flightEndDate)
      );

      if (strtotime($currentTime) >= strtotime($flightEndDate)) {
          echo "Ended";
      } else {
          echo $flightFundingDeadline->d .
              "D " .
              " " .
              $flightFundingDeadline->h .
              "H" .
              " " .
              $flightFundingDeadline->i .
              "M";
      }
      ?>


					</td>


					<td> <?php echo $convertedFundGained; ?> </td>


					<td> <?php echo count($currentMoney); ?> </td>


					<td> <?php echo count($currentPrayers); ?> </td>


					<td>
					<div class="action-buttons">
							<a href="<?php echo "https://" .
           $_SERVER["HTTP_HOST"] .
           $_SERVER["REQUEST_URI"] .
           "&view_flight=" .
           $flight->flight_id; ?>">
								<svg width="20" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M20 5.24002C20.0008 5.10841 19.9756 4.97795 19.9258 4.85611C19.876 4.73427 19.8027 4.62346 19.71 4.53002L15.47 0.290017C15.3766 0.197335 15.2658 0.12401 15.1439 0.0742455C15.0221 0.0244809 14.8916 -0.000744179 14.76 1.67143e-05C14.6284 -0.000744179 14.4979 0.0244809 14.3761 0.0742455C14.2543 0.12401 14.1435 0.197335 14.05 0.290017L11.22 3.12002L0.290017 14.05C0.197335 14.1435 0.12401 14.2543 0.0742455 14.3761C0.0244809 14.4979 -0.000744179 14.6284 1.67143e-05 14.76V19C1.67143e-05 19.2652 0.105374 19.5196 0.29291 19.7071C0.480446 19.8947 0.7348 20 1.00002 20H5.24002C5.37994 20.0076 5.51991 19.9857 5.65084 19.9358C5.78176 19.8858 5.90073 19.8089 6.00002 19.71L16.87 8.78002L19.71 6.00002C19.8013 5.9031 19.8757 5.79155 19.93 5.67002C19.9397 5.59031 19.9397 5.50973 19.93 5.43002C19.9347 5.38347 19.9347 5.33657 19.93 5.29002L20 5.24002ZM4.83002 18H2.00002V15.17L11.93 5.24002L14.76 8.07002L4.83002 18ZM16.17 6.66002L13.34 3.83002L14.76 2.42002L17.58 5.24002L16.17 6.66002Z" fill="black" />
								</svg>
							</a>
								<svg width="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="delete--flight" data-flight-id="<?php echo $flight->flight_id;?>">
									<path d="M7 16C7.26522 16 7.51957 15.8946 7.70711 15.7071C7.89464 15.5196 8 15.2652 8 15V9C8 8.73478 7.89464 8.48043 7.70711 8.29289C7.51957 8.10536 7.26522 8 7 8C6.73478 8 6.48043 8.10536 6.29289 8.29289C6.10536 8.48043 6 8.73478 6 9V15C6 15.2652 6.10536 15.5196 6.29289 15.7071C6.48043 15.8946 6.73478 16 7 16ZM17 4H13V3C13 2.20435 12.6839 1.44129 12.1213 0.87868C11.5587 0.316071 10.7956 0 10 0H8C7.20435 0 6.44129 0.316071 5.87868 0.87868C5.31607 1.44129 5 2.20435 5 3V4H1C0.734784 4 0.48043 4.10536 0.292893 4.29289C0.105357 4.48043 0 4.73478 0 5C0 5.26522 0.105357 5.51957 0.292893 5.70711C0.48043 5.89464 0.734784 6 1 6H2V17C2 17.7956 2.31607 18.5587 2.87868 19.1213C3.44129 19.6839 4.20435 20 5 20H13C13.7956 20 14.5587 19.6839 15.1213 19.1213C15.6839 18.5587 16 17.7956 16 17V6H17C17.2652 6 17.5196 5.89464 17.7071 5.70711C17.8946 5.51957 18 5.26522 18 5C18 4.73478 17.8946 4.48043 17.7071 4.29289C17.5196 4.10536 17.2652 4 17 4ZM7 3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H10C10.2652 2 10.5196 2.10536 10.7071 2.29289C10.8946 2.48043 11 2.73478 11 3V4H7V3ZM14 17C14 17.2652 13.8946 17.5196 13.7071 17.7071C13.5196 17.8946 13.2652 18 13 18H5C4.73478 18 4.48043 17.8946 4.29289 17.7071C4.10536 17.5196 4 17.2652 4 17V6H14V17ZM11 16C11.2652 16 11.5196 15.8946 11.7071 15.7071C11.8946 15.5196 12 15.2652 12 15V9C12 8.73478 11.8946 8.48043 11.7071 8.29289C11.5196 8.10536 11.2652 8 11 8C10.7348 8 10.4804 8.10536 10.2929 8.29289C10.1054 8.48043 10 8.73478 10 9V15C10 15.2652 10.1054 15.5196 10.2929 15.7071C10.4804 15.8946 10.7348 16 11 16Z" fill="black" />
								</svg>
							</a>
							<svg width="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px" class="notify">
									<path d="M21 8.5V13.5C21 17 19 18.5 16 18.5H6C3 18.5 1 17 1 13.5V6.5C1 3 3 1.5 6 1.5H13M6 7L9.13 9.5C10.16 10.32 11.85 10.32 12.88 9.5L14.06 8.56M18.5 6C19.163 6 19.7989 5.73661 20.2678 5.26777C20.7366 4.79893 21 4.16304 21 3.5C21 2.83696 20.7366 2.20107 20.2678 1.73223C19.7989 1.26339 19.163 1 18.5 1C17.837 1 17.2011 1.26339 16.7322 1.73223C16.2634 2.20107 16 2.83696 16 3.5C16 4.16304 16.2634 4.79893 16.7322 5.26777C17.2011 5.73661 17.837 6 18.5 6Z" stroke="black" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
						</div>
						<div class="notify-popup">
								<div class="popup-form">
									<p style="margin: 0; font-size: 16px; font-weight: 600;">Send Notification</p>
									<form action="" method="post">
									<input type="hidden" name="flight_id" value="<?php echo $flight->flight_id;?>">
										<label>Notification Subject
											<input type="text" name="notify_subject" id="">
										</label>
										<label>Notification Message
											<textarea name="notify_message" rows="5" id=""></textarea>
										</label>
										<input type="submit" name="send_notification" value="Send Notification" style="background-color: #4D0071; color: #ffffff; height: 40px;">
									</form>
								</div>
								<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" class="close--notify" style="position: absolute; right: 20px;">
									<path d="M8.17 13.83L13.83 8.17M13.83 13.83L8.17 8.17M11 21C16.5 21 21 16.5 21 11C21 5.5 16.5 1 11 1C5.5 1 1 5.5 1 11C1 16.5 5.5 21 11 21Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
						</div>
					</td>


				</tr>


				<?php
    } ?>


			</table>
		</div>
		<!-- Live Projects Html -->


		<div class="flights" id="live-projects">


			<table cellspacing="0" cellpadding="0">


				<th>Status</th>


				<th>Title</th>


				<th>Description


				<th>Time Left


				<th>Amount


				<th># Donors


				<th># Prayers


				<th>Action</th>


				<?php foreach ($liveFlights as $liveFlight) {

        if ($liveFlight->flight_fund_needed < 1) {
            $convertedFundGained = $getSettings[4]->settings_value . " " . 0;
        }

        //$flightFundNeeded < 1
        elseif ($liveFlight->flight_fund_needed <= 10000) {
            $convertedFundGained =
                $getSettings[4]->settings_value .
                " " .
                $liveFlight->flight_fund_needed;
        }

        //$flightFundNeeded >= 1000 && flightFundNeeded <= 99999
        else {
            $convertedFundGained =
                $getSettings[4]->settings_value .
                " " .
                $liveFlight->flight_fund_needed / 1000000 .
                "M";
        }

        /* Fetching Prayers Table For Current Flight ID */

        $prayersTable = $wpdb->prefix . "flight_funders_prayer_donations";

        $currentPrayers = $wpdb->get_results(
            "SELECT * FROM $prayersTable WHERE `flight_id` = $liveFlight->flight_id"
        );

        /* Fetching Money Table For Current Flight ID */

        $moneyTable = $wpdb->prefix . "flight_funders_money_donations";

        $currentMoney = $wpdb->get_results(
            "SELECT * FROM $moneyTable WHERE `flight_id` = $liveFlight->flight_id"
        );
        ?>


				<tr class='solid'>


					<td class="flight--status">
						<p><?php echo $liveFlight->flight_status; ?></p>
					</td>


					<td> <?php echo $liveFlight->flight_title; ?> </td>


					<td> <?php echo substr($liveFlight->flight_description, 0, 60) .
         "[...]"; ?> </td>


					<td>


<?php
      $currentTime = date("d-m-Y H:i:s");

      $flightEndDate = date(
          "d-m-Y H:i:s",
          strtotime($liveFlight->flight_timeline)
      );

      $flightFundingDeadline = date_diff(
          date_create($currentTime),
          date_create($flightEndDate)
      );

      if (strtotime($currentTime) >= strtotime($flightEndDate)) {
          echo "Ended";
      } else {
          echo $flightFundingDeadline->d .
              "D " .
              " " .
              $flightFundingDeadline->h .
              "H" .
              " " .
              $flightFundingDeadline->i .
              "M";
      }
      ?>
					</td>
					<td> <?php echo $convertedFundGained; ?> </td>
					<td> <?php echo count($currentMoney); ?> </td>
					<td> <?php echo count($currentPrayers); ?> </td>
					<td>
					<div class="action-buttons">
							<a href="<?php echo "https://" .
           $_SERVER["HTTP_HOST"] .
           $_SERVER["REQUEST_URI"] .
           "&view_flight=" .
           $liveFlight->flight_id; ?>">
								<svg width="20" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M20 5.24002C20.0008 5.10841 19.9756 4.97795 19.9258 4.85611C19.876 4.73427 19.8027 4.62346 19.71 4.53002L15.47 0.290017C15.3766 0.197335 15.2658 0.12401 15.1439 0.0742455C15.0221 0.0244809 14.8916 -0.000744179 14.76 1.67143e-05C14.6284 -0.000744179 14.4979 0.0244809 14.3761 0.0742455C14.2543 0.12401 14.1435 0.197335 14.05 0.290017L11.22 3.12002L0.290017 14.05C0.197335 14.1435 0.12401 14.2543 0.0742455 14.3761C0.0244809 14.4979 -0.000744179 14.6284 1.67143e-05 14.76V19C1.67143e-05 19.2652 0.105374 19.5196 0.29291 19.7071C0.480446 19.8947 0.7348 20 1.00002 20H5.24002C5.37994 20.0076 5.51991 19.9857 5.65084 19.9358C5.78176 19.8858 5.90073 19.8089 6.00002 19.71L16.87 8.78002L19.71 6.00002C19.8013 5.9031 19.8757 5.79155 19.93 5.67002C19.9397 5.59031 19.9397 5.50973 19.93 5.43002C19.9347 5.38347 19.9347 5.33657 19.93 5.29002L20 5.24002ZM4.83002 18H2.00002V15.17L11.93 5.24002L14.76 8.07002L4.83002 18ZM16.17 6.66002L13.34 3.83002L14.76 2.42002L17.58 5.24002L16.17 6.66002Z" fill="black" />
								</svg>
							</a>
								<svg width="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="delete--flight" data-flight-id="<?php echo $liveFlight->flight_id;?>">
									<path d="M7 16C7.26522 16 7.51957 15.8946 7.70711 15.7071C7.89464 15.5196 8 15.2652 8 15V9C8 8.73478 7.89464 8.48043 7.70711 8.29289C7.51957 8.10536 7.26522 8 7 8C6.73478 8 6.48043 8.10536 6.29289 8.29289C6.10536 8.48043 6 8.73478 6 9V15C6 15.2652 6.10536 15.5196 6.29289 15.7071C6.48043 15.8946 6.73478 16 7 16ZM17 4H13V3C13 2.20435 12.6839 1.44129 12.1213 0.87868C11.5587 0.316071 10.7956 0 10 0H8C7.20435 0 6.44129 0.316071 5.87868 0.87868C5.31607 1.44129 5 2.20435 5 3V4H1C0.734784 4 0.48043 4.10536 0.292893 4.29289C0.105357 4.48043 0 4.73478 0 5C0 5.26522 0.105357 5.51957 0.292893 5.70711C0.48043 5.89464 0.734784 6 1 6H2V17C2 17.7956 2.31607 18.5587 2.87868 19.1213C3.44129 19.6839 4.20435 20 5 20H13C13.7956 20 14.5587 19.6839 15.1213 19.1213C15.6839 18.5587 16 17.7956 16 17V6H17C17.2652 6 17.5196 5.89464 17.7071 5.70711C17.8946 5.51957 18 5.26522 18 5C18 4.73478 17.8946 4.48043 17.7071 4.29289C17.5196 4.10536 17.2652 4 17 4ZM7 3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H10C10.2652 2 10.5196 2.10536 10.7071 2.29289C10.8946 2.48043 11 2.73478 11 3V4H7V3ZM14 17C14 17.2652 13.8946 17.5196 13.7071 17.7071C13.5196 17.8946 13.2652 18 13 18H5C4.73478 18 4.48043 17.8946 4.29289 17.7071C4.10536 17.5196 4 17.2652 4 17V6H14V17ZM11 16C11.2652 16 11.5196 15.8946 11.7071 15.7071C11.8946 15.5196 12 15.2652 12 15V9C12 8.73478 11.8946 8.48043 11.7071 8.29289C11.5196 8.10536 11.2652 8 11 8C10.7348 8 10.4804 8.10536 10.2929 8.29289C10.1054 8.48043 10 8.73478 10 9V15C10 15.2652 10.1054 15.5196 10.2929 15.7071C10.4804 15.8946 10.7348 16 11 16Z" fill="black" />
								</svg>
							</a>
							<svg width="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px" class="notify">
									<path d="M21 8.5V13.5C21 17 19 18.5 16 18.5H6C3 18.5 1 17 1 13.5V6.5C1 3 3 1.5 6 1.5H13M6 7L9.13 9.5C10.16 10.32 11.85 10.32 12.88 9.5L14.06 8.56M18.5 6C19.163 6 19.7989 5.73661 20.2678 5.26777C20.7366 4.79893 21 4.16304 21 3.5C21 2.83696 20.7366 2.20107 20.2678 1.73223C19.7989 1.26339 19.163 1 18.5 1C17.837 1 17.2011 1.26339 16.7322 1.73223C16.2634 2.20107 16 2.83696 16 3.5C16 4.16304 16.2634 4.79893 16.7322 5.26777C17.2011 5.73661 17.837 6 18.5 6Z" stroke="black" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<div class="notify-popup">
								<div class="popup-form">
									<p style="margin: 0; font-size: 16px; font-weight: 600;">Send Notification</p>
									<form action="" method="post">
									<input type="hidden" name="flight_id" value="<?php echo $flight->flight_id;?>">
										<label>Notification Subject
											<input type="text" name="notify_subject" id="">
										</label>
										<label>Notification Message
											<textarea name="notify_message" rows="5" id=""></textarea>
										</label>
										<input type="submit" name="send_notification" value="Send Notification" style="background-color: #4D0071; color: #ffffff; height: 40px;">
									</form>
								</div>
								<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" class="close--notify" style="position: absolute; right: 20px;">
									<path d="M8.17 13.83L13.83 8.17M13.83 13.83L8.17 8.17M11 21C16.5 21 21 16.5 21 11C21 5.5 16.5 1 11 1C5.5 1 1 5.5 1 11C1 16.5 5.5 21 11 21Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
						</div>
						</div>
					</td>


				</tr>


				<?php
    } ?>


			</table>
		</div>



		<!-- Fully Funded Projects Html -->


		<div class="flights" id="fully-funded-projects">


			<table cellspacing="0" cellpadding="0">
				<th>Status</th>
				<th>Title</th>
				<th>Description
				<th>Time Left
				<th>Amount
				<th># Donors
				<th># Prayers
				<th>Action</th>
				<?php foreach ($fullyFundedFlights as $fullyFundedFlight) {

        if ($fullyFundedFlight->flight_fund_needed < 1) {
            $convertedFundGained = $getSettings[4]->settings_value . " " . 0;
        }

        //$flightFundNeeded < 1
        elseif ($fullyFundedFlight->flight_fund_needed <= 10000) {
            $convertedFundGained =
                $getSettings[4]->settings_value .
                " " .
                $fullyFundedFlight->flight_fund_needed;
        }

        //$flightFundNeeded >= 1000 && flightFundNeeded <= 99999
        else {
            $convertedFundGained =
                $getSettings[4]->settings_value .
                " " .
                $fullyFundedFlight->flight_fund_needed / 1000000 .
                "M";
        }

        /* Fetching Prayers Table For Current Flight ID */

        $prayersTable = $wpdb->prefix . "flight_funders_prayer_donations";

        $currentPrayers = $wpdb->get_results(
            "SELECT * FROM $prayersTable WHERE `flight_id` = $fullyFundedFlight->flight_id"
        );

        /* Fetching Money Table For Current Flight ID */

        $moneyTable = $wpdb->prefix . "flight_funders_money_donations";

        $currentMoney = $wpdb->get_results(
            "SELECT * FROM $moneyTable WHERE `flight_id` = $fullyFundedFlight->flight_id"
        );
        ?>


				<tr class='solid'>


					<td class="flight--status">
						<p><?php echo $fullyFundedFlight->flight_status; ?></p>
					</td>


					<td> <?php echo $fullyFundedFlight->flight_title; ?> </td>


					<td> <?php echo substr($fullyFundedFlight->flight_description, 0, 60) .
         "[...]"; ?> </td>


					<td>


						<?php
      $currentTime = date("d-m-Y H:i:s");

      $flightEndDate = date(
          "d-m-Y H:i:s",
          strtotime($fullyFundedFlight->flight_timeline)
      );

      $flightFundingDeadline = date_diff(
          date_create($currentTime),
          date_create($flightEndDate)
      );

      if (strtotime($currentTime) >= strtotime($flightEndDate)) {
          echo "Ended";
      } else {
          echo $flightFundingDeadline->d .
              "D " .
              " " .
              $flightFundingDeadline->h .
              "H" .
              " " .
              $flightFundingDeadline->i .
              "M";
      }
      ?>


					</td>


					<td> <?php echo $convertedFundGained; ?> </td>


					<td> <?php echo count($currentMoney); ?> </td>


					<td> <?php echo count($currentPrayers); ?> </td>


					<td>
					<div class="action-buttons">
							<a href="<?php echo "https://" .
           $_SERVER["HTTP_HOST"] .
           $_SERVER["REQUEST_URI"] .
           "&view_flight=" .
           $fullyFundedFlight->flight_id; ?>">
								<svg width="20" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M20 5.24002C20.0008 5.10841 19.9756 4.97795 19.9258 4.85611C19.876 4.73427 19.8027 4.62346 19.71 4.53002L15.47 0.290017C15.3766 0.197335 15.2658 0.12401 15.1439 0.0742455C15.0221 0.0244809 14.8916 -0.000744179 14.76 1.67143e-05C14.6284 -0.000744179 14.4979 0.0244809 14.3761 0.0742455C14.2543 0.12401 14.1435 0.197335 14.05 0.290017L11.22 3.12002L0.290017 14.05C0.197335 14.1435 0.12401 14.2543 0.0742455 14.3761C0.0244809 14.4979 -0.000744179 14.6284 1.67143e-05 14.76V19C1.67143e-05 19.2652 0.105374 19.5196 0.29291 19.7071C0.480446 19.8947 0.7348 20 1.00002 20H5.24002C5.37994 20.0076 5.51991 19.9857 5.65084 19.9358C5.78176 19.8858 5.90073 19.8089 6.00002 19.71L16.87 8.78002L19.71 6.00002C19.8013 5.9031 19.8757 5.79155 19.93 5.67002C19.9397 5.59031 19.9397 5.50973 19.93 5.43002C19.9347 5.38347 19.9347 5.33657 19.93 5.29002L20 5.24002ZM4.83002 18H2.00002V15.17L11.93 5.24002L14.76 8.07002L4.83002 18ZM16.17 6.66002L13.34 3.83002L14.76 2.42002L17.58 5.24002L16.17 6.66002Z" fill="black" />
								</svg>
							</a>
								<svg width="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="delete--flight" data-flight-id="<?php echo $fullyFundedFlight->flight_id;?>">
									<path d="M7 16C7.26522 16 7.51957 15.8946 7.70711 15.7071C7.89464 15.5196 8 15.2652 8 15V9C8 8.73478 7.89464 8.48043 7.70711 8.29289C7.51957 8.10536 7.26522 8 7 8C6.73478 8 6.48043 8.10536 6.29289 8.29289C6.10536 8.48043 6 8.73478 6 9V15C6 15.2652 6.10536 15.5196 6.29289 15.7071C6.48043 15.8946 6.73478 16 7 16ZM17 4H13V3C13 2.20435 12.6839 1.44129 12.1213 0.87868C11.5587 0.316071 10.7956 0 10 0H8C7.20435 0 6.44129 0.316071 5.87868 0.87868C5.31607 1.44129 5 2.20435 5 3V4H1C0.734784 4 0.48043 4.10536 0.292893 4.29289C0.105357 4.48043 0 4.73478 0 5C0 5.26522 0.105357 5.51957 0.292893 5.70711C0.48043 5.89464 0.734784 6 1 6H2V17C2 17.7956 2.31607 18.5587 2.87868 19.1213C3.44129 19.6839 4.20435 20 5 20H13C13.7956 20 14.5587 19.6839 15.1213 19.1213C15.6839 18.5587 16 17.7956 16 17V6H17C17.2652 6 17.5196 5.89464 17.7071 5.70711C17.8946 5.51957 18 5.26522 18 5C18 4.73478 17.8946 4.48043 17.7071 4.29289C17.5196 4.10536 17.2652 4 17 4ZM7 3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H10C10.2652 2 10.5196 2.10536 10.7071 2.29289C10.8946 2.48043 11 2.73478 11 3V4H7V3ZM14 17C14 17.2652 13.8946 17.5196 13.7071 17.7071C13.5196 17.8946 13.2652 18 13 18H5C4.73478 18 4.48043 17.8946 4.29289 17.7071C4.10536 17.5196 4 17.2652 4 17V6H14V17ZM11 16C11.2652 16 11.5196 15.8946 11.7071 15.7071C11.8946 15.5196 12 15.2652 12 15V9C12 8.73478 11.8946 8.48043 11.7071 8.29289C11.5196 8.10536 11.2652 8 11 8C10.7348 8 10.4804 8.10536 10.2929 8.29289C10.1054 8.48043 10 8.73478 10 9V15C10 15.2652 10.1054 15.5196 10.2929 15.7071C10.4804 15.8946 10.7348 16 11 16Z" fill="black" />
								</svg>
							</a>
							<svg width="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px" class="notify">
									<path d="M21 8.5V13.5C21 17 19 18.5 16 18.5H6C3 18.5 1 17 1 13.5V6.5C1 3 3 1.5 6 1.5H13M6 7L9.13 9.5C10.16 10.32 11.85 10.32 12.88 9.5L14.06 8.56M18.5 6C19.163 6 19.7989 5.73661 20.2678 5.26777C20.7366 4.79893 21 4.16304 21 3.5C21 2.83696 20.7366 2.20107 20.2678 1.73223C19.7989 1.26339 19.163 1 18.5 1C17.837 1 17.2011 1.26339 16.7322 1.73223C16.2634 2.20107 16 2.83696 16 3.5C16 4.16304 16.2634 4.79893 16.7322 5.26777C17.2011 5.73661 17.837 6 18.5 6Z" stroke="black" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<div class="notify-popup">
								<div class="popup-form">
									<p style="margin: 0; font-size: 16px; font-weight: 600;">Send Notification</p>
									<form action="" method="post">
									<input type="hidden" name="flight_id" value="<?php echo $flight->flight_id;?>">
										<label>Notification Subject
											<input type="text" name="notify_subject" id="">
										</label>
										<label>Notification Message
											<textarea name="notify_message" rows="5" id=""></textarea>
										</label>
										<input type="submit" name="send_notification" value="Send Notification" style="background-color: #4D0071; color: #ffffff; height: 40px;">
									</form>
								</div>
								<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" class="close--notify" style="position: absolute; right: 20px;">
									<path d="M8.17 13.83L13.83 8.17M13.83 13.83L8.17 8.17M11 21C16.5 21 21 16.5 21 11C21 5.5 16.5 1 11 1C5.5 1 1 5.5 1 11C1 16.5 5.5 21 11 21Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
						</div>
						</div>
					</td>


				</tr>


				<?php
    } ?>


			</table>
		</div>



		<!-- Completed Projects Html -->


		<div class="flights" id="completed-projects">


			<table cellspacing="0" cellpadding="0">


				<th>Status</th>


				<th>Title</th>


				<th>Description


				<th>Time Left


				<th>Amount


				<th># Donors


				<th># Prayers


				<th>Action</th>


				<?php foreach ($completedFlights as $completedFlight) {

        if ($completedFlight->flight_fund_needed < 1) {
            $convertedFundGained = $getSettings[4]->settings_value . " " . 0;
        }

        //$flightFundNeeded < 1
        elseif ($completedFlight->flight_fund_needed <= 1000) {
            $convertedFundGained =
                $getSettings[4]->settings_value .
                " " .
                $completedFlight->flight_fund_needed;
        }

        //$flightFundNeeded >= 1000 && flightFundNeeded <= 99999
        else {
            $convertedFundGained =
                $getSettings[4]->settings_value .
                " " .
                $completedFlight->flight_fund_needed / 1000000 .
                "M";
        }

        /* Fetching Prayers Table For Current Flight ID */

        $prayersTable = $wpdb->prefix . "flight_funders_prayer_donations";

        $currentPrayers = $wpdb->get_results(
            "SELECT * FROM $prayersTable WHERE `flight_id` = $completedFlight->flight_id"
        );

        /* Fetching Money Table For Current Flight ID */

        $moneyTable = $wpdb->prefix . "flight_funders_money_donations";

        $currentMoney = $wpdb->get_results(
            "SELECT * FROM $moneyTable WHERE `flight_id` = $completedFlight->flight_id"
        );
        ?>


				<tr class='solid'>


					<td class="flight--status">
						<p><?php echo $completedFlight->flight_status; ?></p>
					</td>


					<td> <?php echo $completedFlight->flight_title; ?> </td>


					<td> <?php echo substr($completedFlight->flight_description, 0, 60) .
         "[...]"; ?> </td>


					<td>


						<?php
      $currentTime = date("d-m-Y H:i:s");

      $flightEndDate = date(
          "d-m-Y H:i:s",
          strtotime($completedFlight->flight_timeline)
      );

      $flightFundingDeadline = date_diff(
          date_create($currentTime),
          date_create($flightEndDate)
      );

      if (strtotime($currentTime) >= strtotime($flightEndDate)) {
          echo "Ended";
      } else {
          echo $flightFundingDeadline->d .
              "D " .
              " " .
              $flightFundingDeadline->h .
              "H" .
              " " .
              $flightFundingDeadline->i .
              "M";
      }
      ?>


					</td>


					<td> <?php echo $convertedFundGained; ?> </td>


					<td> <?php echo count($currentMoney); ?> </td>


					<td> <?php echo count($currentPrayers); ?> </td>


					<td>
					<div class="action-buttons">
							<a href="<?php echo "https://" .
           $_SERVER["HTTP_HOST"] .
           $_SERVER["REQUEST_URI"] .
           "&view_flight=" .
           $completedFlight->flight_id; ?>">
								<svg width="20" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M20 5.24002C20.0008 5.10841 19.9756 4.97795 19.9258 4.85611C19.876 4.73427 19.8027 4.62346 19.71 4.53002L15.47 0.290017C15.3766 0.197335 15.2658 0.12401 15.1439 0.0742455C15.0221 0.0244809 14.8916 -0.000744179 14.76 1.67143e-05C14.6284 -0.000744179 14.4979 0.0244809 14.3761 0.0742455C14.2543 0.12401 14.1435 0.197335 14.05 0.290017L11.22 3.12002L0.290017 14.05C0.197335 14.1435 0.12401 14.2543 0.0742455 14.3761C0.0244809 14.4979 -0.000744179 14.6284 1.67143e-05 14.76V19C1.67143e-05 19.2652 0.105374 19.5196 0.29291 19.7071C0.480446 19.8947 0.7348 20 1.00002 20H5.24002C5.37994 20.0076 5.51991 19.9857 5.65084 19.9358C5.78176 19.8858 5.90073 19.8089 6.00002 19.71L16.87 8.78002L19.71 6.00002C19.8013 5.9031 19.8757 5.79155 19.93 5.67002C19.9397 5.59031 19.9397 5.50973 19.93 5.43002C19.9347 5.38347 19.9347 5.33657 19.93 5.29002L20 5.24002ZM4.83002 18H2.00002V15.17L11.93 5.24002L14.76 8.07002L4.83002 18ZM16.17 6.66002L13.34 3.83002L14.76 2.42002L17.58 5.24002L16.17 6.66002Z" fill="black" />
								</svg>
							</a>
								<svg width="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="delete--flight" data-flight-id="<?php echo $completedFlight->flight_id;?>">
									<path d="M7 16C7.26522 16 7.51957 15.8946 7.70711 15.7071C7.89464 15.5196 8 15.2652 8 15V9C8 8.73478 7.89464 8.48043 7.70711 8.29289C7.51957 8.10536 7.26522 8 7 8C6.73478 8 6.48043 8.10536 6.29289 8.29289C6.10536 8.48043 6 8.73478 6 9V15C6 15.2652 6.10536 15.5196 6.29289 15.7071C6.48043 15.8946 6.73478 16 7 16ZM17 4H13V3C13 2.20435 12.6839 1.44129 12.1213 0.87868C11.5587 0.316071 10.7956 0 10 0H8C7.20435 0 6.44129 0.316071 5.87868 0.87868C5.31607 1.44129 5 2.20435 5 3V4H1C0.734784 4 0.48043 4.10536 0.292893 4.29289C0.105357 4.48043 0 4.73478 0 5C0 5.26522 0.105357 5.51957 0.292893 5.70711C0.48043 5.89464 0.734784 6 1 6H2V17C2 17.7956 2.31607 18.5587 2.87868 19.1213C3.44129 19.6839 4.20435 20 5 20H13C13.7956 20 14.5587 19.6839 15.1213 19.1213C15.6839 18.5587 16 17.7956 16 17V6H17C17.2652 6 17.5196 5.89464 17.7071 5.70711C17.8946 5.51957 18 5.26522 18 5C18 4.73478 17.8946 4.48043 17.7071 4.29289C17.5196 4.10536 17.2652 4 17 4ZM7 3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H10C10.2652 2 10.5196 2.10536 10.7071 2.29289C10.8946 2.48043 11 2.73478 11 3V4H7V3ZM14 17C14 17.2652 13.8946 17.5196 13.7071 17.7071C13.5196 17.8946 13.2652 18 13 18H5C4.73478 18 4.48043 17.8946 4.29289 17.7071C4.10536 17.5196 4 17.2652 4 17V6H14V17ZM11 16C11.2652 16 11.5196 15.8946 11.7071 15.7071C11.8946 15.5196 12 15.2652 12 15V9C12 8.73478 11.8946 8.48043 11.7071 8.29289C11.5196 8.10536 11.2652 8 11 8C10.7348 8 10.4804 8.10536 10.2929 8.29289C10.1054 8.48043 10 8.73478 10 9V15C10 15.2652 10.1054 15.5196 10.2929 15.7071C10.4804 15.8946 10.7348 16 11 16Z" fill="black" />
								</svg>
							</a>
							<svg width="20" viewBox="0 0 22 20" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-top: -5px" class="notify">
									<path d="M21 8.5V13.5C21 17 19 18.5 16 18.5H6C3 18.5 1 17 1 13.5V6.5C1 3 3 1.5 6 1.5H13M6 7L9.13 9.5C10.16 10.32 11.85 10.32 12.88 9.5L14.06 8.56M18.5 6C19.163 6 19.7989 5.73661 20.2678 5.26777C20.7366 4.79893 21 4.16304 21 3.5C21 2.83696 20.7366 2.20107 20.2678 1.73223C19.7989 1.26339 19.163 1 18.5 1C17.837 1 17.2011 1.26339 16.7322 1.73223C16.2634 2.20107 16 2.83696 16 3.5C16 4.16304 16.2634 4.79893 16.7322 5.26777C17.2011 5.73661 17.837 6 18.5 6Z" stroke="black" stroke-width="2" stroke-miterlimit="10" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<div class="notify-popup">
								<div class="popup-form">
									<p style="margin: 0; font-size: 16px; font-weight: 600;">Send Notification</p>
									<form action="" method="post">
									<input type="hidden" name="flight_id" value="<?php echo $flight->flight_id;?>">
										<label>Notification Subject
											<input type="text" name="notify_subject" id="">
										</label>
										<label>Notification Message
											<textarea name="notify_message" rows="5" id=""></textarea>
										</label>
										<input type="submit" name="send_notification" value="Send Notification" style="background-color: #4D0071; color: #ffffff; height: 40px;">
									</form>
								</div>
								<svg width="22" height="22" viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg" class="close--notify" style="position: absolute; right: 20px;">
									<path d="M8.17 13.83L13.83 8.17M13.83 13.83L8.17 8.17M11 21C16.5 21 21 16.5 21 11C21 5.5 16.5 1 11 1C5.5 1 1 5.5 1 11C1 16.5 5.5 21 11 21Z" stroke="black" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
						</div>
						</div>
					</td>


				</tr>


				<?php
    } ?>


			</table>
		</div>
		<button class="create--new--project" style="background-color: #4D0071; color: #ffffff; height: 35px; width: 180px; border: none; border-radius: 4px; display: flex; justify-content: center; align-items: center; margin: 10px auto; cursor: pointer;">+ Create New Project</button>

		<!-- Create new project popup -->
		<div class="new-project-popup">
		<div class="request--flight--header">
				<div></div>
				<div>
					<p>Create New Project</p>
				</div>
				<div class="header--buttons">
					<button class="close--new--project"><<  Back</button>
					<input type="submit" name="create--project" class="request--flight--button" value="Create Project" form="create-new-project">
				</div>
			</div>
			<div class="flight--request--inner">
					<div class="left--inner">
					<form action="" method="POST" id="create-new-project" enctype="multipart/form-data">
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
						<label>Fund Needed (In <?php echo $getSettings[4]->settings_value; ?>) <label>
								<input type="number" name="project_fund_needed">
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
</div>
	</div>
</div>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');
	#wpbody {
		position: relative;
		margin-left: -20px;
		background-color: #FFFFFF;
	}

	.flights--manager--container {
		height: 650px;
		width: 100%;
		margin: 0;
		padding: 0;
		display: flex;
		flex-direction: column;
		font-family: Poppins;
	}

	.flights--manager-inner {
		height: 60%;
		width: 100%;
		margin: auto;
	}

	.flights--manager-inner h1,
	h2,
	h3,
	h4,
	h5,
	h6 {
		text-align: center;
	}

	.projects-filters {
		display: flex;
		justify-content: center;
		gap: 10px;
	}

	.projects-filters p {
		cursor: pointer;
		font-weight: 500;
	}

	.projects-filters p.active-filter {
    	color: #4D0071;
	}

	.flights {
		display: none;
		width: 80%;
		height: 80%;
		margin: auto;
		border: solid 1px #000000;
		border-radius: 10px;
		padding: 10px;
		overflow-y: scroll;
	}

	.active-flights-tab {
		display: block !important;
	}

	.flights::-webkit-scrollbar {
		width: 5px;
	}

	.flights::-webkit-scrollbar-thumb {
		width: 5px;
		border-radius: 100px;
		background-color: #4D0071;
	}

	.flights table {
		width: 100%;
		border-spacing: 0;
		border-collapse: seperate;
		border-spacing: 0 10px;
		overflow-y: scroll !important;
	}

	.flights table::-webkit-scrollbar {
		display: none;
	}

	.flights table tbody tr:first-child {
		background-color: #4D0071;
		border-radius: 10px;
		position: sticky;
		top: 0;
	}

	.flights table th {
		color: #FFFFFF;
		padding: 10px;
		font-weight: 500;
	}

	.flights table td {
		padding: 10px;
		margin: auto;
		text-align: center;
		border: none;
	}

	.flights table tr.solid td:nth-child(1) {
		width: 80px;
	}

	.flights table tr.solid td:nth-child(2),
	.flights table tr.solid td:nth-child(3) {
		width: 20%;
		text-align: left;
	}

	.flights table td.flight--status {
		text-transform: capitalize;
	}
	td.flight--status p {
		height: 30px;
		background-color: #00000010;
		border-radius: 6px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	td.flight--status select {
		border: none;
		background-color: #00000014;
	}

	.flights table tr.solid td div.action-buttons {
		display: flex;
		gap: 10px;
		justify-content: center;
		align-items: center;
	}

	.flights table tr.solid td div.action-buttons svg {
		cursor: pointer;
		width: 15px;
	}

	.notify-popup {
		border: solid 2px #000;
		background: #fff;
		height: 50%;
		width: 30%;
		position: absolute;
		left: 50%;
		top: 50%;
		transform: translate(-50%, -50%);
		backdrop-filter: blur(10px);
		border-radius: 10px;
		display: none;
		padding: 20px;
	}

	.notify-popup div {
		width: 100%;
		display: flex;
		flex-direction: column;
	}

	.notify-popup div p {
		cursor: pointer;
	}

	.notify-popup div form {
		display: flex;
		flex-direction: column;
		gap: 10px;
		margin: auto;
		width: 100%;
}

	.notify-popup div form label {
		display: flex;
		flex-direction: column;
		gap: 5px;
		text-align: left;
	}

	.flights table tr.solid td div.action-buttons svg.delete--flight {
    	margin-top: -6px;
	}

	.new-project-popup {
		position: absolute;
		top: 0;
		left: 50%;
		transform: translate(-50%,0);
		background-color: #ffffff;
		width: 90%;
		height: 100%;
		display: none;
		flex-direction: column;
	}

	.request--flight--header {
		display: flex;
		justify-content: space-between;
		align-items: center;
		height: 60px;
	}

	.request--flight--header p {
		font-size: 16px;
		font-weight: 600;
	}

	.request--flight--header .header--buttons {
		display: flex;
		gap: 10px;
	}

	.request--flight--header .header--buttons button {
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

	.request--flight--header .header--buttons--header a {
		text-decoration: none !important;
	}

	.request--flight--header .header--buttons button:nth-child(1) {
		border: solid 1px #000000;
	}

	.request--flight--header .header--buttons input {
		background-color: #4D0071 !important;
		color: #ffffff;
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

	.new-project-popup form {
		display: flex;
		flex-direction: column;
		gap: 20px;
	}

	.new-project-popup form label {
		display: flex;
		flex-direction: column;
		gap: 5px;
	}

	.new-project-popup form textarea {
		height: 120px;
		resize: none;
	}

	.new-project-popup form label input[type='file'] {
		display: none;
	}

	.image--container {
		height: 160px;
		border: dashed 2px #333333;
		border-radius: 6px;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.image--container img {
		width: 100%;
		height: 100%;
		border-radius: 6px;
		object-fit: cover;
	}

	.flight--request--inner {
		height: 480px;
		width: 60%;
		overflow-y: scroll;
		padding: 20px;
		margin: auto;
	}

	.flight--request--inner::-webkit-scrollbar {
		width: 5px;
	}

	.flight--request--inner::-webkit-scrollbar-thumb {
		width: 5px;
		background-color: #4D0071;
		border-radius: 100px;
	}
</style>
<script>
	$(document).ready(function() {

		$(".all-projects").on("click", function() {
			$(".projects-filters p").removeClass("active-filter");
			$(this).addClass("active-filter");
			$("#all-projects").addClass("active-flights-tab");
			$("#to-review-projects").removeClass("active-flights-tab");
			$("#live-projects").removeClass("active-flights-tab");
			$("#fully-funded-projects").removeClass("active-flights-tab");
			$("#completed-projects").removeClass("active-flights-tab");
		})
		$(".to-review-projects").on("click", function() {
			$(".projects-filters p").removeClass("active-filter");
			$(this).addClass("active-filter");
			$("#all-projects").removeClass("active-flights-tab");
			$("#to-review-projects").addClass("active-flights-tab");
			$("#live-projects").removeClass("active-flights-tab");
			$("#fully-funded-projects").removeClass("active-flights-tab");
			$("#completed-projects").removeClass("active-flights-tab");
		})
		$(".live-projects").on("click", function() {
			$(".projects-filters p").removeClass("active-filter");
			$(this).addClass("active-filter");
			$("#all-projects").removeClass("active-flights-tab");
			$("#to-review-projects").removeClass("active-flights-tab");
			$("#live-projects").addClass("active-flights-tab");
			$("#fully-funded-projects").removeClass("active-flights-tab");
			$("#completed-projects").removeClass("active-flights-tab");
		})
		$(".fully-funded-projects").on("click", function() {
			$(".projects-filters p").removeClass("active-filter");
			$(this).addClass("active-filter");
			$("#all-projects").removeClass("active-flights-tab");
			$("#to-review-projects").removeClass("active-flights-tab");
			$("#live-projects").removeClass("active-flights-tab");
			$("#fully-funded-projects").addClass("active-flights-tab");
			$("#completed-projects").removeClass("active-flights-tab");
		})
		$(".completed-projects").on("click", function() {
			$(".projects-filters p").removeClass("active-filter");
			$(this).addClass("active-filter");
			$("#all-projects").removeClass("active-flights-tab");
			$("#to-review-projects").removeClass("active-flights-tab");
			$("#live-projects").removeClass("active-flights-tab");
			$("#fully-funded-projects").removeClass("active-flights-tab");
			$("#completed-projects").addClass("active-flights-tab");
		})
		
		var notify = document.querySelectorAll(".notify");
		var notifyCount = notify.length;

		for(let i=0; i<notifyCount;i++){
			notify[i].addEventListener("click",function(){
				document.querySelectorAll(".notify-popup")[i].style.display = "flex";
				document.querySelectorAll(".notify-popup input[type='submit']")[i].addEventListener("click",function(event){
					event.preventDefault();
					var flight_id = document.querySelectorAll(".notify-popup input[name='flight_id']")[i].value;
					var email_subject = document.querySelectorAll(".notify-popup input[name='notify_subject']")[i].value;
					var email_message = document.querySelectorAll(".notify-popup textarea[name='notify_message']")[i].value;
					$.ajax({
						type: "POST",
						url: window.location.origin + "/wp-content/plugins/project-funders/admin/api/project-funders-notifications.php",
						data: "flight_id=" + flight_id + "&email_subject=" + email_subject + "&email_message=" + email_message,
						success: function (response) {
							console.log(response);
						}
					});
			})
			})
		}

		var deleteFlight = document.querySelectorAll(".delete--flight");
		var flightsCount = deleteFlight.length;
		for(let i=0; i<flightsCount; i++){
			deleteFlight[i].addEventListener("click",function(){
				var flightId = deleteFlight[i].getAttribute("data-flight-id");
				$.ajax({
					url: window.location.origin + "/wp-content/plugins/project-funders/admin/api/project-funders-flight-delete.php",
					data: "flight_id=" + flightId,
					type: "POST",
					success: function(success){
						if(success === "Flight deleted"){
							alert("Current flight has been deleted");
							window.location.href = window.location.href;
						}
					}
				})
			})
		}

	})

	$(".close--notify").on("click",function(){
		$(".notify-popup").hide();
	})

	$(document).ready(function() {
        $(".image--container input[type='file']").on("change",function(event){
            $(".image--container img").show();
            $(".image--container svg").hide();
            $(".image--container").css("border","none");
			$(".image--container img").prop("srcset",URL.createObjectURL(event.target.files[0]))
		})
    })

	$(".create--new--project").on("click",function(){
		$(".new-project-popup").css("display","flex");
	})

	$(".close--new--project").on("click",function(){
		$(".new-project-popup").hide();
	})

	$("#create-new-project").on("submit",function(event){
		event.preventDefault();
		var payload = new FormData(this);
		$.ajax({
			type: "POST",
			url: window.location.origin + "/wp-content/plugins/project-funders/admin/api/project-funders-new-project.php",
			data: payload,
			contentType: false,
			processData: false,
			success: function (response) {
				if(response === "Project Inserted"){
					alert("New project inserted");
					window.location.href = window.location.href; 
				}
			}
		});
	})
</script>
<?php
 }
}


/* Flight Funders notification panel code */
function project_funders_notification_manager() {
	global $wpdb;
	$notificationTable = $wpdb->prefix . "flight_funders_notifications";
	$notifications = $wpdb->get_results(
		"SELECT * FROM $notificationTable"
	);
	?>
	<!-- Flight Manager Html Code -->
<div class="notifications--manager--container">
	<div class="notifications--manager-inner">
		<h1>Notification</h1>
		<div class="notifications">
			<table cellspacing="0" cellpadding="0">
				<th>Title</th>
				<th>Subject</th>
				<th>Message</th>
				<th>Date & Time</th>
				<?php
					foreach($notifications as $notification){
						?>
						<tr>
							<td><?php echo $notification->flight_title;?></td>
							<td><?php echo $notification->notification_subject;?></td>
							<td><?php echo $notification->notification_message;?></td>
							<td><?php echo date("M d, Y h:i A",strtotime($notification->notification_timestamp));?></td>
						</tr>
						<?php
					}
					?>
			</table>
		</div>
</div>
</div>

<style>
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');
	#wpbody {
		position: relative;
		margin-left: -20px;
		background-color: #FFFFFF;
	}

	.notifications--manager--container {
		height: 650px;
		width: 100%;
		margin: 0;
		padding: 0;
		display: flex;
		flex-direction: column;
		font-family: Poppins;
	}

	.notifications--manager-inner {
		height: 60%;
		width: 100%;
		margin: auto;
	}

	.notifications--manager-inner h1,
	h2,
	h3,
	h4,
	h5,
	h6 {
		text-align: center;
	}

	.notifications {
		width: 80%;
		height: 100%;
		margin: auto;
		border: solid 1px #000000;
		border-radius: 10px;
		padding: 10px;
		overflow-y: scroll;
	}

	.active-notifications-tab {
		display: block !important;
	}

	.notifications::-webkit-scrollbar {
		width: 5px;
	}

	.notifications::-webkit-scrollbar-thumb {
		width: 5px;
		border-radius: 100px;
		background-color: #4D0071;
	}

	.notifications table {
		width: 100%;
		border-spacing: 0;
		border-collapse: seperate;
		border-spacing: 0 10px;
		overflow-y: scroll !important;
	}

	.notifications table::-webkit-scrollbar {
		display: none;
	}

	.notifications table tbody tr:first-child {
		background-color: #4D0071;
		border-radius: 10px;
		position: sticky;
		top: 0;
	}

	.notifications table th {
		color: #FFFFFF;
		padding: 10px;
		font-weight: 500;
	}

	.notifications table td {
		padding: 10px;
		margin: auto;
		text-align: center;
		border: none;
	}

	.notifications table tr.solid td:nth-child(1) {
		width: 80px;
	}

	.notifications table tr.solid td:nth-child(2),

	.notifications table tr.solid td:nth-child(3) {
		width: 20%;
	}

	.notifications table td.flight--status {
		text-transform: capitalize;
	}
	td.flight--status p {
		height: 30px;
		background-color: #00000010;
		border-radius: 6px;
		display: flex;
		align-items: center;
		justify-content: center;
	}
	td.flight--status select {
		border: none;
		background-color: #00000014;
	}

	.notifications table tr.solid td div.action-buttons {
		display: flex;
		gap: 10px;
		justify-content: center;
		align-items: center;
	}

	.notifications table tr.solid td div.action-buttons svg {
		cursor: pointer;
		width: 15px;
	}
</style>
	<?php
}


/* flight-funders users management content */

function project_funders_user_manager()
{
    global $wpdb;

    $usersTable = $wpdb->prefix . "flight_funders_users";

    $users = $wpdb->get_results("SELECT * FROM $usersTable");
    ?> <?php if (isset($_GET["view_user"]) && $_GET["view_user"] != "") {

     $currentUser = $_GET["view_user"];

     if (isset($_POST["update_user"])) {
         $donorUserId = $_POST["user_id"];

         $donorName = $_POST["flight_user_name"];

         $donorEmail = $_POST["flight_user_email"];

         $donorMobile = $_POST["flight_user_mobile"];

         $updateUser = $wpdb->update(
             "$usersTable",

             [
                 "flight_user_name" => "$donorName",
                 "flight_user_email" => "$donorEmail",
                 "flight_user_mobile" => "$donorMobile",
             ],

             ["id" => "$donorUserId"]
         );

         if ($updateUser) {
             $userUpdationStatus =
                 "Current user details has been updated " . ✅;
         }
     }

     $currentUserDetails = $wpdb->get_results(
         "SELECT * FROM $usersTable WHERE `id`='$currentUser'"
     );
     ?>
<!-- Rendering view current user in admin panel -->
<div class="users--manager--container">
	<div class="users--manager-inner">
		<h1>Update User</h1>
		<div class="users">
			<form action="" method="POST">
				<input type="hidden" name="user_id" value="<?php echo $currentUserDetails[0]->id; ?>">
				<label>User Full Name <input type="text" name="flight_user_name" value="<?php echo $currentUserDetails[0]->flight_user_name; ?>">
				</label>
				<label>User Email <input type="text" name="flight_user_email" value="<?php echo $currentUserDetails[0]->flight_user_email; ?>">
				</label>
				<label>User Mobile <input type="text" name="flight_user_mobile" value="<?php echo $currentUserDetails[0]->flight_user_mobile; ?>">
				</label>
				<input type="submit" name="update_user" value="Update User">
			</form>
			<p> <?php echo $userUpdationStatus; ?> </p>
		</div>
	</div>
</div>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');
	#wpbody {
		position: relative;
		margin-left: -20px;
		background-color: #ffffff;
	}

	.users--manager--container {
		height: 650px;
		width: 100%;
		margin: 0;
		padding: 0;
		display: flex;
		flex-direction: column;
		font-family: Poppins;
	}

	.users--manager-inner {
		height: 60%;
		width: 100%;
		margin: auto;
	}

	.users--manager-inner h1,
	h2,
	h3,
	h4,
	h5,
	h6 {
		text-align: center;
	}

	.users {
		display: flex;
		flex-direction: column;
		gap: 10px;
		width: 30%;
		height: 100%;
		margin: auto;
		padding: 20px;
		border-radius: 10px;
	}

	.users form {
		display: flex;
		flex-direction: column;
		gap: 10px;
		width: 100%;
	}

	.users form label {
		font-weight: 500;
		font-size: 14px;
		display: flex;
		flex-direction: column;
		gap: 5px;
	}

	.users form input[name='update_user'] {
		width: 100%;
		height: 45px;
		border: none;
		outline: none;
		border-radius: 8px !important;
		background-color: #4D0071;
		color: #FFF;
		cursor: pointer;
	}

	.users form input[name='update_user']:hover {
		background-color: #2271b1d1;
	}

	.users form input[name='delete_user'] {
		width: 100%;
		height: 45px;
		border: none;
		outline: none;
		border-radius: 8px !important;
		background-color: red;
		color: #FFF;
		cursor: pointer;
	}

	.users form input[name='delete_user']:hover {
		background-color: #ff0000a3;
	}

	.users p {
		margin: 0;
	}
</style> <?php
 } else {
      ?> <div class="users--manager--container">
	<div class="users--manager-inner">
		<h1>Users Manager</h1>
		<div class="users">
			<div>
				<table cellspacing="0" cellpadding="0">
					<th>User Id</th>
					<th>User Fullname</th>
					<th>User Email</th>
					<th>User Mobile</th>
					<th>Action</th> <?php foreach ($users as $user) { ?> <tr>
						<td> <?php echo $user->id; ?> </td>
						<td> <?php echo $user->flight_user_name; ?> </td>
						<td> <?php echo $user->flight_user_email; ?> </td>
						<td> <?php echo $user->flight_user_mobile; ?> </td>
						<td>
							<div class="action-buttons" style="display: flex; justify-content: space-around;">
								<a href="<?php echo "https://" .
            $_SERVER["HTTP_HOST"] .
            $_SERVER["REQUEST_URI"] .
            "&view_user=" .
            $user->id; ?>">
									<svg width="20" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M20 5.24002C20.0008 5.10841 19.9756 4.97795 19.9258 4.85611C19.876 4.73427 19.8027 4.62346 19.71 4.53002L15.47 0.290017C15.3766 0.197335 15.2658 0.12401 15.1439 0.0742455C15.0221 0.0244809 14.8916 -0.000744179 14.76 1.67143e-05C14.6284 -0.000744179 14.4979 0.0244809 14.3761 0.0742455C14.2543 0.12401 14.1435 0.197335 14.05 0.290017L11.22 3.12002L0.290017 14.05C0.197335 14.1435 0.12401 14.2543 0.0742455 14.3761C0.0244809 14.4979 -0.000744179 14.6284 1.67143e-05 14.76V19C1.67143e-05 19.2652 0.105374 19.5196 0.29291 19.7071C0.480446 19.8947 0.7348 20 1.00002 20H5.24002C5.37994 20.0076 5.51991 19.9857 5.65084 19.9358C5.78176 19.8858 5.90073 19.8089 6.00002 19.71L16.87 8.78002L19.71 6.00002C19.8013 5.9031 19.8757 5.79155 19.93 5.67002C19.9397 5.59031 19.9397 5.50973 19.93 5.43002C19.9347 5.38347 19.9347 5.33657 19.93 5.29002L20 5.24002ZM4.83002 18H2.00002V15.17L11.93 5.24002L14.76 8.07002L4.83002 18ZM16.17 6.66002L13.34 3.83002L14.76 2.42002L17.58 5.24002L16.17 6.66002Z" fill="black" />
									</svg>
								</a>
									<svg width="20" viewBox="0 0 18 20" fill="none" xmlns="http://www.w3.org/2000/svg" class="delete--user" data-user-id="<?php echo $user->id;?>" style="margin-top: -6px">
										<path d="M7 16C7.26522 16 7.51957 15.8946 7.70711 15.7071C7.89464 15.5196 8 15.2652 8 15V9C8 8.73478 7.89464 8.48043 7.70711 8.29289C7.51957 8.10536 7.26522 8 7 8C6.73478 8 6.48043 8.10536 6.29289 8.29289C6.10536 8.48043 6 8.73478 6 9V15C6 15.2652 6.10536 15.5196 6.29289 15.7071C6.48043 15.8946 6.73478 16 7 16ZM17 4H13V3C13 2.20435 12.6839 1.44129 12.1213 0.87868C11.5587 0.316071 10.7956 0 10 0H8C7.20435 0 6.44129 0.316071 5.87868 0.87868C5.31607 1.44129 5 2.20435 5 3V4H1C0.734784 4 0.48043 4.10536 0.292893 4.29289C0.105357 4.48043 0 4.73478 0 5C0 5.26522 0.105357 5.51957 0.292893 5.70711C0.48043 5.89464 0.734784 6 1 6H2V17C2 17.7956 2.31607 18.5587 2.87868 19.1213C3.44129 19.6839 4.20435 20 5 20H13C13.7956 20 14.5587 19.6839 15.1213 19.1213C15.6839 18.5587 16 17.7956 16 17V6H17C17.2652 6 17.5196 5.89464 17.7071 5.70711C17.8946 5.51957 18 5.26522 18 5C18 4.73478 17.8946 4.48043 17.7071 4.29289C17.5196 4.10536 17.2652 4 17 4ZM7 3C7 2.73478 7.10536 2.48043 7.29289 2.29289C7.48043 2.10536 7.73478 2 8 2H10C10.2652 2 10.5196 2.10536 10.7071 2.29289C10.8946 2.48043 11 2.73478 11 3V4H7V3ZM14 17C14 17.2652 13.8946 17.5196 13.7071 17.7071C13.5196 17.8946 13.2652 18 13 18H5C4.73478 18 4.48043 17.8946 4.29289 17.7071C4.10536 17.5196 4 17.2652 4 17V6H14V17ZM11 16C11.2652 16 11.5196 15.8946 11.7071 15.7071C11.8946 15.5196 12 15.2652 12 15V9C12 8.73478 11.8946 8.48043 11.7071 8.29289C11.5196 8.10536 11.2652 8 11 8C10.7348 8 10.4804 8.10536 10.2929 8.29289C10.1054 8.48043 10 8.73478 10 9V15C10 15.2652 10.1054 15.5196 10.2929 15.7071C10.4804 15.8946 10.7348 16 11 16Z" fill="black" />
									</svg>
							</div>
						</td>
					</tr> <?php } ?>
				</table>
			</div>
		</div>
	</div>
</div>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');
	#wpbody {
		position: relative;
		margin-left: -20px;
		background-color: #ffffff;
	}

	.users--manager--container {
		height: 650px;
		width: 100%;
		margin: 0;
		padding: 0;
		display: flex;
		flex-direction: column;
		font-family: Poppins;
	}

	.users--manager-inner {
		height: 60%;
		width: 100%;
		margin: auto;
	}

	.users--manager-inner h1,
	h2,
	h3,
	h4,
	h5,
	h6 {
		text-align: center;
	}

	.users {
		width: 80%;
		height: 100%;
		margin: auto;
		border: solid 1px #000000;
		border-radius: 10px;
		padding: 10px;
		overflow-y: scroll;
	}

	.users::-webkit-scrollbar {
		width: 5px;
	}

	.users::-webkit-scrollbar-thumb {
		width: 5px;
		border-radius: 100px;
		background-color: #4D0071;
	}

	.users table {
		width: 100%;
		border-spacing: 0;
		border-collapse: seperate;
		border-spacing: 0 10px;
		overflow-y: scroll !important;
	}

	.users table::-webkit-scrollbar {
		display: none;
	}

	.users table tr.solid {}

	.users table tbody tr:first-child {
		background-color: #4D0071;
		border-radius: 10px;
	}

	.users table th {
		color: #FFFFFF;
		padding: 10px;
		font-weight: 500;
	}

	.users table td {
		padding: 10px;
		margin: auto;
		text-align: center;
		border: none;
	}

	.users table tr.solid td:nth-child(1) {
		width: 80px;
	}

	.users table tr.solid td:nth-child(2),

	.users table tr.solid td:nth-child(3) {
		width: 20%;
	}

	.users table td.flight--status {
		text-transform: capitalize;
	}
	td.flight--status select {
		border: none;
		background-color: #00000014;
	}

	.users table tr.solid td div.action-buttons {
		display: flex;
		gap: 10px;
		justify-content: center;
		align-items: center;
	}

	.action-buttons svg {
		cursor: pointer;
		width: 15px;
	}
</style>

<script>

var deleteUser = document.querySelectorAll(".delete--user");
		var usersCount = deleteUser.length;
		for(let i=0; i<usersCount; i++){
			deleteUser[i].addEventListener("click",function(){
				var userId = deleteUser[i].getAttribute("data-user-id");
				$.ajax({
					url: window.location.origin + "/wp-content/plugins/project-funders/admin/api/project-funders-user-delete.php",
					data: "user_id=" + userId,
					type: "POST",
					success: function(success){
						if(success === "User deleted"){
							alert("Current user has been deleted");
							window.location.href = window.location.href;
						}
					}
				})
			})
		}

</script>

<?php
 } ?> <?php
}

/* flight-funders settings content */

function project_funders_settings()
{
    global $wpdb;

    /* Fetching settings from database */

    $table = $wpdb->prefix . "flight_funders_settings";

    $settings = $wpdb->get_results("SELECT * FROM $table");

    $paypalEmail = $settings[0]->settings_value;

    $paypalSandboxStatus = $settings[1]->settings_value;

    $paypalSuccessPage = $settings[2]->settings_value;

    $paypalFailurePage = $settings[3]->settings_value;

    $settingsCurrency = $settings[4]->settings_value;

    $brandingAccentColor = $settings[5]->settings_value;

    $brandingTextColor = $settings[6]->settings_value;

    ?>
<!-- Flight Settings Html Code -->
<div class="flight-funders--settings">
	<div class="inner--class">
		<h1>Settings</h1>
		<div class="settings-tabs">
			<div class="tab active-settings" id="general">
				<p>General</p>
			</div>
			<div class="tab" id="payment-method">
				<p>Payment Methods</p>
			</div>
			<div class="tab" id="branding">
				<p>Branding</p>
			</div>
		</div>

		<!-- General Html Code -->
		<div class="general-methods" style="display: block !important;">
			<div>
				<form action="" method="POST">
					<label>SMTP Host
						<input type="text" name="smtp_host" value="<?php echo $settings[7]->settings_value;?>">
					</label>
					<label>SMTP Port
						<input type="text" name="smtp_port" value="<?php echo $settings[8]->settings_value;?>">
					</label>
					<label>SMTP Email
						<input type="email" name="smtp_email" value="<?php echo $settings[9]->settings_value;?>">
					</label>
					<label>SMTP Password
						<input type="password" name="smtp_password" value="<?php echo $settings[10]->settings_value;?>">
					</label>
			</div>
			<div>
				<p style="margin: 0; font-weight: 600; font-size: 16px;">Default Text</p>
				<p>Set the defaul text used for certain submission and notification messages.</p>
				<div class="default-text">
						<label>Submit a Project -- Submission Received Message 
							<textarea name="submission_message" cols="30" rows="10"><?php echo $settings[11]->settings_value;?></textarea>
						</label>
						<label>Fund a Project -- Donation Received Message
							<textarea name="donation_message" cols="30" rows="10"><?php echo $settings[12]->settings_value;?></textarea>
						</label>
						</form>
				</div>
			</div>
			<button class="general-settings-update" style="margin-left: auto; display: flex; align-items: center; justify-content: center; margin-top: 10px; height: 40px; width: 150px; border: none; border-radius: 5px; background-color: #4D0071; color: #ffffff; cursor: pointer;">Update Settings</button>
		</div>

		<!-- Payment Html Methods -->
		<div class="payment-methods">
			<p>In order to receive donations via credit card, please activate one of the Payment systems, below.</p>
			<p>
				<span class="note">Note:</span> Only one system can be used at a time.
			</p>
			<div class="paypal-method" style="height: 100%;overflow-y: scroll;padding: 10px;">
				<h2 style="font-weight:bold;">Paypal</h2>
				<input type="checkbox" id="pay" name="pay" value="pay">
  				<label for="payment"> Use Paypal to Receive Payments</label><br>				
  				<p>Select whether your PayPal account is in Sandbox (Testing) or Live mode.</p> 
				<p>Live mode is required to successfully process real-world credit cart payments.</p>
				<div class="paypal-gateway">
					<div>
						<p style="font-weight:bold;">Mode:</p>
						<select name="paypal_sandbox_status">
							<option value="false" <?php if ($paypalSandboxStatus === "false") {
           echo "selected";
       } ?>>Sandbox</option>
							<option value="true" <?php if ($paypalSandboxStatus === "true") {
           echo "selected";
       } ?>>Live</option>
						</select>
					</div>
					<div>
						<p style="font-weight:bold;">Currency:</p>
						<select name="currency"> 
						<option value="CAD" 
							<?php 
								if ($settingsCurrency === "CAD") {
									echo "selected";
								} 
								?>
							> CAD
						</option> 
						<option value="GBP" 
							<?php 
								if ($settingsCurrency === "GBP") {
									echo "selected";
								} 
								?>
							> GBP
						</option> 
						<option value="USD" 
							<?php 
								if ($settingsCurrency === "USD") {
									echo "selected";
								} 
								?>
							> USD
						</option> 
						</select>
					</div>
				</div>
				<p>Enter your Sandbox and Live API Credentials.</p>
				<p> Need help setting these up? Visit <a style="font-weight:bold;" href="https://paypal.com/commercesetup/APICredentials/" target="_blank" style="text-decoration: none;">www.paypal.com/commercesetup/APICredentials</a></p>

				<div class="paypal-credentials">
					<div>
						<p style="font-weight: 600;">Sandbox/Live API Credentials</p>
						<label>Email Address <input type="text" name="paypal_email_address" placeholder="Enter sandbox email address" value="<?php echo $paypalEmail; ?>">
						</label>
					</div>


				</div>


			</div>


			<button class="payment-settings-update" style="margin-left: auto; display: flex; align-items: center; justify-content: center; margin-top: 10px; height: 40px; width: 150px; border: none; border-radius: 5px; background-color: #4d0071;; color: #ffffff; cursor: pointer;">Update Settings</button>


		</div>
		<!-- Branding Html Code -->
		<div class="branding-methods">
			<p style="font-weight: 600; font-size: 16px">Colour Palette</p>
			<p>Customize the colours of various visual aspects of the plugin, to match your overall site theme.</p>
			<div class="branding-colors-box">
				<label style="font-weight: 500">Accent Color <input type="color" name="button_background" value="<?php echo $brandingAccentColor; ?>">
				</label>
				<label style="font-weight: 500">Button Text Color <input type="color" name="button_text" value="<?php echo $brandingTextColor; ?>">
				</label>
			</div>


			<button class="branding-settings-update" style="margin: auto; display: flex; align-items: center; justify-content: center; margin-top: 10px; height: 40px; width: 150px; border: none; border-radius: 5px; background-color: #4D0071; color: #ffffff; cursor: pointer;">Update Settings</button>
			<p id="message"></p>		</div>
	</div>
</div>
<script>
	$(document).ready(function() {
		$("#general").on("click", function() {
			$(".settings-tabs div.tab").removeClass("active-settings");
			$(this).addClass("active-settings");
			$(".general-methods").show();
			$(".payment-methods").hide();
			$(".branding-methods").hide();
		})
		$("#payment-method").on("click", function() {
			$(".settings-tabs div.tab").removeClass("active-settings");
			$(this).addClass("active-settings");
			$(".general-methods").hide();
			$(".payment-methods").show();
			$(".branding-methods").hide();
		})
		$("#branding").on("click", function() {
			$(".settings-tabs div.tab").removeClass("active-settings");
			$(this).addClass("active-settings");
			$(".general-methods").hide();
			$(".payment-methods").hide();
			$(".branding-methods").show();
		})

	$(".general-settings-update").on("click", function() {
			var paymentSettingsUrl = window.location.origin + "/wp-content/plugins/project-funders/admin/api/project-funders-settings.php";
			var payload = "smtp_host=" + $("input[name='smtp_host']").val() + "&smtp_port=" + $("input[name='smtp_port']").val() + "&smtp_email=" + $("input[name='smtp_email']").val() + "&smtp_password=" + $("input[name='smtp_password']").val() + "&submission_message=" + $("textarea[name='submission_message']").val() + "&donation_message=" + $("textarea[name='donation_message']").val();
			$.ajax({
				type: "POST",
				url: paymentSettingsUrl,
				data: payload,
				success: function(response) {
					alert("Settings updated");
					window.location.href = window.location.href;
        		}
			});
		});
		
    $(".payment-settings-update").on("click", function() {
			var paymentSettingsUrl = window.location.origin + "/wp-content/plugins/project-funders/admin/api/project-funders-settings.php";
			var payload = "paypal_sandbox_status=" + $("select[name='paypal_sandbox_status']").val() + "&currency=" + $("select[name='currency']").val() + "&paypal_email_address=" + $("input[name='paypal_email_address']").val();
			$.ajax({
				type: "POST",
				url: paymentSettingsUrl,
				data: payload,
				success: function(response) {
          			alert("Settings updated");
          			window.location.href = window.location.href;
        		}
			});
		});

		$(".branding-settings-update").on("click", function() {
			var paymentSettingsUrl = window.location.origin + "/wp-content/plugins/project-funders/admin/api/project-funders-settings.php";
			var payload = "branding_accent_color=" + $("input[name='button_background']").val() + "&branding_text_color=" + $("input[name='button_text']").val();
			$.ajax({
				type: "POST",
				url: paymentSettingsUrl,
				data: payload,
				success: function(response) {
					alert("Settings updated");
					window.location.href = window.location.href;
        		}
			});
		});

	})
</script>
<style>
	@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@100;200;300;400;500;600;700;800;900&display=swap');
	#wpbody {
		position: relative;
		margin-left: -20px;
		background-color: #ffffff;
	}

	.flight-funders--settings {
		height: 650px;
		width: 100%;
		margin: 0;
		padding: 0;
		display: flex;
		flex-direction: column;
		font-family: Poppins;
	}

	.inner--class {
		height: 70%;
		width: 100%;
		margin: auto;
	}

	.inner--class h1,
	h2,
	h3,
	h4,
	h5,
	h6 {
		text-align: center;
	}

	.settings-tabs {
		width: 50%;
		display: flex;
		justify-content: center;
		gap: 30px;
		margin: auto;
		border-bottom: solid 1px #000000;
	}

	.settings-tabs p {
		margin: 0;
		cursor: pointer;
	}

	.settings-tabs div.tab {
		background-color: #00000030;
		padding: 15px;
		border-radius: 6px;
		margin-bottom: 10px;
		cursor: pointer;
	}

	.settings-tabs div.tab {
		background-color: #0000000a;
		padding: 15px;
		border-radius: 6px;
		margin-bottom: 10px;
		cursor: pointer;
	}

	.settings-tabs div.tab.active-settings {
		background-color: #00000040;
		padding: 5px 15px;
		border-radius: 6px;
		display: flex;
		justify-content: center;
		align-items: center;
	}

	.settings-tabs div.tab p {
		color: #333333;
		font-weight: 600;
	}

	.general-methods {
		height: 100%;
		overflow-y: scroll;
		padding: 10px;
	}

	.general-methods::-webkit-scrollbar {
		width: 5px;
	}

	.general-methods::-webkit-scrollbar-thumb {
		height: 5px;
		background-color: #4D0071;
		border-radius: 10px;
	}

	.default-text {
		border: none !important;
		text-align: left;
	}

	.default-text {
		display: grid !important;
		grid-template-columns: repeat(2,1FR) !important;
		gap: 20px;
	}

	.default-text label {
		font-size: 14px;
		font-weight: 500;
		color: #333333;
	}

	.default-text label textarea {
		font-size: 12px;
		font-weight: 400;
		width: 100%;
	}

	.general-methods,
	.payment-methods,
	.branding-methods {
		width: 60%;
		margin: auto;
		text-align: center;
		display: none;
	}

	.payment-methods span.note {
		font-weight: 600;
	}

	.paypal-method {
		padding: 25px;
		border: solid 1px #000;
		border-radius: 10px;
	}

	.paypal-method p:first-child {
		margin: 0 auto !important;
	}

	.paypal-method p {
		width: 80%;
		margin: 20px auto;
	}

	.paypal-gateway {
		display: flex;
		justify-content: center;
		gap: 100px;
	}

	.paypal-gateway div {
		display: flex;
		align-items: center;
		gap: 5px;
	}

	.paypal-gateway div select {
		border: none;
		background-color: #00000010;
	}

	.paypal-credentials {
		display: flex;
		justify-content: center;
		gap: 50px;
	}

	.branding-methods .branding-colors-box {
		display: flex;
		justify-content: center;
		gap: 40px;
	}


	.branding-methods .branding-colors-box label {
		width: 180px;
	}


	.branding-methods .branding-colors-box input[type='color'] {
		width: 100%;
		height: 50px;
		border: none;
		border-radius: 6px;
	}


	.branding-methods .branding-colors-box input[type='color']::-webkit-color-swatch {
		border-radius: 5px;
	}

	.general-methods div {
		padding: 25px;
		border: solid 1px #000;
		border-radius: 10px;
		margin-top: 20px;
	}

	.general-methods div form {
		display: flex;
		flex-direction: column;
		gap: 10px;
	}

	.general-methods div form label {
		display: flex;
		flex-direction: column;
		gap: 5px;
		text-align: left;
	}
</style> <?php
} ?>
