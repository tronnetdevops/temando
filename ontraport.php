<?php
	error_reporting(-1);
	header("Access-Control-Allow-Origin: ". trim($_SERVER["HTTP_REFERER"], "/") );

	session_start();

	include('./modules/AJAX.php');
	include('./modules/OntraportAPI.php');

	$failed = array();
	$created = array();

	$sequenceByOriginMap = array(
		"SS-Original-Teacher-Registration" => "*/*16*/*",
		"SS-Digital-Teacher-Registration" => "*/*75*/*",
		"SS-Original-Digital-Teacher-Registration" => "*/*76*/*",
		"KE-Original-Teacher-Registration" => "*/*33*/*",
		"KE-Digital-Teacher-Registration" => "*/*77*/*",
		"KE-Original-Digital-Teacher-Registration" => "*/*78*/*"
	);

	$redirectByOriginMap = array(
		"SS-Original-Teacher-Registration" => "http://musicedu.pages.ontraport.net/SS-Teacher-Thank-You",
		"SS-Digital-Teacher-Registration" => "http://musicedu.pages.ontraport.net/SS-Teacher-Thank-You",
		"SS-Original-Digital-Teacher-Registration" => "http://musicedu.pages.ontraport.net/SS-Teacher-Thank-You",
		"KE-Original-Teacher-Registration" => "http://musicedu.pages.ontraport.net/KE-Teacher-Thank-You",
		"KE-Digital-Teacher-Registration" => "http://musicedu.pages.ontraport.net/KE-Teacher-Thank-You",
		"KE-Original-Digital-Teacher-Registration" => "http://musicedu.pages.ontraport.net/KE-Teacher-Thank-You"
	);

	$requiredFields = array("first-name", "last-name", "email", "office-phone", "organisation-job-title");
	$origin = $_REQUEST["form-origin"];
	$teachers = $_REQUEST["teachers"];
	$sequence = $sequenceByOriginMap[ $origin ];

	if (is_array($teachers) && isset($sequence)){
		foreach($teachers as $teacher){
			if (!count(array_diff(array_keys($teacher), $requiredFields))){
				$data = <<<STRING
<contact>
    <Group_Tag name="Contact Information">
        <field name="First Name">${teacher["first-name"]}</field>
        <field name="Last Name">${teacher["last-name"]}</field>
        <field name="Email">${teacher["email"]}</field>
        <field name="Office Phone">${teacher["office-phone"]}</field>
        <field name="Zip Code">${_REQUEST["organisation-zip"]}</field>
        <field name="City">${_REQUEST["organisation-city"]}</field>
        <field name="State">${_REQUEST["organisation-state"]}</field>
    </Group_Tag>
    <Group_Tag name="Sequences and Tags">
        <field name="Contact Tags"></field>
        <field name="Sequences">$sequence</field>
    </Group_Tag>
    <Group_Tag name="MusicEDU">
        <field name="Organisation -Job Title">${teacher["organisation-job-title"]}</field>
        <field name="Organisation - School">${_REQUEST["organisation-school"]}</field>
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
				$failed[] = array_diff(array_keys($teacher), $requiredFields);
			}
		}
	}

	header("Location: " . $redirectByOriginMap[ $origin ]);