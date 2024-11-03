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
<th>Champion</th>
</tr>
<?php
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query = "SELECT championship.Saison, championship.Bezeichnung as Championship, championship.Kategorie, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, sum(races.Runden) as Laps, sum(races.Runden * races.Length) as Distanz
FROM (championship INNER JOIN races on races.ID = championship.RaceID INNER JOIN tracks on races.TrackID = tracks.ID) LEFT JOIN race_results on (race_results.RaceID = races.ID) and (race_results.Finish = 1)
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY championship.Saison, championship.Bezeichnung, championship.Kategorie ORDER BY Championship, Saison ASC";
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
print "<th>Driver</th>";
print "<th>Points</th>";
print "<th>Interval</th>";
print "</tr>";
include("verbindung.php");
$query0 = "SELECT TT.Bezeichnung, TT.Saison, TT.Kategorie, TT.DriverID, drivers.Display_Name, 
	(SUM(TT.Rennpunkte) + SUM(TT.Sprintpunkte) + SUM(TT.Stagepunkte) + SUM(TT.Qualifikationspunkte) + SUM(TT.Bonuspunkte)) AS Punkte, 
	SUM(TT.Rennpunkte) AS Rennpunkte, 
	SUM(TT.Sprintpunkte) AS Sprintpunkte, 
	SUM(TT.Stagepunkte) AS Stagepunkte, 
	SUM(TT.Bonuspunkte) AS Bonuspunkte, 
	SUM(TT.Qualifikationspunkte) AS Qualifikationspunkte, 
	GROUP_CONCAT(LPAD(TT.Finish, 2, '0') ORDER BY TT.Finish) AS Platzierungen
	FROM (
	SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, race_results.Finish AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, 0 AS Qualifikationspunkte, 0 AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID, race_results.Finish 
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, SUM(championship_scoring.Rank_Points) AS Rennpunkte, SUM(championship_scoring.Sprint_Points) AS Sprintpunkte, SUM(championship_scoring.Stage_Points) AS Stagepunkte, SUM(championship_scoring.Qualification_Points) AS Qualifikationspunkte, SUM(championship_scoring.Bonus_Points) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID LEFT JOIN championship_scoring ON championship_scoring.ChampionshipID = championship.ID AND championship_scoring.DriverID = race_results.DriverID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, 0 AS Qualifikationspunkte, SUM(penalties.Points) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID 
	LEFT JOIN penalties ON penalties.RaceID = race_results.RaceID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND (penalties.DriverID = race_results.DriverID)
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	) AS TT INNER JOIN drivers ON drivers.ID = TT.DriverID
	GROUP BY TT.Saison, TT.Bezeichnung, TT.Kategorie, TT.DriverID, drivers.Display_Name
	ORDER BY TT.Saison, TT.Bezeichnung, TT.Kategorie, Punkte DESC, Platzierungen LIMIT 2";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
	if ($i == 1) {
		$champ = $row['Display_Name'];
		$maxpoints = $row['Punkte'];
	}

	if ($i == 2) {
		$interval = $row['Punkte'] - $maxpoints;
		print"<tr bgcolor = '$track_color'>";
			print'<TD width = "50%"><FONT >'.$champ.'</FONT></TD>';
			print'<TD width = "25%"><FONT >'.$maxpoints.'</FONT></TD>';
			print'<TD width = "25%"><FONT >'.$interval.'</FONT></TD>';
		print'</TR>';
	}
}
print "</table>";
print "</td>";
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
