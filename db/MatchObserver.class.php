<?php

include "dbConnection.php";
include "yr/autoload.php";

require_once 'DatabaseUtils.class.php';
require_once 'LineupInfo.class.php';

class MatchObserver {

    public function getMatches(array $suspTL, array $susp1div) {

        $settings = MatchObserver::getSettings();

        $q = "SELECT d.level, m.dateofmatch, m.matchid, 
            IF(mo.hometeam_lineup IS NULL,0,mo.hometeam_lineup) as homelineup, 
            IF(mo.awayteam_lineup IS NULL,0,mo.awayteam_lineup) as awaylineup,
            IF(mo.hometeam_squad IS NULL,0,mo.hometeam_squad) as homesquad, 
            IF(mo.awayteam_squad IS NULL,0,mo.awayteam_squad) as awaysquad,
            UNIX_TIMESTAMP(m.dateofmatch) * 1000 as timestamp , home.teamid as homeid, away.teamid as awayid, 
            home.teamname as homename, away.teamname as awayname, l.java_variable,
            home.surface as homesurface, home.surface_condition as homecondition, away.surface as awaysurface, away.surface_condition as awaycondition,
            SUBSTRING_INDEX(c1.url,'/sted/',-1) AS c1url, SUBSTRING_INDEX(c2.url,'/sted/',-1) AS c2url, o.matchid as odds
            FROM matchtable m 
            JOIN teamtable home on m.hometeamid = home.teamid 
            LEFT JOIN match_observe mo ON mo.matchid = m.matchid
            JOIN teamtable away on m.awayteamid = away.teamid 
            JOIN leaguetable l ON l.leagueid = m.leagueid
            LEFT JOIN odds o on o.matchid = m.matchid
            LEFT JOIN city_weatherurl c1 ON c1.city = home.city
            LEFT JOIN city_weatherurl c2 ON c2.city = home.teamname
            LEFT JOIN derby d ON (d.teamid = home.teamid OR d.teamid = away.teamid) AND (d.teamid2 = home.teamid OR d.teamid2 = away.teamid)
            WHERE m.`result` LIKE '- : -' AND m.`dateofmatch` > NOW() AND l.java_variable IN (1,2) 
            GROUP by m.matchid 
            ORDER BY m.dateofmatch ASC, m.matchid LIMIT 100
            ";
        $data = array();

        // Loop throguh suspension and sort list with team => suspensioncount

        $suspension = array();

        foreach ($suspTL as $array) {
            foreach ($array as $player) {
                if (isset($suspension[$player['teamid']])) {
                    $suspension[$player['teamid']] = $suspension[$player['teamid']] + 1;
                } else {
                    $suspension[$player['teamid']] = 1;
                }
            }
        }
        foreach ($susp1div as $array) {
            foreach ($array as $player) {
                if (isset($suspension[$player['teamid']])) {
                    $suspension[$player['teamid']] = $suspension[$player['teamid']] + 1;
                } else {
                    $suspension[$player['teamid']] = 1;
                }
            }
        }

        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            if (isset($data[$row['java_variable']])) {
                if (count($data[$row['java_variable']]) >= 7) {
                    continue;
                }
            }

            $url = '';
            if (isset($row['c2url'])) {
                $url = $row['c2url'];
            } else if (isset($row['c1url'])) {
                $url = $row['c1url'];
            }
            $url = str_replace('ø', '%c3%b8', $url);
            $url = str_replace('æ', '%c3%a6', $url);
            $url = str_replace('å', '%c3%85', $url);
            $url = stripslashes($url);


            $yr = Yr\Yr::create($url, "/tmp", $settings[Constant::SETTING_CACHE]['value'], 'norwegian');
            $from = ($row['timestamp'] / 1000);
            $to = ($from + 7200);
            $forecastRes = array();
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


            $suspHome = 0;
            $suspAway = 0;
            if (isset($suspension[$row['homeid']])) {
                $suspHome = $suspension[$row['homeid']];
            }
            if (isset($suspension[$row['awayid']])) {
                $suspAway = $suspension[$row['awayid']];
            }

            $arrayName = MatchObserver::getArrayName($row['java_variable']);

            $data[$arrayName][] = array(
                'matchid' => $row['matchid'],
                'homename' => $row['homename'],
                'awayname' => $row['awayname'],
                'dateofmatch' => $row['dateofmatch'],
                'leagueid' => $row['java_variable'],
                'timestamp' => $row['timestamp'],
                'homelineup' => $row['homelineup'],
                'awaylineup' => $row['awaylineup'],
                'homesquad' => $row['homesquad'],
                'awaysquad' => $row['awaysquad'],
                'homesurface' => $row['homesurface'],
                'homecondition' => $row['homecondition'],
                'awaysurface' => $row['awaysurface'],
                'awaycondition' => $row['awaycondition'],
                'forecast' => $forecastRes,
                'derby' => $row['level'],
                'totalsusp' => ($suspHome + $suspAway),
                'odds' => $row['odds']
            );
        }
        return $data;
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
        $q = "SELECT d.level,m.dateofmatch, m.matchid, UNIX_TIMESTAMP(m.dateofmatch) * 1000 as timestamp, 
            home.teamname as homename, home.teamid as homeid, away.teamid as awayid, away.teamname as awayname, home.surface as homesurface, home.surface_condition, away.surface as awaysurface,
            l.java_variable, r.refereename, SUBSTRING_INDEX(c1.url,'/sted/',-1) AS c1url, SUBSTRING_INDEX(c2.url,'/sted/',-1) AS c2url,
            mo.hometeam_lineup_text as homelineup, mo.awayteam_lineup_text as awaylineup, m.leagueid, m.refereeid, home.official_homepage as home_homepage, away.official_homepage as away_homepage
            
