<!DOCTYPE html>
<html lang="en">
<head>
	<style>
	h1 {
		text-align: center;
		vertical-align: middle;
	}
	h2 {
		text-align: center;
		vertical-align: middle;
	}
	h3 {
		text-align: center;
		vertical-align: middle;
	}
	h4 {
		text-align: center;
		vertical-align: middle;
	}
	p {
		text-align: center;
		vertical-align: middle;
	}
	a {
		text-align: center;
		vertical-align: middle;
	}
	table {
		text-align: center;
		vertical-align: middle;
	}
	th {
		text-align: center;
		vertical-align: middle;
	}
	tr {
		text-align: center;
		vertical-align: middle;
	}
	TD {
		text-align: center;
		vertical-align: middle;
	}
	</style>
	<title>Motorsport Statistik</title>
</head>
<body>
<h1>Motorsport Statistik</h1>
<p>
<table align="center">
	<TR>
		<TD colspan="5"><hr></TD>
	</TR>
	<TR>
		<TD colspan="5"><hr></TD>
	</TR>
	<TR>
		<TD colspan="2"><FONT><a href='F1.php'><img src="F1.png" alt="F1"></a></FONT></TD>
		<TD colspan="1"></TD>
		<TD colspan="2"><FONT><a href='ICS.php'><img src="ICS.png" alt="ICS"></a></FONT></TD>
	</TR>
	<TR>
		<TD colspan="5"><hr></TD>
	</TR>
	<TR>
		<TD><FONT><a href='championship/seasons.php'>Saison&uuml;bersicht nach Jahr</a></FONT></TD>
		<TD><FONT><a href='championship/index.php'>Saison&uuml;bersicht nach Meisterschaft</a></FONT></TD>
		<TD><FONT><a href='http://localhost/phpmyadmin/index.php'>Datenbank Admin</a></FONT></TD>
		<TD><FONT><a href='tracks/index.php'>Liste der Rennstrecken</a></FONT></TD>
		<TD><FONT><a href='tracks/tracks_tracktype.php'>Liste der Streckentypen</a></FONT></TD>
	</TR>
	<TR>
		<TD><FONT><a href='statistics/seasonstatistics.php'>Saisonstatistik</a></FONT></TD>
		<TD><FONT><a href='statistics/seasonchampions.php'>Champions</a></FONT></TD>
		<TD><FONT><a href='statistics/records.php'>Rekorde (allgemein)</a><br /></TD>
		<TD><FONT><a href='statistics/track_poles.php'>Poles nach Rennstrecke</a></FONT></TD>
		<TD><FONT><a href='statistics/track_wins.php'>Siege nach Rennstrecke</a></FONT></TD>
	</TR>
	<TR>
		<TD colspan="5"><hr></TD>
	</TR>
	<TR>
		<TD colspan="2">
			<form class="form-horizontal" action="import_schedule.php" method="post" name="upload_excel" enctype="multipart/form-data">
				<fieldset>
					<!-- Form Name -->
					<legend><b>Import Racing Schedule</b></legend>
					<!-- File Button -->
					<div class="form-group">
						<label class="col-md-4 control-label" for="filebutton">Select File</label>
						<br/>
						<div class="col-md-4">
							<input type="file" name="file[]" id="file" class="input-large" multiple>
						</div>
					</div>
					<br/>
					<!-- Button -->
					<div class="form-group">
						<div class="col-md-4">
							<button type="submit" id="submit" name="Import" value = "" class="btn btn-primary button-loading" data-loading-text="Loading...">Import Schedule Data</button>
						</div>
					</div>
				</fieldset>
			</form>
		</TD>
		<TD colspan="1">
			<form class="form-horizontal" action="import_qualy_result.php" method="post" name="upload_excel" enctype="multipart/form-data">
				<fieldset>
					<!-- Form Name -->
					<legend><b>Import Qualification Results</b></legend>
					<!-- File Button -->
					<div class="form-group">
						<label class="col-md-4 control-label" for="filebutton">Select File</label>
						<br/>
						<div class="col-md-4">
							<input type="file" name="file[]" id="file" class="input-large" multiple>
						</div>
					</div>
					<br/>
					<!-- Button -->
					<div class="form-group">
						<div class="col-md-4">
							<button type="submit" id="submit" name="Import" value = "" class="btn btn-primary button-loading" data-loading-text="Loading...">Import Qualy Data</button>
						</div>
					</div>
				</fieldset>
			</form>
		</TD>
		<TD colspan="2">
			<form class="form-horizontal" action="import_race_result.php" method="post" name="upload_excel" enctype="multipart/form-data">
				<fieldset>
					<!-- Form Name -->
					<legend><b>Import Race Results</b></legend>
					<!-- File Button -->
					<div class="form-group">
						<label class="col-md-4 control-label" for="filebutton">Select File</label>
						<br/>
						<div class="col-md-4">
							<input type="file" name="file[]" id="file" class="input-large" multiple>
						</div>
					</div>
					<br/>
					<!-- Button -->
					<div class="form-group">
						<div class="col-md-4">
							<button type="submit" id="submit" name="Import" value = "" class="btn btn-primary button-loading" data-loading-text="Loading...">Import Main Race Data</button>
						</div>
					</div>
				</fieldset>
			</form>
		</TD>
	</TR>
	<TR>
		<TD colspan="5"><hr></TD>
	</TR>
	<TR>
	</TR>
</table>
</p>
</body>
</html>
