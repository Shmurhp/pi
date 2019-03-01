<?php

require 'vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

$bucket = $_SERVER["s3bucket"];
$file_Path = "./excel_results/results.xlsx";
$key = "results.xlsx";

try {
    //Create a S3Client
    $s3Client = new S3Client([
        'region' => $_SERVER["region"],
        'version' => 'latest'
    ]);
    $result = $s3Client->putObject([
        'Bucket' => $bucket,
        'Key' => $key,
        'SourceFile' => $file_Path,
    ]);
} catch (S3Exception $e) {
    echo $e->getMessage() . "\n";
}

?>