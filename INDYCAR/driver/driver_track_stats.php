<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["ID"])) {$driverID = $_GET["ID"]; $query = "SELECT * FROM drivers WHERE (ID = $driverID) ORDER BY Kategorie, Display_Name";} ELSE {$query = "SELECT * FROM drivers ORDER BY Kategorie, Display_Name";}
include("verbindung.php");
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
#if(!$row)die("Keine Ergebnisse <br/>");

$ID = $result['ID'];
$name = $result['Display_Name'];
print '<p>';
print "<h2 align='center'>$name</h2>";
print "</p>";
?>
<p align = "center">
<table border="1" cellspacing="0">
<tr>
<th>Rennstrecke</th>
<th>Rennteilnahmen</th>
<th colspan ="4">Qualifikationen</th>
<th colspan ="7">Zielank&uuml;nfte</th>
<th colspan="2">Alle F&uuml;hrungsrunden</th>
<th>Meiste F&uuml;hrungsrunden</th>
<th>Schnellste Rennrunden</th>
<th>H&ouml;chste Positionsgewinne</th>
<th>Ausf&auml;lle</th>
</tr>
<tr>
<th></th>
<th></th>
<th>Min</th>
<th>Max</th>
<th>Durchschnitt</th>
<th>Poles</th>
<th>Min</th>
<th>Max</th>
<th>Durchschnitt</th>
<th>Siege</th>
<th>Podiums</th>
<th>Top 5</th>
<th>Top 10</th>
<th></th>
<th>%</th>
<th></th>
<th></th>
<th></th>
<th></th>
</tr>
<?php
$query_tracks = "SELECT tracks.ID as TrackID, tracks.Bezeichnung as Bezeichnung, tracks.Kennzeichen as StreckenKz, track_type.Type, track_type.ColorCode
FROM tracks INNER JOIN races on races.TrackID = tracks.ID INNER JOIN race_results on race_results.RaceID = races.ID LEFT JOIN track_type on track_type.ID = races.TypeID
WHERE (race_results.DriverID = $driverID or $driverID = 0)
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Kennzeichen, track_type.ID, track_type.Type, track_type.ColorCode
ORDER BY tracks.Bezeichnung ASC";
//print $query_tracks;
include("verbindung.php");
$recordset_tracks = $database_connection->query($query_tracks);
while ($row_tracks = $recordset_tracks->fetch_assoc())
{
$trackID = $row_tracks['TrackID'];
$track_abbreviation = $row_tracks['StreckenKz'];
$track_name = $row_tracks['Bezeichnung'];
$track_type = $row_tracks['Type'];
$track_color = $row_tracks['ColorCode'];
print "<tr bgcolor='$track_color' align='center'>";
print "<td>";
print $track_name."<br>".$track_abbreviation;
print "</td>";
include("verbindung.php");
$query = "SELECT COUNT(race_results.RaceID) AS Events, SUM(race_results.Laps) AS Laps, SUM(race_results.DNF) AS DNF, 
MIN(race_results.Start) AS MinQual, MAX(race_results.Start) AS MaxQual, AVG(race_results.Start) AS AvgQual, SUM(race_results.Start = 1) AS Poles,
MIN(race_results.Finish) AS MinFin, MAX(race_results.Finish) AS MaxFin, AVG(race_results.Finish) AS AvgFin, SUM(race_results.Finish = 1) AS Wins,
SUM(race_results.Finish <= 3) AS Podiums, SUM(race_results.Finish <= 5) AS Top5, SUM(race_results.Finish <= 10) AS Top10, 
SUM(race_results.Led) AS Led, SUM(race_results.MostLapsLed) AS MLL, SUM(race_results.FastestRaceLap) AS FRL, SUM(race_results.MostPositionsGained) AS MPG
FROM race_results INNER JOIN races ON races.ID = race_results.RaceID INNER JOIN championship ON championship.RaceID = races.ID
WHERE (race_results.DriverID = ".$driverID.") AND (races.TrackID = $trackID or $trackID = 0)
GROUP BY races.TrackID";
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
$laps = $result['Laps'];
$led = $result['Led'];
$wins = $result['Wins'];
$poles = $result['Poles'];
if ($result['Events'] > 0) {
print "<td>";
echo $result['Events'];
print "</td>";
print "<td>";
if ($result['MinQual'] > 0) {echo $result['MinQual'];}
print "</td>";
print "<td>";
if ($result['MaxQual'] > 0) {echo $result['MaxQual'];}
print "</td>";
print "<td>";
if ($result['AvgQual'] > 0) {echo round($result['AvgQual']);}
print "</td>";
if ($poles > 0)
{
	print "<td><b>";
	echo $poles;
	print "</b></td>";
}
else
{
	print "<td>";
	print "</td>";
}
print "<td>";
if ($result['MinFin'] > 0) {echo $result['MinFin'];}
print "</td>";
print "<td>";
if ($result['MaxFin'] > 0) {echo $result['MaxFin'];}
print "</td>";
print "<td>";
if ($result['AvgFin'] > 0) {echo round($result['AvgFin']);}
print "</td>";
if ($wins > 0)
{
	print "<td><b>";
	echo $wins;
	print "</b></td>";
}
else
{
	print "<td>";
	print "</td>";
}
print "<td>";
if ($result['Podiums'] > 0) {echo $result['Podiums'];}
print "</td>";
print "<td>";
if ($result['Top5'] > 0) {echo $result['Top5'];}
print "</td>";
print "<td>";
if ($result['Top10'] > 0) {echo $result['Top10'];}
print "</td>";
print "<td>";
if ($result['Led'] > 0) {echo $result['Led'];}
print "</td>";
print "<td>";
if ($result['Led'] > 0 && $result['Laps'] > 0) {echo round(100*$result['Led']/$result['Laps'],2);}
print "</td>";
print "<td>";
if ($result['MLL'] > 0) {echo $result['MLL'];}
print "</td>";
print "<td>";
if ($result['FRL'] > 0) {echo $result['FRL'];}
print "</td>";
print "<td>";
if ($result['MPG'] > 0) {echo $result['MPG'];}
print "</td>";
print "<td>";
if ($result['DNF'] > 0) {echo $result['DNF'];}
print "</td>";
print "<tr/>";
}
}
?>
</p>
</td>
</tr>
</table>
</td>
</tr>
</p>
</td>
</tr>
</table>
</p>
<br/>
<p align = "center">
<?php
print "<a href='driver.php?ID=".$driverID."'>Zur&uuml;ck zur &Uuml;bersicht</a>";
?>
</p>
</body>
</html>
