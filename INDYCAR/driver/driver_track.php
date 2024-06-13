<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Motorsport Statistik</title>
</head>
<body>
<?php
if (isset($_GET["ID"])) {$driverID = $_GET["ID"]; $query = "SELECT * FROM drivers WHERE (ID = $driverID) ORDER BY Kategorie, Name";} ELSE {$query = "SELECT * FROM drivers ORDER BY Kategorie, Name";}
include("verbindung.php");
$recordset = $database_connection->query($query);
$result = $recordset->fetch_assoc();
#if(!$row)die("Keine Ergebnisse <br/>");

$ID = $result['ID'];
$name = $result['Name'];
if ($ID > 1) {
	print "<p>";
	print "<h2 align='center'>$name</h2>";
	print "</p>";
}
$query_tracks = "SELECT tracks.ID as TrackID, tracks.Kennzeichen as StreckenKz, MAX(races.Length) as Length, races.TypeID, track_type.ColorCode
FROM tracks INNER JOIN races on races.TrackID = tracks.ID INNER JOIN race_results on race_results.RaceID = races.ID LEFT JOIN track_type on track_type.ID = races.TypeID
WHERE race_results.DriverID = $ID
GROUP BY tracks.ID, tracks.Kennzeichen, races.TypeID, track_type.ColorCode
ORDER BY tracks.Kennzeichen, tracks.ID";

$query_wins = "SELECT drivers.Name as drivers, drivers.ID as DriverID, count(distinct tracks.ID) as Siege
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID
INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks on tracks.ID = races.TrackID
WHERE Finish = 1 and drivers.ID = $ID
GROUP BY drivers.Name, drivers.ID 
ORDER BY Siege DESC";

$query_poles = "SELECT drivers.Name as drivers, drivers.ID as DriverID, count(distinct tracks.ID) as Poles
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID
INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks on tracks.ID = races.TrackID
WHERE Start = 1 and drivers.ID = $ID
GROUP BY drivers.Name, drivers.ID 
ORDER BY Poles DESC";

$query_mlls = "SELECT drivers.Name as drivers, drivers.ID as DriverID, count(distinct tracks.ID) as MLLs
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID
INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks on tracks.ID = races.TrackID
WHERE MostLapsLed = 1 and drivers.ID = $ID
GROUP BY drivers.Name, drivers.ID 
ORDER BY MLLs DESC";

$query_frls = "SELECT drivers.Name as drivers, drivers.ID as DriverID, count(distinct tracks.ID) as FRLs
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID
INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks on tracks.ID = races.TrackID
WHERE FastestRaceLap = 1 and drivers.ID = $ID
GROUP BY drivers.Name, drivers.ID 
ORDER BY FRLs DESC";

$query_mpgs = "SELECT drivers.Name as drivers, drivers.ID as DriverID, count(distinct tracks.ID) as MPGs
FROM race_results INNER JOIN drivers on drivers.ID = race_results.DriverID
INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks on tracks.ID = races.TrackID
WHERE MostPositionsGained = 1 and drivers.ID = $ID
GROUP BY drivers.Name, drivers.ID 
ORDER BY MPGs DESC";
//print $query;
?>
<p>
<table border="2" cellspacing="2">
<tr>
<td>
<h3>&Uuml;bersicht Fahrererfolge nach Rennstrecke</h3>
<br/>
</td>
</tr>
<tr>
<td>
<h3>Siege</h3>
<?php
$events = 0;
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT size="2">Pos</FONT></TH>';
	print'<TH align="left"><FONT size="2">Fahrer</FONT></TH>';
	print'<TH><FONT size="2">Total</FONT></TH>';
	include("verbindung.php");
	$recordset = $database_connection->query($query_tracks);
	while ($result = $recordset->fetch_assoc())
	{
	$track_color = 'white';
	$track_color = $result['ColorCode'];
	$trackID = $result['TrackID'];
	$trackTypeID = $result['TypeID'];
	$track_abbreviation = $result['StreckenKz'];
	$events = $events + 1;
	print "<TH bgcolor= $track_color><font size='2'><a href='?ID=".$ID."'>".$track_abbreviation."</a></font></TH>";
	}
