<?php

class LineupInfo {

    public function getLineupInfo($teamid, $teamString) {

        $mostUsedLineup = LineupInfo::getMostUsedLineup($teamid);
        $startedLastMatch = LineupInfo::getStartedLastMatch($teamid);
        $startCountArray = LineupInfo::getStartCount($teamid);
        $squadCountArray = LineupInfo::getSquadCount($teamid);
        $startedLastFive = LineupInfo::getSquadCount($teamid);

        $q = "SELECT * FROM playertable p 
        WHERE p.teamid = " . $teamid . ' 
        AND p.year = ' . Constant::CURRENT_YEAR . ' 
        AND p.playername REGEXP \'' . substr($teamString, 0, -1) . '\' ';

        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {

            $playerid = $row['playerid'];

            $mostUsed = 'NEI';
            $startedLast = 'NEI';
            $startCount = 0;
            $squadCount = 0;
            $lastFive = 0;

            if (in_array($playerid, $mostUsedLineup)) {
                $mostUsed = 'JA';
            }
            if (in_array($playerid, $startedLastMatch)) {
                $startedLast = 'JA';
            }
            if (isset($startCountArray[$playerid])) {
                $startCount = $startCountArray[$playerid];
            }
            if (isset($squadCountArray[$playerid])) {
                $squadCount = $squadCountArray[$playerid];
            }
            if (isset($startedLastFive[$playerid])) {
                $lastFive = $startedLastFive[$playerid];
            }
            
            $playtime = LineupInfo::getPlaytime($teamid,$playerid);


            $data[] = array(
                'playername' => $row['playername'],
                'key' => number_format($row['key'],2),
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
                "JOIN matchtable p ON p.`matchid` = matches.matchid AND p.`start` = 1 AND p.`teamid` =  " . $teamid . " " .
                "GROUP BY p.`playerid`";

        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[$row['playerid']] = $row['starts'];
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
