<?php
require 'vendor/autoload.php';
date_default_timezone_set('America/New_York');

use Aws\DynamoDb\DynamoDbClient;
$client = new DynamoDbClient([
    #'region'  => 'us-east-1',
    'region' => $_SERVER["region"],
    'version' => 'latest'
]);

// put full path to Smarty.class.php
require('vendor/smarty/smarty/libs/Smarty.class.php');
$smarty = new Smarty();

$smarty->setTemplateDir('templates');
$smarty->setCompileDir('templates_c');
$smarty->setCacheDir('cache');
$smarty->setConfigDir('configs');

function getRealIpAddr()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP']))   //check ip from share internet
    {
      $ip=$_SERVER['HTTP_CLIENT_IP'];
    }
    elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))   //to check ip is pass from proxy
    {
      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else
    {
      $ip=$_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

?>
