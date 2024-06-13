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
							if ($index_raceid == 0 && (str_contains(strtolower($val), "raceid") || str_contains(strtolower($val), "RaceID"))) {
								$index_raceid = $key;
								echo "RaceID: ".$index_raceid."\n";
							}
							if ($index_finish == 0 && (str_contains(strtolower($val), "pos") || str_contains(strtolower($val), "fin") || str_contains(strtolower($val), "rank"))) {
								$index_finish = $key;
								echo "Finish: ".$index_finish."\n";
							}
							if ($index_start == 0 && (str_contains(strtolower($val), "start") || str_contains(strtolower($val), "st"))) {
								$index_start = $key;
								echo "Start: ".$index_start."\n";
							}
							if ($index_qualification == 0 && (str_contains(strtolower($val), "qual") || str_contains(strtolower($val), "grid"))) {
								$index_qualification = $key;
								echo "Qual: ".$index_qualification."\n";
							}
							if ($index_fastest_lap == 0 && (str_contains(strtolower($val), "frl") || str_contains(strtolower($val), "fastest") || str_contains(strtolower($val), "race lap"))) {
								$index_fastest_lap = $key;
								echo "Laps: ".$index_fastest_lap."\n";
							}
							if ($index_laps == 0 && (str_contains(strtolower($val), "laps"))) {
								$index_laps = $key;
								echo "Laps: ".$index_laps."\n";
							}
							if ($index_led_laps == 0 && (str_contains(strtolower($val), "led"))) {
								$index_led_laps = $key;
								echo "Led Laps: ".$index_led_laps."\n";
							}
							if ($index_status == 0 && (str_contains(strtolower($val), "status"))) {
								$index_status = $key;
								echo "Status: ".$index_status."\n";
							}
							if ($index_interval == 0 && (str_contains(strtolower($val), "interval"))) {
								$index_interval = $key;
								echo "Interval: ".$index_interval."\n";
							}
							if ($index_money == 0 && (str_contains(strtolower($val), "money"))) {
								$index_money = $key;
								echo "Money: ".$index_money."\n";
							}
							if ($index_points == 0 && (str_contains(strtolower($val), "point") || str_contains(strtolower($val), "pts"))) {
								$index_points = $key;
								echo "Points: ".$index_points."\n";
							}
							if ($index_car == 0 && (str_contains(strtolower($val), "#") || str_contains(strtolower($val), "car") || str_contains(strtolower($val), "number"))) {
								$index_car = $key;
								echo "Number: ".$index_car."\n";
							}
							if ($index_driver == 0 && (str_contains(strtolower($val), "driver") || str_contains(strtolower($val), "number"))) {
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
						
						if (is_numeric($getData[$index_finish])) {$finish_pos = (int)$getData[$index_finish];} else {$finish_pos = $finish_pos + 1;}
						if (is_numeric($getData[$index_start])) {$start_pos = (int)$getData[$index_start];} else {$start_pos = 0;}
						if (is_numeric($getData[$index_fastest_lap]) && $index_fastest_lap > 0) {$frl = (int)$getData[$index_fastest_lap];} else {$frl = 0;}
						if (is_numeric($getData[$index_laps]) && $index_laps > 0) {$laps = (int)$getData[$index_laps];} else {$laps = 0;}
						if (is_numeric($getData[$index_led_laps]) && $index_led_laps > 0) {$led = (int)$getData[$index_led_laps];} else {$led = 0;}
						$status = $getData[$index_status];
						$car_number = $getData[$index_car];
						
						$driver_name = trim(str_replace(chr(194).chr(160), "", $getData[$index_driver]));
						$driver_name = trim(str_replace("'", "", $driver_name));
						$driver_name = trim(str_replace(".", "", $driver_name));
						$driver_name = trim(str_replace(",", "", $driver_name));
						
						$max_id_driver = 1;
						$query_driver = "SELECT ID, Name FROM drivers WHERE Suchname LIKE '%".$driver_name."%'";
						
						$recordset = $database_connection->query($query_driver);
						if ($result_driver = $recordset->fetch_assoc()) {
							$driver_name = $result_driver['Name'];
							$driverID = $result_driver['ID'];
						} else {
							$max_id_driver = 1 + $database_connection->query("SELECT MAX(ID) AS MaxID FROM drivers")->fetch_assoc()['MaxID'];
							$sql_insert_driver = "INSERT into drivers (ID, Suchname, Name, Kategorie)
							values ('".$max_id_driver."','".$driver_name."','".$driver_name."','0')";
							print($sql_insert_driver."<br />");
							$result_insert_driver = mysqli_query($database_connection, $sql_insert_driver);
							
							$query_driver = "SELECT ID, Name FROM drivers WHERE Name = '".$driver_name."'";
							$recordset = $database_connection->query($query_driver);
							$driverID = $recordset->fetch_assoc()['ID'];
						}
						
						$result_id = 0;
						if (strlen($status) > 1) {$dnf = 1-(int)str_contains($status, 'running');} else {$status = 'dnf'; $dnf = '1';}
						$result_id_result = $database_connection->query("SELECT RaceID, Finish FROM race_results WHERE (RaceID = ".$raceID.") AND (Finish = ".$finish_pos.")"); 
						if (!($result_id_result->fetch_assoc())) { 
							$sql_insert_race_result = "INSERT into race_results (RaceID, Car, DriverID, Finish, Start, Laps, Led, FastestRaceLap, Status, DNF)
							values ('".$raceID."','".$car_number."','".$driverID."','".$finish_pos."','".$start_pos."','".$laps."','".$led."','".$frl."','".$status."','".$dnf."')";
							//print($sql_insert_race_result);print"<br/>";
							$result = mysqli_query($database_connection, $sql_insert_race_result);
						}
					}
				}
				fclose($file);
			}
		}
	}
	$sql_update_race_result_dnf1 = "UPDATE race_results SET STATUS = 'dnf' WHERE Status = '–';";
	$result = mysqli_query($database_connection, $sql_update_race_result_dnf1);
	$sql_update_race_result_dnf2 = "UPDATE race_results SET STATUS = 'Running' WHERE (Status like 'running') or (Status like '%Flagged%') or (Status like '%:%') or (Status like '%.%') or (Status like '%+%') or (Status REGEXP '^[[:digit:]]+$');";
	$result = mysqli_query($database_connection, $sql_update_race_result_dnf2);
	$sql_update_race_result_dnf3 = "UPDATE race_results SET DNF = 1 WHERE Status <> 'Running';";
	$result = mysqli_query($database_connection, $sql_update_race_result_dnf3);
	$sql_update_race_result_dnf4 = "UPDATE race_results SET DNF = 0 WHERE Status = 'Running';";
	$result = mysqli_query($database_connection, $sql_update_race_result_dnf4);
	$sql_update_race_result_llf1 = "UPDATE race_results INNER JOIN (SELECT RaceID, MAX(Laps) AS LLF FROM race_results GROUP BY RaceID) AS LLFtemp ON race_results.RaceID = LLFtemp.RaceID SET race_results.LedLapFinish = 1 WHERE (race_results.RaceID = LLFtemp.RaceID) AND (race_results.Laps = LLFtemp.LLF) AND (race_results.Laps > 0);";
	$result = mysqli_query($database_connection, $sql_update_race_result_llf1);
	$sql_update_race_result_llf2 = "UPDATE race_results INNER JOIN (SELECT RaceID, MAX(Laps) AS LLF FROM race_results GROUP BY RaceID) AS LLFtemp ON race_results.RaceID = LLFtemp.RaceID SET race_results.LedLapFinish = 0 WHERE (race_results.RaceID = LLFtemp.RaceID) AND (race_results.Laps <> LLFtemp.LLF) OR (race_results.Laps = 0);";
	$result = mysqli_query($database_connection, $sql_update_race_result_llf2);
	$sql_update_race_result_mll1 = "UPDATE race_results INNER JOIN (SELECT RaceID, MAX(Led) AS MLL FROM race_results GROUP BY RaceID) AS MLLtemp ON race_results.RaceID = MLLtemp.RaceID SET race_results.MostLapsLed = 1 WHERE (race_results.RaceID = MLLtemp.RaceID) AND (race_results.Led = MLLtemp.MLL) AND (race_results.Led > 0);";
	$result = mysqli_query($database_connection, $sql_update_race_result_mll1);
	$sql_update_race_result_mll2 = "UPDATE race_results INNER JOIN (SELECT RaceID, MAX(Led) AS MLL FROM race_results GROUP BY RaceID) AS MLLtemp ON race_results.RaceID = MLLtemp.RaceID SET race_results.MostLapsLed = 0 WHERE (race_results.RaceID = MLLtemp.RaceID) AND (race_results.Led <> MLLtemp.MLL) OR (race_results.Led = 0);";
	$result = mysqli_query($database_connection, $sql_update_race_result_mll2);
	$sql_update_race_result_mpg1 = "UPDATE race_results INNER JOIN (SELECT RaceID, MAX(Start-Finish) AS MPG FROM race_results GROUP BY RaceID) AS MPGtemp ON race_results.RaceID = MPGtemp.RaceID SET race_results.MostPositionsGained = 1 WHERE (race_results.RaceID = MPGtemp.RaceID) AND ((race_results.Start-race_results.Finish) = MPGtemp.MPG);";
	$result = mysqli_query($database_connection, $sql_update_race_result_mpg1);
	$sql_update_race_result_mpg2 = "UPDATE race_results INNER JOIN (SELECT RaceID, MAX(Start-Finish) AS MPG FROM race_results GROUP BY RaceID) AS MPGtemp ON race_results.RaceID = MPGtemp.RaceID SET race_results.MostPositionsGained = 0 WHERE (race_results.RaceID = MPGtemp.RaceID) AND ((race_results.Start-race_results.Finish) <> MPGtemp.MPG);";
	$result = mysqli_query($database_connection, $sql_update_race_result_mpg2);
	$sql_race_laps = "UPDATE races LEFT JOIN (SELECT RaceID, MAX(Laps) AS MaxLaps FROM race_results GROUP BY RaceID) AS TempTable ON TempTable.RaceID = races.ID SET races.Runden = TempTable.MaxLaps WHERE TempTable.MaxLaps > races.Runden;";
	$result = mysqli_query($database_connection, $sql_race_laps);
	
	$sql_update_qualy_scoring = "UPDATE championship SET championship.Race_Scoring = 1 WHERE RaceID IN (SELECT RaceID FROM race_results);";
	$result = mysqli_query($database_connection, $sql_update_qualy_scoring);
	
	echo "<script type=\"text/javascript\">
			alert(\"CSV files has been successfully imported.\");
			window.location = '.'
		 </script>";
}
?>
