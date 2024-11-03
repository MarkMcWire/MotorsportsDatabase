<?php
include("verbindung.php");
	
if(isset($_POST["Import"])) {
	$championship_name = $_POST["Import"];
	$countfiles = count($_FILES["file"]["tmp_name"]);
	
	for ($i = 0; $i < $countfiles; $i++) {
		if ($_FILES["file"]["tmp_name"][$i]) {
			$filename = $_FILES["file"]["tmp_name"][$i];
			if ($_FILES["file"]["size"][$i] > 0) {
				$file = fopen($filename, "r");
				while (($getData = fgetcsv($file, 10000, ",")) !== FALSE) {
					if ($getData[0] == 'RaceID' || $getData[1] == 'RaceID') {
						$index_raceid = 0;
						$index_stageid = 0;
						$index_finish = 0;
						$index_start = 0;
						$index_qualification = 0;
						$index_fastest_lap = 0;
						$index_laps = 0;
						$index_led_laps = 0;
						$index_status = 0;
						$index_interval = 0;
						$index_money = 0;
						$index_points = 0;
						$index_car = 0;
						$index_driver = 0;
						$index_team = 0;
						$index_vehicle = 0;
						
						foreach ($getData as $key=>$val) { 
							if ($index_raceid == 0 && (str_contains(strtolower($val), "race") || str_contains(strtolower($val), "raceid"))) {
								$index_raceid = $key;
								echo "RaceID: ".$index_raceid."\n";
							}
							if ($index_stageid == 0 && (str_contains(strtolower($val), "stage") || str_contains(strtolower($val), "segment") || str_contains(strtolower($val), "stageid"))) {
								$index_stageid = $key;
								echo "StageID: ".$index_stageid."\n";
							}
							if ($index_finish == 0 && (str_contains(strtolower($val), "pos") || str_contains(strtolower($val), "fin"))) {
								$index_finish = $key;
								echo "Finish: ".$index_finish."\n";
							}
							if ($index_laps == 0 && (str_contains(strtolower($val), "laps"))) {
								$index_laps = $key;
								echo "Laps: ".$index_laps."\n";
							}
							if ($index_car == 0 && (str_contains(strtolower($val), "#") || str_contains(strtolower($val), "car") || str_contains(strtolower($val), "no") || str_contains(strtolower($val), "number"))) {
								$index_car = $key;
								echo "Number: ".$index_car."\n";
							}
							if ($index_driver == 0 && (str_contains(strtolower($val), "driver"))) {
								$index_driver = $key;
								echo "Driver: ".$index_driver."\n";
							}
							if ($index_team == 0 && (str_contains(strtolower($val), "team") || str_contains(strtolower($val), "owner"))) {
								$index_team = $key;
								echo "Team: ".$index_team."\n";
							}
							if ($index_vehicle == 0 && (str_contains(strtolower($val), "make") || str_contains(strtolower($val), "vehicle") || str_contains(strtolower($val), "manufact") || str_contains(strtolower($val), "chassis") || str_contains(strtolower($val), "engine"))) {
								$index_vehicle = $key;
								echo "Car: ".$index_vehicle."\n";
							}
						}
						print('<br />');
					} else {
						if (strlen($getData[$index_raceid]) > 1) { $raceID = trim($getData[$index_raceid]);}
						if (strlen($getData[$index_stageid]) > 0) { $stageID = trim($getData[$index_stageid]);} else {$stageID = 1;}
						
						if (is_numeric($getData[$index_finish])) {$finish_pos = (int)$getData[$index_finish];} else {$finish_pos = $finish_pos + 1;}
						if (is_numeric($getData[$index_laps]) && $index_laps > 0) {$laps = (int)$getData[$index_laps];} else {$laps = 0;}
						$car_number = $getData[$index_car];
						
						if ($laps > 0) {
							$max_id_driver = 1;
							$query_driver = "SELECT DriverID FROM race_results WHERE (RaceID = ".$raceID.") AND (Car = '".$car_number."')";
							
							$recordset = $database_connection->query($query_driver);
							if ($result_driver = $recordset->fetch_assoc()) {
								$driverID = $result_driver['DriverID'];
							} else {
								$driver_name = trim(str_replace(chr(194).chr(160), "", $getData[$index_driver]));
								$driver_name = trim(str_replace("'", "", $driver_name));
								$driver_name = trim(str_replace(".", "", $driver_name));
								$driver_name = trim(str_replace(",", "", $driver_name));
								$query_driver = "SELECT ID, Display_Name FROM drivers WHERE Comparison_Name LIKE '%".$driver_name."%'";
								$recordset = $database_connection->query($query_driver);
								$driverID = $recordset->fetch_assoc()['ID'];
							}
							
							$result_id = 0;
							$result_id_result = $database_connection->query("SELECT RaceID, Position FROM stage_results WHERE (RaceID = ".$raceID.") AND (StageID = ".$stageID.") AND (Position = ".$finish_pos.")"); 
							if (!($result_id_result->fetch_assoc())) { 
								$sql_insert_stage_result = "INSERT into stage_results (RaceID, StageID, Laps, DriverID, Position) values ('".$raceID."','".$stageID."','".$laps."','".$driverID."','".$finish_pos."')";
								$result = mysqli_query($database_connection, $sql_insert_stage_result);
							}
						}
					}
				}
				fclose($file);
			}
		}
	}
	$sql_update_stage_scoring = "UPDATE championship SET championship.Stage_Scoring = 11 WHERE RaceID IN (SELECT RaceID FROM stage_results);";
	$result = mysqli_query($database_connection, $sql_update_stage_scoring);
	
	echo "<script type=\"text/javascript\">
			alert(\"CSV files has been successfully imported.\");
			window.location = '.'
		 </script>";
}
?>
