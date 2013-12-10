<?php
include "dbConnection.php";

class DatabaseNationalTeam {
    
    public function getTeamInfo($teamid,$season)
    {
        $events = array (
            'players' => self::getPlayers($teamid,$season)
        );
        return $events;
    }
    
    private function getPlayers($teamid,$season){
    {
        $q = "SELECT p.`playerid`,
        SUM(IF(e.`eventtype` = \"4\", 1,0)) AS `goals scored`, 
        SUM(IF(e.eventtype = \"8\", 1,0)) AS `penalty`,
        SUM(IF(e.eventtype = \"9\", 1,0)) AS `own goals`,
        SUM(IF(e.eventtype = \"2\", 1,0)) AS `yellow cards`, 
        (SUM(IF(e.eventtype = \"3\", 1,0)) + SUM(IF(e.eventtype = \"1\", 1,0))) AS `red cards`,
        SUM(IF(e.eventtype = \"7\", 1,0)) AS `subbed in` ,
        SUM(IF(e.eventtype = \"6\", 1,0)) AS `subbed off` 
        FROM playtable_national p 
        LEFT JOIN eventtable_national e ON e.matchid = p.matchid AND e.playerid = p.playerid AND e.teamid = p.teamid AND e.ignore = 0
        JOIN matchtable_national m ON e.matchid = m.matchid
        JOIN leaguetable l ON m.leagueid = l.leagueid
        WHERE p.`teamid` = {$teamid}
        and l.year = {$season}
        AND p.ignore = 0 
        GROUP BY p.`playerid`";
        
        $q2 = "SELECT pt.shirtnumber, p.`playerid`,pt.`playername`, SUM(p.minutesplayed) AS `minutes played`, SUM(p.start) AS `started`
        FROM playtable_national p 
        JOIN playertable_national pt ON p.`playerid` = pt.`playerid` AND p.`teamid` = pt.`teamid` AND pt.year = {$season}
        JOIN matchtable_national m ON p.`matchid` = m.`matchid`
        JOIN leaguetable l ON m.`leagueid` = l.`leagueid`
        WHERE pt.`teamid` = {$teamid}
        AND l.`year` = {$season}
        AND p.ignore = 0
        GROUP BY p.`playerid`
        order by pt.shirtnumber asc";
        
        $data = array();
        $result = mysql_query($q2);
        while($row = mysql_fetch_array($result))
        {
            $data[$row['playerid']] = array(
                'shirtnumber' => $row['shirtnumber'],
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'minutesplayed' => $row['minutes played'],
                'started' => $row['started']
            );
        }
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            if(isset($data[$row['playerid']])){
                $data[$row['playerid']]['goals'] = $row['goals scored'];
                $data[$row['playerid']]['penalty'] = $row['penalty'];
                $data[$row['playerid']]['owngoals'] = $row['own goals'];
                $data[$row['playerid']]['yellowcards'] = $row['yellow cards'];
                $data[$row['playerid']]['redcards'] = $row['red cards'];
                $data[$row['playerid']]['subbedin'] = $row['subbed in'];
                $data[$row['playerid']]['subbedoff'] = $row['subbed off'];
            }
        }
        $json = array();
        foreach($data as $value){
            $json[] = $value;
        }
        return $json;
    }
    }
}