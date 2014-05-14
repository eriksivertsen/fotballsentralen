var allowClicks = true;
var selectedMatch = '';

function getInfo(){
    startLoad();
    $('#matches').hide();
    $('#odds_match').hide();
    $('#odds_firsthalf').hide();
    $('[id^="match_"]').hide();
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "getInfo",
            userid : $('#userid').val()
        },
        success: function(json) {
            $("#team_detail").tabs("option","disabled", [1,2,3,4,5]);
            $("#matchlist_div").tabs("option","disabled", [0,1,2,3,4,5]);
            updateTabs(json.matches);
            selectHighestTab(json.matches);
            $('#matchlist').show();
            autoRefreshMatches();
            stopLoad();
        }
    });
}
function autoRefreshMatches(){
    setTimeout(function(){
       updateMatchesOnly();
       autoRefreshMatches();
    }, 15000);
}
function updateMatchesOnly(){
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
    
    for(var i=0;i<=7;i++){
        
        var row = '<tr>';
        var style = 'background-color:none;text-align:top';
        
        if(array[i].homelineup == 1 && array[i].awaylineup == 1){
            style = 'background-color:#33CC33';
        }else if(array[i].homelineup == 1 || array[i].awaylineup == 1){
            style = 'background-color:#CC9933';
        }else if(array[i].homesquad == 1 || array[i].awaysquad == 1){
            style = 'background-color:#66CCFF';
        }
        row += '<td rowspan=2 style='+style+'>'+getMatchDateString(array[i].timestamp)+'</td>';
        row += '<td rowspan=2 style='+style+'><a href="#" onclick="getMatchMO('+array[i].matchid+');return false;">'+array[i].homename + ' - ' + array[i].awayname +'</a></td>';
        
        var inputhome='<input type="text" size=2 id=input_home_'+array[i].matchid+' value='+array[i].homepercentage+'></input>';
        var inputdraw='<input type="text" size=2 id=input_draw_'+array[i].matchid+' value='+array[i].drawpercentage+'></input>';
        var inputaway='<input type="text" size=2 id=input_away_'+array[i].matchid+' value='+array[i].awaypercentage+'></input>';
        var button ='<input type="button" onclick=updatePercentage('+array[i].matchid+') id=input_button_'+array[i].matchid+' value=OK></input>';
        
        row += '<td rowspan=2 style='+style+'>'+inputhome+'' + inputdraw + '' + inputaway+' ' + button + '</td>';
        row += '<td style='+style+';text-align:center><b>' + array[i].valuehome+'</b></td>';
        // Varsel
        row += '<td rowspan=2 style="text-align:center;'+style+'">';
        if(array[i].derby != undefined) {
            row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Derby\', WIDTH, 75);" onmouseout="return nd();">D </a>';
        }
        if(array[i].forecast.symbol != undefined) {
            if(array[i].forecast.alert == true){
                row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Ekstremvær\', WIDTH, 75);" onmouseout="return nd();">E </a>';
            }
        }
        if(array[i].homesurface != array[i].awaysurface){
           row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Underlag mismatch\', WIDTH, 115);" onmouseout="return nd();">U </a>';
        }
        if(array[i].tightfixure != 0){
           row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Tight fixture\', WIDTH, 115);" onmouseout="return nd();">F </a>';
        }
        if(array[i].totalsusp != 0){
            row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Totale suspensjoner\', WIDTH, 135);" onmouseout="return nd();">'+array[i].totalsusp+'S </a>';
        }
        if(array[i].preferedalert != 0){
            row += '<a href="javascript:void(0);" onmouseover="return overlib(\'Mangler førstelagsspillere\', WIDTH, 155);" onmouseout="return nd();">M </a>';
        }
        
        row += '</td>';
        // Varsel end
        
        if(array[i].homespread != undefined){
            row += '<td style='+style+'>'+getDiv(array[i].homename + ' ' + array[i].homespread, array[i].homeprice)+'</td>';
        }else{
            row += '<td style='+style+'>&nbsp;</td>';
        }
        if(array[i].totaloddsline != undefined){
            row += '<td style='+style+'>'+getDiv('Over '+array[i].totaloddsline,array[i].totalover)+'</td>';
        }else{
            row += '<td style='+style+'>&nbsp;</td>';
        }
        row += '<tr>';
        row += '<td style='+style+';text-align:center><b>' + array[i].valueaway+'</b></td>';
        if(array[i].awayspread != undefined){
            row += '<td style='+style+'>'+getDiv(array[i].awayname + ' ' + array[i].awayspread, array[i].awayprice)+'</td>';
        }else{
            row += '<td style='+style+'>&nbsp;</td>';
        }
        if(array[i].totaloddsline != undefined){
            row += '<td style='+style+'>'+getDiv('Under '+array[i].totaloddsline,array[i].totalunder)+'</td>';
        }else{
            row += '<td style='+style+'>&nbsp;</td>';
        }
        row += '</tr>';
        
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


function getMatchMO(matchid){
    startLoad();
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "getMatch",
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
            updateOdds(json.firsthalfodds,'firsthalf');
            
            
            stopLoad();
        }
    });
}

