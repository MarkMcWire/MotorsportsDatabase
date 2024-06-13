<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<h1>Rekorde</h1>
<p>
<table border="3">
<tr>
<th colspan="1"><h3>Renn-Siege</h3></th>
<th colspan="1"><h3>Sprint-Siege</h3></th>
<th colspan="1"><h3>Stage-Siege</h3></th>
<th colspan="1"><h3>Pole Positions</h3></th>
<th colspan="1"><h3>Schnellste Rennrunden</h3></th>
<th colspan="1"><h3>Führungsrunden</h3></th>
<th colspan="1"><h3>Positionsgewinne</h3></th>
</tr>
<tr>
<td colspan="1"><h4>Meiste Renn-Siege (absolut)</h4></td>
<td colspan="1"><h4>Meiste Sprint-Siege (absolut)</h4></td>
<td colspan="1"><h4>Meiste Stage-Siege (absolut)</h4></td>
<td colspan="1"><h4>Meiste Poles (absolut)</h4></td>
<td colspan="1"><h4>Meiste FRL</h4></td>
<td colspan="1"><h4>Meiste MLL</h4></td>
<td colspan="1"><h4>Meiste MPG</h4></td>
</tr>
<tr>
<td>
<table border = "0">
<?php
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1)
GROUP BY drivers.Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct sprint_results.RaceID) as Siege
FROM sprint_results INNER JOIN drivers on drivers.ID = sprint_results.DriverID INNER JOIN championship ON championship.RaceID = sprint_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1)
GROUP BY drivers.Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct 10*stage_results.RaceID+stage_results.StageID) as Siege
FROM stage_results INNER JOIN drivers on drivers.ID = stage_results.DriverID INNER JOIN championship ON championship.RaceID = stage_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Position = 1)
GROUP BY drivers.Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as Poles
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Start = 1)
GROUP BY drivers.Name
ORDER BY Poles DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["Poles"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as FRL
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (FastestRaceLap = 1)
GROUP BY drivers.Name
ORDER BY FRL DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["FRL"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as MLL
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (MostLapsLed = 1)
GROUP BY drivers.Name
ORDER BY MLL DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["MLL"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as MPG
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (MostPositionsGained = 1)
GROUP BY drivers.Name
ORDER BY MPG DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["MPG"].'</b></td></tr>';
}
?>
</table>
</td>
</tr>
<tr>
<td colspan="8">
<hr></hr>
</td>
</tr>
<tr>
<td colspan="1"><h4>Meiste Siege und MPG</h4></td>
<td colspan="1"><h4>Meiste Siege mit Sprint-Siege</h4></td>
<td colspan="1"><h4>Meiste Siege mit allen Stages</h4></td>
<td colspan="1"><h4>Meiste Siege von der Pole</h4></td>
<td colspan="1"><h4>Meiste Siege mit FRL</h4></td>
<td colspan="1"><h4>Meiste Führungsrunden</h4></td>
<td colspan="1"><h4>Höchster Positionsgewinn</h4></td>
</tr>
<tr>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1) AND (MostPositionsGained = 1)
GROUP BY drivers.Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID 
INNER JOIN sprint_results ON (sprint_results.RaceID = race_results.RaceID AND sprint_results.DriverID = race_results.DriverID)
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (race_results.Finish = 1) AND (sprint_results.Finish = 1)
GROUP BY drivers.Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID 
INNER JOIN (SELECT RaceID, DriverID FROM stage_results GROUP BY RaceID, DriverID HAVING (COUNT(StageID) > 1) AND (MAX(StageID) > 1) AND (AVG(Position) = 1)) AS StageTemp ON (StageTemp.RaceID = race_results.RaceID AND StageTemp.DriverID = race_results.DriverID)
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (race_results.Finish = 1)
GROUP BY drivers.Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1) AND (Start = 1)
GROUP BY drivers.Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as Siege
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1) AND (FastestRaceLap = 1)
GROUP BY drivers.Name
ORDER BY Siege DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, sum(Led) as Led
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY drivers.Name
ORDER BY Led DESC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["Led"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, max(Start-Finish) as MPG, Start, Finish
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship ON championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY Name, Start, Finish
ORDER BY MPG DESC, Finish ASC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td><b>'.$row["MPG"].'</b></td><td><span style="white-space: nowrap;">('.$row['Start'].'->'.$row['Finish'].')</span></td></tr>';
}
?>
</table>
</td>
</tr>
<tr>
<td colspan="8">
<hr></hr>
</td>
</tr>
<tr>
<td colspan="1"><h4>Meiste Renn-Siege in einer Saison</h4></td>
<td colspan="1"><h4>Meiste Sprint-Siege in einer Saison</h4></td>
<td colspan="1"><h4>Meiste Stage-Siege in einer Saison</h4></td>
<td colspan="1"><h4>Meiste Poles in einer Saison</h4></td>
<td colspan="1"><h4>Meiste FRL in einer Saison</h4></td>
<td colspan="1"><h4>Meiste MLL in einer Saison</h4></td>
<td colspan="1"><h4>Meiste MPG in einer Saison</h4></td>
</tr>
<tr>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as Siege, championship.Saison as Saison
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship on championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1)
GROUP BY Name, championship.Saison
ORDER BY Siege DESC, Saison ASC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td>'.$row["Saison"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct sprint_results.RaceID) as Siege, championship.Saison as Saison
FROM sprint_results INNER JOIN drivers on drivers.ID = sprint_results.DriverID INNER JOIN championship on championship.RaceID = sprint_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Finish = 1)
GROUP BY Name, championship.Saison
ORDER BY Siege DESC, Saison ASC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td>'.$row["Saison"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct 10*stage_results.RaceID+stage_results.StageID) as Siege, championship.Saison as Saison
FROM stage_results INNER JOIN drivers on drivers.ID = stage_results.DriverID INNER JOIN championship on championship.RaceID = stage_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Position = 1)
GROUP BY Name, championship.Saison
ORDER BY Siege DESC, Saison ASC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td>'.$row["Saison"].'</td><td><b>'.$row["Siege"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as Poles, championship.Saison as Saison
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship on championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (Start = 1)
GROUP BY Name, championship.Saison
ORDER BY Poles DESC, Saison ASC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td>'.$row["Saison"].'</td><td><b>'.$row["Poles"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as FRL, championship.Saison as Saison
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship on championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (FastestRaceLap = 1)
GROUP BY Name, championship.Saison
ORDER BY FRL DESC, Saison ASC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td>'.$row["Saison"].'</td><td><b>'.$row["FRL"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as MLL, championship.Saison as Saison
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship on championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (MostLapsLed = 1)
GROUP BY Name, championship.Saison
ORDER BY MLL DESC, Saison ASC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td>'.$row["Saison"].'</td><td><b>'.$row["MLL"].'</b></td></tr>';
}
?>
</table>
</td>
<td>
<table border = "0">
<?php
include("verbindung.php");
$query="SELECT drivers.Name as Drivers, count(distinct race_results.RaceID) as MPG, championship.Saison as Saison
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID INNER JOIN championship on championship.RaceID = race_results.RaceID
WHERE (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') AND (MostPositionsGained = 1)
GROUP BY Name, championship.Saison
ORDER BY MPG DESC, Saison ASC Limit 10";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
	print '<tr><td>'.$row["Drivers"].'</td><td>'.$row["Saison"].'</td><td><b>'.$row["MPG"].'</b></td></tr>';
}
?>
</table>
</td>
</tr>
</table>
</p>
<HR>
<p align="center">
<a href='../index.php'>Zur&uuml;ck zum Index</a>
</p>
</body>
</html>
