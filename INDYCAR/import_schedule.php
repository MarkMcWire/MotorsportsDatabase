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
					if ($getData[0] == 'Meisterschaft' || $getData[0] == 'Championship' || $getData[0] == 'ChampionshipID' || $getData[0] == 'ChampID') {
						$index_champid = 0;
						$index_championship = 0;
						$index_raceid = 0;
						$index_qualid = 0;
						$index_date = 0;
						$index_racename = 0;
						$index_laps = 0;
						$index_tracklength = 0;
						$index_trackname = 0;
						$index_trackid = 0;
						$index_tracktype = 0;
						$index_typeid = 0;
						
						foreach ($getData as $key=>$val) { 
							if ($index_championship == 0 && (str_contains(strtolower($val), "meisterschaft") || str_contains(strtolower($val), "championship"))) {
								$index_championship = $key;
								echo "Championship: ".$index_championship."\n";
							}
							if ($index_champid == 0 && (str_contains(strtolower($val), "championshipid") || str_contains(strtolower($val), "champid"))) {
								$index_champid = $key;
								echo "ChampID: ".$index_champid."\n";
							}
							if ($index_date == 0 && (str_contains(strtolower($val), "datum") || str_contains(strtolower($val), "date"))) {
								$index_date = $key;
								echo "Date: ".$index_date."\n";
							}
							if ($index_racename == 0 && (str_contains(strtolower($val), "rennen") || str_contains(strtolower($val), "veranstaltung") || str_contains(strtolower($val), "event"))) {
								$index_racename = $key;
								echo "Name: ".$index_racename."\n";
							}
							if ($index_raceid == 0 && (str_contains(strtolower($val), "rennid") || str_contains(strtolower($val), "raceid"))) {
								$index_raceid = $key;
								echo "RaceID: ".$index_raceid."\n";
							}
							if ($index_laps == 0 && (str_contains(strtolower($val), "runden") || str_contains(strtolower($val), "laps") || str_contains(strtolower($val), "racelaps"))) {
								$index_laps = $key;
								echo "Laps: ".$index_laps."\n";
							}
							if ($index_tracklength == 0 && (str_contains(strtolower($val), "länge") || str_contains(strtolower($val), "length") || str_contains(strtolower($val), "distance"))) {
								$index_tracklength = $key;
								echo "Length: ".$index_tracklength."\n";
							}
							if ($index_trackname == 0 && (str_contains(strtolower($val), "rennstrecke") ||str_contains(strtolower($val), "strecke") || str_contains(strtolower($val), "racetrack") || str_contains(strtolower($val), "track"))) {
								$index_trackname = $key;
								echo "Track: ".$index_trackname."\n";
							}
							if ($index_trackid == 0 && (str_contains(strtolower($val), "trackid"))) {
								$index_trackid = $key;
								echo "TrackID: ".$index_trackid."\n";
							}
							if ($index_tracktype == 0 && (str_contains(strtolower($val), "streckentyp") || str_contains(strtolower($val), "type"))) {
								$index_tracktype = $key;
								echo "Type: ".$index_tracktype."\n";
							}
							if ($index_typeid == 0 && (str_contains(strtolower($val), "typeid"))) {
								$index_typeid = $key;
								echo "TypeID: ".$index_typeid."\n";
							}
						}
						print('<br />');
					} else {
						if (strlen($getData[$index_date]) > 0) {
							$date_string = $getData[$index_date]; 
							if (strlen($date_string) == 6) {$date = date_create_from_format('ymd', $date_string);}
							if (strlen($date_string) == 8) {$date = date_create_from_format('Ymd', $date_string);}
							if (strlen($date_string) > 8 && str_contains($date_string, ".")) {$date = date_create_from_format('d.m.Y', $date_string);}
							if (strlen($date_string) > 6 && str_contains($date_string, ".")) {$date = date_create_from_format('d.m.y', $date_string);}
							if (strlen($date_string) > 8 && str_contains($date_string, "/")) {$date = date_create_from_format('m/d/Y', $date_string);}
							if (strlen($date_string) > 6 && str_contains($date_string, "/")) {$date = date_create_from_format('m/d/y', $date_string);}
							if (strlen($date_string) > 8 && str_contains($date_string, "-")) {$date = date_create_from_format('Y-m-d', $date_string);}
							if (strlen($date_string) > 6 && str_contains($date_string, "-")) {$date = date_create_from_format('y-m-d', $date_string);}
							if (strlen($date_string) > 8 && str_contains($date_string, "–")) {$date = date_create_from_format('Y–m–d', $date_string);}
							if (strlen($date_string) > 6 && str_contains($date_string, "–")) {$date = date_create_from_format('y–m–d', $date_string);}
						} else {
							$date_string = ''; 
							$date = date('Y-m-d');
						}
						$date_string = $date->format('Ymd');
						$season_year = $date->format('Y');
						
						if (strlen($getData[$index_champid]) > 0) {$champID = trim($getData[$index_champid]);} else {$champID = 0;}
						if (strlen($getData[$index_raceid]) > 1) {$raceID = trim($getData[$index_raceid]);} else {$raceID = date_format($date, 'Ymd').$champID;}
						
						if (strlen($getData[$index_laps]) > 0) {$race_laps = trim($getData[$index_laps]);} else {$race_laps = 0;}
						if (strlen($getData[$index_tracklength]) > 0) {$track_length = trim(str_replace(',', '.', $getData[$index_tracklength]));} else {$track_length = 0;}
						if (strlen($getData[$index_typeid]) > 0) {$typeID = trim($getData[$index_typeid]);} else {$typeID = 0;}
						
						if (strlen($getData[$index_trackid]) > 1) {$trackID = trim($getData[$index_trackid]);} else {$trackID = 0;}
						if (strlen($getData[$index_trackname]) > 1) {$track_name = trim($getData[$index_trackname]);} else {$track_name = '';}
						if (strlen($track_name) < 4) {
							$query_track = "SELECT ID FROM tracks WHERE (Kennzeichen = '".$track_name."') AND (".$season_year." >= tracks.Eroeffnung) AND ((".$season_year." <= tracks.Schliessung) OR (tracks.Schliessung IS NULL))";
						} else {
							$query_track = "SELECT ID FROM tracks WHERE (Bezeichnung LIKE '%".$track_name."%') AND (".$season_year." >= tracks.Eroeffnung) AND ((".$season_year." <= tracks.Schliessung) OR (tracks.Schliessung IS NULL))";
						}
						$recordset = $database_connection->query($query_track);
						if ($result_track = $recordset->fetch_assoc()) {
							$trackID = $result_track['ID'];
						} else {
							$trackID = 0;
						}
						
						if (strlen($getData[$index_racename]) > 0) {
							$race_name = trim(str_replace(chr(194).chr(160), "", $getData[$index_racename]));
							$race_name = trim(str_replace(chr(9), chr(92).chr(9), $race_name));
							$race_name = trim(str_replace(chr(34), chr(92).chr(34), $race_name));
							$race_name = trim(str_replace(chr(37), chr(92).chr(37), $race_name));
							$race_name = trim(str_replace(chr(39), chr(92).chr(39), $race_name));
							$race_name = trim(str_replace(chr(42), chr(92).chr(42), $race_name));
							$race_name = trim(str_replace(chr(44), chr(92).chr(44), $race_name));
							$race_name = trim(str_replace(chr(46), chr(92).chr(46), $race_name));
							$race_name = trim(str_replace(chr(95), chr(92).chr(95), $race_name));
							$race_name = trim(str_replace(chr(96), chr(92).chr(96), $race_name));
							$race_name = trim(str_replace(chr(180), chr(92).chr(180), $race_name));
						} else {
							$race_name = "";
						}
						
						
						$result_id_result0 = 0;
						$result_id_result1 = 0;
						$result_id_result0 = $database_connection->query("SELECT ID FROM races WHERE (ID = ".$raceID.")"); 
						$result_id_result1 = $database_connection->query("SELECT ID FROM races WHERE (ID = ".($raceID + 1).")"); 
						if (!($result_id_result0->fetch_assoc())) { 
							$sql_insert_schedule = "INSERT into races (ID, Datum, Event, Runden, Length, TrackID, TypeID)
							values ('".$raceID."','".$date_string."','".$race_name."','".$race_laps."','".$track_length."','".$trackID."','".$typeID."')";
							$result = mysqli_query($database_connection, $sql_insert_schedule);
						} else {
							if (!($result_id_result1->fetch_assoc())) { 
								$sql_insert_schedule = "INSERT into races (ID, Datum, Event, Runden, Length, TrackID, TypeID)
								values ('".($raceID + 1)."','".$date_string."','".$race_name."','".$race_laps."','".$track_length."','".$trackID."','".$typeID."')";
								$result = mysqli_query($database_connection, $sql_insert_schedule);
							} else {
								$sql_insert_schedule = "INSERT into races (ID, Datum, Event, Runden, Length, TrackID, TypeID)
								values ('".($raceID + 2)."','".$date_string."','".$race_name."','".$race_laps."','".$track_length."','".$trackID."','".$typeID."')";
								$result = mysqli_query($database_connection, $sql_insert_schedule);
							}
						}
					}
				}
				fclose($file);
			}
		}
	}
	
	$sql_update_race_champ1 = "INSERT INTO championship (Bezeichnung, Saison, RaceID, Kategorie) SELECT 'Formula One World Championship', YEAR(races.Datum), races.ID, 1 FROM races LEFT JOIN championship ON championship.RaceID = races.ID WHERE (championship.ID IS NULL) AND (races.ID MOD 10 IN (1,2));";
	$result = mysqli_query($database_connection, $sql_update_race_champ1);
	$sql_update_race_champ2 = "INSERT INTO championship (Bezeichnung, Saison, RaceID, Kategorie) SELECT 'IndyCar Series', YEAR(races.Datum), races.ID, 1 FROM races LEFT JOIN championship ON championship.RaceID = races.ID WHERE (championship.ID IS NULL) AND (races.ID MOD 10 IN (3,4,5));";
	$result = mysqli_query($database_connection, $sql_update_race_champ2);
	$sql_race_name = "UPDATE championship LEFT JOIN races ON races.ID = championship.RaceID SET races.Event = Concat(championship.Saison, '–') WHERE (races.Event = '') OR (races.Event IS NULL);";
	$result = mysqli_query($database_connection, $sql_race_name);
	
	echo "<script type=\"text/javascript\">
			alert(\"CSV files has been successfully imported.\");
			window.location = '.'
		 </script>";
}
?>
