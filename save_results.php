<?php
	require 'setup.php';
	require 'vendor/autoload.php';	
	$entryid = time();
	$clientIP = getRealIpAddr();
	$resultText = $_POST["resultText"];
	$demoQuestions = $_POST["demoQuestions"];
	$groupID = $_POST["groupID"];
	$groupText = ($groupID < 8 && $groupID > 0) ? $groupLookup[$_POST["groupID"]] : 'Invalid Group ID';
	$explicits = $_POST["explicits"];
	$traits = $_POST["traits"];

	//code here to call something to write everything to file

	$groupLookup = array();
	$groupLookup[1] = 'Coalition of Hispanic Women';
	$groupLookup[2] = 'NAACP';
	$groupLookup[3] = 'Alpha Kappa Alpha Sorority, Inc.';
	$groupLookup[4] = 'Kansas City Women in Technology';
	$groupLookup[5] = 'Delta Sigma Theta, Sorority, Inc.';
	$groupLookup[6] = 'Women\'s Foundation';
	$groupLookup[7] = 'KC Council Women Business Owners';

	$requiredDemo = array('raceomb002', 'birthMonth', 'incomeSelf', 'edu', 'genderIdentity', 'edu', 'ethnicityomb', 'occuSelf');
	$requiredExplicit = array('att7', 'othersay001', 'iam001', 'mostpref001', 'comptomost001', 'myheight002', 'myweight002');

	$tableName = $_SERVER["ddbtableb"];
	$demoArr = array();
	foreach ($requiredDemo as $lookup) {
		if (strlen($demoQuestions[$lookup]['response']) == 0 || $demoQuestions[$lookup]['response'] == 'NaN'){
			$demoArr[$lookup] = 'Declined to Answer';
		} else {
			// query the $lookup table with value they answered in $demoQuestions[$lookup]
			// build up array of demo answers with key = $lookup and value = what was returned from above
			// print $key['name'] . " : " . $key['response'] . "\n";
			$key = $marshaler->marshalJson('
   				{
        			"name": "' . $lookup . '"
    			}
			');
			$params = [
				'TableName' => $tableName,
				'Key' => $key
			];
			try {
				$result = $dynamodb->getItem($params);
				$lookupResult = $result["Item"]["lookup"]["M"][$demoQuestions[$lookup]['response']]["S"];
			} catch (DynamoDbException $e) {
				echo "Unable to get item:\n";
				echo $e->getMessage() . "\n";
			}
			if ($lookup == 'occuSelf'){
				$occuSelfVal = $result["Item"]["lookup"]["M"][$demoQuestions[$lookup]['response']]["S"]; 
			} else {
				$demoArr[$lookup] = "'" . $lookupResult. "'";
			}
		}
	}
	$demoArr['birthYear'] = (is_numeric($demoQuestions['birthYear']['response'])) ? 2010 - $demoQuestions['birthYear']['response'] : 9999;
	// gonna need to lookup occupation title using occuSelf value here
	if (strlen($occuSelfVal > 0)){
		$key = $marshaler->marshalJson('
   			{
     	  		"name": "' . $occuSelfVal . '"
    		}
		');	
		$params = [
			'TableName' => $tableName,
			'Key' => $key
		];
		try {
			$result = $dynamodb->getItem($params);
			$lookupResult = $result["Item"]["lookup"]["M"][$demoQuestions['occuSelfDetail']['response']]["S"];
		} catch (DynamoDbException $e) {
			echo "Unable to get item:\n";
			echo $e->getMessage() . "\n";
		}
	} else {
		$lookupResult = 'Declined to Answer';
	}
	$demoArr['occuSelfDetail'] = $lookupResult == 'Unemployed' ? 'Unemployed' : $lookupResult;
	//
	$explicitArr = array();
	foreach ($requiredExplicit as $lookup) {
		if (strlen($explicits[$lookup]['response']) == 0 || $explicits[$lookup]['response'] == 'NaN'){
			$explicitArr[$lookup] = 'Declined to Answer';
		} else {
			switch ($lookup){
					case 'myweight002':
						// math to convert $explicits[$lookup]['response'] to inches
						if ($explicits[$lookup]['response'] == 1){
							$explicitArr[$lookup] = '< 50';
						} elseif ($explicits[$lookup]['response'] == 51) {
							$explicitArr[$lookup] = '> 400';
						} else {
							$response = $explicits[$lookup]['response'];
							$explicitArr[$lookup] = "'" . (($explicits[$lookup]['response'] * 5) + 40) . "'";
						}
						break;
					case 'myheight002': 
						// math to convert $explicits[$lookup]['response'] to inches
						if ($explicits[$lookup]['response'] == 1){
							$explicitArr[$lookup] = '< 36';
						} elseif ($explicits[$lookup]['response'] == 51) {
							$explicitArr[$lookup] = '> 84';
						} else {
							$explicitArr[$lookup] = "'" . ($explicits[$lookup]['response'] + 34) . "'";
						}
						break;
					default:
						if (strlen($explicits[$lookup]['response']) == 0){
							$explicitArr[$lookup] = 'Declined to Answer';
						} else {
							// query the $lookup table with value they answered in $demoQuestions[$lookup]
							// build up array of demo answers with key = $lookup and value = what was returned from above
							// print $key['name'] . " : " . $key['response'] . "\n";
							$explicitVal = 3;
							for ($i = 1; $i < $explicits[$lookup]['response']; $i++){
								$explicitVal -= 1;
							}
							$explicitArr[$lookup] = "'" . $explicitVal . "'";
							
							// $requiredExplicit = array('att7', 'othersay001', 'iam001', 'mostpref001', 'comptomost001', 'myheight002', 'myweight002');
						}	
						break;
			}
		}
	}
	$resultTextArr = array();
	$resultTextArr['Your data suggest a strong automatic preference for Fat people over Thin people.'] = 3;
	$resultTextArr['Your data suggest a moderate automatic preference for Fat people over Thin people.'] = 2;
	$resultTextArr['Your data suggest a slight automatic preference for Fat people over Thin people.'] = 1;
	$resultTextArr['Your data suggest no automatic preference between categoryA and categoryB.'] = 0;
	$resultTextArr['Your data suggest a slight automatic preference for Thin people over Fat people.'] = -1;
	$resultTextArr['Your data suggest a moderate automatic preference for Thin people over Fat people.'] = -2;
	$resultTextArr['Your data suggest a strong automatic preference for Thin people over Fat people.'] = -3;

	$resultScore = is_numeric($resultTextArr[$resultText]) ? $resultTextArr[$resultText] : 999;

	$tableName = $_SERVER["ddbtablea"];
	$item = $marshaler->marshalJson('
    	{
        	"clientIP": "' . $clientIP . '",
        	"entryid": ' . $entryid . ',
        	"groupid": ' . $groupID . ',
			"groupText": "' . $groupText . '",
			"demographics": ' . json_encode($demoArr) . ',
        	"explicits": ' . json_encode($explicitArr) . ',
			"resultScore": ' . $resultScore . ',
			"traits": "' . $traits . '",
        	"resultText": "' . $resultText . '"

    	}
	');
	$params = [
    	'TableName' => $tableName,
    	'Item' => $item
	];
	try {
	    	$result = $dynamodb->putItem($params);
	} catch (DynamoDbException $e) {
    		echo "Unable to add item:\n";
			echo $e->getMessage() . "\n";
	}
?>