print '</TR>';

include("verbindung.php");
$recordset_wins = $database_connection->query($query_wins);
$i = 0;
while ($results_wins = $recordset_wins->fetch_assoc())
{
$i = $i + 1;
$driverID = $results_wins['DriverID'];

print '<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$results_wins['drivers'].'</a></FONT></TD>';
	print'<TD><FONT><b>'.$results_wins['Siege'].' of '.$events.'</b></FONT></TD>';
	include("verbindung.php");
	$recordset = $database_connection->query($query_tracks);
	while ($result = $recordset->fetch_assoc())
	{
		$track_color = 'white';
		$track_color = $result['ColorCode'];
		$trackID = $result['TrackID'];
		$trackTypeID = $result['TypeID'];
		$track_abbreviation = $result['StreckenKz'];
		include("verbindung.php");
		$query_details = "SELECT MIN(Finish) as Siege FROM race_results INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks ON tracks.ID = races.TrackID
		WHERE races.TrackID = $trackID and races.TypeID = $trackTypeID and race_results.DriverID = $driverID GROUP BY race_results.DriverID, tracks.Kennzeichen";
		$recordset_details = $database_connection->query($query_details);
		if ($results_details = $recordset_details->fetch_assoc())
		{
			$finish = $results_details['Siege'];
			if ($finish == 1) {print "<TD bgcolor= $track_color align='center'><b>x</b></TD>";}
			else {print "<TD bgcolor='white' align='center'>-</TD>";}
		}
		else
		{print "<TD bgcolor='white' align='center'></TD>";}
	}
print '</TR>';
}
?>
</TABLE>
</td>
</tr>
<tr>
<td>
<h3>Poles</h3>
<?php
$events = 0;
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT size="2">Pos</FONT></TH>';
	print'<TH align="left"><FONT size="2">Fahrer</FONT></TH>';
	print'<TH><FONT size="2">Total</FONT></TH>';
	include("verbindung.php");
	$recordset = $database_connection->query($query_tracks);
	while ($result = $recordset->fetch_assoc())
	{
	$track_color = 'white';
	$track_color = $result['ColorCode'];
	$trackID = $result['TrackID'];
	$trackTypeID = $result['TypeID'];
	$track_abbreviation = $result['StreckenKz'];
	$events = $events + 1;
	print "<TH bgcolor= $track_color><font size='2'><a href='?ID=".$ID."'>".$track_abbreviation."</a></font></TH>";
	}
print '</TR>';

include("verbindung.php");
$recordset_poles = $database_connection->query($query_poles);
$i = 0;
while ($results_poles = $recordset_poles->fetch_assoc())
{
$i = $i + 1;
$driverID = $results_poles['DriverID'];

print '<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$results_poles['drivers'].'</a></FONT></TD>';
	print'<TD><FONT><b>'.$results_poles['Poles'].' of '.$events.'</b></FONT></TD>';
	include("verbindung.php");
	$recordset = $database_connection->query($query_tracks);
	while ($result = $recordset->fetch_assoc())
	{
		$track_color = 'white';
		$track_color = $result['ColorCode'];
		$trackID = $result['TrackID'];
		$trackTypeID = $result['TypeID'];
		$track_abbreviation = $result['StreckenKz'];
		include("verbindung.php");
		$query_details = "SELECT MIN(Start) as Poles FROM race_results INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks ON tracks.ID = races.TrackID
		WHERE races.TrackID = $trackID and races.TypeID = $trackTypeID and race_results.DriverID = $driverID and Start > 0 GROUP BY race_results.DriverID, tracks.Kennzeichen";
		$recordset_details = $database_connection->query($query_details);
		if ($results_details = $recordset_details->fetch_assoc())
		{
			$finish = $results_details['Poles'];
			if ($finish == 1) {print "<TD bgcolor= $track_color align='center'><b>x</b></TD>";}
			else {print "<TD bgcolor='white' align='center'>-</TD>";}
		}
		else
		{print "<TD bgcolor='white' align='center'></TD>";}
	}
print '</TR>';
}
?>
</TABLE>
</td>
</tr>
<tr>
<td>
<h3>Meiste F&uuml;hrungsrunden</h3>
<?php
$events = 0;
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT size="2">Pos</FONT></TH>';
	print'<TH align="left"><FONT size="2">Fahrer</FONT></TH>';
	print'<TH><FONT size="2">Total</FONT></TH>';
	include("verbindung.php");
	$recordset = $database_connection->query($query_tracks);
	while ($result = $recordset->fetch_assoc())
	{
	$track_color = 'white';
	$track_color = $result['ColorCode'];
	$trackID = $result['TrackID'];
	$trackTypeID = $result['TypeID'];
	$track_abbreviation = $result['StreckenKz'];
	$events = $events + 1;
	print "<TH bgcolor= $track_color><font size='2'><a href='?ID=".$ID."'>".$track_abbreviation."</a></font></TH>";
	}
