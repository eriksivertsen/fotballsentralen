<?php
require_once('db/Database.class.php');

$db = new Database();

if(isset($_POST['action'])){
    
    if($_POST['action'] == 'getTeams'){
        $events = array (
            //Tippeligaen
            'tippeligaen' => $db->getTeamsJSON(1,$_POST['season']),
            //Adeccoligaen
            '1div ' => $db->getTeamsJSON(2,$_POST['season']),
            //2.divisjon
            '2div1 ' => $db->getTeamsJSON(3,$_POST['season']),
            '2div2 ' => $db->getTeamsJSON(4,$_POST['season']),
            '2div3 ' => $db->getTeamsJSON(5,$_POST['season']),
            '2div4 ' => $db->getTeamsJSON(6,$_POST['season']),
            );
        echo json_encode($events);
    }

    if($_POST['action'] == 'getEvents'){
        $events = array (
            'yellow' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],2),
            'red' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],'3,1'),
            'goal' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],4),
            'subin' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],6),
            'subout' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],7),
            'penalty' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],8),
            'owngoal' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],9)
         );
        
        echo json_encode($events);
    }
    if($_POST['action'] == 'getEventsTotal'){
        echo json_encode($db->getEventInfoTotalJSON($_POST['eventtype'],200,$_POST['season']));
    }
    if($_POST['action'] == 'getTotalPlayerminutes'){
        echo json_encode($db->getTotalPlayerminutes($_POST['season'],200));
    }
    if($_POST['action'] == 'getEventsTotalTeam'){
        echo json_encode($db->getEventInfoTotalTeam($_POST['eventtype'],200,$_POST['season']));
    }
    if($_POST['action'] == 'getPlayerInfo'){
        $db->setPlayerHit($_POST['playerid']);
        $events = array (
            'yellow' => $db->getEventRankPlayer($_POST['playerid'],2,$_POST['season']),
            'red' => $db->getEventRankPlayer($_POST['playerid'],3,$_POST['season']),
            'goal' => $db->getEventRankPlayer($_POST['playerid'],4,$_POST['season']),
            'subin' => $db->getEventRankPlayer($_POST['playerid'],6,$_POST['season']),
            'subout' => $db->getEventRankPlayer($_POST['playerid'],7,$_POST['season']),
            'penalty' => $db->getEventRankPlayer($_POST['playerid'],8,$_POST['season']),
            'owngoal' => $db->getEventRankPlayer($_POST['playerid'],9,$_POST['season']),
            'playerinfo' => $db->getPlayerInfoJSON($_POST['playerid'],$_POST['season']),
            'playertoleague' => $db->getPlayerToLeague($_POST['playerid'],$_POST['season']),
            'playingminutes' => $db->getPlayingMinutes($_POST['playerid'], $_POST['season']),
            'winpercentage' => $db->getWinPercentageFromStart($_POST['playerid'], $_POST['season']),
            'info' => $db->getPlayerNifsInfo($_POST['playerid']),
            'similar' => $db->getSimilarPlayers($_POST['playerid'])
         );
        echo json_encode($events);
    }
    if($_POST['action'] == 'getLeagueInfo'){
        $db->setLeagueHit($_POST['leagueid']);
        $events = array (
            'lastupdate' => $db->getLastUpdate($_POST['leagueid'],$_POST['season']),
            'yellow' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],2,$_POST['season']),
            'red' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],'3,1',$_POST['season']),
            'goal' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],4,$_POST['season']),
            'subin' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],6,$_POST['season']),
            'subout' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],7,$_POST['season']),
            'penalty' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],8,$_POST['season']),
            'owngoal' => $db->getEventInfoJSON($_POST['teamid'],$_POST['leagueid'],9,$_POST['season']),
            'minutes' => $db->getPlayingMinutesJSON($_POST['teamid'],$_POST['leagueid'],$_POST['season']),
            'topscorer' => $db->getEventInfoJSON(0, $_POST['leagueid'], '4,8', $_POST['season']),
            'topscorercount' => $db->getTopscorerCount(0,$_POST['leagueid'],$_POST['season']),
            'hometeam' => $db->getBestHometeam($_POST['leagueid'], $_POST['season']),
            'awayteam' => $db->getBestAwayteam($_POST['leagueid'], $_POST['season'])
         );
        echo json_encode($events);
    }
    if($_POST['action'] == 'getTeamInfo'){
        $db->setTeamHit($_POST['teamid']);
        $events = array (
            'teamtoleague' => $db->getTeamToLeague($_POST['teamid'],$_POST['season']),
            'yellow' => $db->getEventRankTeam($_POST['teamid'],2,$_POST['season']),
            'red' => $db->getEventRankTeam($_POST['teamid'],3,$_POST['season']),
            'goal' => $db->getEventRankTeam($_POST['teamid'],4,$_POST['season']),
            'subin' => $db->getEventRankTeam($_POST['teamid'],6,$_POST['season']),
            'subout' => $db->getEventRankTeam($_POST['teamid'],7,$_POST['season']),
            'penalty' => $db->getEventRankTeam($_POST['teamid'],8,$_POST['season']),
            'owngoal' => $db->getEventRankTeam($_POST['teamid'],9,$_POST['season']),
            'teamplayer' => $db->getTeamPlayerJSON($_POST['teamid'] ,$_POST['season']),
            'scoringminute' => $db->getGoalsScoringMinute($_POST['teamid'],$_POST['season']),
            'concededminute' => $db->getGoalsConcededMinute($_POST['teamid'],$_POST['season']),
            'topscorer' => $db->getMostEvents($_POST['teamid'], $_POST['season'],'4,8'),
            'topscorercount' => $db->getTopscorerCount($_POST['teamid'],0,$_POST['season']),
            'mostminutes' => $db->getMostMinutes($_POST['teamid'], $_POST['season']),
            //'winstreak' => $db->getBestWinStreak($_POST['teamid'], $_POST['season']),
            'cleansheets' => $db->getCleanSheets($_POST['teamid'], $_POST['season']),
            'overgoals' => $db->getOverGoals($_POST['teamid'], $_POST['season']),
            'mostyellow' => $db->getMostEvents($_POST['teamid'], $_POST['season'],2),
            'mostred' => $db->getMostEvents($_POST['teamid'], $_POST['season'],'1,3'),
            'nextmatches' => $db->getNextMatches($_POST['teamid']),
            'latestmatches' => $db->getLatestMatches($_POST['teamid']),
            'homestats' => $db->getHomestats($_POST['teamid'], $_POST['season']),
            'awaystats' => $db->getAwaystats($_POST['teamid'], $_POST['season']),
            'allmatches' => $db->getAllMatches($_POST['teamid'], $_POST['season'])
         );
        echo json_encode($events);
    }
    if($_POST['action'] == 'getPopulare'){
        echo json_encode($db->getPopulare());
    }
    if($_POST['action'] == 'getSuspensionList'){
        echo json_encode($db->getSuspList($_POST['leagueid']));
    }
    if($_POST['action'] == 'getSearchArray'){
        echo json_encode($db->getSearchArray());
    }
}

?>
