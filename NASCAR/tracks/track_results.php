<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<p>
<table border="2" cellspacing="10">
<?php
if (isset($_GET["ID"])) {$trackID = $_GET["ID"];} ELSE {$trackID = 0;}
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query = "SELECT tracks.ID as TrackID, tracks.Bezeichnung as Bezeichnung, tracks.Ort as Ort, tracks.Land as Land, tracks.Kennzeichen as StreckenKz, tracks.Eroeffnung as Eroeffnung, tracks.Schliessung as Einstellung, MAX(races.Length) as Length
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID LEFT JOIN championship on races.ID = championship.RaceID
WHERE (tracks.ID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, tracks.Kennzeichen, tracks.Eroeffnung, tracks.Schliessung
ORDER BY Bezeichnung";
$recordset = $database_connection->query($query);
while ($result = $recordset->fetch_assoc())
{
$trackID = $result['TrackID'];
$track_name = $result['Bezeichnung'];
print "<tr>";
print "<td align='center'>";
print "<table border='1' cellspacing='0'>";
print "<tr>";
print "<th>Datum</th>";
print "<th>Event</th>";
print "<th>Runden</th>";
print "<th>Länge</th>";
print "<th>Distanz</th>";
print "<th></th>";
print "<th>Polesetter</th>";
print "<th>Sieger</th>";
print "<th>Meiste F&uuml;hrungsrunden</th>";
print "<th>Schnellste Rennrunde</th>";
print "<th>Meiste Positionen gewonnen</th>";
print "</tr>";
include("verbindung.php");
$query0 = "SELECT races.ID as ID, races.Datum as Datum, coalesce(championship.Saison, 0) as Saison, races.Event as Event, tracks.ID as TrackID, tracks.Kennzeichen as StreckenKz, tracks.Bezeichnung as Rennstrecke, track_type.Type, track_type.ColorCode, 
races.Runden as Laps, round(races.Runden * races.Length, 0) as RaceMiles, round(races.Runden * races.Length * 1.60934, 0) as RaceKm, round(races.Length, 3) as Miles, round(1.60934*races.Length, 3) as Kilometer
FROM races LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN championship on races.ID = championship.RaceID LEFT JOIN track_type on track_type.ID = races.TypeID 
WHERE (TrackID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY races.ID, races.Datum, championship.Saison, races.Event, races.Runden, races.Length, tracks.ID, tracks.Kennzeichen, tracks.Bezeichnung, races.Length, track_type.Type, track_type.ColorCode ORDER BY races.Datum";
//print $query2;
$recordset0 = $database_connection->query($query0);
$track_color='darkgrey';
while ($row = $recordset0->fetch_assoc())
{
$ID = $row['ID'];
$typ = $row['Type'];
$track_color = $row['ColorCode'];
$race_date = date('d.m.Y',strtotime($row['Datum']));
print "<tr bgcolor =$track_color>";
print "<td>";
print "<a href='../championship/raceresult.php?ID=".$ID."'>";
print $race_date;
print "</a>";
print "</td>";
print "<td>";
print "<a href='../championship/raceresult.php?ID=".$ID."'>";
echo $row['Event'];
print "</a>";
print "</td>";
print "<td>";
print $row['Laps'];
print "</td>";
print "<td>";
print $row['Miles'].' mi ('.$row['Kilometer'].' km)';
print "</td>";
print "<td>";
print $row['RaceMiles'].' mi ('.$row['RaceKm'].' km)';
print "</td>";
print "<td>";
print "</td>";
include("verbindung.php");
$query1 = "SELECT races.Event as Event, drivers.ID as DriverID, drivers.Display_Name, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
LEFT JOIN championship on championship.RaceID = races.ID
INNER JOIN tracks on races.TrackID = tracks.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE (race_results.Start = 1) and (races.ID = $ID or $ID = 0)
GROUP BY races.ID, races.Event, drivers.ID, drivers.Display_Name, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status
ORDER BY races.ID, race_results.Start";
$recordset1 = $database_connection->query($query1);
$result1 = $recordset1->fetch_assoc();
print "<td>";
if ($result1) {$driverID = $result1['DriverID'];} else {$driverID = 0;}
print "<a href='../driver/driver.php?ID=".$driverID."'>";
if ($result1) {echo $result1['Display_Name'];} else {echo '';}
print "</a>";
print "</td>";
print "</td>";
include("verbindung.php");
$query2 = "SELECT races.Event as Event, drivers.ID as DriverID, drivers.Display_Name, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
LEFT JOIN championship on championship.RaceID = races.ID
INNER JOIN tracks on races.TrackID = tracks.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE (race_results.Finish = 1) and (races.ID = $ID or $ID = 0)
GROUP BY races.ID, races.Event, drivers.ID, drivers.Display_Name, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status
ORDER BY races.ID, race_results.Finish";
$recordset2 = $database_connection->query($query2);
$result2 = $recordset2->fetch_assoc();
print "<td>";
print "<b>";
if ($result2) {$driverID = $result2['DriverID'];} else {$driverID = 0;}
print "<a href='../driver/driver.php?ID=".$driverID."'>";
if ($result2) {echo $result2['Display_Name'];} else {echo '';}
print "</a>";
print "</b>";
print "</td>";
include("verbindung.php");
$query3 = "SELECT races.Event as Event, drivers.ID as DriverID, drivers.Display_Name, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
LEFT JOIN championship on championship.RaceID = races.ID
INNER JOIN tracks on races.TrackID = tracks.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.MostLapsLed and (races.ID = $ID or $ID = 0)
GROUP BY races.ID, races.Event, drivers.ID, drivers.Display_Name, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status
ORDER BY races.ID, race_results.Led";
$recordset3 = $database_connection->query($query3);
;
print "<td>";
while ($result3 = $recordset3->fetch_assoc())
{
if ($result3) {$driverID = $result3['DriverID'];} else {$driverID = 0;}
print "<a href='../driver/driver.php?ID=".$driverID."'>";
echo $result3['Display_Name']." (".$result3['Led'].")";
print "</a>";
print "<br />";
}
print "</td>";
include("verbindung.php");
$query4 = "SELECT races.Event as Event, drivers.ID as DriverID, drivers.Display_Name, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
LEFT JOIN championship on championship.RaceID = races.ID
INNER JOIN tracks on races.TrackID = tracks.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.FastestRaceLap and (races.ID = $ID or $ID = 0)
GROUP BY races.ID, races.Event, drivers.ID, drivers.Display_Name, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status
ORDER BY races.ID, race_results.Led";
$recordset4 = $database_connection->query($query4);
;
print "<td>";
while ($result4 = $recordset4->fetch_assoc())
{
if ($result4) {$driverID = $result4['DriverID'];} else {$driverID = 0;}
print "<a href='../driver/driver.php?ID=".$driverID."'>";
if ($result4) {echo $result4['Display_Name'];} else {echo '';}
print "</a>";
print "<br />";
}
print "</td>";
include("verbindung.php");
$query5 = "SELECT races.Event as Event, drivers.ID as DriverID, drivers.Display_Name, race_results.RaceID, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status, (race_results.Start-race_results.Finish) as MPG
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
LEFT JOIN championship on championship.RaceID = races.ID
INNER JOIN tracks on races.TrackID = tracks.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.MostPositionsGained and (races.ID = $ID or $ID = 0)
GROUP BY races.ID, races.Event, drivers.ID, drivers.Display_Name, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status
ORDER BY race_results.RaceID, drivers.ID";
$recordset5 = $database_connection->query($query5);
print "<td>";
while ($result5 = $recordset5->fetch_assoc())
{
if ($result5) {$driverID = $result5['DriverID'];} else {$driverID = 0;}
print "<a href='../driver/driver.php?ID=".$driverID."'>";
echo $result5['Display_Name']." (".$result5['MPG'].")";
print "</a>";
print "<br />";
}
print "</td>";
print "</tr>";
}
print "</table>";
print "</td>";
print "</tr>";
}
?>
</table>
</p>
</body>
</html>
