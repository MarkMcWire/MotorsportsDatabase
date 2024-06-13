<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<h2>Saison&uuml;bersicht</h2>
<p>
<table border="2" cellspacing="10">
<tr>
<td>
<p>
<table border="1" cellspacing="0">
<tr>
<th>Saison</th>
<th>Meisterschaft</th>
<th>Anzahl der Rennen</th>
<th>Distanz</th>
<th colspan='1'>Rennen</th>
<th colspan='1'>Teilnehmer</th>
</tr>
<?php
include("verbindung.php");
$query = "SELECT championship.Saison, championship.Bezeichnung as Championship, championship.Kategorie, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, sum(race_results.Laps) as Laps, sum(race_results.Laps * races.Length) as Distanz
FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN race_results on (race_results.RaceID = races.ID) and (race_results.Finish = 1) LEFT JOIN tracks on races.TrackID = tracks.ID
GROUP BY championship.Saison, championship.Bezeichnung, championship.Kategorie ORDER BY Saison DESC, Championship, Kategorie";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
$season = $row['Saison'];
$championship_name = $row['Championship'];
$category = $row['Kategorie'];
$events = $row['ScheduledEvents'];
$fevents = $row['FinishedEvents'];
$laps = $row['Laps'];
$miles = $row['Distanz'];
if ($events == $fevents) {$track_color = 'darkgrey';}
elseif ($fevents > 0) {$track_color = 'lightgreen';}
elseif ($fevents == 0 and $events <> $fevents) {$track_color = 'salmon';}
elseif ($fevents == 0 and $events == $fevents) {$track_color = 'yellow';}
else {$track_color = 'lightgrey';}
if ($championship_name == '' or $championship_name == NULL) {$track_color = 'lightgrey';}
if ($championship_name <> 'TBA') {
print "<tr bgcolor = $track_color align='center'>";
print "<td>";
print "<a href='schedule.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>".$season."</a>";
print "</td>";
print "<td>";
print "<a href='schedule.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>".$championship_name."</a>";
print "</td>";
print "<td>";
print $fevents.' von '.$events;
print "</td>";
print "<td>";
print $miles.' Meilen ('.$laps.' Runden)';
print "</td>";
print "<td>";
print "<a href='schedule.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Saisonraces<br />".$championship_name.' '.$season."</a>";
print "</td>";
print "<td>";
print "</td>";
print "</tr>";
}
if ($championship_name == 'TBA') {
print "<tr bgcolor = $track_color align='center'>";
print "<td>";
echo $season;
print "</td>";
print "<td>";
echo $championship_name;
print "</td>";
print "<td>";
print $fevents.' von '.$events;
print "</td>";
print "<td>";
print $miles.' Meilen ('.$laps.' Runden)';
print "</td>";
print "<td colspan='2'>";
print "</td>";
print "</tr>";
}
}
?>
</table>
</p>
</td>
</tr>
</table>
<br/>
</p>
<p align="center">
<a href='../index.php'>Zur&uuml;ck zum Index</a>
</p>
</body>
</html>
