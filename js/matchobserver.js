var allowClicks = true;
var selectedMatch = '';

function getInfo(){
    if(!allowClicks){
        return;
    }
    startLoad();
    $('#matches').hide();
    $('#odds_match').hide();
    $('#odds_firsthalf').hide();
    $('[id^="match_"]').hide();
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        timeout: 30000,
        data: {
            action: "getInfo",
            userid : $('#userid').val()
        },
        error: function() {
            location.reload();
        },
        success: function(json) {
            $("#team_detail").tabs("option","disabled", [1,2,3,4,5]);
            $("#matchlist_div").tabs("option","disabled", [0,1,2,3,4,5,6]);
            $('#matchlist_playable_body').empty();
            updateTabs(json.matches);
            selectHighestTab(json.matches);
            $('#matchlist').show();
            autoRefreshMatches();
            stopLoad();
        },
        
    });
}
function autoRefreshMatches(){
    setTimeout(function(){
       updateMatchesOnly();
       autoRefreshMatches();
    }, 15000);
}
function updateMatchesOnly(){
    if($('[id^="input_"]').is(":focus")){
        return;
    }
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "getInfo",
            userid: $('#userid').val()
        },
        success: function(json) {
            updateTabs(json.matches);
        }
    });
}

function updateTabs(json){
    $('#matchlist_playable_body').empty();
    updateMatchesMO(json.seconddiv4,'seconddiv4');
    updateMatchesMO(json.seconddiv3,'seconddiv3');
    updateMatchesMO(json.seconddiv2,'seconddiv2');
    updateMatchesMO(json.seconddiv1,'seconddiv1');
    updateMatchesMO(json.firstdiv,'firstdiv');
    updateMatchesMO(json.tippeligaen,'tippeliga');
}

function selectHighestTab(json) {
    if(json.tippeligaen != undefined){ $("#matchlist_div").tabs("select",0); return;}
    if(json.firstdiv != undefined){$("#matchlist_div").tabs("select",1);return;}
    if(json.seconddiv1 != undefined){$("#matchlist_div").tabs("select",2);return;}
    if(json.seconddiv2 != undefined){$("#matchlist_div").tabs("select",3);return;}
    if(json.seconddiv3 != undefined){$("#matchlist_div").tabs("select",4);return;}
    if(json.seconddiv4 != undefined){$("#matchlist_div").tabs("select",5);return;}
}

