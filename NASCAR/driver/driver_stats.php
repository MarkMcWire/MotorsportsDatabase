<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["ID"])) {$driverID = $_GET["ID"]; $query = "SELECT * FROM drivers WHERE (ID = $driverID) ORDER BY Kategorie, Name";} ELSE {$query = "SELECT * FROM drivers ORDER BY Kategorie, Name";}
include("verbindung.php");
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
#if(!$row)die("Keine Ergebnisse <br/>");

$ID = $result['ID'];
$name = $result['Name'];
print '<p>';
print "<h2 align='center'>$name</h2>";
print "</p>";
?>
<p>
<table border="1" cellspacing="0">
<tr>
<th>Pole Positions</th>
<th>Siege</th>
<th>Meiste F&uuml;hrungsrunden</th>
<th>Schnellste Rennrunden</th>
<th>Meiste Positionen gewonnen</th>
</tr>
<tr bgcolor ='lightgrey' align='center'>
<?php
include("verbindung.php");
$query_wins = "SELECT COUNT(race_results.RaceID) as Wins
FROM race_results
INNER JOIN championship on championship.RaceID = race_results.RaceID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.Finish = 1 and drivers.ID = $driverID";
$recordset_wins = $database_connection->query($query_wins);
$result_wins = $recordset_wins->fetch_assoc();
$wins = $result_wins['Wins'];

$query_poles = "SELECT COUNT(race_results.RaceID) as Poles
FROM race_results
INNER JOIN championship on championship.RaceID = race_results.RaceID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.Start = 1 and drivers.ID = $driverID";
$recordset_poles = $database_connection->query($query_poles);
$result_poles = $recordset_poles->fetch_assoc();
$poles = $result_poles['Poles'];

$query_mll = "SELECT COUNT(race_results.RaceID) as MLL
FROM race_results
INNER JOIN championship on championship.RaceID = race_results.RaceID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.MostLapsLed and drivers.ID = $driverID";
$recordset_mll = $database_connection->query($query_mll);
$result_mll = $recordset_mll->fetch_assoc();
$mll = $result_mll['MLL'];

$query_frl = "SELECT COUNT(race_results.RaceID) as FRL
FROM race_results
INNER JOIN championship on championship.RaceID = race_results.RaceID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.FastestRaceLap and drivers.ID = $driverID";
$recordset_frl = $database_connection->query($query_frl);
$result_frl = $recordset_frl->fetch_assoc();
$frl = $result_frl['FRL'];

$query_mpg = "SELECT COUNT(race_results.RaceID) as MPG
FROM race_results
INNER JOIN championship on championship.RaceID = race_results.RaceID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.MostPositionsGained and drivers.ID = $driverID";
$recordset_mpg = $database_connection->query($query_mpg);
$result_mpg = $recordset_mpg->fetch_assoc();
$mpg = $result_mpg['MPG'];

