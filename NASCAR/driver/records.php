<!DOCTYPE html>
<html lang="en">
<HEAD>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</HEAD>
<BODY>
<FONT face="Times New Roman">
<HR>
<H2>Fahrerrekorde</H2>
<p>
<?php
include("verbindung.php");
$query = "SELECT coalesce(count(distinct race_results.RaceID),0) as Events, coalesce(sum(race_results.Laps),0) as Laps, coalesce(sum(race_results.Laps * races.Length),0) as Miles, MAX(LEFT(race_results.RaceID,4)) AS MaxSeason
FROM race_results
LEFT JOIN races on race_results.RaceID = races.ID
LEFT JOIN championship on championship.RaceID = races.ID
LEFT JOIN tracks on races.TrackID = tracks.ID
WHERE race_results.Finish = 1";
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
$events = $result['Events'];
$maxseason = $result['MaxSeason'];
print'<H3>Results after '.$events.' events</H3>';
?>
<TABLE>
<TR>
<TD>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH><FONT >Pos</FONT></TH>
	<TH align="left"><FONT >drivers</FONT></TH>
	<TH><FONT >Teil-<br>nahmen</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, COUNT(race_results.Finish) as Events
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
GROUP BY drivers.ID
ORDER BY Events DESC, drivers";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$driverID = $row['DriverID'];
include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['MaxSeason']< $maxseason) {$driver_color = 'darkgrey';}
else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$row['drivers'].'</a></FONT></TD>';
	print'<TD><FONT >'.$row['Events'].'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</TD>
<TD>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH><FONT >Pos</FONT></TH>
	<TH align="left"><FONT >drivers</FONT></TH>
	<TH><FONT >Runden</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, sum(race_results.laps) as Laps
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.laps > 0
GROUP BY drivers.ID
ORDER BY Laps DESC";
$recordset0 = $database_connection->query($query0);
include("verbindung.php");
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$driverID = $row['DriverID'];
include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['MaxSeason']< $maxseason) {$driver_color = 'darkgrey';}
else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$row['drivers'].'</a></FONT></TD>';
	print'<TD><FONT >'.$row['Laps'].'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</TD>
<TD>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH><FONT >Pos</FONT></TH>
	<TH align="left"><FONT >drivers</FONT></TH>
	<TH><FONT >Führerungs-<br>runden</FONT></TH>
	<TH><FONT >%</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, sum(race_results.laps) as Laps, sum(race_results.led) as Led
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.laps > 0 and race_results.led > 0
GROUP BY drivers.ID
ORDER BY Led DESC;";
$recordset0 = $database_connection->query($query0);
include("verbindung.php");
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$driverID = $row['DriverID'];
include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['Differenz'] >0) {$driver_color = 'darkgrey';}
else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$row['drivers'].'</a></FONT></TD>';
	print'<TD><FONT >'.$row['Led'].'</FONT></TD>';
	if ($row['Laps'] > 0) {
		print'<TD><FONT >'.round(100*$row['Led']/$row['Laps'],2).'</FONT></TD>';
	} else {
		print'<TD><FONT >'.round(0,2).'</FONT></TD>';
	}
print'</TR>';
}
?>
</TABLE>
</TD>
<TD>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH><FONT >Pos</FONT></TH>
	<TH align="left"><FONT >drivers</FONT></TH>
	<TH><FONT >Siege</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, COUNT(race_results.Finish) as Siege
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.Finish = 1
GROUP BY drivers.ID
ORDER BY Siege DESC";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$driverID = $row['DriverID'];
include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['MaxSeason']< $maxseason) {$driver_color = 'darkgrey';}
else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$row['drivers'].'</a></FONT></TD>';
	print'<TD><FONT >'.$row['Siege'].'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</TD>