function updateMatchesMO(array,type){
    
    if(array == undefined){
        return;
    }
    var index = $('#matchlist_div a[href="#'+type+'"]').parent().index();
    $("#matchlist_div").tabs("enable",index);
    var table = $('#matchlist_'+type+"_body");
    table.empty();
    
    for(var i=0;i<array.length;i++){
        var warnCount = 0;
        var row = '<tr>';
        var style = 'background-color:none;text-align:top;';
        
        if(array[i].homelineup == 1 && array[i].awaylineup == 1){
            style = 'background-color:#33CC33';
        }else if(array[i].homelineup == 1 || array[i].awaylineup == 1){
            style = 'background-color:#CC9933';
        }else if(array[i].homesquad == 1 || array[i].awaysquad == 1){
            style = 'background-color:#66CCFF';
        }
        row += '<td rowspan=2 style="'+style+'">'+getMatchDateString(array[i].timestamp)+'</td>';
        var homename = '';
        
        var tablea = '';
        if(array[i].nowgoal.odds1x2 != undefined){
            tablea = createOddsTable(array[i].nowgoal.odds1x2);
            row += '<td align=center rowspan=2 style="'+style+'"><a href="javascript:void(0);" onmouseover="return overlib(\''+tablea+'\', WIDTH, 600);" onmouseout="return nd();">Odds</a></td>';
        }else{
            row += '<td align=center rowspan=2 style="'+style+'"><a href="javascript:void(0);" onmouseover="return overlib(\'Ikke tilgjengelig enda\', WIDTH, 115);" onmouseout="return nd();">Odds</a></td>';
        }
        
        if(array[i].nowgoal.results != undefined){
            var h2hdata = '<b>Head2Head:</b></br>';
            h2hdata += createResultTable(array[i].nowgoal.results.H2H);
            row += '<td align=center rowspan=2 style="'+style+'"><a href="javascript:void(0);" onmouseover="return overlib(\''+h2hdata+'\', WIDTH, 800);" onmouseout="return nd();">H2H</a></td>';
            
            var tabledata = '<b>Siste 20 kamper:</b></br>';
            tabledata += createResultTable(array[i].nowgoal.results.lasthome);
            homename =  '<a href="javascript:void(0);" onmouseover="return overlib(\''+tabledata+'\', WIDTH, 800);" onmouseout="return nd();" onclick="getMatchMO('+array[i].matchid+');return false;">'+array[i].homename+'</a>';
        
        }else{
            row += '<td align=center rowspan=2 style="'+style+'"><a href="javascript:void(0);" onmouseover="return overlib(\'Ikke tilgjengelig enda\', WIDTH, 115);" onmouseout="return nd();">H2H</a></td>';
            homename = '<a href="#" onclick="getMatchMO('+array[i].matchid+');return false;">'+array[i].homename+'</a>';
        }
        
        if(array[i].homespread != undefined){
            row += '<td style='+style+'>'+getDiv(homename + ' ' + array[i].homespread, array[i].homeprice)+'</td>';
        }else{
            row += '<td style='+style+'>'+homename+'</td>';
        }
        var inputhomemore='<input type="text" size=2 id=input_home_more_'+array[i].matchid+' value='+array[i].homemorepercentage+' title="H-1"></input>';
        var inputhome='<input type="text" size=2 id=input_home_'+array[i].matchid+' value='+array[i].homepercentage+' title="H"></input>';
        var inputdraw='<input type="text" size=2 id=input_draw_'+array[i].matchid+' value='+array[i].drawpercentage+' title="U"></input>';
        var inputaway='<input type="text" size=2 id=input_away_'+array[i].matchid+' value='+array[i].awaypercentage+' title="B"></input>';
        var inputawaymore='<input type="text" size=2 id=input_away_more_'+array[i].matchid+' value='+array[i].awaymorepercentage+' title="B-1"></input>';
        var button ='<input type="button" onclick=updatePercentage('+array[i].matchid+') id=input_button_'+array[i].matchid+' value=OK></input>';
        
        var input = '';
        if(array[i].homespread != undefined && array[i].homespread <= -0.75){
            input = inputhomemore;
        }
        input += inputhome+'' + inputdraw + '' + inputaway;
        if(array[i].awayspread != undefined && array[i].awayspred <= -0.75){
            input += inputawaymore;
        }
        
        row += '<td rowspan=2 style="'+style+'">'+input+' ' + button + '</td>';
        row += '<td style="'+style+';text-align:center"><text id=match_'+array[i].matchid+'_valuehome style=font-weight:bold>' + array[i].valuehome+'</text></td>';
        // Varsel
        row += '<td rowspan=2 style="text-align:center;'+style+'">';
        
        if(array[i].derby != undefined) {
            row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Derby\', WIDTH, 75);" onmouseout="return nd();">D </a>';
            warnCount++;
        }
        if(array[i].forecast.symbol != undefined) {
            if(array[i].forecast.alert == true){
                row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Ekstremvær\', WIDTH, 75);" onmouseout="return nd();">E </a>';
                warnCount++;
            }
        }
        if(array[i].homesurface != array[i].awaysurface){
           warnCount++;
           row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Underlag mismatch\', WIDTH, 115);" onmouseout="return nd();">U </a>';
        }
        if(array[i].tightfixurehome != 0){
            warnCount++;
           row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Tight fixture ('+array[i].homename+')\', WIDTH, 115);" onmouseout="return nd();">HF </a>';
        }
        if(array[i].tightfixureaway != 0){
            warnCount++;
           row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Tight fixture ('+array[i].awayname+')\', WIDTH, 115);" onmouseout="return nd();">BF </a>';
        }
        if(array[i].susphome >= array[i].settings['MIN_PLAYERS_SUSPENDED']['value']){
            warnCount++;
            row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Hjemmelag suspensjoner\', WIDTH, 135);" onmouseout="return nd();">'+array[i].susphome+'HS </a>';
        }
        if(array[i].suspaway >= array[i].settings['MIN_PLAYERS_SUSPENDED']['value']){
            warnCount++;
            row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Bortelag suspensjoner\', WIDTH, 135);" onmouseout="return nd();">'+array[i].suspaway+'BS </a>';
        }
        if(array[i].preferedalerthome != 0){
            warnCount++;
            row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Mangler førstelagsspillere ('+array[i].homename+') \', WIDTH, 155);" onmouseout="return nd();">HM </a>';
        }
        if(array[i].preferedalertaway != 0){
            warnCount++;
            row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Mangler førstelagsspillere ('+array[i].awayname+') \', WIDTH, 155);" onmouseout="return nd();">BM </a>';
        }
        
        row += '</td>';
        // Varsel end
        
        if(array[i].totaloddsline != undefined){
            row += '<td style='+style+'>'+getDiv('Over '+array[i].totaloddsline,array[i].totalover)+'</td>';
        }else{
            row += '<td style='+style+'>&nbsp;</td>';
        }
        row += '</tr><tr>';
        
        var awayname = '';
        if(array[i].nowgoal.results != undefined){
            tabledata = '<b>Siste 20 kamper:</b></br>';
            tabledata += createResultTable(array[i].nowgoal.results.lastaway);
            awayname = '<a href="javascript:void(0);" onmouseover="return overlib(\''+tabledata+'\', WIDTH, 800);" onmouseout="return nd();" onclick="getMatchMO('+array[i].matchid+');return false;">'+array[i].awayname+'</a>';
        
        }else{
            awayname = '<a href="#" onclick="getMatchMO('+array[i].matchid+');return false;">'+array[i].awayname+'</a>';
        }
        if(array[i].awayspread != undefined){
            row += '<td style="'+style+'">'+getDiv(awayname + ' ' + array[i].awayspread, array[i].awayprice)+'</td>';
        }else{
            row += '<td style="'+style+'">'+awayname+'</td>';
        }
        row += '<td style="'+style+';text-align:center;"><text id=match_'+array[i].matchid+'_valueaway style=font-weight:bold>' + array[i].valueaway+' </text></td>';
        if(array[i].totaloddsline != undefined){
            row += '<td style="'+style+'">'+getDiv('Under '+array[i].totaloddsline,array[i].totalunder)+'</td>';
        }else{
            row += '<td style="'+style+'">&nbsp;</td>';
        }
        row += '</tr>';
        
        if(warnCount >= array[i].settings['MIN_WARN_MATCH']['value']){
            var index1 = $('#matchlist_div a[href="#playable"]').parent().index();
            $("#matchlist_div").tabs("enable",index1);
            $('#matchlist_playable_body').append(row);
        }
        table.append(row);
    }
    table.show();
}

