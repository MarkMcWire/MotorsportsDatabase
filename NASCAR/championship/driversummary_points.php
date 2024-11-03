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
print '<h3>Fahrermeisterschaft '.$season.'</h3>';
?>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH rowspan="1"><FONT>Position</FONT></TH>
	<TH align="left" rowspan="1"><FONT >Driver</FONT></TH>
	<TH><FONT>Total</FONT></TH>
	<TH><FONT>Interval</FONT></TH>
	<TH><FONT>Main Races</FONT></TH>
	<TH><FONT>Sprint Races</FONT></TH>
	<TH><FONT>Stages</FONT></TH>
	<TH><FONT>Qualification</FONT></TH>
	<TH><FONT>Bonus/Penalty</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT TT.Bezeichnung, TT.Saison, TT.Kategorie, TT.DriverID, drivers.Display_Name, 
	(SUM(TT.Rennpunkte) + SUM(TT.Sprintpunkte) + SUM(TT.Stagepunkte) + SUM(TT.Qualifikationspunkte) + SUM(TT.Bonuspunkte)) AS Punkte, 
	SUM(TT.Rennpunkte) AS Rennpunkte, 
	SUM(TT.Sprintpunkte) AS Sprintpunkte, 
	SUM(TT.Stagepunkte) AS Stagepunkte, 
	SUM(TT.Bonuspunkte) AS Bonuspunkte, 
	SUM(TT.Qualifikationspunkte) AS Qualifikationspunkte, 
	GROUP_CONCAT(LPAD(TT.Finish, 2, '0') ORDER BY TT.Finish) AS Platzierungen
	FROM (
	SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, race_results.Finish AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, 0 AS Qualifikationspunkte, 0 AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID, race_results.Finish 
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, SUM(championship_scoring.Rank_Points) AS Rennpunkte, SUM(championship_scoring.Sprint_Points) AS Sprintpunkte, SUM(championship_scoring.Stage_Points) AS Stagepunkte, SUM(championship_scoring.Qualification_Points) AS Qualifikationspunkte, SUM(championship_scoring.Bonus_Points) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID LEFT JOIN championship_scoring ON championship_scoring.ChampionshipID = championship.ID AND championship_scoring.DriverID = race_results.DriverID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, 0 AS Rennpunkte, 0 AS Sprintpunkte, 0 AS Stagepunkte, 0 AS Qualifikationspunkte, SUM(penalties.Points) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID 
	LEFT JOIN penalties ON penalties.RaceID = race_results.RaceID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	AND (penalties.DriverID = race_results.DriverID)
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	) AS TT INNER JOIN drivers ON drivers.ID = TT.DriverID
	GROUP BY TT.Saison, TT.Bezeichnung, TT.Kategorie, TT.DriverID, drivers.Display_Name
	ORDER BY TT.Saison, TT.Bezeichnung, TT.Kategorie, Punkte DESC, Platzierungen";
$recordset0 = $database_connection->query($query0);
$i = 0;
$points_left = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$points = $row['Punkte'];
if ($i == 1) {$points_max = $points;}
$driverID = $row['DriverID'];

include("verbindung.php");
$query3 = "
SELECT SUM(Punkte) As RestPunkte, SUM(Races) AS RestRennen FROM (
SELECT MAX(championship.Race_Scoring - 1) * COUNT(DISTINCT championship.RaceID) AS Punkte, COUNT(DISTINCT championship.RaceID) AS Races 
FROM (championship INNER JOIN races ON (races.ID = championship.RaceID)) LEFT JOIN tracks ON tracks.ID = races.TrackID LEFT JOIN race_results ON race_results.RaceID = championship.RaceID
WHERE (championship.Bezeichnung = '$championship_name') AND (championship.Saison = $season) AND (championship.Kategorie = $category OR $category = -1) AND (race_results.Finish IS NULL) 
UNION ALL SELECT MAX(championship.Sprint_Scoring - 1) * COUNT(DISTINCT championship.RaceID) AS Punkte, 0 AS Races 
FROM (championship INNER JOIN races ON (races.ID = championship.RaceID)) LEFT JOIN tracks ON tracks.ID = races.TrackID LEFT JOIN sprint_results ON sprint_results.RaceID = championship.RaceID
WHERE (championship.Bezeichnung = '$championship_name') AND (championship.Saison = $season) AND (championship.Kategorie = $category OR $category = -1) AND (sprint_results.Finish IS NULL)
UNION ALL SELECT MAX(championship.Stage_Scoring - 1) * COUNT(DISTINCT championship.RaceID) AS Punkte, 0 AS Races 
FROM (championship INNER JOIN races ON (races.ID = championship.RaceID)) LEFT JOIN tracks ON tracks.ID = races.TrackID LEFT JOIN stage_results ON stage_results.RaceID = championship.RaceID
WHERE (championship.Bezeichnung = '$championship_name') AND (championship.Saison = $season) AND (championship.Kategorie = $category OR $category = -1) AND (stage_results.Position IS NULL)
UNION ALL SELECT MAX(championship.Qualification_Scoring - 1) * COUNT(DISTINCT championship.RaceID) AS Punkte, 0 AS Races 
FROM (championship INNER JOIN races ON (races.ID = championship.RaceID)) LEFT JOIN tracks ON tracks.ID = races.TrackID LEFT JOIN qualification_results ON qualification_results.RaceID = championship.RaceID
WHERE (championship.Bezeichnung = '$championship_name') AND (championship.Saison = $season) AND (championship.Kategorie = $category OR $category = -1) AND (qualification_results.Position IS NULL)
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
	print'<TD><FONT >'.ROUND($points).'</FONT></TD>';
	if ($i == 1) {print'<TD><FONT >--</FONT></TD>';} else {print'<TD><FONT >'.ROUND($points-$points_max).'</FONT></TD>';}
	print'<TD><FONT >'.ROUND($row['Rennpunkte']).'</FONT></TD>';
	print'<TD><FONT >'.ROUND($row['Sprintpunkte']).'</FONT></TD>';
	print'<TD><FONT >'.ROUND($row['Stagepunkte']).'</FONT></TD>';
	print'<TD><FONT >'.ROUND($row['Qualifikationspunkte']).'</FONT></TD>';
	print'<TD><FONT >'.ROUND($row['Bonuspunkte']).'</FONT></TD>';
print'</TR>';
}
print'<TR>';
	if ($points_left > 0) {
	print'<TD colspan="2"><FONT>Noch zu fahrende Rennen: <b>'.$races_left.'</b></FONT></TD>';
	print'<TD colspan="7"><FONT>Noch zu vergebende Meisterschaftspunkte: <b>'.$points_left.'</b></FONT></TD>';}
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
