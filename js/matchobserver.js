var allowClicks = true;

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
            action: "getInfo"
        },
        success: function(json) {
            updateTabs(json.matches);
            updateSettings(json);
            $('#matchlist').show();
            stopLoad();
        }
    });
}

function updateSettings(array) {
    
    var selectBox1 = '<select id="derby_teamid1">';
    var selectBox2 = '<select id="derby_teamid2">';
    
    for(var k in array.surface) {
        var surface = array.surface[k];
        
        selectBox1 += '<option value='+surface.teamid+'>'+surface.teamname+'</option>';
        selectBox2 += '<option value='+surface.teamid+'>'+surface.teamname+'</option>';
        
        var row = '<tr>';
        var selectBox = '<select id="surface_teamid_'+surface.teamid+'" onchange=saveSurface('+surface.teamid+')>';
        selectBox += '<option value=DÅRLIG '+(surface.surface_condition == 'DÅRLIG' ? 'selected=selected' : '')+'>DÅRLIG</option>';
        selectBox += '<option value=MEDIUM '+(surface.surface_condition == 'MEDIUM' ? 'selected=selected' : '')+'>MEDIUM</option>';
        selectBox += '<option value=BRA '+(surface.surface_condition == 'BRA' ? 'selected=selected' : '')+'>BRA</option>';
        selectBox += '<option value=UTMERKET '+(surface.surface_condition == 'UTMERKET' ? 'selected=selected' : '')+'>UTMERKET</option>';
        selectBox += '</select>';
        row += '<td>'+surface.teamname+'</td>';
        row += '<td>'+surface.surface+'</td>';
        row += '<td> '+selectBox+'</td>'; 
        row += '</tr>';
        $('#surface_match_body').append(row);
    }
    for(var k in array.derby) {
        var val = array.derby[k];
        row = createDerbyRow(val);
        $('#derby_body').append(row);
    }
    var selectBoxLevel = '<select id="derby_level_new">';
    selectBoxLevel += '<option value=LAV>LAV</option>';
    selectBoxLevel += '<option value=MEDIUM>MEDIUM</option>';
    selectBoxLevel += '<option value=HØY>HØY</option>';
    selectBoxLevel += '</select>';
    var savebutton = '<input type=button onclick=saveDerby() value=Lagre></input>';
    $('#derby_body').append('<tr id="derby_new"><td>'+selectBox1+'</td><td>'+selectBox2+'</td><td>'+selectBoxLevel+'</td><td>'+savebutton+'</td></tr>');
    
    $('#players_body').empty();
    for(var k in array.players){
        var player = array.players[k];
        row = '<tr><td>'+player.playername+'</td><td>'+player.teamname+'</td><td><input size=3 onblur=updateKey('+player.playerid+') type=text id="key_'+player.playerid+'" value='+player.key+'></input></td></tr>';
        $('#players_body').append(row);
    }
    
    $('#warning_body').empty();
    for(var k in array.settings){
        var setting = array.settings[k];
        row = '<tr><td>'+setting.desc+'</td><td><input size=3 type=text id="setting_'+setting.key+'" value='+setting.value+'></input></td></tr>';
        $('#warning_body').append(row);
    }
    savebutton = '<input type=button onclick=postSettings() value=Lagre></input>';
    $('#warning_body').append('<tr><td colspan=2>'+savebutton+'</td></tr>');
}
function postSettings(){
    $('[id^="setting_"]').each(function() {
        
        var value = $(this).val();
        var key = $(this).attr('id');
        key = key.substring(key.indexOf("_")+1, key.length);
        
        $.ajax({
            type: "POST",
            url: "db/MatchObserver.class.php",
            dataType: "json",
            data: {
                action: "updateSettings",
                key: key,
                value: value
            }
        });
    });
}
function updateKey(playerid){
    var newKey = $('#key_'+playerid).val();
    newKey = newKey.replace(',','.');
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "setKey",
            playerid: playerid,
            key: newKey
        }
    });

}

function createDerbyRow(val){
    var row = '<tr id="derby_row_'+val.id+'">';
    row += '<td>'+val.teamname1+'</td>';
    row += '<td>'+val.teamname2+'</td>';
    var deletebutton = '<input type=button onclick=deleteDerby('+val.id+') value=Slett></input>';
    var selectBox = '<select id="derby_level_'+val.id+'" onchange=saveDerbyLevel('+val.id+')>';
    selectBox += '<option value=LAV '+(val.level == 'LAG' ? 'selected=selected' : '')+'>LAV</option>';
    selectBox += '<option value=MEDIUM '+(val.level == 'MEDIUM' ? 'selected=selected' : '')+'>MEDIUM</option>';
    selectBox += '<option value=HØY '+(val.level == 'HØY'  ? 'selected=selected' : '')+'>HØY</option>';
    selectBox += '</select>';
    row += '<td>'+selectBox+'</td>';
    row += '<td>'+deletebutton+'</td>';
    row += '</tr>';
    return row;
}

