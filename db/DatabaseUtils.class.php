<?php
include "dbConnection.php";

//Methods that do not go under the other Database-categories.
class DatabaseUtils {
    
    public static $whitelist = array('localhost', '127.0.0.1', '46.9.149.168');
    
    public function getTransfers()
    {
        $q = "SELECT p.`playerid`,p.`playername`, fromteam.`teamid` AS fromteamid, fromteam.`teamname` AS fromteamname, toteam.`teamid` AS toteamid, toteam.`teamname` AS toteamname, SUBSTRING(datefound FROM 1 FOR 11) AS datefound
            FROM transfer t
            JOIN teamtable fromteam ON fromteam.`teamid` = t.`from_teamid`
            JOIN teamtable toteam ON toteam.`teamid` = t.`to_teamid`
            JOIN playertable p ON p.`playerid` = t.`playerid`
            GROUP BY p.`playerid`
            ORDER BY datefound DESC";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array (
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'fromteamid' => $row['fromteamid'],
                'fromteamname' => $row['fromteamname'],
                'toteamid' => $row['toteamid'],
                'toteamname' => $row['toteamname'],
                'datefound' => $row['datefound']
            );
        }
        return $data;
    }
    
    public function getSearchArray()
    {
        $q = "SELECT playername,playerid FROM playertable WHERE playerid != -1 GROUP BY playerid";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data['searcharray'][] = array(
                'label' => $row['playername'],
                'id' => $row['playerid'],
                'type' => 'player'
           );
        }
        
        $q = "SELECT teamname,teamid FROM teamtable WHERE teamid != -1 GROUP BY teamid ";
        
       
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data['searcharray'][] = array(
                'label' => $row['teamname'],
                'id' => $row['teamid'],
                'type' => 'team'
           );
        }
        return $data;
    }
    
    public function getLatestMatches($season)
    {
        $q = "SELECT m.`matchid`, home.`teamid` as homeid ,home.`teamname` as homename,away.`teamid` as awayid,away.`teamname` as awayname, m.`result`,UNIX_TIMESTAMP(m.`dateofmatch`) as timestamp, l.java_variable " .
            "FROM matchtable m  " .
            "JOIN leaguetable l ON l.`leagueid` = m.`leagueid` " .
            "JOIN teamtable home ON home.`teamid` = m.`hometeamid` " .
            "JOIN teamtable away ON away.`teamid` = m.`awayteamid` " .
            "WHERE m.`result` NOT LIKE '- : -' " .
            "AND l.year = {$season} " .
            "AND m.`dateofmatch` > NOW() - INTERVAL 7 DAY " .
            "ORDER BY m.`dateofmatch` DESC ";
            
        $data = array();
        $result = mysql_query($q);
        
        while($row = mysql_fetch_array($result))
        {
            $data[$row['java_variable']][] = array(
                'matchid' => $row['matchid'],
                'homeid' => $row['homeid'],
                'homename' => $row['homename'],
                'awayid' => $row['awayid'],
                'awayname' => $row['awayname'],
                'result' => $row['result'],
                'timestamp' => $row['timestamp']
            );
            $data['0'][] = array(
                'matchid' => $row['matchid'],
                'homeid' => $row['homeid'],
                'homename' => $row['homename'],
                'awayid' => $row['awayid'],
                'awayname' => $row['awayname'],
                'result' => $row['result'],
                'timestamp' => $row['timestamp']
            );
        }
        $data['8'] = DatabaseUtils::getLatestMatchesSecondDiv($season);
        $matchIds = array();
        foreach($data as $key => $val){
            foreach($val as $match){
                $matchIds[] = $match['matchid'];
            }
        }
        $data['scorers'] = DatabaseUtils::getGoalScoreresMatch($matchIds);
        return $data;
    }
    
    private function getLatestMatchesSecondDiv($season)
    {
        $q = "SELECT m.`matchid`, home.`teamid` as homeid ,home.`teamname` as homename,away.`teamid` as awayid,away.`teamname` as awayname, m.`result`,UNIX_TIMESTAMP(m.`dateofmatch`) as timestamp, l.java_variable " .
            "FROM matchtable m  " .
            "JOIN leaguetable l ON l.`leagueid` = m.`leagueid` " .
            "JOIN teamtable home ON home.`teamid` = m.`hometeamid` " .
            "JOIN teamtable away ON away.`teamid` = m.`awayteamid` " .
            "WHERE m.`result` NOT LIKE '- : -' " .
            "AND l.year = {$season} " .
            "AND l.java_variable IN (3,4,5,6) " .
            "ORDER BY m.`dateofmatch` DESC " .
            "LIMIT 15";
            
        
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
                'timestamp' => $row['timestamp']
            );
        }
        return $data;
    }
    
    public function setTeamHit($id)
    {
        if(!in_array($_SERVER['HTTP_HOST'], self::$whitelist) && !in_array($_SERVER['REMOTE_ADDR'], self::$whitelist)){
            $q = "UPDATE teamtable SET webpagehits=webpagehits+1, last_visit=NOW() WHERE teamid = $id";
            mysql_query($q);
            DatabaseUtils::setHit($id,'team');
        }
    }
    public function setPlayerHit($id)
    {
        if(!in_array($_SERVER['HTTP_HOST'], self::$whitelist) && !in_array($_SERVER['REMOTE_ADDR'], self::$whitelist)){           
            $q = "UPDATE playertable SET webpagehits=webpagehits+1, last_visit=NOW() WHERE playerid = $id";
            mysql_query($q);
            DatabaseUtils::setHit($id,'player');
        }
    }
    public function setLeagueHit($id)
    {
        if($id == '3,4,5,6'){
            $id = '8';
        }        
        DatabaseUtils::setHit($id,'league');
    }
    public function setPreviewHit($id)
    {
        DatabaseUtils::setHit($id,'preview');
    }
    public function setRefereeHit($id)
    {
        DatabaseUtils::setHit($id, 'referee');
    }
    public function setExternalMatchHit($id)
    {
        DatabaseUtils::setHit($id,'match');
    }
    public function setInternalMatchHit($id)
    {
        DatabaseUtils::setHit($id,'match_internal');
    }
    public function setPlayerHitFrom($id,$from)
    {
        DatabaseUtils::setHit($id,$from);
    }
    public function setTeamSearchHit($id)
    {
        DatabaseUtils::setHit($id,'team_search');
    }
    public function setEventPageHit($id)
    {
        if($id == '4,8'){
            $id = 10;
        }
        DatabaseUtils::setHit($id,'eventtotal');
    }
    public function setEventPageTeamHit($id)
    {
        if($id == '4,8'){
            $id = 10;
        }
        DatabaseUtils::setHit($id,'eventtotal_team');
    }
    public function setHit($id,$type)
    {
        if(!in_array($_SERVER['HTTP_HOST'], self::$whitelist) && !in_array($_SERVER['REMOTE_ADDR'], self::$whitelist) && $id != 0){
            $q = "INSERT INTO clicktable (clicktype,clicked_id,ip) VALUES ('$type',$id,'".$_SERVER['REMOTE_ADDR']."')";
            mysql_query($q);
        }
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
    public function getTrending()
    {
        $q = "SELECT
            trending.clicked_id,
            trending.clicktype,
            p.`playername` AS name1,
            p1.`playername` AS name2,
            t.`teamname`,
            m.`matchid`,
            home.`teamname` AS hometeam,
            away.`teamname` AS awayteam 
            FROM
            (SELECT 
            c.clicked_id,
            c.clicktype,
            c.ip,
            count(*) as antall
            FROM
            clicktable c 
            WHERE c.time > NOW() - INTERVAL 24 HOUR  
            AND c.`clicktype` IN ('player','team','preview','match_internal')
            GROUP BY clicktype,
            clicked_id,
            ip 
            ORDER BY TIME DESC) AS trending 
            LEFT JOIN playertable p ON p.`playerid` = trending.clicked_id AND p.year = 2012
            LEFT JOIN playertable p1 ON p1.`playerid` = trending.clicked_id AND p1.year = 2013
            LEFT JOIN teamtable t ON t.`teamid` = trending.clicked_id
            LEFT JOIN matchtable m ON m.`matchid` = trending.clicked_id
            LEFT JOIN teamtable home ON m.`hometeamid` = home.`teamid`
            LEFT JOIN teamtable away ON m.`awayteamid` = away.`teamid`
            GROUP BY trending.clicked_id,
            trending.clicktype 
            ORDER BY trending.antall DESC 
            LIMIT 10";
        
        $data = array();
       
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            if($row['clicktype'] == 'player'){
                if(isset($row['name1']) && !empty($row['name1'])){
                    $playername = $row['name1'];
                }else{
                    $playername = $row['name2'];
                }
                
                $data[] = array(
                    'type' => $row['clicktype'],
                    'playerid' => $row['clicked_id'],
                    'playername' => $playername
                );
            }else if($row['clicktype']=='team'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'teamid' => $row['clicked_id'],
                    'teamname' => $row['teamname']
                );
            }else if($row['clicktype']=='preview' || $row['clicktype']=='match_internal'){
                $data[] = array(
                    'type' => $row['clicktype'],
                    'matchid' => $row['clicked_id'],
                    'hometeam' => $row['hometeam'],
                    'awayteam' => $row['awayteam']
                );
            }
            
            
        }
        return $data;
    }    
        
    public function getEventInfoTotalJSON($eventtype, $limit, $season, $leagueid)
    {
        if($leagueid == '8'){
            $leagueid = '3,4,5,6';
        }
        // Clean sheet hack
        if($eventtype == 12){
            return self::getCleanSheetsPlayer($season,$leagueid);
        }
        
        $q = 
        "SELECT t.playerid,t.playername,tt.teamid,tt.teamname,COUNT(*) AS `event count`, eventtype FROM eventtable e " .
        "JOIN playertable t ON t.playerid = e.playerid AND e.teamid = t.teamid AND t.year = " . $season . " " .
        "JOIN teamtable tt ON tt.teamid = t.teamid " .
        "JOIN matchtable m ON e.matchid = m.matchid  ".
        "JOIN leaguetable l ON l.leagueid = e.leagueid " .         
        "WHERE e.eventtype IN ( ".$eventtype . " )" .
        "AND e.playerid != -1 ".
        "AND l.year = "  . $season .  " "  .
        ($leagueid == 0 ? '' : ' AND l.java_variable IN ('.$leagueid.') ') .
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
    public function getTotalPlayerminutes($season,$limit, $leagueid)
    {
        if($leagueid == 8){
            $leagueid = '3,4,5,6';
        }
        
        $q = "SELECT pp.playerid,pp.playername,t.teamname,t.teamid,SUM(p.minutesplayed) AS `minutes played` FROM playtable p " . 
        "JOIN matchtable m ON m.matchid = p.matchid " .
        "JOIN playertable pp ON pp.playerid = p.playerid AND p.teamid = pp.teamid AND pp.year = ". $season . " " . 
        "JOIN teamtable t ON t.teamid = pp.teamid ".
        "JOIN leaguetable l ON m.leagueid = l.leagueid " .
        "WHERE pp.playerid != -1 " .
        "AND l.year = pp.year AND p.ignore = 0 " .
        ($leagueid == '0' ? '' : ' AND l.java_variable IN ('.$leagueid.' ) ')      .  
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
    
    public function getSuspList($leagueid)
    {
        $year = 2013;
        $q = "SELECT * FROM eventtable e
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
        $threeYellow = array();
        $fiveYellow = array();
        $sevenYellow = array();
        $moreYellow = array();
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
                
                if(count($data[$row['playerid']]['matchid']) == 3){
                    $threeYellow[$row['playerid']]['matchid'] = $row['matchid'];
                    $threeYellow[$row['playerid']]['leagueid'] = $row['leagueid'];
                }
                if(count($data[$row['playerid']]['matchid']) == 5){
                    $fiveYellow[$row['playerid']]['matchid'] = $row['matchid'];
                    $fiveYellow[$row['playerid']]['leagueid'] = $row['leagueid'];
                }
                if(count($data[$row['playerid']]['matchid']) == 7){
                    $sevenYellow[$row['playerid']]['matchid'] = $row['matchid'];
                    $sevenYellow[$row['playerid']]['leagueid'] = $row['leagueid'];
                }
                if(count($data[$row['playerid']]['matchid']) >= 8){
                    $moreYellow[$row['playerid']]['matchid'] = $row['matchid'];
                    $moreYellow[$row['playerid']]['leagueid'] = $row['leagueid'];
                    $moreYellow[$row['playerid']]['count'] = count($data[$row['playerid']]['matchid']);
                }
            }
        }
        
        
        //Sent off
        $q = "SELECT * FROM eventtable e
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
            
            $q = "SELECT SUBSTRING(m.dateofmatch FROM 1 FOR 16) as date,m.*,home.`teamname` AS homename, away.`teamname` AS awayname,p.*, unix_timestamp(m.dateofmatch) as timestamp 
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
                        'eventtype' => $event,
                        'timestamp' => $row['timestamp']
                    );
                }
            }
        }
        return array (
            'threeYellow' => DatabaseUtils::getSuspendedFromArray($threeYellow,$year),
            'fiveYellow' => DatabaseUtils::getSuspendedFromArray($fiveYellow,$year),
            'sevenYellow' => DatabaseUtils::getSuspendedFromArray($sevenYellow,$year),
            'moreYellow' => DatabaseUtils::getSuspendedFromArray($moreYellow,$year),
            'redCard' => $redCardSuspended
        );
    }
    
    public function getSuspendedFromArray(array $suspendedArray,$year)
    {
        $retVal = array();
        foreach($suspendedArray as $key => $value){
            $count = 0;
            if(isset($value['count'])){
                $count =  $value['count'];
            }
            $leagueid = $value['leagueid'];
            $value = $value['matchid'];
            
            $q = "SELECT SUBSTRING(m.dateofmatch FROM 1 FOR 16) as date,m.*,home.`teamname` AS homename, away.`teamname` AS awayname,p.*, unix_timestamp(m.dateofmatch) as timestamp 
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
                if(!$row['matchid'] == null && strtotime($row['dateofmatch']) > strtotime('now')){
                    $retVal[] = array(
                        'matchid' => $row['matchid'],
                        'dateofmatch' => $row['date'],
                        'homename' => $row['homename'],
                        'awayname' => $row['awayname'],
                        'hometeamid' => $row['hometeamid'],
                        'awayteamid' => $row['awayteamid'],
                        'playerid' => $row['playerid'],
                        'playername' => $row['playername'],
                        'teamid' => $row['teamid'], 
                        'count' => $count,
                        'timestamp' => $row['timestamp']
                    );
                }
            }
        }
        return $retVal;
    }
    
    public function getGoalScoreresMatch(array $matchid)
    {
        $matchids = implode($matchid,',');
        $q = "SELECT 
        e.matchid, p.`playername`,p.`playerid`,e.`eventtype`, e.`minute`,e.teamid
        FROM
        eventtable e 
        JOIN playertable p 
            ON p.`playerid` = e.`playerid` 
            AND e.`leagueid` = p.`leagueid`
        WHERE e.`matchid` IN ($matchids)
        AND e.`eventtype` IN (4, 8, 9)
        AND e.ignore = 0 
        GROUP BY e.`playerid`,e.`minute`
        ORDER BY e.minute ASC";
        
        //echo $q;
       
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[$row['matchid']][] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'minute' => $row['minute'],
                'eventtype' => $row['eventtype'],
                'teamid' => $row['teamid']
            );
        }
        return $data;
    }
    
    public function getRefereeStats($year)
    {
        $q = "
       SELECT 
        t.`refereeid`,
        t.`refereename`,
        SUM(IF(e.`eventtype` = 2, 1, 0)) AS `yellow`,
        SUM(IF(e.`eventtype` = 3, 1, IF(e.`eventtype` = 1,1,0))) AS `red`
        FROM
        eventtable e 
        JOIN matchtable m 
            ON e.`matchid` = m.`matchid` 
        JOIN refereetable t 
            ON t.`refereeid` = m.`refereeid` 
            WHERE t.refereeid != -1
            AND e.ignore = 0 
        GROUP BY t.`refereeid` ";
        
         $data = array();
       
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[$row['refereeid']] = array(
                'refereename' => $row['refereename'],
                'refereeid' => $row['refereeid'],
                'yellow' => $row['yellow'],
                'red' => $row['red']
            );
        }

        $q2 = "SELECT COUNT(*) AS matches,r.`refereeid` FROM matchtable r WHERE refereeid IS NOT NULL  AND r.`result` NOT LIKE '- : -' AND refereeid != -1 AND refereeid != 0 GROUP BY r.`refereeid`;";
        $result = mysql_query($q2);
        while($row = mysql_fetch_array($result))
        {
            $data[$row['refereeid']]['matches'] = $row['matches'];
            $data[$row['refereeid']]['yellowpr'] = number_format($data[$row['refereeid']]['yellow'] / $row['matches'], 2);
            $data[$row['refereeid']]['redpr'] = number_format($data[$row['refereeid']]['red'] / $row['matches'], 2);
        }
        
        $q3 = "SELECT 
        matchid, m.`refereeid`,r.refereename, home.`teamname` as hometeam, away.teamname as awayteam, SUBSTRING(date FROM 1 FOR 16) as date
        FROM
        (SELECT 
            MIN(r.dateofmatch) AS `date`,
            r.`refereeid` 
        FROM
            matchtable r 
        WHERE refereeid IS NOT NULL 
            AND refereeid != - 1 
            AND refereeid != 0 
            AND r.`result` LIKE '- : -' 
        GROUP BY r.`refereeid`) AS `first` 
        JOIN matchtable m 
            ON m.`dateofmatch` = first.date 
            AND m.`refereeid` = first.refereeid 
            
            JOIN teamtable home ON m.`hometeamid` = home.`teamid`
            JOIN teamtable away ON m.`awayteamid` = away.`teamid`
            JOIN refereetable r ON r.refereeid = m.refereeid ";
        
        $result = mysql_query($q3);
        while($row = mysql_fetch_array($result))
        {
            if(isset($data[$row['refereeid']])){
                $data[$row['refereeid']]['nextmatch'] = $row['matchid'];
                $data[$row['refereeid']]['dateofmatch'] = $row['date'];
                $data[$row['refereeid']]['hometeam'] = $row['hometeam'];
                $data[$row['refereeid']]['awayteam'] = $row['awayteam'];
            }
           
        }
        //var_dump($data);
        return $data;
    }
    public function getRefereeId($refereeid)
    {
        $q = "SELECT 
        t.`refereeid`,
        t.`refereename`,
        m.*,
        h.teamname as homename,
        a.teamname as awayname,
        SUM(IF(e.`eventtype` = 2, 1, 0)) AS `yellow`,
        SUM(IF(e.`eventtype` = 3, 1, IF(e.`eventtype` = 1,1,0))) AS `red`
        FROM
        eventtable e 

        JOIN matchtable m 
            ON e.`matchid` = m.`matchid`
        JOIN teamtable h 
            ON h.teamid = m.hometeamid
        JOIN teamtable a 
            ON a.teamid = m.awayteamid
        JOIN refereetable t 
            ON t.`refereeid` = m.`refereeid` 
            WHERE t.refereeid = {$refereeid}
            AND e.ignore = 0
        GROUP BY m.`matchid` ORDER BY m.dateofmatch DESC";
            
           // echo $q;
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'yellow' => $row['yellow'],
                'red' => $row['red'],
                'matchid' => $row['matchid'],
                'dateofmatch' => $row['dateofmatch'],
                'homename' => $row['homename'],
                'homeid' => $row['hometeamid'],
                'awayid' => $row['awayteamid'],
                'awayname' => $row['awayname'],
                'result' => $row['result'],
                'refereename' => $row['refereename']
            );
        }
        return $data;
    }

    public function getEventCountMatch($eventtype,$matchid)
    {
        $q = "SELECT 
                COUNT(*) AS s 
            FROM
                eventtable e 
                WHERE e.`matchid` = {$matchid}
                AND e.`eventtype` IN ($eventtype) 
                AND e.`ignore` = 0";
        
        $total = 0;
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $total = $row['s'];
        }
        return $total;
    }    
    
    public function getCleanSheetsPlayer($season, $leagueid){
        
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
    AND l.`year` = {$season}  ".
    ($leagueid == 0 ? "" : " AND l.java_variable IN ( {$leagueid} )") .
    "GROUP BY p.`playerid` ORDER BY COUNT(*) Desc";
  
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
    
    public function getCleanSheetsTeam($season, $leagueid){
        //TODO::: This sql is baaaaaad
        $q= "SELECT home.teamid as teamid1, SUM(IF(home.c_home IS NULL,0,home.c_home) + IF(away.c_away IS NULL,0,away.c_away)) AS total_c, home.java_variable, t.teamname FROM 
            (SELECT 
            m.`hometeamid` AS teamid,
            COUNT(*) AS c_home,
            l.`java_variable`
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON m.leagueid = l.leagueid 
            WHERE l.year = {$season} 
            AND m.`result` NOT REGEXP '- : -|(Utsatt)' 
            AND m.awayscore = 0 
            GROUP BY m.`hometeamid` ) AS home
            left JOIN 
            (SELECT 
            m.awayteamid AS teamid,
            COUNT(*) AS c_away,
            l.`java_variable`
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON m.leagueid = l.leagueid 
            WHERE l.year = {$season}  
            AND m.`result` NOT REGEXP '- : -|(Utsatt)' 
            AND m.homescore = 0 
            GROUP BY m.`awayteamid`) AS away ON home.teamid = away.teamid JOIN teamtable t on home.teamid = t.teamid  ".
            ($leagueid == 0 ? "" : " WHERE home.java_variable IN ({$leagueid}) ") . " GROUP BY home.teamid order by total_c desc" ;
            
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
    public function getCleanSheetRank($teamid,$season) {
        
        //This sql is prob. so bad that it hurts
        mysql_query("SET @rownum = 0, @rank = 1, @prev_val = NULL;");
        
        $q="SELECT 
                rank,
            `event count` 
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
        (SELECT 
        home.teamid as teamid,
        SUM(IF(home.c_home IS NULL,0,home.c_home) + IF(away.c_away IS NULL,0,away.c_away)) AS `event count`,
        home.java_variable,
        t.teamname 
        FROM
        (SELECT 
            m.`hometeamid` AS teamid,
            COUNT(*) AS c_home,
            l.`java_variable` 
        FROM
            matchtable m 
            JOIN leaguetable l 
            ON m.leagueid = l.leagueid 
        WHERE l.year = {$season} 
            AND m.`result` NOT REGEXP '- : -|(Utsatt)' 
            AND m.awayscore = 0 
        GROUP BY m.`hometeamid`) AS home 
        left JOIN 
            (SELECT 
            m.awayteamid AS teamid,
            COUNT(*) AS c_away,
            l.`java_variable` 
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON m.leagueid = l.leagueid 
            WHERE l.year = {$season} 
            AND m.`result` NOT REGEXP '- : -|(Utsatt)' 
            AND m.homescore = 0 
            GROUP BY m.`awayteamid`) AS away 
            ON home.teamid = away.teamid 
        JOIN teamtable t 
            on home.teamid = t.teamid 
        GROUP BY home.teamid  ORDER BY `event count` desc) as t,
        (SELECT @rownum := 0) r) AS showRank 
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
    public function getCleanSheetPlayerRank($playerid,$season){
        
        //This sql is prob. so bad that it hurts
        mysql_query("SET @rownum = 0, @rank = 1, @prev_val = NULL;");
        
        $q = "SELECT rank,
            `event count` 
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
            FROM (SELECT 
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
            (
            m.hometeamid = p.`teamid` 
            AND m.awayscore = 0
            ) 
            OR (
            m.awayteamid = p.`teamid` 
            AND m.homescore = 0
            )
        ) 
        AND l.`year` = {$season} 
        GROUP BY p.`playerid`  ORDER BY `event count` desc) AS t,
        (SELECT @rownum := 0) r) AS showRank 
            WHERE playerid = {$playerid}";
            
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
    public function getMatch($matchid)
    {
        DatabaseUtils::setInternalMatchHit($matchid);
        
        $q = "SELECT e.*,p.`playername`,m.teamwonid, m.homescore,m.awayscore, home.`teamname` AS homename, away.`teamname` AS awayname,home.`teamid` AS homeid, away.`teamid` AS awayid
        FROM eventtable e 
        JOIN leaguetable l ON l.`leagueid` = e.`leagueid`
        JOIN matchtable m ON m.`matchid` = e.`matchid`
        JOIN teamtable home ON home.`teamid` = m.`hometeamid`
        JOIN teamtable away ON away.`teamid` = m.`awayteamid`
        JOIN playertable p ON p.`playerid` = e.`playerid` AND p.`year` = l.`year` AND p.`teamid` = e.`teamid`
        WHERE e.`matchid` = $matchid
        AND e.`ignore` = 0
        ORDER BY e.`minute` ASC, e.`eventid` ASC";
            
        $data = array();   
        $result = mysql_query($q);
        $homeId = 0;
        $awayId = 0;
        
        $playerInId = 0;
        $playerInName = '';
        $playerOutId = 0;
        $playerOutName = '';
        $teamWonId = -1;
        
        while($row = mysql_fetch_array($result))
        {
            $teamWonId = $row['teamwonid'];
            $event = $row['eventtype'];
            $homeId = $row['homeid'];
            $awayId = $row['awayid'];
            
            if($event == 6){
                $playerInId = $row['playerid'];
                $playerInName = $row['playername'];
            }else if($event == 7){
                $playerOutId = $row['playerid'];
                $playerOutName = $row['playername'];
            }
            
            if(($event == 7 || $event == 6) && $playerInId != 0 && $playerOutId != 0){
                $data['events'][] = array(
                    'matchid' => $row['matchid'],
                    'homescore' => $row['homescore'],
                    'awayscore' => $row['awayscore'],
                    'teamid' => $row['teamid'],
                    'homeid' => $row['homeid'],
                    'homename' => $row['homename'],
                    'awayid' => $row['awayid'],
                    'awayname' => $row['awayname'],
                    'playerid' => $row['playerid'],
                    'playername' => $row['playername'],
                    'eventtype' => $row['eventtype'],
                    'minute' => $row['minute'],
                    'playerinid' => $playerInId,
                    'playerinname' => $playerInName,
                    'playeroutid' => $playerOutId,
                    'playeroutname' => $playerOutName
                );
              
                $playerInId = 0;
                $playerInName = '';
                $playerOutId = 0;
                $playerOutName = '';
            
            }else if($event != 6 && $event != 7){
                
                
                $data['events'][] = array(
                    'matchid' => $row['matchid'],
                    'homescore' => $row['homescore'],
                    'awayscore' => $row['awayscore'],
                    'teamid' => $row['teamid'],
                    'homeid' => $row['homeid'],
                    'homename' => $row['homename'],
                    'awayid' => $row['awayid'],
                    'awayname' => $row['awayname'],
                    'playerid' => $row['playerid'],
                    'playername' => $row['playername'],
                    'eventtype' => $row['eventtype'],
                    'minute' => $row['minute']
                );
            }
        }
        $type = 'away';
        if($teamWonId == $data['events'][0]['homeid']){
            $type = 'home';
        }
        $data['homelineup'] = DatabaseTeam::getLineup($homeId, 2013, $matchid);
        $data['awaylineup'] = DatabaseTeam::getLineup($awayId, 2013, $matchid);
        $data['streak'] = DatabaseTeam::getStreakString($teamWonId,$matchid,$type);
        $data['homerealteamid'] = DatabaseTeam::getSecondTeamId($homeId);
        $data['awayrealteamid'] = DatabaseTeam::getSecondTeamId($awayId);
        return $data;
    }
}