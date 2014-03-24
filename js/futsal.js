
function getFutsalLeague(){
    if(!allowClicks){
        return;
    }
    window.location.hash = '/'+season+'/futsal/';
    $('#feedback_page').val('Futsal');
    startLoad();
    
    leagueidselected = 12;
    
    updateBreadcrumbSpecific("Futsal","getFutsalLeague();return false");
    
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getFutsalLeague",  season: season},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            if (json.leaguetable == undefined || json.leaguetable.length == 0){
                $('#noData').show();
                stopLoad();
                return;
            }
            $('[id^="futsal_league"]').show();
            var ses = season;
            if(season == 2012){
                ses = '2012/2013';
            }else if(season == 2013){
                ses = '2013/2014';
            }else if(season == 2015){
                ses = '2014/2015';
            }else if(season == 0){
                ses = 'totalt';
            }
            $('#futsal_league_name').html('Eliteserien Futsal ' + ses);
            updateLeagueTable(json.leaguetable,$('#futsal_league_table'),'Tabell');
            updateEventTableMin(json.topscorer,$('#futsal_league_totalgoals'),10);
            updateEventTableMin(json.yellowcard, $('#futsal_league_yellowcard'), 2);
            updateEventTableMin(json.redcard, $('#futsal_league_redcard'), 13);
            stopLoad();
        }
    }); 
}

function getFutsalTeam(teamid)
{
     if(!allowClicks){
        return;
    }
    window.location.hash = '/'+season+'/futsal/team/'+teamid;
    $('#feedback_page').val('Futsal-lag');
    startLoad();
    teamidselected = teamid;
    updateBreadcrumbSpecific("Futsal","getFutsalLeague();return false;");
    
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getFutsalTeam",  season: season, teamid: teamid},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            if (json.leaguetable == undefined || json.leaguetable.length == 0){
                $('#noData').show();
                stopLoad();
                return;
            }
            $('[id^="futsal_team"]').show();
            var teamname = json.teamplayer[0].teamname;
            updatePlayers(json.teamplayer);
            updateLeagueTable(json.leaguetable,$('#futsal_team_leaguetable'),'Tabell',teamid);
            updateBreadcrumbSpecific("Futsal","getFutsalLeague();return false;",""+teamname,"getFutsalTeam("+teamid+");return false;");
            setFutsalTeamLogo($('#futsal_team_logo'),teamid);
            $('#futsal_team_teamname').html(teamname);
            
            if(json.topscorer.length != 0){ 
                $('#futsal_team_topscorer').html(getFutsalPlayerLink(json.topscorer[0].playerid,json.topscorer[0].playername)+' - ' +json.topscorer[0].events+' mål');
            }
            if(json.mostyellow.length != 0){ 
                $('#futsal_team_yellow').html(getFutsalPlayerLink(json.mostyellow[0].playerid,json.mostyellow[0].playername)+' - ' +json.mostyellow[0].events+' gule');
            }else{
                $('#futsal_team_yellow').html('');
            }
            if(json.mostred.length != 0){ 
                $('#futsal_team_red').html(getFutsalPlayerLink(json.mostred[0].playerid,json.mostred[0].playername)+' - ' +json.mostred[0].events+' røde');
            }else{
                $('#futsal_team_red').html('');
            }
            updateAllFutsalMatches($('#futsal_team_allmatches'),json.allmatches, json.goalscorers, teamid);
            stopLoad();
        }
    }); 
}

