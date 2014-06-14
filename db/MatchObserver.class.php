<?php

include "dbConnection.php";
include "yr/autoload.php";

require_once 'DatabaseUtils.class.php';
require_once 'DatabaseSettings.class.php';
require_once 'LineupInfo.class.php';

class MatchObserver {
    
    const LOAD_WEATHER = false;

    public function getTightFixtures() {
        $q = " SELECT 
                m.hometeamid, m.awayteamid 
                FROM
                matchtable m 
                WHERE m.`dateofmatch` > NOW() - INTERVAL 7 DAY 
                AND m.`dateofmatch` < NOW() + INTERVAL 7 DAY
                UNION
                SELECT 
                mc.hometeamid, mc.awayteamid  
                FROM
                matchtable_cup mc
                WHERE mc.`dateofmatch` > NOW() - INTERVAL 7 DAY 
                AND mc.`dateofmatch` < NOW() + INTERVAL 7 DAY
            ";
        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $teamid = $row['hometeamid'];
            $teamid2 = $row['awayteamid'];
            
            if(isset($data[$teamid])){
                $data[$teamid] = $data[$teamid]+1;
            }else{
                $data[$teamid] = 1;
            }
            
            if(isset($data[$teamid2])){
                $data[$teamid2] = $data[$teamid2]+1;
            }else{
                $data[$teamid2] = 1;
            }
        }
        return $data;
    }

    public function getMatches(array $susp, array $tightFixtures, $userid) {

        set_time_limit(60);
        $settings = DatabaseSettings::getSettings();

        $q = "SELECT d.level, m.dateofmatch, m.matchid, 
            IF(mo.hometeam_lineup IS NULL,IF(mo.hometeam_lineup_text is null,0,1),mo.hometeam_lineup) as homelineup, 
            IF(mo.hometeam_lineup_text > '', mo.hometeam_lineup_text,homenews_team.squad_string) AS homelineup_string,
            IF(mo.awayteam_lineup_text > '', mo.awayteam_lineup_text, awaynews_team.`squad_string`) AS awaylineup_string,
            homenews.squad_string AS homesquad_string,
            awaynews.squad_string AS awaysquad_string,
            IF(mo.awayteam_lineup IS NULL,IF(mo.awayteam_lineup_text is null,0,1),mo.awayteam_lineup) as awaylineup,
            IF((mo.hometeam_squad = 0 OR mo.hometeam_squad IS NULL),IF(homenews.squad_string IS NULL,0,1),mo.hometeam_squad) as homesquad, 
            IF((mo.awayteam_squad = 0 OR mo.awayteam_squad IS NULL),IF(awaynews.squad_string IS NULL,0,1),mo.awayteam_squad) as awaysquad,
            UNIX_TIMESTAMP(m.dateofmatch) * 1000 as timestamp , home.teamid as homeid, away.teamid as awayid, 
            home.teamname as homename, away.teamname as awayname, l.java_variable, m.hometeamid as homeid, m.awayteamid as awayid,
            home.surface as homesurface, home.surface_condition as homecondition, away.surface as awaysurface, away.surface_condition as awaycondition,
            SUBSTRING_INDEX(c1.url,'/sted/',-1) AS c1url, SUBSTRING_INDEX(c2.url,'/sted/',-1) AS c2url, spread.homespread, spread.homeprice, spread.awayspread,spread.awayprice, total.points, total.overprice, total.underprice,
            o.match_homeprice as homeodds, o.match_drawprice as drawodds, o.match_awayprice as awayodds, mp.home as homepercentage, mp.draw as drawpercentage, mp.away as awaypercentage,  mp.homemore as homemorepercentage , mp.awaymore as awaymorepercentage
            FROM matchtable m 
            JOIN teamtable home on m.hometeamid = home.teamid 
            JOIN matchobserve_user mou ON mou.userid = $userid
            LEFT JOIN match_percentage mp on mp.matchid = m.matchid and mp.userid = $userid
            LEFT JOIN match_observe mo ON mo.matchid = m.matchid
            LEFT JOIN news homenews ON homenews.news_id = mo.homesquad_news_source
            LEFT JOIN news awaynews ON awaynews.news_id = mo.awaysquad_news_source
            LEFT JOIN news homenews_team ON homenews_team.news_id = mo.hometeam_news_source
            LEFT JOIN news awaynews_team ON awaynews_team.news_id = mo.awayteam_news_source
            JOIN teamtable away on m.awayteamid = away.teamid 
            JOIN leaguetable l ON l.leagueid = m.leagueid
            LEFT JOIN odds o ON o.matchid = m.matchid
            LEFT JOIN spreadodds spread on spread.matchid = m.matchid and spread.is_main_line = 1 and spread.period = 'MATCH'
            LEFT JOIN totalodds total on total.matchid = m.matchid and total.is_main_line = 1 and total.period = 'MATCH'
            LEFT JOIN city_weatherurl c1 ON c1.city = home.city
            LEFT JOIN city_weatherurl c2 ON c2.city = home.teamname
            LEFT JOIN derby d ON (d.teamid = home.teamid OR d.teamid = away.teamid) AND (d.teamid2 = home.teamid OR d.teamid2 = away.teamid)
            WHERE m.`result` LIKE '- : -' AND m.`dateofmatch` > NOW() AND l.java_variable REGEXP (mou.leaguestring) 
            GROUP by m.matchid 
            ORDER BY m.dateofmatch ASC, m.matchid LIMIT 100
            ";
        $data = array();
        // Loop throguh suspension and sort list with team => suspensioncount
        $suspension = array();
        foreach($susp as $league){
            $leagueFix = MatchObserver::fixSuspension($league);
            foreach ($leagueFix as $array) {
                foreach ($array as $player) {
                    if (isset($suspension[$player['teamid']])) {
                        $suspension[$player['teamid']] = $suspension[$player['teamid']] + 1;
                    } else {
                        $suspension[$player['teamid']] = 1;
                    }
                }
            }
        }
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $arrayName = MatchObserver::getArrayName($row['java_variable']);

            if (isset($data[$arrayName])) {
                if (count($data[$arrayName]) >= 9) {
                    continue;
                }
            }

            $url = '';
            if (isset($row['c2url'])) {
                $url = $row['c2url'];
            } else if (isset($row['c1url'])) {
                $url = $row['c1url'];
            }

            $from = ($row['timestamp'] / 1000);
            $to = ($from + 7200);
            
            try{
                $forecastRes = MatchObserver::getWeather($url, $from, $to, $settings);
            }catch(Exception $e){
                $forecastRes = array();
            }

            $suspHome = 0;
            $suspAway = 0;
            if (isset($suspension[$row['homeid']])) {
                $suspHome = $suspension[$row['homeid']];
            }
            if (isset($suspension[$row['awayid']])) {
                $suspAway = $suspension[$row['awayid']];
            }
            
            $tightFixtureHome = 0;
            $tightFixtureAway = 0;
            if(isset($tightFixtures[$row['homeid']])){
                if($tightFixtures[$row['homeid']] >= $settings[Constant::TIGHT_FIXTURE_MIN]['value']){
                    $tightFixtureHome = 1;
                }
            }
            if(isset($tightFixtures[$row['awayid']])){
                if($tightFixtures[$row['awayid']] >= $settings[Constant::TIGHT_FIXTURE_MIN]['value']){
                    $tightFixtureAway = 1;
                }
            }
            
            $homemoreperceantage = (isset($row['homemorepercentage']) ? $row['homemorepercentage'] : 0);
            $homepercentage = (isset($row['homepercentage']) ? $row['homepercentage'] : 0);
            $drawpercentage = (isset($row['drawpercentage']) ? $row['drawpercentage'] : 0);
            $awaypercentage = (isset($row['awaypercentage']) ? $row['awaypercentage'] : 0);
            $awaymoreperceantage = (isset($row['awaymorepercentage']) ? $row['awaymorepercentage'] : 0);
            
            $valueHome = MatchObserver::getHomeValue($homepercentage, $drawpercentage, $row['homespread'],$row['homeprice']);
            $valueAway = MatchObserver::getAwayValue($awaypercentage, $drawpercentage, $row['awayspread'],$row['awayprice']);

            $homeLineupInfo = array();
            $awayLineupInfo = array();
            $homeLineup = 0;
            $awayLineup = 0;
            $prefferedAlertHome = 0;
            $prefferedAlertAway = 0;
            
            if (!empty($row['homelineup_string'])) {
                $homeLineupInfo = LineupInfo::getLineupInfo($row['homeid'], $row['homelineup_string']);
                $homeLineup = 1;
                if((11-$homeLineupInfo['summary']['preferred']) >= $settings[Constant::MIN_FIRSTTEAM_PLAYER]['value']){
                    $prefferedAlertHome = 1;
                }
            }else{
                if (!empty($row['homesquad_string'])) {
                    $homeSquadInfo = LineupInfo::getLineupInfo($row['homeid'],$row['homesquad_string']);
                    if((11-$homeSquadInfo['summary']['preferred']) >= $settings[Constant::MIN_FIRSTTEAM_PLAYER]['value']){
                        $prefferedAlertHome = 1;
                    }
                }
            }
            if (!empty($row['awaylineup_string'])) {
                $awayLineup = 1;
                $awayLineupInfo = LineupInfo::getLineupInfo($row['awayid'], $row['awaylineup_string']);
                if((11 - $awayLineupInfo['summary']['preferred']) >= $settings[Constant::MIN_FIRSTTEAM_PLAYER]['value']){
                    $prefferedAlertAway = 1;
                }
            }else{
                if (!empty($row['awaysquad_string'])) {
                    $awaySquadInfo = LineupInfo::getLineupInfo($row['awayid'],$row['awaysquad_string']);
                    if((11-$awaySquadInfo['summary']['preferred']) >= $settings[Constant::MIN_FIRSTTEAM_PLAYER]['value']){
                        $prefferedAlertAway = 1;
                    }
                }
            }
            
            $data[$arrayName][] = array(
                'matchid' => $row['matchid'],
                'homename' => $row['homename'],
                'awayname' => $row['awayname'],
                'dateofmatch' => $row['dateofmatch'],
                'leagueid' => $row['java_variable'],
                'timestamp' => $row['timestamp'],
                'homelineup' => $homeLineup,
                'awaylineup' => $awayLineup,
                'homelineupInfo' => $homeLineupInfo,
                'awaylineupInfo' => $awayLineupInfo,
                'homesquad' => $row['homesquad'],
                'awaysquad' => $row['awaysquad'],
                'homesurface' => $row['homesurface'],
                'homecondition' => $row['homecondition'],
                'awaysurface' => $row['awaysurface'],
                'awaycondition' => $row['awaycondition'],
                'forecast' => $forecastRes,
                'tightfixurehome' => $tightFixtureHome,
                'tightfixureaway' => $tightFixtureAway,
                'preferedalerthome' => $prefferedAlertHome,
                'preferedalertaway' => $prefferedAlertAway,
                'derby' => $row['level'],
                'totalsusp' => ($suspHome + $suspAway),
                'susphome' => $suspHome,
                'suspaway' => $suspAway,
                'totaloddsline' => $row['points'],
                'totalover' => $row['overprice'],
                'totalunder' => $row['underprice'],
                'homespread' => ($row['homespread'] > 0 ? '+' . $row['homespread'] : $row['homespread']),
                'homeprice' => $row['homeprice'],
                'awayspread' => ($row['awayspread'] > 0 ? '+' . $row['awayspread'] : $row['awayspread']),
                'awayprice' => $row['awayprice'],
                'homeodds' => $row['homeodds'],
                'drawodds' => $row['drawodds'],
                'awayodds' => $row['awayodds'],
                'homemorepercentage' => $homemoreperceantage,
                'homepercentage' => $homepercentage,
                'drawpercentage' => $drawpercentage,
                'awaypercentage' => $awaypercentage,
                'awaymorepercentage' => $awaymoreperceantage,
                'valuehome' => $valueHome,
                'valueaway' => $valueAway,
                'settings' => $settings
            );
        }
        return $data;
    }
    public function getTotalValue($homeOver, $awayOver, $points, $overprice, $underprice){
        $overAverage = ($homeOver + $awayOver) / 2;
        $underAverage = 100 - $overAverage;
        switch($points){
            case 2.75:
            case 2.5: 
                $ourOver = (100 / $overAverage);
                $ourUnder = (100 / $underAverage);
                $array = array();
                $array['over'] = number_format(($ourOver / $overprice),2);
                $array['under'] = number_format(($ourUnder / $underprice),2);
                return $array;
            default: return array('over' => '', 'under' => '');
        }
    }
    public function getHomeValue($percentage, $drawpercentage, $spread,$homeprice) {
        if(!isset($percentage) || $percentage == 0 || !isset($spread) || !isset($homeprice)){
            return '';
        }
        $total = 100;
        switch($spread){
            case 0: 
                $total = 100 - $drawpercentage;
                $value = ($percentage / $total) * 100;
                $value = 100 / $value;
                return number_format($homeprice / $value ,2);
            case -0.25: 
                $total = 100 - ($drawpercentage / 2);
                $value = ($percentage / $total) * 100;
                $value = 100 / $value;
                return number_format($homeprice / $value ,2);
            case -0.5: 
                $total = 100;
                $value = (($percentage + ($drawpercentage / 2)) / $total) * 100;
                $value = 100 / $value;
                return number_format($homeprice  / $value ,2);
            case -0.75: return '';
            case -0.5: return '';
            default: return '';
        }
    }
    public function getAwayValue($percentage, $drawpercentage, $spread,$awayprice) {
        if(!isset($percentage) || $percentage == 0 || !isset($spread) || !isset($awayprice)){
            return '';
        }
        switch($spread){
            case 0: 
                $total = 100 - $drawpercentage;
                $value = ($percentage / $total) * 100;
                $value = 100 / $value;
                return number_format($awayprice / $value ,2);
            case 0.25: 
                $total = 100 - ($drawpercentage / 2);
                $value = (($percentage + + ($drawpercentage / 2)) / $total) * 100;
                $value = 100 / $value;
                return number_format($awayprice  / $value ,2);
            case 0.5: 
                $total = 100;
                $value = (($percentage + ($drawpercentage / 2)) / $total) * 100;
                $value = 100 / $value;
                return number_format($awayprice  / $value ,2);
            case 0.75:
            case 0.5: ;
            default: return '';
        }
    }
    
    public function getWeather($url, $from, $to, $settings) {
        $url = str_replace('ø', '%c3%b8', $url);
        $url = str_replace('æ', '%c3%a6', $url);
        $url = str_replace('å', '%c3%85', $url);
        $url = str_replace('Norge', 'Norway', $url);
        $url = substr($url, 0, strlen($url)-1);
        $forecastRes = array();
        try{
            $yr = Yr\Yr::create($url, "/tmp", $settings[Constant::SETTING_CACHE]['value'], 'norwegian');
        }
        catch(Exception $e){
            return $forecastRes;
        }
        foreach ($yr->getHourlyForecasts($from, $to) as $forecast) {
            $timestamp = $forecast->getFrom()->getTimestamp();
            if ($timestamp > $from && $timestamp < $to) {
                $windMps = $forecast->getWindSpeed('mps');
                $percipitation = $forecast->getPrecipitation();
                $alert = false;
                if ($windMps > $settings[Constant::SETTING_WIND]['value'] || $percipitation >= $settings[Constant::SETTING_PREP]['value']) {
                    $alert = true;
                }
                $forecastRes = array(
                    'from' => $forecast->getFrom(),
                    'alert' => $alert,
                    'temperature' => $forecast->getTemperature(),
                    'symbol' => $forecast->getSymbol(),
                    'wind_speed' => $forecast->getWindSpeed('name'),
                    'wind_speed_mps' => $windMps,
                    'precipitation' => $percipitation
                );
            }
        }
        return $forecastRes;
    }
    private function endsWith($haystack, $needle)
        {
            return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
        }


    public function getArrayName($java_variable) {
        switch ($java_variable) {
            case 1: return 'tippeligaen';
            case 2: return 'firstdiv';
            case 3: return 'seconddiv1';
            case 4: return 'seconddiv2';
            case 5: return 'seconddiv3';
            case 6: return 'seconddiv4';
        }
    }

    public function getMatchIds(array $matches) {
        $matchIds = array();
        foreach ($matches as $leagues) {
            foreach ($leagues as $match) {
                $matchIds[] = $match['matchid'];
            }
        }
        return $matchIds;
    }

    public function getMatchInfo($matchid) {
        $q = 
        "SELECT d.level,m.dateofmatch, m.matchid, UNIX_TIMESTAMP(m.dateofmatch) * 1000 as timestamp, 
        home.teamname as homename, home.teamid as homeid, 
        away.teamid as awayid, away.teamname as awayname, 
        home.surface as homesurface, 
        home.surface_condition, 
        away.surface as awaysurface,
        l.java_variable, r.refereename, 
        SUBSTRING_INDEX(c1.url,'/sted/',-1) AS c1url, 
        SUBSTRING_INDEX(c2.url,'/sted/',-1) AS c2url,
        home.official_homepage as home_homepage, 
        away.official_homepage as away_homepage,
        mo.hometeam_lineup_text as homelineup, 
        mo.awayteam_lineup_text as awaylineup,
        m.leagueid, m.refereeid, 
        mo.hometeam_squad_text AS homesquad, 
        mo.awayteam_squad_text AS awaysquad,
        homesource.squad_string  as homesquad_news, 
        awaysource.squad_string as awaysquad_news, 
        homesource.href as homesquad_news_source, 
        awaysource.href as awaysquad_news_source, 
        awaysource.news_id as away_news_id, 
        homesource.news_id as home_news_id, 
        homesource.header as home_news_header, 
        awaysource.header as away_news_header,
        homesource_team.news_id AS hometeam_news_id,
        awaysource_team.news_id AS awayteam_news_id,
        hometeam_source.squad_string AS hometeam_news,
        awayteam_source.squad_string AS awayteam_news
        
        FROM matchtable m JOIN teamtable home on m.hometeamid = home.teamid 
        JOIN teamtable away on m.awayteamid = away.teamid 
        JOIN leaguetable l ON l.leagueid = m.leagueid
        LEFT JOIN match_observe mo on mo.matchid = m.matchid 
        LEFT JOIN refereetable r on r.refereeid = m.refereeid
        LEFT JOIN news homesource ON homesource.news_id = mo.homesquad_news_source
        LEFT JOIN news awaysource ON awaysource.news_id = mo.awaysquad_news_source
        LEFT JOIN news homesource_team ON homesource_team.news_id = mo.hometeam_news_source
        LEFT JOIN news awaysource_team ON awaysource_team.news_id = mo.awayteam_news_source
        LEFT JOIN city_weatherurl c1 ON c1.city = home.city
        LEFT JOIN city_weatherurl c2 ON c2.city = home.teamname
        LEFT JOIN news hometeam_source ON hometeam_source.news_id = homesource_team.news_id
        LEFT JOIN news awayteam_source ON awayteam_source.news_id = awaysource_team.news_id
        LEFT JOIN derby d ON (d.teamid = home.teamid OR d.teamid = away.teamid) AND (d.teamid2 = home.teamid OR d.teamid2 = away.teamid)
        WHERE m.matchid = $matchid";
        
        
        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $url = '';
            if (isset($row['c2url'])) {
                $url = $row['c2url'];
            } else if (isset($row['c1url'])) {
                $url = $row['c1url'];
            }
            $url = str_replace('ø', '%c3%b8', $url);
            $url = str_replace('æ', '%c3%a6', $url);
            $url = str_replace('å', '%c3%85', $url);
            $url = substr($url, 0, strlen($url)-1);

            $data = array(
                'matchid' => $row['matchid'],
                'home_homepage' => $row['home_homepage'],
                'away_homepage' => $row['away_homepage'],
                'homename' => $row['homename'],
                'homeid' => $row['homeid'],
                'awayid' => $row['awayid'],
                'awayname' => $row['awayname'],
                'dateofmatch' => $row['dateofmatch'],
                'leagueid' => $row['java_variable'],
                'real_leagueid' => $row['leagueid'],
                'timestamp' => $row['timestamp'],
                'surface' => $row['homesurface'],
                'surface_condition' => $row['surface_condition'],
                'awaysurface' => $row['awaysurface'],
                'refereename' => $row['refereename'],
                'refereeid' => $row['refereeid'],
                'url' => $url,
                'level' => $row['level'],
                'homelineup' => $row['homelineup'],
                'awaylineup' => $row['awaylineup'],
                'homesquad' => $row['homesquad'],
                'awaysquad' => $row['awaysquad'],
                'homesquad_news' => $row['homesquad_news'],
                'awaysquad_news' => $row['awaysquad_news'],
                'homesquad_news_source' => $row['homesquad_news_source'],
                'awaysquad_news_source' => $row['awaysquad_news_source'],
                'hometeam_news' => $row['hometeam_news'],
                'awayteam_news' => $row['awayteam_news'],
                'hometeam_news_source' => $row['hometeam_news_id'],
                'awayteam_news_source' => $row['awayteam_news_id'],
                'away_news_id' => $row['away_news_id'],
                'home_news_id' => $row['home_news_id'],
                'home_news_header' => $row['home_news_header'],
                'away_news_header' => $row['away_news_header']
            );
        }
        return $data;
    }

    public function getLastMatch($teamid, $type = '') {
        $andClause = "AND m.`result` NOT LIKE '- : -' ";
        if ($type == '_cup') {
            $andClause = "AND m.dateofmatch < NOW() ";
        }
        $q = "SELECT m.homescore, m.awayscore, m.`hometeamid`, home.`teamname` as homename, m.`awayteamid`, away.teamname as awayname, m.`dateofmatch`, m.`result`, UNIX_TIMESTAMP(m.dateofmatch) * 1000 AS `timestamp`, m.`leagueid`, l.`leaguename` FROM matchtable$type m 
            JOIN teamtable home ON home.`teamid` = m.`hometeamid`
            JOIN teamtable away ON away.`teamid` = m.`awayteamid`
            JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
            WHERE (m.`hometeamid` = $teamid OR m.`awayteamid` = $teamid) $andClause
            ORDER BY m.`dateofmatch` DESC LIMIT 1";

        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $today = time();
            $cdate = $row['timestamp'] / 1000;
            $difference = $cdate - $today;
            $sinceDays = abs(floor($difference / 60 / 60 / 24));

            if ($row['hometeamid'] == $teamid) {
                $opponentId = $row['awayteamid'];
                $opponentName = $row['awayname'];
                $opponentScore = $row['awayscore'];
                $teamScore = $row['homescore'];
                $venue = 'H';
            } else {
                $opponentId = $row['hometeamid'];
                $opponentName = $row['homename'];
                $opponentScore = $row['homescore'];
                $teamScore = $row['awayscore'];
                $venue = 'B';
            }
            $league = MatchObserver::getLeagueShortName($row['leaguename']);

            $data = array(
                'homeid' => $row['hometeamid'],
                'homename' => $row['homename'],
                'awayid' => $row['awayteamid'],
                'result' => $teamScore . ' - ' . $opponentScore . ' (' . $venue . ')',
                'awayname' => $row['awayname'],
                'leaguename' => $league,
                'dateofmatch' => $row['dateofmatch'],
                'timestamp' => $cdate,
                'opponentid' => $opponentId,
                'opponentname' => $opponentName,
                'dayssince' => $sinceDays
            );
        }
        return $data;
    }

    public function getLeagueShortName($leaguename){
        switch($leaguename){
            case 'Tippeligaen': return 'TL';
            case '1.divisjon': return '1.div';
            case 'NM cup': return 'NM';
            case '2.divisjon avdeling 1': return '2div1';
            case '2.divisjon avdeling 2': return '2div2';
            case '2.divisjon avdeling 3': return '2div3';
            case '2.divisjon avdeling 4': return '2div4';
        }
    }
    
    public function getNext($teamid) {
        $nextLeague = MatchObserver::getNextMatch($teamid);
        $nextCup = MatchObserver::getNextMatch($teamid, '_cup');

        if (empty($nextCup)) {
            return $nextLeague;
        }

        if ($nextLeague['timestamp'] > $nextCup['timestamp']) {
            return $nextCup;
        } else {
            return $nextLeague;
        }
    }

    public function getLast($teamid) {
        $lastLeague = MatchObserver::getLastMatch($teamid);
        $lastCup = MatchObserver::getLastMatch($teamid, '_cup');

        if (empty($lastCup)) {
            return $lastLeague;
        }

        if ($lastLeague['timestamp'] < $lastCup['timestamp']) {
            return $lastCup;
        } else {
            return $lastLeague;
        }
    }

    public function getNextMatch($teamid, $type = '') {

        $q = "SELECT m.`hometeamid`, home.`teamname` as homename, m.`awayteamid`, away.teamname as awayname, m.`dateofmatch`, UNIX_TIMESTAMP(m.dateofmatch) * 1000 AS `timestamp`, m.`leagueid`, l.`leaguename` FROM matchtable$type m 
            JOIN teamtable home ON home.`teamid` = m.`hometeamid`
            JOIN teamtable away ON away.`teamid` = m.`awayteamid`
            JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
            WHERE (m.`hometeamid` = $teamid OR m.`awayteamid` = $teamid) AND m.`result` LIKE '- : -'  AND m.dateofmatch > NOW() 
            ORDER BY m.`dateofmatch` ASC LIMIT 1,1";
        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {

            $today = time();
            $cdate = $row['timestamp'] / 1000;
            $difference = $cdate - $today;
            $toDays = abs(floor($difference / 60 / 60 / 24));
            $venue = '';
            if ($row['hometeamid'] == $teamid) {
                $opponentId = $row['awayteamid'];
                $opponentName = $row['awayname'];
                $venue = 'H';
            } else {
                $opponentId = $row['hometeamid'];
                $opponentName = $row['homename'];
                $venue = 'B';
            }

            $league = MatchObserver::getLeagueShortName($row['leaguename']);

            $data = array(
                'homeid' => $row['hometeamid'],
                'homename' => $row['homename'],
                'awayid' => $row['awayteamid'],
                'awayname' => $row['awayname'],
                'leaguename' => $league,
                'dateofmatch' => $row['dateofmatch'],
                'timestamp' => $row['timestamp'],
                'opponentid' => $opponentId,
                'opponentname' => $opponentName . ' (' . $venue . ')',
                'todays' => $toDays
            );
        }
        return $data;
    }

    public function getLatestNews() {
        $q = "SELECT 
            news_id,
            header,
            href,
            `text`,
            lastupdate,
            UNIX_TIMESTAMP(lastupdate) AS `timestamp`,
            `type` 
            FROM
            news n 
            ORDER BY lastupdate DESC 
            LIMIT 5";
        $data = array();

        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[$row['news_id']] = array(
                'header' => $row['header'],
                'href' => $row['href'],
                'text' => $row['text'],
                'lastupdate' => $row['lastupdate'],
                'timestamp' => $row['timestamp'],
                'source' => $row['type']
            );
        }
        return $data;
    }

    public function getNewsText($newsid) {
        $q = "SELECT news_id,header,href,`text`,lastupdate, type, includes_squad FROM news n where n.news_id = $newsid";
        $data = array();

        $result = mysql_query($q);

        while ($row = mysql_fetch_array($result)) {
            $data = array(
                'id' => $row['news_id'],
                'header' => $row['header'],
                'href' => $row['href'],
                'text' => nl2br($row['text']),
                'includes_squad' => $row['includes_squad'],
                'lastupdate' => $row['lastupdate'],
                'source' => $row['type']
            );
        }
        return $data;
    }

    public function getNews($teamid, $source = false) {
        
        $sourceClause = "";
        if($source){
            $sourceClause = " AND type not like 'AFTENPOSTEN' ";
        }
        
        $q = "SELECT news_id,header,href,includes_squad,lastupdate, unix_timestamp(lastupdate) * 1000 as timestamp, 
        type FROM news n JOIN teamtable_news t ON t.`newsid` = n.`news_id` 
        WHERE teamid = $teamid $sourceClause  ORDER BY lastupdate DESC LIMIT 20";
        $data = array();

        $result = mysql_query($q);

        while ($row = mysql_fetch_array($result)) {
            $header = $row['header'];
            $header = str_replace('«', '', $header);
            $header = str_replace('»', '', $header);
            if (mb_strlen($header, 'utf8') >= 43) {
                $header = substr($header, 0, 40) . '...';
                if(!isset($header) || empty($header)){
                    $header = $row['header'];
                }
            }
            $data[] = array(
                'id' => $row['news_id'],
                'header' => $header,
                'href' => $row['href'],
                'lastupdate' => $row['lastupdate'],
                'timestamp' => $row['timestamp'],
                'source' => $row['type'],
                'includes_squad' => $row['includes_squad']
            );
        }
        return $data;
    }

    public function fixSuspension(array $suspension) {
        $retVal = array();
        foreach ($suspension as $key => $array) {
            if ($key == 'redCard') {
                foreach ($suspension['redCard'] as $key => $value) {
                    $retVal[$value['playerid']] = array($value);
                }
            } else {
                $retVal[$key] = $array;
            }
        }

        return $retVal;
    }

    public function getOdds($matchid, $period) {
        $q = "SELECT * FROM odds o where o.matchid = " . $matchid;
        $data = array();
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data['match'] = array(
                'homeprice' => $row['match_homeprice'],
                'awayprice' => $row['match_awayprice'],
                'drawprice' => $row['match_drawprice']
            );
        }
        $q = "SELECT * FROM spreadodds o where o.matchid = " . $matchid . " AND o.period LIKE '$period'";
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data['spread'][] = array(
                'homespread' => $row['homespread'],
                'homeprice' => $row['homeprice'],
                'awayspread' => $row['awayspread'],
                'awayprice' => $row['awayprice'],
                'mainline' => $row['is_main_line'],
                'period' => $row['period']
            );
        }
        $q = "SELECT * FROM totalodds o where o.matchid = " . $matchid . " AND o.period LIKE '$period' ";
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data['total'][] = array(
                'points' => $row['points'],
                'overprice' => $row['overprice'],
                'underprice' => $row['underprice'],
                'mainline' => $row['is_main_line'],
                'period' => $row['period']
            );
        }
        
        $q = "select h.`homespread`, h.`homeprice`, h.`awayspread`, h.`awayprice`, h.`changenumber`, unix_timestamp(h.lastupdate) * 1000 as lastupdate
            from
            spreadodds_history h 
            where h.`matchid` = $matchid 
            and h.`period` = 'MATCH'
            and h.`is_main_line` = 1
            order by h.`changenumber` asc ;
            ";
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data['history'][] = array(
                'changenumber' => $row['changenumber'],
                'homespread' => $row['homespread'],
                'homeprice' => $row['homeprice'],
                'awayspread' => $row['awayspread'],
                'awayprice' => $row['awayprice'],
                'lastupdate' => $row['lastupdate']
            );
        }
        
        return $data;
    }
    public function setCustomLineup($matchid,$type,$text){
        $column = $type.'team_news_source';
        $foundPlayers = MatchObserver::setCustomSource($matchid,$type,$text,11);
        if(isset($foundPlayers['error'])){
            return $foundPlayers;
        }
        MatchObserver::saveCustomNews($foundPlayers,$column,$matchid,$text);
    }
    
    public function setCustomSquad($matchid, $type, $text){
        $column = $type.'squad_news_source';
        $foundPlayers = MatchObserver::setCustomSource($matchid,$type,$text);
        MatchObserver::saveCustomNews($foundPlayers,$column,$matchid,$text);
    }
    
    private function setCustomSource($matchid,$type, $text, $maxPlayers = 100){
        $select = $type.'teamid';
        $q = "SELECT $select FROM matchtable o where o.matchid = " . $matchid;
        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $teamid = $row[$select];
        }
        $array = LineupInfo::getBestSquad($teamid, '>=');   
        
        $playerArray = array();
        foreach($array as $playerid => $player){
            $playerNames[] = $player['playername'];
            $playerNames[] = $player['nickname'];
            $split = explode(' ', $player['playername']);
            if(count($split) >= 2){
                $playerNames[] = $split[1];
                if(count($split) >= 3){
                    $playerNames[] = $split[0] . " " . $split[count($split)-1];
                    $playerNames[] = $split[1] . " " . $split[2];
                    $playerNames[] = $split[count($split)-1];
                }
            }
            $playerArray[$playerid] = $playerNames;
            $playerNames = array();
        }
        
        $foundPlayers = array();
        
        foreach($playerArray as $playerid => $playernameArray){
            foreach($playernameArray as $name){
                if(empty($name)){
                    continue;
                }
                if (stripos($text, $name) !== FALSE){
                    $foundPlayers[$playerid] = $array[$playerid]['playername'];
                }
            }
        }
        if(count($foundPlayers) > $maxPlayers){
            if($maxPlayers == 11){
                if(count($foundPlayers) != 11){
                    return array('error' => 'Not 11 players, but ' . count($foundPlayers) . ': Found players: ' . print_r($foundPlayers,true) . ' Try with only last names.');
                }
            }
            return array('error' => 'Not 11 players, but ' . count($foundPlayers));
        }
        return $foundPlayers;
    }
    
    public function saveCustomNews(array $foundPlayers, $column, $matchid, $text){
        if(!empty($foundPlayers)){
            $playerString = implode('|', $foundPlayers);
            $newsQuery = 'INSERT INTO news (header,text,squad_string) values (\'Egendefinert tropp\',\''.$text.'\',\''.$playerString.'\')';
            mysql_query($newsQuery);
            $id = mysql_insert_id();
            $observeQuery = 'INSERT INTO match_observe (matchid,'.$column.') values ('.$matchid.', '.$id.') ON DUPLICATE KEY UPDATE '.$column.'=VALUES('.$column.')';
            mysql_query($observeQuery);
        }
    }
}

