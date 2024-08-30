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
	<TH colspan="6"><FONT >Points</FONT></TH>
</TR>
<TR>
	<TH><FONT>Total</FONT></TH>
	<TH><FONT>Interval</FONT></TH>
	<TH><FONT>Main Races</FONT></TH>
	<TH><FONT>Sprint Races</FONT></TH>
	<TH><FONT>Stages</FONT></TH>
	<TH><FONT>Bonus/Penalty</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT TT.Bezeichnung, TT.Saison, TT.Kategorie, TT.DriverID, drivers.Display_Name, 
	(SUM(TT.Rennpunkte) + SUM(TT.Sprintpunkte) + SUM(TT.Stagepunkte) + SUM(TT.Bonuspunkte)) AS Punkte, 
	SUM(TT.Rennpunkte) AS Rennpunkte, 
	SUM(TT.Sprintpunkte) AS Sprintpunkte, 
	SUM(TT.Stagepunkte) AS Stagepunkte, 
	SUM(TT.Bonuspunkte) AS Bonuspunkte, 
	GROUP_CONCAT(LPAD(TT.Finish, 2, '0') ORDER BY TT.Finish) AS Platzierungen
	FROM (
	SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, race_results.Finish AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, 0 AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID, race_results.Finish 
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, SUM(rank_points.Punkte) AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, 0 AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID
	LEFT JOIN rank_points ON rank_points.Scoring = championship.Race_Scoring 
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND ((rank_points.Saison <= championship.Saison) OR (rank_points.Saison = 0)) AND ((rank_points.Mileage = championship.Mileage) OR (rank_points.Mileage = 0)) AND (rank_points.Wert = race_results.Finish)
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	sprint_results.DriverID, NULL AS Finish, 0 AS Rennpunkte, SUM(sprint_points.Punkte) AS Sprintpunkte, 0 AS Stagepunkte, 0 AS Bonuspunkte
	FROM sprint_results LEFT JOIN championship ON championship.RaceID = sprint_results.RaceID
	LEFT JOIN sprint_points ON sprint_points.Scoring = championship.Sprint_Scoring 
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND ((sprint_points.Saison <= championship.Saison) OR (sprint_points.Saison = 0)) AND ((sprint_points.Mileage = championship.Mileage) OR (sprint_points.Mileage = 0)) AND (sprint_points.Wert = sprint_results.Finish)
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, sprint_results.RaceID, sprint_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	stage_results.DriverID, NULL AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, SUM(stage_points.Punkte) AS Stagepunkte, 0 AS Bonuspunkte
	FROM stage_results LEFT JOIN championship ON championship.RaceID = stage_results.RaceID
	LEFT JOIN stage_points ON stage_points.Scoring = championship.Stage_Scoring 
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND ((stage_points.Saison <= championship.Saison) OR (stage_points.Saison = 0)) AND ((stage_points.Mileage = championship.Mileage) OR (stage_points.Mileage = 0)) AND (stage_points.Wert = stage_results.Position)
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, stage_results.RaceID, stage_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, SUM(bonus_points.Punkte) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID 
	LEFT JOIN bonus_points ON bonus_points.Scoring = championship.Bonus_Scoring
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND ((bonus_points.Saison <= championship.Saison) OR (bonus_points.Saison = 0)) AND ((bonus_points.Mileage = championship.Mileage) OR (bonus_points.Mileage = 0)) AND (race_results.LedLapFinish > 0) AND (bonus_points.Bewertung = 'LLF')
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, SUM(bonus_points.Punkte) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID 
	LEFT JOIN bonus_points ON bonus_points.Scoring = championship.Bonus_Scoring
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND ((bonus_points.Saison <= championship.Saison) OR (bonus_points.Saison = 0)) AND ((bonus_points.Mileage = championship.Mileage) OR (bonus_points.Mileage = 0)) AND (race_results.FastestRaceLap > 0) AND (bonus_points.Bewertung = 'FL')
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, SUM(bonus_points.Punkte) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID 
	LEFT JOIN bonus_points ON bonus_points.Scoring = championship.Bonus_Scoring
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND ((bonus_points.Saison <= championship.Saison) OR (bonus_points.Saison = 0)) AND ((bonus_points.Mileage = championship.Mileage) OR (bonus_points.Mileage = 0)) AND (race_results.MostPositionsGained > 0) AND (bonus_points.Bewertung = 'MPG')
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, SUM(bonus_points.Punkte) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID 
	LEFT JOIN bonus_points ON bonus_points.Scoring = championship.Bonus_Scoring
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND ((bonus_points.Saison <= championship.Saison) OR (bonus_points.Saison = 0)) AND ((bonus_points.Mileage = championship.Mileage) OR (bonus_points.Mileage = 0)) AND (race_results.MostLapsLed > 0) AND (bonus_points.Bewertung = 'MLL')
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, SUM(bonus_points.Punkte) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID 
	LEFT JOIN bonus_points ON bonus_points.Scoring = championship.Bonus_Scoring
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND ((bonus_points.Saison <= championship.Saison) OR (bonus_points.Saison = 0)) AND ((bonus_points.Mileage = championship.Mileage) OR (bonus_points.Mileage = 0)) AND (race_results.Led > 0) AND (bonus_points.Bewertung = 'LL')
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, SUM(penalties.Points) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID 
	LEFT JOIN penalties ON penalties.RaceID = race_results.RaceID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND (penalties.DriverID = race_results.DriverID)
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	) AS TT INNER JOIN drivers ON drivers.ID = TT.DriverID
	GROUP BY TT.Saison, TT.Bezeichnung, TT.Kategorie, TT.DriverID, drivers.Display_Name
	ORDER BY TT.Saison, TT.Bezeichnung, TT.Kategorie, Punkte DESC, Platzierungen";
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
$points_left = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$points = $row['Punkte'];
if ($i == 1) {$points_max = $points;}
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

