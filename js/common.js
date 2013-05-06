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
var spinner;
var allowClick;
var playeridselected;
var teamidselected;
var leagueidselected;
var eventselected;

var title = 'FotballSentralen.com';
var season = 2013;
var timeout = 15000; //ms

function selectSuspendedLeague()
{
    var league = $('#suspensionSelect').val()
    window.history.pushState("", "Title", 'index.php?page=suspensionpage&league_id='+league);
    getSuspensionList(league);
}
function selectSeason()
{
    //only possible when in HOME 
    season = $('#season').val();
    getLeagues();
    
    if($('#player').is(":visible")){
       //window.location.href = 'index.php?season='+season+'&player_id='+playeridselected;
       getPlayer(playeridselected);
    }
    if($('#teamplayerinfo').is(":visible")){
        getTeam(0, teamidselected);
    }
    if($('#eventoverview').is(":visible")){
        getTeam(leagueidselected,0);
    }
    if($('#events').is(":visible")){
        getEventsTotal(eventselected);
    }
    if($('#playerminutes').is(":visible")){
        getTotalPlayerMinutes();
    }
}
function setSeason(season_){
    season = season_;
    $('#season').val(season_);
}
function nextSeason() {
    var season_ = parseInt(season);
    if($('#season').val() != '2013'){
        $('#season').val(''+(season_+1));
        selectSeason();
    }
}

