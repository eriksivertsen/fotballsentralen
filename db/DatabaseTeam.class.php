<?php
include "dbConnection.php";

class DatabaseTeam {
    
    public function getTeamInfo($teamid,$season,$from)
    {
        if(isset($from) && !empty($from)){
            DatabaseUtils::setTeamSearchHit($teamid,$season);
        }else{
            DatabaseUtils::setTeamHit($teamid,$season);
        }
        
        if($season == 0){
            $season = Constant::ALL_STRING;
        }
        $allmatches = self::getAllMatches($teamid,'both', $season);
        $matchids = array();
        foreach($allmatches as $array){
            $matchids [] = $array['matchid'];
        }
        $goalscorers = DatabaseUtils::getGoalScoreresMatch($matchids);
        $teamtoleague = self::getTeamToLeague($teamid,$season);
        $latestMatches = self::getLatestMatches($teamid,'both');
        if(isset($latestMatches[0]['matchid'])){
            $lastLineup = self::getLineup($teamid, $season, $latestMatches[0]['matchid']);
        }else{
            $lastLineup = array();
        }
        
        if(empty($teamtoleague)){
            return array('teamtoleague' => array());
        }
          
        $events = array (
            'teamtoleague' => $teamtoleague,
            'yellow' => self::getEventRankTeam($teamid,2,$season),
            'yellowred' => self::getEventRankTeam($teamid,1,$season),
            'red' => self::getEventRankTeam($teamid,3,$season),
            'goal' => self::getEventRankTeam($teamid,4,$season),
            'subin' => self::getEventRankTeam($teamid,6,$season),
            'subout' => self::getEventRankTeam($teamid,7,$season),
            'penalty' => self::getEventRankTeam($teamid,8,$season),
            'owngoal' => self::getEventRankTeam($teamid,9,$season),
            'cleansheetrank' => DatabaseUtils::getCleanSheetRank($teamid,$season),
            'teamplayer' => self::getTeamPlayerJSON($teamid ,$season),
            'scoringminute' => self::getGoalsScoringMinute($teamid,$season),
            'concededminute' => self::getGoalsConcededMinute($teamid,$season),
            'topscorer' => self::getMostEvents($teamid, $season,'4,8'),
            'topscorercount' => self::getTopscorerCount($teamid,0,$season),
            'mostminutes' => self::getMostMinutes($teamid, $season),
            //'winstreak' => $this->getBestWinStreak($teamid, $season),
            'cleansheets' => self::getCleanSheets($teamid, $season),
            'overgoals' => self::getOverGoals($teamid, $season),
            'overgoalshome' => self::getOverGoals($teamid, $season,'home'),
            'overgoalsaway' => self::getOverGoals($teamid, $season,'away'),
            'mostyellow' => self::getMostEvents($teamid, $season,2),
            'mostred' => self::getMostEvents($teamid, $season,'1,3'),
            'nextmatches' => self::getNextMatches($teamid,'both'),
            'latestmatches' => $latestMatches,
            'latestmatcheshome' =>self::getLatestMatches($teamid,'home'),
            'latestmatchesaway' => self::getLatestMatches($teamid,'away'),
            'homestats' => DatabaseLeague::getHomestats($teamid, $season),
            'awaystats' => DatabaseLeague::getAwaystats($teamid, $season),
            'allmatches' => $allmatches,
//            'homestreak' => self::getStreakString($teamid,'home'),
//            'awaystreak' => self::getStreakString($teamid,'away'),
            'goalscorers' => $goalscorers,
            'attendance' => self::getAttendance($teamid,$season),
            'currentposition' => self::getLeaguePosition($teamid, $teamtoleague[0]['leagueid'], $season),
            'currentpositionhome' => self::getLeaguePosition($teamid, $teamtoleague[0]['leagueid'], $season,'home'),
            'currentpositionaway' => self::getLeaguePosition($teamid, $teamtoleague[0]['leagueid'], $season, 'away'),
            'mostusedplayers' => self::getMostUsedLineup($teamid, $season),
            'lastlineup' => $lastLineup,
            'last5lineup' => self::getLastFiveLineups($teamid,$season,$latestMatches),
            'leaguetable' => DatabaseLeague::getLeagueTable($season, $teamtoleague[0]['leagueid'],0),
            'realteamid' => self::getSecondTeamId($teamid),
            'scoringpercentagehalfs' => self::getScoringPercentageHalfs($teamid,$season),
            'scoringhomeaway' => self::getPercentageHomeAway($teamid,$season,'scoring'),
            'concededhomeaway' => self::getPercentageHomeAway($teamid,$season,'conceded'),
            'concededpercentagehalfs' => self::getConcededPercentageHalfs($teamid,$season),
            'dangerlist' => DatabaseUtils::getDangerListTeam($teamid,$season),
            'winpercentage' => self::getWinPercentage($teamid,$season),
//            'pointmonth' => DatabaseTeam::getMontlyPoints($teamid,$season)
         );
        return $events;
    }
    public function getConcededPercentageHalfs($teamid,$season)
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
            matchtable m 
            JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
            LEFT JOIN eventtable e ON e.`matchid` = m.`matchid` AND e.`eventtype` IN (4,8)
            LEFT JOIN eventtable own ON own.`matchid` = m.`matchid` AND own.`eventtype` = 9 AND own.`minute` >= 45
            WHERE 
            (m.`hometeamid` = {$teamid} OR m.`awayteamid` = {$teamid}) 
            AND l.`year` IN ( {$season} )
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
            matchtable m 
            JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
            LEFT JOIN eventtable e ON e.`matchid` = m.`matchid` AND e.`eventtype` IN (4,8)
            LEFT JOIN eventtable own ON own.`matchid` = m.`matchid` AND own.`eventtype` = 9 AND own.minute < 45
            WHERE 
            (m.`hometeamid` = {$teamid} OR m.`awayteamid` = {$teamid}) 
            AND l.`year` IN ({$season})
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
    public function getScoringPercentageHalfs($teamid,$season)
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
                eventtable e 
                JOIN leaguetable l 
                ON e.leagueid = l.leagueid 
            WHERE e.minute <= 45 
                AND l.year IN ({$season})
                AND e.eventtype IN (4, 8) 
                AND e.ignore = 0
                AND e.teamid = {$teamid}
            GROUP BY e.teamid) AS `first`
            JOIN 
                (SELECT 
                e.teamid,
                COUNT(*)  AS second_half 
                FROM
                eventtable e 
                JOIN leaguetable l 
                    ON e.leagueid = l.leagueid 
                WHERE e.minute >= 45 
                AND l.year IN ({$season})
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
    
    public function getPercentageHomeAway($teamid,$season,$type)
    {
        
        if($type == 'scoring'){
            $string1 = '!=';
            $string2 = '=';
        }else if($type == 'conceded'){
            $string1 = '=';
            $string2 = '!=';
        }else{
            echo 'not supported getPercentageAway('.$type.')';
            return array();
        }
        $q = "SELECT 
            home.teamid, 
            ROUND((home.scored / (home.scored + away.scored)) * 100,2) AS scoredHome, 
            ROUND((away.scored / (home.scored + away.scored)) * 100,2) AS scoredAway,
            home.scored AS scored_home_total,
            away.scored AS scored_away_total,
            (home.scored + away.scored) AS total_scored
            FROM (
            SELECT 
            m.hometeamid AS teamid,
            SUM(IF(e.eventtype = 9, IF(e.teamid {$string1} m.hometeamid,1,0), IF(e.teamid {$string2} m.hometeamid,1,0))) AS scored
            FROM
            eventtable e 
            JOIN matchtable m 
                ON m.matchid = e.matchid 
            JOIN leaguetable l 
                ON l.leagueid = e.leagueid 
            WHERE e.eventtype IN (4, 8, 9) 
            AND l.year IN ( {$season} )
            AND e.ignore = 0
            AND m.hometeamid = {$teamid}
            GROUP BY m.hometeamid ) AS home JOIN 
            (SELECT 
            m.awayteamid AS teamid,
            SUM(IF(e.eventtype = 9, IF(e.teamid {$string1} m.awayteamid,1,0), IF(e.teamid {$string2} m.awayteamid,1,0))) AS scored
            FROM
            eventtable e 
            JOIN matchtable m 
                ON m.matchid = e.matchid 
            JOIN leaguetable l 
                ON l.leagueid = e.leagueid 
            WHERE e.eventtype IN (4, 8, 9) 
            AND l.year IN ( {$season} )
            AND m.awayteamid = {$teamid}
            AND e.ignore = 0
            GROUP BY m.awayteamid ) AS away ON home.teamid = away.teamid";
            
        $result = mysql_query($q);
        $data = array();
        
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'teamid' => $row['teamid'],
                'percentage_home' => $row['scoredHome'],
                'percentage_away' => $row['scoredAway'],
                'total' => $row['total_scored'],
                'total_home' => $row['scored_home_total'],
                'total_away' => $row['scored_away_total']
            );
        }
        return $data; 
    }
    
    public function getLeaguePosition($teamid,$leagueid,$season,$type = 'all')
    {
        if ($type == 'all') {
            $leaguetable = DatabaseLeague::getLeagueTable($season, $leagueid,0);
        } elseif ($type == 'home') {
            $leaguetable = DatabaseLeague::getLeagueTableHome($leagueid, $season,0);
        } elseif ($type == 'away') {
            $leaguetable = DatabaseLeague::getLeagueTableAway($leagueid, $season,0);
        }
        foreach($leaguetable as $position => $array){
            if($array['teamid'] == $teamid){
                return ($position +1);
            }
        }
    }
    
    public function getAttendance($teamid,$season)
    {
        $q = "SELECT AVG(attendance) AS average, MAX(attendance) AS `max` FROM matchtable m JOIN leaguetable l ON l.`leagueid` = m.leagueid WHERE l.`year` IN ( {$season} ) AND m.`hometeamid` = {$teamid} AND m.result NOT LIKE '- : -' and m.attendance > 0 ";
          
        $result = mysql_query($q);
        $value = array();
        
        while($row = mysql_fetch_array($result))
        {
            $value[] = array(
                'average' => number_format($row['average'],0,'.',''),
                'max' => number_format($row['max'],0,'.','')
            );
        }
        return $value;
    }
    
    public function getTopscorerCount($teamid,$leagueid,$season)
    {
        $topscorer = DatabaseUtils::getEventInfoTotalJSON('4,8',3,$season,$leagueid);
        if(!isset($topscorer[0])){
            return 0;
        }
        $topscorerCount = $topscorer[0]['eventcount'];
       
        $q = "SELECT playerid,COUNT(*) as topscorer FROM eventtable e " .
        "LEFT JOIN leaguetable l ON e.leagueid = l.`leagueid` " . 
        "WHERE eventtype IN (4,8) ".
        "AND l.`year` IN ( {$season} ) AND e.ignore = 0 " .
        ($leagueid == '0' ? '' : ' AND l.`java_variable` IN ('.$leagueid.') ') .
        ($teamid == '0' ? '' : ' AND e.teamid  IN ('.$teamid.') ') .
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
    public function getStreakString($teamid,$matchid,$type = '')
    {
        $where = "m.`hometeamid` = $teamid OR m.`awayteamid` = $teamid";
        $typestring = '';
        
        if($type == 'home'){
            $where = "m.`hometeamid` = $teamid";
            $typestring = 'hjemme';
        }else if($type == 'away'){
            $where = "m.`awayteamid` = $teamid";
            $typestring = 'borte';
        }
        $q = "SELECT m.*, home.teamname as homename, away.teamname as awayname FROM matchtable m JOIN teamtable home on home.teamid = m.hometeamid JOIN teamtable away ON away.teamid = m.awayteamid 
        WHERE ($where) AND m.`result` NOT LIKE '- : -' ORDER BY m.`dateofmatch` ASC";
        
        $wins = 0;
        $loss = 0;
        $draws = 0;
        $withoutloss = 0;
        $withoutwin = 0;
        $withoutdraws = 0;
        $lossMatchId = 0;
        $winMatchId = 0;
        
        $wonName = '';
        $lossName = '';
        
        $string = '';
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $winId = $row['teamwonid'];
            if($winId == $teamid){
                $wins++;
                $withoutloss++;
                $withoutdraws++;
                $withoutwin = 0;
                $loss = 0;
                $draws = 0;
                if($row['matchid'] != $matchid){
                    $winMatchId = $row['matchid'];
                }
            }else if ($winId == 0){
                $draws++;
                $wins = 0;
                $loss = 0;
                $withoutloss++;
                $withoutwin++;
                $withoutdraws = 0;
            }else{
                $loss++;
                $wins = 0;
                $draws = 0;
                $withoutloss = 0;
                $withoutdraws++;
                $withoutwin++;
                if($row['matchid'] != $matchid){
                    $lossMatchId = $row['matchid'];
                }
            }
            
            if($row['matchid'] == $matchid){
                $lossName = $row['awayname'];
                $wonName = $row['homename'];

                if($row['teamwonid'] == $row['awayteamid']){
                    $lossName = $row['homename'];
                    $wonName = $row['awayname'];
                }    
                break;
            }
        }
        if($withoutloss > $withoutwin){
            //positive
            if($wins >= $withoutloss){
                if($wins == 1){
                    $string = 'Med seieren over '.$lossName.' tok ' .$wonName.' sin første seier på '.$typestring. 'bane siden ' . self::getMatchInfo($winMatchId,$winId);
                }else{
                    $string = 'Med seieren over '.$lossName.' tok ' .$wonName.' sin ' . $wins . ' seier på rad '.$typestring. '.';
                }
            }else{
                $string = $wonName . ' ikke tapt på ' . $withoutloss . ' ' .($typestring != '' ? $typestring : '' ). 'kamper';
                if($wins >= $draws){
                    if($wins == 1){
                        $string .= ', og tok sin første seier siden ' . self::getMatchInfo($winMatchId,$winId);
                    }else{
                        $string .= ', og har ' .$wins. ' seire på rad. Sist tap kom mot ' . self::getMatchInfo($lossMatchId,$winId);
                    }
                }
            }
        }
        return $string;
    }
    
    public function getMatchInfo($matchid,$teamid)
    {
        $q = "SELECT m.*, home.teamname as homename, away.teamname as awayname FROM matchtable m JOIN teamtable home on home.teamid = m.hometeamid JOIN teamtable away ON away.teamid = m.awayteamid 
        WHERE m.matchid = $matchid AND m.`result` NOT LIKE '- : -' ORDER BY m.`dateofmatch` ASC LIMIT 1";
    
        $string = '';
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
                $string = $row['awayname'] . ' ('.$row['homescore']. '-' .$row['awayscore'] . ')';
        }
        return $string;
    }
    
    public function getLineup($teamid,$season,$matchid)
    {
        $q = "SELECT pt.playerid, pt.shirtnumber,pt.is_goalkeeper, SUBSTRING_INDEX(pt.`playername`,' ',-1) AS lastname,pt.playername as fullname, pta.`position` as apos ,ptn.`position` as npos, 
            t1.teamname as t1name,t2.teamname as t2name,t1.teamid as t1id ,t2.teamid as t2id
            FROM playtable p 
            JOIN matchtable m ON m.`matchid` = p.`matchid`
            JOIN teamtable t1 ON t1.`teamid` = m.hometeamid
            JOIN teamtable t2 ON t2.`teamid` = m.awayteamid
            JOIN leaguetable l ON m.`leagueid` = l.`leagueid`
            JOIN playertable pt ON p.`playerid` = pt.`playerid` AND p.`teamid` = pt.`teamid` AND pt.`year` = l.`year` 
            LEFT JOIN playertable_altom pta ON pta.`playerid` = pt.`playerid_altom`
            LEFT JOIN playertable_nifs ptn ON ptn.`playerid` = pt.`playerid_nifs`
            WHERE p.`teamid` = $teamid
            AND l.year IN ( $season )
            AND p.start = 1
            AND p.matchid = $matchid
            ORDER BY is_goalkeeper DESC
            LIMIT 11";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            if(isset($row['apos'])){
                $pos = $row['apos'];
            }else if(isset($row['npos'])){
                $pos = $row['npos'];
            }else{
                $pos = '';
            }
            $data[] = array (
                'playerid' => $row['playerid'],
                'playername' => $row['lastname'],
                'position' => $pos,
                'fullname' => $row['fullname'],
                'shirtnumber' => $row['shirtnumber'],
                'is_goalkeeper' => $row['is_goalkeeper'],
                'teamname' => ($teamid == $row['t1id'] ? $row['t2name'] : $row['t1name'] )
            );
        }
        return $data;
    }
    public function getLastFiveLineups($teamid,$season, $latestMatches){
        $lineupArray = array();
        foreach($latestMatches as $matches){
            $lineupArray[$matches['matchid']] = self::getLineup($teamid, $season, $matches['matchid']);
        }
        return $lineupArray;
    }
    public function getSecondTeamId($teamid){
        $q = "SELECT teamid FROM team_secondteam WHERE second_teamid = {$teamid}";
        $realteamid = -1;
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $realteamid = $row['teamid'];
        }
        return $realteamid;
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
         $q = "SELECT m.*, SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS dateofmatch1, unix_timestamp(m.dateofmatch) as timestamp, home.`teamid` as homeid ,home.`teamname` as homename ,away.`teamid` as awayid ,away.`teamname` as awayname, m.teamwonid, home.surface " .
            "FROM matchtable m  " .
            "JOIN leaguetable l ON m.`leagueid` = l.`leagueid` " .
            "JOIN teamtable home ON m.`hometeamid` = home.`teamid` " .
            "JOIN teamtable away ON m.`awayteamid` = away.`teamid` "      .
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
                'timestamp' => $row['timestamp'],
                'surface' => $row['surface']
            );
        }
        return $data;
    }    
    
    public function getOverGoals($teamid,$season,$type = '')
    {
        $where = "(m.`awayteamid` = {$teamid} OR m.`hometeamid` = {$teamid})";
        if($type == 'home'){
            $where = "(m.`hometeamid` = {$teamid})";
        }else if($type == 'away'){
            $where = "(m.`awayteamid` = {$teamid})";
        }
        $q = "SELECT (m.`homescore` + m.awayscore) AS totalgoals FROM matchtable m 
        JOIN leaguetable l ON m.`leagueid` = l.`leagueid` 
        WHERE m.`result` NOT REGEXP '- : -|(Utsatt)' 
        AND l.year IN ( {$season} ) AND  
        {$where}  
        ORDER BY m.`dateofmatch` ASC";
        
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
    
    public function getCleanSheets($teamid, $season)
    {
        $q = "SELECT COUNT(*) as sum FROM matchtable m 
            JOIN leaguetable l ON m.leagueid = l.leagueid
            WHERE l.year  IN  ({$season}) AND m.`result` NOT REGEXP '- : -|(Utsatt)'  AND ((m.hometeamid = {$teamid} AND m.awayscore = 0) OR (m.awayteamid = {$teamid} AND m.homescore = 0)) ";
        
        $result = mysql_query($q);
        $count = 0;
        while($row = mysql_fetch_array($result))
        {
           $count = $row['sum'];
        }
        return $count;
    }
    public function getMostEvents($teamid, $season, $eventtypes)
    {
        $q = "SELECT p.`playerid`,p.`playername`,COUNT(*) AS events FROM eventtable e 
        JOIN leaguetable l ON l.`leagueid` = e.`leagueid`
        JOIN playertable p ON p.`playerid` = e.`playerid` AND p.teamid = e.teamid AND p.year = l.year
        WHERE e.eventtype IN ({$eventtypes}) AND e.teamid = {$teamid} AND l.`year` IN ( {$season} ) AND e.ignore = 0
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
        JOIN playertable p ON p.`playerid` = e.`playerid` AND p.`teamid` = e.`teamid` AND p.year = YEAR(m.dateofmatch) 
        WHERE e.teamid={$teamid} AND l.`year` IN ( {$season} )
        AND e.ignore = 0 
        GROUP BY e.`playerid`
        ORDER BY minutes DESC
        LIMIT 1";
        
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
        WHERE m.`awayteamid` = {$teamid} OR m.`hometeamid` = {$teamid} AND l.year  IN( {$season}) ORDER BY m.`dateofmatch` ASC";
        
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
            (SELECT tt.teamid,tt.teamname,COUNT(*) AS `event count` FROM eventtable e
        JOIN playertable t ON t.playerid = e.playerid AND e.teamid = t.teamid AND t.year IN ( {$season} )
        JOIN teamtable tt ON tt.teamid = t.teamid
        JOIN matchtable m ON e.matchid = m.matchid
        JOIN leaguetable l ON l.leagueid = m.leagueid
        WHERE e.eventtype IN ( {$eventtype} )
        AND l.year IN ( {$season} )
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
            eventtable e 
            JOIN matchtable m 
                ON e.matchid = m.matchid 
                AND (
                m.hometeamid = {$teamid} 
                OR m.awayteamid = {$teamid}
                )
            JOIN playertable p ON p.`playerid` = e.`playerid` AND p.`teamid` = e.`teamid` AND p.year = YEAR(m.dateofmatch)
            
                JOIN teamtable home ON m.`hometeamid` = home.`teamid` 
                JOIN teamtable away ON m.`awayteamid` = away.`teamid` 
                JOIN leaguetable l ON m.leagueid = l.leagueid
            WHERE (e.minute > 0) 
            AND (
                (e.eventtype IN (4, 8) 
                AND e.teamid = {$teamid}) 
                OR (e.eventtype = 9 and e.teamid != {$teamid})
                )
                AND l.year IN ( {$season} )
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
            eventtable e 
        JOIN matchtable m 
                ON e.matchid = m.matchid 
                AND (
                m.hometeamid = {$teamid} 
                OR m.awayteamid =  {$teamid} 
                )
            JOIN playertable p ON p.`playerid` = e.`playerid` AND p.`teamid` = e.`teamid` AND p.year = YEAR(m.dateofmatch)
            
                JOIN leaguetable l ON l.leagueid = m.leagueid
            JOIN teamtable home ON m.`hometeamid` = home.`teamid` 
                JOIN teamtable away ON m.`awayteamid` = away.`teamid`     
            WHERE (e.minute > 0) 
            AND (
                (
                e.eventtype IN (4, 8) 
                AND e.teamid !=  {$teamid} 
                ) 
                OR (e.eventtype = 9 
                AND e.teamid =  {$teamid} )
            ) AND l.year IN ( {$season} ) AND e.ignore = 0 ORDER BY e.minute asc;
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
                'result' => $row['result'],
                'teamid' => $row['teamid']
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
    
    public function getTeamPlayerJSON($teamid,$season)
    {
        $q = "SELECT * FROM (SELECT p.`playerid`,
        SUM(IF(e.`eventtype` = \"4\", 1,0)) AS `goals scored`, 
        SUM(IF(e.eventtype = \"8\", 1,0)) AS `penalty`,
        SUM(IF(e.eventtype = \"9\", 1,0)) AS `own goals`,
        SUM(IF(e.eventtype = \"2\", 1,0)) AS `yellow cards`, 
        (SUM(IF(e.eventtype = \"3\", 1,0)) + SUM(IF(e.eventtype = \"1\", 1,0))) AS `red cards`,
        SUM(IF(e.eventtype = \"7\", 1,0)) AS `subbed in` ,
        SUM(IF(e.eventtype = \"6\", 1,0)) AS `subbed off`
        FROM playtable p 
        LEFT JOIN eventtable e ON e.matchid = p.matchid AND e.playerid = p.playerid AND e.teamid = p.teamid AND e.ignore = 0
        JOIN matchtable m ON e.matchid = m.matchid
        JOIN leaguetable l ON m.leagueid = l.leagueid
        WHERE p.`teamid` = {$teamid}
        and l.year  IN ( {$season} )
        AND p.ignore = 0 
        GROUP BY p.`playerid`) as team
        LEFT JOIN
        (SELECT GROUP_CONCAT(DISTINCT (mn.`leagueid`) SEPARATOR ',') AS nationalleague, pn.playerid 
    FROM playtable_national pn 
      JOIN matchtable_national mn 
        ON mn.`matchid` = pn.`matchid` 
    WHERE YEAR(mn.`dateofmatch`) IN ($season)  and pn.ignore = 0 
    GROUP BY pn.`playerid`) AS `national` 
    ON national.playerid = team.`playerid`  ";
        
        
        $q2 = "SELECT total.*, p.shirtnumber, p.playername FROM (
        SELECT  p.`playerid`,p.teamid, SUM(p.minutesplayed) AS `minutes played`, SUM(p.start) AS `started`
        FROM playtable p 
        JOIN matchtable m ON p.`matchid` = m.`matchid`
        JOIN leaguetable l ON m.`leagueid` = l.`leagueid`
        WHERE p.`teamid` = {$teamid}
        AND l.`year`  IN ( {$season} )
        AND p.ignore = 0
        GROUP BY p.`playerid`) as total 
        JOIN playertable p on p.playerid = total.playerid and p.teamid = total.teamid GROUP by p.playerid order by p.shirtnumber asc ";
        
        
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
                $data[$row['playerid']]['nationalleague'] = $row['nationalleague'];
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
    
    public function getPlayingMinutesBenchJSON($season,$limit, $leagueid)
    {
        return DatabaseTeam::getPlayingMinutesJSON($season,$limit,$leagueid,0);
    }
    public function getPlayingMinutesJSON($teamid,$leagueid,$season,$start = 1)
    {
        if(strpos($teamid, ',') !== false){
            $limit = true;
        }else{
            $limit = false;
        }
        
        $q = "SELECT pp.playerid,pp.playername,t.teamname,t.teamid,SUM(p.minutesplayed) AS `minutes played` FROM playtable p " . 
        "JOIN matchtable m ON m.matchid = p.matchid " .
        "JOIN playertable pp ON pp.playerid = p.playerid AND p.teamid = pp.teamid AND pp.year = YEAR(m.dateofmatch) ".
        "JOIN teamtable t ON t.teamid = pp.teamid ".
        "JOIN leaguetable l ON m.leagueid = l.leagueid " .
        "WHERE pp.playerid != -1 " .
        ($teamid == 0 ? '' : ' AND t.teamid IN ( '.$teamid.' )') .
        "AND p.start = " . $start. " " .
        ($leagueid == '0' ? '' : ' AND l.java_variable IN ('.$leagueid.') ') .
        "AND l.year IN ( " . $season . " ) AND p.ignore = 0 " .
        "GROUP BY p.playerid ".
        "ORDER BY SUM(p.minutesplayed) DESC " .
        ($teamid == 0 || $limit == 1 ? ' LIMIT 10  ' : ' ') ;

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
    
    public function getEventInfoTotalTeam($eventtype, $limit, $season, $leagueid)
    {
        if($leagueid == '12'){
            return DatabaseFutsal::getEventlistTeam($eventtype,$season,0,$limit);
        }
        if($leagueid == '8'){
            $leagueid = '3,4,5,6';
        }
        // Clean sheet hack
        if($eventtype == 12){
            return DatabaseUtils::getCleanSheetsTeam($season,$leagueid);
        }
        if($eventtype == 70){
            return DatabaseUtils::getGoalsAsSubstitutes('teamid',$leagueid,$season);
        }
        
        $teamid = 0;
        $teamids = DatabaseLeague::getCustomLeagueTeams($leagueid);
        if(!empty($teamids)){
            $teamid = implode(" , ", $teamids);
            $leagueid = 0;
        }
        
        $q = 
        "SELECT tt.teamid,tt.teamname,COUNT(*) AS `event count`, eventtype FROM eventtable e " .
        "JOIN matchtable m ON e.matchid = m.matchid  ".
        "JOIN playertable t ON t.playerid = e.playerid AND e.teamid = t.teamid AND t.year = YEAR(m.dateofmatch) ". 
        "JOIN teamtable tt ON tt.teamid = t.teamid " .
        "JOIN leaguetable  l ON l.leagueid = m.leagueid " .        
        "WHERE e.eventtype IN ( ".$eventtype . ")" .
        "AND e.playerid != -1 ".
        "AND l.year IN ( "  . $season .  " ) "  .
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
    
    
    public function getTeamToLeague($teamid,$season)
    {
        $q = "SELECT t.teamid,t.teamname, l.java_variable,l.leaguename,c1.url as c1url, c2.url as c2url, t.surface FROM teamtable t 
            JOIN matchtable m ON m.hometeamid = t.teamid
            JOIN leaguetable l ON l.leagueid = m.leagueid
            LEFT JOIN city_weatherurl c1 ON c1.city = t.city
            LEFT JOIN city_weatherurl c2 ON c2.city = t.teamname
            WHERE t.teamid = {$teamid}
            AND l.year IN ( {$season} )
            GROUP BY m.hometeamid
            ORDER BY t.teamname ASC";
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $url = '';
            if(isset($row['c2url'])){
                $url = $row['c2url'];
            }else if(isset($row['c1url'])){
                $url = $row['c1url'];
            }
            $url = str_replace('ø','%c3%b8',$url);
            $url = str_replace('æ','%c3%a6',$url);
            $url = str_replace('å','%c3%85',$url);
            $data[] = array(
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname'],
                'leagueid' => $row['java_variable'],
                'leaguename' => $row['leaguename'],
                'weatherurl' => $url,
                'surface' => $row['surface']
            );
        }
        return $data;
    }
    
    public function getMostUsedLineup($teamid,$season)
    {
        $q = "SELECT SUM(p.`start`) AS starts, pt.playerid, SUBSTRING_INDEX(pt.`playername`,' ',-1) AS lastname,pta.`position` as apos ,ptn.`position` as npos
            FROM playtable p 
            JOIN matchtable m ON m.`matchid` = p.`matchid`
            JOIN leaguetable l ON m.`leagueid` = l.`leagueid`
            JOIN playertable pt ON p.`playerid` = pt.`playerid` AND p.`teamid` = pt.`teamid` AND pt.`leagueid` = m.`leagueid`
            LEFT JOIN playertable_altom pta ON pta.`playerid` = pt.`playerid_altom`
            LEFT JOIN playertable_nifs ptn ON ptn.`playerid` = pt.`playerid_nifs`
            WHERE p.`teamid` = $teamid
            AND l.year IN ( $season )
            GROUP BY p.`playerid`
            ORDER BY starts DESC
            LIMIT 11";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            if(isset($row['apos'])){
                $pos = $row['apos'];
            }else if(isset($row['npos'])){
                $pos = $row['npos'];
            }else{
                $pos = '';
            }
            $data[] = array (
                'starts' => $row['starts'],
                'playerid' => $row['playerid'],
                'playername' => $row['lastname'],
                'position' => $pos
            );
        }
        return $data;
    }
    public function getTeam($teamid){
        $q = "SELECT * FROM teamtable where teamid = $teamid";
        return mysql_query($q);
    }
    public function getTwitterName($teamid){
        $q = "SELECT twittername FROM teamtable where teamid = $teamid LIMIT 1";
        $data = '';
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data = $row['twittername'];
        }
        return $data;
    }
    public function getWinPercentage($teamid,$season){
        $q="
        SELECT 
        p.`playerid`,
        pl.`playername`,
        COUNT(*) AS started,
        COUNT(won.`matchid`) AS won,
        ROUND((COUNT(won.`matchid`) / COUNT(*)) * 100,2) AS percentage,
        SUM(p.minutesplayed)
        FROM
        playtable p 
        JOIN matchtable m 
            ON m.`matchid` = p.`matchid` 
        LEFT JOIN matchtable won 
            ON won.`matchid` = p.`matchid`
            AND won.`teamwonid` = {$teamid}
        JOIN leaguetable l 
            ON l.`leagueid` = m.`leagueid` 
            JOIN playertable pl
            ON p.`playerid` = pl.`playerid`
            AND pl.`teamid` = p.`teamid`
            AND pl.`year` = l.`year`
        WHERE p.`teamid` = {$teamid} 
        AND p.`start` = 1 
        AND l.`year` IN ( {$season} )
        AND p.ignore = 0 
        GROUP BY p.`playerid` 
        HAVING COUNT(*) >= 5
        ORDER BY (COUNT(won.`matchid`) / COUNT(*)) DESC";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'percentage'=> $row['percentage'],
                'matcheswon' => $row['won'],
                'matchesstarted' => $row['started']
            );
        }
        return $data;
    }
    function getMontlyPoints($teamid, $season)
    {
        $q="SELECT 
            MONTH(m.dateofmatch) AS month, SUM(IF(m.teamwonid = $teamid, 1, 0) * 3 + IF(m.teamwonid = 0, 1, 0)) AS points 
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON l.leagueid = m.leagueid 
            WHERE (m.hometeamid = $teamid OR m.awayteamid = $teamid) 
            AND l.year IN ($season)
            GROUP BY MONTH(m.dateofmatch)";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'month' => $row['month'],
                'points' => $row['points']
            );
        }
        return $data;
    }
    public function getTeamMinutes($year)
    {
        $q="select 
        home.total + away.total as totalmin,
        home.teamid
        from
        (SELECT 
            (COUNT(*) * 90) AS total,
            m.`hometeamid` AS teamid 
        FROM
            matchtable m 
            JOIN leaguetable l 
            ON m.`leagueid` = l.`leagueid` 
            AND l.`year` IN ($year) 
        where m.`result` NOT REGEXP '- : -|(Utsatt)' 
        group by m.`hometeamid`) as home 
        join 
            (SELECT 
            (COUNT(*) * 90) AS total,
            m.`awayteamid` AS teamid 
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON m.`leagueid` = l.`leagueid` 
                AND l.`year` IN ($year) 
            WHERE m.`result` NOT REGEXP '- : -|(Utsatt)' 
            GROUP BY m.awayteamid) as away  on home.teamid = away.teamid";
        
        $teammins = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result)){   
            $teammins[$row['teamid']] = $row['totalmin'];
        }
        return $teammins;
    }
    public function getTeamMinutesScoped($from,$to)
    {
        $q="select 
        home.total + away.total as totalmin,
        home.teamid
        from
        (SELECT 
            (COUNT(*) * 90) AS total,
            m.`hometeamid` AS teamid 
        FROM
            matchtable m 
            JOIN leaguetable l 
            ON m.`leagueid` = l.`leagueid` 
            where m.`result` NOT REGEXP '- : -|(Utsatt)'
        AND m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) 
        group by m.`hometeamid`) as home 
        join 
            (SELECT 
            (COUNT(*) * 90) AS total,
            m.`awayteamid` AS teamid 
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON m.`leagueid` = l.`leagueid` 
            WHERE m.`result` NOT REGEXP '- : -|(Utsatt)' 
            AND m.dateofmatch BETWEEN '$from' AND DATE_ADD('$to',INTERVAL 1 MONTH) 
            GROUP BY m.awayteamid) as away  on home.teamid = away.teamid";
        
        
        $teammins = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result)){   
            $teammins[$row['teamid']] = $row['totalmin'];
        }
        return $teammins;
    }
    
}
