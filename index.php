<?php
	/**
	 * @todo  Change HTTP_REFERER to be a specific access domain
	 *        ie: http://ontrapalooa.com
	 */
	header("Access-Control-Allow-Origin: " . rtrim($_SERVER["HTTP_ORIGIN"], "/"));
	header("Access-Control-Allow-Credentials: true");

	session_start();

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
		$cached["total_boxes"] = $quantity;
		echo json_encode(array(
			"data" => $cached,
			"status" => array(
				"code" => 0,
				"message" => "Success!"
			)
		));
	} else {
		$message = "Still building shipping matrix!";

		if (!$_SESSION["working"]){
			
			$sess_id = session_id();
			$_SESSION["working"] = true;
			$_SESSION["working_pos"] = 0;

			for ($i=0;$i<$typicalMax;$i++){
				$orderQuantity = $i+1;
				Proc_Close (Proc_Open ("php -f ./fetch-script.php $memcacheKey $orderQuantity $country $code $suburb $price $paid $shippingType $sess_id $debug &> /dev/null &", Array (), $orderQuantity));
			}

			$message = "Building shipping matrix!";
		}

		echo json_encode(array(
			"data" => array(),
			"status" => array(
				"code" => 1,
				"message" => $message
			)
		));
	}

	exit();