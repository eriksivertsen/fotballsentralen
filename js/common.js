var opts = {
        lines: 13, // The number of lines to draw
        length: 7, // The length of each line
        width: 3, // The line thickness
        radius: 10, // The radius of the inner circle
        corners: 1, // Corner roundness (0..1)
        rotate: 0, // The rotation offset
        color: '#000', // #rgb or #rrggbb
        speed: 1, // Rounds per second
        trail: 50, // Afterglow percentage
        shadow: false, // Whether to render a shadow
        hwaccel: false, // Whether to use hardware acceleration
        className: 'spinner', // The CSS class to assign to the spinner
        zIndex: 2e9, // The z-index (defaults to 2000000000)
        top: 'auto', // Top position relative to parent in px
        left: 'auto' // Left position relative to parent in px
    };
var lastVisit;
var spinner;
var allowClick;
var playeridselected;
var teamidselected;
var leagueidselected;
var eventselected;
var typeselected;
var statsSeason;

var CURRENT_SEASON = 2014;

var title = 'FotballSentralen.com';
var season = CURRENT_SEASON;
var suspendedLeagueLand = 138918;
var timeout = 25000; //ms

function selectSuspendedLeague()
{
    var league = $('#suspensionSelect').val();
    getSuspensionList(league);
}

function selectTotalEvents()
{
    leagueidselected = $('#allEventsSelect').val();
    $('#leagueselect').val(leagueidselected);
    selectEvents();
}

function setLeagueSelected(){
    leagueidselected = $('#scope_league').val();
}

function selectLeague()
{
    if(!allowClicks){
        return;
    }
    leagueidselected = $('#leagueselect').val();
    if(leagueidselected == 0){
        getTeam(0,0);
    }else if(leagueidselected == 12) { // Futsal
        getFutsalLeague();
    } 
    else{
        getTeam(leagueidselected);
    }
}


function selectEvents(){
    if($('#playerradio').is(':checked')){
        getEventsTotalSelected();
    }else{
        getEventsTotalTeamSelected();
    }
}

function selectTotalEventsType()
{
    eventselected = $('#allEventsSelectType').val();
    selectEvents();
}

function selectSeason()
{
    //only possible when in HOME 
    season = $('#season').val();
    getLeagues();
    
    var paramArray =  window.location.hash.split("/");
    var type = paramArray[2];
    var id = paramArray[3];
    var specialid = paramArray[4];
    
    setCurrentPage(type,id,specialid);
    
//    if($('#futsal_league').is(":visible")){
//       getFutsalLeague();
//    }
//    if($('#futsal_player').is(":visible")){
//       getFutsalPlayer(playeridselected);
//    }
//    if($('#futsal_team').is(":visible")){
//       getFutsalTeam(teamidselected);
//    }
//    if($('#player').is(":visible")){
//       getPlayer(playeridselected);
//    }
//    if($('#teamplayerinfo').is(":visible")){
//        getTeam(0, teamidselected);
//    }
//    if($('#eventoverview').is(":visible")){
//        getTeam(leagueidselected,0);
//    }
//    if($('#events').is(":visible")){
//        getEventsTotal(eventselected,leagueidselected);
//    }
    if($('#playerminutes').is(":visible")){
        getTotalPlayerMinutes();
    }
}
function setSeason(season_){
    season = season_;
    $('#season').val(season_);
}
function next() {
    if(!allowClicks){
        return;
    }
    var season_ = parseInt(season);
    if($('#season').val() != '2014'){
        $('#season').val(''+(season_+1));
        selectSeason();
    }
}

function previous() {
    if(!allowClicks){
        return;
    }
    var season_ = parseInt(season);
    if($('#season').val() != '2011'){
        $('#season').val(''+(season_-1));
        selectSeason();
    }
}

function getLeagues()
{
    $('#season').val(season);
    $('#ranking').empty();
    clearMenu();
        
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {
            action: "getTeams" ,
            season: season
        },
        success: function(json) {
            var array = json;
            for(var index in array) {
                var teamarray = array[index];
                for (var i=0; i<teamarray.length; i++) { //<li><a href="index.php?season='+season+'&team_id='+teamarray[i].teamid+'">'+teamarray[i].teamname+'</a></li>
                    $('#'+index).append('<li><a onclick="getTeam(0,'+teamarray[i].teamid+')">'+teamarray[i].teamname+'</a></li>');
                }
            }
        }
    });
    allowClicks = true;
}
function clearMenu(){
     $('#tippeligaen').empty();
     $('#1div').empty();
     $('#2div1').empty();
     $('#2div2').empty();
     $('#2div3').empty();
     $('#2div4').empty();
}
function getPopulare() 
{
    if(!allowClicks){
        return;
    }
    startLoad();
    //history.pushState("", "Title", 'index.php?page=populare');
    window.location.hash = '/'+season+'/page/populare';
    $('#feedback_page').val('Populære');
    
    $("#populare").show();
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {
            action: "getPopulare"
        },
        error: function () {
            stopLoad()
        },
        success: function(json) {
            var array = json;
            updatePopulareTables(array);
            stopLoad();
            
        }
    });
}
function getSuspensionList(leagueid) 
{
    if(leagueid === undefined){
        leagueid = suspendedLeagueLand;
    }
    if(!allowClicks){
        return;
    }
    startLoad();
    //history.pushState("", "Title", 'index.php?page=suspension&league_id='+leagueid);
    window.location.hash = '/'+season+'/page/suspension/'+leagueid;
    $('#feedback_page').val('Suspensjonsliste');
    $('#suspensionSelect').show();
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        data: {
            action: "getSuspensionList",
            leagueid: leagueid
        },
        error: function () {
            stopLoad()
        },
        success: function(json) {
            var array = json;
            updateSuspensionList(array);
            stopLoad();
        }
    });
}
function getPreviewMatches(){
    if(!allowClicks){
        return;
    }
    //history.pushState("", "Title", 'index.php?page=preview');
    window.location.hash = '/'+season+'/page/preview';
    $('#feedback_page').val('Forhåndsstoff oversikt');
    startLoad();
    
    $('#preview').show();
    $('#preview_table').hide();
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getMatchesOneWeek"},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            updateBreadcrumbSpecific("Forhåndsstoff","getPreviewMatches()");
            $('#previewmatches').empty();
            $('#previewmatches').append('<h4>Kamper neste 3 dager:</h4>');
            var string = '';
            var leagueArray = [];
            
            for(var key in json){
                var match = json[key];
                if(leagueArray[match.leagueid] === undefined){
                    leagueArray[match.leagueid] = '<br/><ul><b>'+getLeagueName(match.leagueid)+'</b>';
                    leagueArray[match.leagueid] += '<li>'+getPreviewLink(match.matchid,match.homename,match.awayname,match.timestamp)+'</li>';
                }else{
                    leagueArray[match.leagueid] += '<li>'+getPreviewLink(match.matchid,match.homename,match.awayname,match.timestamp)+'</li>';
                }
            }
            
            for(var leaguekey in leagueArray){
                leagueArray[leaguekey] += '</ul>';
                string += leagueArray[leaguekey];
            }   
            
            $('#previewmatches').append(string);
            $('#previewmatches').show();
            stopLoad();
        }
    });
}

function setStatsType(){
    var paramArray =  window.location.hash.split("/");
    var specialid = paramArray[4];
    getPreviewFull(specialid,$('#preview_stats_type').val());
}

function getPreview(matchid){
    getPreviewFull(matchid,statsSeason);
}

function getPreviewFull(matchid,statsSeasonSel)
{
    if(!allowClicks){
        return;
    }
    startLoad();
    
    window.location.hash = '/'+season+'/page/preview/'+matchid;
    $('#feedback_page').val('Forhåndsstoff');
    if(statsSeasonSel == undefined){
        statsSeason = 1;
    }else{
        statsSeason = statsSeasonSel;
    }
    $('#preview_stats_type').val(statsSeason);
    $('#preview').show();
    
    $('#preview_table').show();
    $('#previewmatches').hide();
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getMatchInfo", matchid: matchid, season: statsSeason},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            if(json.hometeam.currentposition == undefined || json.awayteam.currentposition == 0){
                $('#noData').show();
                $('[id^="preview_"]').hide();
                $('#preview_label').show();
                $('#preview_stats_type').show();
                stopLoad();
                return;
            }
            $('[id^="preview_"]').show();
            var url = json.hometeam.teamtoleague[0].weatherurl;
            $('#preview_weather').attr('src',url+'ekstern_boks_tre_dager.html');
            updateBreadcrumbSpecific("Forhåndsstoff","getPreviewMatches()",json.hometeam.teamtoleague[0].teamname + ' - ' +json.awayteam.teamtoleague[0].teamname,"getPreview("+matchid+")");
            
            updatePreviewTable(json.hometeam,'home');
            updatePreviewTable(json.awayteam,'away');
            
            var now = new Date();
            var timestamp = json.timestamp;
            var twoHours = 7200;
            var matchtime = parseInt(timestamp) + parseInt(twoHours);
            $('#preview_warning').html('');
            if(matchtime < (now.getTime())){
                $('#preview_warning').html('NB: Kamp allerede spilt: ' +getMatchLinkTextInternal(matchid,'Rapport'));
                $('#preview_warning').attr('style','color:#FFFFCC;font-weight:bold;');
            }
            
            $('#preview_dateofmatch').html(getMatchDateString(json.timestamp));
            var overlibString = 'Snitt gule kort: '+json.refereestats.yellowpr+'<br/>Snitt røde kort: '+json.refereestats.redpr+' <br/>Kamper: '+json.refereestats.matches;
            
            if(json.refereestats.matches != undefined){
                $('#preview_referee').html(getOverlibLink(overlibString,'Dommer: ' + json.referee, '#/'+season+'/page/referee/'+json.refereeid+''));
            }else{
                $('#preview_referee').html(getOverlib('Dommer ikke funnet!','Dommer: Ukjent'));
            }
            
            $('#preview_officallink').html(getMatchLinkText(matchid,'Offisielle lag/tropper'));
            $('#preview_home_fsscore').html(json.hometeamFS);
            $('#preview_away_fsscore').html(json.awayteamFS);
