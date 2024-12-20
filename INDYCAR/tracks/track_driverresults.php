<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["Strecke"])) {$track_name = $_GET["Strecke"];} ELSE {$track_name = 0;}
if (isset($_GET["ID"])) {$trackID = $_GET["ID"];} ELSE {$trackID = $track;}
if (isset($_GET["Champ"])) {$championship_name_global = $_GET["Champ"];} ELSE {$championship_name_global = '';}
?>
<p>
<table border="2" cellspacing="2">
<tr>
<td>
<?php
include("verbindung.php");
$query = "SELECT tracks.ID as TrackID, tracks.Bezeichnung, tracks.Ort, tracks.Land, tracks.Kennzeichen as StreckenKz, tracks.Eroeffnung, tracks.Schliessung as Einstellung, MAX(races.Length) as Length, track_type.Type, track_type.ColorCode
FROM tracks LEFT JOIN races on races.TrackID = tracks.ID LEFT JOIN track_type on track_type.ID = races.TypeID LEFT JOIN championship on races.ID = championship.RaceID
WHERE (tracks.ID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY tracks.ID, tracks.Bezeichnung, tracks.Ort, tracks.Land, tracks.Kennzeichen, tracks.Eroeffnung, tracks.Schliessung, track_type.ID, track_type.Type, track_type.ColorCode
ORDER BY track_type.ID, tracks.Bezeichnung";
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print '<TH><FONT>Pos</FONT></TH>';
	print '<TH align="left"><FONT>Fahrer</FONT></TH>';
	print '<TH><FONT>Punkte</FONT></TH>';
	print '<TH><FONT></FONT></TH>';
	include("verbindung.php");
	$query1 = "SELECT races.ID, races.Datum, coalesce(championship.Saison, 0) as Saison, tracks.ID as TrackID, tracks.Kennzeichen as StreckenKz, track_type.ColorCode
		FROM races LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN championship on races.ID = championship.RaceID LEFT JOIN track_type on track_type.ID = races.TypeID
		WHERE (tracks.ID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
		GROUP BY championship.Saison, races.ID, races.Datum, tracks.ID, tracks.Kennzeichen, track_type.ColorCode ORDER BY races.ID";
	$recordset1 = $database_connection->query($query1);
	while ($result = $recordset1->fetch_assoc()) {
		$result_color = 'white';
		$result_color = $result['ColorCode'];
		$trackid = $result['TrackID'];
		print "<TH bgcolor=$result_color colspan='2'>".$result['Saison']."</TH>";
	}
print '</TR>';

include("verbindung.php");
$query0 = "SELECT DriverID, Display_Name, Gesamtwertung, Platzierungen FROM (
SELECT drivers.ID as DriverID, drivers.Display_Name,
ROUND(MAX(championship.Cars) - AVG(race_results.Finish)) + 10 * SUM(race_results.Finish = 1) + SUM(race_results.MostLapsLed) + SUM(race_results.FastestRaceLap) + SUM(race_results.Start = 1) as Gesamtwertung, 
GROUP_CONCAT(LPAD(race_results.Finish, 2, '0') ORDER BY race_results.Finish) AS Platzierungen
FROM race_results
LEFT JOIN races on race_results.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN drivers on race_results.DriverID = drivers.ID LEFT JOIN championship on races.ID = championship.RaceID
WHERE (races.TrackID = $trackID or $trackID = 0) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
GROUP BY drivers.ID, drivers.Display_Name, championship.Bezeichnung) AS TempTable
GROUP BY DriverID, Display_Name, Gesamtwertung, Platzierungen
ORDER BY Gesamtwertung DESC, Platzierungen";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc()) {
	$i = $i + 1;
	$points = $row['Gesamtwertung'];
	$driverID = $row['DriverID'];

	print'<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT ><a href='../driver/driver.php?ID=".$driverID."'>".$row['Display_Name'].'</a></FONT></TD>';
	print'<TD><FONT >'.$points.'</FONT></TD>';
	print'<TD><FONT >'.'|'.'</FONT></TD>';
	include("verbindung.php");
	$query1 = "SELECT DriverID, raceID, max(Start) as Start, max(Finish) as Finish, max(Led) as Led, max(MLL) as MLL, MAX(FRL) AS FRL, max(MPG) as MPG, max(Status) as Status, max(ColorCode) AS ColorCode FROM
		(SELECT race_results.DriverID as DriverID, races.ID as raceID, race_results.Start as Start, race_results.Finish as Finish, race_results.Led * races.Length as Led, race_results.MostLapsLed as MLL, race_results.FastestRaceLap as FRL, race_results.MostPositionsGained as MPG, race_results.Status, 
		IF(race_results.DNF = 1, '#EFCFFF', race_result_colors.ColorCode) AS ColorCode
		FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN race_results on race_results.RaceID = races.ID LEFT JOIN race_result_colors on (race_result_colors.Finish = race_results.Finish)
		WHERE (race_results.DriverID = $driverID or $driverID = 0) AND (tracks.ID = $trackID) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '')
		GROUP BY races.ID, championship.Saison, championship.Bezeichnung, races.Datum, race_results.DriverID, race_results.Start, race_results.Finish, race_results.Status, race_result_colors.ColorCode
		UNION ALL
		SELECT $driverID as DriverID, races.ID as raceID, 0 as Start, 0 as Finish, 0 as Led, 0 as MLL, 0 as FRL, 0 as MPG, '' as Status, '' as ColorCode
		FROM races INNER JOIN championship on championship.RaceID = races.ID LEFT JOIN tracks on races.TrackID = tracks.ID LEFT JOIN race_results on race_results.RaceID = races.ID
		WHERE (tracks.ID = $trackID) AND (championship.Bezeichnung = '$championship_name_global' or '$championship_name_global' = '') 
		GROUP BY races.ID, championship.Saison, championship.Bezeichnung, races.Datum
		) as temptable GROUP BY DriverID, raceID ORDER BY raceID, Finish";
	$recordset1 = $database_connection->query($query1);
	while ($result = $recordset1->fetch_assoc()) {
		$finish = $result['Finish'];
		$start = $result['Start'];
		$mll = $result['MLL'];
		$frl = $result['FRL'];
		$status = $result['Status'];
		$bonus = '';
		if ($start == 1) {$bonus = $bonus.'P'; };
		if ($mll > 0) {$bonus = $bonus.'L'; };
		if ($frl > 0) {$bonus = $bonus.'F'; };
		$result_color = $result['ColorCode'];
		if ($finish > 0 and $finish < 100 and strlen($bonus) > 0) 
			{
				print "<TD bgcolor=$result_color align='center'>".$finish."</TD>";
				print "<TD bgcolor=$result_color align='center'>".$bonus."</TD>";
			} 
		elseif ($finish > 0 and $finish < 100)
			{
				print "<TD bgcolor=$result_color align='center' colspan='2'>".$finish."</TD>";
			}
		else 
			{
				print "<TD bgcolor='white' align='center' colspan='2'></TD>";
			}
	}
	print'</TR>';
}
?>
</TABLE>
</td>
</tr>
</table>
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
