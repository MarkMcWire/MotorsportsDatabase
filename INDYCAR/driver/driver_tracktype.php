<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["ID"])) {$driverID = $_GET["ID"]; $query = "SELECT * FROM drivers WHERE (ID = $driverID) ORDER BY ID";} ELSE {$query = "SELECT * FROM drivers ORDER BY ID";}
include("verbindung.php");
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
#if(!$row)die("Keine Ergebnisse <br/>");

$ID = $result['ID'];
$name = $result['Name'];
if ($ID > 1) {
	print '<p>';
	print "<h2 align='center'>$name</h2>";
	print "</p>";
}

if (isset($_GET["Type"])) {$track_sortcat = $_GET["Type"];} ELSE {$track_sortcat = 0;}
$query = "SELECT track_type.Type, track_type.Surface, track_type.ID AS SortCat, track_type.ColorCode 
FROM tracks INNER JOIN races on races.TrackID = tracks.ID INNER JOIN race_results on race_results.RaceID = races.ID LEFT JOIN track_type on track_type.ID = races.TypeID
WHERE (race_results.DriverID = $ID or $ID < 2)
GROUP BY track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0) ORDER BY track_type.ID";
?>
<p>
<table border="2" cellspacing="2">
<tr>
<td>
<h3>&Uuml;bersicht Fahrererfolge nach Streckentyp</h3>
<br/>
</td>
</tr>
<tr>
<td>
<h3>Siege</h3>
<?php
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT size="2">Pos</FONT></TH>';
	print'<TH align="left"><FONT size="2">Fahrer</FONT></TH>';
	print'<TH><FONT size="2">Siege</FONT></TH>';
	include("verbindung.php");
	$recordset = $database_connection->query($query);
	while ($result = $recordset->fetch_assoc())
	{
	$track_color = 'white';
	$track_color = $result['ColorCode'];
	$track_type = $result['Type'];
	$track_surface = $result['Surface'];
	$track_cat = $result['SortCat'];
	print "<TH bgcolor= $track_color><font size='2'><a href='?ID=".$ID."&Type=".$track_cat."'>".$track_type."<br>".$track_surface."</a></font></TH>";
	}
print '</TR>';

include("verbindung.php");
$query1 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, drivers.Kategorie, COUNT(race_results.Finish) as Siege
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.Finish = 1 and drivers.ID = $ID
GROUP BY drivers.Name, drivers.ID, drivers.Kategorie
ORDER BY Siege DESC";
$recordset1 = $database_connection->query($query1);
$i = 0;
while ($row1 = $recordset1->fetch_assoc())
{
$i = $i + 1;
$driverID = $row1['DriverID'];

print'<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$row1['drivers'].'</a></FONT></TD>';
	print'<TD><FONT><b>'.$row1['Siege'].'</b></FONT></TD>';
	include("verbindung.php");
	$query2 = "SELECT SortCat, Type, Surface, ColorCode, max(DriverID), sum(Siege) as Siege
	FROM (
	SELECT track_type.ID as SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, race_results.DriverID as DriverID, COUNT(race_results.Finish) as Siege
	FROM race_results INNER JOIN races on race_results.RaceID = races.ID INNER JOIN tracks on tracks.ID = races.TrackID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (race_results.DriverID = $driverID) and (race_results.Finish = 1)
	GROUP BY races.TrackID, track_type.Type, track_type.Surface, track_type.ID, track_type.ColorCode, race_results.DriverID HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0)
	UNION ALL
	SELECT track_type.ID as SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, 0 as DriverID, 0 as Siege
	FROM race_results INNER JOIN races on race_results.RaceID = races.ID INNER JOIN tracks on tracks.ID = races.TrackID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (race_results.DriverID = $driverID)
	GROUP BY tracks.ID, track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0)
	) as temp
	GROUP BY SortCat, Type, Surface, ColorCode ORDER BY SortCat";
	$recordset2 = $database_connection->query($query2);
	while ($result2 = $recordset2->fetch_assoc())
	{
	$finish = $result2['Siege'];
	$track_color = $result2['ColorCode'];
	if ($finish > 0) {print "<TD bgcolor= $track_color align='center'><b>".$finish."</b></TD>";}
	else {print "<TD bgcolor='white' align='center'>".$finish."</TD>";}
	}
