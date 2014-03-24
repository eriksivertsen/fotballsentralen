<?php
require_once('db/DatabaseUtils.class.php');
require_once('db/DatabaseTeam.class.php');
require_once('db/DatabaseNationalTeam.class.php');
require_once('db/DatabaseLeague.class.php');
require_once('db/DatabasePreview.class.php');
require_once('db/DatabasePlayer.class.php');
require_once('db/DatabaseAdmin.class.php');
require_once('db/DatabaseScope.class.php');
require_once('db/DatabaseFutsal.class.php');


$dbScope = new DatabaseScope();
$dbUtils = new DatabaseUtils();
$dbTeam = new DatabaseTeam();
$dbNatTeam = new DatabaseNationalTeam();
$dbLeague = new DatabaseLeague();
$dbPlayer = new DatabasePlayer();
$dbPreview = new DatabasePreview();
$dbAdmin= new DatabaseAdmin();
$dbFutsal = new DatabaseFutsal();

if(isset($_POST['action'])){
    
    if($_POST['action'] == 'getTeams'){
        $events = array (
            //Tippeligaen
            'tippeligaen' => $dbLeague->getTeamsJSON(1,$_POST['season']),
            //Adeccoligaen
            '1div ' => $dbLeague->getTeamsJSON(2,$_POST['season']),
            //2.divisjon
            '2div1 ' => $dbLeague->getTeamsJSON(3,$_POST['season']),
            '2div2 ' => $dbLeague->getTeamsJSON(4,$_POST['season']),
            '2div3 ' => $dbLeague->getTeamsJSON(5,$_POST['season']),
            '2div4 ' => $dbLeague->getTeamsJSON(6,$_POST['season']),
            );
        echo json_encode($events);
    }

    if($_POST['action'] == 'getEvents'){
        $events = array (
            'yellow' => $dbTeam->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],2),
            'red' => $dbTeam->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],'3,1'),
            'goal' => $dbTeam->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],4),
            'subin' => $dbTeam->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],6),
            'subout' => $dbTeam->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],7),
            'penalty' => $dbTeam->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],8),
            'owngoal' => $dbTeam->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],9)
         );
        
        echo json_encode($events);
    }
    if($_POST['action'] == 'getEventsTotal'){
        $season = $_POST['season'];
        $leagueid = $_POST['leagueid'];
        if(!isset($leagueid)){
            $leagueid = 0;
        }
        if($_POST['eventtype'] == 11){
            $dbUtils->setEventPageHit(11,$season);
            if($season == 0){
                $season = Constant::ALL_STRING;
            }
            echo json_encode($dbUtils->getTotalPlayerminutes($season,200,$leagueid));
        }
        else{
            $dbUtils->setEventPageHit($_POST['eventtype'],$season);
            if($season == 0){
                $season = Constant::ALL_STRING;
            }
            echo json_encode($dbUtils->getEventInfoTotalJSON($_POST['eventtype'],200,$season,$leagueid));
        }
    }
    if($_POST['action'] == 'getEventsTotalTeam'){
        $season = $_POST['season'];
        $dbUtils->setEventPageTeamHit($_POST['eventtype'],$season);
        if($season == 0){
            $season = Constant::ALL_STRING;
        }
        echo json_encode($dbTeam->getEventInfoTotalTeam($_POST['eventtype'],200,$season,$_POST['leagueid']));
    }
    if($_POST['action'] == 'getPlayerInfo'){
        $events = $dbPlayer->getPlayerInfo($_POST['playerid'],$_POST['season'],$_POST['from'],$_POST['teamid']);
        echo json_encode($events);
    }
    if($_POST['action'] == 'getLeagueInfo'){
        $events = $dbLeague->getLeagueInfo($_POST['leagueid'],$_POST['teamid'],$_POST['season']);
        echo json_encode($events);
    }
    if($_POST['action'] == 'getTeamInfo'){
        $events = $dbTeam->getTeamInfo($_POST['teamid'],$_POST['season'],$_POST['from']);
        echo json_encode($events);
    }
    if($_POST['action'] == 'getNationalTeam'){
       $events = $dbNatTeam->getTeamInfo($_POST['teamid'],$_POST['season']);
       echo json_encode($events);
    }
    if($_POST['action'] == 'getScope'){
        $from = $dbScope->getMYSQLDate($_POST['from']);
        $to = $dbScope->getMYSQLDate($_POST['to']);
        $dbUtils->setHit(-1, 'scope');
        $scopeEvents = $_POST['scopeEvents'];
        $events = $dbScope->getScope($_POST['leagueid'],$from,$to,$scopeEvents);
        echo json_encode($events);
    }
    if($_POST['action'] == 'getScopeTeam'){
        $from = $dbScope->getMYSQLDate($_POST['from']);
        $to = $dbScope->getMYSQLDate($_POST['to']);
        $dbUtils->setHit(-1, 'scope');
        $scopeEvents = $_POST['scopeEvents'];
        $events = $dbScope->getScopeTeam(0,$_POST['teamid'],$from,$to,$scopeEvents);
        echo json_encode($events);
    }
    if($_POST['action'] == 'getScopeDatabase'){
        $dbUtils->setHit(DatabaseScope::alphaID($_POST['urlhash'],true,8), 'scope');
        $events = $dbScope->getScopeDatabase($_POST['urlhash']);
        echo json_encode($events);
    }
    if($_POST['action'] == 'getRandomScope'){
        $events = $dbScope->getRandomScope();
        echo json_encode($events);
    }
    if($_POST['action'] == 'saveScope'){
        $events = $dbScope->saveScope($_POST['scopeHash'],$_POST['scopeEvents'],$_POST['name'],$_POST['scopepublic']);
        echo json_encode($events);
    }
    if($_POST['action'] == 'getPopulare'){
        $ret = array(
            'populare'  => $dbUtils->getPopulare(),
            'trending' => $dbUtils->getTrending()
        );
        echo json_encode($ret);
    }
    if($_POST['action'] == 'getReferee'){
        echo json_encode($dbUtils->getRefereeStats($_POST['season']));
    }
    
    if($_POST['action'] == 'getRefereeId'){
        $dbUtils->setRefereeHit($_POST['referee_id']);
        echo json_encode($dbUtils->getRefereeId($_POST['referee_id']));
    }
    
    if($_POST['action'] == 'getSuspensionList'){
        $dbUtils->setHit($_POST['leagueid'],'suspension');
        echo json_encode($dbUtils->getSuspList($_POST['leagueid']));
    }
    if($_POST['action'] == 'getSearchArray'){
        echo json_encode($dbUtils->getSearchArray());
    }
    if($_POST['action'] == 'getLatestMatches'){
        echo json_encode($dbUtils->getLatestMatches($_POST['season']));
    }
    if($_POST['action'] == 'getMatchesOneWeek'){
        echo json_encode($dbPreview->getMatchesOneWeek());
    }
    if($_POST['action'] == 'getMatchInfo'){
        echo json_encode($dbPreview->getPreview($_POST['matchid']));
    }
    if($_POST['action'] == 'getTransfers'){
        echo json_encode($dbUtils->getTransfers());
    }
    if($_POST['action'] == 'setExternalMatchHit'){
        $dbUtils->setExternalMatchHit($_POST['matchid']);
    }
    if($_POST['action'] == 'getMatch'){
        echo json_encode($dbUtils->getMatch($_POST['matchid']));
    }
    if($_POST['action'] == 'submitFeedback'){
        $dbUtils->submitFeedback($_POST['name'],$_POST['mail'],$_POST['page'],$_POST['msg'],$_POST['rating']);
    }
    if($_POST['action'] == 'getUsersLeague'){
        echo json_encode($dbAdmin->getUsersLeagues($_POST['userid']));
    }
    if($_POST['action'] == 'saveSettings'){
        echo json_encode($dbAdmin->saveSettings($_POST['userid'],stripslashes($_POST['settings'])));
    }
    if($_POST['action'] == 'changePassword'){
        echo json_encode($dbAdmin->changePassword($_POST['userid'],$_POST['password']));
    }
    if($_POST['action'] == 'getFutsalLeague'){
        echo json_encode($dbFutsal->getLeague($_POST['season']));
    }
    if($_POST['action'] == 'getFutsalTeam'){
        echo json_encode($dbFutsal->getTeam($_POST['teamid'],$_POST['season']));
    }
    if($_POST['action'] == 'getFutsalPlayer'){
        echo json_encode($dbFutsal->getPlayer($_POST['playerid'],$_POST['teamid'],$_POST['season']));
    }
}

?>