$action = filter_input(INPUT_POST, 'action');
$matchid = filter_input(INPUT_POST, 'matchid');

if ($action == 'getNews') {
    $newsid = filter_input(INPUT_POST, 'newsid', FILTER_VALIDATE_INT);
    echo json_encode(MatchObserver::getNewsText($newsid));
    return;
}


if ($action == 'removeAsSource') {
    $matchid = filter_input(INPUT_POST, 'matchid', FILTER_VALIDATE_INT);
    $column = filter_input(INPUT_POST, 'column', FILTER_DEFAULT);
    $type = filter_input(INPUT_POST, 'type', FILTER_DEFAULT);
    
    $col = $column.$type.'_news_source';
    
    $q = "UPDATE match_observe SET $col=0  WHERE matchid = $matchid";
    mysql_query($q);
    echo 'true';
    return;
}

if($action == 'setNewsSource'){
    $newsid = filter_input(INPUT_POST, 'newsid', FILTER_VALIDATE_INT);
    $matchid = filter_input(INPUT_POST, 'matchid', FILTER_VALIDATE_INT);
    $type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING);
    $column = '';
    if($type == 'home'){
        $column = 'homesquad_news_source';
    }else{
        $column = 'awaysquad_news_source';
    }
    $q = "UPDATE match_observe SET $column=$newsid WHERE matchid=$matchid";
    mysql_query($q);
    echo 'true';
    return;
}

