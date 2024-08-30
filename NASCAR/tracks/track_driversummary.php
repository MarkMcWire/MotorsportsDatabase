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
<td>
<?php
if (isset($_GET["Strecke"])) {$track_name = $_GET["Strecke"];} ELSE {$track_name = 0;}
if (isset($_GET["ID"])) {$trackID = $_GET["ID"];} ELSE {$trackID = $track;}
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
include("verbindung.php");
$query = "SELECT tracks.ID as TrackID, tracks.Bezeichnung, tracks.Kennzeichen as StreckenKz, MAX(races.Length) as Length, MAX(LEFT(races.ID, 4)) AS MaxSeason
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID LEFT JOIN championship on races.ID = championship.RaceID
WHERE (tracks.ID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Kennzeichen
ORDER BY tracks.Bezeichnung";
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
$maxseason = $result['MaxSeason'];
print '<h3>'.$result['Bezeichnung'].'</h3>';
?>
</td>
</tr>
<tr>
<td>
<h3>Fahrer&uuml;bersicht</h3>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH><FONT >Position</FONT></TH>
	<TH align="left"><FONT >Fahrer</FONT></TH>
	<TH><FONT >Punkte</FONT></TH>
	<TH><FONT >Interval</FONT></TH>
	<TH><FONT >Rennen</FONT></TH>
	<TH><FONT >Siege</FONT></TH>
	<TH><FONT >Top 5</FONT></TH>
	<TH><FONT >Top 10</FONT></TH>
	<TH><FONT >Poles</FONT></TH>
	<TH><FONT >Runden</FONT></TH>
	<TH><FONT >Led</FONT></TH>
	<TH><FONT >Led%</FONT></TH>
	<TH><FONT >MLL</FONT></TH>
	<TH><FONT >FRL</FONT></TH>
	<TH><FONT >MPG</FONT></TH>
	<TH><FONT >Ausf&auml;lle</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT drivers.ID as DriverID, drivers.Display_Name as drivers,
6 * SUM(race_results.Finish = 1) + 3 * SUM(race_results.Finish = 2) + 1 * SUM(race_results.Finish = 3) + SUM(race_results.LedLapFinish) + SUM(race_results.MostLapsLed) + SUM(race_results.FastestRaceLap) + SUM(race_results.Start = 1) as Gesamtwertung, GROUP_CONCAT(LPAD(race_results.Finish, 2, '0') ORDER BY race_results.Finish) AS Platzierungen
FROM race_results LEFT JOIN races on race_results.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID
LEFT JOIN drivers on race_results.DriverID = drivers.ID LEFT JOIN championship on races.ID = championship.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY drivers.ID, drivers.Display_Name
ORDER BY Gesamtwertung DESC, Platzierungen";
$recordset0 = $database_connection->query($query0);
include("verbindung.php");
$query2 = "SELECT SUM(race_results.Led) AS Led
FROM race_results INNER JOIN races ON races.ID = race_results.RaceID INNER JOIN championship ON championship.RaceID = races.ID
WHERE (races.TrackID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY races.TrackID";
$recordset2 = $database_connection->query($query2);
$result2 = $recordset2->fetch_assoc();
$ledall = $result2['Led'];
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$points = $row['Gesamtwertung'];
if ($i == 1) {$points_max = $points;}
$driverID = $row['DriverID'];
include("verbindung.php");
$query1 = "SELECT COUNT(race_results.RaceID) AS Events, SUM(race_results.Laps) AS Laps, SUM(race_results.Finish = 1) AS Wins, SUM(race_results.Finish <= 3) AS Podiums, SUM(race_results.Finish <= 5) AS Top5, SUM(race_results.Finish <= 10) AS Top10, 
SUM(race_results.Start = 1) AS Poles, SUM(race_results.Led) AS Led, SUM(race_results.MostLapsLed) AS MLL, SUM(race_results.FastestRaceLap) AS FRL, SUM(race_results.MostPositionsGained) AS MPG, SUM(race_results.DNF) AS DNF
FROM race_results INNER JOIN races ON races.ID = race_results.RaceID INNER JOIN championship ON championship.RaceID = races.ID
WHERE (race_results.DriverID = ".$driverID.") AND (races.TrackID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY races.TrackID";
$recordset1 = $database_connection->query($query1);
$result1 = $recordset1->fetch_assoc();
$events = $result1['Events'];
$wins = $result1['Wins'];
$top5 = $result1['Top5'];
$top10 = $result1['Top10'];
$poles = $result1['Poles'];
$frl = $result1['FRL'];
$laps = $result1['Laps'];
$led = $result1['Led'];
$mll = $result1['MLL'];
$mpg = $result1['MPG'];
$dnf = $result1['DNF'];
if ($ledall > 0) {$ledpercent = round(100*$led/$ledall,2);} else {$ledpercent = 0;}

include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['MaxSeason'] < $maxseason) {$driver_color = 'darkgrey';} else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT ><a href='../driver/driver.php?ID=".$driverID."'>".$row['drivers'].'</a></FONT></TD>';
	print'<TD><FONT >'.$points.'</FONT></TD>';
	if ($i == 1) {print'<TD><FONT >--</FONT></TD>';} else {print'<TD><FONT >'.($points-$points_max).'</FONT></TD>';}
	print'<TD><FONT >'.$events.'</FONT></TD>';
	print'<TD><FONT >'.$wins.'</FONT></TD>';
	print'<TD><FONT >'.$top5.'</FONT></TD>';
	print'<TD><FONT >'.$top10.'</FONT></TD>';
	print'<TD><FONT >'.$poles.'</FONT></TD>';
	print'<TD><FONT >'.$laps.'</FONT></TD>';
	print'<TD><FONT >'.$led.'</FONT></TD>';
	print'<TD><FONT >'.$ledpercent.'</FONT></TD>';
	print'<TD><FONT >'.$mll.'</FONT></TD>';
	print'<TD><FONT >'.$frl.'</FONT></TD>';
	print'<TD><FONT >'.$mpg.'</FONT></TD>';
	print'<TD><FONT >'.$dnf.'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</td>
</tr>
</table>
</p>
</p>
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
</body>
</html>
