<?php

	if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
		echo "Unsupported method!";
		exit();
	} else {
		if(isset($_GET["toplong"]) &&
			isset($_GET["toplat"]) &&
			isset($_GET["lowlong"]) &&
			isset($_GET["lowlat"])
		){
			$lat_regex = "/^\-?[0-9]{1,2}\.?[0-9]{1,}?$/";
			$long_regex = "/^\-?[0-9]{1,3}\.?[0-9]{1,}?$/";

			$topLongitude = $_GET["toplong"];
			$topLatitude  = $_GET["toplat"];
			$lowLongitude = $_GET["lowlong"];
			$lowLatitude  = $_GET["lowlat"];

			if (preg_match($lat_regex, $topLatitude) && preg_match($lat_regex, $lowLatitude)
			&& preg_match($long_regex, $topLongitude) && preg_match($long_regex, $lowLongitude) ) {
				include "db.php";

				$carbon_db = mysqli_select_db($conn, "carbongatchi");
				$sql = "SELECT * FROM carbondioxide WHERE lat BETWEEN " . floatval($lowLatitude) . " AND " . floatval($topLatitude) .
				" AND longitude BETWEEN " . floatval($lowLongitude) . " AND " . floatval($topLongitude);
				$result = $conn->query($sql);
				echo "{";
					if ($result->num_rows > 0) {
						echo "\"max\": " . $result->num_rows . ", \"data\": [";
						$json_strs = "";
						while($row = $result->fetch_assoc()) {
								$json_strs = 	$json_strs . "{ \"lat\": " . $row["lat"] . ", \"lng\": " . $row["longitude"] . ", \"carbon\": " . $row["carbon"] . " },";
							}
							echo chop($json_strs, ",");
							echo "]";
						}
						echo " }";
					}
				} else {
					echo "Bad data";
					exit();
				}

	}

?>
