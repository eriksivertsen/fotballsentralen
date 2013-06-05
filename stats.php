<?php

require_once('db/Database.class.php');

$db = new Database();

$clicktable = $db->getClickTable();


echo '<table border="1">';
foreach($clicktable as $value){
    echo '<tr>';
    foreach($value as $a){
        echo '<td>'.$a.'</td>';
    }
    echo '</tr>';
}
echo '</table>';


