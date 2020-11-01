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

	$aConfig = array(array());
	if (file_exists('/var/www/html/config.bin')) {
		//config file exists, read it out
		$fConfigFile = fopen('/var/www/html/config.bin', 'r') or die('Konnte config.bin nicht laden\r');
        $aConfig = unserialize(fread($fConfigFile, filesize('/var/www/html/config.bin')));
        fclose($fConfigFile);
		//echo "Success";
		
	} else {
		
		echo "Error loading config.bin";
		exit;
	}

//Check each timer
for ($i = 0; $i <=7; $i++) {
	if ( $aConfig[$i]->iTimerActive == 1 ) {
		//Timer ist aktiv
		$checkTime = localtime(time(),true);
		$checkMins = $checkTime['tm_hour']*60 + $checkTime['tm_min'];
		if ( ($checkMins > $aConfig[$i]->iTimerOn) and ($checkMins < $aConfig[$i]->iTimerOff) ) {
			//einschalten
			shell_exec('gpio write ' . $aConfig[$i]->iPin . ' 0');
			echo $checkTime['tm_hour'] . ':' . $checkTime['tm_min'] . ' -> ' . 'gpio write ' . $aConfig[$i]->iPin . ' 0';
		} else {
			shell_exec('gpio write ' . $aConfig[$i]->iPin . ' 1');
			echo $checkTime['tm_hour'] . ':' . $checkTime['tm_min'] . ' -> ' . 'gpio write ' . $aConfig[$i]->iPin . ' 1';
		}
	} else {
	//Timer ist inaktiv --> nichts tun
	}
}


?>

