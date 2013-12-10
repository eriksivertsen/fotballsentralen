<?php

include "dbConnection.php";

class DatabaseAdmin {
    
    public function login($username,$password){
        $q = "SELECT * FROM matchobserve_user WHERE mail = '" . $username . "' AND password = '" . md5($password) . "'";
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            DatabaseAdmin::updateLoginOk($row['userid']);
            return $row['userid'];
        }
        return -1;
    }
    
    private function updateLoginOk($userid){
        $q = "UPDATE matchobserve_user SET lastlogin = NOW() WHERE userid = ".$userid;
        mysql_query($q);
    }
    
    public function getUsersLeagues($userid){
        
        $q= "SELECT ul.* FROM matchobserve_user u 
            LEFT JOIN `matchobserve_user_league` ul ON ul.`userid` = u.`userid`
            WHERE u.userid = " . $userid;
        
        $result = mysql_query($q);
        $data = array();
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'leagueid' => $row['leagueid'],
                'active' => $row['active'],
                'sendsquad_single' => $row['sendsquad_single'],
                'sendsquad_double' => $row['sendsquad_double'],
                'sendlineup_single' => $row['sendlineup_single'],
                'sendlineup_double' => $row['sendlineup_double']
            );
        }
        return $data;
    }
    public function saveSettings($userid, $jsonStringSettings){
        $array = json_decode($jsonStringSettings, true);
        foreach($array as $value){
            $leagueid = $value['leagueid'];
            $active = $value['active_'];
            $sendsquad_double = (isset($value['squad_double']) ? $value['squad_double'] : 0);
            $sendsquad_single = (isset($value['squad_single']) ? $value['squad_single'] : 0);
            $sendlineup_double = (isset($value['lineup_double']) ? $value['lineup_double'] : 0);
            $sendlineup_single = (isset($value['lineup_single']) ? $value['lineup_single'] : 0);
            
            if($active == 1 && $sendsquad_double == 0 && $sendsquad_single == 0 && $sendlineup_double == 0 & $sendlineup_single == 0){
                if($leagueid == 100 || $leagueid == 200 || $leagueid = 300){
                    $sendlineup_single = 1;
                }else{ 
                    $sendlineup_double = 1;
                    $sendsquad_double = 1;
                }
            }
            
            $q= "INSERT INTO matchobserve_user_league (userid,leagueid,active,sendsquad_single,sendsquad_double,sendlineup_single,sendlineup_double,lastupdate) VALUES ($userid,$leagueid,$active,$sendsquad_single,$sendsquad_double,$sendlineup_single,$sendlineup_double,NOW()) 
                ON DUPLICATE KEY UPDATE active=VALUES(active),sendsquad_single=VALUES(sendsquad_single),sendsquad_double=VALUES(sendsquad_double),sendlineup_single=VALUES(sendlineup_single),sendlineup_double=VALUES(sendsquad_double), lastupdate=NOW()";
            mysql_query($q);
        }
    }
    
    function changePassword($userid, $newPassword){
        $q = "UPDATE matchobserve_user SET password = '" .md5($newPassword). "' WHERE userid = ".$userid;
        mysql_query($q);
    }
}


?>
