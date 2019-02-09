<?php
// put full path to Smarty.class.php
require('Smarty/Smarty.class.php');
$smarty = new Smarty();

$smarty->setTemplateDir('templates');
$smarty->setCompileDir('templates_c');
$smarty->setCacheDir('cache');
$smarty->setConfigDir('configs');

?>