function previousSeason() {
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
    if(!allowClicks){
        return;
    }
    startLoad();
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
function getPreview()
{
    if(!allowClicks){
        return;
    }
    startLoad();
    $('#preview').show();
    stopLoad();
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
            //2div
            var leagueString = 2 + '' + season;
            getLeagueInfo(getAndreDivAll(),0);
        }
        else{
            getLeagueInfo(leagueid,0);
        }      
    }
}
function updateBreadcrumb(leagueid,teamid,jsonarray)
{
    var leagueidfound ;
    var teamidfound;
    var teamname;
    var playername;
    var playerid; 
    
    
    $('#breadcrumbs').empty();
    
    //ALL
    if(teamid == 0 && leagueid == 0){
        $('#breadcrumbs').append('<li>'+season+'</li>');
        $('#breadcrumbs').append('<li><a href="index.php">Norge</a></li>');
        $("#breadcrumbs").breadcrumbs("home");
        $('#league_name').html('Hele Norge');
        return;
    }
    //POPULæRe
    if(teamid == -1 && leagueid == -1){
        $('#breadcrumbs').append('<li>'+season+'</li>');
        $('#breadcrumbs').append('<li onclick="getPopulare()">Populære</li>');
        $("#breadcrumbs").breadcrumbs("home");
        return;
    }
    
    if(teamid != 0){
        var json = jsonarray;
        leagueidfound = json[0].leagueid;
        teamidfound = json[0].teamid;
        teamname = json[0].teamname;
        playername = json[0].playername;
        playerid = json[0].playerid;
        
        $('#breadcrumbs').append('<li>'+season+'</li>');
        $('#breadcrumbs').append('<li><a onclick="getTeam(0,0)">Norge</a></li>');
        
        $('#league_name').html(getLeagueName(leagueidfound));
        
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
        
        
        $('#breadcrumbs').append('<li>'+season+'</li>');
        $('#breadcrumbs').append('<li><a href="index.php">Norge</a></li>');

        $('#league_name').html(getLeagueName(leagueid));
        
        if(leagueid == getAdeccoligaen()){
              $('#breadcrumbs').append('<li>'+getLeagueLink(getAdeccoligaen())+'</li>');
             //$('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAdeccoligaen()+'">Adeccoligaen</a></li>');
             document.title = "Adeccoligaen | " + title;
        }
        if(leagueid == getTippeligaen()){
            $('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getTippeligaen()+'">Tippeligaen</a></li>');
            document.title = "Tippeligaen | " + title;
        }
        if(leagueid == getAndreDivAll()){
            $('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAndreDiv()+'">2.divisjon</a></li>');
            document.title = "2.divisjon | " + title;
        }
        
        if(leagueid == getAndreDiv1()){
            $('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAndreDiv()+'">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAndreDiv1()+'">Avdeling 1</a></li>');
            document.title = "2.divisjon avd 1 | " + title;
        }
        if(leagueid == getAndreDiv2()){
            $('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAndreDiv()+'">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAndreDiv2()+'">Avdeling 2</a></li>');
            document.title = "2.divisjon avd 2 | " + title;
        }
        if(leagueid == getAndreDiv3()){
            $('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAndreDiv()+'">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAndreDiv3()+'">Avdeling 3</a></li>');
            document.title = "2.divisjon avd 3 | " + title;
        }
        if(leagueid == getAndreDiv4()){
            $('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAndreDiv()+'">2.divisjon</a></li>');
            $('#breadcrumbs').append('<li><a href="index.php?season='+season+'&league_id='+getAndreDiv4()+'">Avdeling 4</a></li>');
            document.title = "2.divisjon avd 4 | " + title;
        }
        $("#breadcrumbs").breadcrumbs("home");
    }
    
}
function getEventsTotalSelected()
{
    getEventsTotal(eventselected);
}
function getEventsTotalTeamSelected()
{
    getEventsTotalTeam(eventselected);
}

function getTotalPlayerMinutes(){
    
    if(!allowClicks){
        return;
    }
    
    startLoad();
  
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {
            action: "getTotalPlayerminutes", 
            season: season
        },
        error: function () {
            stopLoad()
        },
        success: function(json) {
           
            var array = json;
            $('#playerminutes_table').empty();
            $('#playerminutes_table').append('<caption class="tableheader" onclick="getTotalPlayerMinutes()">Spilleminutter</caption>');
            for (var i=0; i<array.length; i++) {
                
                $('#playerminutes_table').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td><a href="index.php?season='+season+'&player_id='+array[i].playerid+'">' +array[i].playername+ '</a></td>'+
                    '<td><a href="index.php?season='+season+'&team_id='+array[i].teamid+'">' +array[i].teamname+ '</a></td>'+
                    '<td>'+array[i].minutesplayed+'</td></tr>');
            }
            stopLoad();
            
            $('#playerminutes').show();
            $('#playerminutes_table').show();
        }
    });
    
}

function getEventsTotal(eventtype)
{
    if(!allowClicks){
        return;
    }
    
    eventselected = eventtype;
    $('#playerradio').prop('checked',true);
    
    startLoad();
    
    var tableheader = getEventFromId(eventtype);
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {
            action: "getEventsTotal", 
            eventtype : eventtype,
            season: season
        },
        error: function () {
            stopLoad()
        },
        success: function(json) {
            
            var array = json;
            
            $('#allEvents').empty();
            
            $('#allEvents').append('<caption class="tableheader" onclick="getEventsTotal('+eventtype+')">'+tableheader+'</caption>');
            //$('#allEvents').append('<thead><tr><td><b>Spillernavn</td><td><b>Lag<b></td><td><b>Antall<b></td></tr></thead>');
            for (var i=0; i<array.length; i++) {
                $('#allEvents').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td><a href="index.php?season='+season+'&player_id='+array[i].playerid+'">' +array[i].playername+ '</a></td>'+
                    '<td><a href="index.php?season='+season+'&team_id='+array[i].teamid+'">' +array[i].teamname+ '</a></td>'+
                    '<td>'+array[i].eventcount+'</td></tr>');
            }
            stopLoad();
            $('#allEvents').show();
            $('#events').show();
            $('#radio').show();
        }
    });
}
function getEventsTotalTeam(eventtype)
{
    if(!allowClicks){
        return;
    }
    eventselected = eventtype;

    $('#teamradio').prop('checked',true);
    
    startLoad();

    updateBreadcrumb(0,0,null);
    
    var tableheader = getEventFromId(eventtype);
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {
            action: "getEventsTotalTeam", 
            eventtype : eventtype,
            season: season
        },
        error: function () {
            stopLoad()
        },
        success: function(json) {
            $('#allEvents').empty();
            var array = json;
            $('#allEvents').append('<caption class="tableheader" onclick="getEventsTotal('+eventtype+')">'+tableheader+'</caption>');
            $('#allEvents').append('<thead><tr><td><b>Lag<b></td><td><b>Antall<b></td></tr></thead>');
            for (var i=0; i<array.length; i++) {
                $('#allEvents').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td><a href="index.php?season='+season+'&team_id='+array[i].teamid+'">' +array[i].teamname+ '</a></td>'+
                    '<td>'+array[i].eventcount+'</td></tr>');
            }
            stopLoad();
            $('#allEvents').show();
            $('#events').show();
            $('#radio').show();
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

function updateTeamPlayers(teamidarray)
{
    
    var array = teamidarray;
    $('#teamplayerinfo').empty();    
    $('#teamplayerinfo').show(); 
    $('#teamplayerinfo').append('<thead><th>Nr</th><th>Navn</th><th>Spilleminutter&nbsp&nbsp&nbsp</th><th>Fra&nbspstart&nbsp&nbsp&nbsp</th><th>Mål&nbsp&nbsp&nbsp</th><th>Straffemål&nbsp&nbsp&nbsp</th><th>Selvmål&nbsp&nbsp&nbsp</th><th>Gule&nbspkort&nbsp&nbsp&nbsp</th><th>Røde&nbspkort&nbsp&nbsp&nbsp</th><th>Byttet&nbsput&nbsp&nbsp&nbsp</th><th>Byttet&nbspinn&nbsp&nbsp&nbsp</th></thead>');
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

        $('#teamplayerinfo').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td>'+array[i].shirtnumber+'</td>'+
            '<td><a href="index.php?season='+season+'&player_id='+array[i].playerid+'">' +array[i].playername+ '</a></td>'+
            '<td>'+array[i].minutesplayed+'</td>'+
            '<td>'+array[i].started+'</td>'+
            '<td>'+array[i].goals+'</td>'+
            '<td>'+array[i].penalty+'</td>'+
            '<td>'+array[i].owngoals+'</td>'+
            '<td>'+array[i].yellowcards+'</td>'+
            '<td>'+array[i].redcards+'</td>'+
            '<td>'+array[i].subbedin+'</td>'+
            '<td>'+array[i].subbedoff+'</td>'+
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
    $('#teamplayerinfo').append('<tr><td><b>Totalt</b></td><td>&nbsp</td><td><b>'+minutes+'</b></td><td><b>'+start+'</b></td>'+
    '<td><b>'+goals+'</b></td><td><b>'+penalty+'</b></td><td><b>'+owngoals+'</b></td><td><b>'+yellow+'</b></td><td><b>'+red+'</b></td><td><b>'+subbedin+'</b></td><td><b>'+subbedoff+'</b></td></tr>');
    $('#teamplayerinfo').append('</tbody>');
    $('#teamplayerinfo').tablesorter();
    $('#team_players_used').html(players_used);
        
}
function getPlayer(playerid)
{
    if(!allowClicks){
        return;
    }
    playeridselected = playerid;
    
    window.history.pushState("", "Title", 'index.php?season='+season+'&player_id='+playeridselected);
    startLoad();
    $('#player').show();

    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getPlayerInfo", playerid: playerid, season: season},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            if(json.playertoleague.length == 0){
                stopLoad();
                $('#noData').show();
                return;
            }
            $('#noData').hide();
            $('#playerinfo').empty();
            var array = json.playerinfo;
            if(array == null){
               
            }
            $('#playerinfo').append('<thead>'+
                '<th>Dato</th><th>Hjemmelag</th><th>Bortelag</th>'+
                '<th>Resultat&nbsp&nbsp</th><th>Start&nbsp&nbsp</th>'+
                '<th>Min&nbsp&nbsp</th><th>Mål&nbsp&nbsp</th><th>Straffemål&nbsp&nbsp</th>'+
                '<th>Selvmål&nbsp&nbsp&nbsp</th><th>Gule&nbspkort&nbsp&nbsp</th>'+
                '<th>Rødt&nbspkort&nbsp</th><th>Byttet&nbspinn&nbsp&nbsp</th>'+
                '<th>Byttet&nbsput&nbsp&nbsp</th></thead>');
            $('#playerinfo').append('<tbody>');
            updateBreadcrumb(0, 1, json.playertoleague);
            
            $('#player_logo').attr("src",'images/logos/'+json.playertoleague[0].teamid+'.png');
            $('#player_logo').error(function (){
                $('#player_logo').attr("src",'images/logos/blank.png');
            });
            $('#player_logo').show();
            $('#player_table').show();
            $('#player_playingminutes').html(json.playingminutes + ' %');
            if(json.info[0].height != undefined && json.info[0].height != 0){
                $('#player_height').html(json.info[0].height + ' cm');
            }
            if(json.info[0].dateofbirth != undefined && json.info[0].dateofbirth != 0){
                var date = new Date(''+json.info[0].dateofbirth);
                var age=((Date.now() - date) / (31557600000))
                age = Math.floor(age);
                $('#player_dateofbirth').html(getDateString(json.info[0].dateofbirth)+" ("+age+" år)");
            }
            
            $('#player_position').html(json.info[0].position);
            
            
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

            for (var i=0; i<array.length; i++) {
                $('#playerinfo').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td>'+getDateString(array[i].dateofmatch)+'</td>'+
                    '<td><a href="index.php?season='+season+'&team_id='+array[i].homeid+'">' +array[i].hometeamname+ '</a></td>'+
                    //'<td>-</td>'+
                    '<td><a href="index.php?season='+season+'&team_id='+array[i].awayid+'">' +array[i].awayteamname+ '</a></td>'+
                    '<td><a target="blank" href="http://www.fotball.no/System-pages/Kampfakta/?matchId='+array[i].matchid+'">' +array[i].result+ '</a></td>'+
                    '<td>'+(array[i].start == 0 ? 'Nei' : 'Ja' )+'</td>'+
                    '<td>'+array[i].minutesplayed+'</td>'+
                    '<td>'+array[i].goals+'</td>'+
                    '<td>'+array[i].penalty+'</td>'+
                    '<td>'+array[i].owngoal+'</td>'+
                    '<td>'+array[i].yellowcards+'</td>'+
                    '<td>'+array[i].redcards+'</td>'+
                    '<td>'+(array[i].subbedin == 0 ? 'Nei' : 'Ja' )+'</td>'+
                    '<td>'+(array[i].subbedoff == 0 ? 'Nei' : 'Ja' )+'</td>'+
                    '</tr>');
                
                
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
            '<td><b>'+start+'</b></td><td><b>'+minutes+'</b></td><td><b>'+goals+'</b></td><td><b>'+penalty+'</b></td><td><b>'+owngoal+'</b></td><td><b>'+yellow+'</b></td><td><b>'+red+'</b></td><td><b>'+subbedin+'</b></td><td><b>'+subbedoff+'</b></td></tr>');
            $('#playerinfo').append('</tbody>');
            $("#playerinfo").tablesorter();
            
            $('#player_totalgoals').html(totgoals);
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
    updatePlayerRank(array.red,3);
    updatePlayerRank(array.goal,4);
    updatePlayerRank(array.penalty,8);
    updatePlayerRank(array.owngoal,9);
    updatePlayerRank(array.subin,6);
    updatePlayerRank(array.subout,7);
    
}

