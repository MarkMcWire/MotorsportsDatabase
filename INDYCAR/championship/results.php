<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<h2>Rennergebnisse in der &Uuml;bersicht</h2>
<p>
<table border="2" cellspacing="10">
<?php
if (isset($_GET["Saison"])) {$season = $_GET["Saison"];} ELSE {$season = 0;}
if (isset($_GET["Champ"])) {$championship_name = $_GET["Champ"];} ELSE {$championship_name = 0;}
if (isset($_GET["Kategorie"])) {$category = $_GET["Kategorie"];} ELSE {$category = -1;}
if (isset($_GET["races"])) {$race_id_global = $_GET["races"];} ELSE {$race_id_global = 0;}
include("verbindung.php");
$query = "SELECT * FROM (
SELECT championship.Saison, championship.Bezeichnung as Championship, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, sum(race_results.Laps) as Laps, sum(race_results.Laps * races.Length) as Distanz
FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN race_results on (race_results.RaceID = races.ID) and (race_results.Finish = 1) LEFT JOIN tracks on races.TrackID = tracks.ID
WHERE (championship.Saison = $season or $season = 0) and (championship.Bezeichnung = '$championship_name' or '$championship_name' = '') and (championship.Kategorie = $category or championship.Kategorie = 0 or $category = -1)
GROUP BY championship.Saison, championship.Bezeichnung
UNION ALL
SELECT 0 as Saison, 'TBA' as Championship, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, sum(race_results.Laps) as Laps, sum(race_results.Laps * races.Length) as Distanz
FROM races LEFT JOIN championship on championship.RaceID = races.ID LEFT JOIN race_results on (race_results.RaceID = races.ID) and (race_results.Finish = 1) LEFT JOIN tracks on races.TrackID = tracks.ID
WHERE (championship.Saison is NULL)
) as temp WHERE (Saison = $season or $season = 0) ORDER BY Saison, Championship";
$recordset = $database_connection->query($query);
while ($result = $recordset->fetch_assoc())
{
$season = $result['Saison'];
$championship_name = $result['Championship'];
print "<tr>";
print "<td align='center'>";
print "<h3 align='center'>".$championship_name.' '.$season."</h3>";
print "<table border='1' cellspacing='0'>";
print "<tr>";
print "<th>Nummer</th>";
print "<th>Event</th>";
print "<th>Rennstrecke</th>";
print "<th></th>";
print "<th colspan='1'>Polesetter</th>";
print "<th></th>";
print "<th colspan='1'>Sieger</th>";
print "<th></th>";
print "<th colspan='1'>Meiste F&uuml;hrungsrunden</th>";
print "<th></th>";
print "<th colspan='1'>Schnellste Rennrunde</th>";
print "<th></th>";
print "<th colspan='1'>Meiste Positionen gewonnen</th>";
print "</tr>";
include("verbindung.php");
$query0 = "SELECT races.ID, races.Datum as Datum, coalesce(championship.Saison, 0) as Saison, championship.Bezeichnung as Championship, races.Event, round(races.Runden * races.Length,2) as Distanz, races.Runden as Runden, tracks.ID as TrackID, tracks.Kennzeichen as StreckenKz, tracks.Bezeichnung as Rennstrecke, races.Length, track_type.Type, track_type.ColorCode
FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID
WHERE (championship.Saison = $season or $season = 0) and (championship.Bezeichnung = '$championship_name' or '$championship_name' = '') and (championship.Kategorie = $category or championship.Kategorie = 0 or $category = -1) and (championship.RaceID <= $race_id_global or $race_id_global = 0)
GROUP BY championship.Bezeichnung, championship.Saison, races.ID, races.Datum, races.Event, races.Runden, races.Length, tracks.ID, tracks.Bezeichnung, track_type.Type, track_type.ColorCode ORDER BY championship.Saison, races.Datum";
$recordset0 = $database_connection->query($query0);
$track_color = 'darkgrey';
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$ID = $row['ID'];
$track_type = $row['Type'];
$track_color = $row['ColorCode'];
print "<tr bgcolor = $track_color>";
print "<td>";
print "<a href='../championship/raceresult.php?ID=".$ID."&Champ=".$championship_name."'>";
echo $i;
print "</a>";
print "</td>";
print "<td>";
print "<a href='../championship/raceresult.php?ID=".$ID."&Champ=".$championship_name."'>";
echo $row['Event'];
print "</a>";
print "</td>";
print "<td>";
$trackID= $row['TrackID'];
print "<a href='../tracks/track.php?ID=".$trackID."&Champ=".$championship_name."'>";
echo $row['Rennstrecke'];
print "</a>";
print "</td>";
include("verbindung.php");
$query1 = "SELECT tracks.Bezeichnung as Rennstrecke, championship.Saison as Saison, races.Event as Event, race_results.Car as Autonummer, race_results.Start, race_results.Status,
drivers.ID as DriverID, drivers.Display_Name as DriverName
FROM race_results INNER JOIN races on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID INNER JOIN tracks on races.TrackID = tracks.ID 
INNER JOIN drivers on race_results.DriverID = drivers.ID 
WHERE (race_results.Start = 1) and (races.ID = $ID or $ID = 0)
GROUP BY championship.Saison, races.ID, races.Event, tracks.Bezeichnung, race_results.Car, race_results.Start, race_results.Status, drivers.ID, drivers.Display_Name
ORDER BY races.ID, race_results.Start";
$recordset1 = $database_connection->query($query1);
$result1 = $recordset1->fetch_assoc();
if ($result1) {$driverID = $result1['DriverID'];} else {$driverID = 0;}
print "<td></td>";
print "<td>";
print "<a href='../driver/driver.php?ID=".$driverID."'>";
if ($result1) {echo $result1['DriverName'];} else {echo '';}
print "</a>";
print "</td>";
include("verbindung.php");
$query2 = "SELECT tracks.Bezeichnung as Rennstrecke, championship.Saison as Saison, races.Event as Event, race_results.Car as Autonummer, race_results.Finish, race_results.Status,
drivers.ID as DriverID, drivers.Display_Name as DriverName
FROM race_results INNER JOIN races on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID INNER JOIN tracks on races.TrackID = tracks.ID #
INNER JOIN drivers on race_results.DriverID = drivers.ID 
WHERE (race_results.Finish = 1) and (races.ID = $ID or $ID = 0)
GROUP BY championship.Saison, races.ID, races.Event, tracks.Bezeichnung, race_results.Car, race_results.Finish, race_results.Status, drivers.ID, drivers.Display_Name
ORDER BY races.ID, race_results.Finish";
$recordset2 = $database_connection->query($query2);
$result2 = $recordset2->fetch_assoc();
if ($result2) {$driverID = $result2['DriverID'];} else {$driverID = 0;}
print "<td></td>";
print "<td>";
print "<b>";
print "<a href='../driver/driver.php?ID=".$driverID."'>";
if ($result2) {echo $result2['DriverName'];} else {echo '';}
print "</a>";
print "</b>";
print "</td>";
include("verbindung.php");
$query3 = "SELECT tracks.Bezeichnung as Rennstrecke, championship.Saison as Saison, races.Event as Event, race_results.Car as Autonummer, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status,
drivers.ID as DriverID, drivers.Display_Name as DriverName
FROM race_results INNER JOIN races on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID INNER JOIN tracks on races.TrackID = tracks.ID 
INNER JOIN drivers on race_results.DriverID = drivers.ID 
WHERE race_results.MostLapsLed and (races.ID = $ID or $ID = 0)
GROUP BY championship.Saison, races.ID, races.Event, tracks.Bezeichnung, race_results.Car, race_results.Finish, race_results.Laps, race_results.Led, race_results.Status, drivers.ID, drivers.Display_Name
ORDER BY races.ID, race_results.Led, race_results.Finish";
$recordset3 = $database_connection->query($query3);
;
print "<td></td>";
print "<td>";
while ($result3 = $recordset3->fetch_assoc())
{
if ($result3) {$driverID = $result3['DriverID'];} else {$driverID = 0;}
print "<a href='../driver/driver.php?ID=".$driverID."'>";
echo $result3['DriverName']." (".$result3['Led'].")";
print "</a>";
print "<br />";
}
print "</td>";
include("verbindung.php");
$query4 = "SELECT tracks.Bezeichnung as Rennstrecke, championship.Saison as Saison, races.Event as Event, race_results.Car as Autonummer, race_results.Finish, race_results.Status,
drivers.ID as DriverID, drivers.Display_Name as DriverName
FROM race_results INNER JOIN races on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID INNER JOIN tracks on races.TrackID = tracks.ID 
INNER JOIN drivers on race_results.DriverID = drivers.ID 
WHERE race_results.FastestRaceLap and (races.ID = $ID or $ID = 0)
GROUP BY championship.Saison, races.ID, races.Event, tracks.Bezeichnung, race_results.Car, race_results.Finish, race_results.Status, drivers.ID, drivers.Display_Name
ORDER BY races.ID, race_results.Finish";
$recordset4 = $database_connection->query($query4);
;
print "<td></td>";
print "<td>";
while ($result4 = $recordset4->fetch_assoc())
{
if ($result4) {$driverID = $result4['DriverID'];} else {$driverID = 0;}
print "<a href='../driver/driver.php?ID=".$driverID."'>";
echo $result4['DriverName'];
print "</a>";
print "<br />";
}
print "</td>";
include("verbindung.php");
$query5 = "SELECT tracks.Bezeichnung as Rennstrecke, championship.Saison as Saison, races.Event as Event, race_results.Car as Autonummer, race_results.RaceID, race_results.Finish, race_results.Status, (race_results.Start-race_results.Finish) as MPG,
drivers.ID as DriverID, drivers.Display_Name as DriverName
FROM race_results INNER JOIN races on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID INNER JOIN tracks on races.TrackID = tracks.ID 
INNER JOIN drivers on race_results.DriverID = drivers.ID 
WHERE race_results.MostPositionsGained and (races.ID = $ID or $ID = 0)
GROUP BY championship.Saison, races.ID, races.Event, tracks.Bezeichnung, race_results.Car, race_results.Start, race_results.Finish, race_results.Status, drivers.ID, drivers.Display_Name
ORDER BY race_results.RaceID, race_results.Finish";
$recordset5 = $database_connection->query($query5);
print "<td></td>";
print "<td>";
while ($result5 = $recordset5->fetch_assoc())
{
if ($result5) {$driverID = $result5['DriverID'];} else {$driverID = 0;}
print "<a href='../driver/driver.php?ID=".$driverID."'>";
echo $result5['DriverName']." (".$result5['MPG'].")";
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
<br/>
</p>
<?php
print '<p>';
print '</p>';
print '<p align="center">';
print "<a href='?Champ=".$championship_name."&Saison=".($season - 1)."&Kategorie=".$category."'>Vorherige Saison</a>";
print "&nbsp&nbsp&nbsp&nbsp&nbsp;";
print "<a href='index.php'>Zur&uuml;ck zum Index</a>";
print "&nbsp&nbsp&nbsp&nbsp&nbsp;";
print "<a href='?Champ=".$championship_name."&Saison=".($season + 1)."&Kategorie=".$category."'>Nachfolgende Saison</a>";
print '</p>';
?>
</p>
</body>
</html>
