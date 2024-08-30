<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["ID"])) {$driverID = $_GET["ID"]; $query = "SELECT * FROM drivers WHERE (ID = $driverID) ORDER BY Kategorie, Display_Name";} ELSE {$query = "SELECT * FROM drivers ORDER BY Kategorie, Display_Name";}
if (isset($_GET["Saison"])) {$season = $_GET["Saison"];} else {$season = 0;}
if (isset($_GET["Champ"])) {$championship_name = $_GET["Champ"];} else {$championship_name = '';}
include("verbindung.php");
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
#if(!$row)die("Keine Ergebnisse <br/>");

$ID = $result['ID'];
$name = $result['Display_Name'];
print "<p>";
print "<h2 align='center'>$name</h2>";
print "</p>";
?>
<p>
<table border="2" cellspacing="10">
<tr>
<td>
<?php
if ($season == 0) {print '<h3>Alle Resultate</h3>';} else {print '<h3>'.$season.' '.$championship_name.'</h3>';}
?>
<p align='center'>
<table border="1" cellspacing="0">
<tr>
<th>Event</th>
<th>Rennstrecke</th>
<th>Auto</th>
<th>Start</th>
<th>Finish</th>
<th>Runden</th>
<th>F&uuml;hrungsrunden</th>
<th>Positionsgewinn</th>
<th>Status</th>
</tr>
<?php
include("verbindung.php");
$query1 = "SELECT races.ID, championship.Saison, races.Event, tracks.ID as TrackID, tracks.Bezeichnung as Rennstrecke, race_results.Car as Autonummer, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.MostLapsLed as MLL, race_results.MostPositionsGained as MPG, race_results.Status, 
IF(race_results.DNF = 1, '#EFCFFF', IF(race_results.LedLapFinish = 0, '#CFCFFF', IF(race_results.Finish > 5, '#CFEAFF', race_result_colors.ColorCode))) AS ColorCode
FROM races LEFT JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN race_results on race_results.RaceID = races.ID LEFT JOIN drivers on race_results.DriverID = drivers.ID LEFT JOIN race_result_colors on (race_result_colors.Finish = race_results.Finish)
WHERE (drivers.ID = $driverID or $driverID = 0) and (championship.Saison = $season or $season = 0) and (championship.Bezeichnung = '$championship_name' or '$championship_name' = '')
GROUP BY races.ID, drivers.ID, championship.Saison, races.Event, tracks.ID, tracks.Bezeichnung, race_results.Car, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.MostLapsLed, race_results.MostPositionsGained, race_results.Status, race_result_colors.ColorCode
ORDER BY races.ID, race_results.Finish";
//print $query1;
$recordset1 = $database_connection->query($query1);
$result_color = 'darkgrey';
while ($row = $recordset1->fetch_assoc())
{
$result_color = $row['ColorCode'];
$raceID = $row['ID'];
$trackID = $row['TrackID'];
$start = $row['Start'];
$finish = $row['Finish'];
$led = $row['Led'];
$mll = $row['MLL'];
$mpg = $row['MPG'];
print "<tr bgcolor = $result_color>";
print "<td>";
print "<a href='../championship/raceresult.php?ID= $raceID'>";
echo $row['Saison']." ".$row['Event'];
print "</a>";
print "</td>";
print "<td>";
print "<a href='../tracks/track.php?ID=".$trackID."'>";
echo $row['Rennstrecke'];
print "</a>";
print "</td>";
print "<td>";
echo '#'.$row['Autonummer'];
print "</td>";
print "<td>";
if ($start == 1) {
	print "<b>".$start."</b>";
}
else {
	echo $start;
}
print "</td>";
print "<td>";
if ($finish == 1) {
	print "<b>".$finish."</b>";
}
else {
	echo $finish;
}
print "</td>";
print "<td>";
echo $row['Laps'];
print "</td>";
print "<td>";
if ($mll == 1) {
	print "<b>".$led."</b>";
}
else {
	echo $led;
}
print "</td>";
print "<td>";
if ($mpg == 1) {
	print "<b>".($start-$finish)."</b>";
}
else {
	echo ($start-$finish);
}
print "</td>";
print "<td>";
echo $row['Status'];
print "</td>";
$result_color = 'darkgrey';
print "</tr>";
}
?>
</table>
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
