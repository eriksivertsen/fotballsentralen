<?php
session_start();
header('Cache-control: private');

require('smarty/libs/Smarty.class.php');
require('db/DatabaseAdmin.class.php');
require('db/MatchObserver.class.php');


$smarty = new Smarty();
$dbAdmin = new DatabaseAdmin();

$smarty->setTemplateDir('smarty/templates');
$smarty->setCompileDir('smarty/templates_c');
$smarty->setCacheDir('smarty/cache');
$smarty->setConfigDir('smarty/configs');
$smarty->error_reporting = 4;


$username = filter_input(INPUT_POST, 'username');
$password = filter_input(INPUT_POST, 'password');

if($username != '' && $password != ''){
    $userid = $dbAdmin->login($username, $password);
    if($userid != -1){
        $_SESSION['loggedIn'] = true;
        $_SESSION['userid'] = $userid;
    }
}

if(isset($_SESSION['loggedIn']) && $_SESSION['loggedIn'] == true){
    echo '<input id=userid type=hidden value='.$_SESSION['userid'].'>';
    $smarty->display('matchobserver/index.tpl');
}else{
    $smarty->display('login.tpl');
}



?>
