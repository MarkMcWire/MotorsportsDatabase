<!DOCTYPE html>
<html lang="en">
<HEAD>
	<link rel="stylesheet" href="stylesheet.css">
	<TITLE>Points Scoring overview</TITLE>
</HEAD>
<BODY bgcolor="#FFFFFF" text="#000000" link="#0000FF" vlink="#FF00FF" alink="#FF0000">
<FONT face="Times New Roman">
<HR>
<H1>Point Scoring Systems / Punktevergabesysteme</H1>
<HR>
<form action="" method="get">
<table>
<tr>
<th>Filter nach Rängen</th>
<th>Filter nach Maxpunkte</th>
</tr>
<tr>
<td>
<SELECT name="Ranks">
	<option value="10">bis 10</option>
	<option value="15">bis 15</option>
	<option value="20">bis 20</option>
	<option value="25">bis 25</option>
	<option value="30">bis 30</option>
	<option value="35">bis 35</option>
	<option value="40">bis 40</option>
	<option value="1000">alle</option>
</SELECT>
</td>
<td>
<SELECT name="Points">
	<option value="1000">alle</option>
	<option value="10">bis 10</option>
	<option value="20">bis 20</option>
	<option value="30">bis 30</option>
	<option value="40">bis 40</option>
	<option value="50">bis 50</option>
</SELECT>
</td>
<td>
<input type="submit" name="absenden" value="Filter anwenden">
</td>
<td>
<input type="submit" name="absenden" value="Punktesystem anwenden">
</td>
</tr>
</table>
</form>
<HR>
<H2>Ranking Points / Platzierungspunkte</H2>
<?php
if (isset($_GET["Ranks"])) {$ranks_filter = $_GET["Ranks"];} ELSE {$ranks_filter = 0;}
if (isset($_GET["Points"])) {$points_filter = $_GET["Points"];} ELSE {$points_filter = 0;}
if (isset($_GET["Bonus"])) {$bonus = $_GET["Bonus"];} ELSE {$bonus = 'all';}
//print $listbonus;
$query = "SELECT Scoring, Saison, Mileage, MAX(Wert), Max(Punkte) FROM rank_points GROUP BY Scoring, Saison, Mileage HAVING (MAX(Wert) <= $ranks_filter) AND (MAX(Punkte)<= $points_filter) ORDER BY Scoring ASC";
$track_color = 'white';
$query1 = "SELECT Wert FROM rank_points GROUP BY Wert ORDER BY Wert ASC";
include("verbindung.php");
$recordset = $database_connection->query($query);
$recordset1 = $database_connection->query($query1);
print '<TABLE border=1 cellpadding=3 cellspacing=0>';
print '<TR>';
print '<TH><FONT >Rank</FONT></TH>';
$anzahlwertung = 0;
while ($row = $recordset->fetch_assoc())
	{
		$anzahlwertung = $anzahlwertung + 1;
		print '<TH><FONT>'.$row['Scoring'].'<br/>('.$row['Saison'].', '.$row['Mileage'].')</FONT></TH>';
	}
	print '<TH><FONT >Summe/Sum</FONT></TH>';
	print '<TH><FONT >Durchschnitt/Average ('.$anzahlwertung.')</FONT></TH>';