//            var overlibString2 = 'Kortratingen baseres på hvor mange gule kort hjemme- og bortelaget har fått denne sesongen, '+
//                'og hvor mange gule kort det deles ut i gjennomsnitt i avdelingen. Dommerens snitt blir også tatt med i utregningen. '+
//                'En negativ rating betyr at det sannsynligvis blir få gule kort, en positiv motsatt. ' +
//                'En rating på +/- 10 ansees som høy.';
//            $('#preview_cardrating').html(getOverlibWidth(overlibString2,'Kortrating: ' + json.cardrating,350));
            
            
            updateSuspensions(json.suspension, json.hometeam.teamtoleague[0].teamid, 'home');
            updateSuspensions(json.suspension, json.awayteam.teamtoleague[0].teamid, 'away');
            
            var prevMatches = [];
            for(var k in json.previousmatches){
                var prevMatch = json.previousmatches[k];
                //TODO: Add surface to prevmatches.
                if(prevMatch.teamwonid == prevMatch.hometeamid){
                    var matchInfo = prevMatch.dateofmatch + ': <b>'+ prevMatch.homename + '</b> - ' +  prevMatch.awayname + ' ' + prevMatch.result; 
                }else if(prevMatch.teamwonid == prevMatch.awayteamid){
                    matchInfo = prevMatch.dateofmatch + ': '+ prevMatch.homename + ' - <b>' +  prevMatch.awayname + '</b> ' + prevMatch.result; 
                }else{
                    matchInfo = prevMatch.dateofmatch + ': '+ prevMatch.homename + ' - ' +  prevMatch.awayname + ' ' + prevMatch.result; 
                }
               prevMatches.push(matchInfo);
            }
            if(prevMatches.length != 0){
                $('#preview_previous').html(getOverlibWidth(prevMatches.join('<br/>'),'Innbyrdes oppgjør',320));
            }else{
                $('#preview_previous').html(getOverlibWidth('Ingen kamper! Kun data fra 2011 sesongen og senere.','Innbyrdes oppgjør',320));
            }
            stopLoad();
        }
    });

}
function updateSuspensions(array, teamid, team){
    $('#preview_'+team+'_suspensions').empty();
    var suspArray = [];
    for(var key in array.threeYellow){
        var value = array.threeYellow[key];
        if(value['teamid'] == teamid){
            suspArray.push(getPlayerLink(value['playerid'],value['playername']) + ' (3 gule)');
        }
    }
    for(var key1 in array.redCard){
        var value1 = array.redCard[key1];
        if(value1['teamid'] == teamid){
            suspArray.push(getPlayerLink(value1['playerid'],value1['playername']) + ' (rødt)');
        }
    }
    for(var key2 in array.fiveYellow){
        var value2 = array.fiveYellow[key2];
        if(value2['teamid'] == teamid){
            suspArray.push(getPlayerLink(value2['playerid'],value2['playername']) + ' (5 gule)');
        }
    }
    for(var key3 in array.sevenYellow){
        var value3 = array.sevenYellow[key3];
        if(value3['teamid'] == teamid){
            suspArray.push(getPlayerLink(value3['playerid'],value3['playername']) + ' (7 gule)');
        }
    }
    for(var key4 in array.moreYellow){
        var value4 = array.moreYellow[key4];
        if(value4['teamid'] == teamid){
            suspArray.push(getPlayerLink(value4['playerid'],value4['playername']) + ' ('+value4['count']+' gule)');
        }
    }
    var susp = suspArray.join('<br/>');
    if(suspArray.length == 0){
        susp = 'Ingen';
    }
    $('#preview_'+team+'_suspensions').append(susp);
    
}
function getTransfers()
{
    if(!allowClicks){
        return;
    }
    startLoad();
    //history.pushState("", "Title", 'index.php?page=transfers');
    window.location.hash = '/'+season+'/page/transfers';
    $('#feedback_page').val('Overganger');
    $('#transfer_text').html('Kun interne overganger/lån i Norge. Spilleren må være i tropp for at overgang skal registreres. ');
    
    $('[id^="transfer_"]').show();
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getTransfers", season: season},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            $('#transfer_transfer').empty();
            $('#transfer_transfer').append(getTableHeader(["Spillernavn","Fra lag","Til lag","Dato registrert"]));
            var i=0;
            for(var val in json){
                var player = json[val];
                $('#transfer_transfer').append(getTableRow([getPlayerLink(player.playerid, player.playername),getTeamLink(player.fromteamid,player.fromteamname),getTeamLink(player.toteamid,player.toteamname),player.datefound],i));
                $('#transfer_transfer').show();
                i++;
            }
            $('#transfer_transfer').tablesorter({widgets: ['zebra']});
            $('#transfer_table').show();
            updateBreadcrumbSpecific("Overganger","getTransfers()");
            stopLoad();
        }
    });
}
function getReferee()
{
    if(!allowClicks){
        return;
    }
    startLoad();
    //history.pushState("", "Title", 'index.php?page=referee');
    window.location.hash = '/'+season+'/page/referee';
    $('#feedback_page').val('Dommeroversikt');
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getReferee", season: season},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            $('#referee_table').empty();
            $('#referee_table').append(getTableHeader(["Dommer","Kamper","Gule kort ","Røde kort","Snitt gule ","Snitt røde","Neste kamp"]));
            var i=0;
            for(var val in json){
                var ref = json[val];
                $('#referee_table').append(getTableRow([getRefereeLink(ref.refereeid,ref.refereename),ref.matches,ref.yellow,ref.red,ref.yellowpr,ref.redpr, getPreviewLinkText(ref.nextmatch,ref.hometeam +' - '+ref.awayteam)], i));
                $('#referee_table').show();
                i++;
            }
            $('#referee_table').tablesorter({widgets: ['zebra']});
            $('#referee').show();
            updateBreadcrumbSpecific("Dommere","getReferee()");
            stopLoad();
        }
    });
}
function getNationalTeam(teamid)
{
    if(!allowClicks){
        return;
    }
    teamidselected = teamid;    
    window.location.hash = '/'+season+'/nationalteam/'+teamid;
    $('#feedback_page').val('Landslagsversikt');
    startLoad();
    updateBreadcrumbSpecific('Landslag','',getNationalTeamName(teamid),'getNationalTeam('+teamid+')');
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getNationalTeam", teamid: teamid, season: season},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            var array = json;
            $('#nationalteamname').html('Norge '+getNationalTeamName(teamid));
            setNationalTeamLogo($('#nationalteam_logo'),teamid);
            updateTeamPlayers('national_teamplayerinfo',array.players);
            stopLoad();
            $('#nationalteam').show();
        }
    }); 
}
function getRefereeId(refereeid)
{
    if(!allowClicks){
        return;
    }
    startLoad();
    //history.pushState("", "Title", 'index.php?page=referee&referee_id='+refereeid);
    window.location.hash = '/'+season+'/page/referee/'+refereeid;
    $('#feedback_page').val('Dommer');
    
    $('#referee').show();
    $('#referee_table_specific').empty();
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getRefereeId", referee_id: refereeid},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            $('#referee_table_specific').append(getTableHeader(["Kampdato","Hjemmelag","Bortelag","Resultat","Gule kort","Røde kort"]));
            var i = 0;
            var yellow = 0;
            var red = 0;
            for(var key in json){
                var v = json[key];
                $('#referee_table_specific').append(getTableRow([v.dateofmatch,getTeamLink(v.homeid,v.homename),getTeamLink(v.awayid,v.awayname),getMatchResultLink(v.matchid,v.result),v.yellow,v.red],i));
                yellow += parseInt(v.yellow);
                red += parseInt(v.red);
                i++;
            }
            $('#referee_table_specific').append(getTableRow(["<b>Snitt</b>","","","",(yellow/i).toFixed(2),(red/i).toFixed(2)],0));
            stopLoad();
            $('#referee_table_specific').show();
            $('#referee_table_specific').tablesorter({widgets: ['zebra']});
            updateBreadcrumbSpecific("Dommere","getReferee()",json[0].refereename,"getRefereeId("+refereeid+")");
        }
    });
    
}
function getTeam(leagueid,teamid)
{
    if(!allowClicks){
        return;
    }
    
    $('#team').show();
    $('#player').hide();
    $('#events').hide();
    $('#eventoverview').hide();
    
    if(leagueid == 0) {
        if(teamid == 0) {
            getLeagueInfo(0,0); // ALL
        }else{
            getTeamInfo(teamid);
        }        
    }
    else{
        if(leagueid == getAndreDiv()) {
            getLeagueInfo(getAndreDivAll(),0);
        }
        else{
            getLeagueInfo(leagueid,0);
        }      
    }
}