function updateAllFutsalMatches(tablename, array, scorers, selectedteamid)
{
    tablename.empty();
    tablename.append('<caption class="tableheader">Alle kamper</caption>');
    tablename.append(getTableHeader(['Dato','Hjemmelag&nbsp','Bortelag&nbsp','Resultat','Målscorere']))
    tablename.append('<tbody>');
    for (var i=0; i<array.length; i++) {
        var scorerArray = [];
        var scorer = scorers[array[i].matchid];
        if(scorer !== undefined){
            for(var player in scorer){
                var p = scorer[player];
                var eventtype = p.eventtype;
                var teamid = p.teamid;
                
                var playerArr = p.playername.split(" ");
                var playername = playerArr[playerArr.length-1];
                
                if(teamid == selectedteamid){
                    if(eventtype != 9){
                        if(scorerArray[p.playerid] != undefined){
                            scorerArray[p.playerid].goal = scorerArray[p.playerid].goal+1;
                        }else{
                            var obj = new Object();
                            obj.name = getFutsalPlayerLink(p.playerid, playername) + " (";
                            obj.goal = 1;
                            scorerArray[p.playerid] = obj;
                        }
                    }
                }else{
                    if(eventtype == 9){
                        playername = 'Selvmål (';
                        if(scorerArray[p.playerid] != undefined){
                            scorerArray[p.playerid].goal = scorerArray[p.playerid].goal+1;
                        }else{
                            var obja = new Object();
                            obja.name = playername;
                            obja.goal = 1;
                            scorerArray[p.playerid] = obja;
                        }
                    }
                }
            }
        }
        var scorerstring = '';
        for(var s in scorerArray){
            scorerArray[s].name += scorerArray[s].goal + ' mål) ';
            scorerstring += scorerArray[s].name;
        }
        tablename.append(
            '<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
            '<td>'+getDateStringMilli(array[i].timestamp)+'</td>'+
            '<td>'+getFutsalTeamLink(array[i].homeid,array[i].homename)+'</td>'+
            '<td>'+getFutsalTeamLink(array[i].awayid,array[i].awayname)+'</td>'+
            '<td>'+getMatchLinkText(array[i].matchid,array[i].result)+'</td>'+
            '<td>'+scorerstring+'</td>'+
            '</tr>');
    }
    tablename.append('</tbody>');
    
    if(array.length != 0){
        tablename.show();
    }
}

function updatePlayers(teamidarray)
{
    var array = teamidarray;
    $('#futsal_team_teamplayerinfo').empty();    
    $('#futsal_team_teamplayerinfo').show(); 
    $('#futsal_team_teamplayerinfo').append('<thead><th>Navn</th><th>Mål&nbsp&nbsp&nbsp</th><th>Straffemål&nbsp&nbsp&nbsp</th><th>Selvmål&nbsp&nbsp&nbsp</th><th>Gule&nbspkort&nbsp&nbsp&nbsp</th><th>Røde&nbspkort&nbsp&nbsp&nbsp</th></thead>');
    $('#futsal_team_teamplayerinfo').append('<tbody>');


    var goals = 0;
    var penalty = 0;
    var yellow = 0;
    var red = 0;
    var owngoals = 0;

    for (var i=0; i<array.length; i++) {
        
        if(array[i].playerid == null || array[i].playerid == -1){
            continue;
        }
        
        array[i].goals = getCount(array[i].goals);
        array[i].penalty = getCount(array[i].penalty);
        array[i].owngoals = getCount(array[i].owngoals);
        array[i].yellowcards = getCount(array[i].yellowcards);
        array[i].redcards = getCount(array[i].redcards);
        
        $('#futsal_team_teamplayerinfo').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
            '<td>'+getFutsalPlayerLink(array[i].playerid,array[i].playername)+'</td>'+
            '<td>'+array[i].goals+'</td>'+
            '<td>'+array[i].penalty+'</td>'+
            '<td>'+array[i].owngoals+'</td>'+
            '<td>'+array[i].yellowcards+'</td>'+
            '<td>'+array[i].redcards+'</td>'+
            '</tr>');

        goals += parseInt(array[i].goals);
        penalty += parseInt(array[i].penalty);
        red += parseInt(array[i].redcards);
        yellow += parseInt(array[i].yellowcards);
        owngoals += parseInt(array[i].owngoals);
        
    }
    
    $('#futsal_team_teamplayerinfo').append('<tr><td><b>Totalt</b></td>'+
    '<td><b>'+goals+'</b></td><td><b>'+penalty+'</b></td><td><b>'+owngoals+'</b></td><td><b>'+yellow+'</b></td><td><b>'+red+'</b></td></tr>');
    $('#futsal_team_teamplayerinfo').append('</tbody>');
    $('#futsal_team_teamplayerinfo').tablesorter({widgets: ['zebra']});
    $('#futsal_team_yellowcard').html(yellow);
    $('#futsal_team_redcard').html(red);
}

function selectFutsalPlayerTeam(){
    getFutsalPlayer(playeridselected, $('#futsal_player_teamselect').val());
}

