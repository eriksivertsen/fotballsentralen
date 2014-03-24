<?php
 
class DatabasePlayer {
    
    public function getPlayerInfo($playerid,$season,$from,$teamid)
    {
        if($season == 0){
            $season = Constant::ALL_STRING;
        }
        if(isset($from) && !empty($from)){
            DatabaseUtils::setPlayerHitFrom($playerid,$from,$season);
        }else{
            DatabaseUtils::setPlayerHit($playerid,$season);
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
            'cleansheetrank' => DatabaseUtils::getCleanSheetPlayerRank($playerid,$season),
            'playerinfo' => self::getPlayerInfoJSON($playerid,$season,$teamid),
            'playertoleague' => self::getPlayerToLeague($playerid,$season),
            'winpercentage' => self::getWinPercentageFromStart($playerid, $season, $teamid),
            'info' => self::getPlayerDetails($playerid),
            'similar' => self::getSimilarPlayers($playerid),
            'cleansheets' => self::getCleanSheets($playerid,$season),
            'teams' => self::getTeams($playerid,$season),
            'national' => self::getNationalTeam($playerid)
         );
        return $events;
    }
    
    public function getNationalteam($playerid)
    {
         $q = "SELECT 
            matches.playerid, matches.matches, goals.goals
            FROM
            (SELECT 
                COUNT(*) AS matches ,
                p.`playerid` 
            FROM
                playtable_national p 
            WHERE p.`ignore` = 0 
            AND p.minutesplayed > 0
                AND p.`playerid` = $playerid) AS matches 
            LEFT JOIN 
                (SELECT 
                COUNT(*) AS goals ,
                p.`playerid` 
                FROM
                eventtable_national p 
                WHERE p.`ignore` = 0 
                AND p.`playerid` = $playerid 
                AND p.`eventtype` IN (4, 8)) AS goals 
                ON matches.playerid = goals.playerid";

        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data = array(
                'matches' => $row['matches'],
                'goals' => $row['goals'],
            );
        }
        return $data;
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
                AND l.`year` IN ( {$season} )
                GROUP by t.teamid
                ORDER BY m.`dateofmatch` DESC
                LIMIT 1";

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
            $q = "SELECT pl.playerid,pl.playername,p.teamid,t.teamname,l.java_variable,group_concat(distinct(mn.leagueid) separator ', ') as nationalleague
                FROM playtable p
                JOIN matchtable m ON m.`matchid` = p.`matchid`
                JOIN playertable pl ON pl.playerid = p.playerid
                JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
                JOIN teamtable t ON t.teamid = p.teamid
                LEFT JOIN playtable_national pn ON pn.`playerid` = p.`playerid`
                LEFT JOIN matchtable_national mn ON mn.`matchid` = pn.`matchid` AND  YEAR(mn.dateofmatch) IN ( $season )
                WHERE p.`playerid` = {$playerid}
                AND l.`year` IN ( {$season} )
                GROUP by t.teamid";