function updateBreadcrumbSpecific(first,onclickfirst,second,onclicksecond,third,onclickthird){
    $('#breadcrumbs').empty();
    $('#breadcrumbs').append('<li><a href="#" onclick="'+onclickfirst+'">'+first+'</a></li>');
    document.title = first.replace("&nbsp"," ") + " | " + title;
    if(second != undefined && onclicksecond != undefined){
        document.title = second.replace("&nbsp"," ") + " | " + title;
        $('#breadcrumbs').append('<li><a href="#" onclick="'+onclicksecond+'">'+second+'</a></li>');
        if(third != undefined && onclickthird != undefined){
            document.title = third.replace("&nbsp"," ") + " | " + title;
            $('#breadcrumbs').append('<li><a href="#" onclick="'+onclickthird+'">'+third+'</a></li>');
        }
    }
    $("#breadcrumbs").breadcrumbs("home");
}
function updateBreadcrumb(leagueid,teamid,jsonarray)
{
    var leagueidfound ;
    var teamidfound;
    var teamname;
    var playername;
    var playerid; 
    
    $('#breadcrumbs').empty();
    var ses = season;
    if(season == 0){
        ses = 'Totalt';
    }
    
    //ALL
    if(teamid == 0 && leagueid == 0){
        $('#breadcrumbs').append('<li>'+ses+'</li>');
        $('#breadcrumbs').append('<li><a href="#" onclick="getTeam(0,0)">Norge</a></li>');
        $("#breadcrumbs").breadcrumbs("home");
        $('#league_name').html('Norge ' + ses);
        document.title = title;
        return;
    }
   
    
    if(teamid != 0){
        var json = jsonarray;
        leagueidfound = json[0].leagueid;
        teamidfound = json[0].teamid;
        teamname = json[0].teamname;
        playername = json[0].playername;
        playerid = json[0].playerid;
        
        $('#breadcrumbs').append('<li>'+ses+'</li>');
        $('#breadcrumbs').append('<li><a onclick="getTeam(0,0)">Norge</a></li>');
        
        $('#league_name').html(getLeagueName(leagueidfound) + ' ' + ses);
        
        if(leagueidfound == getAdeccoligaen()){
             $('#breadcrumbs').append('<li>'+getLeagueLink(getAdeccoligaen(), 'Adeccoligaen')+'</li>');
             document.title = "Adeccoligaen | " + title;
        }
        if(leagueidfound == getTippeligaen()){
            $('#breadcrumbs').append('<li><a onclick="getTeam('+getTippeligaen()+',0)">Tippeligaen</a></li>');
            document.title = "Tippeligaen | " + title;
        }
        if(leagueidfound == getAndreDivAll()){
            $('#breadcrumbs').append('<li><a onclick="getTeam('+getAndreDiv()+',0)">2.divisjon</a></li>');
            document.title = "2.divisjon | " + title;
        }
        
        if(leagueidfound == getAndreDiv1()){
            $('#breadcrumbs').append('<li><a onclick="getTeam('+getAndreDiv()+',0)">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a onclick="getTeam('+getAndreDiv1()+',0)">Avdeling 1</a></li>');
            document.title = "2.divisjon avd 1 | " + title;
        }
        if(leagueidfound == getAndreDiv2()){
            $('#breadcrumbs').append('<li><a onclick="getTeam('+getAndreDiv()+',0)">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a onclick="getTeam('+getAndreDiv2()+',0)">Avdeling 2</a></li>');
            document.title = "2.divisjon avd 2 | " + title;
        }
        if(leagueidfound == getAndreDiv3()){
            $('#breadcrumbs').append('<li><a onclick="getTeam('+getAndreDiv()+',0)">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a onclick="getTeam('+getAndreDiv3()+',0)">Avdeling 3</a></li>');
            document.title = "2.divisjon avd 3 | " + title;
        }
        if(leagueidfound == getAndreDiv4()){
            $('#breadcrumbs').append('<li><a onclick="getTeam('+getAndreDiv()+',0)">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a onclick="getTeam('+getAndreDiv4()+',0)">Avdeling 4</a></li>');
            document.title = "2.divisjon avd 4 | " + title;
        }
        $('#breadcrumbs').append('<li><a onclick="getTeam(0,'+teamidfound+')">'+teamname+'</a></li>');
        $('#teamname').html(teamname);

        document.title = teamname + " | " + title;
        
        if(playername != null){
            $('#breadcrumbs').append('<li><a onclick="getPlayer('+playerid+')">'+playername+'</a></li>');
            $('#playername').html(playername);
            document.title = playername + " | " + title;
        }
        $("#breadcrumbs").breadcrumbs("home");
            
    }
    else if(leagueid != 0){
        
        
        $('#breadcrumbs').append('<li>'+ses+'</li>');
        $('#breadcrumbs').append('<li><a href="index.php">Norge</a></li>');

        $('#league_name').html(getLeagueName(leagueid) + ' ' + ses);
        
        if(leagueid == getAdeccoligaen()){
              $('#breadcrumbs').append('<li>'+getLeagueLink(getAdeccoligaen())+'</li>');
             //$('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAdeccoligaen()+'">Adeccoligaen</a></li>');
             document.title = "Adeccoligaen | " + title;
        }
        if(leagueid == getTippeligaen()){
            $('#breadcrumbs').append('<li><a href="#" onclick=getTeam('+getTippeligaen()+',0)">Tippeligaen</a></li>');
            document.title = "Tippeligaen | " + title;
        }
        if(leagueid == getAndreDivAll()){
            $('#breadcrumbs').append('<li><a href="#" onclick=getTeam('+getAndreDiv()+',0)">2.divisjon</a></li>');
            document.title = "2.divisjon | " + title;
        }
        
        if(leagueid == getAndreDiv1()){
            $('#breadcrumbs').append('<li><a href="#" onclick=getTeam('+getAndreDiv()+',0)">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a href="#" onclick=getTeam('+getAndreDiv1()+',0)">Avdeling 1</a></li>');
            document.title = "2.divisjon avd 1 | " + title;
        }
        if(leagueid == getAndreDiv2()){
            $('#breadcrumbs').append('<li><a href="#" onclick=getTeam('+getAndreDiv()+',0)">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a href="#" onclick=getTeam('+getAndreDiv2()+',0)">Avdeling 2</a></li>');
            document.title = "2.divisjon avd 2 | " + title;
        }
        if(leagueid == getAndreDiv3()){
            $('#breadcrumbs').append('<li><a href="#" onclick=getTeam('+getAndreDiv()+',0)">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a href="#" onclick=getTeam('+getAndreDiv3()+',0)">Avdeling 3</a></li>');
            document.title = "2.divisjon avd 3 | " + title;
        }
        if(leagueid == getAndreDiv4()){
            $('#breadcrumbs').append('<li><a href="#" onclick=getTeam('+getAndreDiv()+',0)">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a href="#" onclick=getTeam('+getAndreDiv4()+',0)">Avdeling 4</a></li>');
            document.title = "2.divisjon avd 4 | " + title;
        }
        $("#breadcrumbs").breadcrumbs("home");
    }
    
}
function getEventsTotalSelected()
{
    getEventsTotal(eventselected,leagueidselected);
}
function getEventsTotalTeamSelected()
{
    getEventsTotalTeam(eventselected,leagueidselected);
}

function getTotalPlayerMinutes(){
    
    if(!allowClicks){
        return;
    }
    eventselected = 11;
    startLoad();
    window.location.hash = '/'+season+'/events/'+eventselected;
    if(leagueidselected == undefined){
        leagueidselected = 0;
    }
    $('#allEventsSelectType').val(eventselected);
    $('#allEventsSelect').val(leagueidselected);
    $('#leagueselect').val(leagueidselected);
    
    
    if(leagueidselected == 0){
        updateBreadcrumbSpecific('Norge', 'getTeam(0,0)', 'Spilleminutter', 'getTotalPlayerMinutes()');
    }else{
        updateBreadcrumbSpecific('Norge', 'getTeam(0,0)', ''+getLeagueName(leagueidselected),'getTeam('+leagueidselected+',0)','Spilleminutter', 'getTotalPlayerMinutes()');
    }
    $('#feedback_page').val('Spilleminutter');
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {
            action: "getTotalPlayerminutes", 
            season: season,
            leagueid: leagueidselected
        },
        error: function () {
            stopLoad()
        },
        success: function(json) {
           
            var array = json;
            $('#playerminutes_table').empty();
            $('#playerminutes_table').append('<caption class="tableheader" onclick="getTotalPlayerMinutes()">Spilleminutter&nbspi&nbsp'+ getLeagueName(leagueidselected)+',&nbsp' + season+'</caption>');
            $('#playerminutes_table').append('<thead ><tr><td><b>Spiller<b></td><td><b>Lag<b></td><td><b>Min<b></td></tr></thead>');
            for (var i=0; i<array.length; i++) {
                
                $('#playerminutes_table').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td>'+getPlayerLink(array[i].playerid,array[i].playername)+'</td>'+
                    '<td>'+getTeamLink(array[i].teamid,array[i].teamname)+'</td>'+
                    '<td>'+array[i].eventcount+'</td></tr>');
            }
            stopLoad();
            $('#allEventsSelectType').val(11);
            $('#allEventsSelectType').show();
            $('#allEventsSelect').show();
            $('[id^="event_top"]').show();
            $('#playerminutes').show();
            $('#playerminutes_table').show();
            $('#events').show();
        }
    });
}

function getEventsTotal(eventtype,leagueid)
{
    if(!allowClicks){
        return;
    }
    if(leagueid == undefined){
        leagueid = 0;
    }
    $('#allEventsSelectType').val(eventtype);
    $('#allEventsSelect').val(leagueid);
    $('#leagueselect').val(leagueid);
    
    leagueidselected = leagueid;
    eventselected = eventtype;
    
    window.location.hash = '/'+season+'/events/'+eventtype;
    $('#feedback_page').val('Toppliste spiller ' + eventtype);
    
    if(eventtype == 10){
        // topscorer hack
        eventtype = '4,8';
    }else if(eventtype == 13){
        eventtype = '1,3';
    }
    
    if(eventtype == 50 || eventtype == 60 || eventtype == 80 || eventtype == 11){
        $('#teamradio').prop('disabled',true);
    }else{
        $('#teamradio').prop('disabled',false);
    }
    
    $('#playerradio').prop('checked',true);
    
    startLoad();
    
    var tableheader = getEventFromId(eventtype);
    if(leagueid == 0){
        updateBreadcrumbSpecific('Norge', 'getTeam(0,0)', tableheader, 'getEventsTotal('+eventtype+','+leagueid+')');
    }else{
        updateBreadcrumbSpecific('Norge', 'getTeam(0,0)', ''+getLeagueName(leagueid),'getTeam('+leagueid+',0)',tableheader, 'getEventsTotal('+eventtype+','+leagueid+')');
    }
    
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {
            action: "getEventsTotal", 
            eventtype : eventtype,
            season: season,
            leagueid: leagueid
        },
        error: function () {
            stopLoad()
        },
        success: function(json) {
            var array = json;
            var ses = season;
            if(season == 0){
                ses = ' totalt';
            }
            $('#allEvents').empty();
            $('#allEvents').append('<caption class="tableheader" onclick="getEventsTotal('+eventtype+','+leagueid+')">'+tableheader+'&nbspi&nbsp'+ getLeagueName(leagueid)+',&nbsp' + ses+'</caption>');
            $('#allEvents').append('<thead ><tr><td><b>Spiller<b></td><td><b>Lag<b></td><td><b>Antall<b></td></tr></thead>');
            
            
            for (var i=0; i<array.length; i++) {
                $('#allEvents').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td>'+getPlayerLink(array[i].playerid,array[i].playername)+'</td>'+
                    '<td>'+getTeamLink(array[i].teamid,array[i].teamname)+'</td>'+
                    '<td align=center>'+array[i].eventcount+'</td></tr>');
            }
            if(array.length >= 3) {
                setTeamLogo($('#event_top_logo_1'),array[0].teamid);
                setTeamLogo($('#event_top_logo_2'),array[1].teamid);
                setTeamLogo($('#event_top_logo_3'),array[2].teamid);

                $('#event_top_1_info').html(array[0].eventcount + ': ' +getPlayerLink(array[0].playerid,array[0].playername));
                $('#event_top_2_info').html(array[1].eventcount + ': ' +getPlayerLink(array[1].playerid,array[1].playername));
                $('#event_top_3_info').html(array[2].eventcount + ': ' +getPlayerLink(array[2].playerid,array[2].playername));
                
                $('[id^="event_top"]').show();
            }else{
                $('#event_top_logo_1').html('');
                $('#event_top_logo_2').html('');
                $('#event_top_logo_3').html('');
                $('#event_top_1_info').html('');
                $('#event_top_2_info').html('');
                $('#event_top_3_info').html('');
            }
            
            stopLoad();
            $('#allEventsSelectType').show();
            $('#allEventsSelect').show();
            $('#label_event').show();
            $('#label_league').show();
            $('#allEvents').show();
            $('#events').show();
            $('#radio').show();
        }
    });
}
function getEventsTotalTeam(eventtype, leagueid)
{
    if(!allowClicks){
        return;
    }
    eventselected = eventtype;
    leagueidselected = leagueid;
    window.location.hash = '/'+season+'/eventsteam/'+eventtype;
    $('#feedback_page').val('Toppliste lag ' + eventtype);
    
    $('#allEventsSelectType').val(eventtype);
    $('#allEventsSelect').val(leagueid);
    $('#leagueselect').val(leagueid);
    
    if(eventtype == 10){
        eventtype = '4,8';
        // goal hack
    }else if(eventtype == 13){
        eventtype = '1,3';
    }
    $('#teamradio').prop('checked',true);
    
    startLoad();
    var tableheader = getEventFromId(eventtype);
    if(leagueid == 0){
        updateBreadcrumbSpecific('Norge', 'getTeam(0,0)', tableheader, 'getEventsTotalTeam('+eventtype+','+leagueid+')');
    }else{
        updateBreadcrumbSpecific('Norge', 'getTeam(0,0)', ''+getLeagueName(leagueid),'getTeam('+leagueid+',0)',tableheader, 'getEventsTotalTeam('+eventtype+','+leagueid+')');
    }
    
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {
            action: "getEventsTotalTeam", 
            eventtype : eventtype,
            season: season,
            leagueid : leagueid
        },
        error: function () {
            stopLoad()
        },
        success: function(json) {
            $('#allEvents').empty();
            var array = json;
            var ses = season;
            if(season == 0){
                ses = 'totalt';
            }
            $('#allEvents').append('<caption class="tableheader" onclick="getEventsTotal('+eventtype+','+leagueid+')">'+tableheader+'&nbspi&nbsp'+getLeagueName(leagueid)+',&nbsp'+ses+'</caption>');
            $('#allEvents').append('<thead><tr><td><b>Lag<b></td><td><b>Antall<b></td></tr></thead>');
            for (var i=0; i<array.length; i++) {
                $('#allEvents').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td>'+getTeamLink(array[i].teamid,array[i].teamname)+'</td>'+
                    '<td>'+array[i].eventcount+'</td></tr>');
            }
            
            if(array.length >= 3) {
                setTeamLogo($('#event_top_logo_1'),array[0].teamid);
                setTeamLogo($('#event_top_logo_2'),array[1].teamid);
                setTeamLogo($('#event_top_logo_3'),array[2].teamid);

                $('#event_top_1_info').html(array[0].eventcount + ': ' +getTeamLink(array[0].teamid,array[0].teamname));
                $('#event_top_2_info').html(array[1].eventcount + ': ' +getTeamLink(array[1].teamid,array[1].teamname));
                $('#event_top_3_info').html(array[2].eventcount + ': ' +getTeamLink(array[2].teamid,array[2].teamname));
                
                $('[id^="event_top"]').show();
            }else{
                $('#event_top_logo_1').html('');
                $('#event_top_logo_2').html('');
                $('#event_top_logo_3').html('');
                $('#event_top_1_info').html('');
                $('#event_top_2_info').html('');
                $('#event_top_3_info').html('');
            }
            
            
            stopLoad();
            $('#allEventsSelectType').show();
            $('#allEventsSelect').show();
            $('#allEvents').show();
            $('#events').show();
            $('#radio').show();
            $('#label_event').show();
            $('#label_league').show();
        }
    });
}
function getCount(value)
{
    if(value != undefined){
        return value;
    }else{
        return 0;
    }
}

