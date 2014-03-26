<?php

class DatabasePreview {
    
    public function getPreview($matchid,$season)
    {
        if($season == 0){
            $season = Constant::ALL_STRING;
        }else if($season == 1){ // Forrige sesong
            $season = Constant::LAST_YEAR;
        }else{
            $season = Constant::CURRENT_YEAR;
        }
        DatabaseUtils::setPreviewHit($matchid);
        
        $teamArray = self::getMatchInfo($matchid);
        $refereeStats = DatabaseUtils::getRefereeStats($season);
        $homeTeamInfo = DatabaseTeam::getTeamInfo($teamArray['hometeamid'],$season,'null');
        $awayTeamInfo = DatabaseTeam::getTeamInfo($teamArray['awayteamid'],$season,'null');
        $suspensions = DatabaseUtils::getSuspList($teamArray['leagueid']);
        
        $retVal = array(
            'hometeam' => $homeTeamInfo,
            'awayteam' => $awayTeamInfo,
            'hometeamFS' => (isset($fsscore[$teamArray['hometeamid']]) ? $fsscore[$teamArray['hometeamid']]: ''),
            'awayteamFS' => (isset($fsscore[$teamArray['awayteamid']]) ? $fsscore[$teamArray['awayteamid']]: ''),
            'suspension' => $suspensions,
            'dateofmatch' => $teamArray['dateofmatch'],
            'timestamp' => $teamArray['timestamp'],
            'refereename' => $teamArray['refereename'],
            'referee' => (isset($refereeStats[$teamArray['refereeid']]['refereename']) ? $refereeStats[$teamArray['refereeid']]['refereename'] : ''),
            'refereeid' => (isset($refereeStats[$teamArray['refereeid']]['refereeid']) ? $refereeStats[$teamArray['refereeid']]['refereeid']: ''),
            'refereestats' => (isset($refereeStats[$teamArray['refereeid']]) ? $refereeStats[$teamArray['refereeid']] : ''),
            'previousmatches' => self::getPreviousMatches($teamArray['hometeamid'], $teamArray['awayteamid']),
//            'cardrating' => self::getCardRating($homeTeamInfo,$awayTeamInfo,$refereeStatsSpec,$season, $teamArray['java_variable'])
          );
        return $retVal;
    }
    public function getMatchInfo($matchid)
    {
        $q = "SELECT hometeamid,r.refereename,l.java_variable,awayteamid,m.leagueid,SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS dateofmatch,UNIX_TIMESTAMP(dateofmatch) * 1000 as timestamp, m.refereeid 
            FROM matchtable m JOIN leaguetable l on l.leagueid = m.leagueid LEFT JOIN refereetable r ON r.refereeid = m.refereeid WHERE matchid =  ".$matchid." ";
        
        $data = array();
       
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data = array(
                'hometeamid' => $row['hometeamid'],
                'awayteamid' => $row['awayteamid'],
                'dateofmatch' => $row['dateofmatch'],
                'refereeid' => $row['refereeid'],
                'refereename' => $row['refereename'],
                'leagueid' => $row['leagueid'],
                'timestamp' => $row['timestamp'],
                'java_variable' => $row['java_variable']
             );
        }
        return $data;
    }    
    
    public function getMatchesOneWeek()
    {
        $q = "SELECT SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS dateofmatch, m.matchid, UNIX_TIMESTAMP(m.dateofmatch) * 1000 as timestamp, home.teamname as homename, away.teamname as awayname, l.java_variable
            FROM matchtable m JOIN teamtable home on m.hometeamid = home.teamid 
            JOIN teamtable away on m.awayteamid = away.teamid 
            JOIN leaguetable l ON l.leagueid = m.leagueid
            WHERE m.`result` LIKE '- : -' AND m.`dateofmatch` BETWEEN  NOW() AND NOW() + INTERVAL 76 HOUR ORDER BY m.dateofmatch ASC ";
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'matchid' => $row['matchid'],
                'homename' => $row['homename'],
                'awayname' => $row['awayname'],
                'dateofmatch' => $row['dateofmatch'],
                'leagueid' => $row['java_variable'],
                'timestamp' => $row['timestamp']
            );
        }
        return $data;
    }
    public function getPreviousMatches($teamid1, $teamid2)
    {
        $q = "SELECT m.*, SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS date, home.`teamname` AS homename, away.`teamname` AS awayname 
        FROM matchtable m 
        JOIN teamtable home ON home.`teamid` = m.`hometeamid` 
        JOIN teamtable away ON away.`teamid` = m.`awayteamid` 
        WHERE (m.`hometeamid` = $teamid1 OR m.`hometeamid` = $teamid2) 
        AND (m.`awayteamid` = $teamid1 OR m.`awayteamid` = $teamid2 ) 
        AND m.`result` NOT LIKE '- : -' 
        ORDER BY m.dateofmatch desc";
        
        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'matchid' => $row['matchid'],
                'hometeamid' => $row['hometeamid'],
                'homename' => $row['homename'],
                'awayname' => $row['awayname'],
                'awayteamid' => $row['awayteamid'],
                'dateofmatch' => $row['date'],
                'result' => $row['result'],
                'teamwonid' => $row['teamwonid']
            );
        }
        return $data;
    }    
    
    public function getFSScore($leagueid){
        $q = "SELECT m.*, home.`teamname` as homename,away.`teamname` as awayname
            FROM matchtable m 
            JOIN teamtable home ON home.`teamid` = m.`hometeamid` 
            JOIN teamtable away ON away.`teamid` = m.`awayteamid` 
            WHERE m.`leagueid` = {$leagueid}
            AND m.`result` NOT LIKE '- : -' 
            ORDER BY m.`dateofmatch` ASC";
        
        $fsscore = array();
        $result = mysql_query($q);
        $startingValue = 1000;
        
        //constants
        $awayWin = 8;
        $awayDraw = 5;
        
        $homeWin = 6;
        $homeDraw = 2;
        
        $divideConst = 10;
        
        while($row = mysql_fetch_array($result))
        {
            if(!isset($fsscore[$row['hometeamid']])){
                $fsscore[$row['hometeamid']] = $startingValue;
            }
            if(!isset($fsscore[$row['awayteamid']])){
                $fsscore[$row['awayteamid']] = $startingValue;
            }
            
            $homeFS = $fsscore[$row['hometeamid']];
            $awayFS = $fsscore[$row['awayteamid']];
            $homeScore = $row['homescore'];
            $awayScore = $row['awayscore'];
            
            $diff = abs($homeFS - $awayFS);
            $diffreal = $homeFS - $awayFS;
            
            //Homewin
            
            //echo 'match: ' . $row['homename'] . ' - ' .$row['awayname'] . ' :: diff : ' . $diff . '  res:' . $row['result'] . '   ';
            if($homeScore > $awayScore){
                $diffscore = $homeScore - $awayScore;
                $fsscore[$row['hometeamid']] = $fsscore[$row['hometeamid']] + ((($diff/$divideConst) * $homeWin) + $diffscore);
                $fsscore[$row['awayteamid']] = $fsscore[$row['awayteamid']] - ((($diff/$divideConst) * $homeWin) + $awayScore);
                //echo 'Adding ' . (($diff/$divideConst) * $homeWin) + $diffscore . ' to ' .$row['homename'] . ' after winning home against ' .$row['awayname'] . '   '; 
            }
            //awaywin
            else if ($homeScore < $awayScore) {
                $diffscore = $awayScore - $homeScore;
                $fsscore[$row['hometeamid']] = $fsscore[$row['hometeamid']] - ((($diff/$divideConst) * $awayWin) - $diffscore);
                $fsscore[$row['awayteamid']] = $fsscore[$row['awayteamid']] + ((($diff/$divideConst) * $awayWin) + $awayScore);
               // echo 'Adding ' . ((($diff/$divideConst) * $awayWin) + $awayScore) . ' to ' .$row['awayname'] . ' after winning away against ' .$row['homename'] . '  '; 
            }
            //Draw
            else if($homeScore == $awayScore){
                if($diffreal > 0){
                    //Home team strongest
                    $fsscore[$row['hometeamid']] = $fsscore[$row['hometeamid']] - ((($diff/$divideConst) * $homeDraw) - $awayScore);
                    $fsscore[$row['awayteamid']] = $fsscore[$row['awayteamid']] + ((($diff/$divideConst) * $awayDraw) + $awayScore);
                }else{
                    //Away team strongest.
                    $fsscore[$row['hometeamid']] = $fsscore[$row['hometeamid']] + ((($diff/$divideConst) * $homeDraw) + $homeScore);
                    $fsscore[$row['awayteamid']] = $fsscore[$row['awayteamid']] - ((($diff/$divideConst) * $awayDraw) - $homeScore);
                }
                
            }
            $fsscore[$row['hometeamid']] = number_format($fsscore[$row['hometeamid']],1,'.','');
            $fsscore[$row['awayteamid']] = number_format($fsscore[$row['awayteamid']],1,'.','');
        }
        return $fsscore;
    }
    public function getCardRating(array $homeTeam, array $awayTeam, array $refereeStats, $year, $java_variable)
    {
        $yellowavg = 0;
        $averageLeagueMatch = DatabaseLeague::getAvereageEventLeague('2', $java_variable, $year,'matchid');
         
        if(!empty($refereeStats)){
            if(!isset($refereeStats['yellowpr']) || empty($refereeStats['yellowpr'])){
                $yellowavg = $averageLeagueMatch;
            }else{
                $yellowavg = $refereeStats['yellowpr'];
            }
           
        }
        
        $homeCount = $homeTeam['yellow'][0]['count'];
        $awayCount = $awayTeam['yellow'][0]['count'];
        
        $averageLeagueTeam = DatabaseLeague::getAvereageEventLeague('2', $java_variable, $year,'teamid');
       
        $homeDiff = $homeCount - $averageLeagueTeam;
        $awayDiff = $awayCount - $averageLeagueTeam;
        $refereeDiff = $yellowavg - $averageLeagueMatch;
        
       // echo 'HomeDiff: ' . $homeDiff . ', awaydiff: ' .$awayDiff. ', refereeDiff: ' . $refereeDiff . ',avgLgTeam: ' .$averageLeagueTeam . ', avgLgMatch: ' . $averageLeagueMatch . ' homeYelw: ' . $homeCount. ', awayYellow: ' . $awayCount . ', refAvg: ' . $yellowavg;
        
        return number_format($homeDiff + $awayDiff + ($refereeDiff * 2),2);
    }
}