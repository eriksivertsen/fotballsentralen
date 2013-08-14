<?php
 
class DatabasePlayer {
    
    public function getPlayerInfo($playerid,$season,$from,$teamid)
    {
        if(isset($from) && !empty($from)){
            DatabaseUtils::setPlayerHitFrom($playerid,$from);
        }else{
            DatabaseUtils::setPlayerHit($playerid);
        }
        $events = array (
            'yellow' => self::getEventRankPlayer($playerid,2,$season),
            'yellow_red' => self::getEventRankPlayer($playerid,1,$season),
            'red' => self::getEventRankPlayer($playerid,3,$season),
            'goal' => self::getEventRankPlayer($playerid,4,$season),
            'subin' => self::getEventRankPlayer($playerid,6,$season),
            'subout' => self::getEventRankPlayer($playerid,7,$season),
            'penalty' => self::getEventRankPlayer($playerid,8,$season),
            'owngoal' => self::getEventRankPlayer($playerid,9,$season),
            'playerinfo' => self::getPlayerInfoJSON($playerid,$season,$teamid),
            'playertoleague' => self::getPlayerToLeague($playerid,$season),
            'playingminutes' => self::getPlayingMinutes($playerid, $season,$teamid),
            'winpercentage' => self::getWinPercentageFromStart($playerid, $season, $teamid),
            'info' => self::getPlayerDetails($playerid),
            'similar' => self::getSimilarPlayers($playerid),
            'cleansheets' => self::getCleanSheets($playerid,$season),
            'teams' => self::getTeams($playerid,$season)
         );
        return $events;
    }
    
