<?php

include "dbConnection.php";

class DatabaseSettings {
    
    public function getPlayers($userid) {
        $q = "SELECT 
            p.*, t.teamname, t.teamid, l.java_variable, pk.key as real_key
            FROM
            matchtable m 
            JOIN leaguetable l 
            ON l.`leagueid` = m.`leagueid` 
            JOIN playertable p ON p.`teamid` = m.`hometeamid` AND p.`year` = l.year
            JOIN teamtable t on t.teamid = p.teamid
            left join player_key pk on pk.playerid = p.playerid and pk.userid = $userid 
            WHERE l.year = " . Constant::CURRENT_YEAR . " " .
                "AND l.`java_variable` IN (1, 2) 
            GROUP BY p.`playerid`
            ORDER BY  pk.key DESC, p.playername";

        $data = array();
        
        $result = mysql_query($q);

        while ($row = mysql_fetch_array($result)) {
            $key = $row['real_key'];
            if(!isset($key)){
                $key = 1;
            }
            $data[$row['java_variable']][$row['teamid']][] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'teamname' => $row['teamname'],
                'teamid' => $row['teamid'],
                'league' => $row['java_variable'],
                'key' => $key
            );
        }
        return $data;
    }
    
    public function getDerbyTable() {
        $q = "SELECT id,team1.teamid as teamid1, team1.teamname as teamname1, team2.teamid as teamid2, team2.teamname as teamname2, derby.level FROM derby JOIN teamtable team1 on team1.teamid = derby.teamid JOIN teamtable team2 on team2.teamid = derby.teamid2 ";
        $data = array();

        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[] = array(
                'id' => $row['id'],
                'teamid1' => $row['teamid1'],
                'teamname1' => $row['teamname1'],
                'teamid2' => $row['teamid2'],
                'teamname2' => $row['teamname2'],
                'level' => $row['level']
            );
        }
        return $data;
    }

    public function getSettings() {
        $q = "SELECT `key`,value, `desc` from settings where value is not null";
        $data = array();

        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[$row['key']] = array(
                'value' => $row['value'],
                'desc' => $row['desc'],
                'key' => $row['key']
            );
        }
        return $data;
    }

    public function getSurface() {
        $q = "SELECT 
            t.*, l.java_variable
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON l.`leagueid` = m.`leagueid` 
            JOIN teamtable t 
                ON t.`teamid` = m.`hometeamid` 
            WHERE l.year = 2014 
            AND l.`java_variable` IN (1, 2) 
            GROUP BY m.`hometeamid`";
        $data = array();

        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[$row['java_variable']][] = array(
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname'],
                'surface' => $row['surface'],
                'surface_condition' => $row['surface_condition']
            );
        }
        return $data;
    }
}


$action = filter_input(INPUT_POST, 'action');

if($action == 'getSettings') {
    $userid = filter_input(INPUT_POST, 'userid', FILTER_VALIDATE_INT);
    $retval = array(
        'derby' => DatabaseSettings::getDerbyTable(),
        'surface' => DatabaseSettings::getSurface(),
        'players' => DatabaseSettings::getPlayers($userid),
        'settings' => DatabaseSettings::getSettings()
    );
    echo json_encode($retval);
}

if ($action == 'setSurface') {
    $teamid = filter_input(INPUT_POST, 'teamid', FILTER_VALIDATE_INT);
    $condition = filter_input(INPUT_POST, 'surface_condition', FILTER_SANITIZE_STRING);
    $q = "UPDATE teamtable SET surface_condition='$condition' WHERE teamid = $teamid";
    mysql_query($q);
    return;
}
if ($action == 'setKey') {
    $playerid = filter_input(INPUT_POST, 'playerid', FILTER_VALIDATE_INT);
    $key = filter_input(INPUT_POST, 'key', FILTER_DEFAULT);
    $userid = filter_input(INPUT_POST, 'userid', FILTER_DEFAULT);
    $q = "INSERT INTO player_key (playerid,userid,`key`,`lastupdate`) VALUES ($playerid,$userid,'$key',NOW()) ON DUPLICATE KEY UPDATE `key`=VALUES(`key`), lastupdate=VALUES(lastupdate)";
    mysql_query($q);
    echo 'true';
    return;
}

if ($action == 'updateSettings') {
    $value = filter_input(INPUT_POST, 'value', FILTER_DEFAULT);
    $key = filter_input(INPUT_POST, 'key', FILTER_DEFAULT);
    $q = "UPDATE settings SET `value`= '$value' WHERE `key` = '$key'";
    mysql_query($q);
    echo 'true';
    return;
}

if ($action == 'saveDerbyLevel') {
    $derbyid = filter_input(INPUT_POST, 'derbyid', FILTER_VALIDATE_INT);
    $condition = filter_input(INPUT_POST, 'level', FILTER_SANITIZE_STRING);
    $q = "UPDATE derby SET level='$condition' WHERE id = $derbyid";
    mysql_query($q);
    return;
}
if ($action == 'deleteDerby') {
    $derbyid = filter_input(INPUT_POST, 'derbyid', FILTER_VALIDATE_INT);
    $q = "DELETE FROM derby WHERE id = $derbyid";
    mysql_query($q);
    echo 'true';
    return;
}


if ($action == 'saveDerby') {
    $team1 = filter_input(INPUT_POST, 'team1', FILTER_VALIDATE_INT);
    $team2 = filter_input(INPUT_POST, 'team2', FILTER_VALIDATE_INT);
    $level = filter_input(INPUT_POST, 'level', FILTER_SANITIZE_STRING);
    $q = "INSERT INTO derby (teamid,teamid2,level) VALUES ($team1,$team2,'$level')";
    mysql_query($q);
    echo mysql_insert_id();
    return;
}

?>


