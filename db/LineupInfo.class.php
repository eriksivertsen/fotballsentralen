<?php

class LineupInfo {
    public function getLineupInfo($teamid, $teamString, $limit = 11, $includeMissing = true) {

        $mostUsedLineup = LineupInfo::getMostUsedLineup($teamid);
        $startedLastMatch = LineupInfo::getStartedLastMatch($teamid);
        $startCountArray = LineupInfo::getStartCount($teamid);
        $squadCountArray = LineupInfo::getSquadCount($teamid);
        $startedLastFive = LineupInfo::getStartedLastGames($teamid);
        if($includeMissing) {
            $bestSquad = LineupInfo::getBestSquad($teamid);
        }else{
            $bestSquad = array();
        }
        $teamString = addslashes($teamString);
        
        if(empty($teamString)){
            return array();
        }
        
        $q = "SELECT * FROM playertable p 
        WHERE p.teamid = " . $teamid . ' 
        AND p.year = ' . Constant::CURRENT_YEAR . ' 
        AND p.playername REGEXP \'' . substr($teamString, 0, -1) . '\' ';

        $data = array();
        $result = mysql_query($q);
        
        $totalKey = 0;
        $prefferedCount = 0;
        $startedLastCount = 0;
        $totalStart = 0;
        $totalSquad = 0;
        $totalPlaytime = 0;
        
        
        $totalPlayers = 0;
        while ($row = mysql_fetch_array($result)) {
            $totalPlayers++;
            $playerid = $row['playerid'];

            if(isset($bestSquad[$playerid])){
                $bestSquad[$playerid] = '1';
            }
            
            $mostUsed = 'NEI';
            $startedLast = 'NEI';
            $startCount = 0;
            $squadCount = 0;
            $lastFive = 0;

            if (in_array($playerid, $mostUsedLineup)) {
                $mostUsed = 'JA';
                $prefferedCount++;
            }
            if (in_array($playerid, $startedLastMatch)) {
                $startedLast = 'JA';
                $startedLastCount++;
            }
            if (isset($startCountArray[$playerid])) {
                $startCount = $startCountArray[$playerid];
                $totalStart += $startCount;
            }
            if (isset($squadCountArray[$playerid])) {
                $squadCount = $squadCountArray[$playerid];
                $totalSquad += $squadCount;
            }
            if (isset($startedLastFive[$playerid])) {
                $lastFive = $startedLastFive[$playerid];
            }
            
            $playtime = LineupInfo::getPlaytime($teamid,$playerid);
            $totalPlaytime += $playtime;
            
            $key = number_format($row['key'],2);
            $totalKey += $key;

            $data[] = array(
                'playername' => $row['playername'],
                'key' => $key,
                'mostused' => $mostUsed,
                'startedlast' => $startedLast,
                'startcount' => $startCount,
                'squadcount' => $squadCount,
                'lastfive' => $lastFive,
                'squadstatus' => LineupInfo::getSquadStatus($startCount,$squadCount),
                'playtime' => $playtime
            );
        }
        
        
        $players = explode('|',$teamString);
        $sorted = array();
        foreach($players as $player){
            foreach($data as $res){
                if($player == $res['playername']){
                    $sorted []= $res;
                }
            }
        }
        if($includeMissing){
            $missingPlayers = array();
            foreach($bestSquad as $key => $val){
                if($val != '1'){
                    $missingPlayers[] = $val;
                }
            }
        }
        
        if($includeMissing){
            $missingString = implode('|', $missingPlayers);
            $sorted['summary']['missingplayers'] = LineupInfo::getLineupInfo($teamid,$missingString,11,false);
        }
        $sorted['summary']['totalkey'] = $totalKey;
        $sorted['summary']['laststarted'] = $startedLastCount;
        $sorted['summary']['preferred'] = $prefferedCount;
        $sorted['summary']['totalstart'] = number_format($totalStart / $limit ,2);
        $sorted['summary']['totalsquad'] = number_format($totalSquad / $limit ,2);
        $sorted['summary']['totalplaytime'] = number_format($totalPlaytime / $limit ,2);
        
        return $sorted;
    }

