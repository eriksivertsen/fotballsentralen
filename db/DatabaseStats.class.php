<?php
include "dbConnection.php";

class DatabaseStats {
    
    public function getClickTable()
    {
        $q = "SELECT trending.clicked_id,
            trending.clicktype,
            trending.ip,
            trending.time,
            p.`playername` AS name1,
            p1.`playername` AS name2,
            t.`teamname`,
            m.`matchid`,
            home.`teamname` AS hometeam,
            away.`teamname` AS awayteam,
            l.leaguename
            FROM clicktable trending 
            LEFT JOIN playertable p ON p.`playerid` = trending.clicked_id AND p.year = 2012
            LEFT JOIN playertable p1 ON p1.`playerid` = trending.clicked_id AND p1.year = 2013
            LEFT JOIN teamtable t ON t.`teamid` = trending.clicked_id
            LEFT JOIN matchtable m ON m.`matchid` = trending.clicked_id
            LEFT JOIN teamtable home ON m.`hometeamid` = home.`teamid`
            LEFT JOIN teamtable away ON m.`awayteamid` = away.`teamid`
            LEFT JOIN leaguetable l on l.java_variable = trending.clicked_id
        ORDER by trending.time DESC LIMIT 100";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            if($row['clicktype'] == 'player' || $row['clicktype'] == 'player_search'){
                if(isset($row['name1']) && !empty($row['name1'])){
                    $playername = $row['name1'];
                }else{
                    $playername = $row['name2'];
                }
                
                $data[] = array(
                    'type' => $row['clicktype'],
                    'playerid' => $row['clicked_id'],
                    'playername' => $playername,
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }else if($row['clicktype']=='team' || $row['clicktype']=='team_search'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'teamid' => $row['clicked_id'],
                    'teamname' => $row['teamname'],
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }else if($row['clicktype']=='preview'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'matchid' => $row['clicked_id'],
                    'hometeam' => $row['hometeam'] . ' - ' . $row['awayteam'],
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }else if($row['clicktype']=='league'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'matchid' => $row['clicked_id'],
                    'name' => $row['leaguename'],
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }else{
                $data[] = array(
                    'type' => $row['clicktype'],
                    'clicked_id' => $row['clicked_id'],
                    'name' => ' ',
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }
        }
        return $data;
    }
    public function getDailyClicks()
    {
         $q = "SELECT COUNT(*) AS clicks, DATE(TIME) AS dato FROM clicktable GROUP BY DATE(TIME) ORDER BY DATE(TIME) DESC;";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'clicks' => $row['clicks'],
                'dato' => $row['dato']
            );
        }
        return $data;
    }
    public function getClicksUnique()
    {
        $q = "SELECT COUNT(*) AS clicks, DATE(TIME) AS dato FROM clicktable GROUP BY DATE(TIME) ORDER BY DATE(TIME) DESC;";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'clicks' => $row['clicks'],
                'dato' => $row['dato']
            );
        }
        return $data;
    }
    
    public function getUniqueVisitors()
    {
        $q= "SELECT 
            COUNT(*) AS unique_visitors,
            DATE(c.time) AS dag
            FROM
            (SELECT 
            ip,
            TIME 
            FROM
            clicktable c 
            GROUP BY ip,
            DATE(TIME)) AS c 
            GROUP BY DATE(c.time) 
            ORDER BY DATE(c.time) DESC;";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'unique_visitors' => $row['unique_visitors'],
                'date' => $row['dag']
            );
        }
        return $data;
    }
    
    public function printTable(array $data, $tableheader){
        
        echo '<table border="1" style="float:left;margin:26px;">';
        echo '<caption><b>'.$tableheader.'</b></caption>';
        foreach($data as $value){
            echo '<tr>';
            foreach($value as $a){
                echo '<td>'.$a.'</td>';
            }
            echo '</tr>';
        }
        echo '</table>';
    }
    
    public function getLatestObserverDate()
    {
        $q= "SELECT MAX(lastupdate) as max FROM match_observe";
        
        $data = '';
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data = $row['max'];
        }
        return $data;
    }
}