while ($row1 = $recordset1->fetch_assoc())
	{
		$wert = $row1['Wert'];
		if ($wert % 10 == 1) {$track_color='grey';} elseif ($wert % 10 == 0) {$track_color='lightgrey';} else {$track_color='white';}
		print "<TR bgcolor = '$track_color'>";
		print '<TD><FONT ><b>'.$wert.'</FONT></b></TD>';
		$recordset0 = $database_connection->query($query);
		$points_total = 0;
		$durchschnittspunkte = 0;
		while ($row0 = $recordset0->fetch_assoc())
			{
				$wertung = $row0['Scoring'];
				$season = $row0['Saison'];
				$mileage = $row0['Mileage'];
				$query2 = "SELECT Punkte FROM rank_points WHERE (Wert = $wert) and (Scoring = $wertung) and (Saison = $season) and (Mileage = $mileage)";
				$recordset2 = $database_connection->query($query2);
				if ($row2 = $recordset2->fetch_assoc()) {$points = $row2['Punkte']; $points_total = $points_total + $points; $durchschnittspunkte = ROUND($points_total/$anzahlwertung,0); print'<TD><FONT>'.$points.'</FONT></TD>';}
				else {print'<TD><FONT></FONT></TD>';}
			}
	print'<TD><FONT><b>'.$points_total.'</FONT></b></TD>';
	print'<TD><FONT><b>'.$durchschnittspunkte.'</b></FONT></TD>';
	print '</TR>';
	}
?>
</TABLE>
<H2>Sprints / Heats</H2>
<?php
include("verbindung.php");
$query = "SELECT Scoring, Saison, Mileage, MAX(Wert), Max(Punkte) FROM sprint_points GROUP BY Scoring, Saison, Mileage HAVING (MAX(Wert) <= $ranks_filter) AND (MAX(Punkte)<= $points_filter) ORDER BY Scoring ASC";
$recordset = $database_connection->query($query);
$i = 0;
print '<TABLE border=1 cellpadding=3 cellspacing=0>';
print '<TR>';
	print '<TH><FONT >Position</FONT></TH>';
	$anzahlwertung = 0;
	while ($row = $recordset->fetch_assoc())
	{
		$anzahlwertung = $anzahlwertung + 1;
		print '<TH><FONT>'.$row['Scoring'].'<br/>('.$row['Saison'].', '.$row['Mileage'].')</FONT></TH>';
	}
	print '<TH><FONT >Summe/Sum</FONT></TH>';
	print '<TH><FONT >Durchschnitt/Average ('.$anzahlwertung.')</FONT></TH>';
	print '</TR>';
include("verbindung.php");
$query1 = "SELECT Wert FROM sprint_points GROUP BY Wert ORDER BY Wert ASC";
$recordset1 = $database_connection->query($query1);
while ($row1 = $recordset1->fetch_assoc())
{
	$wert = $row1['Wert'];
	print'<TR>';
	print'<TD><FONT ><b>'.$wert.'</FONT></b></TD>';
	$recordset0 = $database_connection->query($query);
	$points_total = 0;
	$durchschnittspunkte = 0;
	while ($row0 = $recordset0->fetch_assoc())
		{
			$wertung = $row0['Scoring'];
			$season = $row0['Saison'];
			$mileage = $row0['Mileage'];
			$query2 = "SELECT Punkte FROM sprint_points WHERE (Wert = $wert) and (Scoring = $wertung) and (Saison = $season) and (Mileage = $mileage)";
			$recordset2 = $database_connection->query($query2);
			if ($row2 = $recordset2->fetch_assoc()) {$points = $row2['Punkte']; $points_total = $points_total + $points; $durchschnittspunkte = ROUND($points_total/$anzahlwertung,0); print'<TD><FONT>'.$points.'</FONT></TD>';}
			else {print'<TD><FONT></FONT></TD>';}
		}
	print'<TD><FONT><b>'.$points_total.'</FONT></b></TD>';
	print'<TD><FONT><b>'.$durchschnittspunkte.'</b></FONT></TD>';
	print'</TR>';
}
?>
</TABLE>
<H2>Stages</H2>
<?php
include("verbindung.php");
$query = "SELECT Scoring, Saison, Mileage, MAX(Wert), Max(Punkte) FROM stage_points GROUP BY Scoring, Saison, Mileage HAVING (MAX(Wert) <= $ranks_filter) AND (MAX(Punkte)<= $points_filter) ORDER BY Scoring ASC";
$recordset = $database_connection->query($query);
$i = 0;
print '<TABLE border=1 cellpadding=3 cellspacing=0>';
print '<TR>';
	print '<TH><FONT >Position</FONT></TH>';
	$anzahlwertung = 0;
	while ($row = $recordset->fetch_assoc())
	{
		$anzahlwertung = $anzahlwertung + 1;
		print '<TH><FONT>'.$row['Scoring'].'<br/>('.$row['Saison'].', '.$row['Mileage'].')</FONT></TH>';
	}
	print '<TH><FONT >Summe/Sum</FONT></TH>';
	print '<TH><FONT >Durchschnitt/Average ('.$anzahlwertung.')</FONT></TH>';
	print '</TR>';
