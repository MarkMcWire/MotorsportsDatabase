<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["Champ"])) {$championship_name = $_GET["Champ"];} ELSE {$championship_name = $championship_name_global;}
if (isset($_GET["ID"])) {$RaceID = $_GET["ID"];} ELSE {$RaceID = 0;}
include("verbindung.php");
$query = "SELECT races.ID, races.Datum as Datum, championship.Saison as Saison, races.Event as Event, round(races.Runden * races.Length,2) as DistanzM, round(races.Runden * races.Length*1.60934,2) as DistanzK, races.Runden as Runden, tracks.ID as TrackID, tracks.Bezeichnung as Rennstrecke, races.Length as Length
FROM races LEFT JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID
WHERE races.ID = $RaceID or $RaceID = 0
ORDER BY races.ID";
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
$race_date = date('d.m.Y',strtotime($result['Datum']));
$trackID = $result['TrackID'];
print "<h2 align='center'>".$result['Saison']." ".$result['Event']."</h2>";
print "<h3 align='center'>".$race_date."</h3>";
print "<h3 align='center'><a href='../tracks/track.php?ID=".$trackID."&Champ=".$championship_name."'>".$result['Rennstrecke']."</a></h3>";
print "<h3 align='center'>".$result['Runden']." Runden (".$result['DistanzM']." Meilen / ".$result['DistanzK']." Kilometer)</h3>";
?>
<p>
<table border="2" cellspacing="10">
<tr>
<td>
<h3>Main Race</h3>
<table border="1" cellspacing="0">
<tr>
<th>Start</th>
<th>Finish</th>
<th>Car #</th>
<th>Driver</th>
<th>Laps</th>
<th>Led Laps</th>
<th>Status</th>
<th></th>
<th>Led Lap</th>
<th>Most Led Laps</th>
<th>Most Positions Gained</th>
<th>Fastest Race Lap</th>
</tr>
<?php
include("verbindung.php");
$query1 = "SELECT races.ID, race_results.Car, drivers.ID as DriverID, drivers.Name, race_results.Start, race_results.Finish, race_results.Laps, race_results.Led, race_results.MostLapsLed as MLL, race_results.MostPositionsGained as MPG, race_results.FastestRaceLap as FRL, race_results.Status, 
	IF(race_results.DNF = 1, '#EFCFFF', IF(race_results.LedLapFinish = 0, '#CFCFFF', IF(race_results.Finish > 5, '#CFEAFF', race_result_colors.ColorCode))) AS ColorCode
	FROM races LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN race_results on race_results.RaceID = races.ID LEFT JOIN drivers on race_results.DriverID = drivers.ID LEFT JOIN race_result_colors on (race_result_colors.Finish = race_results.Finish)
	WHERE (races.ID = $RaceID or $RaceID = 0) ORDER BY races.ID, race_results.Finish";
$recordset1 = $database_connection->query($query1);
$result_color='darkgrey';
while ($row = $recordset1->fetch_assoc())
{
$result_color = $row['ColorCode'];
$driverID= $row['DriverID'];
$LL= $row['Led'];
$MLL= $row['MLL'];
$MPG= $row['MPG'];
$FRL= $row['FRL'];
print "<tr bgcolor = $result_color>";
print "<td>";
echo $row['Start'];
print "</td>";
print "<td>";
echo $row['Finish'];
print "</td>";
print "<td>";
echo $row['Car'];
print "</td>";
print "<td>";
print "<a href='../driver/driver.php?ID=".$driverID."'>".$row['Name']."</a>";
print "</td>";
print "<td>";
echo $row['Laps'];
print "</td>";
print "<td>";
echo $LL;
print "</td>";
print "<td>";
echo $row['Status'];
print "</td>";
print "<td>";
print "</td>";
print '<td align = "center">';
if ($LL > 0) {echo 'x';}
print "</td>";
print '<td align = "center">';
if ($MLL > 0) {echo 'x';}
print "</td>";
print '<td align = "center">';
if ($MPG > 0) {echo 'x';}
print "</td>";
print "</td>";
print '<td align = "center">';
if ($FRL > 0) {echo 'x';}
print "</td>";
$result_color='darkgrey';
print "</tr>";
}
?>
</table>
</td>
</tr>
<tr>
<tr></tr>
<td>
<h3>Stage Results</h3>
<table border="1" cellspacing="0">
<?php
include("verbindung.php");
$query1 = "SELECT races.ID, stage_results.StageID, race_results.Car, drivers.ID as DriverID, drivers.Name, stage_results.Position, stage_results.Laps, IF(stage_results.Position > 5, '#CFEAFF', race_result_colors.ColorCode) AS ColorCode
	FROM races LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN stage_results on stage_results.RaceID = races.ID LEFT JOIN drivers on stage_results.DriverID = drivers.ID LEFT JOIN race_results on (race_results.RaceID = stage_results.RaceID and race_results.DriverID = stage_results.DriverID) LEFT JOIN race_result_colors on (race_result_colors.Finish = stage_results.Position)
	WHERE (races.ID = $RaceID or $RaceID = 0) ORDER BY races.ID, stage_results.StageID, stage_results.Position";
