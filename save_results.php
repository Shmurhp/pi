<?php
	require 'setup.php';
	require 'vendor/autoload.php';
	

	$groupLookup = array();
	$groupLookup[1] = 'Coalition of Hispanic Women';
	$groupLookup[2] = 'NAACP';
	$groupLookup[3] = 'Alpha Kappa Alpha Sorority, Inc.';
	$groupLookup[4] = 'Kansas City Women in Technology';
	$groupLookup[5] = 'Delta Sigma Theta, Sorority, Inc.';
	$groupLookup[6] = 'Women\'s Foundation';
	$groupLookup[7] = 'KC Council Women Business Owners';

	$requiredDemo = array('raceomb002', 'birthmonth', 'birthyear', 'income', 'edu', 'genderIdentity', 'edu', 'occuSelf', 'ethnicityomb');
	$requiredExplicit = array('att7', 'othersay001', 'iam001', 'mostpref001', 'comptomost001', 'myheight002', 'myweight002');

	$clientIP = getRealIpAddr();
	$resultText = $_POST["resultText"];
	$demoQuestions = $_POST["demoQuestions"];
	$groupID = $groupID[$_POST["groupID"]];
	$explicits = $_POST["explicits"];
	$trait = $_POST["trait"];

	$tableName = 'pi-devel-lookup';
	$demoArr = array();
	foreach ($requiredDemo as $lookup) {
		if (strlen($demoQuestions[$lookup]['response']) == 0){
			$demoArr[$lookup] = 'empty';
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
				print_r($result["Item"]);
				$lookupResult = $result["Item"]["lookup"]["M"][$demoQuestions[$lookup]]["S"];
			} catch (DynamoDbException $e) {
				echo "Unable to get item:\n";
				echo $e->getMessage() . "\n";
			}
			$demoArr[$lookup] = $lookupResult;
		}
	}
	$explicitArr = array();
	foreach ($requiredExplicit as $lookup) {
		if (strlen($explicits[$lookup]['response']) == 0){
			$explicitArr[$lookup] = 'empty';
		} else {
			switch ($lookup){
					case myheight002:
						// math to convert $explicits[$lookup]['response'] to inches
						if ($explicits[$lookup]['response'] == 1){
							$heighInches = '< 36';
						} elseif ($explicits[$lookup]['response'] == 51) {
							$heighInches = '> 84';
						}
						$heightInches = $explicits[$lookup]['response'];
						$explicitArr[$lookup] = $height;
						break;
					case myweight002: 
						// math to convert $explicits[$lookup]['response'] to inches
						$explicitArr[$lookup] = $weight;
						break;
					default:
						if (strlen($explicits[$lookup]['response']) == 0){
							$explicitArr[$lookup] = 'empty';
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
								print_r($result["Item"]);
								$lookupResult = $result["Item"]["lookup"]["M"][$demoQuestions[$lookup]]["S"];
							} catch (DynamoDbException $e) {
								echo "Unable to get item:\n";
								echo $e->getMessage() . "\n";
								$lookupResult = '999';
							}
							$explicitArr[$lookup] = $lookupResult;
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

	$resultText = is_numeric($resultTextArr[$resultText]) ? $resultTextArr[$resultText] : 999;

	$tableName = 'pi-devel';
	$item = $marshaler->marshalJson('
    	{
        	"clientIP": "' . $clientIP . '",
        	"entryid": ' . time() . ',
        	"groupid": ' . $groupID . ',
        	"demographics": ' . json_encode($demoArr) . ',
        	"explicits": ' . json_encode($explicitArr) . ',
        	"resultScore": "' . $resultScore . '"
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

// this is where we will save anything off to the database that we decide we want to

?>
