<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
$query0="SELECT coalesce(max(Year(Datum)),0) as maxseason, max(Datum) as maxdate FROM races LEFT JOIN race_results on races.ID = race_results.RaceID WHERE race_results.Finish is NOT NULL";
include("verbindung.php");
$recordset0 = $database_connection->query($query0);
$result0 = $recordset0->fetch_assoc();
$actualseason = $result0["maxseason"]+1;
$actualdate = $result0["maxdate"];
$query = "SELECT tracks.ID as ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, track_type.ID AS SortCat, track_type.Type, track_type.Surface, track_type.ColorCode,
GROUP_CONCAT(DISTINCT races.Length, ' mi' ORDER BY year(races.Datum) Separator '<br/>') as Meilen, GROUP_CONCAT(DISTINCT ROUND(races.Length*1.60934,3), ' km' ORDER BY year(races.Datum) Separator '<br/>') as Kilometer,
max(year(races.Datum)) as Einstellung, GROUP_CONCAT(year(races.Datum), ' ', races.Event ORDER BY year(races.Datum), races.Event Separator '<br/>') as Event
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID LEFT JOIN race_results on race_results.RaceID = races.ID
WHERE races.ID is not NULL and race_results.Finish is NULL
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode
ORDER BY Event, tracks.Bezeichnung";

$query2 = "SELECT tracks.ID as ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, track_type.ID AS SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, 
GROUP_CONCAT(DISTINCT races.Length, ' mi' ORDER BY year(races.Datum) Separator '<br/>') as Meilen, GROUP_CONCAT(DISTINCT ROUND(races.Length*1.60934,3), ' km' ORDER BY year(races.Datum) Separator '<br/>') as Kilometer,
min(year(races.Datum)) as Eroeffnung, max(year(races.Datum)) as Einstellung
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID LEFT JOIN race_results on race_results.RaceID = races.ID
WHERE (track_type.ID BETWEEN 10 and 89)
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, track_type.ID, track_type.Surface, track_type.Type, track_type.ColorCode
HAVING (DATEDIFF('$actualdate', max(races.Datum))>=0)
ORDER BY Einstellung DESC, Eroeffnung DESC, Bezeichnung";

$query3 = "SELECT tracks.ID as ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, track_type.ID AS SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, 
GROUP_CONCAT(DISTINCT races.Length, ' mi' ORDER BY year(races.Datum) Separator '<br/>') as Meilen, GROUP_CONCAT(DISTINCT ROUND(races.Length*1.60934,3), ' km' ORDER BY year(races.Datum) Separator '<br/>') as Kilometer,
min(year(races.Datum)) as Eroeffnung, max(year(races.Datum)) as Einstellung
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID LEFT JOIN race_results on race_results.RaceID = races.ID
WHERE (track_type.ID BETWEEN 90 and 94)
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode
HAVING (DATEDIFF('$actualdate', max(races.Datum))>=0)
ORDER BY Einstellung DESC, Eroeffnung DESC, Bezeichnung";

$query4 = "SELECT tracks.ID as ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, track_type.ID AS SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, 
GROUP_CONCAT(DISTINCT races.Length, ' mi' ORDER BY year(races.Datum) Separator '<br/>') as Meilen, GROUP_CONCAT(DISTINCT ROUND(races.Length*1.60934,3), ' km' ORDER BY year(races.Datum) Separator '<br/>') as Kilometer,
min(year(races.Datum)) as Eroeffnung, max(year(races.Datum)) as Einstellung
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID LEFT JOIN race_results on race_results.RaceID = races.ID
WHERE (track_type.ID BETWEEN 95 and 99)
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode
HAVING (DATEDIFF('$actualdate', max(races.Datum))>=0)
ORDER BY Einstellung DESC, Eroeffnung DESC, Bezeichnung";

$query5 = "SELECT tracks.ID as ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, 0 AS SortCat, '' as Type, '' as Surface, 0 as Meilen, 0 as Kilometer, 'white' AS ColorCode
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID
WHERE races.ID is NULL GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Ort, tracks.Land ORDER BY ID";