if ($action == 'updatePercentage') {
    $matchid = filter_input(INPUT_POST, 'matchid', FILTER_VALIDATE_INT);
    $homemore = filter_input(INPUT_POST, 'homemore', FILTER_VALIDATE_FLOAT);
    $home = filter_input(INPUT_POST, 'home', FILTER_VALIDATE_FLOAT);
    $draw = filter_input(INPUT_POST, 'draw', FILTER_VALIDATE_FLOAT);
    $away = filter_input(INPUT_POST, 'away', FILTER_VALIDATE_FLOAT);
    $awaymore = filter_input(INPUT_POST, 'awaymore', FILTER_VALIDATE_FLOAT);
    $userid = filter_input(INPUT_POST, 'userid', FILTER_VALIDATE_INT);
    
    $q = "INSERT INTO match_percentage (matchid,homemore,home,draw,away,awaymore,userid) VALUES ($matchid,$homemore,$home,$draw,$away,$awaymore, $userid) ON DUPLICATE KEY UPDATE homemore=VALUES(homemore), home=VALUES(home), draw=VALUES(draw), away=VALUES(away), awaymore=VALUES(awaymore),lastupdate=VALUES(lastupdate)";
    mysql_query($q);
    
    $q = "SELECT homespread,homeprice,awayspread,awayprice FROM spreadodds s WHERE s.matchid = $matchid AND s.period = 'MATCH' AND s.is_main_line = 1";
    $result = mysql_query($q);
    
    $homeprice = 0;
    $awayprice = 0;
    $homespread = 0;
    $awayspread = 0;
    
    while ($row = mysql_fetch_array($result)) {
        $homespread = $row['homespread'];
        $homeprice = $row['homeprice'];
        $awayspread = $row['awayspread'];
        $awayprice = $row['awayprice'];
    }
    $valueHome = MatchObserver::getHomeValue($home, $draw, $homespread, $homeprice);
    $valueAway = MatchObserver::getAwayValue($away, $draw, $awayspread, $awayprice);
    
    $data = array (
            'valuehome' => $valueHome,
            'valueaway' => $valueAway
        );
    
    echo json_encode($data);
    return;
}

