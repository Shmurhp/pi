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
$sheet->setCellValueByColumnAndRow($col++,$row,'Birth Month #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Birth Month');
$sheet->setCellValueByColumnAndRow($col++,$row,'Birth Year #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Birth Year');
$sheet->setCellValueByColumnAndRow($col++,$row,'Income Level #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Income Level');
$sheet->setCellValueByColumnAndRow($col++,$row,'Education Level #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Education Level');
$sheet->setCellValueByColumnAndRow($col++,$row,'Race #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Race');
$sheet->setCellValueByColumnAndRow($col++,$row,'Ethnicity #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Ethnicity');
$sheet->setCellValueByColumnAndRow($col++,$row,'Weight Preference #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Weight Preference');
$sheet->setCellValueByColumnAndRow($col++,$row,'Other People Say #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Other People Say');
$sheet->setCellValueByColumnAndRow($col++,$row,'Currently I Am #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Currently I Am');
$sheet->setCellValueByColumnAndRow($col++,$row,'Most People #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Most People');
$sheet->setCellValueByColumnAndRow($col++,$row,'Compared to Most People #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Compared to Most People');
$sheet->setCellValueByColumnAndRow($col++,$row,'Height #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Height');
$sheet->setCellValueByColumnAndRow($col++,$row,'Weight #');
$sheet->setCellValueByColumnAndRow($col++,$row,'Weight');
$sheet->setCellValueByColumnAndRow($col++,$row,'BMI');

$tableName = $_SERVER["ddbtablea"];
// $tableName = 'pi-devel';

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

foreach ($result['Items'] as $r) {
    $col = 1;    
    
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['entryid'] ? $marshaler->unmarshalValue($r['entryid']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['groupid'] ? $marshaler->unmarshalValue($r['groupid']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['groupText'] ? $marshaler->unmarshalValue($r['groupText']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['clientIP'] ? $marshaler->unmarshalValue($r['clientIP']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['traits'] ? $marshaler->unmarshalValue($r['traits']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['resultText'] ? $marshaler->unmarshalValue($r['resultText']) : ''));
    $sheet->setCellValueByColumnAndRow($col++, $row, ($r['resultScore'] ? $marshaler->unmarshalValue($r['resultScore']) : ''));

    $demo = $marshaler->unmarshalValue($r['demographics']);
    foreach ($demo['birthMonth'] as $key => $value) {
    	$sheet->setCellValueByColumnAndRow($col++, $row, $key);
    	$sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    foreach ($demo['birthYear'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    foreach ($demo['incomeSelf'] as $key => $value) {    
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    foreach ($demo['edu'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    foreach ($demo['raceomb002'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    foreach ($demo['ethnicityomb'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    
    $exp = $marshaler->unmarshalValue($r['explicits']);

    foreach ($exp['att7'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    foreach ($exp['othersay001'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    foreach ($exp['iam001'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    foreach ($exp['mostpref001'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    foreach ($exp['comptomost001'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
    }
    foreach ($exp['myheight002'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
	$height = str_replace("'", "", $value);
    }
    foreach ($exp['myweight002'] as $key => $value) {
        $sheet->setCellValueByColumnAndRow($col++, $row, $key);
        $sheet->setCellValueByColumnAndRow($col++, $row, $value);
	$weight = str_replace("'", "", $value);
    }
    $bmi = (is_numeric($height) && is_numeric($weight)) ? (($weight*0.45)/(pow($height*0.025,2))) : 'Could Not Calculate';
    $sheet->setCellValueByColumnAndRow($col++, $row, $bmi);

    $row++;
}

$writer = new Xlsx($spreadsheet);
$writer->save('excel_results/results.xlsx');

shell_exec('php s3copy.php')
?>
