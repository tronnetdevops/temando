<?php
	/**
	 * @todo  Change HTTP_REFERER to be a specific access domain
	 *        ie: http://ontrapalooa.com
	 */
	ini_set('default_socket_timeout', 600);

	header("Access-Control-Allow-Origin: " . rtrim($_SERVER["HTTP_ORIGIN"], "/"));
	header("Access-Control-Allow-Credentials: true");

	$debug = isset($_GET["debug"]) ? $_GET["debug"] : 0;
	$typicalMax = 16; // = 400
	$booksInBox = 25;

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
		$memcache->delete($memcacheKey."::".$quantity);	
	}

	$cached = $memcache->get($memcacheKey."::".$quantity);

	if ($cached){
		echo json_encode(array(
			"data" => $cached,
			"status" => array(
				"code" => 0,
				"message" => "Success!"
			)
		));
	} else {
		Memcached::quit();

		for ($i=0;$i<$typicalMax;$i++){
			$orderQuantity = $i+1;
			Proc_Close (Proc_Open ("php -f ./fetch-script.php $memcacheKey $orderQuantity $country $code $suburb $price $paid $shippingType $debug &> /dev/null &", Array (), $orderQuantity));
		}

		echo json_encode(array(
			"data" => array(),
			"status" => array(
				"code" => 1,
				"message" => "Building shipping matrix!"
			)
		));
	}