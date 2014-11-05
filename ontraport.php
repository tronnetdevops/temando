<?php
	error_reporting(-1);
	header("Access-Control-Allow-Origin: ". trim($_SERVER["HTTP_REFERER"], "/") );

	session_start();

	include('./resources/AJAX.php');

	$requiredFields = array("first-name", "last-name", "email", "office-phone", "organisation-job-title");

	$teachers = $_REQUEST["teachers"];

	if (is_array($teachers)){
		foreach($teachers as $teacher){
			if (!count(array_assoc_diff($teacher, $requiredFields))){
					// Construct contact data in XML format
				$data = '<contact>
				    <Group_Tag name="Contact Information">
				        <field name="First Name">'.$teacher["fname"].'</field>
				        <field name="Last Name">'.$teacher["fname"].'</field>
				        <field name="Email">'.$teacher["email"].'</field>
				        <field name="Position">'.$teacher["position"].'</field>
				        <field name="Office Phone">'.$teacher["phone"].'</field>
				    </Group_Tag>
				    <Group_Tag name="Sequences and Tags">
				        <field name="Contact Tags">Test</field>
				        <field name="Sequences">'.$teacher["fname"].'</field>
				    </Group_Tag>
				</contact>';

				$data = urlencode(urlencode($data));

				//Set your request type and construct the POST request
				$reqType= "add";
				$postargs = "appid=".$appid."&key=".$key."&return_id=1&reqType=".$reqType."&data=".$data;

				$contactCreated = OntraportAPI::Request(array("data" => $data), array_merge(BlueLogicAPI::$requestBaseData, array(
					"reqType" => "add",
					"return_id" => 1
				)));

				if ($contactCreated["status"] == "OK"){
					AJAX::Response("json", $data);
				} else {
					AJAX::Response("json", array(), 1, $createOrderLineResponse);
				}
			}
		}
	}