function getDiv(column1, column2)
{
    var div = '<div style=clear:both;>';
    div += '<text style=float:left;margin-left:3px>'+column1+'</text>';
    div += '<text style=float:right;margin-right:3px><b>'+column2+'</b></text>';
    div += '</div>';
    return div;
}
function getSuspensionString(array, teamid){
    var suspArray = [];
    for(var key in array.threeYellow){
        var value = array.threeYellow[key];
        if(value['teamid'] == teamid){
            suspArray.push(value['playername'] + ' (3 gule)');
        }
    }
    for(var key1 in array.redCard){
        var value1 = array.redCard[key1];
        if(value1['teamid'] == teamid){
            suspArray.push(value1['playername'] + ' (rødt)');
        }
    }
    for(var key2 in array.fiveYellow){
        var value2 = array.fiveYellow[key2];
        if(value2['teamid'] == teamid){
            suspArray.push(value2['playername'] + ' (5 gule)');
        }
    }
    for(var key3 in array.sevenYellow){
        var value3 = array.sevenYellow[key3];
        if(value3['teamid'] == teamid){
            suspArray.push(value3['playername'] + ' (7 gule)');
        }
    }
    for(var key4 in array.moreYellow){
        var value4 = array.moreYellow[key4];
        if(value4['teamid'] == teamid){
            suspArray.push(value4['playername'] + ' ('+value4['count']+' gule)');
        }
    }
    var susp = suspArray.join('<br/>');
    if(suspArray.length == 0){
        susp = 'Ingen';
    }
    return susp;
}

function createResultTable(array){
    var table = '<table class=table style=background-color:white>';
    table += '<thead><tr><td>Dato</td><td>Liga&nbsp&nbsp&nbsp</td><td>Hjemmelag&nbsp&nbsp&nbsp</td><td>Bortelag&nbsp&nbsp&nbsp</td><td>Fulltid</td><td>Pause</td><td>H</td><td>U</td><td>B</td><td>Line</td><td>H</td><td>B</td><td>O/U</td></tr></thead>';
    table += '<tbody>';
    
     for(var i=0;i<array.length;i++){
        
        var homescore = array[i].score.split("-")[0];
        var awayscore = array[i].score.split("-")[1];
        
        var oddsstyle = 'style=background-color:#33CC33';
        var stylehome = '';
        var styledraw = '';
        var styleaway = '';
        
        if(parseInt(homescore) > parseInt(awayscore)){
            stylehome = oddsstyle;
        }else if(parseInt(homescore) == parseInt(awayscore)){
            styledraw = oddsstyle;
        }else{
            styleaway = oddsstyle;
        }
        
        table += '<tr>';
        table += '<td>'+array[i].date+'</td>';
        table += '<td>'+array[i].league+'</td>';
        table += '<td>'+array[i].hometeam+'</td>';
        table += '<td>'+array[i].awayteam+'</td>';
        table += '<td>'+array[i].score+'</td>';
        table += '<td>'+array[i].htscore+'</td>';
        table += '<td '+stylehome+'>'+(array[i].home != null ? array[i].home : '')+'</td>';
        table += '<td '+styledraw+'>'+(array[i].draw != null ? array[i].draw : '')+'</td>';
        table += '<td '+styleaway+'>'+(array[i].away != null ? array[i].away : '')+'</td>';
        table += '<td>'+(array[i].handicapline != null ? array[i].handicapline : '')+'</td>';
        table += '<td>'+(array[i].handicaphome != null ? array[i].handicaphome : '')+'</td>';
        table += '<td>'+(array[i].handicapaway != null ? array[i].handicapaway : '')+'</td>';
        table += '<td>'+array[i].overunder+'</td>';
        table += '</tr>';
    }
    table += '</tbody>'
    table += '</table>';
    return table;
}
function createOddsTable(array){
    var table = '<table class=table style=background-color:white>';
    
    var firststyle = 'background-color:lightgrey';
    
    table += '<thead>';
    table += '<tr>';
    table += '<td>Oppdatert</td>';
    table += '<td>Bookie</td>';
    table += '<td>Start H</td>';
    table += '<td>H</td>';
    table += '<td>Start U</td>';
    table += '<td>U</td>';
    table += '<td>Start B</td>';
    table += '<td>B</td>';
    
    
    table += '</tr>';
    table += '</thead>';
    table += '<tbody>';
    
     for(var i=0;i<array.length;i++){
        
        var homestyle = '';
        var drawstyle = '';
        var awaystyle = '';
        var home = parseFloat(array[i].home);
        var draw = parseFloat(array[i].draw);
        var away = parseFloat(array[i].away);
        var firsthome = parseFloat(array[i].firsthome);
        var firstdraw = parseFloat(array[i].firstdraw);
        var firstaway = parseFloat(array[i].firstaway);

        if(home > firsthome){
            homestyle = 'background-color:#33CC33';
        }else if(home < firsthome){
             homestyle = 'background-color:#FF0000';
        }
        
        if(draw > firstdraw){
            drawstyle = 'background-color:#33CC33';
        }else if(draw < firstdraw){
            drawstyle = 'background-color:#FF0000';
        }

        if(away > firstaway){
            awaystyle = 'background-color:#33CC33';
        }else if(away < firstaway) {
            awaystyle = 'background-color:#FF0000';
        }
        
        table += '<tr>';
        table += '<td>'+array[i].lastupdate+'</td>';
        table += '<td>'+array[i].provider+'</td>';
        table += '<td style='+firststyle+'>'+firsthome+'</td>';
        table += '<td style='+homestyle+'>'+home+'</td>';
        table += '<td style='+firststyle+'>'+firstdraw+'</td>';
        table += '<td style='+drawstyle+'>'+draw+'</td>';
        table += '<td style='+firststyle+'>'+firstaway+'</td>';
        table += '<td style='+awaystyle+'>'+away+'</td>';
        table += '</tr>';
    }
    table += '</tbody>'
    table += '</table>';
    return table;
}
function getMatchMO(matchid){
    if(!allowClicks){
        return;
    }
    var includeOthersHome = $('#home_team_include_source').is(':checked');
    var includeOthersAway = $('#away_team_include_source').is(':checked');
       
    startLoad();
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "getMatch",
            includeHome: includeOthersHome,
            includeAway: includeOthersAway,
            matchid: matchid
        },
        success: function(json) {
            selectedMatch = json.info.matchid;
            $('[id^="match_"]').show();
            $("#team_detail").tabs("option","disabled", [1,2,3,4,5]);
            $('#home_team').html(json.info.homename);
            $('#home_team').attr('href',json.info.home_homepage);
            $('#home_team').attr('target','_blank');
            $('#away_team').html(json.info.awayname);
            $('#away_team').attr('href',json.info.away_homepage);
            $('#away_team').attr('target','_blank');
            
            updateMatchBasic(json);
            updateNews(json.homenews,'home');
            updateNews(json.awaynews,'away');
            updateOdds(json.matchodds,'match');
            
            $("#team_detail").tabs('enable', 1);
            $("#team_detail").tabs('enable', 2);
            $("#team_detail").tabs('enable', 3);
            $("#team_detail").tabs('enable', 4);
            
            stopLoad();
        }
    });
}

