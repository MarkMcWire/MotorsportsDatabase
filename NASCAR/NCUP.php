<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>NASCAR Cup Series</title>
</head>
<body>
<h1>NASCAR Cup Series</h3>
<p>
<table>
	<TR>
		<TABLE border="2" cellspacing="10">
		<TR valign='top'>
		<TD>
		<p>
		<TABLE border='1' cellspacing='0'>
		<TR>
			<TH>Rennstrecken</TH>
			<TH colspan="4">Fahrer</TH>
		</TR>
		<TR>
			<TD><FONT><a href='championship/championship_tracks.php?Champ=NASCAR Cup Series'>Liste der Rennstrecken</a></FONT></TD>
			<TD><FONT><a href='championship/championship_drivers.php?Champ=NASCAR Cup Series'>Liste der Fahrer</a></FONT></TD>
			<TD><FONT><a href='statistics/track_poles.php?Champ=NASCAR Cup Series'>Poles nach Rennstrecke</a></FONT></TD>
			<TD><FONT><a href='statistics/track_wins.php?Champ=NASCAR Cup Series'>Siege nach Rennstrecke</a></FONT></TD>
			<TD><FONT><a href='statistics/records.php?Champ=NASCAR Cup Series'>Fahrerrekorde</a></FONT></TD>
		</TR>
		</TABLE>
		</TD>
		</TR>
		</TABLE>
	</TR>
	<tr>
		<td colspan="3">
			<?php
				$championship_name_global = "%NASCAR Cup%";
				include("championship/championship.php");
			?>
		</td>
	</tr>
</table>
<a><hr/></a>
</p>
</body>
</html>