print '</TR>';

include("verbindung.php");
$recordset_mlls = $database_connection->query($query_mlls);
$i = 0;
while ($results_mlls = $recordset_mlls->fetch_assoc())
{
$i = $i + 1;
$driverID = $results_mlls['DriverID'];

print'<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$results_mlls['drivers'].'</a></FONT></TD>';
	print'<TD><FONT><b>'.$results_mlls['MLLs'].' of '.$events.'</b></FONT></TD>';
	include("verbindung.php");
	$recordset = $database_connection->query($query_tracks);
	while ($result = $recordset->fetch_assoc())
	{
		$track_color = 'white';
		$track_color = $result['ColorCode'];
		$trackID = $result['TrackID'];
		$trackTypeID = $result['TypeID'];
		$track_abbreviation = $result['StreckenKz'];
		include("verbindung.php");
		$query_details = "SELECT MAX(MostLapsLed) as MLL FROM race_results INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks ON tracks.ID = races.TrackID
		WHERE races.TrackID = $trackID and races.TypeID = $trackTypeID and race_results.DriverID = $driverID GROUP BY race_results.DriverID, tracks.Kennzeichen";
		$recordset_details = $database_connection->query($query_details);
		if ($results_details = $recordset_details->fetch_assoc())
		{
			$finish = $results_details['MLL'];
			if ($finish == 1) {print "<TD bgcolor= $track_color align='center'><b>x</b></TD>";}
			else {print "<TD bgcolor='white' align='center'>-</TD>";}
		}
		else
		{print "<TD bgcolor='white' align='center'></TD>";}
	}
print '</TR>';
}
?>
</TABLE>
</td>
</tr>
<tr>
<td>
<h3>Schnellste Rennrunden</h3>
<?php
$events = 0;
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT size="2">Pos</FONT></TH>';
	print'<TH align="left"><FONT size="2">Fahrer</FONT></TH>';
	print'<TH><FONT size="2">Total</FONT></TH>';
	include("verbindung.php");
	$recordset = $database_connection->query($query_tracks);
	while ($result = $recordset->fetch_assoc())
	{
	$track_color = 'white';
	$track_color = $result['ColorCode'];
	$trackID = $result['TrackID'];
	$trackTypeID = $result['TypeID'];
	$track_abbreviation = $result['StreckenKz'];
	$events = $events + 1;
	print "<TH bgcolor= $track_color><font size='2'><a href='?ID=".$ID."'>".$track_abbreviation."</a></font></TH>";
	}
print '</TR>';