function updateTeamPlayers(teamidarray, dangerlistarray, winarray)
{
    var array = teamidarray;
    $('#teamplayerinfo').empty();    
    $('#teamplayerinfo').show(); 
    $('#teamplayerinfo').append('<thead><th>Nr</th><th>Navn</th><th>Spilleminutter&nbsp&nbsp&nbsp</th><th>Fra&nbspstart&nbsp&nbsp&nbsp</th><th>Byttet&nbsput&nbsp&nbsp&nbsp</th><th>Byttet&nbspinn&nbsp&nbsp&nbsp</th><th>Mål&nbsp&nbsp&nbsp</th><th>Straffemål&nbsp&nbsp&nbsp</th><th>Selvmål&nbsp&nbsp&nbsp</th><th>Gule&nbspkort&nbsp&nbsp&nbsp</th><th>Røde&nbspkort&nbsp&nbsp&nbsp</th><th>Seiere&nbsp&nbsp&nbsp</th></thead>');
    $('#teamplayerinfo').append('<tbody>');

    var players_used = 0;
    var goals = 0;
    var penalty = 0;
    var yellow = 0;
    var red = 0;
    var start = 0;
    var minutes = 0;
    var subbedin = 0;
    var subbedoff = 0;
    var owngoals = 0;
    var totalPercentage = 0;
    var totalWinCount = 0;

    for (var i=0; i<array.length; i++) {
        
        if(array[i].playerid == null || array[i].playerid == -1){
            continue;
        }
        
        array[i].goals = getCount(array[i].goals);
        array[i].penalty = getCount(array[i].penalty);
        array[i].owngoals = getCount(array[i].owngoals);
        array[i].yellowcards = getCount(array[i].yellowcards);
        array[i].redcards = getCount(array[i].redcards);
        array[i].subbedin = getCount(array[i].subbedin);
        array[i].subbedoff = getCount(array[i].subbedoff);

        if(array[i].minutesplayed > 0){
            players_used++;
        }
        var winpercentage = '-';
        for(var j=0;j<winarray.length;j++){
            if(winarray[j].playerid == array[i].playerid){
                winpercentage = winarray[j].percentage;
                totalPercentage += parseFloat(winpercentage);
                winpercentage += '%';
                totalWinCount++;
                break;
            }
        }
        
        var img = '<img src="images/events/yellowtiny.png" style="margin-left:3px;" title="Må sone karantene ved neste gule kort"></img>';
        $('#teamplayerinfo').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
            '<td>'+array[i].shirtnumber+'</td>'+
            '<td>'+getPlayerLink(array[i].playerid,array[i].playername)+' ' + (dangerlistarray[array[i].playerid] != undefined && season == CURRENT_SEASON ? img : '') +  ' ' +getNationalTeam(array[i].nationalleague) + '</td>'+
            '<td>'+array[i].minutesplayed+'</td>'+
            '<td>'+array[i].started+'</td>'+
            '<td>'+array[i].subbedin+'</td>'+
            '<td>'+array[i].subbedoff+'</td>'+
            '<td>'+array[i].goals+'</td>'+
            '<td>'+array[i].penalty+'</td>'+
            '<td>'+array[i].owngoals+'</td>'+
            '<td>'+array[i].yellowcards+'</td>'+
            '<td>'+array[i].redcards+'</td>'+
            '<td>'+winpercentage+'</td>'+
            '</tr>');

        goals += parseInt(array[i].goals);
        penalty += parseInt(array[i].penalty);
        red += parseInt(array[i].redcards);
        yellow += parseInt(array[i].yellowcards);
        start += parseInt(array[i].started);
        minutes += parseInt(array[i].minutesplayed);
        subbedin += parseInt(array[i].subbedin);
        subbedoff += parseInt(array[i].subbedoff);
        owngoals += parseInt(array[i].owngoals);
       
    }
    
    $('#teamplayerinfo').append('<tr><td><b>Totalt</b></td><td>&nbsp</td><td><b>'+minutes+'</b></td><td><b>'+start+'</b></td><td><b>'+subbedin+'</b></td><td><b>'+subbedoff+'</b></td>'+
    '<td><b>'+goals+'</b></td><td><b>'+penalty+'</b></td><td><b>'+owngoals+'</b></td><td><b>'+yellow+'</b></td><td><b>'+red+'</b></td><td><b>'+(totalPercentage/totalWinCount).toFixed(2)+'%</b></td></tr>');
    $('#teamplayerinfo').append('</tbody>');
    $('#teamplayerinfo').tablesorter({widgets: ['zebra']});
    $('#team_players_used').html(players_used);
}

