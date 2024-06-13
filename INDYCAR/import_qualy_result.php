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
						$index_qualid = 0;
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
							if ($index_qualid == 0 && (str_contains(strtolower($val), "session") || str_contains(strtolower($val), "segment"))) {
								$index_qualid = $key;
								echo "RaceID: ".$index_qualid."\n";
							}
							if ($index_fastest_lap == 0 && (str_contains(strtolower($val), "interval") || str_contains(strtolower($val), "result") || str_contains(strtolower($val), "time"))) {
								$index_fastest_lap = $key;
								echo "Laps: ".$index_fastest_lap."\n";
							}
							if ($index_laps == 0 && (str_contains(strtolower($val), "laps"))) {
								$index_laps = $key;
								echo "Laps: ".$index_laps."\n";
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
						if (strlen($getData[$index_qualid]) > 0) { $qualID = trim($getData[$index_qualid]);} else {$qualID = 1;}
						
						if (is_numeric($getData[$index_finish])) {$finish_pos = (int)$getData[$index_finish];} else {$finish_pos = $finish_pos + 1;}
						if (is_numeric($getData[$index_fastest_lap]) && $index_fastest_lap > 0) {$time = (int)$getData[$index_fastest_lap];} else {$time = 0;}
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
						$result_id_result = $database_connection->query("SELECT RaceID, Finish FROM qualification_results WHERE (RaceID = ".$raceID.") AND (Session = ".$qualID.") AND (DriverID = ".$driverID.")"); 
						if (!($result_id_result->fetch_assoc())) { 
							$sql_insert_qual_result = "INSERT into qualification_results (RaceID, Session, DriverID, Time)
							values ('".$raceID."','".$qualID."','".$driverID."','".$time."')";
							//print($sql_insert_qual_result);print"<br/>";
							$result = mysqli_query($database_connection, $sql_insert_qual_result);
						}
					}
				}
				fclose($file);
			}
		}
	}
	$sql_update_qualy_scoring = "UPDATE championship SET championship.Qualification_Scoring = 1 WHERE RaceID IN (SELECT RaceID FROM qualification_results);";
	$result = mysqli_query($database_connection, $sql_update_qualy_scoring);
	
	echo "<script type=\"text/javascript\">
			alert(\"CSV files has been successfully imported.\");
			window.location = '.'
		 </script>";
}
?>