include("verbindung.php");
$query1 = "SELECT Wert FROM stage_points GROUP BY Wert ORDER BY Wert ASC";
$recordset1 = $database_connection->query($query1);
while ($row1 = $recordset1->fetch_assoc())
{
	$wert = $row1['Wert'];
	print'<TR>';
	print'<TD><FONT ><b>'.$wert.'</FONT></b></TD>';
	$recordset0 = $database_connection->query($query);
	$points_total = 0;
	$durchschnittspunkte = 0;
	while ($row0 = $recordset0->fetch_assoc())
		{
			$wertung = $row0['Scoring'];
			$season = $row0['Saison'];
			$mileage = $row0['Mileage'];
			$query2 = "SELECT Punkte FROM stage_points WHERE (Wert = $wert) and (Scoring = $wertung) and (Saison = $season) and (Mileage = $mileage)";
			$recordset2 = $database_connection->query($query2);
			if ($row2 = $recordset2->fetch_assoc()) {$points = $row2['Punkte']; $points_total = $points_total + $points; $durchschnittspunkte = ROUND($points_total/$anzahlwertung,0); print'<TD><FONT>'.$points.'</FONT></TD>';}
			else {print'<TD><FONT></FONT></TD>';}
		}
	print'<TD><FONT><b>'.$points_total.'</FONT></b></TD>';
	print'<TD><FONT><b>'.$durchschnittspunkte.'</b></FONT></TD>';
	print'</TR>';
}
?>
</TABLE>
<H2>Qualifikation</H2>
<?php
include("verbindung.php");
$query = "SELECT Scoring, Saison, Mileage, MAX(Wert), Max(Punkte) FROM qualification_points GROUP BY Scoring, Saison, Mileage HAVING (MAX(Wert) <= $ranks_filter) AND (MAX(Punkte)<= $points_filter) ORDER BY Scoring ASC";
$recordset = $database_connection->query($query);
$i = 0;
print '<TABLE border=1 cellpadding=3 cellspacing=0>';
print '<TR>';
	print '<TH><FONT >Position</FONT></TH>';
	$anzahlwertung = 0;
	while ($row = $recordset->fetch_assoc())
	{
		$anzahlwertung = $anzahlwertung + 1;
		print '<TH><FONT>'.$row['Scoring'].'<br/>('.$row['Saison'].', '.$row['Mileage'].')</FONT></TH>';
	}
	print '<TH><FONT >Summe/Sum</FONT></TH>';
	print '<TH><FONT >Durchschnitt/Average ('.$anzahlwertung.')</FONT></TH>';
	print '</TR>';