<TD>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH><FONT >Pos</FONT></TH>
	<TH align="left"><FONT >drivers</FONT></TH>
	<TH><FONT >Top5</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, COUNT(race_results.Finish) as Siege
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.Finish <= 5
GROUP BY drivers.ID
ORDER BY Siege DESC";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$driverID = $row['DriverID'];
include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['MaxSeason']< $maxseason) {$driver_color = 'darkgrey';}
else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$row['drivers'].'</a></FONT></TD>';
	print'<TD><FONT >'.$row['Siege'].'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</TD>
<TD>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH><FONT >Pos</FONT></TH>
	<TH align="left"><FONT >drivers</FONT></TH>
	<TH><FONT >Top10</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, COUNT(race_results.Finish) as Siege
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.Finish <= 10
GROUP BY drivers.ID
ORDER BY Siege DESC";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$driverID = $row['DriverID'];
include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['MaxSeason']< $maxseason) {$driver_color = 'darkgrey';}
else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$row['drivers'].'</a></FONT></TD>';
	print'<TD><FONT >'.$row['Siege'].'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</TD>
<TD>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH><FONT >Pos</FONT></TH>
	<TH align="left"><FONT >drivers</FONT></TH>
	<TH><FONT >Poles</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, COUNT(race_results.Finish) as Poles
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.Start = 1
GROUP BY drivers.ID
ORDER BY Poles DESC";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$driverID = $row['DriverID'];
include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['MaxSeason']< $maxseason) {$driver_color = 'darkgrey';}
else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$row['drivers'].'</a></FONT></TD>';
	print'<TD><FONT >'.$row['Poles'].'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</TD>
<TD>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH><FONT >Pos</FONT></TH>
	<TH align="left"><FONT >drivers</FONT></TH>
	<TH><FONT >MLLs</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, COUNT(race_results.Finish) as MLLs
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.MostLapsLed
GROUP BY drivers.ID
ORDER BY MLLs DESC";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$driverID = $row['DriverID'];
include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['MaxSeason']< $maxseason) {$driver_color = 'darkgrey';}
else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$row['drivers'].'</a></FONT></TD>';
	print'<TD><FONT >'.$row['MLLs'].'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</TD>
<TD>
<TABLE border=1 cellpadding=3 cellspacing=0>
<TR>
	<TH><FONT >Pos</FONT></TH>
	<TH align="left"><FONT >drivers</FONT></TH>
	<TH><FONT >MPGs</FONT></TH>
</TR>
<?php
include("verbindung.php");
$query0 = "SELECT drivers.Name as drivers, drivers.ID as DriverID, COUNT(race_results.Finish) as MPGs
FROM race_results
INNER JOIN races on race_results.RaceID = races.ID
INNER JOIN drivers on race_results.DriverID = drivers.ID
WHERE race_results.MostPositionsGained
GROUP BY drivers.ID
ORDER BY MPGs DESC";
$recordset0 = $database_connection->query($query0);
$i = 0;
while ($row = $recordset0->fetch_assoc())
{
$i = $i + 1;
$driverID = $row['DriverID'];
include("verbindung.php");
$query3 = "SELECT MAX(LEFT(RaceID, 4)) AS MaxSeason FROM race_results WHERE race_results.DriverID = $driverID";
$recordset3 = $database_connection->query($query3);
$result3 = $recordset3->fetch_assoc();
$driver_color = 'white';
if ($result3['MaxSeason']< $maxseason) {$driver_color = 'darkgrey';}
else {$driver_color = 'lightgrey';}

print"<TR bgcolor ='$driver_color'>";
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$row['drivers'].'</a></FONT></TD>';
	print'<TD><FONT >'.$row['MPGs'].'</FONT></TD>';
print'</TR>';
}
?>
</TABLE>
</TD>
</TR>
</TABLE>
</p>
<HR>
<p><STRONG><a href="../index.php">zur&uuml;ck zur &Uuml;bersicht</a></STRONG></p>
</FONT>
</BODY>
</HTML>