function selectPlayerTeam(){
    getPlayerTeam(playeridselected, $('#teamSelect').val());
}
function getPlayerTeam(playerid,teamid){
    getPlayerFull(playerid,'player_team',teamid);
}
function getPlayer(playerid){
    getPlayerFull(playerid,'',0);
}
function getPlayerSearch(playerid){
    getPlayerFull(playerid,'player_search',0);
}
function getPlayerFull(playerid,fromString,teamid)
{
    if(!allowClicks){
        return;
    }
    playeridselected = playerid;
    window.location.hash = '/'+season+'/player/'+playerid;
   // history.pushState("", "Title", 'index.php?season='+season+'&player_id='+playeridselected+(teamid == 0 ? '' : '&team_id='+teamid));
    startLoad();
    $('#player').show();
    $('#feedback_page').val('Spilleroversikt')
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getPlayerInfo", playerid: playerid, season: season, from: fromString, teamid: teamid},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            if(json.playertoleague.length == 0){
                stopLoad();
                $('#noData').show();
                return;
            }
            if(json.teams.length == 1){
                if(json.teams[0].nationalleague != undefined){
                    $('#player_label').show();
                    $('#teamSelect').empty();
                    $('#teamSelect').append('<option value=0>Alle lag</option>');
                    var arr = json.teams[0].nationalleague.split(",");
                    $('#teamSelect').append('<option value='+json.teams[0].teamid+'>'+json.teams[0].teamname+'</option>');
                    if(arr.length > 1){
                        $('#teamSelect').append('<option value="-1">Norge</option>');
                    }
                    for (var i=0; i<arr.length; i++) {
                        $('#teamSelect').append('<option value='+arr[i]+'>'+getNationalLeague(arr[i])+'</option>');
                    }
                    $('#teamSelect').show();
                }else{
                    $('#player_label').hide();
                    $('#teamSelect').hide();
                }
            }else{
                $('#teamSelect').empty();
                $('#teamSelect').append('<option value=0>Alle lag</option>');
                for(var team in json.teams){
                    $('#teamSelect').append('<option value='+json.teams[team].teamid+'>'+json.teams[team].teamname+'</option>');
                }
                if(json.teams[0].nationalleague != undefined){
                    var arra = json.teams[0].nationalleague.split(",");
                    if(arra.length > 1){
                        $('#teamSelect').append('<option value="-1">Norge</option>');
                    }
                    for (var i=0; i<arra.length; i++) {
                        $('#teamSelect').append('<option value='+arra[i]+'>'+getNationalLeague(arra[i])+'</option>');
                    }
                }   
                
                if(teamid != 0){
                    $('#teamSelect').val(teamid);
                }
                $('#player_label').show();
                $('#teamSelect').show();
            }
            
            $('#noData').hide();
            $('#playerinfo').empty();
            var array = json.playerinfo;
            $('#playerinfo').append('<thead>'+
                '<th>Dato</th><th>Hjemmelag</th><th>Bortelag</th>'+
                '<th>Resultat&nbsp&nbsp</th><th>Start&nbsp&nbsp</th>'+
                '<th>Min&nbsp&nbsp</th><th>Byttet&nbspinn&nbsp&nbsp</th>'+
                '<th>Byttet&nbsput&nbsp&nbsp</th>'+
                '<th>Mål&nbsp&nbsp</th><th>Straffemål&nbsp&nbsp</th>'+
                '<th>Selvmål&nbsp&nbsp&nbsp</th><th>Gule&nbspkort&nbsp&nbsp</th>'+
                '<th>Rødt&nbspkort&nbsp</th>'+
                '</thead>');
            $('#playerinfo').append('<tbody>');
            updateBreadcrumb(0, 1, json.playertoleague);
            
            setTeamLogo($('#player_logo'),json.playertoleague[0].teamid);
           
           
            $('#player_nationalmatches').html('Ingen');
            $('#player_nationalgoals').html('Ingen');
            if(json.national.matches != undefined && json.national.matches > 0){
                $('#player_nationalmatches').html(json.national.matches);
            }
            if(json.national.goals != undefined){
                $('#player_nationalgoals').html(json.national.goals);
            }
                 
           
            $('#player_table').show();
            
            if(season != 0){
                $('#player_playingminutes_year').html('i ' + season);
            }else{
                $('#player_playingminutes_year').html('totalt');
            }
            $('#player_height').html('Ukjent');
            $('#player_dateofbirth').html('Ukjent');
            $('#player_position').html('Ukjent');
            $('#player_country').html('Ukjent');
            $('#player_number').html('');
            if(json.info[0].height != undefined && json.info[0].height != 0){
                $('#player_height').html(json.info[0].height + ' cm');
            }
            if(json.info[0].dateofbirth != undefined && json.info[0].dateofbirth != 0){
                var date = new Date(''+json.info[0].dateofbirth);
                var age=((Date.now() - date) / (31557600000))
                age = Math.floor(age);
                $('#player_dateofbirth').html(getDateString(json.info[0].dateofbirth)+" ("+age+" år)");
            }
            $('#player_number').html('#' + array[0].number);
            $('#player_position').html(json.info[0].position);
            $('#player_country').html(json.info[0].country);
            
            
            var totgoals = 0;
            var goals = 0;
            var penalty = 0;
            var yellow = 0;
            var red = 0;
            var start = 0;
            var minutes = 0;
            var subbedin = 0;
            var subbedoff = 0;
            var owngoal = 0;
            var totMins = 0;

            for (var i=0; i<array.length; i++) {
                $('#playerinfo').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td>'+getDateStringMilli(array[i].timestamp)+'</td>'+
                    '<td>'+getTeamLink(array[i].homeid,array[i].hometeamname,array[i].leagueid)+'</td>'+
                    '<td>'+getTeamLink(array[i].awayid,array[i].awayteamname,array[i].leagueid)+'</td>'+
                    '<td>'+getMatchResultLink(array[i].matchid,array[i].result,array[i].is_national)+'</td>'+
                    '<td>'+(array[i].start == 0 ? 'Nei' : 'Ja' )+'</td>'+
                    '<td>'+array[i].minutesplayed+'</td>'+
                    '<td>'+(array[i].subbedin == 0 ? 'Nei' : 'Ja' )+'</td>'+
                    '<td>'+(array[i].subbedoff == 0 ? 'Nei' : 'Ja' )+'</td>'+
                   
                    '<td>'+array[i].goals+'</td>'+
                    '<td>'+array[i].penalty+'</td>'+
                    '<td>'+array[i].owngoal+'</td>'+
                    '<td>'+array[i].yellowcards+'</td>'+
                    '<td>'+array[i].redcards+'</td>'+
                   
                    '</tr>');
                
                totMins += parseInt(array[i].minutesplayed);
                goals += parseInt(array[i].goals);
                penalty += parseInt(array[i].penalty);
                red += parseInt(array[i].redcards);
                yellow += parseInt(array[i].yellowcards);
                start += parseInt(array[i].start);
                minutes += parseInt(array[i].minutesplayed);
                subbedin += parseInt(array[i].subbedin);
                subbedoff += parseInt(array[i].subbedoff);
                owngoal += parseInt(array[i].owngoal);
                
                totgoals += parseInt(array[i].goals);
                totgoals += parseInt(array[i].penalty);
                
            }
            $('#playerinfo').append('<tr><td><b>Totalt</b></td><td>&nbsp</td><td>&nbsp</td><td>&nbsp</td>'+
            '<td><b>'+start+'</b></td><td><b>'+minutes+'</b></td><td><b>'+subbedin+'</b></td><td><b>'+subbedoff+'</b></td><td><b>'+goals+'</b></td><td><b>'+penalty+'</b></td><td><b>'+owngoal+'</b></td><td><b>'+yellow+'</b></td><td><b>'+red+'</b></td></tr>');
            $('#playerinfo').append('</tbody>');
            $('#player_playingminutes').html(totMins + ' minutter');
            $("#playerinfo").tablesorter({widgets: ['zebra']});
            $('#player_totalgoals_text').html('Mål:');
            $('#player_totalgoals').html(totgoals);
            
            if(json.info[0].position == 'Keeper' || array[0].number == '1' || array[0].is_goalkeeper == '1'){
                $('#player_totalgoals_text').html('Clean sheets:');
                $('#player_totalgoals').html(json.cleansheets);
                $('#player_position').html('Keeper');
            }
            
            $('#player_winpercentage').html(json.winpercentage + ' %');
            $('#similarplayers').empty();
            var string = '';
            for(var key in json.similar){
                var player = json.similar[key];
                string += (getPlayerLink(player.playerid, player.name) + ' - ');
                $('#similar').show();
                $('#similarplayers').show();
            }
            string = string.substr(0, string.length-2);
            $('#similarplayers').append(string);
            
            if(json.similar.length == 0){
                $('#similar').hide();
                $('#similarplayers').hide();
            }
            $('#playerinfo').show();
            $('#next').show();
            $('#previous').show();
            getEventRankPlayer(json);  
            stopLoad();
        }
    }); 
}


function getEventRankPlayer(array)
{
    $('#ranking').empty();
    $('#ranking').append('<h4>Spillerranking i Norge</h4>');
    updatePlayerRank(array.yellow,2);
    updatePlayerRank(array.yellow_red,1);
    updatePlayerRank(array.red,3);
    updatePlayerRank(array.goal,4);
    updatePlayerRank(array.penalty,8);
    updatePlayerRank(array.owngoal,9);
    updatePlayerRank(array.subin,6);
    updatePlayerRank(array.subout,7);
    updatePlayerRank(array.cleansheetrank,12);
}

function updatePlayerRank(array, eventtype)
{
    var list = '<a href="#" style="text-decoration:underline" onclick="getEventsTotal('+eventtype+',0)">'+getEventFromId(eventtype) +'</a>';
    if(array.length > 0) {
        $('#ranking').append('<li>' + list + ': ' + array[0].rank +'. plass ('+array[0].count +')</li>');
        $('#ranking').show();
    }
}

function getLeagueInfo(leagueid,teamid)
{
    startLoad();
    leagueidselected = leagueid;
    if(leagueid == 0){
        $('#info_click').show();
    }
    if(leagueid == getAndreDivAll()){
        //history.pushState("", "Title", 'index.php?season='+season+'&league_id='+getAndreDiv());
        window.location.hash = '/'+season+'/league/'+getAndreDiv();
        $('#news_'+getAndreDiv()).show();
        $('#leagueselect').val(getAndreDiv());
    }else{
        //history.pushState("", "Title", 'index.php?season='+season+'&league_id='+leagueidselected);
        $('#leagueselect').val(leagueidselected);
        window.location.hash = '/'+season+'/league/'+leagueid;
        $('#news_'+leagueidselected).show();
    }
    
    $('#feedback_page').val('Ligaoversikt');
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getLeagueInfo", leagueid: leagueid, teamid: teamid, season: season},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            
            $('#lastupdate').html('Sist oppdatert: ' + json.lastupdate);
            $('#lastupdate').show();
            $('#league_table').show();
            
            if(json.topscorer.length > 0){
                $('#league_topscorer').html(getPlayerLink(json.topscorer[0].playerid,json.topscorer[0].playername + ' - ' + json.topscorer[0].eventcount + ' mål'));
                setTeamLogo($('#league_topscorer_logo'),json.topscorer[0].teamid);
            }
            else{
                $('#league_topscorer').html('Ingen funnet');
            }
            if(json.hometeam.length > 0){
                var hometeam = json.hometeam[0];
                $('#league_hometeam').html(getTeamLink(hometeam.teamid,hometeam.teamname + ' (' + hometeam.wins + '-' + hometeam.draws + "-"+hometeam.loss+') - ' +hometeam.goals+'-'+hometeam.conceded));
                setTeamLogo($('#league_hometeam_logo'),hometeam.teamid);
            }
            else{
                $('#league_hometeam').html('Ingen funnet');
            }
            if(json.awayteam.length > 0){
                var awayteam = json.awayteam[0];
                $('#league_awayteam').html(getTeamLink(awayteam.teamid,awayteam.teamname + ' (' + awayteam.wins + '-' + awayteam.draws + "-"+awayteam.loss+') - ' +awayteam.goals+'-'+awayteam.conceded));
                setTeamLogo($('#league_awayteam_logo'),awayteam.teamid);
            }
            else{
                $('#league_awayteam').html('Ingen funnet');
            }
            updateLeagueTable(json.leaguetable,$('#leaguetable'),'Tabell');
            updateLeagueTable(json.leaguetablehome,$('#leaguetablehome'),'Hjemmetabell');
            updateLeagueTable(json.leaguetableaway,$('#leaguetableaway'),'Bortetabell');
            updateEventTableMin(json.topscorer,$('#totalgoals'),10);
            updateEventTableMin(json.yellow_red,$('#yellow_red'),1);
            updateEventTableMin(json.red,$('#redcard'),3);
            updateEventTableMin(json.goal,$('#goals'),4);
            updateEventTableMin(json.penalty,$('#penalty'),8);
            updateEventTableMin(json.owngoal,$('#owngoal'),9);
            updateEventTableMin(json.subout,$('#subsout'),7);
            updateEventTableMin(json.subin,$('#subsin'),6);
            updateEventTableMin(json.yellow, $('#yellowcard'), 2);
            updatePlayerMinutes(json.minutes, $('#playingminutes'));
            
            updateBreadcrumb(leagueid, teamid, null);
            stopLoad();
            $('#event_table').show();
            $('#events').show();
            $('#eventoverview').show();
        }
    }); 
}

function getTeamInfoSearch(teamid){
    getTeamInfoFull(teamid,'search');
}
function getTeamInfo(teamid){
    getTeamInfoFull(teamid,'');
}
function getTeamInfoFull(teamid,fromPage)
{   
    if(!allowClicks){
        return;
    }
    teamidselected = teamid;    
    window.location.hash = '/'+season+'/team/'+teamid;
    $('#feedback_page').val('Lagoversikt');
    startLoad();
    
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getTeamInfo", teamid: teamid, season: season, from: fromPage},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            
            var array = json;
            if(array.teamtoleague.length == 0){
                $('#noData').show();
                stopLoad();
                return;
            }
            $('#noData').hide();
            updateTeamInfoTable(array);
            updateBreadcrumb(0, teamid, array.teamtoleague);
            var ses = season;
            if(season == 0){
                ses = '';
            }
            updateLeagueTable(array.leaguetable,$('#team_leaguetable'),array.teamtoleague[0].leaguename + ' ' + ses, teamid);
            $('#rankingteam').append('<h4>Lagranking i Norge</h4>');
            
            updateTeamRankList(array.yellowred,1); 
            updateTeamRankList(array.red,3); 
            updateTeamRankList(array.penalty,8);
            updateTeamRankList(array.owngoal,9);
            updateTeamRankList(array.yellow,2);
            var matchcount = array.allmatches.length;
            
            if(array.yellow[0] != null){
                $('#team_yellowcard').html(array.yellow[0].count + getPerMatch(array.yellow[0].count,matchcount));
            }else{
                 $('#team_yellowcard').html('');
            }
            
            var redCards = parseInt((array.red[0] != undefined ? array.red[0].count : 0));
            var yellowRed = parseInt((array.yellowred[0] != undefined ? array.yellowred[0].count : 0));
            var totalRed = redCards + yellowRed;
            $('#team_redcard').html(''+totalRed + getPerMatch(totalRed,matchcount));
            updateTeamRankList(array.goal,4);
            updateTeamRankList(array.subin,6);
            updateTeamRankList(array.subout,7);
            updateTeamRankList(array.cleansheetrank, 12);
            
            updateTeamPlayers(array.teamplayer, array.dangerlist, array.winpercentage);
            if($('#season').val() == '2014'){
                updateLatestMatches(array.latestmatches, array.last5lineup);
                updateNextMatches(array.nextmatches);
            }
            
            updateAllMatches($('#team_allmatches'),array.allmatches, array.goalscorers, teamid);
            updateGoalPie(array.scoringminute,$("#scoringminute"), 'scored');
            updateGoalPie(array.concededminute, $("#concededminute"), 'conceded');
            stopLoad();
            $('#team').show();
            $('#next').show();
            $('#previous').show();
        }
    }); 
    //history.href = "?team_id="+teamid;
}

