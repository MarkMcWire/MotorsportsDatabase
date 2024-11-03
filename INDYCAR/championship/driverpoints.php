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
	$query1 = "SELECT races.ID, races.Datum as Datum, coalesce(championship.Saison, 0) as Saison, races.Event, round(races.Runden * races.Length,2) as Distanz, races.Runden as Runden, tracks.ID as TrackID, tracks.Kennzeichen as StreckenKz, tracks.Bezeichnung as Rennstrecke, races.Length, track_type.Type, track_type.ColorCode
		FROM championship INNER JOIN races on races.ID = championship.RaceID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID
		WHERE (championship.Saison = $season) and (championship.Bezeichnung = '$championship_name') and (championship.Kategorie = $category or championship.Kategorie = 0) and (championship.RaceID <= $race_id_global or $race_id_global = 0)
		ORDER BY championship.Saison, races.ID";
	$recordset1 = $database_connection->query($query1);
	while ($result = $recordset1->fetch_assoc())
	{
	$track_color= 'white';
	$track_color= $result['ColorCode'];
	$raceID = $result['ID'];
	print "<TH bgcolor= $track_color><a href='../championship/raceresult.php?ID=".$raceID."&Champ=".$championship_name."'>".$result['StreckenKz']."</a></TH>";
	}
	print'<TH><FONT>Points</FONT></TH>';
print '</TR>';

