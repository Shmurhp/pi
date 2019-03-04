<?php
	require 'setup.php';
	require 'vendor/autoload.php';	

	$entryid = time();
	$clientIP = getRealIpAddr();
	$resultText = $_POST["resultText"];
	$demoQuestions = $_POST["demoQuestions"];
	$groupID = strtoupper($_POST["groupID"]);
	$explicits = $_POST["explicits"];
	$traits = $_POST["traits"];

	$requiredDemo = array('raceomb002', 'birthMonth', 'incomeSelf', 'edu', 'genderIdentity', 'edu', 'ethnicityomb', 'occuSelf');
	$requiredExplicit = array('att7', 'othersay001', 'iam001', 'mostpref001', 'comptomost001', 'myheight002', 'myweight002');
	$tableName = $_SERVER["ddbtableb"];
	$key = $marshaler->marshalJson('
   		{
      		"name": "group"
    	}
	');	
	$params = [
		'TableName' => $tableName,
		'Key' => $key
	];
	try {
		$result = $dynamodb->getItem($params);
		$groupText = strlen($result["Item"]["lookup"]["M"]["$groupID"]["S"]) > 0 ? $result["Item"]["lookup"]["M"]["$groupID"]["S"] : 'Invalid Group ID';
	} catch (DynamoDbException $e) {
		echo "Unable to get item:\n";
		echo $e->getMessage() . "\n";
	}

	$demoArr = array();
	foreach ($requiredDemo as $lookup) {
		if (strlen($demoQuestions[$lookup]['response']) == 0 || $demoQuestions[$lookup]['response'] == 'NaN'){
			$demoArr[$lookup][-1] = 'Declined to Answer';
		} else {
			// query the $lookup table with value they answered in $demoQuestions[$lookup]
			// build up array of demo answers with key = [$lookup][numeric answer value] and value = text from lookup table
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
				// value in occuSelf lookup row is actually the value of the row of the child 
				// so save that off to query later
				$occuSelfVal = $result["Item"]["lookup"]["M"][$demoQuestions[$lookup]['response']]["S"]; 
			} else {
				$demoArr[$lookup][$demoQuestions[$lookup]['response']] = $lookupResult;
			}
		}
	}
	$birthKey = is_numeric($demoQuestions['birthYear']['response']) ? $demoQuestions['birthYear']['response'] : -1;
	$demoArr['birthYear'][$birthKey] = (is_numeric($demoQuestions['birthYear']['response'])) ? 2010 - $demoQuestions['birthYear']['response'] : 9999;
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
	$occuSelfKey = is_numeric($demoQuestions['occuSelfDetail']['response']) ? $demoQuestions['occuSelfDetail']['response'] : -1;
	$demoArr['occuSelfDetail'][$occuSelfKey] = $lookupResult == 'Unemployed' ? 'Unemployed' : $lookupResult;
	$explicitArr = array();
	foreach ($requiredExplicit as $lookup) {
		if (strlen($explicits[$lookup]['response']) == 0 || $explicits[$lookup]['response'] == 'NaN'){
			$explicitArr[$lookup][-1] = 'Declined to Answer';
		} else {
			switch ($lookup){
					case 'myweight002':
						// math to convert $explicits[$lookup]['response'] to lbs
						if ($explicits[$lookup]['response'] == 1){
							$explicitArr[$lookup][$explicits[$lookup]['response']] = '< 50';
						} elseif ($explicits[$lookup]['response'] == 81) {
							$explicitArr[$lookup][$explicits[$lookup]['response']] = '> 440';
						} else {
							$response = $explicits[$lookup]['response'];
							$explicitArr[$lookup][$explicits[$lookup]['response']] = (($explicits[$lookup]['response'] * 5) + 40);
						}
						break;
					case 'myheight002': 
						// math to convert $explicits[$lookup]['response'] to inches
						if ($explicits[$lookup]['response'] == 1){
							$explicitArr[$lookup][$explicits[$lookup]['response']] = '< 36';
						} elseif ($explicits[$lookup]['response'] == 51) {
							$explicitArr[$lookup][$explicits[$lookup]['response']] = '> 84';
						} else {
							$explicitArr[$lookup][$explicits[$lookup]['response']] = ($explicits[$lookup]['response'] + 34);
						}
						break;
					default:
						if (strlen($explicits[$lookup]['response']) == 0){
							$explicitArr[$lookup] = 'Declined to Answer';
						} else {
							// query the $lookup table with value they answered in $explicits[$lookup]
							// build up array of explicit answers with key = [$lookup][numeric answer value] and value = text from lookup table
							$explicitVal = 3;
							for ($i = 1; $i < $explicits[$lookup]['response']; $i++){
								$explicitVal -= 1;
							}
							$explicitArr[$lookup][$explicits[$lookup]['response']] = $explicitVal;
						}	
						break;
			}
		}
	}
	$resultTextArr = array();
	$resultTextArr['Your data suggest a strong automatic preference for Fat people over Thin people.'] = 3;
	$resultTextArr['Your data suggest a moderate automatic preference for Fat people over Thin people.'] = 2;
	$resultTextArr['Your data suggest a slight automatic preference for Fat people over Thin people.'] = 1;
	$resultTextArr['Your data suggest no automatic preference between Fat people and Thin people.'] = 0;
	$resultTextArr['Your data suggest a slight automatic preference for Thin people over Fat people.'] = -1;
	$resultTextArr['Your data suggest a moderate automatic preference for Thin people over Fat people.'] = -2;
	$resultTextArr['Your data suggest a strong automatic preference for Thin people over Fat people.'] = -3;

	$resultScore = is_numeric($resultTextArr[$resultText]) ? $resultTextArr[$resultText] : 999;

	$tableName = $_SERVER["ddbtablea"];
	$item = $marshaler->marshalJson('
    	{
        	"clientIP": "' . $clientIP . '",
        	"entryid": ' . $entryid . ',
        	"groupid": "' . $groupID . '",
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
	shell_exec('php generate_excel.php')
?>
