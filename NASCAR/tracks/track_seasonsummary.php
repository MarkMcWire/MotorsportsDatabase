<!DOCTYPE html>
<html lang="en">
<HEAD>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</HEAD>
<BODY bgcolor="#FFFFFF" text="#000000" link="#0000FF" vlink="#FF00FF" alink="#FF0000">
<FONT face="Times New Roman">
<HR>
<H1>Motorsport Statistik</H1>
<HR>
<H2>Streckenstatistik nach Saison</H2>
<?php
if (isset($_GET["ID"])) {$trackID = $_GET["ID"];} ELSE {$trackID = 0;}
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query = "SELECT tracks.ID as TrackID, tracks.Bezeichnung as Bezeichnung, tracks.Kennzeichen as StreckenKz
FROM tracks INNER JOIN races on races.TrackID = tracks.ID INNER JOIN championship on races.ID = championship.RaceID INNER JOIN race_results on race_results.RaceID = races.ID
WHERE (tracks.ID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (race_results.Finish = 1)
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Kennzeichen
ORDER BY tracks.Bezeichnung";
$recordset = $database_connection->query($query);
while ($result = $recordset->fetch_assoc())
{
$trackID = $result['TrackID'];
$track_name = $result['Bezeichnung'];
include("verbindung.php");
$query0 = "SELECT coalesce(count(distinct race_results.RaceID),0) as Events, coalesce(sum(race_results.Laps),0) as Laps, coalesce(sum(race_results.Laps * races.Length),0) as Miles
FROM tracks INNER JOIN races on races.TrackID = tracks.ID INNER JOIN championship on races.ID = championship.RaceID INNER JOIN race_results on race_results.RaceID = races.ID
WHERE (races.TrackID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (race_results.Finish = 1)";
$recordset0 = $database_connection->query($query0);
$result0 = $recordset0->fetch_assoc();
$events = $result0['Events'];
print'<H3>'.$track_name.'</H3>';
print '<TABLE border=1 cellpadding=3 cellspacing=0>';
print '<TR>';
	print '<TH align="left"><FONT >Saison</FONT></TH>';
	print '<TH align="left"><FONT >Serie</FONT></TH>';
	print '<TH align="left"><FONT >Anzahl der Rennen</FONT></TH>';
	print '<TH align="left"><FONT >Streckenlänge</FONT></TH>';
	print '<TH><FONT >Geplante Rundenzahl</FONT></TH>';
	print '<TH><FONT >Geplante Distanz</FONT></TH>';
	print '<TH><FONT >Gefahrene Rundenzahl</FONT></TH>';
	print '<TH><FONT >Gefahrene Distanz</FONT></TH>';
print '</TR>';
include("verbindung.php");
$query1 = "SELECT championship.Saison, championship.Bezeichnung as Championship, count(races.ID) as ScheduledEvents, count(race_results.Finish) as FinishedEvents, sum(race_results.Laps) as Laps, sum(race_results.Laps * races.Length) as Distanz
FROM tracks INNER JOIN races on races.TrackID = tracks.ID INNER JOIN championship on championship.RaceID = races.ID INNER JOIN race_results on race_results.RaceID = races.ID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') and (race_results.Finish = 1)
GROUP BY championship.Saison, championship.Bezeichnung ORDER BY Saison, Championship";
$recordset1 = $database_connection->query($query1);
while ($result1 = $recordset1->fetch_assoc())
{
$season = $result1['Saison'];
$championship_name = $result1['Championship'];
include("verbindung.php");
$query2 = "SELECT coalesce(count(distinct race_results.RaceID),0) as Events, coalesce(sum(races.Runden), 0) as ScheduledLaps, round(coalesce(sum(races.Runden * races.Length), 0), 2) as ScheduledDistanceMi, round(coalesce(sum(races.Runden * races.Length * 1.60934), 0), 2) as ScheduledDistanceKm, 
coalesce(sum(race_results.Laps),0) as CompletedLaps, round(coalesce(sum(race_results.Laps * races.Length), 0), 2) as CompletedDistanceMi, round(coalesce(sum(race_results.Laps * races.Length * 1.60934), 0), 2) as CompletedDistanceKm, 
round(races.Length, 3) as TrackLengthMi, round(races.Length * 1.60934, 3) as TrackLengthKm, track_type.ColorCode
FROM tracks INNER JOIN races on races.TrackID = tracks.ID INNER JOIN championship on races.ID = championship.RaceID INNER JOIN race_results on race_results.RaceID = races.ID INNER JOIN track_type on track_type.ID = races.TypeID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Saison = $season or $season = 0) and (championship.Bezeichnung = '$championship_name' or '$championship_name' = '') and (race_results.Finish = 1)
GROUP BY races.length, track_type.ID, track_type.ColorCode
HAVING (Events > 0)";
//print $query2;
$recordset2 = $database_connection->query($query2);
$i = 0;
while ($row = $recordset2->fetch_assoc())
{
$i = $i + 1;
$track_color = $row['ColorCode'];
print"<tr bgcolor = '$track_color'>";
	print'<TH><FONT >'.$season.'</FONT></TH>';
	print'<TH><FONT >'.$championship_name.'</FONT></TH>';
	print'<TD><FONT >'.$row['Events'].'</FONT></TD>';
	print'<TD><FONT >'.$row['TrackLengthMi'].' mi ('.$row['TrackLengthKm'].' km)</FONT></TD>';
	print'<TD><FONT >'.$row['ScheduledLaps'].'</FONT></TD>';
	print'<TD><FONT >'.$row['ScheduledDistanceMi'].' mi ('.$row['ScheduledDistanceKm'].' km)</FONT></TD>';
	print'<TD><FONT >'.$row['CompletedLaps'].'</FONT></TD>';
	print'<TD><FONT >'.$row['CompletedDistanceMi'].' mi ('.$row['CompletedDistanceKm'].' km)</FONT></TD>';
print'</TR>';
}
}
print '</TABLE>';
}
?>
<?php
print '<p>';
print '</p>';
print '<p align="center">';
print "<a href='?Champ=".$championship_name_global."&ID=".($trackID - 1)."'>Vorherige Strecke</a>";
print "&nbsp&nbsp&nbsp&nbsp&nbsp;";
print "<a href='../index.php'>Zur&uuml;ck zum Index</a>";
print "&nbsp&nbsp&nbsp&nbsp&nbsp;";
print "<a href='?Champ=".$championship_name_global."&ID=".($trackID + 1)."'>Nachfolgende Strecke</a>";
print '</p>';
?>
</FONT>
</BODY>
</HTML>