function appendRow(table, column, columnval){
    table.append('<tr><td style="border-right: 1px solid black"><b>'+column+'</b></td><td>'+columnval+'</td></tr>');
}
function appendPlayerRow(table, player){
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
    $('#hometeam_name').html(json.info.homename);
    $('#hometeam_name_team').html(json.info.homename);
    var nexthomerow = json.nexthome.opponentname + " (" + json.nexthome.leaguename+") " + getDateStringMilliNoYear(json.nexthome.timestamp) + " (Om " + json.nexthome.todays + " dager)";
    var lasthomerow = json.lasthome.opponentname + " " + json.lasthome.result + " (" + json.lasthome.leaguename+") (" + json.lasthome.dayssince + " dager siden)";
    
    $('#hometeam_body').empty();
    appendRow($('#hometeam_body'),'Suspensjoner', getSuspensionString(json.suspension,json.info.homeid));
    appendRow($('#hometeam_body'),'Neste kamp',nexthomerow);
    appendRow($('#hometeam_body'),'Forrige kamp',lasthomerow);
    
    // AWAYTEAM
    $('#awayteam_name').html(json.info.awayname + ' ('+json.info.awaysurface+')');
    $('#awayteam_name_team').html(json.info.awayname + ' ('+json.info.awaysurface+')');
    var lastawayrow = json.lastaway.opponentname + " " + json.lastaway.result + " (" + json.lastaway.leaguename+") (" + json.lastaway.dayssince + " dager siden)";
    var nextawayrow = json.nextaway.opponentname  +" (" + json.nextaway.leaguename+") " + getDateStringMilliNoYear(json.nextaway.timestamp) + " (Om " + json.nextaway.todays + " dager)";
    
    $('#awayteam_body').empty();
    appendRow($('#awayteam_body'),'Suspensjoner', getSuspensionString(json.suspension,json.info.awayid));
    appendRow($('#awayteam_body'),'Neste kamp',nextawayrow);
    appendRow($('#awayteam_body'),'Forrige kamp',lastawayrow);
    
    //TEAM INFO
    $("#team_detail").tabs('select', 0);
    if(json.homelineup == undefined || json.homelineup.length == 0){
        if(json.homesquad.length != 0){
            $("#team_detail").tabs('enable', 2);
            $('#homesquad_source').html('Fotball.no');
            $('#homesquad_source').attr('href','https://www.fotball.no/System-pages/Kampfakta/?matchId='+json.info.matchid);
            $('#homesquad_source').attr('target','_blank');
            $('#homesquad_source_button').hide();
            updateTeams(json.homesquad,'hometeam_squad','Tropp');
            updateTeams(json.homesquad.summary.missingplayers,'hometeam_missing','Spillere ute');
            $('#team_detail').tabs('select', 2);
        }else if(json.homesquad_news.length != 0){
            $("#team_detail").tabs('enable', 2);
            $('#homesquad_source_button').show();
            $('#homesquad_source').html(json.info.home_news_header);
            $('#homesquad_source').attr('onclick','getNews('+json.info.home_news_id+',\'home\')');
            $('#homesquad_source').attr('href','#');
            $('#homesquad_source_button').attr('onclick','removeNewsSource('+json.info.matchid+',\'home\')');
            updateTeams(json.homesquad_news,'hometeam_squad','Tropp');
            updateTeams(json.homesquad_news.summary.missingplayers,'hometeam_missing','Spillere ute');
            $('#team_detail').tabs('select', 2);
        }
    }else{
        $('#hometeam_source').html('Fotball.no');
        $('#hometeam_source').attr('href','https://www.fotball.no/System-pages/Kampfakta/?matchId='+json.info.matchid);
        $('#hometeam_source').attr('target','_blank');
        $("#team_detail").tabs('enable', 1);
        updateTeams(json.homelineup,'hometeam','Lagoppstilling');
        $('#team_detail').tabs('select', 1);
    }
    
    if(json.awaylineup == undefined || json.awaylineup.length == 0){
        if(json.awaysquad.length != 0){
            $("#team_detail").tabs('enable',4);
            $('#awaysquad_source').html('Fotball.no');
            $('#awaysquad_source').attr('href','https://www.fotball.no/System-pages/Kampfakta/?matchId='+json.info.matchid);
            $('#awaysquad_source').attr('target','_blank');
            $('#awaysquad_source_button').hide();
            updateTeams(json.awaysquad,'awayteam_squad','Tropp');
            updateTeams(json.awaysquad.summary.missingplayers,'awayteam_missing','Spillere ute');
            $('#team_detail').tabs('select', 4);
        }else if(json.awaysquad_news.length != 0){
            $('#team_detail').tabs('enable', 4);
            $('#awaysquad_source').html(json.info.away_news_header);
            $('#awaysquad_source').attr('onclick','getNews('+json.info.away_news_id+',\'away\')');
            $('#awaysquad_source').attr('href','#');
            $('#awaysquad_source_button').attr('onclick','removeNewsSource('+json.info.matchid+',\'away\')');
            updateTeams(json.awaysquad_news, 'awayteam_squad','Tropp');
            updateTeams(json.awaysquad_news.summary.missingplayers,'awayteam_missing','Spillere ute');
            $('#team_detail').tabs('select', 4);
        }
    }else{
        $('#awayteam_source').html('Fotball.no');
        $('#awayteam_source').attr('href','https://www.fotball.no/System-pages/Kampfakta/?matchId='+json.info.matchid);
        $('#awayteam_source').attr('target','_blank');
        $("#team_detail").tabs('enable', 3);
        updateTeams(json.awaylineup,'awayteam','Lagoppstilling');
        $('#team_detail').tabs('select', 3);
    }
    
}