function getPerMatch(events, match)
{
    if(events != undefined && match != undefined)
    {
        return ' (' +(parseInt(events) / parseInt(match)).toFixed(2) +')';
    }
    return '';
}
function updateTeamRankList(array, eventtype)
{
    var list = '<a href="#" style="text-decoration:underline" onclick="getEventsTotalTeam('+eventtype+',0)">'+getEventFromId(eventtype) +'</a>';
    if(array.length > 0) {
        $('#rankingteam').append('<li>' + list + ': ' + array[0].rank +'. plass ('+array[0].count +')</li>');
    }
    $('#rankingteam').show();
}
function updatePopulareTables(array)
{
    updateBreadcrumbSpecific("Populære","getPopulare()");
    $('#popularePlayers').empty();
    $('#populareTeams').empty();
    $('#trending').empty();
    $('#popularePlayers').append('<caption class="tableheader">Populære&nbspspillere</caption>');
    $('#populareTeams').append('<caption class="tableheader">Populære&nbsplag</caption>');
    $('#trending').append('<caption class="tableheader">Aktuelle&nbspsider&nbspsiste&nbsp24&nbsptimer</caption>');
    for(var i=0;i<array.populare.length;i++){  
        if(array.populare[i].playerid !== undefined){
            $('#popularePlayers').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td style="width:300px;">'+getPlayerLink(array.populare[i].playerid,array.populare[i].playername)+'</td></tr>');
        }
        if(array.populare[i].teamid !== undefined){
             $('#populareTeams').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td style="width:300px;">'+getTeamLink(array.populare[i].teamid,array.populare[i].teamname)+'</td></tr>');
        }
        
    }
    var k = 1;
    for(var key in array.trending){
        var value = array.trending[key];
        k++;
        if(value.type == 'player'){
            $('#trending').append('<tr class='+(k % 2 == 0 ? 'odd' : '')+'><td style="width:300px;">'+getPlayerLink(value.playerid, value.playername)+'</td></tr>');
        }
        if(value.type == 'team'){
            $('#trending').append('<tr class='+(k % 2 == 0 ? 'odd' : '')+'><td style="width:300px;">'+getTeamLink(value.teamid, value.teamname)+'</td></tr>');
        }
        if(value.type == 'preview'){
            $('#trending').append('<tr class='+(k % 2 == 0 ? 'odd' : '')+'><td style="width:300px;">'+getPreviewLinkText(value.matchid,  'Preview: '+value.hometeam  + ' - ' +value.awayteam)+'</td></tr>');
        }
        if(value.type == 'match_internal'){
            $('#trending').append('<tr class='+(k % 2 == 0 ? 'odd' : '')+'><td style="width:300px;">'+getMatchLinkTextInternal(value.matchid, value.hometeam  + ' - ' +value.awayteam)+'</td></tr>');
        }
        
    }
    
    $('#popularePlayers').show();
    $('#populareTeams').show();
    $('#trending').show();
}
function updateLeagueTable(leaguetable, tablename, tableheader, selectedteamid)
{
    tablename.empty();
    tablename.append('<caption class="tableheader">'+tableheader+'</caption>');
    tablename.append(getTableHeader(["#","Lag","S","V","U","T","Mål","+/-","P"]));
    tablename.append('<tbody>');
    var pos = 0;
    for(var key in leaguetable){
        pos++;
        var value = leaguetable[key];
        var teamname = value.teamname.toString();
        var link = getTeamLink(value.teamid,teamname);
        if($('#futsal_league').is(":visible") || $('#futsal_team').is(":visible") || $('#futsal_player').is(":visible")){
            link = getFutsalTeamLink(value.teamid,teamname);
        }
        if(selectedteamid != undefined && selectedteamid == value.teamid){
            tablename.append(getTableRowSelected([pos,link,value.played,value.wins,value.draws,value.loss,""+value.goals+"-"+ value.conceded+"",value.mf,value.points],pos));
            $('#futsal_team_scored').html(value.goals);
            $('#futsal_team_conceded').html(value.conceded);
        }else{
            tablename.append(getTableRow([pos,link,value.played,value.wins,value.draws,value.loss,""+value.goals+"-"+ value.conceded+"",value.mf,value.points],pos));
        }
    }
    tablename.append('</tbody>');
    tablename.show();
}

function updatePreviewTable(array,team)
{   
    var prefix = '#preview_'+team+'_';
    
    $(prefix +'name').html(getTeamLink(array.teamtoleague[0].teamid,array.teamtoleague[0].teamname + ' ('+array.currentposition+'. plass)'));
    
    setTeamLogo($(prefix + 'logo'),array.teamtoleague[0].teamid);
    
    var stat = null;
    if(team == 'home'){
        stat = array.homestats[0];
        $(prefix +'form').html(stat.wins +'-'+stat.draws+'-'+stat.loss+ ' (' + stat.goals+'-'+stat.conceded+')');
        //$(prefix +'streak').html(array.homestreak);
        $(prefix + 'over3ha').html(array.overgoalshome.over3+'%');
        $(prefix + 'over4ha').html(array.overgoalshome.over4+'%');
        $(prefix + 'goalsscored').html(array.scoringhomeaway[0].total_home+' av '+array.scoringhomeaway[0].total+' (' +array.scoringhomeaway[0].percentage_home+'%)');
        $(prefix + 'conceded').html(array.concededhomeaway[0].total_home+' av '+array.concededhomeaway[0].total+' (' +array.concededhomeaway[0].percentage_home+'%)');
        $(prefix + 'position').html(array.currentpositionhome+'. plass');
        var homeString = getOverlibStreakString(array.latestmatcheshome, array.teamtoleague[0].teamid);
        $(prefix + 'lastfive_home').html(homeString.join('-'));
    
       
    }else if(team == 'away'){
        stat = array.awaystats[0];
        $(prefix +'form').html(stat.wins +'-'+stat.draws+'-'+stat.loss+ ' (' + stat.goals+'-'+stat.conceded+')');
        //$(prefix +'streak').html(array.awaystreak);
        $(prefix + 'over3ha').html(array.overgoalsaway.over3+'%');
        $(prefix + 'over4ha').html(array.overgoalsaway.over4+'%');
        $(prefix + 'goalsscored').html(array.scoringhomeaway[0].total_away+' av '+array.scoringhomeaway[0].total+' (' +array.scoringhomeaway[0].percentage_away+'%)');
        $(prefix + 'conceded').html(array.concededhomeaway[0].total_away+' av '+array.concededhomeaway[0].total+' (' +array.concededhomeaway[0].percentage_away+'%)');
        $(prefix + 'position').html(array.currentpositionaway+'. plass');
        var awayString = getOverlibStreakString(array.latestmatchesaway, array.teamtoleague[0].teamid);
        $(prefix + 'lastfive_away').html(awayString.join('-'));
    }
    
    var matchString =  getOverlibStreakString(array.latestmatches,array.teamtoleague[0].teamid);
   
    $(prefix + 'lastfive').html(matchString.join('-'));
    
    var lineup = getLineupArray(array.mostusedplayers);
    var lastlineup = getLineupArray(array.lastlineup);
    
    $(prefix + 'surface').html(array.teamtoleague[0].surface);
    $(prefix + 'lineup').html(getOverlibLineup('Foretrukken 11er. Kamper startet i parantes.', lineup, 'Lag'));
    if(array.lastlineup[0] != undefined){
        $(prefix + 'lastlineup').html(getOverlibLineup('Siste lagoppstilling mot ' + array.lastlineup[0].teamname,lastlineup, 'Siste lag'));
    }
    $(prefix + 'suspensions').html('Ingen');
    $(prefix + 'over3').html(array.overgoals.over3+'%');
    $(prefix + 'over4').html(array.overgoals.over4+'%');
    $(prefix + 'firsthalf_g').html(array.scoringpercentagehalfs[0].firsthalfgoals+' av '+array.scoringpercentagehalfs[0].total+' (' +array.scoringpercentagehalfs[0].percentage_first+'%)');
    $(prefix + 'firsthalf_c').html(array.concededpercentagehalfs[0].first_half+' av '+array.concededpercentagehalfs[0].total+' (' +array.concededpercentagehalfs[0].percentage_first+'%)');
    $(prefix + 'secondhalf_g').html(array.scoringpercentagehalfs[0].secondhalfgoals+' av '+array.scoringpercentagehalfs[0].total+' (' +array.scoringpercentagehalfs[0].percentage_second+'%)');
    $(prefix + 'secondhalf_c').html(array.concededpercentagehalfs[0].second_half+' av '+array.concededpercentagehalfs[0].total+' (' +array.concededpercentagehalfs[0].percentage_second+'%)');
}
function updateEventTableMin(array,table,eventtype){
    var league = leagueidselected
    if(league == '3,4,5,6'){
        league = '8';
    }
    updateEventTable(array,table,eventtype,0,false,-1,league);
}
function updateEventTable(array,table,eventtype,type,edit,scopeeventid,leagueid)
{
    if(type == undefined) {
        type = 0;
    }
    var closeTable = '<a href="#" title="Lukk tabell" onclick="closeTable(\''+(scopeeventid >= 0 ? scopeeventid : '')+'\');return false;"><img style="margin-left:3px" src="images/x.png"></img></a>';
    var tableheaderLink = createEventLink(eventtype,type,leagueid);
    table.empty();
    table.attr('class','tablesorter playerinfo');
    table.append('<caption class="tableheader">'+tableheaderLink+(edit ? closeTable : '')+'</caption><tbody>');
    for (var i=0; i<array.length; i++) {
        var string = '<tr class='+(i % 2 == 0 ? 'odd' : '')+'>';
        if(type == 0) {
            var playerLink = getPlayerLink(array[i].playerid,array[i].playername);
            if($('#futsal_league').is(":visible")){
                playerLink = getFutsalPlayerLink(array[i].playerid,array[i].playername);
            }
           string += '<td style=max-width:140px;overflow:hidden>'+playerLink+'</td>';
        }
        var teamLink = getTeamLink(array[i].teamid,array[i].teamname);
        if($('#futsal_league').is(":visible")){
            teamLink = getFutsalTeamLink(array[i].teamid,array[i].teamname);
        }
        string += '<td style=max-width:100px;overflow:hidden>'+teamLink+' </td>';
        
        if(type == 1){
            string += '<td> </td>';
        }
        string += '<td align=center>'+array[i].eventcount+'</td></tr>';
        table.append(string);
    }
    if(array.length == 0){
        table.hide();
    }else{
        table.show();
    }
}
function updateGoalPie(array, divplaceholder, scoringtype)
{
    $("#pies").show();
    
    $.plot(divplaceholder, array.pie, {
        series: {
            pie: {
                show: true
            }
        },
        grid: {
            hoverable: true
        },
        legend: {
            labelBoxBorderColor: "none"
        }
    });
    divplaceholder.bind("plothover", function (event, pos, item) {
        // axis coordinates for other axes, if present, are in pos.x2, pos.x3, ...
        // if you need global screen coordinates, they are pos.pageX, pos.pageY
        if(item == null){
            $('#infoWindow').hide();
        }
        var info = array.pie[item.seriesIndex].info;
        showInfoWindow(info,array.pie[item.seriesIndex].label,scoringtype);
        
    });
    divplaceholder.show();
}