function updatePlayerRank(array, eventtype)
{
    var list = '<a href="#" onclick="getEventsTotal('+eventtype+')">'+getEventFromId(eventtype) +'</a>';
    if(array.length > 0) {
        $('#ranking').append('<li>' + list + ': ' + array[0].rank +'. plass ('+array[0].count +')</li>');
        $('#ranking').show();
    }
}
function getLeagueInfo(leagueid,teamid)
{
    startLoad();
    
    leagueidselected = leagueid;


    if(leagueid == getAndreDivAll()){
        window.history.pushState("", "Title", 'index.php?season='+season+'&league_id='+getAndreDiv());
    }else{
        window.history.pushState("", "Title", 'index.php?season='+season+'&league_id='+leagueidselected);
    }
    
    
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
                $('#league_topscorer').html('<a href="index.php?season='+season+'&player_id='+json.topscorer[0].playerid+'">'+json.topscorer[0].playername + ' - ' + json.topscorer[0].eventcount + ' mål </a>');
                if(json.topscorercount == 2){
                    $('#league_topscorer').append(' ('+(json.topscorercount-1) +' annen spiller)');
                }
                else if(json.topscorercount > 2){
                    $('#league_topscorer').append(' ('+(json.topscorercount-1) +' andre spillere)');
                }
            }   
            if(json.hometeam.length > 0){
                var hometeam = json.hometeam[0];
                $('#league_hometeam').html(getTeamLink(hometeam.teamid,hometeam.teamname + ' (' + hometeam.wins + '-' + hometeam.draws + "-"+hometeam.loss+') - ' +hometeam.goals+'-'+hometeam.conceded));
            }
            if(json.awayteam.length > 0){
                var awayteam = json.awayteam[0];
                $('#league_awayteam').html(getTeamLink(awayteam.teamid,awayteam.teamname + ' (' + awayteam.wins + '-' + awayteam.draws + "-"+awayteam.loss+') - ' +awayteam.goals+'-'+awayteam.conceded));
            }
            
            updateLeagueTable(json.leaguetable);
            updateEventTable(json.red,$('#redcard'),3);
            updateEventTable(json.goal,$('#goals'),4);
            updateEventTable(json.penalty,$('#penalty'),8);
            updateEventTable(json.owngoal,$('#owngoal'),9);
            updateEventTable(json.subout,$('#subsout'),7);
            updateEventTable(json.subin,$('#subsin'),6);
            updateEventTable(json.yellow, $('#yellowcard'), 2);
            updatePlayerMinutes(json.minutes);
            updateBreadcrumb(leagueid, teamid, null);
            stopLoad();
        }
    }); 
}
function getTeamInfo(teamid)
{   
    if(!allowClicks){
        return;
    }
    $('#rankingteam').empty(); 

    teamidselected = teamid;    

    window.history.pushState("", "Title", 'index.php?season='+season+'&team_id='+teamidselected);

    startLoad();
    
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getTeamInfo", teamid: teamid, season: season},
        error: function () {
            stopLoad()
        },
        success: function(json) {
            
            var array = json;
            if(array.teamtoleague.length == 0){
                stopLoad();
                return;
            }
            updateTeamInfoTable(array);
            updateBreadcrumb(0, teamid, array.teamtoleague);
            
            $('#rankingteam').append('<h4>Lagranking i Norge</h4>');
            updateTeamRankList(array.red,3); 
            updateTeamRankList(array.penalty,8);
            updateTeamRankList(array.owngoal,9);
            updateTeamRankList(array.yellow,2);
            updateTeamRankList(array.goal,4);
            updateTeamRankList(array.subin,6);
            updateTeamRankList(array.subout,7);

            updateTeamPlayers(array.teamplayer);
            updateLatestMatches(array.latestmatches);
            updateNextMatches(array.nextmatches);
            updateAllMatches(array.allmatches);
            updateGoalPie(array.scoringminute,$("#scoringminute"));
            updateGoalPie(array.concededminute, $("#concededminute"));
            stopLoad();
            
            $('#team').show();
        }
    }); 
    //window.location.href = "?team_id="+teamid;
}
function updateTeamRankList(array, eventtype)
{
    var list = '<a href="#" onclick="getEventsTotalTeam('+eventtype+')">'+getEventFromId(eventtype) +'</a>';
    if(array.length > 0) {
        $('#rankingteam').append('<li>' + list + ': ' + array[0].rank +'. plass ('+array[0].count +')</li>');
    }
    $('#rankingteam').show();
}
function updatePopulareTables(array)
{
    updateBreadcrumb(-1, -1);
    $('#popularePlayers').empty();
    $('#populareTeams').empty();
    $('#popularePlayers').append('<caption class="tableheader">Populære&nbspspillere</caption>');
    $('#populareTeams').append('<caption class="tableheader">Populære&nbsplag</caption>');
    //var list = '<a href="#" onclick="getEventsTotalTeam('+eventtype+')">'+getEventFromId(eventtype) +'</a>';
    for(var i=0;i<array.length;i++){  
        if(array[i].playerid !== undefined){
            $('#popularePlayers').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+' style="min-width:300px;"><td><a href="index.php?season='+season+'&player_id='+array[i].playerid+'">'+array[i].playername +'</a></td></tr>');
        }
        if(array[i].teamid !== undefined){
             $('#populareTeams').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td><a href="index.php?season='+season+'&team_id='+array[i].teamid+'">'+array[i].teamname +'</a></td></tr>');
        }
        
    }
    $('#popularePlayers').show();
    $('#populareTeams').show();
}
function updateLeagueTable(leaguetable)
{
    $('#leaguetable').append(getTableHeader(["#","Lag","S","V","U","T","Mål","+/-","P"]));
    $('#leaguetable').append('<tbody>');
    var pos = 0;
    for(var key in leaguetable){
        pos++;
        var value = leaguetable[key];
        $('#leaguetable').append(getTableRow([pos,getTeamLink(value.teamid,value.teamname),"3",value.wins,value.draws,value.loss,""+value.goals+" - "+ value.conceded+"",value.mf,value.points],pos));
    }
    $('#leaguetable').append('</tbody>');
    $('#leaguetable').show();
}

