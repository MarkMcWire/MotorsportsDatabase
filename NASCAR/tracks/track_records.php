<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<p>
<table border="2" cellspacing="10">
<?php
if (isset($_GET["ID"])) {$trackID = $_GET["ID"];} ELSE {$trackID = 0;}
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query = "SELECT tracks.ID as TrackID, tracks.Bezeichnung as Bezeichnung, tracks.Ort as Ort, tracks.Land as Land, tracks.Kennzeichen as StreckenKz, tracks.Eroeffnung as Eroeffnung, tracks.Schliessung as Einstellung, MAX(races.Length) as Length, track_type.ColorCode
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID LEFT JOIN championship on races.ID = championship.RaceID
WHERE (tracks.ID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, tracks.Kennzeichen, tracks.Eroeffnung, tracks.Schliessung, track_type.ID, track_type.ColorCode
ORDER BY tracks.Bezeichnung, track_type.ID DESC";
$recordset = $database_connection->query($query);
if ($result = $recordset->fetch_assoc())
{
$trackID = $result['TrackID'];
$track_name = $result['Bezeichnung'];
$opening = $result['Eroeffnung'];
$closing = $result['Einstellung'];
$track_color = $result['ColorCode'];
print "<h3 align='center'>".$track_name."</h3>";
print "<h4 align='center'>".$opening." – ".$closing."</h4>";
}
print '<tr bgcolor ='.$track_color.'>';
?>
<td colspan="1"><h4>Meiste Renn-Siege (absolut)</h4></td>
<td colspan="1"><h4>Meiste Sprint-Siege (absolut)</h4></td>
<td colspan="1"><h4>Meiste Stage-Siege (absolut)</h4></td>
<td colspan="1"><h4>Meiste Poles (absolut)</h4></td>
<td colspan="1"><h4>Meiste FRL</h4></td>
<td colspan="1"><h4>Meiste MLL</h4></td>
<td colspan="1"><h4>Meiste MPG</h4></td>
</tr>
<?php
print '<tr bgcolor ='.$track_color.'>';
?>
<td>
<table border = "0">
<?php
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1)
GROUP BY drivers.Display_Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct sprint_results.RaceID) as Siege
FROM (sprint_results INNER JOIN drivers on drivers.ID = sprint_results.DriverID INNER JOIN championship ON championship.RaceID = sprint_results.RaceID) INNER JOIN races on races.ID = sprint_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1)
GROUP BY drivers.Display_Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct 10*stage_results.RaceID+stage_results.StageID) as Siege
FROM (stage_results INNER JOIN drivers on drivers.ID = stage_results.DriverID INNER JOIN championship ON championship.RaceID = stage_results.RaceID) INNER JOIN races on races.ID = stage_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Position = 1)
GROUP BY drivers.Display_Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct race_results.RaceID) as Poles
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Start = 1)
GROUP BY drivers.Display_Name
ORDER BY Poles DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["Poles"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct race_results.RaceID) as FRL
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (FastestRaceLap = 1)
GROUP BY drivers.Display_Name
ORDER BY FRL DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["FRL"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct race_results.RaceID) as MLL
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (MostLapsLed = 1)
GROUP BY drivers.Display_Name
ORDER BY MLL DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["MLL"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct race_results.RaceID) as MPG
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (MostPositionsGained = 1)
GROUP BY drivers.Display_Name
ORDER BY MPG DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["MPG"].'</b></td></tr>';
}
?>
</table>
</td>
</tr>
<?php
print '<tr bgcolor ='.$track_color.'>';
?>
<td colspan="1"><h4>Meiste Siege und MPG</h4></td>
<td colspan="1"><h4>Meiste Siege mit Sprint-Siege</h4></td>
<td colspan="1"><h4>Meiste Siege mit allen Stages</h4></td>
<td colspan="1"><h4>Meiste Siege von der Pole</h4></td>
<td colspan="1"><h4>Meiste Siege mit FRL</h4></td>
<td colspan="1"><h4>Meiste Führungsrunden</h4></td>
<td colspan="1"><h4>Höchster Positionsgewinn</h4></td>
</tr>
<?php
print '<tr bgcolor ='.$track_color.'>';
?>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1) AND (MostPositionsGained = 1)
GROUP BY drivers.Display_Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID 
INNER JOIN sprint_results ON (sprint_results.RaceID = race_results.RaceID AND sprint_results.DriverID = race_results.DriverID)
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (race_results.Finish = 1) AND (sprint_results.Finish = 1)
GROUP BY drivers.Display_Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
INNER JOIN (SELECT RaceID, DriverID FROM stage_results GROUP BY RaceID, DriverID HAVING (COUNT(StageID) > 1) AND (MAX(StageID) > 1) AND (AVG(Position) = 1)) AS StageTemp ON (StageTemp.RaceID = race_results.RaceID AND StageTemp.DriverID = race_results.DriverID)
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (race_results.Finish = 1)
GROUP BY drivers.Display_Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1) AND (Start = 1)
GROUP BY drivers.Display_Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1) AND (FastestRaceLap = 1)
GROUP BY drivers.Display_Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, sum(Led) as Led
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY drivers.Display_Name
ORDER BY Led DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["Led"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Display_Name as Drivers, max(Start-Finish) as MPG, Start, Finish
FROM (race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID) INNER JOIN races on races.ID = race_results.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) and (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY Display_Name, Start, Finish
ORDER BY MPG DESC, Finish ASC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr bgcolor ='.$track_color.'><td>'.$row["Drivers"].'</td><td><b>'.$row["MPG"].'</b></td><td><span style="white-space: nowrap;">('.$row['Start'].'->'.$row['Finish'].')</span></td></tr>';
}
?>
</table>
</td>
</tr>
</table>
</p>
</body>
</html>