print "<td>";
echo $poles;
print "</td>";
print "<td>";
echo $wins;
print "</td>";
print "<td>";
echo $mll;
print "</td>";
print "<td>";
echo $frl;
print "</td>";
print "<td>";
echo $mpg;
print "</td>";
?>
</tr>
<tr align="left">
<td>
<ol>
<?php
include("verbindung.php");
$query = "SELECT tracks.Bezeichnung as Rennstrecke, track_type.ColorCode, championship.Saison, races.Event
FROM race_results INNER JOIN races on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID INNER JOIN tracks on races.TrackID = tracks.ID INNER JOIN drivers on race_results.DriverID = drivers.ID LEFT JOIN track_type on track_type.ID = races.TypeID
WHERE (drivers.ID = $driverID or $driverID = 0) and (race_results.Start =1)
GROUP BY races.ID, race_results.Start, tracks.Bezeichnung, track_type.ColorCode, championship.Saison, races.Event
ORDER BY races.ID, race_results.Start";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
$track_color = $row['ColorCode'];
print "<li>";
print "<font style='background-color:".$track_color."'>";
print $row['Saison'].' '.$row['Event'].'<br />(<b>'.$row['Rennstrecke'].'</b>)';
print "</font>";
print "</li>";
}
?>
</ol>
</td>
<td>
<ol>
<?php
include("verbindung.php");
$query = "SELECT tracks.Bezeichnung as Rennstrecke, track_type.ColorCode, championship.Saison, races.Event
FROM race_results INNER JOIN races on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID INNER JOIN tracks on races.TrackID = tracks.ID INNER JOIN drivers on race_results.DriverID = drivers.ID LEFT JOIN track_type on track_type.ID = races.TypeID
WHERE (drivers.ID = $driverID or $driverID = 0) and (race_results.Finish = 1)
GROUP BY races.ID, race_results.Finish, tracks.Bezeichnung, track_type.ColorCode, championship.Saison, races.Event
ORDER BY races.ID, race_results.Finish";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
$track_color = $row['ColorCode'];
print "<li>";
print "<font style='background-color:".$track_color."'>";
print $row['Saison'].' '.$row['Event'].'<br />(<b>'.$row['Rennstrecke'].'</b>)';
print "</font>";
print "</li>";
}
?>
</ol>
</td>
<td>
<ol>
<?php
include("verbindung.php");
$query = "SELECT tracks.Bezeichnung as Rennstrecke, track_type.ColorCode, championship.Saison, races.Event
FROM race_results INNER JOIN races on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID INNER JOIN tracks on races.TrackID = tracks.ID INNER JOIN drivers on race_results.DriverID = drivers.ID LEFT JOIN track_type on track_type.ID = races.TypeID
WHERE race_results.MostLapsLed and (drivers.ID = $driverID or $driverID = 0)
GROUP BY races.ID, race_results.Led, tracks.Bezeichnung, track_type.ColorCode, championship.Saison, races.Event
ORDER BY races.ID, race_results.Led";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
$track_color = $row['ColorCode'];
print "<li>";
print "<font style='background-color:".$track_color."'>";
print $row['Saison'].' '.$row['Event'].'<br />(<b>'.$row['Rennstrecke'].'</b>)';
print "</font>";
print "</li>";
}
?>
</ol>
</td>
<td>
<ol>
<?php
include("verbindung.php");
$query = "SELECT tracks.Bezeichnung as Rennstrecke, track_type.ColorCode, championship.Saison, races.Event
FROM race_results INNER JOIN races on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID INNER JOIN tracks on races.TrackID = tracks.ID INNER JOIN drivers on race_results.DriverID = drivers.ID LEFT JOIN track_type on track_type.ID = races.TypeID
WHERE race_results.FastestRaceLap and (drivers.ID = $driverID or $driverID = 0)
GROUP BY races.ID, race_results.Led, tracks.Bezeichnung, track_type.ColorCode, championship.Saison, races.Event
ORDER BY races.ID, race_results.Led";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
$track_color = $row['ColorCode'];
print "<li>";
print "<font style='background-color:".$track_color."'>";
print $row['Saison'].' '.$row['Event'].'<br />(<b>'.$row['Rennstrecke'].'</b>)';
print "</font>";
print "</li>";
}
?>
</ol>
</td>
<td>
<ol>
<?php
include("verbindung.php");
$query = "SELECT tracks.Bezeichnung as Rennstrecke, track_type.ColorCode, championship.Saison, races.Event
FROM race_results INNER JOIN races on race_results.RaceID = races.ID LEFT JOIN championship on championship.RaceID = races.ID INNER JOIN tracks on races.TrackID = tracks.ID INNER JOIN drivers on race_results.DriverID = drivers.ID LEFT JOIN track_type on track_type.ID = races.TypeID
WHERE race_results.MostPositionsGained and (drivers.ID = $driverID or $driverID = 0)
GROUP BY races.ID, race_results.MostPositionsGained, tracks.Bezeichnung, track_type.ColorCode, championship.Saison, races.Event
ORDER BY race_results.RaceID, race_results.MostPositionsGained";
$recordset = $database_connection->query($query);
while ($row = $recordset->fetch_assoc())
{
$track_color = $row['ColorCode'];
print "<li>";
print "<font style='background-color:".$track_color."'>";
print $row['Saison'].' '.$row['Event'].'<br />(<b>'.$row['Rennstrecke'].'</b>)';
print "</font>";
print "</li>";
}
?>
</ol>
</td>
</tr>
</table>
</p>
<br/>
<p align = "center">
<?php
print "<a href='driver.php?ID=".$driverID."'>Zur&uuml;ck zur &Uuml;bersicht</a>";
?>
</p>
</body>
</html>