function getEventsFromDB(leagueid, teamid)
{
    if(!allowClicks){
        return;
    }
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {
            action: "getEvents", 
            teamid: teamid, 
            leagueid: leagueid
        },
        error: function () {
            stopLoad()
        },
        success: function(json) {
            var array = json;
            
            updateEventTable(array.red,$('#redcard'),3);
            updateEventTable(array.goal,$('#goals'),4);
            updateEventTable(array.penalty,$('#penalty'),8);
            updateEventTable(array.owngoal,$('#owngoal'),9);
            updateEventTable(array.subout,$('#subsout'),7);
            updateEventTable(array.subin,$('#subsin'),6);
            updateEventTable(array.yellow, $('#yellowcard'), 2);
        }
    });
}
function updateEventTable(array,table,eventtype)
{
    var tableheader = getEventFromId(eventtype);
    table.empty();
    table.append('<caption class="tableheader" onclick="getEventsTotal('+eventtype+')">'+tableheader+'</caption><tbody>');
    table.append('<tr><th style="width: 55%"><th style="width: 36%"><th style="width: 9%"></tr>');
    for (var i=0; i<array.length; i++) {
        table.append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td><a href="index.php?season='+season+'&player_id='+array[i].playerid+'" >' +array[i].playername.toString().substring(0,24)+ '</a></td>'+
            '<td><a href="index.php?season='+season+'&team_id='+array[i].teamid+'">' +array[i].teamname+ '</a></td>'+
            '<td>'+array[i].eventcount+'</td></tr>');
    }
    table.append('</tbody><tfoot><tr></tr></tfoot>');
    if(array.length == 0){
        table.hide();
    }else{
        table.show();
    }
    $('#events').show();
    $('#eventoverview').show();
}
function updateGoalPie(array, divplaceholder)
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
        showInfoWindow(info,array.pie[item.seriesIndex].label);
        
    });
    divplaceholder.show();
}