function getFutsalPlayer(playerid,teamid)
{
    if(!allowClicks){
        return;
    }
    teamid = teamid || 0;
    window.location.hash = '/'+season+'/futsal/player/'+playerid;
    $('#feedback_page').val('Futsal-spiller');
    startLoad();
    
    playeridselected = playerid;
    
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getFutsalPlayer",  season: season, playerid: playerid, teamid: teamid},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            if (json.playerinfo == undefined || json.playerinfo.length == 0){
                $('#noData').show();
                stopLoad();
                return;
            }
            $('[id^="futsal_player"]').show();
            setFutsalTeamLogo($('#futsal_player_logo'),json.teams[0].teamid);
            var playername = json.playerinfo[0].playername;
            setFutsalTeams(json.teams);
            var extra = json.playerinfo[0].extra;
            $('#futsal_player_extra_1').empty();
            $('#futsal_player_extra_2').empty();
            $('#futsal_player_extra_3').empty();
            if(extra != undefined && extra.length > 0){
                var extraArr = extra.split("|");
                var count = 1;
                for(var key in extraArr){
                    $('#futsal_player_extra_'+count).html(extraArr[key]);
                    count++;
                }
            }
            
            $('#futsal_player_teamselect').val(teamid);
            $('#futsal_player_name').html(playername);
            updateBreadcrumbSpecific("Futsal","getFutsalLeague();return false;",""+playername,"getFutsalPlayer("+playerid+");return false;");
            updateFutsalPlayerInfo(json.playerinfo);
            stopLoad();
        }
    }); 
}
function setFutsalTeams(array)
{
    if(array.length == 1){
        $('#futsal_player_label').hide();
        $('#futsal_player_teamselect').hide();
    }else{
        $('#futsal_player_teamselect').empty();
        $('#futsal_player_teamselect').append('<option value=0>Alle lag</option>');
        for(var team in array){
            $('#futsal_player_teamselect').append('<option value='+array[team].teamid+'>'+array[team].teamname+'</option>');
        }
        $('#futsal_player_label').show();
        $('#futsal_player_teamselect').show();
    }
}

function updateFutsalPlayerInfo(array)
{
    var table = $('#futsal_player_info');
    table.empty();
    table.append('<thead>'+
        '<th>Dato</th><th>Hjemmelag</th><th>Bortelag</th>'+
        '<th>Resultat&nbsp&nbsp</th>'+
        '<th>Mål&nbsp&nbsp</th><th>Straffemål&nbsp&nbsp</th>'+
        '<th>Selvmål&nbsp&nbsp&nbsp</th><th>Gule&nbspkort&nbsp&nbsp</th>'+
        '<th>Rødt&nbspkort&nbsp</th>'+
        '</thead>');
    table.append('<tbody>');
    
    
    var totgoals = 0;
    var goals = 0;
    var penalty = 0;
    var yellow = 0;
    var red = 0;
    var owngoal = 0;

    for (var i=0; i<array.length; i++) {
        table.append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td>'+getDateStringMilli(array[i].timestamp)+'</td>'+
            '<td>'+getFutsalTeamLink(array[i].homeid,array[i].hometeamname,array[i].leagueid)+'</td>'+
            '<td>'+getFutsalTeamLink(array[i].awayid,array[i].awayteamname,array[i].leagueid)+'</td>'+
            '<td>'+getMatchLinkText(array[i].matchid,array[i].result)+'</td>'+
            '<td>'+array[i].goals+'</td>'+
            '<td>'+array[i].penalty+'</td>'+
            '<td>'+array[i].owngoals+'</td>'+
            '<td>'+array[i].yellowcards+'</td>'+
            '<td>'+array[i].redcards+'</td>'+
            '</tr>');

        goals += parseInt(array[i].goals);
        penalty += parseInt(array[i].penalty);
        red += parseInt(array[i].redcards);
        yellow += parseInt(array[i].yellowcards);
        owngoal += parseInt(array[i].owngoals);
        totgoals += parseInt(array[i].goals);
        totgoals += parseInt(array[i].penalty);

    }
    table.append('<tr><td><b>Totalt</b></td><td>&nbsp</td><td>&nbsp</td>'+
    '<td><td><b>'+goals+'</b></td><td><b>'+penalty+'</b></td><td><b>'+owngoal+'</b></td><td><b>'+yellow+'</b></td><td><b>'+red+'</b></td></tr>');
    table.append('</tbody>');
    
    $('#futsal_player_totalgoals').html(totgoals);
    $('#futsal_player_yellowcard').html(yellow);
    $('#futsal_player_redcard').html(red);
}