function appendRow(table, column, columnval){
    table.append('<tr><td style="border-right: 1px solid black"><b>'+column+'</b></td><td>'+columnval+'</td></tr>');
}
function appendPlayerRow(table, player, team){
    
    if(player.playername == undefined){
        return;
    }
    var isMissing = false;
    if(team.indexOf("missing") > -1){
        isMissing = true;
    }
    var type = 'away';
    if(team.indexOf("home") > -1){
        type = 'home';
    }
    
    var button = '<td><input type="button" value="Fjern" onclick="removePlayerFromSource(\''+player.playername+'\',\''+type+'\')"></input></td>';
    if(isMissing){
        button = '<td><input type="button" value="Tropp" onclick="addPlayerToSource(\''+player.playername+'\',\''+type+'\')"></input></td>';
    }
    if(team == 'hometeam' || team == 'awayteam' || team.indexOf("squad") == -1){
        button = '';
    }
    
    table.append('<tr>'+
        '<td style="border-right: 1px solid black">'+player.playername+'</td>'+
        '<td>'+player.key+'</td>'+
        '<td>'+player.startedlast+'</td>'+
        '<td>'+player.mostused+'</td>'+
        '<td>'+player.lastfive+' / 5</td>'+
        '<td>'+player.startcount+'</td>'+
        '<td>'+player.squadcount+'</td>'+
        '<td>'+player.squadstatus+'</td>'+
        '<td>'+player.playtime+'%</td>'+
        ''+button+''+
    '</tr>');
}
function appendSummaryRow(table, summary){
    table.append('<tr>'+
        '<td style="border-right: 1px solid black">&nbsp;</td>'+
        '<td><b>'+summary.totalkey+'</b></td>'+
        '<td><b>'+summary.laststarted+'</b></td>'+
        '<td><b>'+summary.preferred+'</b></td>'+
        '<td>&nbsp;</td>'+
        '<td><b>'+summary.totalstart+'</b></td>'+
        '<td><b>'+summary.totalsquad+'</b></td>'+
        '<td>&nbsp;</td>'+
        '<td><b>'+summary.totalplaytime+'%</b></td>'+
    '</tr>');
}
function updateMatchBasic(json){
    
    var basic = json.info.homename + " - " + json.info.awayname + ', ' + getNewsDateString(json.info.timestamp);
    var surface = json.info.surface + ' ('+json.info.surface_condition+')';
    var referee = (json.info.refereename == undefined ? 'Ikke satt opp' : '<a href="#" onclick="getRefereeInfo('+json.info.refereeid+');return false;">'+json.info.refereename+'</a>');
    var derby = (json.info.level == undefined ? 'Nei' : 'Ja ('+json.info.level+')');
    
    var forecast = 'Ikke klart';

    if(json.forecast.symbol != undefined){
        forecast = json.forecast.symbol + '. ' + json.forecast.wind_speed + ', ' + json.forecast.temperature + ' grader. ' + json.forecast.precipitation + ' mm nedbør.';
    }

    // BASIC INFO
    $('#basic_body').empty();
    appendRow($('#basic_body'),'Info',basic);
    appendRow($('#basic_body'),'Spilles på',surface);
    appendRow($('#basic_body'),'Dommer',referee);
    appendRow($('#basic_body'),'Derby',derby);
    appendRow($('#basic_body'),'Værvarsel',forecast);
    
//    $('#team_detail a[href="#referee"]').attr('onclick','getRefereeInfo('+json.info.refereeid+');return false');
    
    
    // HOMETEAM
//    $('#news_detail a[href=#hometeam]').text(json.info.homename + ' lagoppstilling');
    $('#basic_hometeam_name').html(json.info.homename);
    $('#hometeam_name_team').html(json.info.homename);
    var nexthomerow = json.nexthome.opponentname + " (" + json.nexthome.leaguename+") " + getDateStringMilliNoYear(json.nexthome.timestamp) + " (Om " + json.nexthome.todays + " dager)";
    var lasthomerow = json.lasthome.opponentname + " " + json.lasthome.result + " (" + json.lasthome.leaguename+") (" + json.lasthome.dayssince + " dager siden)";
    
    $('#basic_hometeam_body').empty();
    appendRow($('#basic_hometeam_body'),'Suspensjoner', getSuspensionString(json.suspension,json.info.homeid));
    appendRow($('#basic_hometeam_body'),'Neste kamp',nexthomerow);
    appendRow($('#basic_hometeam_body'),'Forrige kamp',lasthomerow);
    
    // AWAYTEAM
    $('#basic_awayteam_name').html(json.info.awayname + ' ('+json.info.awaysurface+')');
    $('#awayteam_name_team').html(json.info.awayname + ' ('+json.info.awaysurface+')');
    var lastawayrow = json.lastaway.opponentname + " " + json.lastaway.result + " (" + json.lastaway.leaguename+") (" + json.lastaway.dayssince + " dager siden)";
    var nextawayrow = json.nextaway.opponentname  +" (" + json.nextaway.leaguename+") " + getDateStringMilliNoYear(json.nextaway.timestamp) + " (Om " + json.nextaway.todays + " dager)";
    
    $('#basic_awayteam_body').empty();
    appendRow($('#basic_awayteam_body'),'Suspensjoner', getSuspensionString(json.suspension,json.info.awayid));
    appendRow($('#basic_awayteam_body'),'Neste kamp',nextawayrow);
    appendRow($('#basic_awayteam_body'),'Forrige kamp',lastawayrow);
    
    updateTeamInfo(json);
}