include("verbindung.php");
$recordset_frls = $database_connection->query($query_frls);
$i = 0;
while ($results_frls = $recordset_frls->fetch_assoc())
{
$i = $i + 1;
$driverID = $results_frls['DriverID'];

print '<TR>';
	print'<TH><FONT >'.$i.'</FONT></TH>';
	print"<TD align='left'><FONT size='2'><a href='driver.php?ID=".$driverID."'>".$results_frls['drivers'].'</a></FONT></TD>';
	print'<TD><FONT><b>'.$results_frls['FRLs'].' of '.$events.'</b></FONT></TD>';
	include("verbindung.php");
	$recordset = $database_connection->query($query_tracks);
	while ($result = $recordset->fetch_assoc())
	{
		$track_color = 'white';
		$track_color = $result['ColorCode'];
		$trackID = $result['TrackID'];
		$trackTypeID = $result['TypeID'];
		$track_abbreviation = $result['StreckenKz'];
		include("verbindung.php");
		$query_details = "SELECT MAX(FastestRaceLap) as FRL FROM race_results INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks ON tracks.ID = races.TrackID
		WHERE races.TrackID = $trackID and races.TypeID = $trackTypeID and race_results.DriverID = $driverID GROUP BY race_results.DriverID, tracks.Kennzeichen";
		$recordset_details = $database_connection->query($query_details);
		if ($results_details = $recordset_details->fetch_assoc())
		{
			$finish = $results_details['FRL'];
			if ($finish == 1) {print "<TD bgcolor= $track_color align='center'><b>x</b></TD>";}
			else {print "<TD bgcolor='white' align='center'>-</TD>";}
		}
		else
		{print "<TD bgcolor='white' align='center'></TD>";}
	}
print '</TR>';
}
?>
</TABLE>
</td>
</tr>
<tr>
<td>
<h3>Meiste Positionen gewonnen</h3>
<?php
$events = 0;
print '<TABLE border=1 cellpadding=1 cellspacing=0>';
print '<TR>';
	print'<TH><FONT size="2">Pos</FONT></TH>';
	print'<TH align="left"><FONT size="2">Fahrer</FONT></TH>';
	print'<TH><FONT size="2">Total</FONT></TH>';
	include("verbindung.php");
	$recordset = $database_connection->query($query_tracks);
	while ($result = $recordset->fetch_assoc())
	{
	$track_color = 'white';
	$track_color = $result['ColorCode'];
	$trackID = $result['TrackID'];
	$trackTypeID = $result['TypeID'];
	$track_abbreviation = $result['StreckenKz'];
	$events = $events + 1;
	print "<TH bgcolor= $track_color><font size='2'><a href='?ID=".$ID."'>".$track_abbreviation."</a></font></TH>";
	}
print '</TR>';

include("verbindung.php");
$recordset_mpgs = $database_connection->query($query_mpgs);
$i = 0;
while ($results_mpgs = $recordset_mpgs->fetch_assoc())
{
$i = $i + 1;
$driverID = $results_mpgs['DriverID'];

print '<TR>';
	print '<TH><FONT >'.$i.'</FONT></TH>';
	print "<TD align='left'><FONT size='2'><a href='../driver/driver.php?ID=".$driverID."'>".$results_mpgs['drivers'].'</a></FONT></TD>';
	print '<TD><FONT><b>'.$results_mpgs['MPGs'].' of '.$events.'</b></FONT></TD>';
	include("verbindung.php");
	$recordset = $database_connection->query($query_tracks);
	while ($result = $recordset->fetch_assoc())
	{
		$track_color = 'white';
		$track_color = $result['ColorCode'];
		$trackID = $result['TrackID'];
		$trackTypeID = $result['TypeID'];
		$track_abbreviation = $result['StreckenKz'];
		include("verbindung.php");
		$query_details = "SELECT MAX(MostPositionsGained) as MPG FROM race_results INNER JOIN races on races.ID = race_results.RaceID INNER JOIN tracks ON tracks.ID = races.TrackID
		WHERE races.TrackID = $trackID and races.TypeID = $trackTypeID and race_results.DriverID = $driverID GROUP BY race_results.DriverID, tracks.Kennzeichen";
		$recordset_details = $database_connection->query($query_details);
		if ($results_details = $recordset_details->fetch_assoc())
		{
			$finish = $results_details['MPG'];
			if ($finish == 1) {print "<TD bgcolor= $track_color align='center'><b>x</b></TD>";}
			else {print "<TD bgcolor='white' align='center'>-</TD>";}
		}
		else
		{print "<TD bgcolor='white' align='center'></TD>";}
	}
print '</TR>';
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
