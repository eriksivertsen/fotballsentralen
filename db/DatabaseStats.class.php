<?php
include "dbConnection.php";

class DatabaseStats {
    
    public function getClickTable()
    {
        $q = "SELECT trending.clicked_id,
            trending.clicktype,
            trending.season,
            trending.ip,
            trending.time,
            p.`playername` AS name1,
            p1.`playername` AS name2,
            p2.`playername` AS futsalplayername,
            t.`teamname`,
            t1.`teamname` as futsalteamname,
            m.`matchid`,
            home.`teamname` AS hometeam,
            away.`teamname` AS awayteam,
            l.leaguename as leaguename,
            l1.leaguename as leaguename2,
            hash.name as scopename
            FROM clicktable trending 
            LEFT JOIN playertable p ON p.`playerid` = trending.clicked_id AND p.year = 2012
            LEFT JOIN playertable p1 ON p1.`playerid` = trending.clicked_id AND p1.year = 2013
            LEFT JOIN teamtable t ON t.`teamid` = trending.clicked_id
            LEFT JOIN teamtable_futsal t1 ON t1.`teamid` = trending.clicked_id
            LEFT JOIN playertable_futsal p2 ON p2.`playerid` = trending.clicked_id
            LEFT JOIN matchtable m ON m.`matchid` = trending.clicked_id
            LEFT JOIN teamtable home ON m.`hometeamid` = home.`teamid`
            LEFT JOIN teamtable away ON m.`awayteamid` = away.`teamid`
            LEFT JOIN league l on l.java_variable = trending.clicked_id
            LEFT JOIN leaguetable l1 ON l1.`leagueid` = trending.clicked_id
            LEFT JOIN scope_hash hash ON hash.`hashcode` = trending.clicked_id
            GROUP BY TIME,ip
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
                    'season' => $row['season'],
                    'playerid' => $row['clicked_id'],
                    'playername' => $playername,
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }else if($row['clicktype']=='team' || $row['clicktype']=='team_search'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'season' => $row['season'],
                    'teamid' => $row['clicked_id'],
                    'teamname' => $row['teamname'],
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }else if($row['clicktype']=='preview' || $row['clicktype'] == 'match_internal'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'season' => $row['season'],
                    'matchid' => $row['clicked_id'],
                    'hometeam' => $row['hometeam'] . ' - ' . $row['awayteam'],
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }else if($row['clicktype']=='league'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'season' => $row['season'],
                    'matchid' => $row['clicked_id'],
                    'name' => $row['leaguename'],
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }else if($row['clicktype']=='suspension'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'season' => $row['season'],
                    'matchid' => $row['clicked_id'],
                    'name' => $row['leaguename2'],
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }
            else if($row['clicktype']=='scope'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'season' => $row['season'],
                    'matchid' => $row['clicked_id'],
                    'name' => $row['scopename'],
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }
            else if($row['clicktype']=='futsal_player'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'season' => $row['season'],
                    'matchid' => $row['clicked_id'],
                    'name' => $row['futsalplayername'],
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }
            else if($row['clicktype']=='futsal_team'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'season' => $row['season'],
                    'matchid' => $row['clicked_id'],
                    'name' => $row['futsalteamname'],
                    'time' => $row['time'],
                    'ip' => $row['ip']
                );
            }else{
                $data[] = array(
                    'type' => $row['clicktype'],
                    'season' => $row['season'],
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
    public function getLatestObserverDatePL()
    {
        $q= "SELECT MAX(lastupdate) as max FROM matchobserve_pl";
        
        $data = '';
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data = $row['max'];
        }
        return $data;
    }
    public function getLatestMailSent()
    {
        $q = "SELECT MAX(lastupdate) as max FROM match_observe WHERE mailsent = 1";
        $data = '';
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data = $row['max'];
        }
        return $data;
    }
    public function getLatestLeagueUpdate()
    {
        $q = "SELECT MAX(lastupdate) as max FROM leaguetable";
        $data = '';
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data = $row['max'];
        }
        return $data;
    }
    public function getRatings()
    {
        $q = "SELECT pagename, rating, comment, fromname, timestamp FROM rating";
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'pagename' => $row['pagename'],
                'rating' => $row['rating'],
                'comment' => $row['comment'],
                'fromname' => $row['fromname'],
                'timestamp' => $row['timestamp']
            );
        }
        return $data;
    }
    public function getEarliestCrawlerStart()
    {
        
        $q = "SELECT 
            l.leaguename ,
            l.`leagueid`,
            DATE_ADD(MAX(m.dateofmatch),INTERVAL 3 HOUR) AS yo
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON l.leagueid = m.leagueid 
            WHERE DATE(m.`dateofmatch`) = DATE(NOW())
            GROUP BY l.`leagueid`
            ORDER BY yo desc";
        
        $data = '';
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data = $row['yo'];
        }
        return $data;
    }
        public function getConcededPercentageHalfsPL($teamid,$season)
    {
        $q ="SELECT 
        firsthalftotal.teamid AS teamid, 
        firsthalftotal.conceded AS first_half, 
        secondhalftotal.conceded AS second_half,
        ROUND((firsthalftotal.conceded / (firsthalftotal.conceded + secondhalftotal.conceded)) * 100,2) AS percentage_first,
        ROUND((secondhalftotal.conceded / (firsthalftotal.conceded + secondhalftotal.conceded)) * 100,2) AS percentage_second
        FROM (
            SELECT 
            '{$teamid}' AS teamid,
            SUM(secondhalf.goals_conceded + secondhalf.own_goals) AS conceded
            FROM (
            SELECT 
            COUNT(e.`eventid`) AS goals_conceded,
            COUNT(own.eventid) AS own_goals
            FROM
            matchtable_pl m 
            JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
            LEFT JOIN eventtable_pl e ON e.`matchid` = m.`matchid` AND e.`eventtype` IN (4,8)
            LEFT JOIN eventtable_pl own ON own.`matchid` = m.`matchid` AND own.`eventtype` = 9 AND own.`minute` >= 45
            WHERE 
            (m.`hometeamid` = {$teamid} OR m.`awayteamid` = {$teamid}) 
            AND l.`year` = {$season}
            AND e.`minute` >= 45
            AND e.`teamid` != {$teamid}
            AND m.`result` NOT LIKE '- : -'
            GROUP BY m.`matchid`) AS secondhalf ) AS secondhalftotal JOIN (
            SELECT 
            '{$teamid}' AS teamid,
            SUM(secondhalf.goals_conceded + secondhalf.own_goals) AS conceded
            FROM (
            SELECT 
            COUNT(e.`eventid`) AS goals_conceded,
            COUNT(own.eventid) AS own_goals
            FROM
            matchtable_pl m 
            JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
            LEFT JOIN eventtable_pl e ON e.`matchid` = m.`matchid` AND e.`eventtype` IN (4,8)
            LEFT JOIN eventtable_pl own ON own.`matchid` = m.`matchid` AND own.`eventtype` = 9 AND own.minute < 45
            WHERE 
            (m.`hometeamid` = {$teamid} OR m.`awayteamid` = {$teamid}) 
            AND l.`year` = {$season}
            AND e.`minute` < 45
            AND e.`teamid` != {$teamid}
            AND m.`result` NOT LIKE '- : -'
            GROUP BY m.`matchid`) AS secondhalf ) AS firsthalftotal ON firsthalftotal.teamid = secondhalftotal.teamid ";
            
        $result = mysql_query($q);
        $data = array();
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'teamid' => $row['teamid'],
                'first_half' => $row['first_half'],
                'second_half' => $row['second_half'],
                'total' => ($row['first_half'] + $row['second_half']),
                'percentage_first' => $row['percentage_first'],
                'percentage_second' => $row['percentage_second']
            );
        }
        return $data;
    }
    public function getScoringPercentageHalfsPL($teamid,$season)
    {
        $q =
            "SELECT 
            first.teamid,
            first.first_half,
            second.second_half,
            ROUND((first.first_half / (first.first_half + second.second_half)) * 100,2) AS percentage_first,
            ROUND((second.second_half / (first.first_half + second.second_half)) * 100,2) AS percentage_second
            FROM
            (SELECT 
                e.teamid,
                COUNT(*) AS first_half
            FROM
                eventtable_pl e 
        JOIN matchtable_pl m on e.matchid = m.matchid
                JOIN leaguetable l 
                ON m.leagueid = l.leagueid 
            WHERE e.minute <= 45 
                AND l.year = {$season} 
                AND e.eventtype IN (4, 8) 
                AND e.ignore = 0
                AND e.teamid = {$teamid}
            GROUP BY e.teamid) AS `first`
            JOIN 
                (SELECT 
                e.teamid,
                COUNT(*)  AS second_half 
                FROM
                eventtable_pl e 
                JOIN matchtable_pl m on e.matchid = m.matchid
                JOIN leaguetable l 
                ON m.leagueid = l.leagueid 
                WHERE e.minute >= 45 
                AND l.year = {$season} 
                AND e.eventtype IN (4, 8) 
                AND e.ignore = 0
                AND e.teamid = {$teamid}
                GROUP BY e.teamid ) AS `second` 
                ON `first`.teamid = `second`.teamid";
                
        $result = mysql_query($q);
        $data = array();
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'teamid' => $row['teamid'],
                'firsthalfgoals' => $row['first_half'],
                'secondhalfgoals' => $row['second_half'],
                'total' => ($row['first_half'] + $row['second_half']),
                'percentage_first' => $row['percentage_first'],
                'percentage_second' => $row['percentage_second']
            );
        }
        return $data;
    }
    public function getMatchesOneWeek()
    {
        $q = "SELECT m.matchid, UNIX_TIMESTAMP(m.dateofmatch) * 1000 as timestamp, home.teamname as homename, away.teamname as awayname, l.java_variable, home.teamid as homeid, away.teamid as awayid
            FROM matchtable m 
            JOIN teamtable home on m.hometeamid = home.teamid 
            JOIN teamtable away on m.awayteamid = away.teamid 
            JOIN leaguetable l ON l.leagueid = m.leagueid
            WHERE m.`result` LIKE '- : -' AND m.`dateofmatch` BETWEEN  NOW() AND NOW() + INTERVAL 76 HOUR ORDER BY m.dateofmatch ASC ";
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'matchid' => $row['matchid'],
                'hometeamid' => $row['homeid'],
                'awayteamid' => $row['awayid'],
                'homename' => $row['homename'],
                'awayname' => $row['awayname'],
                'leagueid' => $row['java_variable'],
                'timestamp' => $row['timestamp']
            );
        }
        return $data;
    }
    public function getMatchesOneWeekPL()
    {
        $q = "SELECT m.matchid, UNIX_TIMESTAMP(m.dateofmatch) * 1000 as timestamp, home.teamname as homename, away.teamname as awayname, l.java_variable, home.teamid as homeid, away.teamid as awayid
            FROM matchtable_pl m 
            JOIN teamtable_pl home on m.hometeamid = home.teamid 
            JOIN teamtable_pl away on m.awayteamid = away.teamid 
            JOIN leaguetable l ON l.leagueid = m.leagueid
            WHERE m.`result` LIKE '- : -' AND m.`dateofmatch` BETWEEN  NOW() AND NOW() + INTERVAL 76 HOUR ORDER BY m.dateofmatch ASC ";
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'matchid' => $row['matchid'],
                'hometeamid' => $row['homeid'],
                'awayteamid' => $row['awayid'],
                'homename' => $row['homename'],
                'awayname' => $row['awayname'],
                'leagueid' => $row['java_variable'],
                'timestamp' => $row['timestamp']
            );
        }
        return $data;
    }
    public function printScoringPercentagePL($homeid, $awayid, $year, $homename, $awayname){
        $homeTeamScoringFirstArray = DatabaseStats::getScoringPercentageHalfsPL($homeid, $year);
        $homeTeamScoringFirst = $homeTeamScoringFirstArray[0]['percentage_first'];
        $homeTeamScoringSecond= $homeTeamScoringFirstArray[0]['percentage_second'];
        
        $awayTeamScoringFirstArray = DatabaseStats::getScoringPercentageHalfsPL($awayid, $year);
        $awayTeamScoringFirst = $awayTeamScoringFirstArray[0]['percentage_first'];
        $awayTeamScoringSecond= $awayTeamScoringFirstArray[0]['percentage_second'];
        
        $homeTeamConcededFirstArray = DatabaseStats::getConcededPercentageHalfsPL($homeid, $year);
        $homeTeamConcededFirst = $homeTeamConcededFirstArray[0]['percentage_first'];
        $homeTeamConcededSecond= $homeTeamConcededFirstArray[0]['percentage_second'];
        
        $awayTeamConcededFirstArray = DatabaseStats::getConcededPercentageHalfsPL($awayid, $year);
        $awayTeamConcededFirst = $awayTeamConcededFirstArray[0]['percentage_first'];
        $awayTeamConcededSecond= $awayTeamConcededFirstArray[0]['percentage_second'];
        
        $scoringChanceHomeFirst = (($homeTeamScoringFirst + $awayTeamConcededFirst) / 2);
        $scoringChanceAwayFirst = (($awayTeamScoringFirst + $homeTeamConcededFirst) / 2);
        
        $scoringChanceHomeSecond = (($homeTeamScoringSecond + $awayTeamConcededSecond) / 2);
        $scoringChanceAwaySecond = (($awayTeamScoringSecond + $homeTeamConcededSecond) / 2);
        
        $scoringChanceFirst = (($scoringChanceHomeFirst + $scoringChanceAwayFirst) / 2);
        $scoringChanceSecond = (($scoringChanceHomeSecond + $scoringChanceAwaySecond) / 2);
        
        if($scoringChanceFirst > 60){
            echo $homename. ' - ' .$awayname .' : ';
            echo 'ScoringChanceFirst: ' . $scoringChanceFirst . '% </br>';
        }
        if($scoringChanceSecond > 60){
            echo $homename. ' - ' .$awayname . ': ';
            echo 'ScoringChanceSecond: ' . $scoringChanceSecond . '%</br>';
        }
    }
}