function updateTeamInfo(json){
    //TEAM INFO
    $("#team_detail").tabs('select', 0);
    $('[id^="hometeam"]').hide();
    $('[id^="awayteam"]').hide();
    $('[id^="homesquad"]').hide();
    $('[id^="awaysquad"]').hide();
    if(json.homelineup == undefined || json.homelineup.length == 0){
        if(json.homesquad.length != 0){
            $("#team_detail").tabs('enable', 2);
            $('#homesquad_text').show();
            $('#homesquad_source').html('Fotball.no');
            $('#homesquad_source').attr('href','https://www.fotball.no/System-pages/Kampfakta/?matchId='+json.info.matchid);
            $('#homesquad_source').attr('target','_blank');
            $('#homesquad_source').show();
            updateTeams(json.homesquad,'hometeam_squad','Tropp');
            updateTeams(json.homesquad.summary.missingplayers,'homesquad_missing','Spillere ute');
            $('[id^="hometeam_input"]').show();
            $('#team_detail').tabs('select', 2);
        }else if(json.homesquad_news.length != 0){
            $("#team_detail").tabs('enable', 2);
            $('#homesquad_source_button').show();
            $('#homesquad_source').html(json.info.home_news_header);
            $('#homesquad_text').show()
            $('#homesquad_source').attr('onclick','getNews('+json.info.home_news_id+',\'home\')');
            $('#homesquad_source').attr('href','#');
            $('#homesquad_source').show();
            $('#homesquad_source_button').attr('onclick','removeNewsSource('+json.info.matchid+',\'home\',\'squad\')');
            $('#homesquad_source_button').show();
            updateTeams(json.homesquad_news,'hometeam_squad','Tropp');
            updateTeams(json.homesquad_news.summary.missingplayers,'homesquad_missing','Spillere ute');
            $('#team_detail').tabs('select', 2);
            $('[id^="hometeam_input"]').show();
        }else if(json.homelineup_string != undefined){
            $('#hometeam_text').show();
            $('#hometeam_source').html('Fotball.no');
            $('#hometeam_source').attr('href','https://www.fotball.no/System-pages/Kampfakta/?matchId='+json.info.matchid);
            $('#hometeam_source').attr('target','_blank');
            $("#team_detail").tabs('enable', 1);
            updateTeams(json.homelineup_string,'hometeam','Lagoppstilling');
            $('#team_detail').tabs('select', 1);
        }else{
            $('#hometeam_input').show();
            $('#hometeam_input_textarea').show();
            $('#hometeam_input_textarea').val('');
            $('#hometeam_input_button').show();
            
            $('#homesquad_input').show();
            $('#homesquad_input_textarea').show();
            $('#homesquad_input_textarea').val('');
            $('#homesquad_input_button').show();
            
            $('#hometeam_squad_body_team').empty();
            $('#hometeam_missing_body_team').empty();
        }
    }else{
        $('#hometeam_text').show();
        $('#hometeam_source').show();
        if(json.homelineup.source == 'Fotball.no'){
            $('#hometeam_source').html(json.homelineup.source);
            $('#hometeam_source').attr('href','https://www.fotball.no/System-pages/Kampfakta/?matchId='+json.info.matchid);
            $('#hometeam_source').attr('target','_blank');
        }else{
            $('#hometeam_source_button').show();
            $('#hometeam_source_button').attr('onclick','removeNewsSource('+json.info.matchid+',\'home\',\'team\')');
            $('#hometeam_source').html('Egendefinert');
            $('#hometeam_source').attr('href','#');
            $('#hometeam_source').attr('onclick','getNews('+json.homelineup.source+')');
            $('#hometeam_source').show();
            $('#hometeam_text').show();
        }
        $("#team_detail").tabs('enable', 1);
        updateTeams(json.homelineup,'hometeam','Lagoppstilling');
        updateTeams(json.homelineup.summary.missingplayers,'hometeam_missing','Spillere ute (Ikke fra start)');
        $('#team_detail').tabs('select', 1);
    }
    
    if(json.awaylineup == undefined || json.awaylineup.length == 0){
        if(json.awaysquad.length != 0){
            $("#team_detail").tabs('enable',4);
            $('#awaysquad_source').html('Fotball.no');
            $('#awaysquad_source').attr('href','https://www.fotball.no/System-pages/Kampfakta/?matchId='+json.info.matchid);
            $('#awaysquad_source').attr('target','_blank');
            $('#awaysquad_source').show();
            $('#awaysquad_text').show();
            updateTeams(json.awaysquad,'awayteam_squad','Tropp');
            updateTeams(json.awaysquad.summary.missingplayers,'awaysquad_missing','Spillere ute');
            $('#team_detail').tabs('select', 4);
            $('[id^="awayteam_input"]').show();
        }else if(json.awaysquad_news.length != 0){
            $('#team_detail').tabs('enable', 4);
            $('#awaysquad_text').show();
            $('#awaysquad_source').html(json.info.away_news_header);
            $('#awaysquad_source').attr('onclick','getNews('+json.info.away_news_id+',\'away\')');
            $('#awaysquad_source').attr('href','#');
            $('#awaysquad_source').show();
            $('#awaysquad_source_button').attr('onclick','removeNewsSource('+json.info.matchid+',\'away\',\'squad\')');
            $('#awaysquad_source_button').show();
            updateTeams(json.awaysquad_news, 'awayteam_squad','Tropp');
            updateTeams(json.awaysquad_news.summary.missingplayers,'awaysquad_missing','Spillere ute');
            $('#team_detail').tabs('select', 4);
            $('[id^="awayteam_input"]').show();
        }else{
            $('#awayteam_input').show();
            $('#awayteam_input_textarea').show();
            $('#awayteam_input_textarea').val('');
            $('#awayteam_input_button').show();
            
            $('#awaysquad_input').show();
            $('#awaysquad_input_textarea').show();
            $('#awaysquad_input_textarea').val('');
            $('#awaysquad_input_button').show();
            
            $('#awayteam_squad_body_team').empty();
            $('#awayteam_missing_body_team').empty();
        }
    }else{
        $('#awayteam_source').show();
        if(json.awaylineup.source == 'Fotball.no'){
            $('#awayteam_source').html(json.awaylineup.source);
            $('#awayteam_source').attr('href','https://www.fotball.no/System-pages/Kampfakta/?matchId='+json.info.matchid);
            $('#awayteam_source').attr('target','_blank');
        }else{
            $('#awayteam_source_button').show();
            $('#awayteam_source_button').attr('onclick','removeNewsSource('+json.info.matchid+',\'away\',\'team\')');
            $('#awayteam_source').html('Egendefinert');
            $('#awayteam_source').attr('href','#');
            $('#awayteam_source').attr('onclick','getNews('+json.awaylineup.source+')');
        }
        $("#team_detail").tabs('enable', 3);
        updateTeams(json.awaylineup,'awayteam','Lagoppstilling');
        updateTeams(json.awaylineup.summary.missingplayers,'awayteam_missing','Spillere ute (Ikke fra start)');
        $('#awayteam_text').show();
        $('#team_detail').tabs('select', 3);
    }
}
function removeNewsSource(matchid,column,type){
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "removeAsSource",
            column: column,
            matchid: matchid,
            type: type
        },
        success: function(json) {
            var index = $('#team_detail a[href="#news_detail"]').parent().index();
            $('#team_detail').tabs('select', index);
            getMatchMO(selectedMatch);
        }
    });
}
function scrollToAnchor(aid){
    var aTag = $("a[id='"+ aid +"']");
    $('html,body').animate({scrollTop: aTag.offset().top},'slow');
}