$recordset1 = $database_connection->query($query1);
$result_color='darkgrey';
while ($row = $recordset1->fetch_assoc())
{
$result_color = $row['ColorCode'];
$driverID= $row['DriverID'];
$position = $row['Position'];
$stageID = $row['StageID'];
if ($position == 1) {
	print "<tr rowspan=3><th colspan=12><br/>Result Stage ".$stageID."<br />&nbsp;</th></tr>";
	print "<tr><th>Position</th><th>Car #</th><th>Driver</th><th>Laps</th></tr>";
	}
print "<tr bgcolor = $result_color>";
print "<td>";
echo $position;
print "</td>";
print "<td>";
echo $row['Car'];
print "</td>";
print "<td>";
print "<a href='../driver/driver.php?ID=".$driverID."'>".$row['Name']."</a>";
print "</td>";
print "<td>";
echo $row['Laps'];
print "</td>";
$result_color='darkgrey';
print "</tr>";
}
?>
</table>
</td>
</tr>
<tr>
<tr></tr>
<td>
<h3>Sprint / Heat Races</h3>
<table border="1" cellspacing="0">
<?php
include("verbindung.php");
$query1 = "SELECT races.ID, sprint_results.SprintID, sprint_results.Car, drivers.ID as DriverID, drivers.Name, sprint_results.Start, sprint_results.Finish, sprint_results.Laps, sprint_results.Led, sprint_results.MostLapsLed as MLL, sprint_results.MostPositionsGained as MPG, sprint_results.FastestRaceLap as FRL, sprint_results.Status, 
	IF(sprint_results.DNF = 1, '#EFCFFF', IF(sprint_results.LedLapFinish = 0, '#CFCFFF', IF(sprint_results.Finish > 5, '#CFEAFF', race_result_colors.ColorCode))) AS ColorCode
	FROM races LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN sprint_results on sprint_results.RaceID = races.ID LEFT JOIN drivers on sprint_results.DriverID = drivers.ID LEFT JOIN race_result_colors on (race_result_colors.Finish = sprint_results.Finish)
	WHERE (races.ID = $RaceID or $RaceID = 0) ORDER BY races.ID, sprint_results.SprintID, sprint_results.Finish";
$recordset1 = $database_connection->query($query1);
$result_color='darkgrey';
while ($row = $recordset1->fetch_assoc())
{
$result_color = $row['ColorCode'];
$driverID= $row['DriverID'];
$LL= $row['Led'];
$MLL= $row['MLL'];
$MPG= $row['MPG'];
$FRL= $row['FRL'];
$finish = $row['Finish'];
$sprintID = $row['SprintID'];
if ($finish == 1) {
	print "<tr rowspan=3><th colspan=12><br/>Race Result Race #".$sprintID."<br />&nbsp;</th></tr>";
	print "<tr><th>Start</th><th>Finish</th><th>Driver</th><th>Car #</th><th>Laps</th><th>Led Laps</th><th>Status</th><th></th><th>Led Lap</th><th>Most Led Laps</th><th>Most Positions Gained</th><th>Fastest Race Lap</th></tr>";
	}
print "<tr bgcolor = $result_color>";
print "<td>";
echo $row['Start'];
print "</td>";
print "<td>";
echo $finish;
print "</td>";
print "<td>";
print "<a href='../driver/driver.php?ID=".$driverID."'>".$row['Name']."</a>";
print "</td>";
print "<td>";
echo $row['Car'];
print "</td>";
print "<td>";
echo $row['Laps'];
print "</td>";
print "<td>";
echo $LL;
print "</td>";
print "<td>";
echo $row['Status'];
print "</td>";
print "<td>";
print "</td>";
print '<td align = "center">';
if ($LL > 0) {echo 'x';}
print "</td>";
print '<td align = "center">';
if ($MLL > 0) {echo 'x';}
print "</td>";
print '<td align = "center">';
if ($MPG > 0) {echo 'x';}
print "</td>";
print "</td>";
print '<td align = "center">';
if ($FRL > 0) {echo 'x';}
print "</td>";
$result_color='darkgrey';
print "</tr>";
}
?>
</table>
</td>
</tr>
</table>
<br/>
</p>
<?php
print '<p align="center">';
include("verbindung.php");
$queryID = "SELECT MAX(r2.ID) AS IDV, MIN(r3.ID) AS IDN FROM races r1 LEFT JOIN races r2 ON r2.ID < r1.ID LEFT JOIN races r3 ON r3.ID > r1.ID WHERE r1.ID= $RaceID";
$recordsetID = $database_connection->query($queryID);
$resultID = $recordsetID->fetch_assoc();
$RaceIDN = $resultID['IDN'];
$RaceIDV = $resultID['IDV'];
print '</p>';
print '<p align="center">';
if ($RaceIDV) {print "<a href='?ID=$RaceIDV'>Vorheriges Rennen</a>";}
print "&nbsp&nbsp&nbsp&nbsp&nbsp;";
print "<a href='../index.php'>Zur&uuml;ck zum Index</a>";
print "&nbsp&nbsp&nbsp&nbsp&nbsp;";
if ($RaceIDN) {print "<a href='?ID=$RaceIDN'>Nachfolgendes Rennen</a>";}
print '</p>';
?>
</p>
</body>
</html>