include("verbindung.php");
$query1 = "SELECT Wert FROM qualification_points GROUP BY Wert ORDER BY Wert ASC";
$recordset1 = $database_connection->query($query1);
while ($row1 = $recordset1->fetch_assoc())
{
	$wert = $row1['Wert'];
	print'<TR>';
	print'<TD><FONT ><b>'.$wert.'</FONT></b></TD>';
	$recordset0 = $database_connection->query($query);
	$points_total = 0;
	$durchschnittspunkte = 0;
	while ($row0 = $recordset0->fetch_assoc())
		{
			$wertung = $row0['Scoring'];
			$season = $row0['Saison'];
			$mileage = $row0['Mileage'];
			$query2 = "SELECT Punkte FROM qualification_points WHERE (Wert = $wert) and (Scoring = $wertung) and (Saison = $season) and (Mileage = $mileage)";
			$recordset2 = $database_connection->query($query2);
			if ($row2 = $recordset2->fetch_assoc()) {$points = $row2['Punkte']; $points_total = $points_total + $points; $durchschnittspunkte = ROUND($points_total/$anzahlwertung,0); print'<TD><FONT>'.$points.'</FONT></TD>';}
			else {print'<TD><FONT></FONT></TD>';}

		}
	print'<TD><FONT><b>'.$points_total.'</FONT></b></TD>';
	print'<TD><FONT><b>'.$durchschnittspunkte.'</b></FONT></TD>';
	print'</TR>';
}
?>
</TABLE>
<H2>Bonus</H2>
<?php
include("verbindung.php");
$query = "SELECT Scoring, Saison, Mileage, MAX(Wert), Max(Punkte) FROM bonus_points GROUP BY Scoring, Saison, Mileage HAVING (MAX(Wert) <= $ranks_filter) AND (MAX(Punkte)<= $points_filter) ORDER BY Scoring ASC";
$recordset = $database_connection->query($query);
$i = 0;
print '<TABLE border=1 cellpadding=3 cellspacing=0>';
print '<TR>';
	print '<TH><FONT >Bonus</FONT></TH>';
	$anzahlwertung = 0;
	while ($row = $recordset->fetch_assoc())
	{
		$anzahlwertung = $anzahlwertung + 1;
		print '<TH><FONT>'.$row['Scoring'].'<br/>('.$row['Saison'].', '.$row['Mileage'].')</FONT></TH>';
	}
	print '<TH><FONT >Summe/Sum</FONT></TH>';
	print '<TH><FONT >Durchschnitt/Average ('.$anzahlwertung.')</FONT></TH>';
	print '</TR>';
include("verbindung.php");
$query3 = "SELECT Bewertung, Wert FROM bonus_points WHERE Wert > 0 GROUP BY Bewertung, Wert ORDER BY Bewertung, Wert";
$recordset3 = $database_connection->query($query3);
while ($row3 = $recordset3->fetch_assoc())
{
	$wert = $row3['Wert'];
	$bewertung = $row3['Bewertung'];
	print'<TR>';
	print'<TD><FONT ><b>'.$bewertung.' ('.$wert.')</FONT></b></TD>';
	$recordset0 = $database_connection->query($query);
	$points_total = 0;
	$durchschnittspunkte = 0;
	while ($row0 = $recordset0->fetch_assoc())
		{
			$wertung = $row0['Scoring'];
			$season = $row0['Saison'];
			$mileage = $row0['Mileage'];
			$query2 = "SELECT Punkte FROM bonus_points WHERE (Wert = $wert) and (Scoring = $wertung) and (Saison = $season) and (Mileage = $mileage) and (Bewertung = '$bewertung')";
			$recordset2 = $database_connection->query($query2);
			if ($row2 = $recordset2->fetch_assoc()) {$points = $row2['Punkte']; $points_total = $points_total + $points; $durchschnittspunkte = ROUND($points_total/$anzahlwertung,0); print'<TD><FONT>'.$points.'</FONT></TD>';}
			else {print'<TD><FONT></FONT></TD>';}

		}
	print'<TD><FONT><b>'.$points_total.'</FONT></b></TD>';
	print'<TD><FONT><b>'.$durchschnittspunkte.'</b></FONT></TD>';
	print'</TR>';
}
?>
</TABLE>
<p align="center">
<u>
<li>LL = Led Lap / Führungsrunde</li>
<li>MLL = Most Laps Led / Meiste Führungsrunden</li>
<li>FRL = Fastest Racing Lap / Schnellste Rennrunde</li>
<li>MPG = Most Positions Gained / Größter Positionsgewinn</li>
</u>
</p>
<HR>
<p align="center">
<a href='../index.php'>Zur&uuml;ck zum Index</a>
</p>
</FONT>
</BODY>
</HTML>