    public function getPlayerToLeague($playerid,$season)
    {            
            $q = "SELECT pl.playerid,pl.playername,p.teamid,t.teamname,l.java_variable 
                FROM playtable p
                JOIN matchtable m ON m.`matchid` = p.`matchid`
                JOIN playertable pl ON pl.playerid = p.playerid
                JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
                JOIN teamtable t ON t.teamid = p.teamid
                WHERE p.`playerid` = {$playerid}
                AND l.`year` = {$season} 
                GROUP by t.teamid
                ORDER BY m.`dateofmatch` DESC";

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
    public function getTeams($playerid,$season)
    {            
            $q = "SELECT pl.playerid,pl.playername,p.teamid,t.teamname,l.java_variable 
                FROM playtable p
                JOIN matchtable m ON m.`matchid` = p.`matchid`
                JOIN playertable pl ON pl.playerid = p.playerid
                JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
                JOIN teamtable t ON t.teamid = p.teamid
                WHERE p.`playerid` = {$playerid}
                AND l.`year` = {$season} 
                GROUP by t.teamid";

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
    public function getPlayerInfoJSON($playerid,$season,$teamid)
    {
        $q = "SELECT p.`playerid`,p.`matchid`,p.teamid,
        SUM(IF(e.`eventtype` = \"4\", 1,0)) AS `goals scored`, 
        SUM(IF(e.eventtype = \"8\", 1,0)) AS `penalty`,
        SUM(IF(e.eventtype = \"9\", 1,0)) AS `own goals`,
        SUM(IF(e.eventtype = \"2\", 1,0)) AS `yellow cards`, 
        (SUM(IF(e.eventtype = \"3\", 1,0)) + SUM(IF(e.eventtype = \"1\", 1,0))) AS `red cards`,
        SUM(IF(e.eventtype = \"6\", 1,0)) AS `subbed in` ,
        SUM(IF(e.eventtype = \"7\", 1,0)) AS `subbed off` 
        FROM playtable p 
        LEFT JOIN eventtable e ON e.matchid = p.matchid AND e.playerid = p.playerid AND e.teamid = p.teamid AND e.ignore = 0
        JOIN matchtable m ON p.`matchid` = m.`matchid`
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
        WHERE p.`playerid` = {$playerid}
        AND l.year = {$season}
        AND p.ignore = 0  " .
        ($teamid == 0 ? '' : ' AND p.teamid = '.$teamid.' ') .              
        "GROUP BY p.`playerid`,p.`matchid`
        ORDER BY m.dateofmatch DESC";
        
        $q2 = "SELECT p.`playerid`,pt.`playername` as playername, p.minutesplayed AS `minutes played`, p.start AS `start`,
        home.teamid as homeid, away.teamid as awayid, pt.shirtnumber,
        home.teamname as `homename`,away.teamname as `awayname`, m.result,m.`teamwonid`, SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS dateofmatch, m.matchid
        FROM playtable p 
        JOIN playertable pt ON p.`playerid` = pt.`playerid` AND p.`teamid` = pt.`teamid` AND pt.year = {$season}
        JOIN matchtable m ON p.matchid = m.matchid
        JOIN teamtable home ON m.hometeamid = home.teamid
        JOIN teamtable away ON m.awayteamid = away.teamid
        JOIN leaguetable l ON m.`leagueid` = l.`leagueid`
        WHERE pt.`playerid` = {$playerid}
        AND p.ignore = 0 " .
        ($teamid == 0 ? '' : ' AND p.teamid = '.$teamid.' ') .       
        "AND l.year = {$season}
        GROUP BY pt.`teamid`,p.`playerid`,p.`matchid`";
        
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
            if(isset( $data[$row['matchid']])) {
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
                $data[$row['matchid']]['number'] = $row['shirtnumber'];
            }
        }
        $json = array();
        foreach($data as $value){
            $json[] = $value;
        }
        return $json;
    
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
            eventtable e 
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
    public function getPlayingMinutes($playerid, $season,$postteamid)
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
        AND l.`year` = {$season} " .
        ($postteamid == 0 ? '' : ' AND p.teamid = '.$postteamid.' ') .              
        "AND p.ignore = 0";
        
        
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
    public function getWinPercentageFromStart($playerid,$season,$teamid)
    {
        $q = "SELECT (totalmatches_won / totalmatches_started) * 100 as percentage FROM (SELECT COUNT(*) AS totalmatches_started FROM playertable p 
        JOIN matchtable m ON (p.`teamid` = m.`awayteamid` OR p.`teamid` = m.`hometeamid`)
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid` AND l.`year` = p.`year` 
        JOIN playtable pl ON p.`playerid` = pl.`playerid` AND pl.`matchid` = m.`matchid` AND pl.ignore = 0 " .
        ($teamid == 0 ? '' : ' AND p.teamid = '.$teamid.' ') .
        "WHERE p.`playerid` = {$playerid}
        AND l.`year` = {$season} 
        AND m.`result` NOT REGEXP '- : -|(Utsatt)'
        AND START = 1) AS totalmatches_started, (SELECT COUNT(*) AS totalmatches_won FROM playertable p 
        JOIN matchtable m ON (p.`teamid` = m.`awayteamid` OR p.`teamid` = m.`hometeamid`)
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid` AND l.`year` = p.`year`
        JOIN playtable pl ON p.`playerid` = pl.`playerid` AND pl.`matchid` = m.`matchid` AND pl.ignore = 0
        WHERE p.`playerid` = {$playerid}
        AND l.`year` = {$season} " .
        ($teamid == 0 ? '' : ' AND p.teamid = '.$teamid.' ') .
        "AND m.`result` NOT REGEXP '- : -|(Utsatt)'
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
    public function getPlayerDetails($playerid)
    {
        
        $q = "SELECT nifs.height as nheight, nifs.dateofbirth as ndateofbirth, nifs.position as nposition,
         altom.height as aheight,altom.weight as aweight,altom.position as aposition,altom.dateofbirth as adateofbirth,altom.country as acountry,altom.number as anumber FROM playertable p 
        LEFT JOIN playertable_altom altom ON p.playerid_altom = altom.playerid
        LEFT JOIN playertable_nifs nifs ON p.playerid_nifs = nifs.playerid
        WHERE p.playerid = {$playerid} LIMIT 1";
        
        $data = array();
       
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'height' => (isset($row['aheight']) && !empty($row['aheight']) ? $row['aheight'] : $row['nheight']),
                'weight' => (isset($row['aweight']) && !empty($row['aweight']) ? $row['aweight'] : null),
                'dateofbirth' => (isset($row['adateofbirth']) && !empty($row['adateofbirth']) ? $row['adateofbirth'] : $row['ndateofbirth']),
                'position' => (isset($row['aposition']) && !empty($row['aposition']) ? $row['aposition'] : $row['nposition']),
                'country' => (isset($row['acountry']) && !empty($row['acountry']) ? $row['acountry'] : null)
           );
        }
        
        return $data;
    }
    public function getSimilarPlayers($playerid)
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
    public function getCleanSheets($playerid,$season)
    {
        $q= "SELECT count(*) as sum
            FROM playtable p 
                JOIN matchtable m ON p.`matchid` = m.`matchid`
                JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
            WHERE p.`playerid` = {$playerid} 
            AND p.`start` = 1 
            AND l.`year` = {$season}
            AND (
                (
                    m.hometeamid = p.`teamid`
                    AND m.awayscore = 0
                ) 
                OR (
                    m.awayteamid = p.`teamid`
                    AND m.homescore = 0
                ) 
            );   
            ";
            $sum = 0;
            $result = mysql_query($q);
            while($row = mysql_fetch_array($result))
            {
                $sum = $row['sum'];
            }
            return $sum;
    }
}