include("verbindung.php");
$query0 = "SELECT TT.Bezeichnung, TT.Saison, TT.Kategorie, TT.DriverID, drivers.Display_Name, (SUM(TT.Punkte) + SUM(TT.Bonuspunkte)) AS Punkte, SUM(TT.Bonuspunkte) AS Bonuspunkte, GROUP_CONCAT(LPAD(TT.Finish, 2, '0') ORDER BY TT.Finish) AS Platzierungen
	FROM (
	SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, race_results.Finish AS Finish, 0 AS Punkte, 0 AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID, race_results.Finish 
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, SUM(championship_scoring.Rank_Points) + SUM(championship_scoring.Sprint_Points) + SUM(championship_scoring.Stage_Points) + SUM(championship_scoring.Qualification_Points) AS Rennpunkte, SUM(championship_scoring.Bonus_Points) AS Bonuspunkte
	FROM race_results LEFT JOIN championship ON championship.RaceID = race_results.RaceID LEFT JOIN championship_scoring on championship_scoring.ChampionshipID = championship.ID and championship_scoring.DriverID = race_results.DriverID
	WHERE (championship.Bezeichnung LIKE '".$championship_name."') AND (championship.Saison = ".$season.") AND (championship.Kategorie = ".$category.")
	GROUP BY championship.Bezeichnung, championship.Saison, championship.Kategorie, race_results.RaceID, race_results.DriverID
	UNION ALL SELECT championship.Bezeichnung, championship.Saison, championship.Kategorie,
	race_results.DriverID, NULL AS Finish, 0 AS Punkte, SUM(penalties.Points) AS Bonuspunkte
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
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$total_points = $row['Punkte'];
$driverID = $row['DriverID'];

print'<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT ><a href='../driver/driver.php?ID=".$driverID."'>".$row['Display_Name'].'</a></FONT></TD>';
	include("verbindung.php");
	$query1 = "SELECT DriverID, raceID, max(Start) as Start, max(Finish) as Finish, max(Led) as Led, max(MLL) as MLL, MAX(FRL) AS FRL, max(MPG) as MPG, max(Status) as Status, SUM(Points) AS Points, max(ColorCode) AS ColorCode FROM
		(SELECT race_results.DriverID as DriverID, races.ID as raceID, race_results.Start as Start, race_results.Finish as Finish, race_results.Led * races.Length as Led, race_results.MostLapsLed as MLL, race_results.FastestRaceLap as FRL, race_results.MostPositionsGained as MPG, race_results.Status, 
		SUM(championship_scoring.Rank_Points) + SUM(championship_scoring.Sprint_Points) + SUM(championship_scoring.Stage_Points) + SUM(championship_scoring.Qualification_Points) + SUM(championship_scoring.Bonus_Points) AS Points, 
		IF(race_results.DNF = 1, '#EFCFFF', IF(SUM(championship_scoring.Rank_Points) = 0, '#CFCFFF', IF(race_results.Finish > 5, '#CFEAFF', race_result_colors.ColorCode))) AS ColorCode
		FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN race_results on race_results.RaceID = races.ID LEFT JOIN race_result_colors on (race_result_colors.Finish = race_results.Finish)
		LEFT JOIN championship_scoring on championship_scoring.ChampionshipID = championship.ID and championship_scoring.DriverID = race_results.DriverID
		WHERE (championship.Saison = $season) and (championship.Bezeichnung = '$championship_name') AND (championship.Kategorie = $category OR championship.Kategorie = 0 OR $category = -1) AND (race_results.DriverID = $driverID) AND (championship.RaceID <= $race_id_global or $race_id_global = 0)
		GROUP BY races.ID, races.Length, championship.Saison, championship.Bezeichnung, races.Datum, race_results.DriverID, race_results.Start, race_results.Finish, race_results.Led, race_results.MostLapsLed, race_results.FastestRaceLap, race_results.MostPositionsGained, race_results.Status, race_result_colors.ColorCode
		UNION ALL
		SELECT race_results.DriverID as DriverID, races.ID as raceID, race_results.Start as Start, race_results.Finish as Finish, race_results.Led * races.Length as Led, race_results.MostLapsLed as MLL, race_results.FastestRaceLap as FRL, race_results.MostPositionsGained as MPG, race_results.Status, SUM(penalties.Points) AS Points, '#00FFFF' AS ColorCode
		FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN race_results on race_results.RaceID = races.ID LEFT JOIN race_result_colors on (race_result_colors.Finish = race_results.Finish)
		LEFT JOIN penalties ON penalties.RaceID = races.ID
		WHERE (championship.Saison = $season) and (championship.Bezeichnung = '$championship_name') AND (championship.Kategorie = $category OR championship.Kategorie = 0 OR $category = -1) AND (race_results.DriverID = $driverID) AND (championship.RaceID <= $race_id_global or $race_id_global = 0)
		AND (penalties.DriverID = race_results.DriverID)
		GROUP BY races.ID, championship.Saison, championship.Bezeichnung, races.Datum, race_results.DriverID, race_results.Start, race_results.Finish, race_results.Status, race_result_colors.ColorCode
		UNION ALL
		SELECT $driverID as DriverID, races.ID as raceID, 0 as Start, 0 as Finish, 0 as Led, 0 as MLL, 0 as FRL, 0 as MPG, '' as Status, 0 as Points, '' as ColorCode
		FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN race_results on race_results.RaceID = races.ID
		WHERE (championship.Saison = $season) and (championship.Bezeichnung = '$championship_name') AND (championship.Kategorie = $category OR championship.Kategorie = 0 OR $category = -1) and (championship.RaceID <= $race_id_global or $race_id_global = 0)
		GROUP BY races.ID, championship.Saison, championship.Bezeichnung, races.Datum
		) as temptable GROUP BY DriverID, raceID ORDER BY raceID, Finish";
	$recordset1 = $database_connection->query($query1);
	while ($result = $recordset1->fetch_assoc())
	{
	$points = ROUND($result['Points']);
	$finish = $result['Finish'];
	$start = $result['Start'];
	$mll = $result['MLL'];
	$frl = $result['FRL'];
	$status = $result['Status'];
	$result_color = $result['ColorCode'];;
	if ($finish < 1 or $finish > 99) {$result_color= 'white'; $points = '';}
	if ($mll == 1 && $frl == 1 && $start >= 0) {print "<TD bgcolor= $result_color align='center'><B><I>".$points."</I></B></TD>";}
	if ($mll == 1 && $frl == 0 && $start >= 0) {print "<TD bgcolor= $result_color align='center'><B>".$points."</B></TD>";}
	if ($mll == 0 && $frl == 1 && $start >= 0) {print "<TD bgcolor= $result_color align='center'><I>".$points."</I></TD>";}
	if ($mll == 0 && $frl == 0 && $start >= 0) {print "<TD bgcolor= $result_color align='center'>".$points."</TD>";}
	if ($start < 0) {print "<TD bgcolor='white' align='center'></TD>";}
	}
	print'<TD><FONT >'.ROUND($total_points).'</FONT></TD>';
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
