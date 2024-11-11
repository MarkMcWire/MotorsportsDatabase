<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<p>
<h3>Saison&uuml;bersicht</h3>
<table border="2" cellspacing="10">
<tr valign='top'>
<td>
<p>
<table border='1' cellspacing='0'>
<tr>
<th>Saison</th>
<th>Rennen</th>
<th>Distanz</th>
<th colspan='1'>Ergebnisse</th>
<th colspan='5'>Fahrer</th>
</tr>
<?php
if (isset($_GET["Champ"])) {$championship_name = $_GET["Champ"];} ELSE {$championship_name = $championship_name_global;}
$query = "SELECT * FROM
(
SELECT championship.Saison, championship.Bezeichnung as Championship, championship.Kategorie, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, sum(race_results.Laps) as Laps, sum(race_results.Laps * races.Length) as Distanz
FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN race_results on (race_results.RaceID = races.ID) and (race_results.Finish = 1) LEFT JOIN tracks on races.TrackID = tracks.ID
WHERE (championship.Kategorie > 0) and (championship.Bezeichnung like '$championship_name')
GROUP BY championship.Saison, championship.Bezeichnung, championship.Kategorie
) as temp
ORDER BY Saison DESC, Championship ASC";
include("verbindung.php");
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
if ($events == $fevents) {$championship_color = 'darkgrey';}
elseif ($fevents > 0) {$championship_color = 'lightgreen';}
else {$championship_color = 'lightgrey';}
print "<tr bgcolor = $championship_color align='center'>";
print "<td><a href='championship/schedule.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>";
echo $season;
print "</a></td>";
print "<td>";
print $fevents.' von '.$events;
print "</td>";
print "<td>";
print $miles.' Meilen ('.$laps.' Runden)';
print "</td>";
print "<td>";
print "<a href='championship/results.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Resultate ".$season."</a>";
print "</td>";
print "<td>";
print "<a href='championship/driversummary.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>&Uuml;bersicht ".$season."</a>";
print "</td>";
print "<td>";
print "<a href='championship/driverresults.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Platzierungen ".$season."</a>";
print "</td>";
print "<td>";
print "<a href='championship/driverpositions.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Positions&uuml;bersicht ".$season."</a>";
print "</td>";
print "<td>";
print "<a href='championship/driversummary_points.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Punkte&uuml;bersicht ".$season."</a>";
print "</td>";
print "<td>";
print "<a href='championship/driverpoints.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Punkteverlauf ".$season."</a>";
print "</td>";
print "</tr>";
}
?>
</table>
</p>
</td>
</tr>
</table>
</p>
</body>
</html>
