<?php
	error_reporting(-1);
	header("Access-Control-Allow-Origin: ". trim($_SERVER["HTTP_REFERER"], "/") );

	session_start();

	include('./modules/AJAX.php');
	include('./modules/OntraportAPI.php');

	$sequenceByOriginMap = array(
		"SS-Original-Teacher-Registration" => "*/*16*/*",
		"SS-Digital-Teacher-Registration" => "*/*75*/*",
		"SS-Original-Digital-Teacher-Registration" => "*/*76*/*",
		"KE-Original-Teacher-Registration" => "*/*33*/*",
		"KE-Digital-Teacher-Registration" => "*/*77*/*",
		"KE-Original-Digital-Teacher-Registration" => "*/*78*/*"
	);

	$requiredFields = array("first-name", "last-name", "email", "office-phone", "organisation-job-title");

	$teachers = $_REQUEST["teachers"];
	$failed = 0;
	$created = array();
	$sequence = $sequenceByOriginMap[ $_REQUEST["form-origin"] ];

	if (is_array($teachers) && isset($sequence)){
		foreach($teachers as $teacher){
			if (!count(array_diff(array_keys($teacher), $requiredFields))){
				// Construct contact data in XML format
				$data = <<<STRING
<contact>
    <Group_Tag name="Contact Information">
        <field name="First Name">${teacher["first-name"]}</field>
        <field name="Last Name">${teacher["last-name"]}</field>
        <field name="Email">${teacher["email"]}</field>
        <field name="Office Phone">${teacher["office-phone"]}</field>
    </Group_Tag>
    <Group_Tag name="Sequences and Tags">
        <field name="Contact Tags"></field>
        <field name="Sequences">$sequence</field>
    </Group_Tag>
</contact>
STRING;

				$created[] = OntraportAPI::Request("CreateContact", 
					array(
						"data" => urlencode($data)
					),
					array(
						"reqType" => "add",
						"return_id" => 1
					)
				);
			} else {
				error_log("Teacher:");
				error_log(var_export($teacher, true));

				error_log("Teacher didnt have all fields filled out: " . var_export(array_diff(array_keys($teacher), $requiredFields), true));
				$failed++;
			}
		}
	}