function showInfoWindow(array, tablename, scoringtype)
{

    if(array !== undefined){
        $('#infoWindow').show();
        $('#infoTable').empty();
        $('#infoTable').append('<caption class="tableheader">'+tablename+'</caption>');
        $('#infoTable').append('<tr><td><b>Spillernavn</b></td><td><b>Minutt</b></td><td><b>Kamp</b></td></tr>');
        for(var i=0;i<array.length;i++){
            if(scoringtype == 'scored'){
                $('#infoTable').append('<tr><td>'+(array[i].teamid == teamidselected ? array[i].playername : 'Selvmål')+'</td><td>'+array[i].minute+'</td><td>'+array[i].hometeamname+' - '+array[i].awayteamname+' ('+array[i].result+')</td></tr>');
            }else if(scoringtype == 'conceded'){
                $('#infoTable').append('<tr><td>'+(array[i].teamid != teamidselected ? array[i].playername : 'Selvmål')+'</td><td>'+array[i].minute+'</td><td>'+array[i].hometeamname+' - '+array[i].awayteamname+' ('+array[i].result+')</td></tr>');
            }
            
        }
    }
}
function updateMatches(array,tablename,header,preview,arraylast5lineup)
{
    tablename.empty();
    tablename.append('<caption class="tableheader">'+header+'</caption>');
    
    tablename.append('<thead><th>Dato</th><th>Hjemmelag&nbsp&nbsp&nbsp&nbsp</th><th>Bortelag&nbsp&nbsp</th><th>Resultat</th><thead>');
    tablename.append('<tbody>');
    for (var i=0; i<array.length; i++) {
        var matchResultLink = getMatchResultLink(array[i].matchid,array[i].result);
        if(arraylast5lineup != undefined){
             var lineupArray = getLineupArray(arraylast5lineup[array[i].matchid]);
             matchResultLink = getOverlibLineupLink('Lagoppstilling', lineupArray, array[i].result,getMatchHref(array[i].matchid), array[i].matchid);
        }
        
        tablename.append(
            '<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
            '<td>'+getDateStringMilli(array[i].timestamp)+'</td>'+
            '<td>'+getTeamLink(array[i].homeid,array[i].homename)+'</td>'+
            '<td>'+getTeamLink(array[i].awayid,array[i].awayname)+'</td>'+
            '<td>'+(i == 0 && preview ? getPreviewLinkText(array[i].matchid, 'Preview') : matchResultLink )+'</td>'+
            '</tr>');
    }
    tablename.append('</tbody>');
    
    if(array.length != 0){
        tablename.show();
    }
}
function updateLatestMatches(array, arraylast5lineup)
{
    updateMatches(array, $('#team_latestmatches'), 'Siste 5 kamper', false, arraylast5lineup);
}
function updateNextMatches(array)
{
    updateMatches(array, $('#team_nextmatches'), 'Neste 5 kamper', true);
}
function updateAllMatches(tablename, array, scorers, selectedteamid)
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
                            if(p.minute !== undefined){
                                scorerArray[p.playerid] += ', '+ p.minute + '\'';
                            }else{
                            }
                        }else{
                            if(p.minute !== undefined){
                                scorerArray[p.playerid] = getPlayerLink(p.playerid, playername) + ' ('+p.minute+'\'';
                            }else{
                                scorerArray[p.playerid] = getPlayerLink(p.playerid, playername);
                            }
                        }
                    }
                }else{
                    if(eventtype == 9){
                        playername = 'Selvmål ';
                        if(scorerArray[p.playerid] != undefined){
                            scorerArray[p.playerid] += ', '+ p.minute + '\'';
                        }else{
                            scorerArray[p.playerid] = playername + ' ('+p.minute+'\'';
                        }
                    }
                }
            }
        }
        var scorerstring = '';
        for(var s in scorerArray){
            scorerArray[s] += ') ';
            scorerstring += scorerArray[s];
        }
        tablename.append(
            '<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
            '<td>'+getDateStringMilli(array[i].timestamp)+'</td>'+
            '<td>'+getTeamLink(array[i].homeid,array[i].homename)+'</td>'+
            '<td>'+getTeamLink(array[i].awayid,array[i].awayname)+'</td>'+
            '<td>'+getMatchResultLink(array[i].matchid, array[i].result)+'</td>'+
            '<td>'+scorerstring+'</td>'+
            '</tr>');
    }
    tablename.append('</tbody>');
    
    if(array.length != 0){
        tablename.show();
    }
}

function updatePlayerMinutes(array,table, scopeeventid)
{
    if(scopeeventid != undefined){
        var closeTable = '<a href="#" onclick="closeTable(\''+(scopeeventid >= 0 ? scopeeventid : '')+'\');return false;"><img style="margin-left:3px" src="images/x.png"></img></a>';
    }
    var le = leagueidselected;
    if(leagueidselected == '3,4,5,6'){
        leagueidselected = '8';
    }
    var tableheaderLink = '<a href="#" style="cursor:pointer" onclick="getEventsTotal(11,'+leagueidselected+');return false;">Spilleminutter</a>';
  
    
    table.empty();
    table.attr('class','tablesorter playerinfo');
    table.append('<caption class="tableheader">'+tableheaderLink+(scopeeventid != undefined ? closeTable : '')+'</caption>');
    table.append('<tr><th style="width: 55%"><th style="width: 32%"><th style="width: 13%"></tr>');
    table.append('<tbody>');
    for (var i=0; i<array.length; i++) {
        table.append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td>'+getPlayerLink(array[i].playerid,array[i].playername)+'</td>'+
            '<td>'+getTeamLink(array[i].teamid,array[i].teamname)+'</td>'+
            '<td>'+array[i].eventcount+'</td></tr>');
    }
    table.append('</tbody>');
    if(array.length == 0){
        table.hide();
    }else{
        table.show();
    }
   
}
function updateSuspensionList(array)
{
    updateBreadcrumbSpecific("Suspensjoner","getSuspensionList(134365)");
    $('#suspensionTable').empty();
    $('#suspensionTable').append('<thead><th>Spillernavn</th><th>Lag</th><th>Suspensjonsgrunn&nbsp&nbsp&nbsp</th><th>Suspendert i kamp</th><th>Kampdato</th></thead>');
    addSuspensionTable(array.redCard,'rødt kort');
    addSuspensionTable(array.threeYellow,'3 gule kort');
    addSuspensionTable(array.fiveYellow,'5 gule kort');
    addSuspensionTable(array.sevenYellow,'7 gule kort');
    addSuspensionTable(array.moreYellow);
    $('#suspensionText').html('');
    $('#suspensionText').show();
    
    $('#suspensionTable').show();
    $('#suspensionList').show();
}

function updateTeamInfoTable(array)
{
    if(array.length == 0){
        return ;
    }
    if(array.realteamid == -1){
        setTeamLogo( $('#team_logo'),array.teamtoleague[0].teamid);
    }else{
        setTeamLogo( $('#team_logo'),array.realteamid);
    }
    
    if(array.topscorer.length != 0){ 
        $('#team_topscorer').html(getPlayerLink(array.topscorer[0].playerid,array.topscorer[0].playername)+' - ' +array.topscorer[0].events+' mål');
        if(array.topscorercount == 2){
            $('#team_topscorer').append(' ('+(array.topscorercount-1) +' annen spiller)');
        }
        else if(array.topscorercount > 2){
            $('#team_topscorer').append(' ('+(array.topscorercount-1) +' andre spillere)');
        }
    }else{
         $('#team_topscorer').html('');
    }
    if(array.winpercentage.length != 0){//array.mostminutes[0].playerid
        $('#team_winpercentage').html(getPlayerLink(array.winpercentage[0].playerid,array.winpercentage[0].playername)+' - '+array.winpercentage[0].percentage+' %');
    }else{
        $('#team_winpercentage').html('');
    }
    if(array.mostminutes.length != 0){//array.mostminutes[0].playerid
        $('#team_minutes').html(getPlayerLink(array.mostminutes[0].playerid,array.mostminutes[0].playername)+' - '+array.mostminutes[0].minutes+' minutter');
    }else{
         $('#team_minutes').html('');
    }
    if(array.mostyellow.length != 0){
        $('#team_yellow').html(getPlayerLink(array.mostyellow[0].playerid,array.mostyellow[0].playername)+' - '+array.mostyellow[0].events+' gul'+(array.mostyellow[0].events == 1 ? 't' : 'e') +' kort');
    }else{
        $('#team_yellow').html('');
    }
    if(array.mostred.length != 0){
        $('#team_red').html(getPlayerLink(array.mostred[0].playerid,array.mostred[0].playername)+' - '+array.mostred[0].events+' rød'+(array.mostred[0].events == 1 ? 't' : 'e') +' kort');
    }else{
        $('#team_red').html('');
    }
    var matchcount = array.allmatches.length;
    
    if(array.scoringminute.length != 0){
        $('#team_scored').html(array.scoringminute.total + getPerMatch(array.scoringminute.total, matchcount));
    }
    
    if(array.concededminute.length != 0){
        $('#team_conceded').html(array.concededminute.total + getPerMatch(array.concededminute.total, matchcount));
    }
//    if(array.scoringminute.length != 0){
//        $('#team_yellowcard').html(array.scoringminute.total);
//    }
    if(array.homestats.length != 0){
        var stat = array.homestats[0]
        $('#team_home').html(stat.points +' poeng: '+stat.wins +'-'+stat.draws+'-'+stat.loss+ ' (' + stat.goals+'-'+stat.conceded+')');
    }else{
        $('#team_home').html('');
    }
    if(array.awaystats.length != 0){
        stat = array.awaystats[0]
        $('#team_away').html(stat.points +' poeng: '+stat.wins +'-'+stat.draws+'-'+stat.loss+ ' (' + stat.goals+'-'+stat.conceded+')');
    }else{
        $('#team_away').html('');
    }

    if(array.cleansheets.length != 0){
        $('#team_cleansheets').html(array.cleansheets + getPerMatch(array.cleansheets, matchcount));
    }else{
        $('#team_cleansheets').html('0');
    }
    if(array.attendance[0].average != 0){
        $('#team_attendance_avg').html(array.attendance[0].average);
        $('#team_attendance_max').html(array.attendance[0].max);
    }else{
        $('#team_attendance_avg').html('Ikke funnet');
        $('#team_attendance_max').html('Ikke funnet');
    }
    if(array.overgoals.over3.length != 0){
        $('#team_over3').html(array.overgoals.over3+'%');
    }
    if(array.overgoals.over4.length != 0){
        $('#team_over4').html(array.overgoals.over4+'%');
    }
    if(array.teamtoleague[0].surface.length != 0){
        $('#team_surface').html(array.teamtoleague[0].surface);
    }
    $('#team_tops_table').show();
}
function addSuspensionTable(array, reason)
{
    if(array.length == 0){
        return ;
    }
    for(var i=0;i<array.length;i++){
        $('#suspensionTable').append('<tr class='+(i % 2 == 0 ? 'odd' : 'even')+'>'+
            '<td>'+getPlayerLink(array[i].playerid,array[i].playername)+'</td>'+
            '<td>'+getTeamLink(array[i].teamid,(array[i].teamid == array[i].hometeamid ? array[i].homename : array[i].awayname))+'</td>'+
            '<td>'+(reason !== undefined ? reason : array[i].count + ' gule kort') +'</td>'+
            '<td>'+getPreviewLinkText(array[i].matchid, array[i].homename+' - '+array[i].awayname)+'</td>'+
            '<td>'+getDateStringMilli(array[i].timestamp)+'</td>'+
        '</tr>');
    }
    $('#suspensionTable').tablesorter({widgets: ['zebra']});
    
}
function pieHover(event, pos, obj) {
    if (!obj)
        return;
    percent = parseFloat(obj.series.percent).toFixed(2);
    $("#pieHover").html('<span style="font-weight: bold; color: '+obj.series.color+'">'+obj.series.label+' ('+percent+'%)</span>');
}