if ($action == 'removePlayerFromSource') {
    $matchid = filter_input(INPUT_POST, 'matchid', FILTER_VALIDATE_INT);
    $playername = filter_input(INPUT_POST, 'playername', FILTER_DEFAULT);
    $type = filter_input(INPUT_POST, 'type', FILTER_DEFAULT);
    
    $column = 'awaysquad_news_source';
    if($type == 'home'){
        $column = 'homesquad_news_source';
    }
    
    $q = "SELECT n.`squad_string`, n.news_id FROM match_observe m 
        JOIN news n ON n.`news_id` = m.`$column`
        WHERE m.`matchid` = $matchid;";
    $result = mysql_query($q);
    
    while ($row = mysql_fetch_array($result)) {
        $squadString = $row['squad_string'];
        $newsid = $row['news_id'];
    }
    $newString = str_replace($playername.'|', '', $squadString);
    $q = "UPDATE news SET squad_string='$newString' WHERE news_id=$newsid";
    mysql_query($q);
    echo 'true';
    return;
}

if ($action == 'addPlayerToSource') {
    $matchid = filter_input(INPUT_POST, 'matchid', FILTER_VALIDATE_INT);
    $playername = filter_input(INPUT_POST, 'playername', FILTER_DEFAULT);
    $type = filter_input(INPUT_POST, 'type', FILTER_DEFAULT);
    
    $column = 'awaysquad_news_source';
    if($type == 'home'){
        $column = 'homesquad_news_source';
    }
    
    $q = "SELECT n.`squad_string`, n.news_id FROM match_observe m 
        JOIN news n ON n.`news_id` = m.`$column`
        WHERE m.`matchid` = $matchid";
    $result = mysql_query($q);
    
    $squadString = '';
    $newsid = 0;
    while ($row = mysql_fetch_array($result)) {
        $squadString = $row['squad_string'];
        $newsid = $row['news_id'];
    }
    $newString = $squadString . $playername . '|';
    $q = "UPDATE news SET squad_string='$newString' WHERE news_id=$newsid";
    mysql_query($q);
    echo 'true';
    return;
}
if ($action == 'setTextAreaSource') {
    $matchid = filter_input(INPUT_POST, 'matchid', FILTER_VALIDATE_INT);
    $text = filter_input(INPUT_POST, 'text', FILTER_DEFAULT);
    $type = filter_input(INPUT_POST, 'type', FILTER_DEFAULT);
    
    MatchObserver::setCustomSquad($matchid,$type,$text);
    
    echo 'true';
    return;
}

