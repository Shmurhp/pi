<?php
require 'vendor/autoload.php';
date_default_timezone_set('America/New_York');

use Aws\DynamoDb\DynamoDbClient;
$client = new DynamoDbClient([
    'profile' => 'default',
    'region'  => 'us-east-1',
    'version' => 'latest'
]);

// put full path to Smarty.class.php
require('vendor/smarty/smarty/libs/Smarty.class.php');
$smarty = new Smarty();

$smarty->setTemplateDir('templates');
$smarty->setCompileDir('templates_c');
$smarty->setCacheDir('cache');
$smarty->setConfigDir('configs');

?>
