<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Calendar File for NASCAR Racing 2003</title>
</head>
<body>
<p align="left">
<?php
if (isset($_GET["Saison"])) {$season = $_GET["Saison"];} ELSE {$season = 0;}
if (isset($_GET["Champ"])) {$championship_name = $_GET["Champ"];} ELSE {$championship_name = 0;}
if (isset($_GET["Kategorie"])) {$category = $_GET["Kategorie"];} ELSE {$category = -1;}
if (isset($_GET["races"])) {$race_id_global = $_GET["races"];} ELSE {$race_id_global = 0;}
if ($season == NULL) {$season = 0;}
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
include("verbindung.php");
if ($championship_name == 'TBA' or $championship_name == null) {
	$query1 = "SELECT races.ID, races.Datum as Datum, coalesce(championship.Saison, 0) as Saison, championship.Bezeichnung as Championship, races.Event, round(races.Runden * races.Length,2) as Distanz, races.Runden as Runden, tracks.ID as TrackID, tracks.Kennzeichen as StreckenKz, tracks.DirDay as DirDay, tracks.DirNight as DirNight, tracks.Bezeichnung as Rennstrecke, races.Length, track_type.Type
	FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID
	ORDER BY championship.Saison, races.Datum";
} else {
	$query1 = "SELECT races.ID, races.Datum as Datum, coalesce(championship.Saison, 0) as Saison, championship.Bezeichnung as Championship, races.Event, round(races.Runden * races.Length,2) as Distanz, races.Runden as Runden, tracks.ID as TrackID, tracks.Kennzeichen as StreckenKz, tracks.DirDay as DirDay, tracks.DirNight as DirNight, tracks.Bezeichnung as Rennstrecke, races.Length, track_type.Type
	FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (championship.Saison = $season or $season = 0) and (championship.Bezeichnung = '$championship_name' or '$championship_name' = '') AND (championship.Kategorie = $category OR championship.Kategorie = 0 OR $category = -1) and (championship.RaceID <= $race_id_global or $race_id_global = 0)
	ORDER BY championship.Saison, races.Datum";
}
$recordset1 = $database_connection->query($query1);
$i = 0;
while ($row = $recordset1->fetch_assoc())
{
$i = $i + 1;
$ID = $row['ID'];
$track_type = $row['Type'];
$nummer = $i;
$event = $row['Event'];
$laps = $row['Runden'];
$track_name = $row['Rennstrecke'];
$trackID = $row['TrackID'];
$track_abbreviation = $row['StreckenKz'];
$trackDirectoryD = $row['DirDay'];
$trackDirectoryN = $row['DirNight'];
$startingGrid = 1;
$day = date('d',strtotime($row['Datum']));
$month = date('m',strtotime($row['Datum']));
$year = date('Y',strtotime($row['Datum']));
print "<br />";
print "[ Event".$nummer." ]";
print "<br />";
print "day = ".$day;
print "<br />";
print "month = ".$month;
print "<br />";
print "name = ".$event;
print "<br />";
print "numberOfLaps = ".$laps;
print "<br />";
print "track = ".$track_name;
if ($trackDirectoryD) {
print "<br />";
print "trackDirectory = ".$trackDirectoryD;
}
if ($trackDirectoryN) {
print "<br />";
print "trackDirectory = ".$trackDirectoryN;
}
print "<br />";
print "startingGrid = ".$startingGrid;
print "<br />";
}
print "<br />";
print "[ Season ]";
print "<br />";
print "name = ".$year.' '.$championship_name;
print "<br />";
print "numEvents = ".$i;
print "<br />";
print "year = ".$year;
print "<br />";
}
?>
</p>
</body>
</html>