include("verbindung.php");
$query3 = "
SELECT SUM(Punkte) As RestPunkte, SUM(Races) AS RestRennen FROM (
SELECT SUM(rank_points.Punkte) AS Punkte, COUNT(DISTINCT championship.RaceID) AS Races 
FROM (championship INNER JOIN races ON (races.ID = championship.RaceID)) LEFT JOIN tracks ON tracks.ID = races.TrackID LEFT JOIN race_results ON race_results.RaceID = championship.RaceID LEFT JOIN rank_points ON rank_points.Scoring = championship.Race_Scoring
WHERE (championship.Bezeichnung = '$championship_name') AND (championship.Saison = $season) AND (championship.Kategorie = $category OR $category = -1) AND (race_results.Finish IS NULL) 
AND ((rank_points.Saison <= championship.Saison) OR (rank_points.Saison = 0)) AND ((rank_points.Mileage = championship.Mileage) OR (rank_points.Mileage = 0)) AND (rank_points.Wert = 1)
UNION ALL SELECT SUM(stage_points.Punkte) AS Punkte, 0 AS Races 
FROM (championship INNER JOIN races ON (races.ID = championship.RaceID)) LEFT JOIN tracks ON tracks.ID = races.TrackID LEFT JOIN stage_results ON stage_results.RaceID = championship.RaceID LEFT JOIN stage_points ON stage_points.Scoring = championship.Stage_Scoring
WHERE (championship.Bezeichnung = '$championship_name') AND (championship.Saison = $season) AND (championship.Kategorie = $category OR $category = -1) AND (stage_results.Position IS NULL) 
AND ((stage_points.Saison <= championship.Saison) OR (stage_points.Saison = 0)) AND ((stage_points.Mileage = championship.Mileage) OR (stage_points.Mileage = 0)) AND (stage_points.Wert = 1)
UNION ALL SELECT SUM(sprint_points.Punkte) AS Punkte, 0 AS Races 
FROM (championship INNER JOIN races ON (races.ID = championship.RaceID)) LEFT JOIN tracks ON tracks.ID = races.TrackID LEFT JOIN sprint_results ON sprint_results.RaceID = championship.RaceID LEFT JOIN sprint_points ON sprint_points.Scoring = championship.Sprint_Scoring
WHERE (championship.Bezeichnung = '$championship_name') AND (championship.Saison = $season) AND (championship.Kategorie = $category OR $category = -1) AND (sprint_results.Finish IS NULL) 
AND ((sprint_points.Saison <= championship.Saison) OR (sprint_points.Saison = 0)) AND ((sprint_points.Mileage = championship.Mileage) OR (sprint_points.Mileage = 0)) AND (sprint_points.Wert = 1)
UNION ALL SELECT SUM(bonus_points.Punkte) AS Punkte, 0 AS Races 
FROM (championship INNER JOIN races ON (races.ID = championship.RaceID)) LEFT JOIN tracks ON tracks.ID = races.TrackID LEFT JOIN race_results ON race_results.RaceID = championship.RaceID LEFT JOIN bonus_points ON bonus_points.Scoring = championship.Bonus_Scoring
WHERE (championship.Bezeichnung = '$championship_name') AND (championship.Saison = $season) AND (championship.Kategorie = $category OR $category = -1) AND (race_results.Finish IS NULL) 
AND ((bonus_points.Saison <= championship.Saison) OR (bonus_points.Saison = 0)) AND ((bonus_points.Mileage = championship.Mileage) OR (bonus_points.Mileage = 0)) AND (bonus_points.Wert = 1)
) As TempTable
";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
if ($result3) {$points_left = $result3['RestPunkte'];} else {$points_left = 0;}
$races_left = $result3['RestRennen'];
$race_color = 'white';
if (($points_left + $points) < $points_max) {$race_color = 'darkgrey';}
else {$race_color = 'lightgrey';}

if ($i == 1) {$race_color = 'palegreen';}
if (($i > 1) && ($points - $points_max == 0)) {$race_color = 'khaki';}

print"<TR bgcolor ='$race_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT ><a href='../driver/driver.php?ID=".$driverID."'>".$row['Display_Name'].'</a></FONT></TD>';
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
	print'<TD><FONT >'.$points.'</FONT></TD>';
	if ($i == 1) {print'<TD><FONT >--</FONT></TD>';} else {print'<TD><FONT >'.($points-$points_max).'</FONT></TD>';}
	print'<TD><FONT >'.($row['Rennpunkte']).'</FONT></TD>';
	print'<TD><FONT >'.($row['Sprintpunkte']).'</FONT></TD>';
	print'<TD><FONT >'.($row['Stagepunkte']).'</FONT></TD>';
	print'<TD><FONT >'.($row['Bonuspunkte']).'</FONT></TD>';
print'</TR>';
}
print'<TR>';
	if ($points_left > 0) {
	print'<TD colspan="14"><FONT>Noch zu fahrende Rennen: <b>'.$races_left.'</b></FONT></TD>';
	print'<TD colspan="8"><FONT>Noch zu vergebende Meisterschaftspunkte: <b>'.$points_left.'</b></FONT></TD>';}
print'</TR>';
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
