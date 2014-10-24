<?php
	/**
	 * @todo  Change HTTP_REFERER to be a specific access domain
	 *        ie: http://ontrapalooa.com
	 */
	ini_set('default_socket_timeout', 600);

	session_start();

	require_once('modules/temando.php');

	$obj_tem = new TemandoWebServices;

	$creds = json_decode( file_get_contents("data/creds.json"), true );

	$username = $creds["username"];
	$password = $creds["password"];
	$endpoint = 'http://api.temando.com/schema/2009_06/server.wsdl';

	/**
	 * Parameters passed in from runner file.
	 * @var [type]
	 */
	
	$memcacheKey = $argv[1];
	$orderQuantity = $argv[2];
	$country = $argv[3];
	$code = $argv[4];
	$suburb = $argv[5];
	$price = $argv[6];
	$paid = $argv[7];
	$shippingType = $argv[8];
	$sess_id = $argv[9];
	$debug = $argv[10];

	session_id($sess_id);

	error_log("SENDING REQUEST FOR " . $orderQuantity);
	$request = array(
		'anythings' => array(
			'anything' => array (
				0 => array(
					'class' => 'General Goods', 
					'subclass' => 'Household Goods',
					'packaging' => 'Box',
					'qualifierFreightGeneralFragile' => 'N',
					'weight' => 18000,
					'length' => 20,
					'width' => 20,
					'height' => 30,
					'distanceMeasurementType' => 'Centimetres',
					'weightMeasurementType' => 'Grams',
					'quantity' => $orderQuantity
				),
			),
		),

		'anywhere' => array (
			'itemNature' => $shippingType,
			'itemMethod' => 'Door to Door',

			'originCountry' => 'AU',
			'originCode' => '3192',
			'originSuburb' => 'Cheltenham', 
			'originIs' => 'Business', 

			'originBusDock' => 'N', 
			'originBusUnattended' => 'N', 
			'originBusForklift' => 'N', 
			'originBusLoadingFacilities' => 'N', 
			'originBusInside' => 'N', 
			'originBusNotifyBefore' => 'N',
			'originBusLimitedAccess' => 'N', 
			'originBusHeavyLift' => 'N', 
			'originBusContainerSwingLifter' => 'N', 
			'originBusTailgateLifter' => 'N', 


			'destinationCountry' => $country, //'AU',
			'destinationCode' => $code, //'4000', 
			'destinationSuburb' => $suburb, //'BRISBANE', 
			'destinationIs' => 'Business', 


			'destinationBusDock' => 'N', 
			'destinationBusPostalBox' => 'N', 
			'destinationBusUnattended' => 'N', 
			'destinationBusForklift' => 'N', 
			'destinationBusLoadingFacilities' => 'N', 
			'destinationBusInside' => 'N', 
			'destinationBusNotifyBefore' => 'N', 
			'destinationBusLimitedAccess' => 'N', 
			'destinationBusHeavyLift' => 'N', 
			'destinationBusContainerSwingLifter' => 'N', 
			'destinationBusTailgateLifter' => 'N'
		), 



		'anytime' => array(
			'readyDate' => date("Y-m-d", strtotime("this friday")), 
			'readyTime' => 'PM'
		),

		// 'clientId' => '64514', 
		// 'promotionCode' => 'A0001', 
		'general' => array(
			'goodsValue' => $price,
			'termsOfTrade' => "Delivered Duty " . $paid,
			'goodsCurrency' => 'AUD'
		)
	);
					
	$response = $obj_tem->getQuotesByRequest($request,$username,$password,$endpoint);

	if ($debug){
		error_log("RESPONSE FOR: " . $orderQuantity);
		error_log(">>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>>");
		error_log(var_export($response, true));
		error_log("<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<");
	}

	$quotes = array();

	foreach($response["quote"] as $quote){
		$quotes[ $quote["deliveryMethod"] ] = $quote["totalPrice"];
	}

	$memcache = new Memcached;
	$memcache->addServer('localhost', 11211);

	$memcache->set($memcacheKey."::".$orderQuantity, $quotes); 

	/**
	 * Potential race condition, but shouldn't matter as the end result is the same and increments
	 * will be static.
	 */
	if (++$_SESSION["working_pos"] == 12){
		$_SESSION["working"] = false;
	}

	exit();