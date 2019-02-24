<?php

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

date_default_timezone_set('UTC');

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();

// Header Row

$row = 1;
$col = 0;
$sheet->setCellValueByColumnAndRow($col++,$row,'Timestamp');
$sheet->setCellValueByColumnAndRow($col++,$row,'Group ID');
$sheet->setCellValueByColumnAndRow($col++,$row,'Mentee Traits');
$sheet->setCellValueByColumnAndRow($col++,$row,'Age');
$sheet->setCellValueByColumnAndRow($col++,$row,'Income Level');
$sheet->setCellValueByColumnAndRow($col++,$row,'Education Level');
$sheet->setCellValueByColumnAndRow($col++,$row,'User Preference');
$sheet->setCellValueByColumnAndRow($col++,$row,'Other People');
$sheet->setCellValueByColumnAndRow($col++,$row,'Currently I am');
$sheet->setCellValueByColumnAndRow($col++,$row,'Most People');
$sheet->setCellValueByColumnAndRow($col++,$row,'Compared to Most People');
$sheet->setCellValueByColumnAndRow($col++,$row,'Height');
$sheet->setCellValueByColumnAndRow($col++,$row,'Weight');
$sheet->setCellValueByColumnAndRow($col++,$row,'Bias');
$sheet->setCellValueByColumnAndRow($col++,$row,'IAT Score');

$sdk = new Aws\Sdk([
    'region'   => 'us-east-1',
    'version'  => 'latest'
]);

$dynamodb = $sdk->createDynamoDb();
$marshaler = new Marshaler();

$tableName = 'pi-devel';

$params = [
    'TableName' => $tableName
];

try {
    $result = $dynamodb->scan($params);

    $row = 2;
    foreach ($result['Items'] as $r) {
	$col = 1;
	$sheet->setCellValueByColumnAndRow($col++,$row,($r['entryid'] ? $marshaler->unmarshalValue($r['entryid']) : ''));
	$sheet->setCellValueByColumnAndRow($col++,$row,($r['groupid'] ? $marshaler->unmarshalValue($r['groupid']) : ''));
	$sheet->setCellValueByColumnAndRow($col++,$row,($r['clientIP'] ? $marshaler->unmarshalValue($r['clientIP']) : ''));
	$sheet->setCellValueByColumnAndRow($col++,$row,($r['age'] ? $marshaler->unmarshalValue($r['age']) : ''));
	$row++;
    }

} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}



$writer = new Xlsx($spreadsheet);
$writer->save('excel_results/hello_world.xlsx');



?>
