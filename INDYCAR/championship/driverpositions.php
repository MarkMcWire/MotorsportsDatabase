<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["Champ"])) {$championship_name = $_GET["Champ"];} ELSE {$championship_name = '';}
if (isset($_GET["Saison"])) {$season = $_GET["Saison"];} ELSE {$season = 0;}
if (isset($_GET["Kategorie"])) {$category = $_GET["Kategorie"];} ELSE {$category = -1;}
if (isset($_GET["races"])) {$race_id_global = $_GET["races"];} ELSE {$race_id_global = 0;}
?>
<p>
<table border="2" cellspacing="2">
<tr>
<td>
<?php
print '<h3>Fahrerplatzierungen '.$season.'</h3>';

print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT>Pos</FONT></TH>';
	print'<TH align="left"><FONT>Driver</FONT></TH>';
	include("verbindung.php");
	$query1 = "SELECT race_results.Finish, ColorCode
		FROM championship INNER JOIN races on races.ID = championship.RaceID LEFT JOIN race_results on race_results.RaceID = races.ID LEFT JOIN race_result_colors on (race_result_colors.Finish = race_results.Finish)
		WHERE (championship.Saison = $season) and (championship.Bezeichnung = '$championship_name') and (championship.Kategorie = $category or championship.Kategorie = 0) and (championship.RaceID <= $race_id_global or $race_id_global = 0)
		AND race_results.Finish > 0
		GROUP BY race_results.Finish
		ORDER BY championship.Saison, race_results.Finish";
	$recordset1 = $database_connection->query($query1);
	while ($result = $recordset1->fetch_assoc())
	{
	$position_color= 'white';
	$position_color= $result['ColorCode'];
	$finish_pos = $result['Finish'];
	print "<TH bgcolor=$position_color>".$finish_pos."</TH>";
	}
	print'<TH><FONT>Avg. Finish</FONT></TH>';
print '</TR>';

include("verbindung.php");
$query0 = "SELECT TT.Bezeichnung, TT.Saison, TT.Kategorie, TT.DriverID, drivers.Display_Name, GROUP_CONCAT(LPAD(TT.Finish, 2, '0') ORDER BY TT.Finish) AS Platzierungen, AVG(TT.Finish) AS AvgFinish
	FROM (
	SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.DriverID, race_results.Finish AS Finish
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID, race_results.Finish 
	) AS TT INNER JOIN drivers ON drivers.ID = TT.DriverID
	GROUP BY TT.Saison, TT.Bezeichnung, TT.Kategorie, TT.DriverID, drivers.Display_Name
	ORDER BY TT.Saison, TT.Bezeichnung, TT.Kategorie, AvgFinish, Platzierungen";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$avgfinish = number_format($row['AvgFinish'], 2);
$driverID = $row['DriverID'];

print'<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT ><a href='../driver/driver.php?ID=".$driverID."'>".$row['Display_Name'].'</a></FONT></TD>';
	include("verbindung.php");
	$query1 = " SELECT MAX(TempTable.DriverID) AS DriverID, Finish, ColorCode, MAX(TempTable.CountPosition) AS CountPosition FROM 
		(
		SELECT COUNT(race_results.RaceID) AS CountPosition, race_results.DriverID, race_results.Finish, race_result_colors.ColorCode AS ColorCode
		FROM race_results INNER JOIN championship on championship.RaceID = race_results.RaceID INNER JOIN race_result_colors on (race_result_colors.Finish = race_results.Finish)
		WHERE (championship.Saison = $season) and (championship.Bezeichnung = '$championship_name') AND (championship.Kategorie = $category OR championship.Kategorie = 0 OR $category = -1) AND (championship.RaceID <= $race_id_global or $race_id_global = 0)
		AND (race_results.DriverID = $driverID)
		GROUP BY championship.Saison, championship.Bezeichnung, race_results.Finish, race_result_colors.ColorCode, race_results.DriverID
		UNION ALL
		SELECT 0 AS CountPosition, 0 AS DriverID, race_results.Finish, race_result_colors.ColorCode AS ColorCode
		FROM race_results INNER JOIN championship on championship.RaceID = race_results.RaceID INNER JOIN race_result_colors on (race_result_colors.Finish = race_results.Finish)
		WHERE (championship.Saison = $season) and (championship.Bezeichnung = '$championship_name') AND (championship.Kategorie = $category OR championship.Kategorie = 0 OR $category = -1) AND (championship.RaceID <= $race_id_global or $race_id_global = 0)
		GROUP BY championship.Saison, championship.Bezeichnung, race_results.Finish, race_result_colors.ColorCode
		) AS TempTable
		GROUP BY Finish, ColorCode
		ORDER BY Finish, MAX(CountPosition)";
	$recordset1 = $database_connection->query($query1);
	while ($result = $recordset1->fetch_assoc())
	{
		$CountPosition = $result['CountPosition'];
		$result_color = $result['ColorCode'];
		if ($CountPosition > 0) {
			print "<TD bgcolor=$result_color align='center'><B>".$CountPosition."</B></TD>";
		} else {
			print "<TD bgcolor=$result_color align='center'>".$CountPosition."</TD>";
		}
	}
	print'<TD><FONT >'.$avgfinish.'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</td>
</tr>
</table>
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
print '</p>';
?>
</p>
</body>
</html>