function deleteDerby(derbyid){
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "deleteDerby",
            derbyid: derbyid
        },
        success: function() {
            $('#derby_row_'+derbyid).remove();
        }
    });
}
function saveDerby(){
    var team1 = $('#derby_teamid1').val();
    var team2 = $('#derby_teamid2').val();
    var team1name = $('#derby_teamid1').find(":selected").text();
    var team2name = $('#derby_teamid2').find(":selected").text();
    var level = $('#derby_level_new').val();
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "saveDerby",
            team1: team1,
            team2: team2,
            level: level
        },
        success: function(created) {
            var val = new Array();
            val.id = created; 
            val.level = level;
            val.teamname1 = team1name;
            val.teamname2 = team2name;
            
            var row = createDerbyRow(val);
            $('#derby_new').before(row);
        }
    });
}
function saveSurface(teamid){
    var condition = $('#surface_teamid_'+teamid).val();
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "setSurface",
            teamid: teamid,
            surface_condition: condition
        }
    });
}
function saveDerbyLevel(derbyid){
    var level = $('#derby_level_'+derbyid).val();
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "saveDerbyLevel",
            derbyid: derbyid,
            level: level
        }
    });
}
function updateTabs(json){
    updateMatchesMO(json.tippeligaen);
    updateMatchesMO(json.firstdiv);
//    updateMatches('2div1',json.seconddiv1);
//    updateMatches('2div2',json.seconddiv2);
//    updateMatches('2div3',json.seconddiv3);
//    updateMatches('2div4',json.seconddiv4);
}

