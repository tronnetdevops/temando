<?php

abstract class AJAX {

	private static $_registered_post_methods = array();

	private static $_registered_get_methods = array();
	
	static public function Response($method = "json", $data = array(), $statusCode = 0, $statusMessage = "Success!") {
		switch($method){
			case "json":
			default:
				echo json_encode(array(
					"data" => $data,
					"status" => array(
						"code" => $statusCode,
						"message" => $statusMessage
					)
				));
				break;
		}

		return 1;
	}

	static function registerGetMethod($controller, $method){
		if (!isset(self::$_registered_get_methods[$controller])){
			self::$_registered_get_methods[$controller] = array();
		}

		/**
		 * @todo Take advantage of this in the future, make it a meaningful value.
		 */
		self::$_registered_get_methods[$controller][$method] = 1;
	}

	static function registerPostMethod($controller, $method){
		if (!isset(self::$_registered_post_methods[$controller])){
			self::$_registered_post_methods[$controller] = array();
		}

		/**
		 * @todo Take advantage of this in the future, make it a meaningful value.
		 */
		self::$_registered_post_methods[$controller][$method] = 1;
	}

	static function registerGetMethods($controller, $methods){
		foreach($methods as $method){
			self::registerGetMethod($controller, $method);
		}
	}

	static function registerPostMethods($controller, $methods){
		foreach($methods as $method){
			self::registerPostMethod($controller, $method);
		}
	}

	static public function isValidGetMethod($controller, $name){
		return (isset(self::$_registered_get_methods[$controller][$name])) ? 1 : 0;
	}

	static public function isValidPostMethod($controller, $name){
		return (isset(self::$_registered_post_methods[$controller][$name])) ? 1 : 0;
	}


	static public function Request($uri, $data = array()){
		$curl = curl_init($uri);
		$curl_post_data = array_merge($data);

		// var_dump($curl_post_data);
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		$curl_response = curl_exec($curl);
		curl_close($curl);

		// var_dump($curl_response);
		
		if (substr($curl_response, 0, 4) == "ERR:"){
			AJAX::Response("json", array(), 20, substr($curl_response, 5));
		}


		return $curl_response;
	}
}