<?php
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		echo "Unsupported method!";
		exit();
	} else {
		if(!isset($_GET["api"])) {
			echo "401 Unauthorized";
			exit();
		} else {
			if($_GET["api"] != "TEST_API_KEY") {
				echo "401 Unauthorized";
				exit();
			} else {
				
				// data parsing
				
				$lat_value = $_POST["lat"];
				$long_value = $_POST["long"];
				$carbon_ppm = $_POST["carbon"];
				
				$lat_regex = "/^\-?[0-9]{1,2}\.[0-9]{1,}$/";
				$long_regex = "/^\-?[0-9]{1,3}\.[0-9]{1,}$/";
				
				if( ( preg_match($lat_regex, $lat_value) && preg_match($long_regex, $long_value) ) && 
				( ( floatval($lat_value) >= -90 && floatval($lat_value) <= 90) && ( floatval($long_value) >= -180 && floatval($long_value) <= 180) ) ) {
					
					// actual meat and potatoes
					include "db.php";
					
					$carbon_db = mysqli_select_db($conn, "carbongatchi"); 
					$sql = "INSERT INTO carbondioxide (lat, long, level) VALUES (" . floatval($lat_value) . ", " . floatval($long_value) . ", " . intval($carbon_ppm) . ")";
					if ($conn->query($sql) === TRUE) {
						echo "200 Submitted Successfully";
						exit();
					} else {
						echo "500 ISE<br>" . $conn->error;
						exit();
					}
				} else {
					echo "405 Invalid Data";
					exit();
				}
			}
		}
	}
?>