    public function getMostUsedLineup($teamid) {
        $q = "SELECT SUM(p.`start`) AS starts, pt.playerid, SUBSTRING_INDEX(pt.`playername`,' ',-1) AS lastname,pta.`position` as apos ,ptn.`position` as npos " .
                "FROM playtable p  " .
                "JOIN matchtable m ON m.`matchid` = p.`matchid` " .
                "JOIN leaguetable l ON m.`leagueid` = l.`leagueid` " .
                "JOIN playertable pt ON p.`playerid` = pt.`playerid` AND p.`teamid` = pt.`teamid` AND pt.`leagueid` = m.`leagueid` " .
                "LEFT JOIN playertable_altom pta ON pta.`playerid` = pt.`playerid_altom` " .
                "LEFT JOIN playertable_nifs ptn ON ptn.`playerid` = pt.`playerid_nifs` " .
                "WHERE p.`teamid` = " . $teamid . " " .
                "AND l.year = " . Constant::CURRENT_YEAR . " " .
                "AND p.playerid != -1 " .
                "GROUP BY p.`playerid` " .
                "ORDER BY starts DESC " .
                "LIMIT 11 ";

        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[] = $row['playerid'];
        }
        return $data;
    }

    public function getStartedLastMatch($teamid) {
        $q = "SELECT " .
                "m.matchid " .
                "FROM " .
                "matchtable m " .
                "JOIN leaguetable l " .
                "ON m.`leagueid` = l.`leagueid` " .
                "JOIN teamtable home " .
                "ON m.`hometeamid` = home.`teamid` " .
                "JOIN teamtable away " .
                "ON m.`awayteamid` = away.`teamid` " .
                "WHERE (" .
                "m.`hometeamid` =  " . $teamid . " " .
                "OR m.`awayteamid` = " . $teamid . " " .
                ") " .
                "AND m.`dateofmatch` < NOW() " .
                "AND m.result NOT REGEXP '- : -|(Utsatt)' " .
                "ORDER BY m.`dateofmatch` DESC " .
                "LIMIT 1 ";

        $data = array();
        $result = mysql_query($q);
        $matchid = '';
        while ($row = mysql_fetch_array($result)) {
            $matchid = $row['matchid'];
        }

        $q2 = "SELECT pt.playerid,pta.`position` as apos ,ptn.`position` as npos, " .
                "t1.teamname as t1name,t2.teamname as t2name,t1.teamid as t1id ,t2.teamid as t2id " .
                "FROM playtable p " .
                "JOIN matchtable m ON m.`matchid` = p.`matchid` " .
                "JOIN teamtable t1 ON t1.`teamid` = m.hometeamid " .
                "JOIN teamtable t2 ON t2.`teamid` = m.awayteamid " .
                "JOIN leaguetable l ON m.`leagueid` = l.`leagueid` " .
                "JOIN playertable pt ON p.`playerid` = pt.`playerid` AND p.`teamid` = pt.`teamid` AND pt.`leagueid` = m.`leagueid` " .
                "LEFT JOIN playertable_altom pta ON pta.`playerid` = pt.`playerid_altom` " .
                "LEFT JOIN playertable_nifs ptn ON ptn.`playerid` = pt.`playerid_nifs` " .
                "WHERE p.`teamid` =  " . $teamid . "  " .
                "AND l.year =  " . Constant::CURRENT_YEAR . " " .
                "AND p.start = 1 " .
                "AND p.matchid =  " . $matchid . " " .
                "LIMIT 11";

        $data = array();
        $result = mysql_query($q2);
        while ($row = mysql_fetch_array($result)) {
            $data[] = $row['playerid'];
        }
        return $data;
    }

    public function getStartCount($teamid) {
        $q = "select count(*) as starts, p.`playerid` " .
                "from playtable p " .
                "join matchtable m on p.`matchid` = m.`matchid` " .
                "join leaguetable l on l.`leagueid` = m.`leagueid` " .
                "where p.`teamid` =  " . $teamid . " " .
                "and l.`year` = " . Constant::CURRENT_YEAR . " " .
                "and p.start = 1 " .
                "group by p.`playerid`";

        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[$row['playerid']] = $row['starts'];
        }
        return $data;
    }

    public function getSquadCount($teamid) {
        $q = "select count(*) as starts, p.`playerid` " .
                "from playtable p " .
                "join matchtable m on p.`matchid` = m.`matchid` " .
                "join leaguetable l on l.`leagueid` = m.`leagueid` " .
                "where p.`teamid` =  " . $teamid . " " .
                "and l.`year` = " . Constant::CURRENT_YEAR . " " .
                "group by p.`playerid`";
        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[$row['playerid']] = $row['starts'];
        }
        return $data;
    }

    public function getStartedLastGames($teamid) {
        $q = "SELECT COUNT(*) as starts, p.`playerid` FROM (SELECT  " .
                "DISTINCT m.`matchid`  " .
                "FROM  " .
                "matchtable m  " .
                "WHERE m.`result` NOT LIKE '- : -'  " .
                "AND (m.`hometeamid` = " . $teamid . " OR m.`awayteamid` = " . $teamid . ") " .
                "ORDER BY m.`dateofmatch` DESC LIMIT 5) AS matches  " .
                "JOIN playtable p ON p.`matchid` = matches.matchid AND p.`start` = 1 AND p.`teamid` =  " . $teamid . " " .
                "GROUP BY p.`playerid`";

        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[$row['playerid']] = $row['starts'];
        }
        return $data;
    }

    public function getBestSquad($teamid, $value = '>')
    {
       $q = "SELECT  " .
            "SUM(p.minutesplayed) as minutes,p.playerid, pl.playername " .
            "FROM " .
            "playtable p  " .
            "JOIN matchtable m ON m.`matchid` = p.`matchid` " .
            "JOIN leaguetable l ON l.`leagueid` = m.`leagueid` " .
            "JOIN playertable pl ON pl.`playerid` = p.`playerid` AND pl.`year` = l.`year` " .
            "WHERE p.`teamid` =   " . $teamid . " " .
            "AND l.`year` =  " . Constant::CURRENT_YEAR . " " .
            "GROUP BY p.`playerid` " .
            "HAVING sum(p.minutesplayed) $value 0 " .
            "ORDER BY SUM(p.`minutesplayed`) DESC ";
        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[$row['playerid']] = $row['playername'];
        }
        return $data;
    }
    
    public function getSquadStatus($starts, $squads) {
        if ($starts == 0 && $squads == 0) {
            return "Ny";
        }
        $percentage = ((double)$starts / (double) $squads) * 100;
        if ($percentage >= 80) {
            return "Nøkkelspiller";
        } else if ($percentage >= 68) {
            return "Førstelag";
        } else if ($percentage >= 50) {
            return "Rotasjon";
        } else {
            return "Backup";
        }
    }
    
    public function getPlaytime($teamid, $playerid) {
        $q = "SELECT  " .
                "SUM(minutesplayed) AS total, " .
                "p.`teamid` AS teamid " .
                "FROM " .
                "playtable p  " .
                "JOIN matchtable m  " .
                "ON p.`matchid` = m.`matchid`  " .
                "JOIN leaguetable l  " .
                "ON l.`leagueid` = m.`leagueid`  " .
                "WHERE p.`playerid` =  " . $playerid . " " .
                "AND l.`year` =  " . Constant::CURRENT_YEAR . " " .
                "AND p.teamid = " . $teamid . " " .
                "AND p.ignore = 0 ";
        $result = mysql_query($q);
        $totalplayer = 0;
        while ($row = mysql_fetch_array($result)) {
            $totalplayer = $row['total'];
        }
        
        if($totalplayer == 0){
            return 0;
        }
        $q2 = "SELECT " .
                "(COUNT(*) * 90) AS total, " .
                "m.`hometeamid` AS teamid " .
                "FROM " .
                "matchtable m  " .
                "JOIN leaguetable l  " .
                "ON m.`leagueid` = l.`leagueid`  " .
                "WHERE ( " .
                " m.`hometeamid` =  " . $teamid . " " .
                "OR m.`awayteamid` =  " . $teamid . " " .
                ")  " .
                " AND l.`year` =  " . Constant::CURRENT_YEAR . " " .
                "AND m.`result` NOT REGEXP '- : -|(Utsatt)' ";
        
        $result = mysql_query($q2);
        $totalteam = 0;
        while ($row = mysql_fetch_array($result)) {
            $totalteam = $row['total'];
        }
        return number_format($totalplayer / $totalteam * 100,2);
    }

}
