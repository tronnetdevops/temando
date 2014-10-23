<?php
	/**
	 * @todo  Change HTTP_REFERER to be a specific access domain
	 *        ie: http://ontrapalooa.com
	 */
	header("Access-Control-Allow-Origin: " . rtrim($_SERVER["HTTP_ORIGIN"], "/"));
	header("Access-Control-Allow-Credentials: true");

	
	$typicalMax = 16; // = 400
	$booksInBox = 25;

	require_once('modules/temando.php');

	$obj_tem = new TemandoWebServices;

	$creds = json_decode( file_get_contents("data/creds.json"), true );

	$username = $creds["username"];
	$password = $creds["password"];
	$endpoint = 'http://api.temando.com/schema/2009_06/server.wsdl';

	$price = 24.70;

	$quantity = isset($_GET["quantity"]) ? floor($_GET["quantity"] / $booksInBox) : 1;
	$paid = ($_GET["paid"] == "1") ? "Paid" : "Unpaid";

	$country = isset($_GET["country"]) ? $_GET["country"] : "AU";
	$code = $_GET["postalCode"];
	$suburb = $_GET["suburb"];

	$shippingType = ($country == "AU") ? "Domestic" : "International";

	$memcacheKey = $country."::".str_replace(" ", "_", strtolower($suburb))."::".$code;

	$memcache = new Memcached;
	$memcache->addServer('localhost', 11211);

	if ($_GET["_clear"]){
		$memcache->delete($memcacheKey);	
	}

	$cached = $memcache->get($memcacheKey);

	if ($cached){
		echo json_encode(array(
			"data" => $cached[$quantity],
			"status" => array(
				"code" => 0,
				"message" => "Success!"
			)
		));

		exit(10);
	} else {
		$totals = array();

		for ($i=0;$i<$typicalMax;$i++){
			$orderQuantity = $i+1;
			error_log("SEDNING REQUEST FOR " . $orderQuantity);
			$request = array(
				'anythings' => array(
					'anything' => array (
						0 => array(
							'class' => 'General Goods', 
							'subclass' => 'Household Goods',
							'packaging' => 'Box',
							'qualifierFreightGeneralFragile' => 'N',
							'weight' => 18000,
							'length' => 30,
							'width' => 20,
							'height' => 20,
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

			if ($_GET["debug"]){
				var_dump($response);
				error_log(var_export($response, true));
			}

			$quotes = array();

			foreach($response["quote"] as $quote){
				$quotes[ $quote["deliveryMethod"] ] = $quote["basePrice"];
				// if ($quote["deliveryMethod"] == "GENERAL ROAD"){
				// 		// echo "\$".$quote["basePrice"] ." - ". $quote["deliveryMethod"] ."<br/>";
				// }
			}

			$totals[$orderQuantity] = $quotes;
		}

		$memcache->set($memcacheKey, $totals); 
		
		exit();
	}