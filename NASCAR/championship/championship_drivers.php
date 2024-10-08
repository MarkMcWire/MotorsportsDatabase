<!DOCTYPE html>
<html lang="en">
<HEAD>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</HEAD>
<BODY bgcolor="#FFFFFF" text="#000000" link="#0000FF" vlink="#FF00FF" alink="#FF0000">
<FONT face="Times New Roman">
<HR>
<H2>Fahrer&uuml;bersicht</H2>
<?php
if (isset($_GET["Champ"])) {$championship_name = $_GET["Champ"];} ELSE {$championship_name = '';}
$query = "SELECT coalesce(count(distinct race_results.RaceID),0) as Events, coalesce(sum(race_results.Laps),0) as Laps, coalesce(sum(race_results.Laps * races.Length),0) as Miles, MAX(LEFT(race_results.RaceID,4)) AS MaxSeason
FROM race_results
LEFT JOIN races on race_results.RaceID = races.ID
LEFT JOIN championship on championship.RaceID = races.ID
LEFT JOIN tracks on races.TrackID = tracks.ID
WHERE race_results.Finish = 1 and (championship.Bezeichnung like '$championship_name')";
include("verbindung.php");
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
$events = $result['Events'];
$maxseason = $result['MaxSeason'];
print'<H3>Stand nach '.$events.' Rennen</H3>';
?>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH rowspan="2"><FONT>Position</FONT></TH>
	<TH align="left" rowspan="2"><FONT>Fahrer</FONT></TH>
	<TH rowspan="2"><FONT>Rennen</FONT></TH>
	<TH rowspan="2"><FONT>Siege</FONT></TH>
	<TH rowspan="2"><FONT>Top 5</FONT></TH>
	<TH rowspan="2"><FONT>Top 10</FONT></TH>
	<TH rowspan="2"><FONT>Poles</FONT></TH>
	<TH rowspan="2"><FONT>Runden</FONT></TH>
	<TH rowspan="2"><FONT>Led</FONT></TH>
	<TH rowspan="2"><FONT>Led%</FONT></TH>
	<TH rowspan="2"><FONT>MLL</FONT></TH>
	<TH rowspan="2"><FONT>FRL</FONT></TH>
	<TH rowspan="2"><FONT>MPG</FONT></TH>
	<TH rowspan="2"><FONT>Ausf&auml;lle</FONT></TH>
</TR>
<TR>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT TT.Bezeichnung, TT.DriverID, drivers.Display_Name, GROUP_CONCAT(LPAD(TT.Finish, 2, '0') ORDER BY TT.Finish) AS Platzierungen
	FROM (
	SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, race_results.Finish AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, 0 AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."')
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID, race_results.Finish 
	) AS TT INNER JOIN drivers ON drivers.ID = TT.DriverID
	GROUP BY TT.Bezeichnung, TT.DriverID, drivers.Display_Name
	ORDER BY TT.Bezeichnung, Platzierungen";
$recordset0 = $database_connection->query($query0);
include("verbindung.php");
$query2 = "SELECT SUM(race_results.Led) as Led
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN championship on championship.RaceID = races.ID
WHERE championship.Bezeichnung like '$championship_name'";
$recordset2 = $database_connection->query($query2);
$result2 = $recordset2->fetch_assoc();
$ledall = $result2['Led'];
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$points = 0;
if ($i == 1) {$points_max = $points;}
$driverID = $row['DriverID'];
include("verbindung.php");
$query1 = "SELECT COUNT(race_results.RaceID) AS Events, SUM(race_results.Laps) AS Laps, SUM(race_results.Finish = 1) AS Wins, SUM(race_results.Finish <= 3) AS Podiums, SUM(race_results.Finish <= 5) AS Top5, SUM(race_results.Finish <= 10) AS Top10, 
SUM(race_results.Start = 1) AS Poles, SUM(race_results.Led) AS Led, SUM(race_results.MostLapsLed) AS MLL, SUM(race_results.FastestRaceLap) AS FRL, SUM(race_results.MostPositionsGained) AS MPG, SUM(race_results.DNF) AS DNF
FROM race_results INNER JOIN races ON races.ID = race_results.RaceID INNER JOIN championship ON championship.RaceID = races.ID
WHERE (race_results.DriverID = ".$driverID.") AND (championship.Bezeichnung LIKE '".$championship_name."')
GROUP BY championship.Bezeichnung";
$recordset1 = $database_connection->query($query1);
if ($result1 = $recordset1->fetch_assoc()) {
	$events = $result1['Events'];
	$laps = $result1['Laps'];
	$led = $result1['Led'];
	$ledpercent = round(100*$led/$ledall,2);
	$wins = $result1['Wins'];
	$top5 = $result1['Top5'];
	$top10 = $result1['Top10'];
	$poles = $result1['Poles'];
	$frl = $result1['FRL'];
	$mll = $result1['MLL'];
	$mpg = $result1['MPG'];
	$dnf = $result1['DNF'];
} else {
	$events = 0;
	$laps = 0;
	$led = 0;
	$ledpercent = 0;
	$wins = 0;
	$top5 = 0;
	$top10 = 0;
	$poles = 0;
	$frl = 0;
	$mll = 0;
	$mpg = 0;
	$dnf = 0;
}
include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['MaxSeason'] < $maxseason) {$driver_color = 'darkgrey';} else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT>'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT><a href='../driver/driver.php?ID=".$driverID."'>".$row['Display_Name'].'</a></FONT></TD>';
	print'<TD><FONT>'.$events.'</FONT></TD>';
	print'<TD><FONT>'.$wins.'</FONT></TD>';
	print'<TD><FONT>'.$top5.'</FONT></TD>';
	print'<TD><FONT>'.$top10.'</FONT></TD>';
	print'<TD><FONT>'.$poles.'</FONT></TD>';
	print'<TD><FONT>'.$laps.'</FONT></TD>';
	print'<TD><FONT>'.$led.'</FONT></TD>';
	print'<TD><FONT>'.$ledpercent.'</FONT></TD>';
	print'<TD><FONT>'.$mll.'</FONT></TD>';
	print'<TD><FONT>'.$frl.'</FONT></TD>';
	print'<TD><FONT>'.$mpg.'</FONT></TD>';
	print'<TD><FONT>'.$dnf.'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</FONT>
</BODY>
</HTML>
