<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<p>
<h2>&Uuml;bersicht Siege nach Rennstrecken und Meisterschaft</h2>
<br/>
<p align="left">
<?php
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query_championship = "SELECT Bezeichnung FROM championship
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') 
GROUP BY Bezeichnung ORDER BY Bezeichnung";
$recordset_championship = $database_connection->query($query_championship);

while ($results_championship = $recordset_championship->fetch_assoc()) {
	
	print "<h3>".$results_championship['Bezeichnung']."</h3><br/>";
	
	$query_drivers = "SELECT DriverID, COUNT(DISTINCT races.TrackID) AS Wins FROM race_results INNER JOIN races ON races.ID = race_results.RaceID INNER JOIN championship ON championship.RaceID = race_results.RaceID 
	WHERE (championship.Bezeichnung LIKE '".$results_championship['Bezeichnung']."') AND (race_results.Finish = 1)
	GROUP BY race_results.DriverID ORDER BY COUNT(DISTINCT races.TrackID) DESC Limit 36";
	$recordset_drivers = $database_connection->query($query_drivers);
	
	while ($results_drivers = $recordset_drivers->fetch_assoc()) {
		
		$events = 0;
		print '<TABLE border=1 cellpadding=1 cellspacing=1><TR>';
		print '<TH><FONT size="2">Fahrer</FONT></TH>';
		print '<TH><FONT size="2">Total</FONT></TH>';
		print '<TH><FONT size="2">Prozent</FONT></TH>';

		$query_tracks = "SELECT tracks.ID as TrackID, tracks.Kennzeichen as StreckenKz, MAX(races.Length) as Length, MAX(track_type.Type) AS Type, MAX(track_type.ColorCode) AS ColorCode
		FROM tracks INNER JOIN races on races.TrackID = tracks.ID INNER JOIN race_results on race_results.RaceID = races.ID INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN track_type on track_type.ID = races.TypeID
		WHERE (race_results.DriverID = ".$results_drivers['DriverID'].") and (championship.Bezeichnung LIKE '".$results_championship['Bezeichnung']."') GROUP BY tracks.ID, tracks.Kennzeichen ORDER BY tracks.Kennzeichen, tracks.ID";

		include("verbindung.php");
		$recordset_tracks = $database_connection->query($query_tracks);
		while ($result_tracks = $recordset_tracks->fetch_assoc())
		{
			$track_color = 'white';
			$track_color = $result_tracks['ColorCode'];
			$trackID = $result_tracks['TrackID'];
			$track_abbreviation = $result_tracks['StreckenKz'];
			$events = $events + 1;
			print "<TH bgcolor= $track_color><font size='2'><a'>".$track_abbreviation."</a></font></TH>";
		}
		print "</TR>";
		
		$query_wins = "SELECT drivers.Name as drivers, drivers.ID as DriverID, count(distinct tracks.ID) as Siege
		FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID
		INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks on tracks.ID = races.TrackID INNER JOIN championship on championship.RaceID = races.ID
		WHERE (race_results.Finish = 1) and (drivers.ID = ".$results_drivers['DriverID'].") and (championship.Bezeichnung LIKE '".$results_championship['Bezeichnung']."') and (championship.Bezeichnung LIKE '".$results_championship['Bezeichnung']."') 
		GROUP BY drivers.Name, drivers.ID ORDER BY Siege DESC";
		
		include("verbindung.php");
		$recordset_wins = $database_connection->query($query_wins);

		while ($results_wins = $recordset_wins->fetch_assoc())
		{
			$driverID = $results_wins['DriverID'];
			
			print "<TR>";
			print "<TD width=160><FONT size='2'><a href='../driver/driver.php?ID=".$driverID."'>".$results_wins['drivers'].'</a></FONT></TD>';
			print '<TD width=80><FONT><b>'.$results_wins['Siege'].' of '.$events.'</b></FONT></TD>';
			print '<TD width=80><FONT><b>'.Round(100 * $results_wins['Siege'] / $events, 2).' % </b></FONT></TD>';
	
			include("verbindung.php");
			$recordset = $database_connection->query($query_tracks);
			while ($result = $recordset->fetch_assoc())
			{
				$track_color = 'white';
				$track_color = $result['ColorCode'];
				$trackID = $result['TrackID'];
				$track_abbreviation = $result['StreckenKz'];
				
				$query_details = "SELECT MIN(Finish) as Siege FROM race_results INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks ON tracks.ID = races.TrackID INNER JOIN championship ON championship.RaceID = races.ID
				WHERE (tracks.ID = $trackID) and (race_results.DriverID = $driverID) and (championship.Bezeichnung LIKE '".$results_championship['Bezeichnung']."') GROUP BY race_results.DriverID, races.TypeID, tracks.Kennzeichen";
				
				include("verbindung.php");
				$recordset_details = $database_connection->query($query_details);
				
				if ($results_details = $recordset_details->fetch_assoc())
				{
					$finish = $results_details['Siege'];
					if ($finish == 1) {print "<TD bgcolor= $track_color align='center'><b>x</b></TD>";}
					else {print "<TD bgcolor='white' align='center'>-</TD>";}
				} else {
					print "<TD bgcolor='white' align='center'></TD>";
				}
			}
			print "</TR>";
		}
		print "</TABLE><BR/>";
	}
}
?>
</p>
<br/>
</p>
<p align="center">
<a href='../index.php'>Zur&uuml;ck zum Index</a>
</p>
</body>
</html>
