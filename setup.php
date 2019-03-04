<?php
require 'vendor/autoload.php';
date_default_timezone_set('America/New_York');

// required if you want scripts that are called with shell_exec
// to utilize the server vars we establish in the apache conf
putenv("ENV=".getenv("ENV"));
putenv("ddbtablea=".getenv("ddbtablea"));
putenv("ddbtableb=".getenv("ddbtableb"));
putenv("region=".getenv("region"));
putenv("s3bucket=".getenv("s3bucket"));

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;
$sdk = new Aws\Sdk([
  'region' => $_SERVER["region"],
  'version' => 'latest'
]);
$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();

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
