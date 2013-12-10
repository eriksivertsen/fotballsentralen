<?php

include "dbConnection.php";

class DatabaseScope {
    
    public function saveScope($scopeHash,$scopeEvents,$name){
        $url = self::alphaID($scopeHash,false,8);
        self::updateScopeHash($scopeEvents,$scopeHash,$url,$name);
        $events = array();
        $events['url'] = $url;
        return $events;
    }
    public function getRandomScope(){
        $q= "SELECT url FROM scope_hash
            WHERE url is not null
            ORDER BY RAND()
            LIMIT 1";
            
        $hash = '';
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
         $hash = $row['url'];  
        }
        return self::getScopeDatabase($hash);
    }
    public function getScopeDatabase($urlhash){
       $hash = self::alphaID($urlhash,true,8);
       $dbArray = self::getScopeInfo($hash);
       $data2  = json_decode($dbArray[0]['scopestring'],true);
       $scopeEvents = array();
       
       for($i=0;$i<9;$i++){
           $scopeEvents[] = array (
               'graphtype' => $data2[$i]['graphtype'],
               'eventid' => $data2[$i]['eventid'],
               'limit' => $data2[$i]['limit'],
               'type' => $data2[$i]['type'],
               'urlhash' => $urlhash
           );
       }
       self::updateScopeHash($scopeEvents,$hash,$urlhash,$dbArray[0]['name']);
       
       $array = array(
           'scopeEvents' => $scopeEvents,
           'from' => $data2[10]['from'],
           'to' => $data2[11]['to'],
           'leagueid' => $data2[9]['leagueid'],
           'name' => $dbArray[0]['name']
       );
       return $array;
    }
    public function getScope($leagueid, $from, $to, $scopeEvents)
    {
        $teamids = DatabaseLeague::getCustomLeagueTeams($leagueid);
        $teamid = 0;
        if(!empty($teamids)){
            $teamid = implode(" , ", $teamids);
            $leagueid = 0;
        }
        $scopeCount = 0;
        $events = array();
        
        foreach($scopeEvents as $eventtype){
            if($eventtype['graphtype'] == 0){
                $events['scope_event'.$scopeCount] = DatabaseScope::getEventScope($eventtype['eventid'],$eventtype['type'], $teamid,$leagueid, $from, $to,$eventtype['limit']);
            }
            else {
                $array = array('');
                if($eventtype['eventid'] == 0){
                    $array = DatabaseScope::getLeagueTableScope($leagueid, $teamid, $from, $to, $eventtype['limit']);
                }else if($eventtype['eventid'] == 1){
                    $array = DatabaseScope::getLeagueTableHomeScope($leagueid, $teamid,$from, $to, $eventtype['limit']);
                }else if($eventtype['eventid'] == 2){
                    $array = DatabaseScope::getLeagueTableAwayScope($leagueid, $teamid, $from, $to, $eventtype['limit']);
                }
                $events['scope_event'.$scopeCount] = $array;
            }
            $scopeCount++;
            if($scopeCount == 9){
                break;
            }
        }
        return $events;
    }
    
    private function updateScopeHash($scopeEvents,$scopeHash,$url,$name){
        $scopeEvents = json_encode($scopeEvents);
        $q = "INSERT INTO scope_hash (hashcode,url,name,scopestring,hits) VALUES ('$scopeHash','$url','$name','$scopeEvents',1) ON DUPLICATE KEY UPDATE hits=hits+1";
        mysql_query($q);
    }
    public function getScopeInfo($hash)
    {
        $q = "SELECT scopestring,name FROM scope_hash where hashcode =  " .$hash;
        $result = mysql_query($q);
        $data = array();
        while($row = mysql_fetch_array($result))
        {
            $data[] = array (
                'scopestring' => $row['scopestring'],
                'name' => $row['name']
            );
        }
        return $data;
    }
    
    public function getLeagueTableHomeScope($leagueid,$teamid,$from,$to,$limit)
    {
        return DatabaseScope::getBestTeamScope('hometeam',$leagueid,$from,$to,$teamid,$limit);
    }
    
    public function getLeagueTableAwayScope($leagueid,$teamid,$from,$to,$limit)
    {
        return DatabaseScope::getBestTeamScope('awayteam',$leagueid,$from,$to,$teamid,$limit);
    }
    
    private function getBestTeamScope($team,$leagueid,$from,$to,$teamid = 0, $limit = 1)
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
            echo $team . ' not supported ';
            return;
        }
        
        $orderby = 'points';
        $index = $orderby;
        
        $q = "SELECT 
            tabell.*,
            tabell.wins + tabell.draws + tabell.loss AS played ,
            ROUND((points/(tabell.wins + tabell.draws + tabell.loss)),2) AS pointavg
            FROM(SELECT t.`teamname`,{$team} as teamid, " .
        "(SUM(IF(m.teamwonid = {$team}, 1,0)) * 3 + SUM(IF(m.teamwonid = 0, 1,0))) AS points,  " .
        "SUM({$scored}) AS goals,  " .
        "SUM({$conceded}) AS conceded, " .
        "SUM(IF(m.teamwonid = {$team}, 1,0)) AS wins, " .
        "SUM(IF(m.teamwonid = 0, 1,0)) AS draws, " .
        "SUM(IF(m.teamwonid = {$opposite}, 1,0)) AS loss,  " .
        "(SUM({$scored}) - SUM({$conceded})) AS mf ,"  .      
        "(SUM(IF(m.teamwonid = m.awayteamid, 1, 0))+SUM(IF(m.teamwonid = 0, 1, 0))+SUM(IF(m.teamwonid = m.hometeamid, 1, 0))) AS played "    .     
        "FROM matchtable m " .
        "JOIN teamtable t ON t.teamid = {$team} " .
        "JOIN leaguetable l ON m.`leagueid` = l.`leagueid`  " .
        "WHERE m.`result` NOT REGEXP '- : -|(Utsatt)'  " .
        ($leagueid == 0 ? ' ' : ' AND l.java_variable IN ('.$leagueid.') ') .
        ($teamid == 0 ? ' ' : ' AND t.teamid IN ('.$teamid.' ) ') .
        "AND m.dateofmatch between '$from' and DATE_ADD('$to',INTERVAL 1 MONTH) " .
        "GROUP BY {$team} " .
        "" .
        ") as tabell 
        ORDER BY $orderby DESC, mf DESC LIMIT {$limit}";
        
        
        $data = array();   
            
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'points' => $row[$index],
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname'],
                'goals'=> $row['goals'],
                'conceded' => $row['conceded'],
                'mf' => $row['mf'],
                'wins' => $row['wins'],
                'draws' => $row['draws'],
                'loss' => $row['loss'],
                'played' => $row['played']
           );
        }
        return $data;
    }
    public function getLeagueTableScope($leagueid, $teamid, $from, $to,$limit)
    {
        $orderby = 'points';
        $index = $orderby;
        
        if($leagueid == 0){
           $leagueid = '1,2,3,4,5,6';
           //quickfix
        }
        
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
        SUM(home.points + away.points) AS points,
        (SUM(home.wins + away.wins) + SUM(home.draws + away.draws) + SUM(home.loss + away.loss)) AS played,
        ROUND(SUM(home.points + away.points) / (SUM(home.wins + away.wins) + SUM(home.draws + away.draws) + SUM(home.loss + away.loss)),2) as pointavg 
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
        WHERE m.`result` NOT REGEXP '- : -|(Utsatt)' 
            AND l.java_variable IN ( {$leagueid} ) 
            AND m.dateofmatch between '$from' and DATE_ADD('$to',INTERVAL 1 MONTH) ";
       
            if($teamid != 0){
                $q .= " AND  m.hometeamid IN (".$teamid.")  ";
            }  
            
            $q .= " GROUP BY m.hometeamid 
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
        WHERE l.`java_variable` IN ( {$leagueid} ) 
        AND m.dateofmatch between '$from' and DATE_ADD('$to',INTERVAL 1 MONTH) ";
        
        if($teamid != 0){
            $q .= " AND m.awayteamid IN (".$teamid.") ";
        }
        
        $q .= " AND m.`result` NOT REGEXP '- : -|(Utsatt)' 
        GROUP BY m.awayteamid 
        ORDER BY points DESC,
            mf DESC) away ON home.teamid = away.teamid GROUP BY teamid ORDER BY $orderby DESC, mf DESC, goals DESC LIMIT $limit";
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
                'points' => $row[$index],
                'played' => $row['played']
            );
        }
        return $data;
    }
    
    private function getEventScope($eventtype, $type, $teamid, $leagueid, $from, $to, $limit)
    {
        if($leagueid == '8'){
            $leagueid = '3,4,5,6';
        }
        if($type == 0) {
            if($eventtype == 11){
                return self::getTotalPlayerminutes($leagueid,$teamid,$from,$to,$limit);
            }
            if($eventtype == 12){
                return self::getCleanSheetsPlayer($leagueid,$teamid,$from,$to,$limit);
            }
            if($eventtype == 50){
                return self::getWinPercentageLeague($leagueid,$teamid,$from,$to,$limit);
            }
            if($eventtype == 60){
                return self::getMinutePrGoal($leagueid,$teamid,$from,$to,$limit);
            }
            if($eventtype == 70){
                return self::getGoalsAsSubstitutes('playerid',$leagueid,$teamid,$from,$to,$limit);
            }
            if($eventtype == 80){
                $leagueid = 0;
                return self::getPlayPercentage($leagueid,$teamid,$from,$to,$limit);
            }
            return self::getEventtype($eventtype,$leagueid,$teamid,$from,$to,$limit);
        }else{
            if($eventtype == 12){
                return self::getCleanSheetsTeam($leagueid,$teamid,$from,$to,$limit);
            }
            if($eventtype == 70){
                return self::getGoalsAsSubstitutes('teamid',$leagueid,$teamid,$from,$to,$limit);
            }
            return self::getEventtypeTeam($eventtype,$leagueid,$teamid,$from,$to,$limit);
        }
    }
    
    public function getTotalPlayerminutes($leagueid,$teamid,$from,$to,$limit)
    {
        
        $q = "SELECT pp.playerid,pp.playername,t.teamname,t.teamid,SUM(p.minutesplayed) AS `minutes played` FROM playtable p " . 
        "JOIN matchtable m ON m.matchid = p.matchid " .
        "JOIN playertable pp ON pp.playerid = p.playerid AND p.teamid = pp.teamid AND YEAR(m.dateofmatch) = pp.year " . 
        "JOIN teamtable t ON t.teamid = pp.teamid ".
        "JOIN leaguetable l ON m.leagueid = l.leagueid " .
        "WHERE pp.playerid != -1 " .
        "AND l.year = pp.year AND p.ignore = 0 " .
        "AND m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to', INTERVAL 1 MONTH) "    .    
        ($leagueid == 0 ? '' : ' AND l.java_variable IN ('.$leagueid.' ) ')      .  
        ($teamid == 0 ? '' : ' AND p.teamid IN ('.$teamid.' ) ')      .
        "GROUP BY p.playerid ".
        "ORDER BY SUM(p.minutesplayed) DESC " .
        "LIMIT " . $limit;
               
        
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
    
    
    private function getEventtype($eventtype, $leagueid, $teamid, $from,$to, $limit) 
    {
        if($eventtype == 10){
            $eventtype = '4,8';
        }
        $q = 
            "SELECT t.playerid,t.playername,tt.teamid,tt.teamname,COUNT(*) AS `event count`, eventtype FROM eventtable e " .
            "JOIN teamtable tt ON tt.teamid = e.teamid " .
            "JOIN matchtable m ON e.matchid = m.matchid  ".
            "JOIN leaguetable l ON l.leagueid = e.leagueid " .   
            "JOIN playertable t ON t.playerid = e.playerid AND e.teamid = t.teamid AND t.year = YEAR(m.dateofmatch) " .
            "WHERE e.eventtype IN ( ".$eventtype . " ) " .
            "AND e.playerid != -1 ".
            "AND m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) " . 
            ($leagueid == 0 ? '' : ' AND l.java_variable IN ('.$leagueid.') ') .
            ($teamid == 0 ? '' : ' AND e.teamid IN ('.$teamid.') ') .
            "AND e.ignore = 0 " .    
            "GROUP BY e.playerid " .       
            "ORDER BY `event count` DESC ".
            "LIMIT $limit";

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
    private function getEventtypeTeam($eventtype,$leagueid,$teamid,$from,$to,$limit)
    {
        $q = 
        "SELECT tt.teamid,tt.teamname,COUNT(*) AS `event count`, eventtype FROM eventtable e " .
        "JOIN teamtable tt ON tt.teamid = e.teamid " .
        "JOIN matchtable m ON e.matchid = m.matchid  ".
        "JOIN leaguetable  l ON l.leagueid = m.leagueid " .     
        "JOIN playertable t ON t.playerid = e.playerid AND e.teamid = t.teamid AND t.year = YEAR(m.dateofmatch) " . 
        "WHERE e.eventtype IN ( ".$eventtype . ")" .
        "AND e.playerid != -1 ".
        "AND m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) "  .
        ($leagueid == '0' ? '' : ' AND l.java_variable IN ('.$leagueid.') ')  .     
        ($teamid == '0' ? '' : ' AND e.teamid IN ('.$teamid.') ')  .     
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
    
    private function getCleanSheetsPlayer($leagueid,$teamid,$from,$to,$limit){
        
        $q="SELECT 
            p.playerid,
            goalkeeper.`playername`,
            p.`teamid`,
            t.`teamname`,
            COUNT(*) AS `event count`
            FROM
            (SELECT 
                p.`playerid`,
                p.`playername` 
            FROM
                playertable p 
                LEFT JOIN playertable_altom pa 
                ON pa.`playerid` = p.`playerid_altom` 
                LEFT JOIN playertable_nifs pn 
                ON pn.`playerid` = p.`playerid_nifs` 
            WHERE (
                p.is_goalkeeper = 1 
                OR pa.`position` LIKE 'Keeper' 
                OR pn.`position` LIKE 'Keeper' 
                OR p.`shirtnumber` = 1
                ) 
                AND p.`playerid` != - 1 
            GROUP BY p.`playerid`) AS goalkeeper 
            JOIN playtable p 
                ON p.`playerid` = goalkeeper.playerid 
                AND p.`start` = 1 
            JOIN matchtable m 
                ON m.`matchid` = p.`matchid` 
            JOIN leaguetable l 
                ON l.`leagueid` = m.`leagueid` 
                JOIN teamtable t ON t.`teamid` = p.`teamid`
            WHERE (
                (m.hometeamid = p.`teamid` AND m.awayscore = 0 ) 
                OR ( m.awayteamid = p.`teamid` AND m.homescore = 0 )
            ) 
            AND m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) " .
            ($leagueid == 0 ? "" : " AND l.java_variable IN ( {$leagueid} )") .
            ($teamid == 0 ? "" : " AND p.teamid IN ( {$teamid} )") .
            "GROUP BY p.`playerid` ORDER BY COUNT(*) Desc LIMIT $limit ";

        $data = array();   
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'eventcount'=> $row['event count'],
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname']
            );
        }
        return $data;
    }
    
    public function getWinPercentageLeague($leagueid,$teamid,$from,$to,$limit){
        
        $q = "SELECT totalwin.playerid, p.`playername`, totalstarts.teamid,t.`teamname`,  totalwin.won, totalstarts.starts, ROUND((totalwin.won / totalstarts.starts) * 100,2) AS percentage 
        FROM(SELECT p.`playerid`, m.dateofmatch, p.teamid ,COUNT(*) AS STARTS
        FROM playtable p 
        JOIN matchtable m ON m.`matchid` = p.`matchid`
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
        WHERE p.`start` = 1 ";
        if($leagueid != 0){
            $q .= " and l.`java_variable` IN (" .$leagueid . ") ";
        }
        else if($teamid != 0){
            $q .= " and p.`teamid` IN (" .$teamid . ") ";
        }
        
        $q .=" AND p.`ignore` = 0
        AND p.ignore = 0 
        AND m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) 
        GROUP BY p.`playerid`
        HAVING COUNT(*) >= 5) 
        AS totalstarts 
        LEFT JOIN (SELECT p.`playerid`,COUNT(*) AS won
        FROM playtable p 
        JOIN matchtable m ON m.`matchid` = p.`matchid`
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
        WHERE p.`start` = 1 ";
        if($leagueid != 0){
            $q .= " and l.`java_variable` IN (" .$leagueid . ") ";
        }
        else if($teamid != 0){
            $q .= " and p.`teamid` IN (" .$teamid . ") ";
        }
        $q .= " AND p.`ignore` = 0
        AND m.`teamwonid` = p.`teamid`
        AND m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) 
        AND p.ignore = 0 
        GROUP BY p.`playerid`) AS totalwin ON totalwin.playerid = totalstarts.playerid
        JOIN playertable p ON p.`playerid` = totalwin.`playerid` AND p.`teamid` = totalstarts.teamid AND p.`year` = YEAR(totalstarts.dateofmatch)
        JOIN teamtable t ON t.`teamid` = totalstarts.teamid
        ORDER BY percentage DESC LIMIT $limit ";
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'eventcount'=> $row['percentage'] . '%',
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname']
            );
        }
        return $data;
    }
    
    public function getMinutePrGoal($leagueid,$teamid,$from,$to,$limit){
        
        $q="
            SELECT 
            minutes.playerid,
            p.`playername`,
            p.teamid,
            t.`teamname`,
            minutes,
            goals.goals,
            ROUND((minutes  / goals.goals),0) AS percentage
            FROM
            (SELECT 
                p.`playerid`,
                p.`teamid`,
                SUM(p.minutesplayed) AS minutes 
            FROM
                playtable p 
                JOIN matchtable m 
                ON p.`matchid` = m.`matchid` 
                JOIN leaguetable l 
                ON l.leagueid = m.`leagueid` 
            WHERE m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) ";
        
            if($leagueid != 0){
                $q .= " AND l.java_variable IN ($leagueid) ";
            }
            else if($teamid != 0){
                $q .= " and p.`teamid` IN (" .$teamid . ") ";
            }
            $q .= " AND p.`ignore` = 0 
            GROUP BY p.`playerid` 
            HAVING SUM(p.`minutesplayed`) > 500) AS minutes 
            JOIN 
                (SELECT 
                e.`playerid`,
                COUNT(*) AS goals 
                FROM
                eventtable e 
                JOIN leaguetable l 
                ON l.`leagueid` = e.`leagueid` 
                JOIN matchtable m on m.matchid = e.matchid
                WHERE e.`eventtype` IN (4, 8) 
                AND m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH)";
                
            if($leagueid != 0){
                $q .= " AND l.java_variable IN ($leagueid) ";
            }
            else if($teamid != 0){
                $q .= " and e.`teamid` IN (" .$teamid . ") ";
            }
            $q .= " AND e.`ignore` = 0 
            GROUP BY e.`playerid`) AS goals 
            ON goals.playerid = minutes.playerid 
            JOIN playertable p ON p.`playerid` = minutes.playerid AND p.`teamid` = minutes.teamid AND p.`year` = 2013
            JOIN teamtable t ON t.`teamid` = p.`teamid`
            ORDER BY percentage ASC LIMIT $limit";
            
        $data = array();   
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'eventcount'=> $row['percentage'] . ' min/mål',
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname']
            );
        }
        return $data;
    }
    public function getGoalsAsSubstitutes($groupby,$leagueid,$teamid,$from,$to,$limit){
        
        $q="SELECT e.`playerid`, p.playername, e.`teamid`, t.teamname, COUNT(*) AS goals FROM eventtable e 
        JOIN leaguetable l ON l.`leagueid` = e.`leagueid`
        JOIN matchtable m on m.matchid = e.matchid 
        JOIN eventtable goal ON goal.`matchid` = e.`matchid` AND goal.`playerid` = e.`playerid` AND goal.`eventtype` IN (4,8) AND goal.ignore = 0
        JOIN teamtable t ON t.teamid = e.`teamid`
        JOIN playertable p ON p.playerid = e.`playerid` AND p.teamid = e.teamid AND p.year = l.`year`
        WHERE e.`eventtype` = 6
        AND e.ignore = 0 ";
        if($leagueid != 0){
            $q .= " AND l.java_variable IN ( $leagueid ) ";
        }
        else if($teamid != 0){
            $q .= " and p.`teamid` IN (" .$teamid . ") ";
        }
        
        $q .=" AND m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) 
        GROUP BY e.`$groupby`
        ORDER BY goals DESC LIMIT $limit";
        
        $data = array();   
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'eventcount'=> $row['goals'],
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname']
            );
        }
        return $data;
    }
    public function getPlayPercentage($leagueid,$teamid,$from,$to,$limit)
    {
       $q="SELECT 
            SUM(minutesplayed) AS total,
            p.`playerid`,
        pl.playername,
        t.teamname,
            p.`teamid` 
        FROM
            playtable p 
            JOIN matchtable m 
            ON p.`matchid` = m.`matchid` 
            JOIN leaguetable l 
            ON l.`leagueid` = m.`leagueid` 
        join playertable pl on pl.playerid = p.playerid and pl.teamid = p.teamid and pl.year = (SELECT MAX(YEAR) FROM playertable p WHERE p.`playerid` = pl.playerid)
        join teamtable t on t.teamid = p.teamid
        WHERE m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) 
            AND p.ignore = 0 
            AND p.`playerid` != - 1 ";
        if($leagueid != 0){
            $q .= " AND l.`java_variable` IN ($leagueid) ";
        }
        if($teamid != 0){
            $q .= " AND p.teamid IN ($teamid) ";
        }
        $q .=  "GROUP BY p.`playerid";
       
        echo $q;
        
        $teamMins = DatabaseTeam::getTeamMinutesScoped($from,$to);
        $data = array();
        var_dump($teamMins);
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $teamid =  $row['teamid'];
            $teamTotal  = $teamMins[$teamid];
            $playerTotal = $row['total'];
            if($playerTotal == 0){
                $percentage = 0;
            }else{
                $percentage = $playerTotal / $teamTotal * 10000;
            }
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'eventcount'=>  $percentage,
                'teamname' => $row['teamname'],
                'teamid' => $teamid
            );
        }
        uasort($data,"DatabaseUtils::cmp");
        $data = array_slice($data,0,$limit);
        $retVal = array();
        foreach($data as $value)
        {
             $retVal[] = array(
                'playerid' => $value['playerid'],
                'playername' => $value['playername'],
                'eventcount'=>  number_format($value['eventcount'] / 100,2) .'%',
                'teamname' => $value['teamname'],
                'teamid' => $value['teamid']
            );
        }
        return $retVal;
    }
    
     public function getEventInfoTotalTeam($eventtype,$leagueid,$teamid,$from,$to,$limit)
    {
        if($eventtype == 12){
            return DatabaseScope::getCleanSheetsTeam($leagueid,$from,$to,$limit);
        }
        if($eventtype == 70){
            return DatabaseScope::getGoalsAsSubstitutes('teamid',$leagueid,$teamid,$from,$to,$limit);
        }
        
        $teamid = 0;
        $teamids = DatabaseLeague::getCustomLeagueTeams($leagueid);
        if(!empty($teamids)){
            $teamid = implode(" , ", $teamids);
            $leagueid = 0;
        }
        
        $q = 
        "SELECT tt.teamid,tt.teamname,COUNT(*) AS `event count`, eventtype FROM eventtable e " .
        "JOIN playertable t ON t.playerid = e.playerid AND e.teamid = t.teamid AND t.year = " .$season . " " . 
        "JOIN teamtable tt ON tt.teamid = t.teamid " .
        "JOIN matchtable m ON e.matchid = m.matchid  ".
        "JOIN leaguetable  l ON l.leagueid = m.leagueid " .        
        "WHERE e.eventtype IN ( ".$eventtype . ")" .
        "AND e.playerid != -1 ".
        "AND l.year = "  . $season .  " "  .
        ($leagueid == '0' ? '' : ' AND l.java_variable IN ('.$leagueid.') ')  .     
        ($teamid == '0' ? '' : ' AND e.teamid IN ('.$teamid.') ')  .     
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

    public function getCleanSheetsTeam($leagueid,$teamid,$from,$to,$limit){
            $q= "SELECT home.teamid as teamid1, SUM(IF(home.c_home IS NULL,0,home.c_home) + IF(away.c_away IS NULL,0,away.c_away)) AS total_c, home.java_variable, t.teamname FROM 
            (SELECT 
            m.`hometeamid` AS teamid,
            COUNT(*) AS c_home,
            l.`java_variable`
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON m.leagueid = l.leagueid 
            WHERE m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) 
            AND m.`result` NOT REGEXP '- : -|(Utsatt)' 
            AND m.awayscore = 0 ".
            ($teamid == 0 ? '' : ' and m.hometeamid IN ('.$teamid.') ') .
            " GROUP BY m.`hometeamid` ) AS home
            left JOIN 
            (SELECT 
            m.awayteamid AS teamid,
            COUNT(*) AS c_away,
            l.`java_variable`
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON m.leagueid = l.leagueid 
            WHERE m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) 
            AND m.`result` NOT REGEXP '- : -|(Utsatt)'  " .
            ($teamid == 0 ? '' : ' and m.awayteamid IN ('.$teamid.') ') .
            "AND m.homescore = 0 
            GROUP BY m.`awayteamid`) AS away ON home.teamid = away.teamid JOIN teamtable t on home.teamid = t.teamid  ".
            ($leagueid == 0 ? "" : " WHERE home.java_variable IN ({$leagueid}) ") .
                    " GROUP BY home.teamid order by total_c desc LIMIT $limit" ;
            
        $data = array();   
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'teamid' => $row['teamid1'],
                'teamname' => $row['teamname'],
                'eventcount' => $row['total_c']
           );
        }
        return $data;
    }
        
    public function getMYSQLDate($tick)
    {
        switch($tick){
            
            case 0: return '2011-03-01 00:00:00';
            case 1: return '2011-04-01 00:00:00';
            case 2: return '2011-05-01 00:00:00';
            case 3: return '2011-06-01 00:00:00';
            case 4: return '2011-07-01 00:00:00';
            case 5: return '2011-08-01 00:00:00';
            case 6: return '2011-09-01 00:00:00';
            case 7: return '2011-10-01 00:00:00';
            case 8: return '2011-11-01 00:00:00';
                
            case 9: return '2012-03-01 00:00:00';    
            case 10: return '2012-04-01 00:00:00';
            case 11: return '2012-05-01 00:00:00';
            case 12: return '2012-06-01 00:00:00';
            case 13: return '2012-07-01 00:00:00';
            case 14: return '2012-08-01 00:00:00';
            case 15: return '2012-09-01 00:00:00';
            case 16: return '2012-10-01 00:00:00';
            case 17: return '2012-11-01 00:00:00';
                
            case 18: return '2013-03-01 00:00:00';    
            case 19: return '2013-04-01 00:00:00';
            case 20: return '2013-05-01 00:00:00';
            case 21: return '2013-06-01 00:00:00';
            case 22: return '2013-07-01 00:00:00';
            case 23: return '2013-08-01 00:00:00';
            case 24: return '2013-09-01 00:00:00';
            case 25: return '2013-10-01 00:00:00';
            case 26: return '2013-11-01 00:00:00';
                
            case 27: return '2014-03-01 00:00:00';
            case 28: return '2014-04-01 00:00:00';
            case 29: return '2014-05-01 00:00:00';
            case 30: return '2014-06-01 00:00:00';
            case 31: return '2014-07-01 00:00:00';
            case 32: return '2014-08-01 00:00:00';
            case 33: return '2014-09-01 00:00:00';
            case 34: return '2014-10-01 00:00:00';
            case 35: return '2014-11-01 00:00:00';
                
            default: return 'NOW()';
        }
    }
    
    public function alphaID($in, $to_num = false, $pad_up = false, $passKey = null)
    {
	$index = "abcdefghijklmnopqrstuvwxyz0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	if ($passKey !== null) {
		// Although this function's purpose is to just make the
		// ID short - and not so much secure,
		// with this patch by Simon Franz (http://blog.snaky.org/)
		// you can optionally supply a password to make it harder
		// to calculate the corresponding numeric ID

		for ($n = 0; $n<strlen($index); $n++) {
			$i[] = substr( $index,$n ,1);
		}

		$passhash = hash('sha256',$passKey);
		$passhash = (strlen($passhash) < strlen($index))
			? hash('sha512',$passKey)
			: $passhash;

		for ($n=0; $n < strlen($index); $n++) {
			$p[] =  substr($passhash, $n ,1);
		}

		array_multisort($p,  SORT_DESC, $i);
		$index = implode($i);
	}

	$base  = strlen($index);

	if ($to_num) {
		// Digital number  <<--  alphabet letter code
		$in  = strrev($in);
		$out = 0;
		$len = strlen($in) - 1;
		for ($t = 0; $t <= $len; $t++) {
			$bcpow = bcpow($base, $len - $t);
			$out   = $out + strpos($index, substr($in, $t, 1)) * $bcpow;
		}

		if (is_numeric($pad_up)) {
			$pad_up--;
			if ($pad_up > 0) {
				$out -= pow($base, $pad_up);
			}
		}
		$out = sprintf('%F', $out);
		$out = substr($out, 0, strpos($out, '.'));
	} else {
		// Digital number  -->>  alphabet letter code
		if (is_numeric($pad_up)) {
			$pad_up--;
			if ($pad_up > 0) {
				$in += pow($base, $pad_up);
			}
		}

		$out = "";
		for ($t = floor(log($in, $base)); $t >= 0; $t--) {
			$bcp = bcpow($base, $t);
			$a   = floor($in / $bcp) % $base;
			$out = $out . substr($index, $a, 1);
			$in  = $in - ($a * $bcp);
		}
		$out = strrev($out); // reverse
	}
	return $out;
    }
}
?>
