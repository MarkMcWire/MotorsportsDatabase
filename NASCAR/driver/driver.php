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

$ID = $result['ID'];
$name = $result['Display_Name'];
print '<p>';
print "<h2>$name</h2>";
print "</p>";
?>
<p>
<table border="2" cellspacing="10">
<tr>
<td>
<table border="1" cellspacing="6">
<tr>
<td>
<h3>Bewertung des Fahrers</h3>
<table border="1" cellspacing="0">
<tr>
<th colspan="1"><a>Ausfallquote</a></th>
<th colspan="3"><a>Qualifying</a></th>
<th colspan="3"><a>Finishing</a></th>
<th colspan="1"><a>Positionsänderung</a></th>
<th></th>
<th colspan="2"><a>Road Course</a></th>
<th colspan="2"><a>Short Track</a></th>
<th colspan="2"><a>Intermediate</a></th>
<th colspan="2"><a>SuperSpeedway</a></th>
</tr>
<tr>
<th colspan="1"></th>
<th colspan="1"><a>Min.</a></th>
<th colspan="1"><a>Max.</a></th>
<th colspan="1"><a>Avg.</a></th>
<th colspan="1"><a>Min.</a></th>
<th colspan="1"><a>Max.</a></th>
<th colspan="1"><a>Avg.</a></th>
<th colspan="1"><a>Avg.</a></th>
<th></a></th>
<th colspan="1"><a>Durchschnittl.<br />Start</a></a></th>
<th colspan="1"><a>Durchschnittl.<br />Ziel</a></th>
<th colspan="1"><a>Durchschnittl.<br />Start</a></a></th>
<th colspan="1"><a>Durchschnittl.<br />Ziel</a></th>
<th colspan="1"><a>Durchschnittl.<br />Start</a></a></th>
<th colspan="1"><a>Durchschnittl.<br />Ziel</a></th>
<th colspan="1"><a>Durchschnittl.<br />Start</a></a></th>
<th colspan="1"><a>Durchschnittl.<br />Ziel</a></th>
</tr>
<?php
print "<tr bgcolor='white' align='center'>";
include("verbindung.php");
$queryConsistency="SELECT sum(race_results.DNF)/count(race_results.Finish)*100 as Consistency FROM race_results WHERE race_results.DriverID =".$driverID;
$recordsetConsistency = $database_connection->query($queryConsistency);
$resultConsistency = $recordsetConsistency->fetch_assoc();
print "<td bgcolor='lightgrey'>";
echo number_format($resultConsistency['Consistency'], 2);
print "&nbsp;%";
print "</td>";
$queryQualifying="SELECT min(start) as QualifyingMin, max(start) as QualifyingMax, avg(start) as QualifyingAvg FROM race_results WHERE start > 0 and race_results.DriverID =".$driverID;
$recordsetQualifying = $database_connection->query($queryQualifying);
$resultQualifying = $recordsetQualifying->fetch_assoc();
print "<td bgcolor='lightgrey'>";
echo $resultQualifying['QualifyingMin'];
print "</td>";
print "<td bgcolor='grey'>";
echo $resultQualifying['QualifyingMax'];
print "</td>";
print "<td bgcolor='darkgrey'>";
echo number_format($resultQualifying['QualifyingAvg'], 2);
print "</td>";
$queryFinishing="SELECT min(finish) as FinishingMin, max(finish) as FinishingMax, avg(finish) as FinishingAvg FROM race_results WHERE race_results.DriverID =".$driverID;
$recordsetFinishing = $database_connection->query($queryFinishing);
$resultFinishing = $recordsetFinishing->fetch_assoc();
print "<td bgcolor='lightgrey'>";
echo $resultFinishing['FinishingMin'];
print "</td>";
print "<td bgcolor='grey'>";
echo $resultFinishing['FinishingMax'];
print "</td>";
print "<td bgcolor='darkgrey'>";
echo number_format($resultFinishing['FinishingAvg'], 2);
print "</td>";
$queryAgression="SELECT avg(start-finish) as Aggression FROM race_results WHERE race_results.DriverID =".$driverID;
$recordsetAgression = $database_connection->query($queryAgression);
$resultAgression = $recordsetAgression->fetch_assoc();
print "<td bgcolor='darkgrey'>";
echo number_format($resultAgression['Aggression'], 2);
print "</td>";
print "<td>";
print "</td>";
$queryRoad="SELECT avg(start) as QualifyingAvg, avg(finish) as FinishingAvg FROM race_results
INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks on tracks.ID = races.TrackID WHERE (races.TypeID BETWEEN 90 AND 99) and race_results.DriverID =".$driverID;
$recordsetRoad = $database_connection->query($queryRoad);
$resultRoad = $recordsetRoad->fetch_assoc();
print "<td bgcolor='palegreen'>";
echo number_format($resultRoad['QualifyingAvg'], 2);
print "</td>";
print "<td bgcolor='limegreen'>";
echo number_format($resultRoad['FinishingAvg'], 2);
print "</td>";
$queryShort="SELECT avg(start) as QualifyingAvg, avg(finish) as FinishingAvg FROM race_results
INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks on tracks.ID = races.TrackID WHERE (races.TypeID BETWEEN 10 AND 39) AND race_results.DriverID =".$driverID;
$recordsetShort = $database_connection->query($queryShort);
$resultShort = $recordsetShort->fetch_assoc();
print "<td bgcolor='#dda0dd'>";
echo number_format($resultShort['QualifyingAvg'], 2);
print "</td>";
print "<td bgcolor='#dda0dd'>";
echo number_format($resultShort['FinishingAvg'], 2);
print "</td>";
$querySpeedway="SELECT avg(start) as QualifyingAvg, avg(finish) as FinishingAvg FROM race_results
INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks on tracks.ID = races.TrackID WHERE (races.TypeID BETWEEN 40 AND 69) AND race_results.DriverID =".$driverID;
$recordsetSpeedway = $database_connection->query($querySpeedway);
$resultSpeedway = $recordsetSpeedway->fetch_assoc();
print "<td bgcolor='#f08080'>";
echo number_format($resultSpeedway['QualifyingAvg'], 2);
print "</td>";
print "<td bgcolor='#f08080'>";
echo number_format($resultSpeedway['FinishingAvg'], 2);
print "</td>";
$querySuperspeedway="SELECT avg(start) as QualifyingAvg, avg(finish) as FinishingAvg FROM race_results
INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks on tracks.ID = races.TrackID WHERE (races.TypeID BETWEEN 70 AND 89) AND race_results.DriverID =".$driverID;
$recordsetSuperspeedway = $database_connection->query($querySuperspeedway);
$resultSuperspeedway = $recordsetSuperspeedway->fetch_assoc();
print "<td bgcolor='#cd5c5c'>";
echo number_format($resultSuperspeedway['QualifyingAvg'], 2);
print "</td>";
print "<td bgcolor='#cd5c5c'>";
echo number_format($resultSuperspeedway['FinishingAvg'], 2);
print "</td>";
print "</tr>";
?>
</table>
</td>
</tr>
<tr>
<td>
<h3>&nbsp;</h3>
<h3>Saisonstatistik</h3>
<table border="1" cellspacing="0">
<tr>
<th>Saison</th>
<th>Rennteilnahmen</th>
<th>Siege</th>
<th>Podiumspl&auml;tze</th>
<th>Top 5</th>
<th>Top 10</th>
<th>Poles</th>
<th>Alle F&uuml;hrungsrunden</th>
<th>Meiste F&uuml;hrungsrunden</th>
<th>Schnellste Rennrunden</th>
<th>H&ouml;chste Positionsgewinne</th>
<th>Ausf&auml;lle</th>
</tr>
<?php
include("verbindung.php");
$query0 = "SELECT championship.Saison, championship.Bezeichnung as Championship, championship.Kategorie, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, sum(race_results.Laps) as Laps, sum(race_results.Laps * races.Length) as Distanz
FROM races
INNER JOIN championship on championship.RaceID = races.ID
LEFT JOIN race_results on (race_results.RaceID = races.ID) and (race_results.Finish = 1)
LEFT JOIN tracks on races.TrackID = tracks.ID
GROUP BY championship.Saison, championship.Bezeichnung, championship.Kategorie
ORDER BY championship.Saison, championship.Bezeichnung, championship.Kategorie";
$recordset0 = $database_connection->query($query0);
while ($row = $recordset0->fetch_assoc())
{
$season = $row['Saison'];
$championship_name = $row['Championship'];
$category = $row['Kategorie'];
$events = $row['ScheduledEvents'];
$fevents = $row['FinishedEvents'];
if ($fevents > 0 and $events <> $fevents) {$ranking_color='lightgreen';}
else {$ranking_color = 'lightsalmon';}

include("verbindung.php");
$query = "SELECT COUNT(race_results.RaceID) AS Events, SUM(race_results.Finish = 1) AS Wins, SUM(race_results.Finish <= 3) AS Podiums, SUM(race_results.Finish <= 5) AS Top5, SUM(race_results.Finish <= 10) AS Top10, 
SUM(race_results.Start = 1) AS Poles, SUM(race_results.Led) AS Led, SUM(race_results.MostLapsLed) AS MLL, SUM(race_results.FastestRaceLap) AS FRL, SUM(race_results.MostPositionsGained) AS MPG, SUM(race_results.DNF) AS DNF
FROM race_results INNER JOIN races ON races.ID = race_results.RaceID INNER JOIN championship ON championship.RaceID = races.ID
WHERE (race_results.DriverID = ".$driverID.") AND (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie";
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
if ($result) {
print "<tr bgcolor='$ranking_color' align='center'>";
print "<td>";
print "<a href='driver_results.php?ID=".$ID."&Saison=".$season."&Champ=".$championship_name."'>";
print "<b>".$season.' '.$championship_name."</b>";
print "</a>";
print "</td>";
print "<td>";
echo $result['Events'];
print "</td>";
print "<td>";
if ($result['Wins'] > 0) {echo $result['Wins'];}
print "</td>";
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
if ($result['Poles'] > 0) {echo $result['Poles'];}
print "</td>";
print "<td>";
if ($result['Led'] > 0) {echo $result['Led'];}
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
print "</tr>";
}
}
?>
</table>
</td>
</tr>
<tr>
<td>
<h4>Detaillierte Fahrerstatistik</h4>
<p align = "center">
<?php
print "<a href='driver_stats.php?ID=".$ID."'>Alle Erfolge</a><br>";
print "<a href='driver_results.php?ID=".$ID."'>Alle Resultate</a><br>";
print "<a href='driver_track.php?ID=".$ID."'>Erfolge nach Rennstrecke</a><br>";
print "<a href='driver_tracktype.php?ID=".$ID."'>Erfolge nach Streckentyp</a><br>";
print "<a href='driver_track_stats.php?ID=".$ID."'>Gesamtstatistik nach Rennstrecke</a>";
?>
</p>
</td>
</tr>
</table>
</p>
<br/>
<p>
<a href='../index.php'>Zur&uuml;ck zum Index</a>
</p>
</body>
</html>