if ($action == 'setTextAreaTeam') {
    $matchid = filter_input(INPUT_POST, 'matchid', FILTER_VALIDATE_INT);
    $text = filter_input(INPUT_POST, 'text', FILTER_DEFAULT);
    $type = filter_input(INPUT_POST, 'type', FILTER_DEFAULT);
    
    $retVal = MatchObserver::setCustomLineup($matchid,$type,$text);
    
    if(isset($retVal['error'])){
        echo json_encode($retVal);
        return;
    }else{
        echo 'true';
        return;
    }
}

if ($action == 'getInfo') {
    $userid = filter_input(INPUT_POST, 'userid', FILTER_VALIDATE_INT);
    $tights = MatchObserver::getTightFixtures();
    $susp = DatabaseUtils::getAllSuspList();
    $matches = MatchObserver::getMatches($susp, $tights, $userid);
    
    $retval = array(
        'matches' => $matches
    );
    echo json_encode($retval);
    return;
}
if($action == 'getReferee') {
    $refid = filter_input(INPUT_POST, 'refereeid', FILTER_VALIDATE_INT);
    echo json_encode(DatabaseUtils::getRefereeId($refid));
}

if ($action == 'getMatch') {
    $matchInfo = MatchObserver::getMatchInfo($matchid);

    $homeid = $matchInfo['homeid'];
    $awayid = $matchInfo['awayid'];

    $homeNews = MatchObserver::getNews($homeid);
    $awayNews = MatchObserver::getNews($awayid);

    $nextHome = MatchObserver::getNext($homeid);
    $nextAway = MatchObserver::getNext($awayid);

    $lastHome = MatchObserver::getLast($homeid);
    $lastAway = MatchObserver::getLast($awayid);

    $homeLineupInfo = array();
    $awayLineupInfo = array();
    $homeSquadInfo = array();
    $awaySquadInfo = array();
    $homeSquadFotballNO = array();
    $awaySquadFotballNO = array();

    if (!empty($matchInfo['homelineup'])) {
        $homeLineupInfo = LineupInfo::getLineupInfo($homeid, $matchInfo['homelineup']);
        $homeLineupInfo['source'] = 'Fotball.no';
    }else{
        if(!empty($matchInfo['hometeam_news'])){
            $homeLineupInfo = LineupInfo::getLineupInfo($homeid, $matchInfo['hometeam_news']);
            $homeLineupInfo['source'] = $matchInfo['hometeam_news_source'];
        }
        else if (!empty($matchInfo['homesquad'])) {
            $homeSquadFotballNO = LineupInfo::getLineupInfo($homeid, $matchInfo['homesquad']);
        }else{
            if (!empty($matchInfo['homesquad_news'])) {
                $homeSquadInfo = LineupInfo::getLineupInfo($homeid, $matchInfo['homesquad_news']);
            }
        }
    }
    if (!empty($matchInfo['awaylineup'])) {
        $awayLineupInfo = LineupInfo::getLineupInfo($awayid, $matchInfo['awaylineup']);
        $awayLineupInfo['source'] = 'Fotball.no';
    }else{
        if(!empty($matchInfo['awayteam_news'])){
            $awayLineupInfo = LineupInfo::getLineupInfo($awayid, $matchInfo['awayteam_news']);
            $awayLineupInfo['source'] = $matchInfo['awayteam_news_source'];
        }
        if (!empty($matchInfo['awaysquad'])) {
            $awaySquadFotballNO = LineupInfo::getLineupInfo($awayid, $matchInfo['awaysquad']);
        }else{
            if (!empty($matchInfo['awaysquad_news'])) {
                $awaySquadInfo = LineupInfo::getLineupInfo($awayid, $matchInfo['awaysquad_news']);
            }
        }
    }
    
    
    
    $suspension = DatabaseUtils::getSuspList($matchInfo['real_leagueid']);
    $odds = MatchObserver::getOdds($matchid, 'MATCH');

    $from = ($matchInfo['timestamp'] / 1000);
    $to = ($from + 7200);

    $settings = DatabaseSettings::getSettings();
    try{
        $forecastRes = MatchObserver::getWeather($matchInfo['url'], $from, $to, $settings);
    }catch(Exception $e){
        $forecastRes = array();
    }
    
    $retval = array(
        'info' => $matchInfo,
        'homenews' => $homeNews,
        'awaynews' => $awayNews,
        'matchodds' => $odds,
        'nexthome' => $nextHome,
        'nextaway' => $nextAway,
        'lasthome' => $lastHome,
        'lastaway' => $lastAway,
        'forecast' => $forecastRes,
        'homelineup' => $homeLineupInfo,
        'awaylineup' => $awayLineupInfo,
        'homesquad_news' => $homeSquadInfo,
        'awaysquad_news' => $awaySquadInfo,
        'homesquad' => $homeSquadFotballNO,
        'awaysquad' => $awaySquadFotballNO,
        'suspension' => $suspension
    );
    echo json_encode($retval);
}



