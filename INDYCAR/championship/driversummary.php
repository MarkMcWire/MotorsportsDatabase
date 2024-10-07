<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["Saison"])) {$season = $_GET["Saison"];} ELSE {$season = 0;}
if (isset($_GET["Champ"])) {$championship_name = $_GET["Champ"];} ELSE {$championship_name = '0';}
if (isset($_GET["Kategorie"])) {$category = $_GET["Kategorie"];} ELSE {$category = -1;}
?>
<p>
<TABLE border="2" cellspacing="10">
<TR>
<TD>
<?php
print '<h3>Fahrer&uuml;bersicht '.$season.'</h3>';
?>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH rowspan="2"><FONT>Position</FONT></TH>
	<TH align="left" rowspan="2"><FONT >Driver</FONT></TH>
	<TH rowspan="2"><FONT >Races</FONT></TH>
	<TH rowspan="2"><FONT >Avg Finish</FONT></TH>
	<TH rowspan="2"><FONT >Wins</FONT></TH>
	<TH rowspan="2"><FONT >Top 5</FONT></TH>
	<TH rowspan="2"><FONT >Top 10</FONT></TH>
	<TH rowspan="2"><FONT >Poles</FONT></TH>
	<TH rowspan="2"><FONT >Laps</FONT></TH>
	<TH rowspan="2"><FONT >Led</FONT></TH>
	<TH rowspan="2"><FONT >Led%</FONT></TH>
	<TH rowspan="2"><FONT >MLL</FONT></TH>
	<TH rowspan="2"><FONT >FRL</FONT></TH>
	<TH rowspan="2"><FONT >MPG</FONT></TH>
	<TH rowspan="2"><FONT >DNFs</FONT></TH>
</TR>
<TR>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT TT.Bezeichnung, TT.Saison, TT.Kategorie, TT.DriverID, drivers.Display_Name, GROUP_CONCAT(LPAD(TT.Finish, 2, '0') ORDER BY TT.Finish) AS Platzierungen, AVG(TT.Finish) AS AvgFinish
	FROM (
	SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.DriverID, race_results.Finish AS Finish
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID, race_results.Finish 
	) AS TT INNER JOIN drivers ON drivers.ID = TT.DriverID
	GROUP BY TT.Saison, TT.Bezeichnung, TT.Kategorie, TT.DriverID, drivers.Display_Name
	ORDER BY TT.Saison, TT.Bezeichnung, TT.Kategorie, Platzierungen";
$recordset0 = $database_connection->query($query0);
include("verbindung.php");
$query2 = "SELECT SUM(race_results.Led) AS Led
FROM race_results INNER JOIN races ON races.ID = race_results.RaceID INNER JOIN championship ON championship.RaceID = races.ID
WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie";
$recordset2 = $database_connection->query($query2);
$result2 = $recordset2->fetch_assoc();
$ledall = $result2['Led'];
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$driverID = $row['DriverID'];
include("verbindung.php");
$query1 = "SELECT COUNT(race_results.RaceID) AS Events, SUM(race_results.Laps) AS Laps, SUM(race_results.Finish = 1) AS Wins, SUM(race_results.Finish <= 3) AS Podiums, SUM(race_results.Finish <= 5) AS Top5, SUM(race_results.Finish <= 10) AS Top10, 
SUM(race_results.Start = 1) AS Poles, SUM(race_results.Led) AS Led, SUM(race_results.MostLapsLed) AS MLL, SUM(race_results.FastestRaceLap) AS FRL, SUM(race_results.MostPositionsGained) AS MPG, SUM(race_results.DNF) AS DNF
FROM race_results INNER JOIN races ON races.ID = race_results.RaceID INNER JOIN championship ON championship.RaceID = races.ID
WHERE (race_results.DriverID = ".$driverID.") AND (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie";
$recordset1 = $database_connection->query($query1);
$result1 = $recordset1->fetch_assoc();
$events = $result1['Events'];
$avgfinish = number_format($row['AvgFinish'], 2);
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
$ledpercent = round(100*$led/$ledall,2);
$race_color = 'white';
$points = 0;
$points_max = 0;

include("verbindung.php");
$query3 = "SELECT MAX(Saison) AS MaxSeason FROM championship";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
if ($result3['MaxSeason'] > $season) {$race_color = 'darkgrey';} else {$race_color = 'lightgrey';}

print"<TR bgcolor ='$race_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT ><a href='../driver/driver.php?ID=".$driverID."'>".$row['Display_Name'].'</a></FONT></TD>';
	print'<TD><FONT >'.$events.'</FONT></TD>';
	print'<TD><FONT >'.$avgfinish.'</FONT></TD>';
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
</TD>
</TR>
</TABLE>
<br/>
<?php
print '<p>';
print '</p>';
print '<p align="center">';
print "<a href='?Champ=".$championship_name."&Saison=".($season - 1)."&Kategorie=".$category."'>Vorherige Saison</a>";
print "&nbsp&nbsp&nbsp&nbsp&nbsp;";
print "<a href='index.php'>Zur&uuml;ck zum Index</a>";
print "&nbsp&nbsp&nbsp&nbsp&nbsp;";
print "<a href='?Champ=".$championship_name."&Saison=".($season + 1)."&Kategorie=".$category."'>Nachfolgende Saison</a>";
?>
</p>
</body>
</html>
