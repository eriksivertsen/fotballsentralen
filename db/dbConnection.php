<?php
error_reporting(E_ALL);

// Replace the variable values below
// with your specific database information.
//$host = "customsolutions.mysql.domeneshop.no:3306/customsolutions";
//$user = "customsolutions";
//$pass = "E6VF5ykZ";
//$db   = "customsolutions";

$host = "213.162.240.26:3306"; //cpanel20.proisp.no
$user = "fotbalqt_java";
$pass = "Eriketard1";
$db   = "fotbalqt_main";
//
//$host = "localhost";
//$user = "root";
//$pass = "";

// This part sets up the connection to the 
// database (so you don't need to reopen the connection
// again on the same page).

$ms = mysql_connect($host, $user, $pass);

if ( !$ms ) {
    echo "Error connecting to database.\n";
    die;
}
mysql_query('SET CHARACTER SET utf8');

// Then you need to make sure the database you want
// is selected.
mysql_select_db($db);
include_once 'Constant.class.php';
?>
