<?php


// put full path to Smarty.class.php
require('smarty/libs/Smarty.class.php');

$smarty = new Smarty();

$smarty->setTemplateDir('smarty/templates');
$smarty->setCompileDir('smarty/templates_c');
$smarty->setCacheDir('smarty/cache');
$smarty->setConfigDir('smarty/configs');
$smarty->error_reporting = 4;

$smarty->display('matchLineup.tpl');



?>
