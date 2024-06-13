<!DOCTYPE html>
<html lang="en">
<head>
	<link rel="stylesheet" href="stylesheet.css">
	<title>Formula One</title>
</head>
<body>
<h1>Formula One World Championship</h3>
<p>
<TABLE>
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
			<TD><FONT><a href='championship/championship_tracks.php?Champ=Formula One World Championship'>Liste der Rennstrecken</a></FONT></TD>
			<TD><FONT><a href='championship/championship_drivers.php?Champ=Formula One World Championship'>Liste der Fahrer</a></FONT></TD>
			<TD><FONT><a href='statistics/track_poles.php?Champ=Formula One World Championship'>Pole nach Rennstrecke</a></FONT></TD>
			<TD><FONT><a href='statistics/track_wins.php?Champ=Formula One World Championship'>Siege nach Rennstrecke</a></FONT></TD>
			<TD><FONT><a href='statistics/records.php?Champ=Formula One World Championship'>Fahrerrekorde</a></FONT></TD>
		</TR>
		</TABLE>
		</TD>
		</TR>
		</TABLE>
	</TR>
	<TR>
		<TD colspan="3">
			<?php
				$championship_name_global = "%Formula One%";
				include("championship/championship.php");
			?>
		</TD>
	</TR>
</TABLE>
<a><hr/></a>
</p>
</body>
</html>
