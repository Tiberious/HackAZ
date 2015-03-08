<?php

date_default_timezone_set("America/Phoenix");

require('functions.php');
require_once('nest.class.php');

// this line loads the library 
require('twilio-php-master/Services/Twilio.php'); 
 
$account_sid = 'ACa3f044346befe748e0d18e6d7c417c93'; 
$auth_token = 'd2e996739fec13e5d9895300cc748ac1'; 
$client = new Services_Twilio($account_sid, $auth_token); 
 
$client_id = "bac3ceec-d33f-45cf-9566-e97097eedc08";

$token = "c.KdytacmNNt7v2hAeb0kzugDmcFYqszrZxjInSOPxz28IGYanKoTfGhG3aYD8wXZpZFfkxUiMcF1ORWd1qHRT6VbYSXywUloOITsYIhAbj08mBVXCjBGrlSlVuCIeiV0iBz3vzXZhl6KWGjZh";

require('global.php');
$get_alerts = $db->prepare("
        SELECT *, MAX(timestamp) FROM alerts
        GROUP BY code
");
$get_alerts->execute();
$dbalerts = $get_alerts->fetchAll(PDO::FETCH_ASSOC);

foreach ($dbalerts as $alert) {
	$alerts[$alert['code']] = $alert['MAX(timestamp)'];
}

print_r($alerts);

// Your Nest username and password.
define('USERNAME', 'fiziksbrett@icloud.com');
define('PASSWORD', 'belugafish07');

$nest = new Nest();

// Get the device information:

//print_r($nest);

$devices_serials = $nest->getDevices();

foreach ( $devices_serials as $device ) {

	$infos = $nest->getDeviceInfo($device);
	//print_r($infos);

/*
	if ( $infos->current_state->temperature < $infos->target->temperature[0] ) {
		if ( time() - strtotime($alerts['loTemp']) > 600)  {
			logAlert("loTemp", $infos->serial_number, $infos->location );
			echo "ALERT - LOW TEMP ";
			printf("Current temperature: %.02f degrees %s\n", $infos->current_state->temperature, $infos->scale);
			$client->account->messages->create(array( 
				'To' => "520-465-2765", 
				'From' => "+16365244498", 
				'Body' => "ALERT - LOW TEMP \n http://nexus.gusadelic.net/nest",   
			));
		}
	}
*/
	if ( $infos->current_state->alt_heat == 1 ) {
		if ( time() - strtotime($alerts['altHeat']) > 180)  {
			logAlert("altHeat", $infos->serial_number, $infos->location);
			echo "ALERT - EMERGENCY HEATING ACTIVATED ";
			$client->account->messages->create(array( 
				'To' => "520-465-2765", 
				'From' => "+16365244498", 
				'Body' => "ALERT - EMERGENCY HEATING ACTIVATED \n http://nexus.gusadelic.net/nest",   
			));
		}
	}

/*
	if ( $infos->current_state->temperature > $infos->target->temperature[1] ) {
		if ( time() - strtotime($alerts['hiTemp']) > 600)  {
			logAlert("hiTemp", $infos->serial_number, $infos->location);
			echo "ALERT - HIGH TEMP ";
			printf("Current temperature: %.02f degrees %s\n", $infos->current_state->temperature, $infos->scale);
		}
	}

	if ( $infos->network->online == 0 ) {
		if ( ( time() - strtotime($alerts['tOffline']) ) > 600) {
			logAlert('tOffline', $infos->serial_number, $infos->location);
			echo "ALERT - THERMO OFFLINE\n";
			$client->account->messages->create(array( 
				'To' => "520-465-2765", 
				'From' => "+16365244498", 
				'Body' => "ALERT - THERMO OFFLINE \n http://nexus.gusadelic.net/nest",   
			));
		}
	}
*/
}

$protects_serials = $nest->getDevices(DEVICE_TYPE_PROTECT);
foreach ($protects_serials as $protect) {
	$infos = $nest->getDeviceInfo($protect);
	//print_r($infos);

	if ( $infos->co_status > 1) {
		if ( time() - strtotime($alerts['smokeCO']) > 180)  {
			logAlert('smokeCO', $infos->serial_number, $infos->location);
			echo "ALERT - CO DETECTED!\n";
				$client->account->messages->create(array( 
					'To' => "520-465-2765", 
					'From' => "+16365244498", 
					'Body' => "ALERT - CO DETECTED! \n http://nexus.gusadelic.net/nest",   
				));
		}
	}

	if ( $infos->smoke_status > 1) {
		if ( time() - strtotime($alerts['smoke']) > 180)  {
			logAlert('smoke', $infos->serial_number, $infos->location);
			echo "ALERT - SMOKE DETECTED!\n";
				$client->account->messages->create(array( 
					'To' => "520-465-2765", 
					'From' => "+16365244498", 
					'Body' => "ALERT - SMOKE DETECTED! \n http://nexus.gusadelic.net/nest",   
				));
		}
	}

	if ( $infos->battery_health_state > 2) {
		if ( time() - strtotime($alerts['smokeBat']) > 180)  {
			logAlert('smokeBat', $infos->serial_number, $infos->location);
			echo "ALERT - REPLACE BATTERY!\n";
				$client->account->messages->create(array( 
					'To' => "520-465-2765", 
					'From' => "+16365244498", 
					'Body' => "ALERT - REPLACE BATTERY! \nhttp://nexus.gusadelic.net/nest",   
				));
		}
	}

	if ( $infos->network->online == 0) {
		if ( time() - strtotime($alerts['smokeOff']) > 180)  {
			logAlert('smokeOff', $infos->serial_number, $infos->location);
			echo "ALERT - SMOKE/CO DETECTOR OFFLINE!\n";
				$client->account->messages->create(array( 
					'To' => "520-465-2765", 
					'From' => "+16365244498", 
					'Body' => "ALERT - SMOKE/CO DETECTOR OFFLINE! \nhttp://nexus.gusadelic.net/nest",   
				));
		}
	}

}









// Print the current temperature
//printf("Current temperature: %.02f degrees %s\n", $infos->current_state->temperature, $infos->scale);


function logAlert($code, $serial, $location) {
	require('global.php');
	
	$params = array(
		':code' => $code,
		':serial' => $serial,
		':location' => $location
	);
		
	$log = $db->prepare("
		INSERT INTO alerts (`code`, `timestamp`, `serial`, `location`) VALUES (:code, NOW(), :serial, :location )
	");
	
	$log->execute($params);

}

?>