print'</TR>';
}
?>
</TABLE>
</td>
</tr>
<tr>
<td>
<h3>Poles</h3>
<?php
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT size="2">Pos</FONT></TH>';
	print'<TH align="left"><FONT size="2">Fahrer</FONT></TH>';
	print'<TH><FONT size="2">Siege</FONT></TH>';
	include("verbindung.php");
	$recordset = $database_connection->query($query);
	while ($result = $recordset->fetch_assoc())
	{
	$track_color = 'white';
	$track_color = $result['ColorCode'];
	$track_type = $result['Type'];
	$track_surface = $result['Surface'];
	$track_cat = $result['SortCat'];
	print "<TH bgcolor= $track_color><font size='2'><a href='?ID=".$ID."&Type=".$track_cat."'>".$track_type."<br>".$track_surface."</a></font></TH>";
	}
print '</TR>';

include("verbindung.php");
$query3 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, drivers.Kategorie, COUNT(race_results.Start) as Poles
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.Start = 1 and drivers.ID = $ID
GROUP BY drivers.Name, drivers.ID, drivers.Kategorie
ORDER BY Poles DESC";
$recordset3 = $database_connection->query($query3);
$i = 0;
while ($row3 = $recordset3->fetch_assoc())
{
$i = $i + 1;
$driverID = $row3['DriverID'];

print'<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='../driver/driver.php?ID=".$driverID."'>".$row3['drivers'].'</a></FONT></TD>';
	print'<TD><FONT><b>'.$row3['Poles'].'</b></FONT></TD>';
	include("verbindung.php");
	$query4 = "SELECT SortCat, Type, Surface, ColorCode, max(DriverID), sum(Siege) as Poles
	FROM (
	SELECT track_type.ID as SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, race_results.DriverID as DriverID, COUNT(race_results.Finish) as Siege
	FROM race_results INNER JOIN races on race_results.RaceID = races.ID INNER JOIN tracks on tracks.ID = races.TrackID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (race_results.DriverID = $driverID or $driverID = 0) and (race_results.Start = 1)
	GROUP BY races.TrackID, track_type.Type, track_type.Surface, track_type.ID, track_type.ColorCode, race_results.DriverID HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0)
	UNION ALL
	SELECT track_type.ID as SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, 0 as DriverID, 0 as Siege
	FROM race_results INNER JOIN races on race_results.RaceID = races.ID INNER JOIN tracks on tracks.ID = races.TrackID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (race_results.DriverID = $driverID)
	GROUP BY tracks.ID, track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0)
	) as temp
	GROUP BY SortCat, Type, Surface, ColorCode ORDER BY SortCat";
	$recordset4 = $database_connection->query($query4);
	while ($result4 = $recordset4->fetch_assoc())
	{
	$start = $result4['Poles'];
	$track_color = $result4['ColorCode'];
	if ($start > 0) {print "<TD bgcolor= $track_color align='center'><b>".$start."</b></TD>";}
	else {print "<TD bgcolor='white' align='center'>".$start."</TD>";}
	}
print'</TR>';
}
?>
</TABLE>
</td>
</tr>
<tr>
<td>
<h3>Meiste F&uuml;hrungsrunden</h3>
<?php
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT size="2">Pos</FONT></TH>';
	print'<TH align="left"><FONT size="2">Fahrer</FONT></TH>';
	print'<TH><FONT size="2">MLLs</FONT></TH>';
	include("verbindung.php");
	$recordset = $database_connection->query($query);
	while ($result = $recordset->fetch_assoc())
	{
	$track_color = 'white';
	$track_color = $result['ColorCode'];
	$track_type = $result['Type'];
	$track_surface = $result['Surface'];
	$track_cat = $result['SortCat'];
	print "<TH bgcolor= $track_color><font size='2'><a href='?ID=".$ID."&Type=".$track_cat."'>".$track_type."<br>".$track_surface."</a></font></TH>";
	}
print '</TR>';