function updateTeams(array,team, header){
    if(array == undefined){
        return;
    }
    var body = $('#'+team+'_body_team');
    var div = $('#'+team+'_basic');
    var text = $('#'+team+'_text');
    $('#'+team+'_header').html(header);
    $('#'+team+'_header').show();
    
    body.empty();
    body.show();
    if(array.length == 0){
        div.hide();
        text.show();
        return;
    }else{
        div.show();
        text.hide();
        for(var p in array){
            if(p == 'summary'){
                continue;
            }
            var player = array[p];
            appendPlayerRow(body,player,team);
        }
        var summary = array.summary;
        appendSummaryRow(body,summary);
    }
}

function getRefereeInfo(refereeid){
    
    var index = $('#team_detail a[href="#referee"]').parent().index();
    $("#team_detail").tabs('enable', index);
    $('#team_detail').tabs('select', index);
    
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "getReferee",
            refereeid: refereeid
        },
        success: function(json){
            $('#referee_table').empty();
            $('#referee_table').append(getTableHeader(["Kampdato","Hjemmelag","Bortelag","Resultat","Gule kort","Røde kort"]));
            var i = 0;
            var yellow = 0;
            var red = 0;
            for(var key in json){
                var v = json[key];
                $('#referee_table').append(getTableRow([v.dateofmatch,v.homename,v.awayname,v.result,v.yellow,v.red],i));
                yellow += parseInt(v.yellow);
                red += parseInt(v.red);
                i++;
            }
            $('#referee_yellow').html('Snitt gule kort: <b>' + (yellow/i).toFixed(2) + '</b>');
            $('#referee_red').html('Snitt røde kort: <b>' + (red/i).toFixed(2) + '</b>');
            $('#referee_table').append(getTableRow(["<b>Snitt</b>","","","",(yellow/i).toFixed(2),(red/i).toFixed(2)],0));
            $('#referee_table').show();
        }
    });
    
}
function updateOdds(array,type){
    if(array === undefined || array.length == 0){
        return;
    }
    if(array.match != undefined){
        $('#'+type+'_home_price').html(array.match.homeprice);
        $('#'+type+'_draw_price').html(array.match.drawprice);
        $('#'+type+'_away_price').html(array.match.awayprice);
    }
    
    $('#spreadbody_'+type).empty();
    $('#totalbody_'+type).empty();
    
    var spread = array.spread;
    for(var key in spread){
        var row = '';
        var spr = spread[key];
        var style = '';
        if(spr.mainline == 1){
            style = "style=\"border-top: 1px solid black;border-bottom: 1px solid black; background-color: darkkhaki\"";
        }
        row += '<tr>';
        row += ('<td '+ style +'>'+spr.homespread+'</td>');
        row += ('<td '+ style +'>'+spr.homeprice+'</td>');
        row += ('<td '+ style +'>'+spr.awayspread+'</td>');
        row += ('<td '+ style +'>'+spr.awayprice+'</td>');
        row += ('</tr>');
        $('#spreadbody_'+type).append(row);
    }
    
    var total = array.total;
    for(key in total){
        var tot = total[key];
        style = '';
        if(tot.mainline == 1){
            style = "style=\"border-top: 1px solid black;border-bottom: 1px solid black; background-color: border-top: 1px solid black;border-bottom: 1px solid black; background-color: darkkhaki\"";
        }
        row = '';
        row += ('<tr>');
        row += ('<td '+ style +'>'+tot.points+'</td>');
        row += ('<td '+ style +'>'+tot.underprice+'</td>');
        row += ('<td colspan="2" '+ style +'>'+tot.overprice+'</td>');
        row += ('</tr>');
        $('#totalbody_'+type).append(row);
    }
    $('#spreadbody_history_match').empty();
    var maxHome = 0;
    var maxAway = 0;
    for(var k in array.history){
        var history = array.history[k];
        row = '<tr>';
        row += '<td>'+history.changenumber+'</td>';
        row += '<td>'+history.homespread+'</td>';
        row += '<td>'+history.homeprice+'</td>';
        if(history.homeprice > maxHome){
            maxHome = history.homeprice;
        }
        row += '<td>'+history.awayspread+'</td>';
        row += '<td>'+history.awayprice+'</td>';
        if(history.awayprice > maxAway){
            maxAway = history.awayprice;
        }
        row += '<td>'+getMatchDateString(history.lastupdate)+'</td>';
        row += '</tr>';
        $('#spreadbody_history_match').append(row);
    }
    $('#spreadbody_history_match tr').each(function () {
        $(this).find("td:eq(2)").each(function() {
            if($(this).text() == maxHome){
                $(this).attr('style','background-color:#33CC33');
            }
        });
        
        $(this).find("td:eq(4)").each(function() {
            if($(this).text() == maxAway){
                $(this).attr('style','background-color:#33CC33');
            }
        });
    });
    $('#odds_'+type).show();
}
function moreNews(type,count){
    var max = (count+4);
    for(var i=0;i<20;i++){
        if(i >= count && i <= max){
            $('#match_'+type+'_row_'+i).show();
        }else{
            $('#match_'+type+'_row_'+i).hide();
        }
    }
    $('#match_'+type+'_body tr:last').remove();
    var extra = '<tr>';
    extra += '<td colspan=3>';
    if(!$('#match_'+type+'_row_0').is(':visible')){
        extra += '<a href="#" onclick="lessNews(\''+type+'\','+(count-5)+');return false">Mindre nyheter...</a>';
    }
    if(!$('#match_'+type+'_row_19').is(':visible')){
        extra += '<a href="#" onclick="moreNews(\''+type+'\','+(count+5)+');return false">Flere nyheter...</a></td>';
    }
    extra += '</tr>';
    $('#match_'+type+'_body').append(extra);
}