function showInfoWindow(array, tablename)
{
    if(array !== undefined){
        $('#infoWindow').show();
        $('#infoTable').empty();
        $('#infoTable').append('<caption class="tableheader">'+tablename+'</caption>');
        $('#infoTable').append('<tr><td><b>Spillernavn</b></td><td><b>Minutt</b></td><td><b>Kamp</b></td></tr>');
        for(var i=0;i<array.length;i++){
            $('#infoTable').append('<tr><td>'+array[i].playername+'</td><td>'+array[i].minute+'</td><td>'+array[i].hometeamname+' - '+array[i].awayteamname+' ('+array[i].result+')</td></tr>');
        }
    }
}
function updateMatches(array,tablename,header)
{
    tablename.empty();
    tablename.append('<caption class="tableheader">'+header+'</caption>');
    
    tablename.append('<thead><th>Dato</th><th>Hjemmelag&nbsp&nbsp&nbsp&nbsp</th><th>Bortelag&nbsp&nbsp</th><th>Resultat</th><thead>');
    //tablename.append('<tr><th style="width: 20%"><th style="width: 40%"><th style="width: 40%"><th style="width: 10%"></tr>');
    tablename.append('<tbody>');
    for (var i=0; i<array.length; i++) {
        tablename.append(
            '<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
            '<td>'+getDateString(array[i].dateofmatch)+'</td>'+
            '<td><a href="index.php?season='+season+'&team_id='+array[i].homeid+'">' +array[i].homename+ '</a></td>'+
            '<td><a href="index.php?season='+season+'&team_id='+array[i].awayid+'">' +array[i].awayname+ '</a></td>'+
            '<td><a target="blank" href="http://www.fotball.no/System-pages/Kampfakta/?matchId='+array[i].matchid+'">'+array[i].result+'</a></td>'+
            '</tr>');
    }
    tablename.append('</tbody>');
    
    if(array.length != 0){
        tablename.show();
    }
}
function updateLatestMatches(array)
{
    updateMatches(array, $('#team_latestmatches'), 'Siste 5 kamper');
}
function updateNextMatches(array)
{
    updateMatches(array, $('#team_nextmatches'), 'Neste 5 kamper');
}
function updateAllMatches(array)
{
    var tablename = $('#team_allmatches');
    
    tablename.empty();
    tablename.append('<caption class="tableheader">Alle kamper</caption>');
    //<th>Målscorere</th>
    tablename.append('<thead><th>Dato</th><th>Hjemmelag&nbsp&nbsp&nbsp&nbsp</th><th>Bortelag&nbsp&nbsp</th><th>Resultat</th><thead>');
    //tablename.append('<tr><th style="width: 20%"><th style="width: 40%"><th style="width: 40%"><th style="width: 10%"></tr>');
    tablename.append('<tbody>');
    for (var i=0; i<array.length; i++) {
        tablename.append(
            '<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
            '<td>'+getDateString(array[i].dateofmatch)+'</td>'+
            '<td><a href="index.php?season='+season+'&team_id='+array[i].homeid+'">' +array[i].homename+ '</a></td>'+
            '<td><a href="index.php?season='+season+'&team_id='+array[i].awayid+'">' +array[i].awayname+ '</a></td>'+
            '<td><a target="blank" href="http://www.fotball.no/System-pages/Kampfakta/?matchId='+array[i].matchid+'">'+array[i].result+'</a></td>'+
           // '<td>'+getPlayerLink(123,'Erik Sivertsen (12,89)')+'</td>'+
            '</tr>');
    }
    tablename.append('</tbody>');
    
    if(array.length != 0){
        tablename.show();
    }
}


