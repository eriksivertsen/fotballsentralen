<?php
session_start();
header('Cache-control: private');

require('smarty/libs/Smarty.class.php');
require('db/DatabaseSettings.class.php');
require('db/MatchObserver.class.php');


$smarty = new Smarty();

$smarty->setTemplateDir('smarty/templates');
$smarty->setCompileDir('smarty/templates_c');
$smarty->setCacheDir('smarty/cache');
$smarty->setConfigDir('smarty/configs');
$smarty->error_reporting = 4;


if(isset($_SESSION['loggedIn'])){
    echo '<input id=userid type=hidden value='.$_SESSION['userid'].'>';
    $smarty->display('matchobserver/settings.tpl');
}