function lessNews(type,count){
    var max = (count+4);
    for(var i=0;i<20;i++){
        if(i >= count && i <= max){
            $('#match_'+type+'_row_'+i).show();
        }else{
            $('#match_'+type+'_row_'+i).hide();
        }
    }
    $('#match_'+type+'_body tr:last').remove();
    var extra = '<tr>';
    extra += '<td colspan=3>';
    if(!$('#match_'+type+'_row_0').is(':visible')){
        extra += '<a href="#" onclick="lessNews(\''+type+'\','+(count-5)+');return false">Mindre nyheter...</a>';
    }
    if(!$('#match_'+type+'_row_19').is(':visible')){
        extra += '<a href="#" onclick="moreNews(\''+type+'\','+(count+5)+');return false">Flere nyheter...</a></td>';
    }
    extra += '</tr>';
    $('#match_'+type+'_body').append(extra);
}

function updateNews(array,type){
    
    if(array == undefined || array.length == 0){
        return;
    }
    
    $('#match_'+type+'_body').empty();
    
    for(var i=0;i<array.length;i++){
        if(array[i] === undefined){
            continue;
        }
        
        var hidden = '';
        if(i >= 5){
            hidden = 'display: none';
        }
        var style = '';
        if(array[i].includes_squad == 1){
            style = 'background-color:yellow';
        }
        var row = '<tr id=match_'+type+'_row_'+i+' style="'+hidden+'">';
        row += '<td>'+(getNewsDateString(array[i].timestamp))+'</td>';
        row += '<td><a style="'+style+'" href="#" onclick="getNews('+array[i].id+',\''+type+'\');return false"> '+array[i].header+'</a></td>';
        row += '<td>'+array[i].source+'</td>';
        row += '</tr>';
        $('#match_'+type+'_body').append(row);
    }
    var extra = '<tr>';
    extra += '<td colspan=3><a style="'+style+'" href="#" onclick="moreNews(\''+type+'\',5);return false">Flere nyheter...</a></td>';
    extra += '</tr>';
    $('#match_'+type+'_body').append(extra);
}
function getNews(newsid,type){
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "getNews",
            newsid: newsid
        },
        success: function(json) {
            
            var index = $('#team_detail a[href="#news_detail"]').parent().index();
            $('#team_detail').tabs('select', index);
            scrollToAnchor('news_header');
            $("#news_detail").animate({ scrollTop: 0 }, "slow");

            $('#news_text').html(json.text);
            $('#source_button').hide();
            $('#source_button').attr('onclick','setNewsSource('+json.id+',\''+type+'\')');
            if(json.includes_squad == 1){
                $('#source_button').show();
            }
            $('#news_header').html(json.header);
            
            $('#news_header').attr('href',json.href);
            $('#news_header').attr('target','_blank');
            $('#news_text').show();
            $('#news_source_button').show();
            $('#news_header').show();
            $('#news_detail').show();
        }
    });
}
function setNewsSource(newsid,type){
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "setNewsSource",
            newsid: newsid,
            type: type,
            matchid: selectedMatch
        },
        success: function(){
            getMatchMO(selectedMatch);
        }
    });
}
function updatePercentage(matchid){
    var homemore = $('#input_home_more_'+matchid).val();
    var home = $('#input_home_'+matchid).val();
    var draw = $('#input_draw_'+matchid).val();
    var away = $('#input_away_'+matchid).val();
    var awaymore = $('#input_away_more_'+matchid).val();
    
    var combined = 0;
    var combinedString = ''+parseInt(home) + ' + ' + parseInt(draw) + ' + ' + parseInt(away)+'';
    if(homemore != undefined){
        combined = parseInt(homemore) + parseInt(home) + parseInt(draw) + parseInt(away);
        combinedString = parseInt(homemore)+ ' + '+parseInt(home) + ' + ' + parseInt(draw) + ' + ' + parseInt(away)+'';
    }
    else if(awaymore != undefined){
        combined = parseInt(awaymore) + parseInt(home) + parseInt(draw) + parseInt(away);
        combinedString = parseInt(home) + ' + ' + parseInt(draw) + ' + ' + parseInt(away)+' + ' + parseInt(awaymore);
    }else{
        combined = parseInt(home) + parseInt(draw) + parseInt(away);
    }
    
    if(combined != 100){
        alert('Ikke 100%: ' + combinedString + " = " + combined);
        return;
    }
    
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "updatePercentage",
            matchid: matchid,
            homemore: homemore,
            home: home,
            draw: draw,
            away: away,
            awaymore: awaymore,
            userid : $('#userid').val()
        },
        success: function(json){
            $('#match_'+matchid+'_valuehome').text(json.valuehome);
            $('#match_'+matchid+'_valueaway').text(json.valueaway);
            updateMatchesOnly();
        }
    });
}
function removePlayerFromSource(playername,type){
    
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "removePlayerFromSource",
            matchid: selectedMatch,
            playername: playername,
            type: type
        },
        success: function(){
            getMatchMO(selectedMatch);
        }
    });
}
function addPlayerToSource(playername,type){
    
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "addPlayerToSource",
            matchid: selectedMatch,
            playername: playername,
            type: type
        },
        success: function(){
            getMatchMO(selectedMatch);
        }
    });
}

function setTextAreaSource(type){
    
    var text = $('#'+type+'squad_input_textarea').val();
    
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "setTextAreaSource",
            matchid: selectedMatch,
            text: text,
            type: type
        },
        success: function(){
            getMatchMO(selectedMatch);
        }
    });
}
function setTextAreaTeam(type){
    
    var text = $('#'+type+'team_input_textarea').val();
    
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "setTextAreaTeam",
            matchid: selectedMatch,
            text: text,
            type: type
        },
        success: function(json){
            if(json.error != undefined){
                alert(json.error);
            }else{
                getMatchMO(selectedMatch);
            }
        }
    });
}