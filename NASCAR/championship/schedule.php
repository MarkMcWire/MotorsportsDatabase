<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<p>
<table border="2" cellspacing="10">
<tr>
<td colspan="5">
<h2>Rennkalender</h2>
</td>
</tr>
<?php
if (isset($_GET["Saison"])) {$season = $_GET["Saison"];} ELSE {$season = 0;}
if (isset($_GET["Champ"])) {$championship_name = $_GET["Champ"];} ELSE {$championship_name = 0;}
if (isset($_GET["Kategorie"])) {$category = $_GET["Kategorie"];} ELSE {$category = -1;}
if (isset($_GET["races"])) {$race_id_global = $_GET["races"];} ELSE {$race_id_global = 0;}
if ($season == NULL) {$season = 0;}
print "<tr align='center'>";
print "<td><a href='calendar.ini.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Calendar.ini erstellen</a></td>";
print "<td></td>";
print "<td><a href='calendar.csv.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Calendar.csv erstellen</a></td>";
print "<td></td>";
print "<td><a href='stage_template.csv.php?Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>Stage_Template.csv erstellen</a></td>";
print "</tr>";
include("verbindung.php");
$query = "SELECT * FROM (
SELECT championship.Saison, championship.Bezeichnung as Championship, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, sum(race_results.Laps) as Laps, sum(race_results.Laps * races.Length) as Distanz
FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN race_results on (race_results.RaceID = races.ID) and (race_results.Finish = 1) LEFT JOIN tracks on races.TrackID = tracks.ID
WHERE (championship.Saison = $season or $season = 0) and (championship.Bezeichnung = '$championship_name' or '$championship_name' = '') AND (championship.Kategorie = $category OR championship.Kategorie = 0 OR $category = -1)
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
print "<td colspan='5'>";
print "<h3 align='center'>".$season.' '.$championship_name."</h3>";
print '<p>';
print "<table border='1' cellspacing='0'>";
print "<tr>";
print "<th>ID</th>";
print "<th>Meisterschaft<br />Nummer</th>";
print"<th>Event</th>";
print "<th>Rennstrecke</th>";
print "<th>Streckentyp</th>";
print "<th>Distanz</th>";
print "<th>Wertung</th>";
print "</tr>";
include("verbindung.php");
if ($championship_name == 'TBA' or $championship_name == null) {
	$query1 = "SELECT races.ID, races.Datum, coalesce(championship.Saison, 0) as Saison, championship.Bezeichnung as Championship, races.Event, round(races.Runden * races.Length,2) as Distanz, races.Runden as Runden, tracks.ID as TrackID, tracks.Kennzeichen as StreckenKz, tracks.DirDay as DirDay, tracks.DirNight as DirNight, tracks.Bezeichnung as Rennstrecke, races.Length, races.TypeID, track_type.Type, track_type.ColorCode, championship.Race_Scoring
	FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID
	ORDER BY championship.Saison, races.Datum";
} else {
	$query1 = "SELECT races.ID, races.Datum, coalesce(championship.Saison, 0) as Saison, championship.Bezeichnung as Championship, races.Event, round(races.Runden * races.Length,2) as Distanz, races.Runden as Runden, tracks.ID as TrackID, tracks.Kennzeichen as StreckenKz, tracks.DirDay as DirDay, tracks.DirNight as DirNight, tracks.Bezeichnung as Rennstrecke, races.Length, races.TypeID, track_type.Type, track_type.ColorCode, championship.Race_Scoring
	FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (championship.Saison = $season or $season = 0) and (championship.Bezeichnung = '$championship_name' or '$championship_name' = '') AND (championship.Kategorie = $category OR championship.Kategorie = 0 OR $category = -1) and (championship.RaceID <= $race_id_global or $race_id_global = 0)
	ORDER BY championship.Saison, races.Datum";
}
$recordset1 = $database_connection->query($query1);
$track_color='lightgrey';
while ($row = $recordset1->fetch_assoc())
{
$ID = $row['ID'];
$track_type_ID = $row['TypeID'];
$track_color = $row['ColorCode'];
print "<tr bgcolor = $track_color>";
print "<td>";
print "<a href='../championship/raceresult.php?ID=".$ID."'>";
echo $row['ID'];
print "</a>";
print "</td>";
print "<td>";
print "<a href='../championship/raceresult.php?ID=".$ID."'>";
print $row['Championship'].' '.$row['Saison'];
print "</a>";
print "</td>";
print "<td>";
print "<a href='../championship/raceresult.php?ID=".$ID."'>";
echo $row['Event'];
print "</a>";
print "</td>";
print "<td>";
$trackID = $row['TrackID'];
print "<a href='../tracks/track.php?ID=".$trackID."&Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>";
echo $row['Rennstrecke'];
print "</a>";
print "</td>";
print "<td>";
$track_type = $row['Type'];
print "<a href='../tracks/tracks_tracktype.php?Type=".$track_type_ID."&Saison=".$season."&Champ=".$championship_name."&Kategorie=".$category."'>";
echo $row['Type'];
print "</a>";
print "</td>";
print "<td>";
echo $row['Distanz'];
print " Meilen";
print " (";
echo $row['Runden'];
print " Runden)";
print "</td>";
print "<td>";
echo $row['Race_Scoring'];
print "</td>";
print "</tr>";
}
print "</table>";
print '</p>';
print "</td>";
print "</tr>";
}
?>
</table>
<br/>
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