function updateMatchesMO(array){
    
    for(var i=0;i<=7;i++){
        var row = '<tr>';
        var style = 'background-color:none';
        if(array[i].homelineup == 1 && array[i].awaylineup == 1){
            style = 'background-color:green';
        }else if(array[i].homelineup == 1 || array[i].awaylineup == 1){
            style = 'background-color:darkkhaki';
        }
        row += '<td style='+style+'>'+getMatchDateString(array[i].timestamp)+'</td>';
        row += '<td style='+style+'><a href="#" onclick="getMatchMO('+array[i].matchid+');return false;">'+array[i].homename + ' - ' + array[i].awayname +'</a></td>';
        if(array[i].odds != undefined) {
            row += '<td style="text-align:center;'+style+'">X</td>';
        }else{
            row += '<td style='+style+'>&nbsp;</td>';
        }
        
        if(array[i].derby != undefined) {
            row += '<td style="text-align:center;'+style+'">X</td>';
        }else{
            row += '<td style='+style+'>&nbsp;</td>';
        }
        if(array[i].forecast.symbol != undefined) {
            if(array[i].forecast.alert == true){
                row += '<td style="text-align:center;'+style+'">X</td>';
            }else{
                row += '<td style='+style+'>&nbsp;</td>';
            }
        }else{
            row += '<td style='+style+'>&nbsp;</td>';
        }
        
        if(array[i].homesurface != array[i].awaysurface){
            row += '<td style="text-align:center;'+style+'">X</td>';
        }else{
            row += '<td style='+style+'>&nbsp;</td>';
        }
        if(array[i].totalsusp == 0){
            row += '<td style="text-align:center;'+style+'"></td>';
        }else{
            row += '<td style="text-align:center;'+style+'">'+array[i].totalsusp+'</td>';
        }
        
        var team = 'Ingen';
        if(array[i].homelineup == 1 && array[i].awaylineup == 1){
            team = '<b>Begge</b>';
        }else if(array[i].homelineup == 1) {
            team = '<b>'+array[i].homename+'</b>';
        }else if( array[i].awaylineup == 1) {
            team = '<b>'+array[i].awayname+'</b>';
        }
        var squad = 'Ingen';
        if(array[i].homesquad == 1 && array[i].awaysquad == 1){
            squad = '<b>Begge</b>';
        }else if(array[i].homesquad == 1) {
            squad = '<b>'+array[i].homename+'</b>';
        }else if( array[i].awaysquad == 1) {
            squad = '<b>'+array[i].awayname+'</b>';
        }
        //        row += '<td style="text-align:center">'+team+'</td>';
        //        row += '<td style="text-align:center">'+squad+'</td>';
        
        $('#matchlist_body').append(row);
    }
    $('#matchlist_body').show();
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
            
            $('[id^="match_"]').show();
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
function updateMatchBasic(json){
    
    var basic = json.info.homename + " - " + json.info.awayname + ', ' + getNewsDateString(json.info.timestamp);
    var surface = json.info.surface + ' ('+json.info.surface_condition+')';
    var referee = (json.info.refereename == undefined ? 'Ikke satt opp' : '<a href="#" onclick="getRefereeInfo('+json.info.refereeid+');return false;">'+json.info.refereename+'</a>');
    var derby = (json.info.level == undefined ? 'Nei' : 'Ja ('+json.info.level+')');
    
    var forecast = 'Ikke klart';

    if(json.forecast != undefined){
        forecast = json.forecast.symbol + '. ' + json.forecast.wind_speed + ', ' + json.forecast.temperature + ' grader. ' + json.forecast.precipitation + ' mm nedbør.';
    }

    // BASIC INFO
    $('#basic_body').empty();
    appendRow($('#basic_body'),'Info',basic);
    appendRow($('#basic_body'),'Spilles på',surface);
    appendRow($('#basic_body'),'Dommer',referee);
    appendRow($('#basic_body'),'Derby',derby);
    appendRow($('#basic_body'),'Værvarsel',forecast);
    
    
    // HOMETEAM
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
    $('#hometeam_body_team').empty();
    if(json.homelineup.length == 0){
        $('#hometeam_basic').hide();
        $('#home_text').show();
    }else{
        $('#hometeam_basic').show();
        $('#home_text').hide();
        for(var p in json.homelineup){
            var player = json.homelineup[p];
            appendPlayerRow($('#hometeam_body_team'),player);
        }
    }
    $('#awayteam_body_team').empty();
    if(json.awaylineup.length == 0){
        $('#awayteam_basic').hide();
        $('#away_text').show();
    }else{
        $('#awayteam_basic').show();
        $('#away_text').hide();
        for(var p in json.awaylineup){
            player = json.awaylineup[p];
            appendPlayerRow($('#awayteam_body_team'),player);
        }
    }
    
}
function getRefereeInfo(refereeid){
    $('#news_text').html('Mer info om dommer kommer...');
    $('#news_header').html('Dommernavn');
    $('#news_text').show();
    $('#news_detail').show();
    $('#news_header').show();
}
function updateOdds(array,type){
    if(array === undefined || array.length == 0){
        return;
    }
    $('#'+type+'_home_price').html(array.match.homeprice);
    $('#'+type+'_draw_price').html(array.match.drawprice);
    $('#'+type+'_away_price').html(array.match.awayprice);
    
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
    for(var i=0;i<array.length;i++){
        if(array[i] === undefined){
            continue;
        }
        
        var time = $('#match_'+type+'_news_time_'+(i+1));
        var source = $('#match_'+type+'_news_source_'+(i+1));
        var header = $('#match_'+type+'_news_header_'+(i+1));
        
        header.empty();
        time.empty();
        source.empty();
        
        source.html(array[i].source);
        time.html(getNewsDateString(array[i].timestamp));
        
        //        var text = array[i].text;
        //        text = text.replace(/(\r\n|\n|\r)/gm,"<br/><br/>");
        //        text = text.replace(/<\/?[^>]+(>|$)/g, '');
        //        while(text.indexOf('\u2022') != -1){
        //            text = text.replace('\u2022','');
        //        }
        //        text.replace(/'/g, '');
        header.html(array[i].header);
        
        header.attr('onclick','getNews('+array[i].id+');return false');
        //        header.attr('target','_blank');
        header.attr('href','#');
        
        if(array[i].includes_squad == 1){
            header.attr('style','background-color:yellow');
        }else{
            header.removeAttr('style');
        }
    }
}
function getNews(newsid){
    $.ajax({
        type: "POST",
        url: "db/MatchObserver.class.php",
        dataType: "json",
        data: {
            action: "getNews",
            newsid: newsid
        },
        success: function(json) {
            $('#news_text').html(json.text);
            $('#news_header').html(json.header);
            $('#news_header').attr('href',json.href);
            $('#news_header').attr('target','_blank');
            $('#news_text').show();
            $('#news_detail').show();
            $('#news_header').show();
        }
    });
    
    
}