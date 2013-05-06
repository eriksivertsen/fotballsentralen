<?php

include "dbConnection.php";

define('EVENTTABLE','eventtable');
define('PLAYTABLE','playtable');

class Database {
    
    private $whitelist = array('localhost', '127.0.0.1', '46.9.149.168');
    
    public function getTeam($teamid){
        $q = "SELECT * FROM teamtable where teamid = $teamid";
        return mysql_query($q);
    }
    public function getLeagues()
    {
        $q = "SELECT * FROM leaguetable ORDER by leaguename ASC";
        $data = array();
        $result = mysql_query($q);
        $data[] = 'Alle ligaer';
        while($row = mysql_fetch_array($result))
        {
            $data[$row['leagueid']] = $row['leaguename'];
        }
        return $data;
    }
    public function getTeamToLeague($teamid,$season)
    {
        $q = "SELECT t.teamid,t.teamname, l.java_variable FROM teamtable t 
            JOIN matchtable m ON m.hometeamid = t.teamid
            JOIN leaguetable l ON l.leagueid = m.leagueid
            WHERE t.teamid = {$teamid}
            AND l.year = {$season}
            GROUP BY m.hometeamid
            ORDER BY t.teamname ASC";
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname'],
                'leagueid' => $row['java_variable']
            );
        }
        return $data;
    }
    public function getPlayerToLeague($playerid,$season)
    {
        $q = "SELECT t.playerid,t.playername,t.teamid,t1.teamname, l.java_variable FROM playertable t 
            JOIN matchtable m ON m.hometeamid = t.teamid
            JOIN leaguetable l ON l.leagueid = m.leagueid AND l.year = t.year
            JOIN teamtable t1 ON t1.teamid = t.teamid
            WHERE t.playerid = {$playerid}
            AND t.year = {$season} 
            GROUP BY m.hometeamid";
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname'],
                'leagueid' => $row['java_variable']
            );
        }
        return $data;
    }
    public function getTeamsJSON($leagueId, $season)
    {
        $q = "SELECT t.*,l.lastupdate FROM teamtable t 
            JOIN matchtable m ON m.hometeamid = t.teamid
            JOIN leaguetable l ON l.leagueid = m.leagueid
            WHERE l.java_variable = {$leagueId} and l.year = {$season}
            GROUP BY m.`hometeamid`
            ORDER BY t.teamname ASC";
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname']
            );
        }
        return $data;
    }
    public function getEventInfoJSON($teamid,$leagueid,$eventtype,$season)
    {          
        $q = 
        "SELECT t.playerid,t.playername,tt.teamid,tt.teamname,COUNT(*) AS `event count`, eventtype FROM ".EVENTTABLE." e " .
        "JOIN playertable t ON t.playerid = e.playerid AND e.teamid = t.teamid AND t.year = " . $season . " " .
        "JOIN teamtable tt ON tt.teamid = t.teamid " .
        "JOIN matchtable m ON e.matchid = m.matchid  ".
        "JOIN leaguetable l ON l.leagueid = m.leagueid ".    
        "WHERE e.eventtype IN ( ".$eventtype . " )" .
        "AND e.playerid != -1 ".
        ($teamid == 0 ? ' ' : ' AND e.teamid = '.$teamid.' ') .
        ($leagueid == 0 ? ' ' : ' AND l.java_variable IN ('.$leagueid.') ') .
        'AND l.year = '.$season.' ' .
        "AND e.ignore = 0 " .         
        "GROUP BY e.playerid, m.leagueid " .
        "ORDER BY `event count` DESC ".
        ($teamid == 0 ? ' LIMIT 10 ' : ' ');
        
        //echo $q;
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $playername = $row['playername'];
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $playername,
                'eventcount'=> $row['event count'],
                'teamname' => $row['teamname'],
                'teamid' => $row['teamid']
            );
        }
        return $data;
    }
    public function getEventInfoTotalJSON($eventtype, $limit, $season)
    {
        $q = 
        "SELECT t.playerid,t.playername,tt.teamid,tt.teamname,COUNT(*) AS `event count`, eventtype FROM ".EVENTTABLE." e " .
        "JOIN playertable t ON t.playerid = e.playerid AND e.teamid = t.teamid AND t.year = " . $season . " " .
        "JOIN teamtable tt ON tt.teamid = t.teamid " .
        "JOIN matchtable m ON e.matchid = m.matchid  ".
        "JOIN leaguetable l ON l.leagueid = e.leagueid " .         
        "WHERE e.eventtype = ".$eventtype . " " .
        "AND e.playerid != -1 ".
        "AND l.year = "  . $season .  " "  .   
        "AND e.ignore = 0 " .    
        "GROUP BY e.playerid " .       
        "ORDER BY `event count` DESC ".
        "LIMIT $limit";
        
        //echo $q;
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'eventcount'=> $row['event count'],
                'teamname' => $row['teamname'],
                'teamid' => $row['teamid']
            );
        }
        return $data;
    }
    public function getPopulare()
    {
        $q = "SELECT p.`playerid`,p.`playername`,p.`webpagehits` 
            FROM playertable p GROUP BY p.`playerid` HAVING p.`webpagehits` > 0 ORDER BY webpagehits DESC LIMIT 10";
        
        $q2 = "SELECT p.`teamid`,p.`teamname`,p.`webpagehits` 
            FROM teamtable p GROUP BY p.`teamid` HAVING p.`webpagehits` > 0 ORDER BY webpagehits DESC LIMIT 10;";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid'=> $row['playerid'],
                'playername' => $row['playername'],
                'playerhits' => $row['webpagehits']
            );
        }
        $result = mysql_query($q2);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'teamid'=> $row['teamid'],
                'teamname' => $row['teamname'],
                'teamhits' => $row['webpagehits']
            );
        }
        return $data;
    }
    
    public function getTotalPlayerminutes($season,$limit)
    {
        $q = "SELECT pp.playerid,pp.playername,t.teamname,t.teamid,SUM(p.minutesplayed) AS `minutes played` FROM ".PLAYTABLE." p " . 
        "JOIN matchtable m ON m.matchid = p.matchid " .
        "JOIN playertable pp ON pp.playerid = p.playerid AND p.teamid = pp.teamid AND pp.year = ". $season . " " . 
        "JOIN teamtable t ON t.teamid = pp.teamid ".
        "JOIN leaguetable l ON m.leagueid = l.leagueid " .
        "WHERE pp.playerid != -1 " .
        "AND l.year = pp.year AND p.ignore = 0 " .
        "GROUP BY p.playerid ".
        "ORDER BY SUM(p.minutesplayed) DESC " .
        "LIMIT " . $limit;

        //echo $q;
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'minutesplayed'=> $row['minutes played'],
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname']
            );
        }
        return $data;
    }
    public function getEventInfoTotalTeam($eventtype, $limit, $season)
    {
        $q = 
        "SELECT tt.teamid,tt.teamname,COUNT(*) AS `event count`, eventtype FROM ".EVENTTABLE." e " .
        "JOIN playertable t ON t.playerid = e.playerid AND e.teamid = t.teamid AND t.year = " .$season . " " . 
        "JOIN teamtable tt ON tt.teamid = t.teamid " .
        "JOIN matchtable m ON e.matchid = m.matchid  ".
        "JOIN leaguetable  l ON l.leagueid = m.leagueid " .        
        "WHERE e.eventtype = ".$eventtype . " " .
        "AND e.playerid != -1 ".
        "AND l.year = "  . $season .  " "  .  
        "AND e.ignore = 0 " .    
        "GROUP BY e.teamid " .
        "ORDER BY `event count` DESC ".
        "LIMIT $limit";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'eventcount'=> $row['event count'],
                'teamname' => $row['teamname'],
                'teamid' => $row['teamid']
            );
        }
        return $data;
    }
    public function getPlayingMinutesJSON($teamid,$leagueid,$season)
    {
        $q = "SELECT pp.playerid,pp.playername,t.teamname,t.teamid,SUM(p.minutesplayed) AS `minutes played` FROM ".PLAYTABLE." p " . 
        "JOIN matchtable m ON m.matchid = p.matchid " .
        "JOIN playertable pp ON pp.playerid = p.playerid AND p.teamid = pp.teamid AND pp.year = ". $season . " " . 
        "JOIN teamtable t ON t.teamid = pp.teamid ".
        "JOIN leaguetable l ON m.leagueid = l.leagueid " .
        "WHERE pp.playerid != -1 " .
        ($teamid == 0 ? '' : ' AND t.teamid = '.$teamid.' ') .
        ($leagueid == 0 ? '' : ' AND l.java_variable IN ('.$leagueid.') ') .
        "AND l.year = " . $season . "  AND p.ignore = 0 " .
        "GROUP BY p.playerid ".
        "ORDER BY SUM(p.minutesplayed) DESC " .
        ($teamid == 0 ? ' LIMIT 10  ' : ' ') ;

        //echo $q;
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => substr($row['playername'],0,23),
                'minutesplayed'=> $row['minutes played'],
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname']
            );
        }
        return $data;
    }
    
    public function getPlayerInfoJSON($playerid,$season)
    {
        $q = "SELECT p.`playerid`,p.`matchid`,p.teamid,
        SUM(IF(e.`eventtype` = \"4\", 1,0)) AS `goals scored`, 
        SUM(IF(e.eventtype = \"8\", 1,0)) AS `penalty`,
        SUM(IF(e.eventtype = \"9\", 1,0)) AS `own goals`,
        SUM(IF(e.eventtype = \"2\", 1,0)) AS `yellow cards`, 
        (SUM(IF(e.eventtype = \"3\", 1,0)) + SUM(IF(e.eventtype = \"1\", 1,0))) AS `red cards`,
        SUM(IF(e.eventtype = \"6\", 1,0)) AS `subbed in` ,
        SUM(IF(e.eventtype = \"7\", 1,0)) AS `subbed off` 
        FROM ".PLAYTABLE." p 
        LEFT JOIN ".EVENTTABLE." e ON e.matchid = p.matchid AND e.playerid = p.playerid AND e.teamid = p.teamid AND e.ignore = 0
        JOIN matchtable m ON p.`matchid` = m.`matchid`
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
        WHERE p.`playerid` = {$playerid}
        AND l.year = {$season}
        AND p.ignore = 0 
        GROUP BY p.`playerid`,p.`matchid`";
        
        $q2 = "SELECT p.`playerid`,pt.`playername` as playername, p.minutesplayed AS `minutes played`, p.start AS `start`,
        home.teamid as homeid, away.teamid as awayid,
        home.teamname as `homename`,away.teamname as `awayname`, m.result,m.`teamwonid`, SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS dateofmatch, m.matchid
        FROM ".PLAYTABLE." p 
        JOIN playertable pt ON p.`playerid` = pt.`playerid` AND p.`teamid` = pt.`teamid` AND pt.year = {$season}
        JOIN matchtable m ON p.matchid = m.matchid
        JOIN teamtable home ON m.hometeamid = home.teamid
        JOIN teamtable away ON m.awayteamid = away.teamid
        JOIN leaguetable l ON m.`leagueid` = l.`leagueid`
        WHERE pt.`playerid` = {$playerid}
        AND p.ignore = 0 
        AND l.year = {$season}
        GROUP BY pt.`teamid`,p.`playerid`,p.`matchid`";
        
       // echo $q;
        //echo $q2;
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[$row['matchid']] = array(
                'goals' => $row['goals scored'],
                'penalty' => $row['penalty'],
                'yellowcards' => $row['yellow cards'],
                'redcards' => $row['red cards'],
                'subbedin' => $row['subbed in'],
                'owngoal' => $row['own goals'],
                'subbedoff' => $row['subbed off'],
                'teamid' => $row['teamid']
            );
        }
        $result = mysql_query($q2);
        while($row = mysql_fetch_array($result))
        {
                $data[$row['matchid']]['minutesplayed']= $row['minutes played'];
                $data[$row['matchid']]['start'] = $row['start'];
                $data[$row['matchid']]['hometeamname'] = $row['homename'];
                $data[$row['matchid']]['awayteamname'] = $row['awayname'];
                $data[$row['matchid']]['homeid'] = $row['homeid'];
                $data[$row['matchid']]['awayid'] = $row['awayid'];
                $data[$row['matchid']]['result'] = $row['result'];
                $data[$row['matchid']]['dateofmatch'] = $row['dateofmatch'];
                $data[$row['matchid']]['matchid'] = $row['matchid'];
                $data[$row['matchid']]['playername'] = $row['playername'];
        }
        $json = array();
        foreach($data as $value){
            $json[] = $value;
        }
        if(empty($json)){
            //return self::getPlayerInfoAlt($playerid);
        }
        return $json;
    }
    public function getPlayerInfoAlt($playerid)
    {
        $q = "SELECT pta.`playerid`, pta.shirtnumber,pta.`playername`, home.teamid as homeid, away.teamid as awayid,
        home.teamname as `homename`,away.teamname as `awayname`, m.result,m.`teamwonid`, SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS dateofmatch, m.matchid,
        SUM(IF(e.`eventtype` = 4, 1,0)) AS `goals scored`, 
        SUM(IF(e.eventtype = 8, 1,0)) AS `penalty`,
        SUM(IF(e.eventtype = 9, 1,0)) AS `own goals`,
        SUM(IF(e.eventtype = 2, 1,0)) AS `yellow cards`, 
        SUM(IF(e.eventtype = 3, 1,0)) AS `red cards` ,
        SUM(IF(e.eventtype = 7, 1,0)) AS `subbed in` ,
        SUM(IF(e.eventtype = 6, 1,0)) AS `subbed off` 
        FROM playertable pta
        LEFT JOIN ".EVENTTABLE." e ON e.playerid = pta.playerid AND e.teamid = pta.teamid AND e.ignore = 0
        JOIN matchtable m ON e.matchid = m.matchid
        JOIN teamtable home ON m.hometeamid = home.teamid
        JOIN teamtable away ON m.awayteamid = away.teamid
        WHERE pta.`playerid` = {$playerid}
        GROUP BY m.matchid";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[$row['matchid']] = array(
                'goals' => $row['goals scored'],
                'penalty' => $row['penalty'],
                'yellowcards' => $row['yellow cards'],
                'redcards' => $row['red cards'],
                'subbedin' => $row['subbed in'],
                'owngoal' => $row['own goals'],
                'subbedoff' => $row['subbed off'],
                'hometeamname' => $row['homename'],
                'awayteamname' => $row['awayname'],
                'homeid' => $row['homeid'],
                'awayid' => $row['awayid'],
                'result' => $row['result'],
                'dateofmatch' => $row['dateofmatch'],
                'matchid' => $row['matchid']
            );
        }
        $json = array();
        foreach($data as $value){
            $json[] = $value;
        }
        return $json;
    }
    public function getTeamPlayerJSON($teamid,$season)
    {
        $q = "SELECT p.`playerid`,
        SUM(IF(e.`eventtype` = \"4\", 1,0)) AS `goals scored`, 
        SUM(IF(e.eventtype = \"8\", 1,0)) AS `penalty`,
        SUM(IF(e.eventtype = \"9\", 1,0)) AS `own goals`,
        SUM(IF(e.eventtype = \"2\", 1,0)) AS `yellow cards`, 
        (SUM(IF(e.eventtype = \"3\", 1,0)) + SUM(IF(e.eventtype = \"1\", 1,0))) AS `red cards`,
        SUM(IF(e.eventtype = \"7\", 1,0)) AS `subbed in` ,
        SUM(IF(e.eventtype = \"6\", 1,0)) AS `subbed off` 
        FROM ".PLAYTABLE." p 
        LEFT JOIN ".EVENTTABLE." e ON e.matchid = p.matchid AND e.playerid = p.playerid AND e.teamid = p.teamid AND e.ignore = 0
        JOIN matchtable m ON e.matchid = m.matchid
        JOIN leaguetable l ON m.leagueid = l.leagueid
        WHERE p.`teamid` = {$teamid}
        and l.year = {$season}
        AND p.ignore = 0 
        GROUP BY p.`playerid`";
        
        $q2 = "SELECT pt.shirtnumber, p.`playerid`,pt.`playername`, SUM(p.minutesplayed) AS `minutes played`, SUM(p.start) AS `started`
        FROM ".PLAYTABLE." p 
        JOIN playertable pt ON p.`playerid` = pt.`playerid` AND p.`teamid` = pt.`teamid` AND pt.year = {$season}
        JOIN matchtable m ON p.`matchid` = m.`matchid`
        JOIN leaguetable l ON m.`leagueid` = l.`leagueid`
        WHERE pt.`teamid` = {$teamid}
        AND l.`year` = {$season}
        AND p.ignore = 0
        GROUP BY p.`playerid` ";
        
        //echo $q;

      //  echo $q2;
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[$row['playerid']] = array(
                'goals' => $row['goals scored'],
                'penalty' => $row['penalty'],
                'owngoals' => $row['own goals'],
                'yellowcards' => $row['yellow cards'],
                'redcards' => $row['red cards'],
                'subbedin' => $row['subbed in'],
                'subbedoff' => $row['subbed off']
            );
        }
        $result = mysql_query($q2);
        while($row = mysql_fetch_array($result))
        {
            $data[$row['playerid']]['shirtnumber'] = $row['shirtnumber'];
            $data[$row['playerid']]['playerid'] = $row['playerid'];
            $data[$row['playerid']]['playername'] = $row['playername'];
            $data[$row['playerid']]['minutesplayed'] = $row['minutes played'];
            $data[$row['playerid']]['started'] = $row['started'];
        }
        $json = array();
        foreach($data as $value){
            $json[] = $value;
        }
        if(empty($json)){
            return self::getTeamPlayerAltJSON($teamid,$season);
        }
        return $json;
    }
    public function getTeamPlayerAltJSON($teamid,$season)
    {
        $q = "SELECT pta.`playerid`, pta.shirtnumber,pta.`playername`, 
        SUM(IF(e.`eventtype` = 4, 1,0)) AS `goals scored`, 
        SUM(IF(e.eventtype = 8, 1,0)) AS `penalty`,
        SUM(IF(e.eventtype = 9, 1,0)) AS `own goals`,
        SUM(IF(e.eventtype = 2, 1,0)) AS `yellow cards`, 
        SUM(IF(e.eventtype = 3, 1,0)) AS `red cards` ,
        SUM(IF(e.eventtype = 7, 1,0)) AS `subbed in` ,
        SUM(IF(e.eventtype = 6, 1,0)) AS `subbed off` 
        FROM playertable pta
        LEFT JOIN ".EVENTTABLE." e ON e.playerid = pta.playerid AND e.teamid = pta.teamid AND e.ignore = 0
        JOIN leaguetable l ON e.leagueid = l.leagueid
        WHERE pta.`teamid` = {$teamid}
        AND l.year = {$season}
        GROUP BY pta.playerid, pta.teamid";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[$row['playerid']] = array(
                'shirtnumber' => $row['shirtnumber'],
                'playername' => $row['playername'],
                'goals' => $row['goals scored'],
                'penalty' => $row['penalty'],
                'owngoals' => $row['own goals'],
                'yellowcards' => $row['yellow cards'],
                'redcards' => $row['red cards'],
                'subbedin' => $row['subbed in'],
                'subbedoff' => $row['subbed off']
            );
        }
        $json = array();
        foreach($data as $value){
            $json[] = $value;
        }
        return $json;
    }
    public function setTeamHit($id)
    {
        //echo $_SERVER['HTTP_HOST'];
        if(!in_array($_SERVER['HTTP_HOST'], $this->whitelist) && !in_array($_SERVER['REMOTE_ADDR'], $this->whitelist)){
            $q = "UPDATE teamtable SET webpagehits=webpagehits+1, last_visit=NOW() WHERE teamid = $id";
            mysql_query($q);
        }
    }
    public function setPlayerHit($id)
    {
        if(!in_array($_SERVER['HTTP_HOST'], $this->whitelist) && !in_array($_SERVER['REMOTE_ADDR'], $this->whitelist)){           
            $q = "UPDATE playertable SET webpagehits=webpagehits+1, last_visit=NOW() WHERE playerid = $id";
            mysql_query($q);
        }
    }
    public function setLeagueHit($id)
    {
        if($id != 22012 || $id != 32012){
            if(!in_array($_SERVER['HTTP_HOST'], $this->whitelist) && !in_array($_SERVER['REMOTE_ADDR'], $this->whitelist)){
                $q = "UPDATE leaguetable SET webpagehits=webpagehits+1 WHERE leagueid = $id";
                mysql_query($q);
            }
        }
    }
    public function getEventRankPlayer($playerid,$eventtype,$season)
    {
        mysql_query("SET @rownum = 0, @rank = 1, @prev_val = NULL;");
        $q = "
        SELECT 
        rank, `event count`
        FROM
        (SELECT 
            @rownum := @rownum + 1 AS ROW,
            @rank := IF(
            @prev_val != t.`event count`,
            @rownum,
            @rank
            ) AS rank,
            @prev_val := t.`event count` AS `event count`,
            t.playerid 
        FROM
            (SELECT 
            t.playerid,
            COUNT(*) AS `event count` 
            FROM
            ".EVENTTABLE." e 
            JOIN playertable t 
                ON t.playerid = e.playerid 
                AND e.teamid = t.teamid 
                AND t.year = {$season}
            JOIN teamtable tt 
                ON tt.teamid = t.teamid 
            JOIN matchtable m 
                ON e.matchid = m.matchid 
            JOIN leaguetable l on l.leagueid = m.leagueid  
            WHERE e.eventtype = {$eventtype}
            AND e.playerid != - 1 
            AND l.year = {$season}
            AND e.ignore = 0
            GROUP BY e.playerid 
            ORDER BY COUNT(*) DESC) t,
            (SELECT 
            @rownum := 0) r) AS showRank 
        WHERE playerid = {$playerid} ;";
        

        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'rank' => $row['rank'],
                'count' => $row['event count']
            );
        }
        return $data;
    }
    public function getEventRankTeam($teamid,$eventtype, $season)
    {
        mysql_query("SET @rownum = 0, @rank = 1, @prev_val = NULL;");
        $q = "
        SELECT 
        rank, `event count`
        FROM
        (SELECT 
            @rownum := @rownum + 1 AS ROW,
            @rank := IF(
            @prev_val != t.`event count`,
            @rownum,
            @rank
            ) AS rank,
            @prev_val := t.`event count` AS `event count`,
            t.teamid
        FROM
            (SELECT tt.teamid,tt.teamname,COUNT(*) AS `event count` FROM ".EVENTTABLE." e
        JOIN playertable t ON t.playerid = e.playerid AND e.teamid = t.teamid AND t.year = {$season}
        JOIN teamtable tt ON tt.teamid = t.teamid
        JOIN matchtable m ON e.matchid = m.matchid
        JOIN leaguetable l ON l.leagueid = m.leagueid
        WHERE e.eventtype = {$eventtype}
        AND l.year = {$season}
        AND e.ignore = 0
        GROUP BY e.teamid, m.leagueid
        ORDER BY `event count` DESC) t,
            (SELECT 
            @rownum := 0) r) AS showRank 
        WHERE teamid = {$teamid}";

        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'rank' => $row['rank'],
                'count' => $row['event count']
            );
        }
        return $data;
    }

    function getGoalsScoringMinute($teamid,$season) {
        $q = "
            SELECT 
            p.`playerid`,p.`playername`,e.`minute`,e.`matchid`,m.`result`,home.`teamname` as homename,away.`teamname` as awayname,e.teamid as teamid
            FROM
            ".EVENTTABLE." e 
            JOIN playertable p ON p.`playerid` = e.`playerid` AND p.`teamid` = e.`teamid` AND p.year = {$season}
            JOIN matchtable m 
                ON e.matchid = m.matchid 
                AND (
                m.hometeamid = {$teamid} 
                OR m.awayteamid = {$teamid}
                )
                JOIN teamtable home ON m.`hometeamid` = home.`teamid` 
                JOIN teamtable away ON m.`awayteamid` = away.`teamid` 
                JOIN leaguetable l ON m.leagueid = l.leagueid
            WHERE (e.minute > 0 
                AND e.minute < 91) 
            AND (
                (e.eventtype IN (4, 8) 
                AND e.teamid = {$teamid}) 
                OR (e.eventtype = 9 and e.teamid != {$teamid})
                )
                AND l.year = {$season} 
                AND e.ignore = 0 
                ORDER BY e.minute asc;";
                

        $result = mysql_query($q);
        return self::getGoalArray($result,$teamid);
    }
    function getGoalsConcededMinute($teamid, $season)
    {
        $q = "
            SELECT
             p.`playerid`,p.`playername`,e.`minute`,e.`matchid`,m.`result`,home.`teamname` as homename,away.`teamname` as awayname, e.teamid as teamid
            FROM
            ".EVENTTABLE." e 
            JOIN playertable p ON p.`playerid` = e.`playerid` AND p.`teamid` = e.`teamid` AND p.year = {$season}
            JOIN matchtable m 
                ON e.matchid = m.matchid 
                AND (
                m.hometeamid = {$teamid} 
                OR m.awayteamid =  {$teamid} 
                )
                JOIN leaguetable l ON l.leagueid = m.leagueid
            JOIN teamtable home ON m.`hometeamid` = home.`teamid` 
                JOIN teamtable away ON m.`awayteamid` = away.`teamid`     
            WHERE (e.minute > 0 
                AND e.minute < 91) 
            AND (
                (
                e.eventtype IN (4, 8) 
                AND e.teamid !=  {$teamid} 
                ) 
                OR (e.eventtype = 9 
                AND e.teamid =  {$teamid} )
            ) AND l.year = {$season} AND e.ignore = 0 ORDER BY e.minute asc;
            ";

        $result = mysql_query($q);
        return self::getGoalArray($result);
    }
    private function getGoalArray($result)
    {
        $total = 0;
        $minute0 = 0;
        $minute1 = 0;
        $minute2 = 0;
        $minute3 = 0;
        $minute4 = 0;
        $minute5 = 0;
        $minute0array = array();
        $minute1array = array();
        $minute2array = array();
        $minute3array = array();
        $minute4array = array();
        $minute5array = array();
        
        
        while ($row = mysql_fetch_array($result)) {
            $min = $row['minute'];
            $infoArray = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'hometeamname' => $row['homename'],
                'awayteamname' => $row['awayname'],
                'minute' => $min,
                'result' => $row['result']
            );
            
           
            
            if ($min > 0 && $min <= 15) {
                $minute0++;
                $minute0array[] = $infoArray;
            }
            if ($min >= 16 && $min <= 30) {
                $minute1++;
                $minute1array[] = $infoArray;
            }
            if ($min >= 31 && $min <= 45) {
                $minute2++;
                $minute2array[] = $infoArray;
            }
            if ($min >= 46 && $min <= 60) {
                $minute3++;
                $minute3array[] = $infoArray;
            }
            if ($min >= 61 && $min <= 75) {
                $minute4++;
                $minute4array[] = $infoArray;
            }
            if ($min >= 76 && $min < 95) {
                $minute5++;
                $minute5array[] = $infoArray;
            }
        }
        $total = $minute0 + $minute1 + $minute2 + $minute3 + $minute4 + $minute5;
        $data = array (
            'pie' => array(
                array(
                    'label' => '0-15 ('.$minute0.' mål)' ,
                    'data' => $minute0 ,
                    'info' => $minute0array
                ),
                array(
                    'label' => '15-30 ('.$minute1.' mål)' ,
                    'data' => $minute1,
                    'info' => $minute1array
                ),
                array(
                    'label' => '30-45 ('.$minute2.' mål)' ,
                    'data' => $minute2,
                    'info' => $minute2array
                ),
                array(
                    'label' => '45-60 ('.$minute3.' mål)' ,
                    'data' => $minute3,
                    'info' => $minute3array
                ),
                array(
                    'label' => '60-75 ('.$minute4.' mål)' ,
                    'data' => $minute4,
                    'info' => $minute4array
                ),
                array(
                    'label' => '75-90 ('.$minute5.' mål)' ,
                    'data' => $minute5,
                    'info' => $minute5array
                )
            ),
            'total' => $total
        );
        return $data;
    }
    function getSuspList($leagueid)
    {
        $year = 2013;
        $q = "SELECT * FROM ".EVENTTABLE." e
            JOIN matchtable m ON e.`matchid` = m.`matchid` 
            JOIN playertable p ON e.playerid = p.playerid and e.teamid = p.teamid and p.leagueid = m.leagueid
            WHERE e.eventtype = 2
            AND e.playerid != -1
            AND m.leagueid = {$leagueid}
            AND e.ignore = 0
            GROUP BY e.`matchid`,e.`playerid`
            HAVING COUNT(e.`eventid`) = 1
            ORDER BY m.`dateofmatch` ASC ";
        
        $data = array();
        $twoYellow = array();
        $threeYellow = array();
        $fourYellow = array();
        $fiveYellow = array();
        $threeYellowSuspended = array();
        $fiveYellowSuspended = array();
        $redCardSuspended = array();
        
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            if(!isset($data[$row['playerid']])){
                $data[$row['playerid']] = array(
                    'matchid' => array ( $row['matchid'] ),
                    'leagueid' => $row['leagueid']
                );
            }
            else{
                $data[$row['playerid']]['matchid'][] = $row['matchid'];
                
                if(count($data[$row['playerid']]['matchid']) == 2){
                    $twoYellow[$row['playerid']]['teamid'] = $row['teamid'];
                    $twoYellow[$row['playerid']]['playername'] = $row['playername'];
                    $twoYellow[$row['playerid']]['leagueid'] = $row['leagueid'];
                }
                if(count($data[$row['playerid']]['matchid']) == 3){
                    $threeYellow[$row['playerid']]['matchid'] = $row['matchid'];
                    $threeYellow[$row['playerid']]['leagueid'] = $row['leagueid'];
                }
                if(count($data[$row['playerid']]['matchid']) == 4){
                    $fourYellow[$row['playerid']]['teamid'] = $row['teamid'];
                    $fourYellow[$row['playerid']]['playername'] = $row['playername'];
                    $fourYellow[$row['playerid']]['leagueid'] = $row['leagueid'];
                }
                if(count($data[$row['playerid']]['matchid']) == 5){
                    $fiveYellow[$row['playerid']]['matchid'] = $row['matchid'];
                    $fiveYellow[$row['playerid']]['leagueid'] = $row['leagueid'];
                }
            }
        }
        
        
        //var_dump($threeYellow);
        
        //Sent off
        $q = "SELECT * FROM ".EVENTTABLE." e
            JOIN matchtable m ON e.`matchid` = m.`matchid` 
            WHERE e.eventtype IN (1,3)
            AND e.playerid != -1
            AND m.leagueid = {$leagueid}
            AND e.ignore = 0
            GROUP BY e.`matchid`,e.`playerid`
            HAVING COUNT(e.`eventid`) = 1
            ORDER BY m.`dateofmatch` ASC ";
            
       $redcard = array();
       
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $redcard[$row['playerid']] = array(
                'matchid' => $row['matchid'] ,
                'leagueid' => $row['leagueid'] ,
                'eventtype' => $row['eventtype']
            );
        }
        //var_dump($redcard);
        foreach($redcard as $key => $value){
            
            $leagueid = $value['leagueid'];
            $value = $value['matchid'];
            $event = $value['eventtype'];
            
            $q = "SELECT SUBSTRING(m.dateofmatch FROM 1 FOR 16) as date,m.*,home.`teamname` AS homename, away.`teamname` AS awayname,p.*
            FROM matchtable m 
            JOIN matchtable c ON c.`matchid` = {$value} AND m.leagueid = {$leagueid}
            JOIN playertable p ON p.`playerid` = {$key} AND p.year = {$year}
            JOIN teamtable home ON m.`hometeamid` = home.`teamid`
            JOIN teamtable away ON m.`awayteamid` = away.`teamid`
            WHERE m.`dateofmatch` > c.`dateofmatch`
            AND (m.`awayteamid` = p.`teamid` OR m.`hometeamid` = p.`teamid`)
            AND (c.`awayteamid` = p.`teamid` OR c.`hometeamid` = p.`teamid`)
            ORDER BY DATE ASC LIMIT 1";
           // echo $q;
            
            $result = mysql_query($q);
            while($row = mysql_fetch_array($result)) {
                if(!$row['matchid'] == null && strtotime($row['dateofmatch']) > strtotime('now')){
                    $redCardSuspended[] = array(
                        'matchid' => $row['matchid'],
                        'dateofmatch' => $row['date'],
                        'homename' => $row['homename'],
                        'awayname' => $row['awayname'],
                        'hometeamid' => $row['hometeamid'],
                        'awayteamid' => $row['awayteamid'],
                        'playerid' => $row['playerid'],
                        'playername' => $row['playername'],
                        'teamid' => $row['teamid'],
                        'eventtype' => $event
                    );
                }
            }
        }
        foreach($threeYellow as $key => $value){
            
            $leagueid = $value['leagueid'];
            $value = $value['matchid'];
            
            
           
            $q = "SELECT SUBSTRING(m.dateofmatch FROM 1 FOR 16) as date,m.*,home.`teamname` AS homename, away.`teamname` AS awayname,p.*
            FROM matchtable m 
            JOIN matchtable c ON c.`matchid` = {$value} AND m.leagueid = {$leagueid}
            JOIN playertable p ON p.`playerid` = {$key} AND p.year = {$year}
            JOIN teamtable home ON m.`hometeamid` = home.`teamid`
            JOIN teamtable away ON m.`awayteamid` = away.`teamid`
            WHERE m.`dateofmatch` > c.`dateofmatch`
            AND (m.`awayteamid` = p.`teamid` OR m.`hometeamid` = p.`teamid`)
            AND (c.`awayteamid` = p.`teamid` OR c.`hometeamid` = p.`teamid`)
            ORDER BY DATE ASC LIMIT 1";
            //echo $q;
            
            $result = mysql_query($q);
            while($row = mysql_fetch_array($result)) {
                if(!$row['matchid'] == null && strtotime($row['dateofmatch']) > strtotime('now')) {
                    $threeYellowSuspended[] = array(
                        'matchid' => $row['matchid'],
                        'dateofmatch' => $row['date'],
                        'homename' => $row['homename'],
                        'awayname' => $row['awayname'],
                        'hometeamid' => $row['hometeamid'],
                        'awayteamid' => $row['awayteamid'],
                        'playerid' => $row['playerid'],
                        'playername' => $row['playername'],
                        'teamid' => $row['teamid']
                    );
                }
            }
        }
        foreach($fiveYellow as $key => $value){
            
            $leagueid = $value['leagueid'];
            $value = $value['matchid'];
            
            $q = "SELECT SUBSTRING(m.dateofmatch FROM 1 FOR 16) as date,m.*,home.`teamname` AS homename, away.`teamname` AS awayname,p.*
            FROM matchtable m 
            JOIN matchtable c ON c.`matchid` = {$value} and m.leagueid = {$leagueid}
            JOIN playertable p ON p.`playerid` = {$key} and p.year = {$year}
            JOIN teamtable home ON m.`hometeamid` = home.`teamid`
            JOIN teamtable away ON m.`awayteamid` = away.`teamid`
            WHERE m.`dateofmatch` > c.`dateofmatch`
            AND (m.`awayteamid` = p.`teamid` OR m.`hometeamid` = p.`teamid`)
            AND (c.`awayteamid` = p.`teamid` OR c.`hometeamid` = p.`teamid`)
            ORDER BY DATE ASC LIMIT 1";
            $result = mysql_query($q);
            while($row = mysql_fetch_array($result)) {
                if(!$row['matchid'] == null){
                    $fiveYellowSuspended[] = array(
                        'matchid' => $row['matchid'],
                        'dateofmatch' => $row['date'],
                        'homename' => $row['homename'],
                        'awayname' => $row['awayname'],
                        'hometeamid' => $row['hometeamid'],
                        'awayteamid' => $row['awayteamid'],
                        'playerid' => $row['playerid'],
                        'playername' => $row['playername'],
                        'teamid' => $row['teamid']
                    );
                }
            }
        }
        
        return array (
            'twoYellow' => $twoYellow,
            'threeYellow' => $threeYellowSuspended,
            'fourYellow' => $fourYellow,
            'fiveYellow' => $fiveYellowSuspended,
            'redCard' => $redCardSuspended
        );
    }
    
    public function getMostEvents($teamid, $season, $eventtypes)
    {
        $q = "SELECT p.`playerid`,p.`playername`,COUNT(*) AS events FROM eventtable e 
        JOIN leaguetable l ON l.`leagueid` = e.`leagueid`
        JOIN playertable p ON p.`playerid` = e.`playerid` AND p.teamid = e.teamid AND p.year = l.year
        WHERE e.eventtype IN ({$eventtypes}) AND e.teamid = {$teamid} AND l.`year` = {$season} AND e.ignore = 0
        GROUP BY e.`playerid`
        ORDER BY events DESC
        LIMIT 1;";
        
        
        //echo $q;
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'events' => $row['events']
            );
        }
        return $data;
    }
    public function getMostMinutes($teamid, $season)
    {
        $q = "SELECT p.`playerid`,p.`playername`,SUM(e.`minutesplayed`) AS minutes FROM playtable e 
        JOIN matchtable m  ON e.`matchid` = m.`matchid`
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
        JOIN playertable p ON p.`playerid` = e.`playerid` AND p.`teamid` = e.`teamid` AND p.year = l.year
        WHERE e.teamid={$teamid} AND l.`year` = {$season}
        AND e.ignore = 0 
        GROUP BY e.`playerid`
        ORDER BY minutes DESC
        LIMIT 1;";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'minutes' => $row['minutes']
            );
        }
        return $data;
    }
    
    public function getBestWinStreak($teamid, $season)
    {
        $q = "SELECT m.* FROM matchtable m 
        JOIN leaguetable l ON m.`leagueid` = l.`leagueid` 
        WHERE m.`awayteamid` = {$teamid} OR m.`hometeamid` = {$teamid} AND l.year = {$season} ORDER BY m.`dateofmatch` ASC";
        
        $data = array();
        $result = mysql_query($q);
        
        $streaks = array();
        $streak = array();
        
        while($row = mysql_fetch_array($result))
        {
            if($row['teamwonid'] == $teamid){
                $streak[] = $row;
            }else{
                if(!empty($streak)){
                    $streaks[] = $streak;
                }
                $streak = array();
            }
        }

        $maxCount = 0;
        $bestStreak = array();
        foreach($streaks as $arraystreak){
            $count = count($arraystreak);
            
            if($count >= $maxCount){
                $bestStreak = $arraystreak;
                $maxCount = $count;
            }
        }
        
        return $data = array(
            'winstreak' => $maxCount
        );
    }
    public function getCleanSheets($teamid, $season)
    {
        $q = "SELECT COUNT(*) as sum FROM matchtable m 
            JOIN leaguetable l ON m.leagueid = l.leagueid
            WHERE l.year = {$season} AND m.`result` NOT REGEXP '- : -|(Utsatt)'  AND ((m.hometeamid = {$teamid} AND m.awayscore = 0) OR (m.awayteamid = {$teamid} AND m.homescore = 0)) ";
        
        $result = mysql_query($q);
        $count = 0;
        while($row = mysql_fetch_array($result))
        {
           $count = $row['sum'];
        }
        return $count;
    }
    function getOverGoals($teamid,$season){
        
        $q = "SELECT (m.`homescore` + m.awayscore) AS totalgoals FROM matchtable m 
        JOIN leaguetable l ON m.`leagueid` = l.`leagueid` 
        WHERE m.`result` NOT REGEXP '- : -|(Utsatt)' AND l.year = {$season} AND (m.`awayteamid` = {$teamid} OR m.`hometeamid` = {$teamid})  ORDER BY m.`dateofmatch` ASC";
        
        //echo $q;
        $data = array();
        $result = mysql_query($q);
        $over3 = 0;
        $over4 = 0;
        $total = 0;
        
        while($row = mysql_fetch_array($result))
        {
            $total++;
            if($row['totalgoals'] >= 3){
                $over3++;
            }
            if($row['totalgoals'] >= 4){
                $over4++;
            }
        }
        if($total > 0){
            $over3 = ($over3 / $total) * 100;
            $over4 = ($over4 / $total) * 100;
        }else{
            $over3 = 0;
            $over4 = 0;
        }
        
        
        return $data[] = array(
                'over3' => number_format($over3, 2),
                'over4' => number_format($over4, 2)
            );
    }
    function getLatestMatches($teamid){
        return self::getMatches('latest',$teamid,5);
    }
    function getNextMatches($teamid){
        return self::getMatches('next',$teamid,5);
    }
    function getAllMatches($teamid,$season){
        return self::getMatches('all',$teamid,100,$season);
    }
    function getMatches($type,$teamid,$limit,$season = '')
    {
        $date = '';
        $order = '';
        if($type == 'latest'){
            $date = '<';
            $order = 'DESC';
        }else if($type == 'next'){
            $date = '>';
            $order = 'ASC';
        }else if($type == 'all'){
            $date = '<';
            $order = 'ASC';
        }
        else{
            echo 'not supported';
            return;
        }
         $q = "SELECT m.*, SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS dateofmatch1, home.`teamid` as homeid ,home.`teamname` as homename ,away.`teamid` as awayid ,away.`teamname` as awayname " .
            "FROM matchtable m  " .
            "JOIN leaguetable l ON m.`leagueid` = l.`leagueid` " .
            "JOIN teamtable home ON m.`hometeamid` = home.`teamid` " .
            "JOIN teamtable away ON m.`awayteamid` = away.`teamid` "      .
            "WHERE (m.`hometeamid` = {$teamid} OR m.`awayteamid`= {$teamid}) " .
            "AND m.`dateofmatch` {$date} NOW() " .
            ($season == '' ? '' : 'AND l.year = '.$season.' ') .     
            "ORDER BY m.`dateofmatch` {$order} " .
            "LIMIT {$limit}";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'matchid' => $row['matchid'],
                'homeid' => $row['homeid'],
                'homename' => $row['homename'],
                'awayid' => $row['awayid'],
                'awayname' => $row['awayname'],
                'result' => $row['result'],
                'dateofmatch' => $row['dateofmatch1']
            );
        }
        return $data;
    }
    function getSearchArray()
    {
        $q = "SELECT playername,playerid FROM playertable WHERE playerid != -1 GROUP BY playerid";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'label' => $row['playername'],
                'id' => $row['playerid'],
                'type' => 'player'
           );
        }
        
        $q = "SELECT teamname,teamid FROM teamtable WHERE teamid != -1 GROUP BY teamid ";
        
       
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'label' => $row['teamname'],
                'id' => $row['teamid'],
                'type' => 'team'
           );
        }
        return $data;
    }
    public function getLastUpdate($leagueid, $season)
    {
        if($leagueid == 0){
            $q = "SELECT MAX(l.lastupdate) as lastupdate FROM leaguetable l";
        }else{
            $q = "SELECT MAX(l.lastupdate) as lastupdate FROM leaguetable l
        WHERE l.java_variable IN ({$leagueid}) AND l.year = {$season}";
        }
           
        $result = mysql_query($q);
        $value = null;
        
        while($row = mysql_fetch_array($result))
        {
            $value = $row['lastupdate'];
        }
        return $value;
    }
    public function getPlayingMinutes($playerid, $season)
    {
        $q = "SELECT 
        SUM(minutesplayed) AS total,
        p.`teamid` AS teamid
        FROM
        playtable p 
        JOIN matchtable m 
        ON p.`matchid` = m.`matchid` 
        JOIN leaguetable l 
        ON l.`leagueid` = m.`leagueid` 
        WHERE p.`playerid` = {$playerid} 
        AND l.`year` = {$season} 
        AND p.ignore = 0";
        
        
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $totalplayer = $row['total'];
            $teamid = $row['teamid'];
        }
        if(!isset($teamid)){
            return;
        }
        $q2 = "SELECT 
        (COUNT(*) * 90) AS total,
        m.`hometeamid` AS teamid
        FROM
            matchtable m 
            JOIN leaguetable l 
            ON m.`leagueid` = l.`leagueid` 
        WHERE (
            m.`hometeamid` = {$teamid}
            OR m.`awayteamid` = {$teamid}
        ) 
        AND l.`year` = {$season}
        AND m.`result` NOT REGEXP '- : -|(Utsatt)' ";
        
        $result = mysql_query($q2);
        while($row = mysql_fetch_array($result))
        {
            $totalteam = $row['total'];
        }
        
        return number_format($totalplayer / $totalteam * 100 , 2);
    }
    public function getWinPercentageFromStart($playerid,$season)
    {
        $q = "SELECT (totalmatches_won / totalmatches_started) * 100 as percentage FROM (SELECT COUNT(*) AS totalmatches_started FROM playertable p 
        JOIN matchtable m ON (p.`teamid` = m.`awayteamid` OR p.`teamid` = m.`hometeamid`)
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid` AND l.`year` = p.`year` 
        JOIN playtable pl ON p.`playerid` = pl.`playerid` AND pl.`matchid` = m.`matchid` AND pl.ignore = 0
        WHERE p.`playerid` = {$playerid}
        AND l.`year` = {$season}
        AND m.`result` NOT REGEXP '- : -|(Utsatt)'
        AND START = 1) AS totalmatches_started, (SELECT COUNT(*) AS totalmatches_won FROM playertable p 
        JOIN matchtable m ON (p.`teamid` = m.`awayteamid` OR p.`teamid` = m.`hometeamid`)
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid` AND l.`year` = p.`year`
        JOIN playtable pl ON p.`playerid` = pl.`playerid` AND pl.`matchid` = m.`matchid` AND pl.ignore = 0
        WHERE p.`playerid` = {$playerid}
        AND l.`year` = {$season}
        AND m.`result` NOT REGEXP '- : -|(Utsatt)'
        AND m.`teamwonid` = p.`teamid`
        AND START = 1) AS totalmatches_won;
        ";
        
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $percentage = $row['percentage'];
        }
        
        return number_format($percentage,2);
    }
    public function getWinPercentageNotStart($playerid,$season)
    {
        $q = "SELECT (totalmatches_won / totalmatches) * 100 as percentage FROM (SELECT COUNT(*) AS totalmatches FROM playertable p 
        JOIN matchtable m ON (p.`teamid` = m.`awayteamid` OR p.`teamid` = m.`hometeamid`)
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid` AND l.`year` = p.`year`
        JOIN playtable pl ON p.`playerid` = pl.`playerid` AND pl.`matchid` = m.`matchid` AND pl.ignore = 0
        WHERE p.`playerid` = {$playerid}
        AND l.`year` = {$season}
        AND m.`result` NOT REGEXP '- : -|(Utsatt)'
        AND START = 0) AS totalmatches_started, (SELECT COUNT(*) AS totalmatches_won FROM playertable p 
        JOIN matchtable m ON (p.`teamid` = m.`awayteamid` OR p.`teamid` = m.`hometeamid`)
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid` AND l.`year` = p.`year`
        JOIN playtable pl ON p.`playerid` = pl.`playerid` AND pl.`matchid` = m.`matchid` AND pl.ignore = 0
        WHERE p.`playerid` = {$playerid}
        AND l.`year` = {$season}
        AND m.`result` NOT REGEXP '- : -|(Utsatt)'
        AND m.`teamwonid` = p.`teamid`
        AND START = 1) AS totalmatches_won;
        ";
        
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $percentage = $row['percentage'];
        }
        
        return number_format($percentage,2);
    }
    
    public function getPlayerNifsInfo($playerid)
    {
        $q = "SELECT * FROM playertable p 
        LEFT JOIN playertable_nifs nifs ON p.playerid_nifs = nifs.playerid
        WHERE p.playerid = {$playerid} LIMIT 1";
        
        $data = array();
       
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'height' => $row['height'],
                'dateofbirth' => $row['dateofbirth'],
                'position' => $row['position']
           );
        }
        return $data;
    }
    public function getHomestats($teamid, $season)
    {
        return self::getBestTeam('hometeam',0,$season,$teamid);
    }
    
    public function getAwaystats($teamid, $season)
    {
        return self::getBestTeam('awayteam',0,$season,$teamid);
    }

    public function getBestHometeam($leagueid,$season)
    {
        return self::getBestTeam('hometeam',$leagueid,$season);
    }
    public function getBestAwayteam($leagueid,$season)
    {
        return self::getBestTeam('awayteam',$leagueid,$season);
    }
    
    public function getBestTeam($team,$leagueid,$season,$teamid = 0)
    {
        if($team == 'hometeam'){
            $team = 'm.hometeamid';
            $opposite = 'm.awayteamid';
            $scored = 'm.homescore';
            $conceded = 'm.awayscore';
        }
        else if($team == 'awayteam')
        {
            $team = 'm.awayteamid';
            $opposite = 'm.hometeamid';
            $scored = 'm.awayscore';
            $conceded = 'm.homescore';
        }
        else{
            echo 'not supported ';
            return;
        }
        $q = "SELECT t.`teamname`,{$team} as teamid, " .
        "(SUM(IF(m.teamwonid = {$team}, 1,0)) * 3 + SUM(IF(m.teamwonid = 0, 1,0))) AS points,  " .
        "SUM({$scored}) AS goals,  " .
        "SUM({$conceded}) AS conceded, " .
        "SUM(IF(m.teamwonid = {$team}, 1,0)) AS wins, " .
        "SUM(IF(m.teamwonid = 0, 1,0)) AS draws, " .
        "SUM(IF(m.teamwonid = {$opposite}, 1,0)) AS loss,  " .
        "(SUM({$scored}) - SUM({$conceded})) AS mf "  .      
        "FROM matchtable m " .
        "JOIN teamtable t ON t.teamid = {$team} " .
        "JOIN leaguetable l ON m.`leagueid` = l.`leagueid`  " .
        "WHERE l.`year` = {$season}  " .
        ($leagueid == 0 ? ' ' : ' AND l.java_variable IN ('.$leagueid.') ') .
        ($teamid == 0 ? ' ' : ' AND t.teamid = '.$teamid.' ') .
        "AND m.`result` NOT REGEXP '- : -|(Utsatt)' " .
        "GROUP BY {$team} " .
        "ORDER BY points DESC, mf DESC " .
        "LIMIT 1";
        
        //echo $q;
        
        $data = array();   
            
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'points' => $row['points'],
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname'],
                'goals'=> $row['goals'],
                'conceded' => $row['conceded'],
                'wins' => $row['wins'],
                'draws' => $row['draws'],
                'loss' => $row['loss']
           );
        }
        return $data;
    }
    public function getTopscorerCount($teamid,$leagueid,$season)
    {
        $topscorer = self::getEventInfoJSON($teamid,$leagueid,'4,8',$season);
        $topscorerCount = $topscorer[0]['eventcount'];
       
        $q = "SELECT playerid,COUNT(*) as topscorer FROM eventtable e " .
        "LEFT JOIN leaguetable l ON e.leagueid = l.`leagueid` " . 
        "WHERE eventtype IN (4,8) ".
        "AND l.`year` = {$season} AND e.ignore = 0 " .
        ($leagueid == '0' ? '' : ' AND l.`java_variable` IN ('.$leagueid.') ') .
        ($teamid == '0' ? '' : ' AND e.teamid = '.$teamid.' ') .
        "GROUP BY playerid  " . 
        "HAVING COUNT(*) = ".$topscorerCount;
       
       // echo $q;
        
        $count = 0;
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $count++;
        }
        return $count;
    }
    function getSimilarPlayers($playerid)
    {
        $q = "SELECT 
                al.playerid as id,a.* 
            FROM
            playertable p 
            JOIN playertable_nifs n 
                ON p.`playerid_nifs` = n.`playerid` 
            LEFT JOIN playertable_nifs a 
                ON (
                a.`position` = n.`position` 
                AND a.`dateofbirth` BETWEEN DATE_SUB(n.`dateofbirth`, INTERVAL 1 YEAR) 
                AND DATE_ADD(n.`dateofbirth`, INTERVAL 1 YEAR)
                ) 
            JOIN playertable al 
                ON al.playerid_nifs = a.`playerid` 
            JOIN leaguetable l 
                ON l.leagueid = al.leagueid 
            WHERE p.`playerid` = {$playerid} 
            AND al.playerid != p.`playerid`
            GROUP BY a.`playerid` 
            ORDER BY RAND()
            LIMIT 5;";
            
            $data = array();
       
            $result = mysql_query($q);
            while($row = mysql_fetch_array($result))
            {
                $data[] = array(
                    'playerid' => $row['id'],
                    'name' => $row['playername'],
                    'dateofbirth' => $row['dateofbirth'],
                    'position' => $row['position']
                );
            }
            return $data;
    }
    function getLeagueTable($season, $leagueid)
    {
        $q = "
        SELECT 
          home.teamid,
           home.teamname,
        SUM(home.wins + away.wins) AS wins,
        SUM(home.draws + away.draws) AS draws,
        SUM(home.loss + away.loss) AS loss,
        SUM(home.goals + away.goals) AS goals,
        SUM(home.conceded + away.conceded) AS conceded,
        SUM(home.mf + away.mf) AS mf,
        SUM(home.points + away.points) AS points
        FROM
        (SELECT 
            m.hometeamid AS teamid,
            t.teamname,
            (
            SUM(IF(m.teamwonid = m.hometeamid, 1, 0)) * 3 + SUM(IF(m.teamwonid = 0, 1, 0))
            ) AS points,
            SUM(m.homescore) AS goals,
            SUM(m.awayscore) AS conceded,
            SUM(IF(m.teamwonid = m.hometeamid, 1, 0)) AS wins,
            SUM(IF(m.teamwonid = 0, 1, 0)) AS draws,
            SUM(IF(m.teamwonid = m.awayteamid, 1, 0)) AS loss,
            (SUM(m.homescore) - SUM(m.awayscore)) AS mf 
        FROM
            matchtable m 
            JOIN teamtable t 
            ON t.teamid = m.hometeamid 
            JOIN leaguetable l 
            ON m.`leagueid` = l.`leagueid` 
        WHERE l.`year` = {$season} 
            AND m.`result` NOT REGEXP '- : -|(Utsatt)' 
            AND l.java_variable = {$leagueid}
        GROUP BY m.hometeamid 
        ORDER BY points DESC,
            mf DESC) AS home JOIN 
        (SELECT 
            m.awayteamid AS teamid,
            (
            SUM(IF(m.teamwonid = m.awayteamid, 1, 0)) * 3 + SUM(IF(m.teamwonid = 0, 1, 0))
            ) AS points,
            SUM(m.awayscore) AS goals,
            SUM(m.homescore) AS conceded,
            SUM(IF(m.teamwonid = m.awayteamid, 1, 0)) AS wins,
            SUM(IF(m.teamwonid = 0, 1, 0)) AS draws,
            SUM(IF(m.teamwonid = m.hometeamid, 1, 0)) AS loss,
            (SUM(m.awayscore) - SUM(m.homescore)) AS mf 
        FROM
            matchtable m 
            JOIN teamtable t 
            ON t.teamid = m.awayteamid 
            JOIN leaguetable l 
            ON m.`leagueid` = l.`leagueid` 
        WHERE l.`year` = {$season} AND l.`java_variable` = {$leagueid}
            AND m.`result` NOT REGEXP '- : -|(Utsatt)' 
        GROUP BY m.awayteamid 
        ORDER BY points DESC,
            mf DESC) away ON home.teamid = away.teamid GROUP BY teamid ORDER BY points DESC, mf DESC, goals DESC";
        
         $data = array();
       
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname'],
                'wins' => $row['wins'],
                'draws' => $row['draws'],
                'loss' => $row['loss'],
                'goals' => $row['goals'],
                'conceded' => $row['conceded'],
                'mf' => $row['mf'],
                'points' => $row['points']
            );
        }
        return $data;
    }
}