function startLoad()
{
    $('#loaderdiv').show();    
    $('#rankingteam').empty(); 
    $('#scope').hide();
    $('#welcometext').hide();
    $('#events').hide();
    $('#allEvents').hide();
    $('#rankingteam').hide();
    $('#eventoverview').hide();
    $('#teamplayerinfo').hide();
    $('#playerinfo').hide();
    $('#player').hide();
    $('.teamname').hide();
    $('#team').hide();
    $('.playername').hide();
    $("#pies").hide();
    $("#populare").hide();
    $("#popularePlayers").hide();
    $("#populareTeams").hide();
    $("#infoWindow").hide();
    $('#warning').hide();
    $('#ranking').hide();
    $('#radio').hide();
    $('#suspensionTable').hide();
    $('#suspensionTableDanger').hide();
    $('#playerminutes').hide();
    $('#playerminutes_table').hide();
    $('#suspensionText').hide();
    $('#lastupdate').hide();
    $('#player_table').hide();
    $('#league_table').hide();
    $('#player_logo').hide();
    $('#player_label').hide();
    $('#social').hide();
    $('#suspensionSelect').hide();
    $('#similar').hide();
    $('#similarplayers').hide();
    $('#noData').hide();
    $('#preview').hide();
    $('#preview_table').hide();
    $('#referee_table').hide();
    $('#referee_table_specific').hide();
    $('#totalgoals').hide();
    $('#suspensionList').hide();
    $('#referee').hide();
    $('#trending').hide();
    $('#event_table').hide();
    $('#preview_weather').hide();
    $('[id^="transfer_"]').hide();
    $('[id^="match_"]').hide();
    $('[id^="news_"]').hide();
    $('[id^="event_top"]').hide();
    $('#teamSelect').hide();
    $('#allEventsSelect').hide();
    $('#allEventsSelectType').hide();
    $('#nationalteam').hide();
    $('#info_click').hide();
    $('#info_scope_click').hide();
    $('#label_event').hide();
    $('#label_league').hide();
    $('[id^="futsal_"]').hide();
    $('[id^="preview_"]').hide();
    $('#preview_label').hide();
    
    $("html").css("cursor", "progress");
    spinner = new Spinner(opts).spin();
    $('#loader').append(spinner.el);
    allowClicks = false;
}

function getMatch(matchid)
{
    if(!allowClicks){
        return;
    }
   // history.pushState("", "Title", 'index.php?page=match&matchid='+matchid);
    window.location.hash = '/'+season+'/page/match/'+matchid;
    $('#feedback_page').val('Kamprapport');
    startLoad();
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getMatch", matchid: matchid},
        error: function () {
            stopLoad();
        },
        success: function(json) {
            
            var array = json;
            updateMatchTable(array);
            updateMatchEvents(array.events);
            updateBreadcrumbSpecific('Kamp', '',array.events[0].homename+' - '+array.events[0].awayname,'getMatch('+matchid+')');
            stopLoad();
            
            $('[id^="match_"]').show();
        }
    }); 
}

function updateMatchTable(array)
{   
    var prefix = '#matches_';
    $(prefix +'table').empty();
    
    var hlineup = getLineupArrayFullnameLink(array.homelineup);
    var alineup =  getLineupArrayFullnameLink(array.awaylineup);
    var sortedLineup = new Array();
    var sortedLineupAway = new Array();
    
    for (var p in hlineup){
        var pos = hlineup[p];
        for(var pa in pos){
            sortedLineup.push(pos[pa]);
        }
    }
    for (var p1 in alineup){
        var pos1 = alineup[p1];
        for(var pa1 in pos1){
            sortedLineupAway.push(pos1[pa1]);
        }
    }
    var homeid = 0;
    var awayid = 0;
    if(array.homerealteamid == -1){
        homeid = array.events[0].homeid;
    }else{
        homeid = array.homerealteamid;
    }
    if(array.awayrealteamid == -1){
        awayid = array.events[0].awayid;
    }else{
        awayid = array.awayrealteamid;
    }
    
    array = array.events[0];
    
    $(prefix +'table').append(
        '<tr>'+
            '<td align="center"><img src="images/logos/'+homeid+'.png" onclick="getTeam(0,'+array.homeid+')" style="cursor: pointer;float: bottom; vertical-align: middle;"></td>'+
            '<td align="center"></td>'+
            '<td align="center"><img src="images/logos/'+awayid+'.png" onclick="getTeam(0,'+array.awayid+')" style="cursor: pointer;float: bottom; vertical-align: middle;"></td>'+
        '</tr>'+
        '<tr>'+
            '<td align="center"><h4>'+getTeamLink(array.homeid,array.homename)+'</td>'+
            '<td align="center"></td>'+
            '<td align="center"><h4>'+getTeamLink(array.awayid,array.awayname)+'</td>'+
        '</tr>'+
        '<tr id>'+
            '<td align="center"><h1>'+array.homescore+'</h1></td>'+
            '<td align="center"></td>'+
            '<td align="center"><h1>'+array.awayscore+'</h1></td>'+
        '</tr>');
    for (var i = 0; i < sortedLineup.length; i++) {
        $(prefix +'table').append( 
            '<tr '+(i == 10 ? 'id=match_lastrow' : '') +'>'+
                '<td align="center" style="margin:3px">'+sortedLineup[i]+'</td>'+
                '<td align="center"></td>'+
                '<td align="center">'+sortedLineupAway[i]+'</td>'+
            '</tr>');
    }
    var attendance = 'Ikke funnet';
    if(array.attendance !== null){
        attendance = array.attendance;
    }
    $(prefix +'table').append('<tr>'+
        '<td>&nbsp</td></tr>'+
        '<tr><td colspan="3" align="center">'+getMatchLinkText(array.matchid,'Offisiell kamprapport')+'</td></tr>'+
        '<tr><td colspan="3" align="center">Dommer: '+getRefereeLink(array.refereeid,array.refereename)+'</td></tr>'+
        (array.attendance != 0 ? '<tr><td colspan="3" align="center">Tilskuere: '+attendance+'</td></tr>' : '') +
        '<tr><td>&nbsp</td></tr>');
   
    
}
function updateMatchEvents(array)
{
    for(var eventa in array){
        var event = array[eventa];
        var img = '<img src="images/events/'+getEventtype(event.eventtype)+'" style="margin-left:3px;margin-right:3px"></img>';
        if(event.teamid == event.homeid){
            var homeOut = '';
            if(event.eventtype == 6 || event.eventtype == 7){
                var homesub = getPlayerLinkWithId(event.playerinid,event.playerinname);
                homeOut = getPlayerLastnameLink(event.playeroutid,event.playeroutname) +' (ut) / ' + getPlayerLastnameLink(event.playerinid,event.playerinname) +' (inn) ';
                //$('#match_'+event.playerinid).append(img);
            }else{
                homeOut = getPlayerLink(event.playerid,event.playername);
                if(event.eventtype == 8){
                    homeOut += ' (straffe)';
                }
                if(event.eventtype == 9){
                    homeOut += ' (selvmål)';
                }
            }
            // Home event
            $('#matches_table').append(
            '<tr>'+
            '<td align="center">'+homeOut+'</td>'+
            '<td align="center">'+event.minute+'\' ' + img+'</td>'+
            '<td align="center"></td>'+
            '</tr>');
        
        }else{
            // Away event
            var awayOut = '';
            if(event.eventtype == 6 || event.eventtype == 7){
                var awaysub = getPlayerLinkWithId(event.playerinid,event.playerinname);
                awayOut += getPlayerLastnameLink(event.playeroutid,event.playeroutname) +' (ut) / ' + getPlayerLastnameLink(event.playerinid,event.playerinname) +' (inn) ';
                //$('#match_'+event.playerinid).append(img);
            }else{
                awayOut += getPlayerLink(event.playerid,event.playername);
                if(event.eventtype == 8){
                    awayOut += ' (straffe)';
                }
                if(event.eventtype == 9){
                    awayOut += ' (selvmål)';
                }
            }
            // Home event
            $('#matches_table').append(
            '<tr>'+
            '<td align="center"></td>'+
            '<td align="center">'+event.minute+'\' ' + img +'</td>'+
            '<td align="center">'+awayOut+'</td>'+
            '</tr>');
        }
        if(awaysub != undefined && homesub != undefined){
            //$('#matches_table > tbody > tr').eq(13).after('<tr><td align=center>'+homesub+'</td><td></td><td align=center>'+awaysub+'</td></tr>');
            awaysub = undefined;
            homesub = undefined;
        }
        $('#match_'+event.playerid).append(img);
    }
}

function stopLoad()
{
    $('#social').show();
    $('#loaderdiv').hide();
    $("html").css("cursor", "default");
    spinner.spin(false);
    allowClicks = true;
}

