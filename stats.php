<?php

require_once('db/DatabaseStats.class.php');
require_once('db/DatabaseTeam.class.php');
require_once('db/DatabasePreview.class.php');
require_once('db/DatabaseUtils.class.php');
require_once('db/DatabaseLeague.class.php');
echo "<head>
<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\"/>
<title>Stats</title>
</head>";
ini_set('max_execution_time', 300); 

$db = new DatabaseStats();
$dbT = new DatabaseTeam();
$dbP = new DatabasePreview();
$dbU = new DatabaseUtils();

$clicktable = $db->getClickTable();
$dailyClick = $db->getDailyClicks();
$uniqueVisitors = $db->getUniqueVisitors();

//$teams = $dbC->getTeamsJSON(2, 2013);

echo '<br/>';
    
//$matchArray = $dbP->getMatchesOneWeek();
//$susps = array();
//$susps['134371'] = $dbU->getSuspList(134371);
//$susps['134372'] = $dbU->getSuspList(134372);
//$susps['134373'] = $dbU->getSuspList(134373);
//$susps['134374'] = $dbU->getSuspList(134374);
//
//$suspAr = array();
//foreach($susps as $league){
//    foreach($league as $key => $array){
//        if($key == 'threeYellow' || $key == 'redCard' || $key == 'fiveYellow'){
//        foreach($array as $val){
//                $suspAr[$val['teamid']][] = $val;
//            } 
//        }
//    }
//}
//
//foreach($matchArray as $match){
//    $year = 2013;
//    $teamArray = $dbP->getMatchInfo($match['matchid']);
//    
//    $homeTeam = $dbT->getOverGoals($teamArray['hometeamid'], $year,'home');
//    $awayTeam = $dbT->getOverGoals($teamArray['awayteamid'], $year,'away');
//    
//    $homeSuspCount = (isset($suspAr[$teamArray['hometeamid']]) ? count($suspAr[$teamArray['hometeamid']]) : 0);
//    $awaySuspCount = (isset($suspAr[$teamArray['awayteamid']]) ? count($suspAr[$teamArray['awayteamid']]) : 0);
//    
//    $homePos = $dbT->getLeaguePosition($teamArray['hometeamid'],$teamArray['java_variable'],2013,'home');
//    $awayPos = $dbT->getLeaguePosition($teamArray['awayteamid'],$teamArray['java_variable'],2013,'away');
//    
//    if($homePos < 5 && $awayPos > 13){
//        echo 'Solid hometeam('.$homePos.') vs weak awayteam ('.$awayPos.'): ' .  $match['matchid'] . '  ' . $teamArray['dateofmatch'] .'  <br/>';
//    }
//    if($homeTeam['over3'] >= 80 && $awayTeam['over3'] >= 80){
//        echo 'Over 2.5 goals: ' . $match['matchid'] . ': ' .$homeTeam['over3'] .'%-'.$awayTeam['over3'].'% - '.$teamArray['dateofmatch'] .'  <br/>';
//    }
//    if($homeTeam['over4'] >= 80 && $awayTeam['over4'] >= 80){
//        echo 'Over 3.5 goals: ' . $match['matchid'] . ': ' .$homeTeam['over4'] .'%-'.$awayTeam['over4'].'% - '.$teamArray['dateofmatch'] .'   <br/>';
//    }
//    if($homeTeam['over3'] <= 30 && $awayTeam['over3'] <= 30){
//        echo 'Under 2.5 goals: ' . $match['matchid'] . ': ' .$homeTeam['over3'] .'%-'.$awayTeam['over3'].'% - '.$teamArray['dateofmatch'] .'   <br/>';
//    }
//    if($homeTeam['over4'] <= 30 && $awayTeam['over4'] <= 30){
//        echo 'Under 3.5 goals: ' . $match['matchid'] . '  ' .$homeTeam['over4'] .'%-'.$awayTeam['over4'].'% - '.$teamArray['dateofmatch'] .'   <br/>';
//    }
//    if($homeSuspCount >= 2 && $awaySuspCount < 1){ //  
//        echo 'Suspensions hometeam: ' . $homeSuspCount . ' - awayTeam: ' .$awaySuspCount . ' - ' . $match['matchid'] . ' ' .$teamArray['dateofmatch'] . ' <br/>';
//    }
//    if($awaySuspCount >= 2 && $homeSuspCount < 1){ //  
//        echo 'Suspensions hometeam: ' . $homeSuspCount . ' - awayTeam: ' .$awaySuspCount . ' - ' . $match['matchid'] . ' ' .$teamArray['dateofmatch'] . ' <br/>';
//    }
//}


echo 'Latest Observer update: ' . $db->getLatestObserverDate() . ' <br/>';
$db->printTable($clicktable,'clicktable');
$db->printTable($dailyClick,'daily clicks');
$db->printTable($uniqueVisitors,'unique visitors');