$query1 = $query;
if (isset($_GET["Sort"])) {$sort = $_GET["Sort"];} ELSE {$sort='';}
switch ($sort) {
    case "Name":
		$query1 = $query." ORDER BY Bezeichnung";
		break;
    case "Type":
		$query1 = $query."  ORDER BY Type, Bezeichnung";
		break;
	case "Length":
		$query1 = $query."  ORDER BY Length, Bezeichnung";
		break;
	case "Event":
		$query1 = $query."  ORDER BY Event, Bezeichnung";
		break;
	case "Ort":
		$query1 = $query."  ORDER BY Land, Bezeichnung";
		break;
}
// print $query;
print '<h2>Rennstrecken&uuml;bersicht</h2>';
print '<h3>'.$actualseason.'</h3>';
?>
<h4>Strecken mit ausstehenden Rennen</h4>
<p>
<table border="2" cellspacing="10">
<tr>
<td>
<p>
<table border="1" cellspacing="0">
<tr>
<th><a href='?Sort=Name'>Rennstrecke</a></th>
<th><a href='?Sort=Ort'>Ort</a></th>
<th><a href='?Sort=Type'>Type</a></th>
<th colspan='2'><a href='?Sort=Length'>L&auml;nge</a></th>
<th><a href='?Sort=Event'>Ausstehende<br/>Rennen</a></th>
<th colspan='1'>Statistik</th>
<th colspan='1'>Ergebnisse</th>
<th colspan='2'>Fahrer</th>
</tr>
<?php
$track_color='lightgrey';
include("verbindung.php");
$recordset1 = $database_connection->query($query1);
while ($row1 = $recordset1->fetch_assoc())
{
$track_type_ID = $row1['SortCat'];
$track_type = $row1['Type'];
$track_color = $row1['ColorCode'];
$length_miles = $row1['Meilen'];
$length_kilometer = $row1['Kilometer'];
print "<tr bgcolor =$track_color>";
print "<td>";
$trackID = $row1['ID'];
print "<a href='../tracks/track.php?ID=".$trackID."'>";
echo $row1['Bezeichnung'];
print "</a>";
print "</td>";
print "<td>";
echo $row1['Ort'].' ('.$row1['Land'].')';
print "</td>";
print "<td>";
print "<a href='../tracks/tracks_tracktype.php?Type=".$track_type_ID."'>";
echo $row1['Type'];
print "</a>";
print "</td>";
print "<td>";
echo $length_miles;
print "</td>";
print "<td>";
echo $length_kilometer;
print "</td>";
print "<td>";
echo $row1['Event'];
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
<br/>
<h4>Oval Tracks</h4>
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
<th colspan='2'><a>L&auml;nge</a></th>
<th><a>Erstes Rennen</a></th>
<th><a>Letztes Rennen</a></th>
<th colspan='1'>Statistik</th>
<th colspan='1'>Ergebnisse</th>
<th colspan='3'>Fahrer</th>
</tr>
<?php
$track_color='lightgrey';
include("verbindung.php");
$recordset2 = $database_connection->query($query2);
while ($row2 = $recordset2->fetch_assoc())
{
$track_type_ID = $row2['SortCat'];
$track_type = $row2['Type'];
$track_color = $row2['ColorCode'];
$length_miles = $row2['Meilen'];
$length_kilometer = $row2['Kilometer'];
print "<tr bgcolor = $track_color>";
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
print "<a href='../tracks/tracks_tracktype.php?Type=".$track_type_ID."'>";
echo $row2['Type'];
print "</a>";
print "</td>";
print "<td>";
echo $length_miles;
print "</td>";
print "<td>";
echo $length_kilometer;
print "</td>";
print "<td>";
echo $row2['Eroeffnung'];
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
print "<td>";
print "<a href='../tracks/track_driverpoints.php?ID=".$trackID."'>Punkte</a>";
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
<h4>Permanent Road Courses</h4>
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
<th colspan='2'><a>L&auml;nge</a></th>
<th><a>Erstes Rennen</a></th>
<th><a>Letztes Rennen</a></th>
<th colspan='1'>Statistik</th>
<th colspan='1'>Ergebnisse</th>
<th colspan='3'>Fahrer</th>
</tr>
<?php
$track_color='lightgrey';
include("verbindung.php");
$recordset3 = $database_connection->query($query3);
while ($row3 = $recordset3->fetch_assoc())
{
$track_type_ID = $row3['SortCat'];
$track_type = $row3['Type'];
$track_color = $row3['ColorCode'];
$length_miles = $row3['Meilen'];
$length_kilometer = $row3['Kilometer'];
print "<tr bgcolor = $track_color>";
print "<td>";
$trackID = $row3['ID'];
print "<a href='../tracks/track.php?ID=".$trackID."'>";
echo $row3['Bezeichnung'];
print "</a>";
print "</td>";
print "<td>";
echo $row3['Ort'].' ('.$row3['Land'].')';
print "</td>";
print "<td>";
print "<a href='../tracks/tracks_tracktype.php?Type=".$track_type_ID."'>";
echo $row3['Type'];
print "</a>";
print "</td>";
print "<td>";
echo $length_miles;
print "</td>";
print "<td>";
echo $length_kilometer;
print "</td>";
print "<td>";
echo $row3['Eroeffnung'];
print "</td>";
print "<td>";
echo $row3['Einstellung'];
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
print "<td>";
print "<a href='../tracks/track_driverpoints.php?ID=".$trackID."'>Punkte</a>";
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
<h4>Temporary Street Circuits</h4>
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
<th colspan='2'><a>L&auml;nge</a></th>
<th><a>Erstes Rennen</a></th>
<th><a>Letztes Rennen</a></th>
<th colspan='1'>Statistik</th>
<th colspan='1'>Ergebnisse</th>
<th colspan='3'>Fahrer</th>
</tr>
<?php
$track_color='lightgrey';
include("verbindung.php");
$recordset4 = $database_connection->query($query4);
while ($row4 = $recordset4->fetch_assoc())
{
$track_type_ID = $row4['SortCat'];
$track_type = $row4['Type'];
$track_color = $row4['ColorCode'];
$length_miles = $row4['Meilen'];
$length_kilometer = $row4['Kilometer'];
print "<tr bgcolor = $track_color>";
print "<td>";
$trackID = $row4['ID'];
print "<a href='../tracks/track.php?ID=".$trackID."'>";
echo $row4['Bezeichnung'];
print "</a>";
print "</td>";
print "<td>";
echo $row4['Ort'].' ('.$row4['Land'].')';
print "</td>";
print "<td>";
print "<a href='../tracks/tracks_tracktype.php?Type=".$track_type_ID."'>";
echo $row4['Type'];
print "</a>";
print "</td>";
print "<td>";
echo $length_miles;
print "</td>";
print "<td>";
echo $length_kilometer;
print "</td>";
print "<td>";
echo $row4['Eroeffnung'];
print "</td>";
print "<td>";
echo $row4['Einstellung'];
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
print "<td>";
print "<a href='../tracks/track_driverpoints.php?ID=".$trackID."'>Punkte</a>";
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
<h4>Strecken ohne Rennen</h4>
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
<th colspan='2'><a>L&auml;nge</a></th>
</tr>
<?php
$track_color='lightgrey';
include("verbindung.php");
$recordset5 = $database_connection->query($query5);
while ($row5 = $recordset5->fetch_assoc())
{
$track_type_ID = $row5['SortCat'];
$track_type = $row5['Type'];
$track_color = $row5['ColorCode'];
$length_miles = $row5['Meilen'];
$length_kilometer = $row5['Kilometer'];
print "<tr bgcolor = $track_color>";
print "<td>";
$trackID = $row5['ID'];
print "<a href='../tracks/track.php?ID=".$trackID."'>";
echo $row5['Bezeichnung'];
print "</a>";
print "</td>";
print "<td>";
echo $row5['Ort'].' ('.$row5['Land'].')';
print "</td>";
print "<td>";
print "<a href='../tracks/tracks_tracktype.php?Type=".$track_type_ID."'>";
echo $row5['Type'];
print "</a>";;
print "</td>";
print "<td>";
echo $length_miles.' mi';
print "</td>";
print "<td>";
echo $length_kilometer.' km';
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
</p>
<p align="center">
<a href='../index.php'>Zur&uuml;ck zum Index</a>
</p>
</body>
</html>