include("verbindung.php");
$query5 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, drivers.Kategorie, COUNT(race_results.Finish) as MLLs
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.MostLapsLed = 1 and drivers.ID = $ID
GROUP BY drivers.Name, drivers.ID, drivers.Kategorie
ORDER BY MLLs DESC";
$recordset5 = $database_connection->query($query5);
$i = 0;
while ($row5 = $recordset5->fetch_assoc())
{
$i = $i + 1;
$driverID = $row5['DriverID'];

print'<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='../driver/driver.php?ID=".$driverID."'>".$row5['drivers'].'</a></FONT></TD>';
	print'<TD><FONT><b>'.$row5['MLLs'].'</b></FONT></TD>';
	include("verbindung.php");
	$query6 = "SELECT SortCat, Type, Surface, ColorCode, max(DriverID), sum(Siege) as MLLs
	FROM (
	SELECT track_type.ID as SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, race_results.DriverID as DriverID, COUNT(race_results.Finish) as Siege
	FROM race_results INNER JOIN races on race_results.RaceID = races.ID INNER JOIN tracks on tracks.ID = races.TrackID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (race_results.DriverID = $driverID or $driverID = 0) and (race_results.MostLapsLed = 1)
	GROUP BY races.TrackID, track_type.Type, track_type.Surface, track_type.ID, track_type.ColorCode, race_results.DriverID HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0)
	UNION ALL
	SELECT track_type.ID as SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, 0 as DriverID, 0 as Siege
	FROM race_results INNER JOIN races on race_results.RaceID = races.ID INNER JOIN tracks on tracks.ID = races.TrackID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (race_results.DriverID = $driverID)
	GROUP BY tracks.ID, track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0)
	) as temp
	GROUP BY SortCat, Type, Surface, ColorCode ORDER BY SortCat";
	$recordset6 = $database_connection->query($query6);
	while ($result6 = $recordset6->fetch_assoc())
	{
	$mlls = $result6['MLLs'];
	$track_color = $result6['ColorCode'];
	if ($mlls > 0) {print "<TD bgcolor= $track_color align='center'><b>".$mlls."</b></TD>";}
	else {print "<TD bgcolor='white' align='center'>".$mlls."</TD>";}
	}
print'</TR>';
}
?>
</TABLE>
</td>
</tr>
<tr>
<td>
<h3>Schnellste Rennrunden</h3>
<?php
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT size="2">Pos</FONT></TH>';
	print'<TH align="left"><FONT size="2">Fahrer</FONT></TH>';
	print'<TH><FONT size="2">FRLs</FONT></TH>';
	include("verbindung.php");
	$recordset = $database_connection->query($query);
	while ($result = $recordset->fetch_assoc())
	{
	$track_color = 'white';
	$track_color = $result['ColorCode'];
	$track_type = $result['Type'];
	$track_surface = $result['Surface'];
	$track_cat = $result['SortCat'];
	print "<TH bgcolor= $track_color><font size='2'><a href='?ID=".$ID."&Type=".$track_cat."'>".$track_type."<br>".$track_surface."</a></font></TH>";
	}
print '</TR>';

include("verbindung.php");
$query7 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, drivers.Kategorie, COUNT(race_results.Finish) as FRLs
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.FastestRaceLap = 1 and drivers.ID = $ID
GROUP BY drivers.Name, drivers.ID, drivers.Kategorie
ORDER BY FRLs DESC";
$recordset7 = $database_connection->query($query7);
$i = 0;
while ($row7 = $recordset7->fetch_assoc())
{
$i = $i + 1;
$driverID = $row7['DriverID'];

print'<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='../driver/driver.php?ID=".$driverID."'>".$row7['drivers'].'</a></FONT></TD>';
	print'<TD><FONT><b>'.$row7['FRLs'].'</b></FONT></TD>';
	include("verbindung.php");
	$query8 = "SELECT SortCat, Type, Surface, ColorCode, max(DriverID), sum(Siege) as FRLs
	FROM (
	SELECT track_type.ID as SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, race_results.DriverID as DriverID, COUNT(race_results.Finish) as Siege
	FROM race_results INNER JOIN races on race_results.RaceID = races.ID INNER JOIN tracks on tracks.ID = races.TrackID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (race_results.DriverID = $driverID or $driverID = 0) and (race_results.FastestRaceLap = 1)
	GROUP BY races.TrackID, track_type.Type, track_type.Surface, track_type.ID, track_type.ColorCode, race_results.DriverID HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0)
	UNION ALL
	SELECT track_type.ID as SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, 0 as DriverID, 0 as Siege
	FROM race_results INNER JOIN races on race_results.RaceID = races.ID INNER JOIN tracks on tracks.ID = races.TrackID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (race_results.DriverID = $driverID)
	GROUP BY tracks.ID, track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0)
	) as temp
	GROUP BY SortCat, Type, Surface, ColorCode ORDER BY SortCat";
	$recordset8 = $database_connection->query($query8);
	while ($result8 = $recordset8->fetch_assoc())
	{
	$frls = $result8['FRLs'];
	$track_color = $result8['ColorCode'];
	if ($frls > 0) {print "<TD bgcolor= $track_color align='center'><b>".$frls."</b></TD>";}
	else {print "<TD bgcolor='white' align='center'>".$frls."</TD>";}
	}