function removeNewsSource(matchid,column){
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "removeAsSource",
            column: column,
            matchid: matchid
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
    
    body.empty();
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
            appendPlayerRow(body,player);
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
    $('#odds_'+type).show();
}

function updateNews(array,type){
    
    if(array == undefined || array.length == 0){
        for(var i=0;i<7;i++){
            var time1 = $('#match_'+type+'_news_time_'+(i+1));
            var source1 = $('#match_'+type+'_news_source_'+(i+1));
            var header1 = $('#match_'+type+'_news_header_'+(i+1));

            header1.empty();
            time1.empty();
            source1.empty();
        }
        return;
    }
    
    for(var i=0;i<array.length;i++){
        var time = $('#match_'+type+'_news_time_'+(i+1));
        var source = $('#match_'+type+'_news_source_'+(i+1));
        var header = $('#match_'+type+'_news_header_'+(i+1));
        
        header.empty();
        time.empty();
        source.empty();
        
        if(array[i] === undefined){
            continue;
        }
        
        
        source.html(array[i].source);
        time.html(getNewsDateString(array[i].timestamp));
        
        header.html(array[i].header);
        
        header.attr('onclick','getNews('+array[i].id+',\''+type+'\');return false');
        header.attr('href','#');
        
        if(array[i].includes_squad == 1){
            header.attr('style','background-color:yellow');
//            var div = getDiv(header.html(),'<text onclick="setAsNewsSource('+array[i].id+')">Sett som lagkilde</text>');
        }else{
            header.removeAttr('style');
        }
    }
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
    var home = $('#input_home_'+matchid).val();
    var draw = $('#input_draw_'+matchid).val();
    var away = $('#input_away_'+matchid).val();
    
    var combined = parseInt(home) + parseInt(draw) + parseInt(away);
    if(combined != 100){
        alert('Ikke 100%: ' + parseInt(home) + ' + ' + parseInt(draw) + ' + ' + parseInt(away) + " = " + combined);
        return;
    }
    
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "updatePercentage",
            matchid: matchid,
            home: home,
            draw: draw,
            away: away,
            userid : $('#userid').val()
        },
        success: function(){
            updateMatchesOnly();
        }
    });
}