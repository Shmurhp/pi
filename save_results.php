<?php
	require 'setup.php';
	require 'vendor/autoload.php';
	date_default_timezone_set('UTC');
	use Aws\DynamoDb\Exception\DynamoDbException;
	use Aws\DynamoDb\Marshaler;
	$sdk = new Aws\Sdk([
    		'region'   => 'us-east-1',
    		'version'  => 'latest'
	]);
	$dynamodb = $sdk->createDynamoDb();
	$marshaler = new Marshaler();
	$tableName = 'pi-devel';

	$clientIP = getRealIpAddr();
	$resultText = $_POST["resultText"];
	$demoQuestions = $_POST["demoQuestions"];
	$groupID = $_POST["groupID"];
	$answers = $_POST["answers"];

	print "$clientIP\n";
	print "$groupID\n";
	print "$resultText\n";

	$demoArr = array();
	print "<pre>";
	print_r($demoQuestions);
	print "</pre>";
	foreach ($demoQuestions as $key) {
		if (strlen($key['name']) > 0) {
			if (strlen($key['response']) == 0){
				$demoArr[$key['name']] = 'empty';
			} else {
				print $key['name'] . " : " . $key['response'] . "\n";
				$demoArr[$key['name']] = $key['response'];	
			}
		} 
	}

	$item = $marshaler->marshalJson('
    	{
        	"clientIP": "' . $clientIP . '",
        	"entryid": ' . time() . ',
        	"groupid": ' . $groupID . ',
        	"demoQuestions": ' . json_encode($demoArr) . ',
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
