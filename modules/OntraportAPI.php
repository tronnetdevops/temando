<?php
	abstract class OntraportAPI {

		const API_URI = "http://api.ontraport.com/cdata.php";

		static public $requestBaseData = array(
			"appid" => "",
			"key" => ""
		);

		static public $available_calls = array(
			"CreateContact" => array(
				"data"
			)
		);

		static public function MeetsRequirements($params, $reqs){
			$data = array();

			$intersects = array_intersect(array_keys($params), $reqs);

			if (count($intersects) == count($reqs)){

				foreach($reqs as $req){
					$data[$req] = $params[$req];
				}

				return $data;
			} else {
				return "The following fields are missing: ". implode(array_diff($reqs, $intersects), ", ");
			}
		}

		static public function Request($action, $params, $data){
			$data = array_merge(OntraportAPI::$requestBaseData, $data);

			$submittedValues = self::MeetsRequirements($params, self::$available_calls[ $action ]);

			if (is_array($submittedValues)){
				$data = array_merge($submittedValues, $data);

			 	$response = AJAX::Request(self::API_URI, $data);

				return $response;
			} else {
				AJAX::Response("json", array(), 1, $submittedValues);
			}
		}
	}
