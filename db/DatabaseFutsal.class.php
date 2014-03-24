<?php

include "dbConnection.php";

class DatabaseFutsal {
    
    
    public function getLeague($season){
        if($season == 0){
            $season = Constant::ALL_STRING;
        }
        $data = array(
            'leaguetable' => DatabaseFutsal::getLeagueTable($season),
            'topscorer' => DatabaseFutsal::getEventlist('4,8',$season),
            'yellowcard' => DatabaseFutsal::getEventlist('2',$season),
            'redcard' => DatabaseFutsal::getEventlist('1,3',$season),
        );
        return $data;
    }
    
    public function getPlayer($playerid,$teamid,$season){
        if($season == 0){
            $season = Constant::ALL_STRING;
        }
        $data = array(
            'playerinfo' => DatabaseFutsal::getPlayerInfo($playerid,$teamid,$season),
            'teams' => DatabaseFutsal::getPlayerTeam($playerid,$season)
        );
        return $data;
    }
    
    public function getTeam($teamid,$season){
        
//        DatabaseUtils::setTeamHit($teamid,$season);
        
        if($season == 0){
            $season = Constant::ALL_STRING;
        }
        
        $allmatches = DatabaseFutsal::getAllMatches($teamid,'both',$season);
        $matchids = array();
        foreach($allmatches as $array){
            $matchids [] = $array['matchid'];
        }
        $goalscorers = DatabaseFutsal::getGoalScoreresMatch($matchids);
        
        $data = array(
            'teamplayer' => DatabaseFutsal::getTeamPlayer($teamid ,$season),
            'leaguetable' => DatabaseFutsal::getLeagueTable($season),
            'topscorer' => DatabaseFutsal::getMostEvents($teamid, $season,'4,8'),
            'mostyellow' => DatabaseFutsal::getMostEvents($teamid, $season,2),
            'mostred' => DatabaseFutsal::getMostEvents($teamid, $season,'1,3'),
            'allmatches' => $allmatches,
            'goalscorers' => $goalscorers
        );
        return $data;
    }
    
