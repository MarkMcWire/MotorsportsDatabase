<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["Champ"])) {$championship_name = $_GET["Champ"];} ELSE {$championship_name = '';}
$query = "SELECT tracks.ID as ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, track_type.ID AS SortCat, track_type.Type, track_type.ColorCode, GROUP_CONCAT(DISTINCT races.Length, ' mi' ORDER BY races.Datum SEPARATOR '<br/>') as Length,
min(year(races.Datum)) as Erstes, max(year(races.Datum)) as Letztes, count(Distinct championship.RaceID) as Anzahl
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID LEFT JOIN race_results on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID LEFT JOIN track_type on track_type.ID = races.TypeID
WHERE race_results.Finish is not NULL and championship.Bezeichnung like '$championship_name'
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, track_type.ID, track_type.Type, track_type.ColorCode
ORDER BY Letztes DESC, Erstes DESC, Anzahl DESC, Bezeichnung ASC";
print '<h3>Rennstrecken&uuml;bersicht</h3>';
?>
<p>
<table border="2" cellspacing="10">
<tr>
<td>
<p>
<table border="1" cellspacing="0">
<tr>
<th><a>Rennstrecke</a></th>
<th><a>Ort</a></th>
<th><a>Type</a></th>
<th><a>L&auml;nge</a></th>
<th><a>Erstes Rennen</a></th>
<th><a>Letztes Rennen</a></th>
<th colspan='1'>Anzahl<br/>der<br/>Rennen</th>
<th colspan='2'>Allgemein</th>
<th colspan='2'>Fahrer</th>
</tr>
<?php
$track_color = 'lightgrey';
include("verbindung.php");
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
$track_type_ID = $row['SortCat'];
$track_type = $row['Type'];
$track_color = $row['ColorCode'];
print "<tr bgcolor = $track_color>";
print "<td>";
$trackID = $row['ID'];
print "<a href='../tracks/track.php?ID=".$trackID."&Champ=".$championship_name."'>";
echo $row['Bezeichnung'];
print "</a>";
print "</td>";
print "<td>";
echo $row['Ort'].' ('.$row['Land'].')';
print "</td>";
print "<td>";
print "<a href='../tracks/tracks_tracktype.php?Type=".$track_type_ID."&Champ=".$championship_name."'>";
echo $row['Type'];
print "</a>";
print "</td>";
print "<td>";
echo $row['Length'];
print "</td>";
print "<td>";
echo $row['Erstes'];
print "</td>";
print "<td>";
echo $row['Letztes'];
print "</td>";
print "<td>";
echo $row['Anzahl'];
print "</td>";
print "<td>";
print "<a href='../tracks/track_seasonsummary.php?ID=".$trackID."&Champ=".$championship_name."'>Statistik</a>";
print "</td>";
print "<td>";
print "<a href='../tracks/track_results.php?ID=".$trackID."&Champ=".$championship_name."'>Resultate</a>";
print "</td>";
print "<td>";
print "<a href='../tracks/track_driversummary.php?ID=".$trackID."&Champ=".$championship_name."'>Fahrer&uuml;bersicht</a>";
print "</td>";
print "<td>";
print "<a href='../tracks/track_driverresults.php?ID=".$trackID."&Champ=".$championship_name."'>Fahrerplatzierungen</a>";
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
