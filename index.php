<?php
	/**
	 * @todo  Change HTTP_REFERER to be a specific access domain
	 *        ie: http://ontrapalooa.com
	 */
	header("Access-Control-Allow-Origin: " . rtrim($_SERVER["HTTP_REFERER"], "/"));
	header("Access-Control-Allow-Credentials: true");


	require_once('modules/temando.php');

	$obj_tem = new TemandoWebServices;

	$creds = json_decode( file_get_contents("creds.json"), true );

	$username = $creds["username"];
	$password = $creds["password"];
	$endpoint = 'http://api.temando.com/schema/2009_06/server.wsdl';

	$price = 24.70;
	$quantity = 1;

	$request = array(
		'anythings' => array(
			'anything' => array (
				0 => array(
					'class' => 'General Goods', 
					'subclass' => 'Household Goods',
					'packaging' => 'Box',
					'qualifierFreightGeneralFragile' => 'N',
					'weight' => 700,
					'length' => 20,
					'width' => 30,
					'height' => 20,
					'distanceMeasurementType' => 'Centimetres',
					'weightMeasurementType' => 'Grams',
					'quantity' => $quantity
				),
			),
		),

		'anywhere' => array (
			'itemNature' => 'Domestic',
			'itemMethod' => 'Door to Door',

			'originCountry' => 'AU',
			'originCode' => '4069',
			'originSuburb' => 'KENMORE', 
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
			'destinationCountry' => 'AU', 
			'destinationCode' => '4000', 
			'destinationSuburb' => 'BRISBANE', 
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
			'readyDate' => '2014-09-29', 
			'readyTime' => 'PM'
		),

		// 'clientId' => '64514', 
		// 'promotionCode' => 'A0001', 
		'general' => array(
			'goodsValue' => 24.70,
			'goodsCurrency' => 'AUD'
		)
	);
					
	$response = $obj_tem->getQuotesByRequest($request,$username,$password,$endpoint);

	foreach($response["quote"] as $quote){
		if ($quote["deliveryMethod"] == "GENERAL ROAD"){
				// echo "\$".$quote["basePrice"] ." - ". $quote["deliveryMethod"] ."<br/>";
			
			echo json_encode(array(
				"data" => array(
					"shipping" => $quote["basePrice"]
				),
				"status" => array(
					"code" => 0,
					"message" => "Success!"
				)
			));
			
			exit();
		}
	}