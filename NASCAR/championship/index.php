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
<tr valign='top'>
<td>
<p>
<table border='1' cellspacing='0'>
<tr>
<th>Saison</th>
<th>Meisterschaft</th>
<th>Rennen</th>
<th>Distanz</th>
<th colspan='1'>Ergebnisse</th>
<th colspan='4'>Fahrer</th>
</tr>
<?php
include("verbindung.php");
$query = "SELECT championship.Saison, championship.Bezeichnung as Championship, championship.Kategorie, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, sum(race_results.Laps) as Laps, sum(race_results.Laps * races.Length) as Distanz
FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN race_results on (race_results.RaceID = races.ID) and (race_results.Finish = 1) LEFT JOIN tracks on races.TrackID = tracks.ID
GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie ORDER BY Championship, Saison DESC";
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
if ($events == $fevents) {$race_color = 'darkgrey';}
elseif ($fevents > 0) {$race_color = 'lightgreen';}
else {$race_color = 'lightgrey';}
print "<tr bgcolor = $race_color align='center'>";
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
print "<a href='results.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Rennergebnisse<br />".$championship_name.' '.$season."</a>";
print "</td>";
print "<td>";
print "<a href='driversummary.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>&Uuml;bersicht<br />".$championship_name.' '.$season."</a>";
print "</td>";
print "<td>";
print "<a href='driverresults.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Platzierungen<br />".$championship_name.' '.$season."</a>";
print "</td>";
print "<td>";
print "<a href='driversummary_points.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Punkte&uuml;bersicht<br />".$championship_name.' '.$season."</a>";
print "</td>";
print "<td>";
print "<a href='driverpoints.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Punkteverlauf<br />".$championship_name.' '.$season."</a>";
print "</td>";
print "</tr>";
}
?>
</table>
</p>
</td>
</tr>
</table>
<br/>
<p align="center">
<a href='../index.php'>Zur&uuml;ck zum Index</a>
</p>
</p>
</body>
</html>