print'</TR>';
}
?>
</TABLE>
</td>
</tr>
<tr>
<td>
<h3>Meiste Positionen gewonnen</h3>
<?php
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT size="2">Pos</FONT></TH>';
	print'<TH align="left"><FONT size="2">Fahrer</FONT></TH>';
	print'<TH><FONT size="2">MPGs</FONT></TH>';
	include("verbindung.php");
	$recordset = $database_connection->query($query);
	while ($result = $recordset->fetch_assoc())
	{
	$track_color = 'white';
	$track_color = $result['ColorCode'];
	$track_type = $result['Type'];
	$track_surface = $result['Surface'];
	$track_cat = $result['SortCat'];
	print "<TH bgcolor= $track_color><font size='2'><a href='?ID=".$ID."&Type=".$track_cat."'>".$track_type."<br>".$track_surface."</a></font></TH>";
	}
print '</TR>';

include("verbindung.php");
$query7 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, drivers.Kategorie, COUNT(race_results.Finish) as MPGs
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.MostPositionsGained = 1 and drivers.ID = $ID
GROUP BY drivers.Name, drivers.ID, drivers.Kategorie
ORDER BY MPGs DESC";
$recordset7 = $database_connection->query($query7);
$i = 0;
while ($row7 = $recordset7->fetch_assoc())
{
$i = $i + 1;
$driverID = $row7['DriverID'];

print'<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='../driver/driver.php?ID=".$driverID."'>".$row7['drivers'].'</a></FONT></TD>';
	print'<TD><FONT><b>'.$row7['MPGs'].'</b></FONT></TD>';
	include("verbindung.php");
	$query8 = "SELECT SortCat, Type, Surface, ColorCode, max(DriverID), sum(Siege) as MPGs
	FROM (
	SELECT track_type.ID as SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, race_results.DriverID as DriverID, COUNT(race_results.Finish) as Siege
	FROM race_results INNER JOIN races on race_results.RaceID = races.ID INNER JOIN tracks on tracks.ID = races.TrackID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (race_results.DriverID = $driverID or $driverID = 0) and (race_results.MostPositionsGained = 1)
	GROUP BY races.TrackID, track_type.Type, track_type.Surface, track_type.ID, track_type.ColorCode, race_results.DriverID HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0)
	UNION ALL
	SELECT track_type.ID as SortCat, track_type.Type, track_type.Surface, track_type.ColorCode, 0 as DriverID, 0 as Siege
	FROM race_results INNER JOIN races on race_results.RaceID = races.ID INNER JOIN tracks on tracks.ID = races.TrackID LEFT JOIN track_type on track_type.ID = races.TypeID
	WHERE (race_results.DriverID = $driverID)
	GROUP BY tracks.ID, track_type.ID, track_type.Type, track_type.Surface, track_type.ColorCode HAVING (track_type.ID = $track_sortcat or $track_sortcat = 0)
	) as temp
	GROUP BY SortCat, Type, Surface, ColorCode ORDER BY SortCat";
	$recordset8 = $database_connection->query($query8);
	while ($result8 = $recordset8->fetch_assoc())
	{
	$mpgs = $result8['MPGs'];
	$track_color = $result8['ColorCode'];
	if ($mpgs > 0) {print "<TD bgcolor= $track_color align='center'><b>".$mpgs."</b></TD>";}
	else {print "<TD bgcolor='white' align='center'>".$mpgs."</TD>";}
	}
print'</TR>';
}
?>
</TABLE>
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