    public function getGoalScoreresMatch(array $matchid)
    {
        if(empty($matchid)){
            return array();
        }
        $matchids = implode($matchid,',');
        $q = "SELECT 
        e.matchid, p.`playername`,p.`playerid`,e.`eventtype`,e.teamid
        FROM
        eventtable_futsal e 
        JOIN playertable_futsal p 
            ON p.`playerid` = e.`playerid` 
        WHERE e.`matchid` IN ($matchids)
        AND e.`eventtype` IN (4, 8, 9)
        AND e.ignore = 0 
        GROUP BY e.matchid,e.`playerid`,e.`minute`
        ORDER BY e.minute ASC";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[$row['matchid']][] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'eventtype' => $row['eventtype'],
                'teamid' => $row['teamid']
            );
        }
        return $data;
    }
    
    
    public function getMostEvents($teamid, $season, $eventtypes)
    {
        $q = "SELECT p.`playerid`,p.`playername`,COUNT(*) AS events FROM eventtable_futsal e 
        JOIN leaguetable l ON l.`leagueid` = e.`leagueid`
        JOIN playertable_futsal p ON p.`playerid` = e.`playerid` 
        WHERE e.eventtype IN ({$eventtypes}) AND e.teamid = {$teamid} AND l.`year` IN ( {$season} ) AND e.ignore = 0
        GROUP BY e.`playerid`
        ORDER BY events DESC
        LIMIT 1;";
        
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
    
    public function getLatestMatches($teamid,$type){
        return self::getMatches('latest',$type,$teamid,5);
    }
    public function getNextMatches($teamid,$type){
        return self::getMatches('next',$type,$teamid,5);
    }
    public function getAllMatches($teamid,$type,$season){
        return self::getMatches('all',$type,$teamid,100,$season);
    }
    public function getMatches($type,$loc,$teamid,$limit,$season = '')
    {
        $date = '';
        $order = '';
        $homeaway = ' (m.`hometeamid` = '.$teamid.' OR m.`awayteamid`= '.$teamid.')';
        
        if($loc == 'home'){
            $homeaway = ' (m.`hometeamid` = '.$teamid.')';
        }else if($loc == 'away'){
            $homeaway = ' (m.`awayteamid`= '.$teamid.')';
        }
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
         $q = "SELECT m.*, SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS dateofmatch1, unix_timestamp(m.dateofmatch) as timestamp, home.`teamid` as homeid ,home.`teamname` as homename ,away.`teamid` as awayid ,away.`teamname` as awayname, m.teamwonid " .
            "FROM matchtable_futsal m  " .
            "JOIN leaguetable l ON m.`leagueid` = l.`leagueid` " .
            "JOIN teamtable_futsal home ON m.`hometeamid` = home.`teamid` " .
            "JOIN teamtable_futsal away ON m.`awayteamid` = away.`teamid` "      .
            "WHERE " . $homeaway . " " .
            "AND m.`dateofmatch` {$date} NOW() " .
            ($season == '' ? '' : 'AND l.year IN ( '.$season.' ) ') .
            ($type == 'latest' ? ' AND m.result NOT REGEXP \'- : -|(Utsatt)\'' : '' ) .
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
                'dateofmatch' => $row['dateofmatch1'],
                'teamwonid' => $row['teamwonid'],
                'timestamp' => $row['timestamp']
            );
        }
        return $data;
    }    
    
    private function getPlayerInfo($playerid,$teamid,$season)
    {
        $q="SELECT * FROM (
         SELECT 
            e.`playerid`,
            m.matchid, m.result, UNIX_TIMESTAMP(m.dateofmatch) as timestamp, m.dateofmatch, home.teamname as homename, away.teamname as awayname, m.awayteamid as awayid,m.hometeamid as homeid,
            SUM(IF(e.`eventtype` = \"4\", 1, 0)) AS `goals scored`,
            SUM(IF(e.eventtype = \"8\", 1, 0)) AS `penalty`,
            SUM(IF(e.eventtype =\"9\", 1, 0)) AS `own goals`,
            SUM(IF(e.eventtype = \"2\", 1, 0)) AS `yellow cards`,
            (
            SUM(IF(e.eventtype = \"3\", 1, 0)) + SUM(IF(e.eventtype = \"1\", 1, 0))
            ) AS `red cards`
        FROM
            eventtable_futsal e 
            JOIN matchtable_futsal m 
            ON e.matchid = m.matchid 
            JOIN teamtable_futsal home ON home.teamid = m.hometeamid
            JOIN teamtable_futsal away ON away.teamid = m.awayteamid 
            JOIN leaguetable l 
            ON m.leagueid = l.leagueid 
        WHERE l.year IN ($season) ";
        if($teamid != 0) {
            $q .= "AND e.`teamid` = $teamid ";
        }
        $q .= "AND e.`playerid` = $playerid AND e.ignore = 0 
        GROUP BY e.`matchid`) AS `events`
        JOIN (SELECT 
        p.`playerid`,p.`playername`, p.extra
        FROM
        eventtable_futsal e 
        JOIN playertable_futsal p 
        WHERE e.`playerid` = p.`playerid`
        AND p.`real_playerid` IS NULL
        GROUP BY e.`playerid`) AS players ON players.playerid = events.playerid";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'extra' => $row['extra'],
                'goals' => $row['goals scored'],
                'penalty' =>$row['penalty'],
                'owngoals' => $row['own goals'],
                'yellowcards' => $row['yellow cards'],
                'redcards' => $row['red cards'],
                'hometeamname' => $row['homename'],
                'awayteamname' => $row['awayname'],
                'homeid' => $row['homeid'],
                'awayid' => $row['awayid'],
                'result' => $row['result'],
                'timestamp' => $row['timestamp'],
                'dateofmatch' => $row['dateofmatch'],
                'matchid' => $row['matchid']
            );
        }
        return $data;
    }
    
    private function getTeamPlayer($teamid,$season)
    {
        $q = "SELECT * FROM (
         SELECT 
            e.`playerid`,
            SUM(IF(e.`eventtype` = \"4\", 1, 0)) AS `goals scored`,
            SUM(IF(e.eventtype = \"8\", 1, 0)) AS `penalty`,
            SUM(IF(e.eventtype = \"9\", 1, 0)) AS `own goals`,
            SUM(IF(e.eventtype = \"2\", 1, 0)) AS `yellow cards`,
            (
            SUM(IF(e.eventtype = \"3\", 1, 0)) + SUM(IF(e.eventtype = \"1\", 1, 0))
            ) AS `red cards`
        FROM
            eventtable_futsal e 
            JOIN matchtable_futsal m 
            ON e.matchid = m.matchid 
            JOIN leaguetable l 
            ON m.leagueid = l.leagueid 
        WHERE e.`teamid` = $teamid
            AND l.year IN ($season) 
            AND e.ignore = 0 
        GROUP BY e.`playerid`) AS `events`
        JOIN (SELECT 
        p.`playerid`,p.`playername`, t.teamname
        FROM
        eventtable_futsal e 
        JOIN playertable_futsal p 
        ON e.`playerid` = p.`playerid` 
        AND e.`teamid` = $teamid
        AND p.`real_playerid` IS NULL
        JOIN teamtable_futsal t on t.teamid = e.teamid 
        GROUP BY e.`playerid`) AS players ON players.playerid = events.playerid";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'teamname' => $row['teamname'],
                'goals' => $row['goals scored'],
                'penalty' =>$row['penalty'],
                'owngoals' => $row['own goals'],
                'yellowcards' => $row['yellow cards'],
                'redcards' => $row['red cards']
            );
        }
        return $data;
    }
    
    private function getLeagueTable($season)
    {
        $orderby = 'points';
        $index = $orderby;
        $limit = 20;
                
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
            matchtable_futsal m 
            JOIN teamtable_futsal t 
            ON t.teamid = m.hometeamid 
            JOIN leaguetable l 
            ON m.`leagueid` = l.`leagueid` 
        WHERE l.`year` IN ( {$season} )
            AND m.`result` NOT REGEXP '- : -|(Utsatt)' ";
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
            matchtable_futsal m 
            JOIN teamtable_futsal t 
            ON t.teamid = m.awayteamid 
            JOIN leaguetable l 
            ON m.`leagueid` = l.`leagueid` 
        WHERE l.`year` IN ( {$season} ) ";
                
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
    private function getPlayerTeam($playerid,$season){
        
        $q = "SELECT 
        e.`playerid`,
        e.`teamid`,
        t.teamname
        FROM
        eventtable_futsal e
        JOIN matchtable_futsal m ON m.`matchid` = e.`matchid`
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
        JOIN teamtable_futsal t on t.teamid = e.teamid 
        WHERE e.`playerid` = $playerid  AND l.`year` IN ($season)
        GROUP BY e.`teamid`";
        
        $columns = array('playerid','teamid','teamname');
        return DatabaseFutsal::getData($q,$columns);
    }
    public function getData($query, array $columns)
    {
        $data = array();
        $result = mysql_query($query);
        if(!$result){
            echo $query;
            die;
        }
        while($row = mysql_fetch_array($result))
        {
            $dataRow = array();
            foreach($columns as $colname){
                $dataRow[$colname] = $row[$colname];
            }
            array_push($data,$row);
        }
       
        return $data;
    }
    public function getEventlist($eventtype,$season,$teamid = 0,$limit = 10)
    {
        $q = 
        "SELECT t.playerid,t.playername,tt.teamid,tt.teamname,COUNT(*) AS `event count`, eventtype 
            FROM eventtable_futsal e " .
        "JOIN matchtable_futsal m ON e.matchid = m.matchid  ".
        "JOIN playertable_futsal t ON t.playerid = e.playerid " .
        "JOIN teamtable_futsal tt ON e.teamid = tt.teamid " .
        "JOIN leaguetable l ON l.leagueid = e.leagueid " .   
        "WHERE e.eventtype IN ( ".$eventtype . " )" .
        "AND e.playerid != -1 ".
        "AND l.year IN ( "  . $season .  " ) "  .
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
    public function getEventlistTeam($eventtype,$season,$teamid = 0,$limit = 10)
    {
        $q = 
        "SELECT t.playerid,t.playername,tt.teamid,tt.teamname,COUNT(*) AS `event count`, eventtype 
            FROM eventtable_futsal e " .
        "JOIN matchtable_futsal m ON e.matchid = m.matchid  ".
        "JOIN playertable_futsal t ON t.playerid = e.playerid " .
        "JOIN teamtable_futsal tt ON e.teamid = tt.teamid " .
        "JOIN leaguetable l ON l.leagueid = e.leagueid " .   
        "WHERE e.eventtype IN ( ".$eventtype . " )" .
        "AND e.playerid != -1 ".
        "AND l.year IN ( "  . $season .  " ) "  .
        ($teamid == 0 ? '' : ' AND e.teamid IN ('.$teamid.') ') .
        "AND e.ignore = 0 " .    
        "GROUP BY e.teamid " .       
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
}
