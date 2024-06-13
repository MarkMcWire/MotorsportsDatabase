<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["Type"])) {$tracktyp = $_GET["Type"];} ELSE {$tracktyp = 0;}
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
$query0="SELECT coalesce(max(Year(Datum)),0) as maxseason, max(Datum) as maxdate 
FROM races LEFT JOIN race_results on races.ID = race_results.RaceID LEFT JOIN championship on races.ID = championship.RaceID
WHERE (race_results.Finish is NOT NULL) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')";
include("verbindung.php");
$recordset0 = $database_connection->query($query0);
$result0 = $recordset0->fetch_assoc();
$actualseason = $result0["maxseason"]+1;
$actualdate = $result0["maxdate"];

$query1 = "SELECT track_type.ID AS SortCat, track_type.Type, track_type.Surface, track_type.ColorCode 
FROM track_type LEFT JOIN races on races.TypeID = track_type.ID  LEFT JOIN championship on races.ID = championship.RaceID
WHERE (track_type.ID = '$tracktyp' or '$tracktyp' = 0) and (races.ID IS NOT NULL) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode ORDER BY track_type.ID";
//print $query1;
print '<h2>Rennstrecken nach Streckentyp</h2>';
include("verbindung.php");
$recordset1 = $database_connection->query($query1);
while ($row1 = $recordset1->fetch_assoc())
{
$track_type = $row1['Type'];
$track_color = $row1['ColorCode'];
$track_surface = $row1['Surface'];
$sortcat = $row1['SortCat'];
$query2 = "SELECT tracks.ID as ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, tracks.Eroeffnung, tracks.Schliessung, track_type.Type, track_type.Surface, track_type.ColorCode,
GROUP_CONCAT(DISTINCT races.Length, ' mi' ORDER BY year(races.Datum) Separator '<br/>') as Meilen, GROUP_CONCAT(DISTINCT ROUND(races.Length*1.60934,3), ' km' ORDER BY year(races.Datum) Separator '<br/>') as Kilometer,
max(year(races.Datum)) as Einstellung, GROUP_CONCAT(year(races.Datum), ' ', races.Event Separator '<br/>') as Event
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID LEFT JOIN race_results on race_results.RaceID = races.ID LEFT JOIN track_type on track_type.ID = races.TypeID LEFT JOIN championship on races.ID = championship.RaceID
WHERE (track_type.ID = $sortcat or $sortcat = -1) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY track_type.ID, tracks.ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, tracks.Eroeffnung, tracks.Schliessung, track_type.Type, track_type.Surface, track_type.ColorCode
ORDER BY track_type.ID, Type, Surface, Einstellung DESC, Bezeichnung";
//print $query2;
?>
<p>
<?php
print '<h2>'.$track_type.' ('.$track_surface.')</h2>';
?>
<table border="2" cellspacing="10">
<tr>
<td>
<p>
<table border="1" cellspacing="0">
<tr>
<th><a>Rennstrecke</a></th>
<th><a>Ort</a></th>
<th><a>Type</a></th>
<th colspan='2'><a>L&auml;nge</a></th>
<th><a>Oberfl&aumlche</a></th>
<th><a>Letztes Rennen</a></th>
<th colspan='1'>Statistik</th>
<th colspan='1'>Ergebnisse</th>
<th colspan='2'>Fahrer</th>
</tr>
<?php
include("verbindung.php");
$recordset2 = $database_connection->query($query2);
while ($row2 = $recordset2->fetch_assoc())
{
$track_type = $row2['Type'];
$length_miles = $row2['Meilen'];
$length_kilometer = $row2['Kilometer'];
print "<tr bgcolor =$track_color>";
print "<td>";
$trackID = $row2['ID'];
print "<a href='../tracks/track.php?ID=".$trackID."'>";
echo $row2['Bezeichnung'];
print "</a>";
print "</td>";
print "<td>";
echo $row2['Ort'].' ('.$row2['Land'].')';
print "</td>";
print "<td>";
echo $row1['Type'];
print "</td>";
print "<td>";
echo $length_miles;
print "</td>";
print "<td>";
echo $length_kilometer;
print "</td>";
print "<td>";
echo $row2['Surface'];
print "</td>";
print "<td>";
echo $row2['Einstellung'];
print "</td>";
print "<td>";
print "<a href='../tracks/track_seasonsummary.php?ID=".$trackID."'>Statistik</a>";
print "</td>";
print "<td>";
print "<a href='../tracks/track_results.php?ID=".$trackID."'>Resultate</a>";
print "</td>";
print "<td>";
print "<a href='../tracks/track_driversummary.php?ID=".$trackID."'>&Uuml;bersicht</a>";
print "</td>";
print "<td>";
print "<a href='../tracks/track_driverresults.php?ID=".$trackID."'>Platzierungen</a>";
print "</td>";
print "</tr>";
}
?>
</table>
</p>
</td>
</tr>
</table>
<?php
}
?>
<br/>
</p>
<p align="center">
<a href='../index.php'>Zur&uuml;ck zum Index</a>
</p>
</body>
</html>
