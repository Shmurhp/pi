<?php

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

$entryid = $_POST["entryid"];
$groupid = $_POST["groupid"];

$item = $marshaler->marshalJson('
    {
        "entryid": ' . $entryid . ',
        "groupid": ' . $groupid . '
    }
');

$params = [
    'TableName' => $tableName,
    'Item' => $item
];


try {
    $result = $dynamodb->putItem($params);
    echo "Added item: $entryid - $groupid\n";

} catch (DynamoDbException $e) {
    echo "Unable to add item:\n";
    echo $e->getMessage() . "\n";
}

?>