function updatePlayerMinutes(array)
{
    $('#playingminutes').empty();
    $('#playingminutes').append('<caption class="tableheader" onclick="getTotalPlayerMinutes()">Spilleminutter</caption>');
    $('#playingminutes').append('<tr><th style="width: 55%"><th style="width: 32%"><th style="width: 13%"></tr>');
    $('#playingminutes').append('<tbody>');
    for (var i=0; i<array.length; i++) {
        $('#playingminutes').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'><td><a href="index.php?season='+season+'&player_id='+array[i].playerid+'">' +array[i].playername+ '</a></td>'+
            '<td><a href="index.php?season='+season+'&team_id='+array[i].teamid+'">' +array[i].teamname+ '</a></td>'+
            '<td>'+array[i].minutesplayed+'</td></tr>');
    }
    $('#playingminutes').append('</tbody>');
    if(array.length == 0){
        $('#playingminutes').hide();
    }else{
        $('#playingminutes').show();
    }
   
}
function updateSuspensionList(array)
{
    updateBreadcrumb(-1, -1);
    $('#suspensionTable').empty();
    $('#suspensionTable').append('<thead><th>Spillernavn</th><th>Lag</th><th>Suspensjonsgrunn&nbsp&nbsp&nbsp</th><th>Suspendert i kamp</th><th>Kampdato</th></thead>');
    //addDangerTable(array.twoYellow,array.fourYellow);
    addSuspensionTable(array.redCard,'rødt kort');
    addSuspensionTable(array.threeYellow,'3 gule kort');
    addSuspensionTable(array.fiveYellow,'5 gule kort');
    //addDangerTable(array.twoYellow, null);
    $('#suspensionText').html('Suspensjonslisten er under testing! Feil eller mangler? <a href="mailto:kontakt@fotballsentralen.com">Rapporter<a>. ');
    $('#suspensionText').show();
    
    $('#suspensionTable').show();
}
function addDangerTable(arraytwo, arrayfour)
{
    $('#suspensionTableDanger').empty();
    $('#suspensionTableDanger').append('<thead><th>Spillernavn</th><th>Lag</th><th>Gule&nbspkort</th></thead>');
    
    if(arraytwo.length == 0 && arrayfour.length == 0){
        return ;
    }
    for(var i=0;i<arraytwo.length;i++){
      
        $('#suspensionTableDanger').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
            '<td><a href="index.php?season='+season+'&player_id='+arraytwo[i].playerid+'">' +arraytwo[i].playername+ '</a></td>'+
            '<td><a href="index.php?season='+season+'&team_id='+arraytwo[i].teamid+'">' +(arraytwo[i].teamid == arraytwo[i].hometeamid ? arraytwo[i].homename : arraytwo[i].awayname)+ '</a></td>'+
            '<td>2 gule kort</td>'+
        '</tr>');
    }
    for(var playerid in arraytwo){
        $('#suspensionTableDanger').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
            '<td>'+getPlayerLink(playerid,arraytwo[playerid].playername)+'</td>'+
            '<td>'+getTeamLink(arraytwo[playerid].teamid,'Nuts')+'</td>'+
            '<td>2 gule kort</td>');
    }
