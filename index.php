<?php
session_start();
require 'smarty_setup.php';

$_SESSION["favcolor"] = "green";

$smarty->assign('hello_msg', 'Hello!');
$smarty->display('index.tpl');
?>
