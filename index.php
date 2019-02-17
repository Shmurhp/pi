<?php
require 'setup.php';

$result = $client->describeTable(array(
    'TableName' => 'pi-devel'
));

$_SESSION["favcolor"] = "green";
// print phpinfo();

$smarty->assign('hello_msg', 'Hello!');
$smarty->display('index.tpl');
?>