        $data = array();
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result))
        {
            $data[] = array(
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname'],
                'nationalleague' => $row['nationalleague']
            );
        }
        return $data;
    }
    public function getPlayerInfoNational($playerid,$season,$teamid)
    {
        $q = "SELECT p.`playerid`,p.`matchid`,p.teamid,
        SUM(IF(e.`eventtype` = \"4\", 1,0)) AS `goals scored`, 
        SUM(IF(e.eventtype = \"8\", 1,0)) AS `penalty`,
        SUM(IF(e.eventtype = \"9\", 1,0)) AS `own goals`,
        SUM(IF(e.eventtype = \"2\", 1,0)) AS `yellow cards`, 
        (SUM(IF(e.eventtype = \"3\", 1,0)) + SUM(IF(e.eventtype = \"1\", 1,0))) AS `red cards`,
        SUM(IF(e.eventtype = \"6\", 1,0)) AS `subbed in` ,
        SUM(IF(e.eventtype = \"7\", 1,0)) AS `subbed off` 
        FROM playtable_national p 
        LEFT JOIN eventtable_national e ON e.matchid = p.matchid AND e.playerid = p.playerid AND e.ignore = 0
        JOIN matchtable_national m ON p.`matchid` = m.`matchid`
        WHERE p.`playerid` = {$playerid}
        AND year(m.dateofmatch) IN ( {$season} ) " .
        ($teamid == 0 ? "" : " AND m.leagueid =  " . $teamid . ""  ) .
        " AND p.ignore = 0  " .           
        "GROUP BY p.`playerid`,p.`matchid`
        ORDER BY m.dateofmatch DESC";
        
        $q2 = "SELECT total.*, p.playername, p.is_goalkeeper, p.shirtnumber FROM (SELECT p.teamid,p.`playerid`,p.minutesplayed AS `minutes played`, p.start AS `start`,
        home.teamid as homeid, away.teamid as awayid,
        home.teamname as `homename`,away.teamname as `awayname`, m.result,m.`teamwonid`, SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS dateofmatch, m.matchid,
        unix_timestamp(m.dateofmatch) as timestamp, m.leagueid
        FROM playtable_national p 
        JOIN matchtable_national m ON p.matchid = m.matchid
        JOIN teamtable_national home ON m.hometeamid = home.teamid
        JOIN teamtable_national away ON m.awayteamid = away.teamid
        WHERE p.`playerid` = {$playerid}
        AND p.ignore = 0      
        AND year(m.dateofmatch) IN ( {$season} ) " .
        ($teamid == 0 ? "" : " AND m.leagueid =  " . $teamid . ""  ) .
        " AND p.ignore = 0  " . 
        "GROUP BY p.`teamid`,p.`playerid`,p.`matchid` ) as total
        LEFT JOIN playertable_national p ON p.playerid = total.playerid GROUP by total.matchid";
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
                $data[$row['matchid']]['is_national']= 1;
                $data[$row['matchid']]['leagueid']= $row['leagueid'];
                $data[$row['matchid']]['minutesplayed']= $row['minutes played'];
                $data[$row['matchid']]['start'] = $row['start'];
                $data[$row['matchid']]['hometeamname'] = $row['homename'];
                $data[$row['matchid']]['awayteamname'] = $row['awayname'];
                $data[$row['matchid']]['homeid'] = $row['homeid'];
                $data[$row['matchid']]['awayid'] = $row['awayid'];
                $data[$row['matchid']]['result'] = $row['result'];
                $data[$row['matchid']]['dateofmatch'] = $row['dateofmatch'];
                $data[$row['matchid']]['timestamp'] = $row['timestamp'];
                $data[$row['matchid']]['matchid'] = $row['matchid'];
                $data[$row['matchid']]['playername'] = $row['playername'];
                $data[$row['matchid']]['number'] = $row['shirtnumber'];
                $data[$row['matchid']]['is_goalkeeper'] = $row['is_goalkeeper'];
            }
        }
        $json = array();
        foreach($data as $value){
            $json[] = $value;
        }
        return $json;
    }    
    public function getPlayerInfoJSON($playerid,$season,$teamid)
    {
        //39906, 39901, 39904, 39903, 39907, 39908, 39909, 39899
        if($teamid == -1){
            return DatabasePlayer::getPlayerInfoNational($playerid,$season,0);
        }
        if($teamid == '39901' || $teamid == '39906' || $teamid == '39904' || 
                $teamid == '39903' || $teamid == '39907' ||$teamid == '39908' 
                ||$teamid == '39909' || $teamid == '39899' ){
            return DatabasePlayer::getPlayerInfoNational($playerid,$season,$teamid);
        }
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
        AND l.year IN ( {$season} )
        AND p.ignore = 0  " .
        ($teamid == 0 ? '' : ' AND p.teamid = '.$teamid.' ') .              
        "GROUP BY p.`playerid`,p.`matchid`
        ORDER BY m.dateofmatch DESC";
        
        $q2 = "SELECT total.*, p.playername, p.is_goalkeeper, p.shirtnumber FROM (SELECT p.teamid,p.`playerid`,p.minutesplayed AS `minutes played`, p.start AS `start`,
        home.teamid as homeid, away.teamid as awayid,
        home.teamname as `homename`,away.teamname as `awayname`, m.result,m.`teamwonid`, SUBSTRING(m.dateofmatch FROM 1 FOR 16) AS dateofmatch, m.matchid,
        unix_timestamp(m.dateofmatch) as timestamp
        FROM playtable p 
        JOIN matchtable m ON p.matchid = m.matchid
        JOIN teamtable home ON m.hometeamid = home.teamid
        JOIN teamtable away ON m.awayteamid = away.teamid
        JOIN leaguetable l ON m.`leagueid` = l.`leagueid`
        WHERE p.`playerid` = {$playerid}
        AND p.ignore = 0 " .
        ($teamid == 0 ? '' : ' AND p.teamid = '.$teamid.' ') .       
        "AND l.year IN ( {$season} )
        GROUP BY p.`teamid`,p.`playerid`,p.`matchid` ) as total
        LEFT JOIN playertable p ON p.playerid = total.playerid AND p.teamid = total.teamid GROUP by total.matchid";
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
                $data[$row['matchid']]['is_national']= 0;
                $data[$row['matchid']]['leagueid']= 0;
                $data[$row['matchid']]['minutesplayed']= $row['minutes played'];
                $data[$row['matchid']]['start'] = $row['start'];
                $data[$row['matchid']]['hometeamname'] = $row['homename'];
                $data[$row['matchid']]['awayteamname'] = $row['awayname'];
                $data[$row['matchid']]['homeid'] = $row['homeid'];
                $data[$row['matchid']]['awayid'] = $row['awayid'];
                $data[$row['matchid']]['result'] = $row['result'];
                $data[$row['matchid']]['dateofmatch'] = $row['dateofmatch'];
                $data[$row['matchid']]['timestamp'] = $row['timestamp'];
                $data[$row['matchid']]['matchid'] = $row['matchid'];
                $data[$row['matchid']]['playername'] = $row['playername'];
                $data[$row['matchid']]['number'] = $row['shirtnumber'];
                $data[$row['matchid']]['is_goalkeeper'] = $row['is_goalkeeper'];
            }
        }
        if($teamid == 0){
            $nationalArray = DatabasePlayer::getPlayerInfoNational($playerid,$season,0);
            if(!empty($nationalArray)){
                foreach($nationalArray as $match){
                    array_push($data, $match);
                }    
                uasort($data,"DatabaseUtils::date");
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
            JOIN matchtable m 
                ON e.matchid = m.matchid 
            JOIN playertable t 
                ON t.playerid = e.playerid 
                AND e.teamid = t.teamid 
                AND t.year = YEAR(m.dateofmatch)
            JOIN teamtable tt 
                ON tt.teamid = t.teamid 
            JOIN leaguetable l on l.leagueid = m.leagueid  
            WHERE e.eventtype = {$eventtype}
            AND e.playerid != - 1 
            AND l.year IN ( {$season} )
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
        AND l.`year` IN ( {$season} ) " .
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
        
        if($season == Constant::ALL_STRING){
            return $totalplayer . ' minutter';
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
        AND l.`year` IN ( {$season} )
        AND m.`result` NOT REGEXP '- : -|(Utsatt)' ";
               
        $result = mysql_query($q2);
        while($row = mysql_fetch_array($result))
        {
            $totalteam = $row['total'];
        }
        
        return '' .number_format($totalplayer / $totalteam * 100 , 2) . ' %';
    }
    public function getWinPercentageFromStart($playerid,$season,$teamid)
    {
        $q = "SELECT (totalmatches_won / totalmatches_started) * 100 as percentage FROM (SELECT COUNT(*) AS totalmatches_started FROM playertable p 
        JOIN matchtable m ON (p.`teamid` = m.`awayteamid` OR p.`teamid` = m.`hometeamid`)
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid` AND l.`year` = p.`year` 
        JOIN playtable pl ON p.`playerid` = pl.`playerid` AND pl.`matchid` = m.`matchid` AND pl.ignore = 0 " .
        ($teamid == 0 ? '' : ' AND p.teamid = '.$teamid.' ') .
        "WHERE p.`playerid` = {$playerid}
        AND l.`year` IN ( {$season} )
        AND m.`result` NOT REGEXP '- : -|(Utsatt)'
        AND START = 1) AS totalmatches_started, (SELECT COUNT(*) AS totalmatches_won FROM playertable p 
        JOIN matchtable m ON (p.`teamid` = m.`awayteamid` OR p.`teamid` = m.`hometeamid`)
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid` AND l.`year` = p.`year`
        JOIN playtable pl ON p.`playerid` = pl.`playerid` AND pl.`matchid` = m.`matchid` AND pl.ignore = 0
        WHERE p.`playerid` = {$playerid}
        AND l.`year` IN ( {$season} ) " .
        ($teamid == 0 ? '' : ' AND p.teamid = '.$teamid.' ') .
        "AND m.`result` NOT REGEXP '- : -|(Utsatt)'
        AND m.`teamwonid` = p.`teamid`
        AND START = 1) AS totalmatches_won";
        
        $result = mysql_query($q);
        while($row = mysql_fetch_array($result)){
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
        AND l.`year` IN ( {$season} )
        AND m.`result` NOT REGEXP '- : -|(Utsatt)'
        AND START = 0) AS totalmatches_started, (SELECT COUNT(*) AS totalmatches_won FROM playertable p 
        JOIN matchtable m ON (p.`teamid` = m.`awayteamid` OR p.`teamid` = m.`hometeamid`)
        JOIN leaguetable l ON l.`leagueid` = m.`leagueid` AND l.`year` = p.`year`
        JOIN playtable pl ON p.`playerid` = pl.`playerid` AND pl.`matchid` = m.`matchid` AND pl.ignore = 0
        WHERE p.`playerid` = {$playerid}
        AND l.`year` IN ( {$season} )
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
            AND l.`year` IN ( {$season} )
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
