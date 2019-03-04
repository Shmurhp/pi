<?php

require 'vendor/autoload.php';
require 'setup.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

use Aws\DynamoDb\Exception\DynamoDbException;
use Aws\DynamoDb\Marshaler;

date_default_timezone_set('UTC');

$spreadsheet = new Spreadsheet();

$sheet = $spreadsheet->getActiveSheet();

// Header Row

$row = 1;
$col = 1;
$sheet->setCellValueByColumnAndRow($col++,$row,'Timestamp');
$sheet->setCellValueByColumnAndRow($col++,$row,'Group ID');
$sheet->setCellValueByColumnAndRow($col++,$row,'Group ID Text');
$sheet->setCellValueByColumnAndRow($col++,$row,'Client IP');
$sheet->setCellValueByColumnAndRow($col++,$row,'Mentee Traits');
$sheet->setCellValueByColumnAndRow($col++,$row,'Result Text');
$sheet->setCellValueByColumnAndRow($col++,$row,'Result Score');
$sheet->setCellValueByColumnAndRow($col++,$row,'Birth Month');
$sheet->setCellValueByColumnAndRow($col++,$row,'Birth Year');
$sheet->setCellValueByColumnAndRow($col++,$row,'Income Level');
$sheet->setCellValueByColumnAndRow($col++,$row,'Education Level');
$sheet->setCellValueByColumnAndRow($col++,$row,'Race');
$sheet->setCellValueByColumnAndRow($col++,$row,'Ethnicity');
$sheet->setCellValueByColumnAndRow($col++,$row,'Weight Preference');
$sheet->setCellValueByColumnAndRow($col++,$row,'Other People Say');
$sheet->setCellValueByColumnAndRow($col++,$row,'Currently I am');
$sheet->setCellValueByColumnAndRow($col++,$row,'Most People');
$sheet->setCellValueByColumnAndRow($col++,$row,'Compared to Most People');
$sheet->setCellValueByColumnAndRow($col++,$row,'Height');
$sheet->setCellValueByColumnAndRow($col++,$row,'Weight');

$tableName = $_SERVER["ddbtablea"];
#$tableName = 'pi-devel';

$params = [
    'TableName' => $tableName
];

try {
    $result = $dynamodb->scan($params);
} catch (DynamoDbException $e) {
    echo "Unable to query:\n";
    echo $e->getMessage() . "\n";
}

$row = 2;

//print_r($result['Items'][0]);

foreach ($result['Items'] as $r) {
    $col = 1;    
    
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['entryid'] ? $marshaler->unmarshalValue($r['entryid']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['groupid'] ? $marshaler->unmarshalValue($r['groupid']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['groupText'] ? $marshaler->unmarshalValue($r['groupText']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['clientIP'] ? $marshaler->unmarshalValue($r['clientIP']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['traits'] ? $marshaler->unmarshalValue($r['traits']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['resultText'] ? $marshaler->unmarshalValue($r['resultText']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['resultScore'] ? $marshaler->unmarshalValue($r['resultScore']) : ''));

    //print $marshaler->unmarshalValue($r['demographics']['birthYear']);


    $demo = $marshaler->unmarshalValue($r['demographics']);
    //print $demo['birthYear'];
    //  print_r($demo);
    /*
    $sheet->setCellValueByColumnAndRow($col++,$row,'Education Level');
$sheet->setCellValueByColumnAndRow($col++,$row,'User Preference');
$sheet->setCellValueByColumnAndRow($col++,$row,'Other People');
$sheet->setCellValueByColumnAndRow($col++,$row,'Currently I am');
$sheet->setCellValueByColumnAndRow($col++,$row,'Most People');
$sheet->setCellValueByColumnAndRow($col++,$row,'Compared to Most People');
$sheet->setCellValueByColumnAndRow($col++,$row,'Height');
$sheet->setCellValueByColumnAndRow($col++,$row,'Weight');
*/
    $sheet->setCellValueByColumnAndRow($col++, $row, $demo['birthMonth']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $demo['birthYear']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $demo['incomeSelf']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $demo['edu']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $demo['raceomb002']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $demo['ethnicityomb']);
    $exp = $marshaler->unmarshalValue($r['explicits']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $exp['att7']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $exp['othersay001']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $exp['iam001']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $exp['mostpref001']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $exp['comptomost001']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $exp['myheight002']);
    $sheet->setCellValueByColumnAndRow($col++, $row, $exp['myweight002']);


//    $sheet->setCellValueByColumnAndRow($col++,$row,($r['clientIP'] ? $marshaler->unmarshalValue($r['clientIP']) : ''));
  //  $sheet->setCellValueByColumnAndRow($col++,$row,($r['age'] ? $marshaler->unmarshalValue($r['age']) : ''));
    $row++;
}


$writer = new Xlsx($spreadsheet);
$writer->save('excel_results/results.xlsx');

shell_exec('php s3copy.php')

?>