//    for(i=0;i<arrayfour.length;i++){
//      
//        $('#suspensionTableDanger').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
//            '<td><a href="index.php?season='+season+'&player_id='+arrayfour[i].playerid+'">' +arrayfour[i].playername+ '</a></td>'+
//            '<td><a href="index.php?season='+season+'&team_id='+arrayfour[i].teamid+'">' +(arrayfour[i].teamid == arrayfour[i].hometeamid ? arrayfour[i].homename : arrayfour[i].awayname)+ '</a></td>'+
//            '<td>4 gule kort</td>'+
//        '</tr>');
//    }
    $('#suspensionTableDanger').tablesorter();
    $('#suspensionTableDanger').show();
}
function updateTeamInfoTable(array)
{
    if(array.length == 0){
        return ;
    }
    $('#team_logo').attr("src",'images/logos/'+array.teamtoleague[0].teamid+'.png');
    $('#team_logo').error(function (){
        $('#team_logo').attr("src",'images/logos/blank.png');
    });
    
    if(array.topscorer.length != 0){
        $('#team_topscorer').html('<a href="index.php?season='+season+'&player_id='+array.topscorer[0].playerid+'">' +array.topscorer[0].playername+ '</a> - '+array.topscorer[0].events+' mål');
        if(array.topscorercount == 2){
            $('#team_topscorer').append(' ('+(array.topscorercount-1) +' annen spiller)');
        }
        else if(array.topscorercount > 2){
            $('#team_topscorer').append(' ('+(array.topscorercount-1) +' andre spillere)');
        }
    }
    if(array.mostminutes.length != 0){
        $('#team_minutes').html('<a href="index.php?season='+season+'&player_id='+array.mostminutes[0].playerid+'">' +array.mostminutes[0].playername+ '</a> - '+array.mostminutes[0].minutes+' minutter');
    }
    if(array.mostyellow.length != 0){
        $('#team_yellow').html('<a href="index.php?season='+season+'&player_id='+array.mostyellow[0].playerid+'">' +array.mostyellow[0].playername+ '</a> - '+array.mostyellow[0].events+' gul'+(array.mostyellow[0].events == 1 ? 't' : 'e') +' kort');
    }
    if(array.mostred.length != 0){
        $('#team_red').html('<a href="index.php?season='+season+'&player_id='+array.mostred[0].playerid+'">' +array.mostred[0].playername+ '</a> - '+array.mostred[0].events+' rød'+(array.mostred[0].events == 1 ? 't' : 'e') +' kort');
    }
    if(array.scoringminute.length != 0){
        $('#team_scored').html(array.scoringminute.total);
    }
    if(array.concededminute.length != 0){
        $('#team_conceded').html(array.concededminute.total);
    }
    if(array.homestats.length != 0){
        var stat = array.homestats[0]
        $('#team_home').html(stat.points +' poeng: '+stat.wins +'-'+stat.draws+'-'+stat.loss+ ' (' + stat.goals+'-'+stat.conceded+')');
    }
    if(array.awaystats.length != 0){
        stat = array.awaystats[0]
        $('#team_away').html(stat.points +' poeng: '+stat.wins +'-'+stat.draws+'-'+stat.loss+ ' (' + stat.goals+'-'+stat.conceded+')');
    }

    if(array.cleansheets.length != 0){
        $('#team_cleansheets').html(array.cleansheets);
    }else{
        $('#team_cleansheets').html('0');
    }
    $('#team_over3').html(array.overgoals.over3+'%');
    $('#team_over4').html(array.overgoals.over4+'%');
    $('#team_tops_table').show();
}
function addSuspensionTable(array, reason)
{
    if(array.length == 0){
        return ;
    }
    for(var i=0;i<array.length;i++){
      
        $('#suspensionTable').append('<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+
            '<td><a href="index.php?season='+season+'&player_id='+array[i].playerid+'">' +array[i].playername+ '</a></td>'+
            '<td><a href="index.php?season='+season+'&team_id='+array[i].teamid+'">' +(array[i].teamid == array[i].hometeamid ? array[i].homename : array[i].awayname)+ '</a></td>'+
            '<td>'+reason+'</td>'+
            '<td><a target="blank" href="http://www.fotball.no/System-pages/Kampfakta/?matchId='+array[i].matchid+'">'+array[i].homename+' - '+array[i].awayname+'</a></td>'+
            '<td>'+getDateString(array[i].dateofmatch)+'</td>'+
        '</tr>');
    }
    $('#suspensionTable').tablesorter();
}
function pieHover(event, pos, obj) {
    if (!obj)
        return;
    percent = parseFloat(obj.series.percent).toFixed(2);
    $("#pieHover").html('<span style="font-weight: bold; color: '+obj.series.color+'">'+obj.series.label+' ('+percent+'%)</span>');
}

function startLoad()
{
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
    $('#social').hide();
    $('#suspensionSelect').hide();
    $('#similar').hide();
    $('#similarplayers').hide();
    $('#noData').hide();
    $('#preview').hide();
    
    $("html").css("cursor", "progress");
    spinner = new Spinner(opts).spin();
    $('#loader').append(spinner.el);
    allowClicks = false;
}

function stopLoad()
{
    $('#social').show();
    $("html").css("cursor", "default");
    spinner.spin(false);
    allowClicks = true;
}

