<?php
require_once('db/DatabaseUtils.class.php');
require_once('db/DatabaseTeam.class.php');
require_once('db/DatabaseLeague.class.php');
require_once('db/DatabasePreview.class.php');
require_once('db/DatabasePlayer.class.php');


$dbUtils = new DatabaseUtils();
$dbTeam = new DatabaseTeam();
$dbLeague = new DatabaseLeague();
$dbPlayer = new DatabasePlayer();
$dbPreview = new DatabasePreview();

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
        $dbUtils->setEventPageHit($_POST['eventtype']);
        echo json_encode($dbUtils->getEventInfoTotalJSON($_POST['eventtype'],200,$_POST['season'],$_POST['leagueid']));
    }
    if($_POST['action'] == 'getTotalPlayerminutes'){
        $dbUtils->setEventPageHit(11);
        echo json_encode($dbUtils->getTotalPlayerminutes($_POST['season'],200,$_POST['leagueid']));
    }
    if($_POST['action'] == 'getEventsTotalTeam'){
        $dbUtils->setEventPageTeamHit($_POST['eventtype']);
        echo json_encode($dbTeam->getEventInfoTotalTeam($_POST['eventtype'],200,$_POST['season'],$_POST['leagueid']));
    }
    if($_POST['action'] == 'getPlayerInfo'){
        $events = $dbPlayer->getPlayerInfo($_POST['playerid'],$_POST['season'],$_POST['from']);
        echo json_encode($events);
    }
    if($_POST['action'] == 'getLeagueInfo'){
        $events = $dbLeague->getLeagueInfo($_POST['leagueid'],$_POST['teamid'],$_POST['season']);
        echo json_encode($events);
    }
    if($_POST['action'] == 'getTeamInfo'){
        $from = $_POST['from'];
         if(isset($from) && !empty($from)){
            DatabaseUtils::setTeamSearchHit($_POST['teamid']);
        }else{
            DatabaseUtils::setTeamHit($_POST['teamid']);
        }
        $events = $dbTeam->getTeamInfo($_POST['teamid'],$_POST['season']);
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
        echo json_encode($dbUtils->getSuspList($_POST['leagueid']));
    }
    if($_POST['action'] == 'getSearchArray'){
        echo json_encode($dbUtils->getSearchArray());
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
}


?>
