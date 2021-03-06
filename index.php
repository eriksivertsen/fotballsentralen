<?php

// put full path to Smarty.class.php
require('smarty/libs/Smarty.class.php');
require_once('db/DatabaseUtils.class.php');
require_once('db/DatabaseScope.class.php');

$dbUtils = new DatabaseUtils();
$dbScope = new DatabaseScope();

$smarty = new Smarty();

$smarty->setTemplateDir('smarty/templates');
$smarty->setCompileDir('smarty/templates_c');
$smarty->setCacheDir('smarty/cache');
$smarty->setConfigDir('smarty/configs');
$smarty->error_reporting = 4;

set_time_limit(60);

if(isset($_GET['player_id'])){
    $smarty->assign('player_id',$_GET['player_id']);
}
if(isset($_GET['team_id'])){
    $smarty->assign('team_id',$_GET['team_id']);
}
if(isset($_GET['league_id'])){
    $smarty->assign('league_id',$_GET['league_id']);
}
if(isset($_GET['page'])){
    $smarty->assign('page', $_GET['page']);
}
if(isset($_GET['matchid'])){
    $smarty->assign('matchid', $_GET['matchid']);
}
if(isset($_GET['referee_id'])){
    $smarty->assign('refereeid', $_GET['referee_id']);
}
if(isset($_GET['season'])){
    $smarty->assign('season',$_GET['season']);
}else{
    $smarty->assign('season',Constant::CURRENT_YEAR);
}
$searcharray = $dbUtils->getSearchArray();
$smarty->assign('searcharray', json_encode($searcharray['searcharray']));
$smarty->assign('status', $dbUtils->getStatus());
$smarty->assign('scopes', $dbScope->getLiveScopes());
$smarty->assign('contents',$smarty->fetch('infoindex.tpl'));
$smarty->display('main.tpl');

?>