        FROM matchtable m JOIN teamtable home on m.hometeamid = home.teamid 
            JOIN teamtable away on m.awayteamid = away.teamid 
            JOIN leaguetable l ON l.leagueid = m.leagueid
            LEFT JOIN match_observe mo on mo.matchid = m.matchid 
            LEFT JOIN refereetable r on r.refereeid = m.refereeid
            LEFT JOIN city_weatherurl c1 ON c1.city = home.city
            LEFT JOIN city_weatherurl c2 ON c2.city = home.teamname
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
                'url' => $url,
                'level' => $row['level'],
                'homelineup' => $row['homelineup'],
                'awaylineup' => $row['awaylineup']
            );
        }
        return $data;
    }

    public function getDerbyTable() {
        $q = "SELECT id,team1.teamid as teamid1, team1.teamname as teamname1, team2.teamid as teamid2, team2.teamname as teamname2, derby.level FROM derby JOIN teamtable team1 on team1.teamid = derby.teamid JOIN teamtable team2 on team2.teamid = derby.teamid2 ";
        $data = array();

        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[] = array(
                'id' => $row['id'],
                'teamid1' => $row['teamid1'],
                'teamname1' => $row['teamname1'],
                'teamid2' => $row['teamid2'],
                'teamname2' => $row['teamname2'],
                'level' => $row['level']
            );
        }
        return $data;
    }

    public function getSettings() {
        $q = "SELECT `key`,value, `desc` from settings where value is not null";
        $data = array();

        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[$row['key']] = array(
                'value' => $row['value'],
                'desc' => $row['desc'],
                'key' => $row['key']
            );
        }
        return $data;
    }

    public function getSurface() {
        $q = "SELECT 
            t.*
            FROM
            matchtable m 
            JOIN leaguetable l 
                ON l.`leagueid` = m.`leagueid` 
            JOIN teamtable t 
                ON t.`teamid` = m.`hometeamid` 
            WHERE l.year = 2014 
            AND l.`java_variable` IN (1, 2) 
            GROUP BY m.`hometeamid`";
        $data = array();

        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $data[] = array(
                'teamid' => $row['teamid'],
                'teamname' => $row['teamname'],
                'surface' => $row['surface'],
                'surface_condition' => $row['surface_condition']
            );
        }
        return $data;
    }

    public function getLastMatch($teamid, $type = '') {
        $andClause = "AND m.`result` NOT LIKE '- : -' ";
        if ($type == '_cup') {
            $andClause = "";
        }
        $q = "SELECT m.homescore, m.awayscore, m.`hometeamid`, home.`teamname` as homename, m.`awayteamid`, away.teamname as awayname, m.`dateofmatch`, m.`result`, UNIX_TIMESTAMP(m.dateofmatch) AS `timestamp`, m.`leagueid`, l.`leaguename` FROM matchtable$type m 
            JOIN teamtable home ON home.`teamid` = m.`hometeamid`
            JOIN teamtable away ON away.`teamid` = m.`awayteamid`
            JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
            WHERE (m.`hometeamid` = $teamid OR m.`awayteamid` = $teamid) $andClause
            ORDER BY m.`dateofmatch` DESC LIMIT 1";

        $data = array();

        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {
            $today = time();
            $cdate = $row['timestamp'];
            $difference = $cdate - $today;
            $sinceDays = abs(floor($difference / 60 / 60 / 24));

            if ($row['hometeamid'] == $teamid) {
                $opponentId = $row['awayteamid'];
                $opponentName = $row['awayname'];
                $opponentScore = $row['awayscore'];
                $teamScore = $row['homescore'];
                $venue = 'hjemme';
            } else {
                $opponentId = $row['hometeamid'];
                $opponentName = $row['homename'];
                $opponentScore = $row['homescore'];
                $teamScore = $row['awayscore'];
                $venue = 'borte';
            }
            $data = array(
                'homeid' => $row['hometeamid'],
                'homename' => $row['homename'],
                'awayid' => $row['awayteamid'],
                'result' => $teamScore . ' - ' . $opponentScore . ' (' . $venue . ')',
                'awayname' => $row['awayname'],
                'leaguename' => $row['leaguename'],
                'dateofmatch' => $row['dateofmatch'],
                'timestamp' => $cdate,
                'opponentid' => $opponentId,
                'opponentname' => $opponentName,
                'dayssince' => $sinceDays
            );
        }
        return $data;
    }

    public function getNext($teamid) {
        $nextLeague = MatchObserver::getNextMatch($teamid);
        $nextCup = MatchObserver::getNextMatch($teamid, '_cup');

//        var_dump($nextLeague);
//        var_dump($nextCup);

        if (empty($nextCup)) {
            return $nextLeague;
        }

        if ($nextLeague['timestamp'] < $nextCup['timestamp']) {
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

        $limit = '1,1';
        if ($type == '_cup') {
            $limit = '1';
        }

        $q = "SELECT m.`hometeamid`, home.`teamname` as homename, m.`awayteamid`, away.teamname as awayname, m.`dateofmatch`, UNIX_TIMESTAMP(m.dateofmatch) AS `timestamp`, m.`leagueid`, l.`leaguename` FROM matchtable$type m 
            JOIN teamtable home ON home.`teamid` = m.`hometeamid`
            JOIN teamtable away ON away.`teamid` = m.`awayteamid`
            JOIN leaguetable l ON l.`leagueid` = m.`leagueid`
            WHERE (m.`hometeamid` = $teamid OR m.`awayteamid` = $teamid) AND m.`result` LIKE '- : -' 
            ORDER BY m.`dateofmatch` ASC LIMIT $limit";
        $data = array();

        $result = mysql_query($q);
        while ($row = mysql_fetch_array($result)) {

            $today = time();
            $cdate = $row['timestamp'];
            $difference = $cdate - $today;
            $toDays = abs(floor($difference / 60 / 60 / 24));
            if ($row['hometeamid'] == $teamid) {
                $opponentId = $row['awayteamid'];
                $opponentName = $row['awayname'];
            } else {
                $opponentId = $row['hometeamid'];
                $opponentName = $row['homename'];
            }

            $data = array(
                'homeid' => $row['hometeamid'],
                'homename' => $row['homename'],
                'awayid' => $row['awayteamid'],
                'awayname' => $row['awayname'],
                'leaguename' => $row['leaguename'],
                'dateofmatch' => $row['dateofmatch'],
                'timestamp' => $row['timestamp'],
                'opponentid' => $opponentId,
                'opponentname' => $opponentName,
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
        $q = "SELECT news_id,header,href,`text`,lastupdate, type FROM news n where n.news_id = $newsid";
        $data = array();

        $result = mysql_query($q);

        while ($row = mysql_fetch_array($result)) {
            $data = array(
                'id' => $row['news_id'],
                'header' => $row['header'],
                'href' => $row['href'],
                'text' => $row['text'],
                'lastupdate' => $row['lastupdate'],
                'source' => $row['type']
            );
        }
        return $data;
    }

    public function getPlayers() {
        $q = "SELECT 
            p.*, t.teamname 
            FROM
            matchtable m 
            JOIN leaguetable l 
            ON l.`leagueid` = m.`leagueid` 
            JOIN playertable p ON p.`teamid` = m.`hometeamid` AND p.`year` = l.year
            JOIN teamtable t on t.teamid = p.teamid
            WHERE l.year = " . Constant::CURRENT_YEAR . " " .
                "AND l.`java_variable` IN (1, 2) 
            GROUP BY p.`playerid`
            ORDER BY t.teamname, p.playername";

        $data = array();

        $result = mysql_query($q);

        while ($row = mysql_fetch_array($result)) {
            $data[] = array(
                'playerid' => $row['playerid'],
                'playername' => $row['playername'],
                'teamname' => $row['teamname'],
                'key' => $row['key']
            );
        }
        return $data;
    }

    public function getNews($teamid) {
        $q = "SELECT news_id,header,href,includes_squad,lastupdate, unix_timestamp(lastupdate) * 1000 as timestamp, type FROM news n JOIN teamtable_news t ON t.`newsid` = n.`news_id` WHERE teamid = $teamid ORDER BY lastupdate AND header not like '' DESC LIMIT 6";
        $data = array();

        $result = mysql_query($q);

        while ($row = mysql_fetch_array($result)) {
            $header = $row['header'];
            if (strlen($header) >= 43) {
                $header = substr($header, 0, 39) . '...';
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
        return $data;
    }

}

$action = filter_input(INPUT_POST, 'action');
$matchid = filter_input(INPUT_POST, 'matchid');
if ($action == 'getNews') {
    $newsid = filter_input(INPUT_POST, 'newsid', FILTER_VALIDATE_INT);
    echo json_encode(MatchObserver::getNewsText($newsid));
    return;
}
if ($action == 'setSurface') {
    $teamid = filter_input(INPUT_POST, 'teamid', FILTER_VALIDATE_INT);
    $condition = filter_input(INPUT_POST, 'surface_condition', FILTER_SANITIZE_STRING);
    $q = "UPDATE teamtable SET surface_condition='$condition' WHERE teamid = $teamid";
    mysql_query($q);
    return;
}
if ($action == 'setKey') {
    $playerid = filter_input(INPUT_POST, 'playerid', FILTER_VALIDATE_INT);
    $key = filter_input(INPUT_POST, 'key', FILTER_DEFAULT);
    $q = "UPDATE playertable SET `key`='$key' WHERE playerid = $playerid";
    mysql_query($q);
    echo 'true';
    return;
}

if ($action == 'updateSettings') {
    $value = filter_input(INPUT_POST, 'value', FILTER_DEFAULT);
    $key = filter_input(INPUT_POST, 'key', FILTER_DEFAULT);
    $q = "UPDATE settings SET `value`= '$value' WHERE `key` = '$key'";
    mysql_query($q);
    echo 'true';
    return;
}

if ($action == 'saveDerbyLevel') {
    $derbyid = filter_input(INPUT_POST, 'derbyid', FILTER_VALIDATE_INT);
    $condition = filter_input(INPUT_POST, 'level', FILTER_SANITIZE_STRING);
    $q = "UPDATE derby SET level='$condition' WHERE id = $derbyid";
    mysql_query($q);
    return;
}
if ($action == 'deleteDerby') {
    $derbyid = filter_input(INPUT_POST, 'derbyid', FILTER_VALIDATE_INT);
    $q = "DELETE FROM derby WHERE id = $derbyid";
    mysql_query($q);
    echo 'true';
    return;
}
if ($action == 'saveDerby') {
    $team1 = filter_input(INPUT_POST, 'team1', FILTER_VALIDATE_INT);
    $team2 = filter_input(INPUT_POST, 'team2', FILTER_VALIDATE_INT);
    $level = filter_input(INPUT_POST, 'level', FILTER_SANITIZE_STRING);
    $q = "INSERT INTO derby (teamid,teamid2,level) VALUES ($team1,$team2,'$level')";
    mysql_query($q);
    echo mysql_insert_id();
    return;
}
if ($action == 'getInfo') {
    $suspTL = DatabaseUtils::getSuspList(Constant::CURRENT_TIPPELIGA);
    $susp1div = DatabaseUtils::getSuspList(Constant::CURRENT_1DIV);

    $matches = MatchObserver::getMatches($suspTL, $susp1div);

    $retval = array(
        'matches' => $matches,
        'derby' => MatchObserver::getDerbyTable(),
        'surface' => MatchObserver::getSurface(),
        'players' => MatchObserver::getPlayers(),
        'settings' => MatchObserver::getSettings()
    );
    echo json_encode($retval);
    return;
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

    if (!empty($matchInfo['homelineup'])) {
        $homeLineupInfo = LineupInfo::getLineupInfo($homeid, $matchInfo['homelineup']);
    }

    if (!empty($matchInfo['awaylineup'])) {
        $awayLineupInfo = LineupInfo::getLineupInfo($awayid, $matchInfo['awaylineup']);
    }

    $suspension = DatabaseUtils::getSuspList($matchInfo['real_leagueid']);
    $odds = MatchObserver::getOdds($matchid, 'MATCH');
    $firsthalfodds = MatchObserver::getOdds($matchid, 'FIRST_HALF');

    $settings = MatchObserver::getSettings();
    $yr = Yr\Yr::create($matchInfo['url'], "/tmp", $settings[Constant::SETTING_CACHE]['value'], 'norwegian');

    $forecastRes = array();
    $from = ($matchInfo['timestamp'] / 1000);
    $to = ($from + 7200);
    foreach ($yr->getHourlyForecasts(strtotime("now"), strtotime("tomorrow") + 7000222) as $forecast) {
        $timestamp = $forecast->getFrom()->getTimestamp();
        if ($timestamp > $from && $timestamp < $to) {
            $forecastRes = array(
                'from' => $forecast->getFrom(),
                'temperature' => $forecast->getTemperature(),
                'symbol' => $forecast->getSymbol(),
                'wind_speed' => $forecast->getWindSpeed('name'),
                'wind_speed_mps' => $forecast->getWindSpeed('mps'),
                'precipitation' => $forecast->getPrecipitation()
            );
        }
    }

    $retval = array(
        'info' => $matchInfo,
        'homenews' => $homeNews,
        'awaynews' => $awayNews,
        'matchodds' => $odds,
        'firsthalfodds' => $firsthalfodds,
        'nexthome' => $nextHome,
        'nextaway' => $nextAway,
        'lasthome' => $lastHome,
        'lastaway' => $lastAway,
        'forecast' => $forecastRes,
        'homelineup' => $homeLineupInfo,
        'awaylineup' => $awayLineupInfo,
        'suspension' => $suspension
    );
    echo json_encode($retval);
}



