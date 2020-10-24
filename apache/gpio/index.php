<?php
	//print "Beginn";
	$aNames = array();
	
	if (file_exists('relay_config.bin')) {
		//config file exists, read it out
		$confFile = fopen('relay_config.bin', 'r') or die('Konnte config file nicht laden');
		$aNames = unserialize(fread($confFile,filesize('relay_config.bin')));
		fclose($confFile);
		
	} else {
		//config file has to be created and initialized
		$aNames = array('12V Nr. 1', '12V Nr. 2', '12V Nr. 3', '12V Nr. 4', '230V Nr. 1', '230V Nr. 2', '230V Nr. 3',  '230V Nr. 4');
		$confFile = fopen('relay_config.bin', 'w') or die('Konnte config file nicht laden');
		fwrite($confFile, serialize($aNames));
		fclose($confFile);
	}

	//Deklarieren des Relaisboard
	class cRelayBoard {
		private	$aWpPins = array(0,2,3,12,13,14,21,22);
		public	$aStates = array(-1,-1,-1,-1,-1,-1,-1,-1);

		function fnSetState($nr, $state) {
			shell_exec('gpio write ' . $this->aWpPins[$nr] . ' ' . $state);
			//print 'gpio write ' . $this->aWpPins[$nr] . ' ' . $state;
		}

		function fnGetState($nr) {
			$this->aStates[$nr] = shell_exec('gpio read ' . $this->aWpPins[$nr]);
			return $this->aStates[$nr];
		}

		function __construct() {
			for ($i = 0; $i <=7; $i++) {
				shell_exec('gpio mode ' . $this->aWpPins[$i] . ' OUT');
				//print $this->aWpPins[$i];
			}
		}
	}

	//Neues Relayboard
	$MyRelay = new cRelayBoard;

	//Buttons
	$setNr = $_GET['setNr'];
	$setState = $_GET['setState'];

	//Eventuell Zustände setzen

	if (($setNr === NULL) or ($setState === NULL)) {
		//print 'no';
	} else {
		//print 'yes';
		$MyRelay->fnSetState($setNr,$setState);
	}

	//Formular zum Umbenennen
	//?selectbox_name=4&text_name=&button_name=Senden
	$changeID = $_GET['selectbox_name'];
	$changeNewName = $_GET['text_name'];
	
	if (($changeID === NULL) or ($changeNewName === NULL)) {
		
	} else {
		$aNames[$changeID] = $changeNewName;
		$confFile = fopen('relay_config.bin', 'w') or die('Konnte config file nicht laden');
		fwrite($confFile, serialize($aNames));
		fclose($confFile);
	}

	//Alle Zustände abfragen
	for ($i = 0; $i <=7; $i++) {
		$MyRelay->fnGetState($i);
	}

?>
<!doctype html>
<!--[if lt IE 7]> <html class="ie6 oldie"> <![endif]-->
<!--[if IE 7]>    <html class="ie7 oldie"> <![endif]-->
<!--[if IE 8]>    <html class="ie8 oldie"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="">
<!--<![endif]-->
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Raspberry Pi Relaisboard</title>
<link href="boilerplate.css" rel="stylesheet" type="text/css">
<link href="mobil.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" type="image/x-icon" href="img/favicon.png">
<script src="respond.min.js"></script>
<style>
table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
}
</style>
</head>
<body>
<div class="gridContainer clearfix" align="center">
	<div align="center">
		<h1>Raspberry Pi Relaisboard</h1>
	</div>
	<div>
		<table>
			<tbody>  
			<tr>
				<td width="50px"><b>Status</b></td>
				<td width="80px"><b>Name</b></td>
				<td width="100px"><b>Aktionen</b></td>
				<td	width="80px"><b>Timer 1 ein</b></td>
				<td width="80px"><b>Timer 1 aus</b></td>
			</tr>
				<?php
					for ($i = 0; $i <=7; $i++) {
						if ($i > 0) {
							echo "\t\t\t";
						}
						
						echo '<tr>';
							//1. Spalte | Status
							echo '<td>';
								echo '<img src="img/';
								if ( $MyRelay->aStates[$i] == 1 ) {
									echo 'red';
								} else {
									echo 'green';
								}
								echo '.png">';
							echo '</td>';
							
							//2. Spalte | Name
							echo '<td>';
								echo $aNames[$i];
							echo '</td>';
							
							//3. Spalte | Aktion
							echo '<td>';
							echo '<a href="?setNr=' . $i . '&amp;setState=';
								if ( $MyRelay->aStates[$i] == 1 ) {
									echo '0"><button>einschalten';
								} else {
									echo '1"><button>ausschalten';
								}
								echo '</button></a>';
							echo '</td>';
							echo '<td><b></b></td><td><b></b></td>';
						echo '<tr>';						
						echo "\r\n";
					}
				?>
			</tbody>
		</table>
	</div>
	<br>
	<div>
		<form>
			<select name="selectbox_name">
				<?php 
					for ($i = 0; $i <=7; $i++) {
						echo '<option value="' . $i . '">' . $aNames[$i] . '</option>';
					}
				?>				
			</select>
			<label> --> </label>
			<input type="text" name="text_name" placeholder="neuer Name">
			<input type="submit" name="button_name" value="Senden">
		</form>
	</div>
<div>
<div align="center">
<p>&copy; 2020 Pascal Tippelt</p>
</div>
</div>
</div>
</body>
</html>
