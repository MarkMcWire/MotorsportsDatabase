<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<h2>Saisonstatistik</h2>
<p>
<table border="2" cellspacing="10">
<tr valign='top'>
<td>
<p>
<table border='1' cellspacing='0'>
<tr>
<th>Saison</th>
<th>Meisterschaft</th>
<th>Rennen nach Streckentyp</th>
<th>Rennen nach Rennl&auml;nge</th>
</tr>
<?php
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query = "SELECT championship.Saison, championship.Bezeichnung as Championship, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, sum(races.Runden) as Laps, sum(races.Runden * races.Length) as Distanz
FROM (championship INNER JOIN races on races.ID = championship.RaceID INNER JOIN tracks on races.TrackID = tracks.ID) LEFT JOIN race_results on (race_results.RaceID = races.ID) and (race_results.Finish = 1)
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY championship.Saison, championship.Bezeichnung ORDER BY Saison DESC, Championship";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
$season = $row['Saison'];
$championship_name = $row['Championship'];
$events = $row['ScheduledEvents'];
$fevents = $row['FinishedEvents'];
$laps = $row['Laps'];
$miles = $row['Distanz'];
if ($events == $fevents) {$track_color = 'darkgrey';}
else {$track_color = 'lightgrey';}
if ($championship_name == '' or $championship_name == NULL) {$track_color = 'lightgrey';}
print "<tr bgcolor = $track_color align='center'>";
print "<td>";
echo $season;
print "</td>";
print "<td>";
echo $championship_name;
print "</td>";
print "<td>";
print "<table border='1' cellspacing='0'>";
print "<tr bgcolor = $track_color align='center'>";
print "<th>Streckentyp</th>";
print "<th>Anzahl</th>";
print "<th>Distanz</th>";
print "</tr>";
include("verbindung.php");
$query0 = "SELECT track_type.Type, track_type.Surface, track_type.ColorCode, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, coalesce(sum(races.Runden),0) as Laps, coalesce(sum(races.Runden * races.Length),0) as Miles
FROM (championship INNER JOIN races on races.ID = championship.RaceID INNER JOIN tracks on races.TrackID = tracks.ID INNER JOIN track_type on track_type.ID = races.TypeID) LEFT JOIN race_results on race_results.RaceID = races.ID and race_results.Finish = 1
WHERE (championship.Saison = $season or $season = 0) and (championship.Bezeichnung = '$championship_name' or '$championship_name' = '')
GROUP BY track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode ORDER BY track_type.ID";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$track_type = $row['Type']." (".$row['Surface'].")";
$track_color1 = $row['ColorCode'];
$i = $i + 1;
print"<tr bgcolor = '$track_color1'>";
	print'<TH><FONT >'.$track_type.'</FONT></TH>';
	print'<TH><FONT >'.$row['ScheduledEvents'].' von '.$events.'</FONT></TH>';
    print'<TD><FONT >'.$row['Miles'].' Meilen ('.$row['Laps'].' Runden)'.'</FONT></TD>';
print'</TR>';
}
print "</table>";
print "</td>";
print "<td>";
print "<table border='1' cellspacing='0'>";
print "<tr bgcolor = $track_color align='center'>";
print "<th>Länge</th>";
print "<th>Anzahl</th>";
print "<th>Distanz</th>";
print "</tr>";
include("verbindung.php");
$query0 = "SELECT IF(Round(races.Runden * races.Length, 2) >= 95, Round(races.Runden * races.Length, -2), Round(races.Runden * races.Length, -1)) as Distanz, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, coalesce(sum(races.Runden), 0) as Laps, coalesce(sum(races.Runden * races.Length), 0) as Miles
FROM (championship INNER JOIN races on races.ID = championship.RaceID INNER JOIN tracks on races.TrackID = tracks.ID INNER JOIN track_type on track_type.ID = races.TypeID) LEFT JOIN race_results on race_results.RaceID = races.ID and race_results.Finish = 1 
WHERE (championship.Saison = $season or $season = 0) and (championship.Bezeichnung = '$championship_name' or '$championship_name' = '')
GROUP BY IF(Round(races.Runden * races.Length, 2) >= 95, Round(races.Runden * races.Length, -2), Round(races.Runden * races.Length, -1)) ORDER BY IF(Round(races.Runden * races.Length, 2) >= 95, Round(races.Runden * races.Length, -2), Round(races.Runden * races.Length, -1))";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
print"<tr bgcolor = '$track_color'>";
	print'<TH><FONT >'.$row['Distanz'].'</FONT></TH>';
	print'<TH><FONT >'.$row['ScheduledEvents'].' von '.$events.'</FONT></TH>';
	print'<TD><FONT >'.$row['Miles'].' Meilen ('.$row['Laps'].' Runden)'.'</FONT></TD>';
print'</TR>';
}
print "</table>";
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
<br/>
<p align="center">
<a href='../index.php'>Zur&uuml;ck zum Index</a>
</p>
</body>
</html>
