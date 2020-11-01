<?php

	define("ON", 0);
	define("OFF", 1);

	class cRelay{
		public $iPin 			= -1;
		public $iState 			= OFF;
		public $sName 			= "No Name";
		public $iTimerOn 		= 420;
		public $iTimerOff 		= 480;	
		public $iTimerActive	= 0;

		public function __construct($iPin, $iState, $sName, $iTimerOn, $iTimerOff) {
			$this->iPin = $iPin;
			$this->iState = $iState;
			$this->sName = $sName;
			$this->iTimerOn = $iTimerOn;
			$this->iTimerOff = $iTimerOff;
			$this->iTimerActive = 0;
			
		}
	}
	
	//Konfiguration laden oder erstellen
	//$aConfig = array(array());
	if (file_exists('/var/www/html/config.bin')) {
		//config file exists, read it out
		$fConfigFile = fopen('/var/www/html/config.bin', 'r') or die('Konnte config.bin nicht laden\r');
        $aConfig = unserialize(fread($fConfigFile, filesize('/var/www/html/config.bin')));
        fclose($fConfigFile);
		//echo "Success";
		
	} else {
		//config file has to be created and initialized
		//Inside-Array (PIN,STATE,NAME,TIMER_ON,TIMER_OFF)
		//PIN:		wiredpi-pin
		//STATE:	-1 [illegal], 0 [off], 1 [on], 2 [automatic_off], 3 [automatic_on]
		//array(0,2,3,12,13,14,21,22);
		$aConfig = array(new cRelay(0,false,"New Relay 1",420,480), new cRelay(2,false,"New Relay 2",420,480), new cRelay(3,false,"New Relay 3",420,480), new cRelay(12,false,"New Relay 4",420,480), new cRelay(13,false,"New Relay 5",420,480), new cRelay(14,false,"New Relay 6",420,480), new cRelay(21,false,"New Relay 7",420,480), new cRelay(22,false,"New Relay 8",420,480));
		$fConfigFile = fopen('/var/www/html/config.bin', 'w') or die('Konnte config.bin nicht laden\r');
		fWrite($fConfigFile, serialize($aConfig));
        fclose($fConfigFile);
	}	
	
	//Buttons
	$setNr 			= $_GET['setNr'];
	$setState 		= $_GET['setState'];
	$setMode 		= $_GET['setMode'];
	$changeID 		= $_GET['selectbox_name'];
	$changeIDTimer 	= $_GET['selectbox_timer'];
	$changeNewName 	= $_GET['text_name'];
	$changeTimerOn 	= $_GET['timer_on'];
	$changeTimerOff = $_GET['timer_off'];
	
	//Stati setzen
	if (($setNr === NULL) or ($setState === NULL)) {
		//print 'no';
	} else {
		//print 'yes';
		shell_exec('gpio write ' . $aConfig[$setNr]->iPin . ' ' . $setState);
	}
	
	//Modi setzen
	if (($setNr === NULL) or ($setMode === NULL)) {
		//
	} else {
		$aConfig[$setNr]->iTimerActive = $setMode;
		$fConfigFile = fopen('/var/www/html/config.bin', 'w') or die('Konnte config.bin nicht laden\r');
		fWrite($fConfigFile, serialize($aConfig));
        fclose($fConfigFile);
	}
	
	//Change Name
	if (($changeID === NULL) or ($changeNewName === NULL)) {
		//Nothing
	} else {
		$aConfig[$changeID]->sName = $changeNewName;
		$fConfigFile = fopen('/var/www/html/config.bin', 'w') or die('Konnte config.bin nicht laden\r');
		fWrite($fConfigFile, serialize($aConfig));
        fclose($fConfigFile);
	}
	
	//Change Timers
	//echo $changeID . " " . $changeTimerOn . " " . $changeTimerOff;
	if (isset($changeIDTimer) and isset($changeTimerOn) and isset($changeTimerOff)) {
		//echo $changeIDTimer . " " . $changeTimerOn . " " . $changeTimerOff;
		$aTimerOn = explode(':',$changeTimerOn);
		$aTimerOff = explode(':',$changeTimerOff);
		$aConfig[$changeIDTimer]->iTimerOn = $aTimerOn[0]*60 + $aTimerOn[1];
		$aConfig[$changeIDTimer]->iTimerOff = $aTimerOff[0]*60 + $aTimerOff[1];
		$fConfigFile = fopen('/var/www/html/config.bin', 'w') or die('Konnte config.bin nicht laden\r');
		fWrite($fConfigFile, serialize($aConfig));
        fclose($fConfigFile);
	} 
	
	//Stati abfragen
	for ($i = 0; $i <=7; $i++) {
		$aConfig[$i]->iState = shell_exec('gpio read ' . $aConfig[$i]->iPin);
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
<!--<meta name="viewport" content="width=device-width, initial-scale=1">-->
<meta http-equiv="refresh" content="10, url=index.php">
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
				<td ><b>Status</b></td>
				<td width="80px"><b>Name</b></td>
				<td width="150px"><b><center>Aktionen</center></b></td>
				<td	width="95px"><b><center>Timer</center></b></td>
			</tr>
				<?php
					for ($i = 0; $i <=7; $i++) {
						if ($i > 0) {
							echo "\t\t\t";
						}

						echo '<tr>';
							//1. Spalte | Status
							echo '<td><center>';
								echo '<img src="img/';
								if ( $aConfig[$i]->iState == ON ) {
									echo 'green';
								} else {
									echo 'red';
								}
								echo '.png">';
							echo '</center></td>';

							//2. Spalte | Name
							echo '<td>';
								echo $aConfig[$i]->sName;
							echo '</td>';

							//3. Spalte | Aktion
							echo '<td><center>';
								if ($aConfig[$i]->iTimerActive == 0) {
									if ( $aConfig[$i]->iState == OFF ) {
										echo '<a href="?setNr=' . $i . '&amp;setState=0"><button>einschalten';
									} else {
										echo '<a href="?setNr=' . $i . '&amp;setState=1"><button>ausschalten';
									}
								} else {
									echo 'An von ' . intDiv($aConfig[$i]->iTimerOn,60) . ":" . sprintf('%02d',$aConfig[$i]->iTimerOn%60) . " bis " . intdiv($aConfig[$i]->iTimerOff,60) . ":" . sprintf('%02d',$aConfig[$i]->iTimerOff%60) . " Uhr";
									//
								}
								echo '</button></center></a>';
							echo '</td>';

							//4.palte Timer							
							echo '<td><center>';
								if ($aConfig[$i]->iTimerActive == 1) {
									echo '<a href="?setNr=' . $i . '&amp;setMode=0"><button>deaktivieren';
								} else {
									echo '<a href="?setNr=' . $i . '&amp;setMode=1"><button>aktivieren';
								}		
							echo '</button></a></center></td>';
							

						echo '<tr>';
						echo "\r\n";
					}
				?>
			</tbody>
		</table>
	</div>
	<br>
	<div align="center">
		<h2>Einstellungen</h2>
	</div>
	<div>
		<form>
			<select name="selectbox_name">
				<?php 
					for ($i = 0; $i <=7; $i++) {
						echo '<option value="' . $i . '">' . $aConfig[$i]->sName . '</option>';
					}
				?>				
			</select>
			<label> --> </label>
			<input type="text" name="text_name" placeholder="neuer Name">
			<input type="submit" name="button_name" value="Senden">
		</form>
	</div>
	<br>
	<div>
		<form>
			<select name="selectbox_timer">
				<?php 
					for ($i = 0; $i <=7; $i++) {
						echo '<option value="' . $i . '">' . $aConfig[$i]->sName . '</option>';
					}
				?>				
			</select>
			<label> --> </label>			
			<input id="appt-time" type="time" name="timer_on">
			<input id="appt-time" type="time" name="timer_off">
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
