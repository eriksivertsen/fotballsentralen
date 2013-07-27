
function getTippeligaen(){return 1;}
function getAdeccoligaen(){return 2;}
function getAndreDiv(){return 8;}
function getAndreDiv1(){return 3;}
function getAndreDiv2(){return 4;}
function getAndreDiv3(){return 5;}
function getAndreDiv4(){return 6;}

function getAndreDivAll(){
    return getAndreDiv1() + ','+getAndreDiv2() + ','+getAndreDiv3() + ','+getAndreDiv4();
}

function getPlayerLink(playerid,playername)
{
    return '<a href="#" onclick="getPlayer('+playerid+');return false;">'+playername+'</a>'
    //return '<a href="index.php?season='+season+'&player_id='+playerid+'">'+playername+'</a>';
}
function getTeamLink(teamid,teamname)
{
    return '<a href="#" onclick="getTeam(0,'+teamid+');return false;">'+teamname+'</a>'
    //return '<a href="index.php?season='+season+'&team_id='+teamid+'">'+teamname+'</a>';
}
function getLeagueLink(leagueid)
{
    return '<a href="#" onclick="getTeam('+leagueid+',0);return false;">'+getLeagueName(leagueid)+'</a>'
    //return '<a href="index.php?season='+season+'&league_id='+leagueid+'">'+getLeagueName(leagueid)+'</a>';
}

function getMatchLink(matchid,hometeam,awayteam)
{
    if(matchid !== undefined && hometeam !== undefined && awayteam !== undefined){
        return getMatchLinkText(matchid,hometeam+' - ' + awayteam);
    }
    return '';
}
function getMatchResultLink(matchid,result)
{
    if(matchid !== undefined){
        return getMatchLinkText(matchid,result);
    }
    return '';
}
function getMatchHref(matchid){
    return 'http://www.fotball.no/System-pages/Kampfakta/?matchId='+matchid;
}
function getMatchLinkText(matchid,text){
    if(matchid !== undefined){
        return '<a target="blank" href='+getMatchHref(matchid)+' onclick="setExternalMatchHit('+matchid+')">'+text+'</a>';
    }
    return '';
}
function getPreviewLink(matchid,hometeam,awayteam, milliseconds)
{
    if(matchid !== undefined){
        return getPreviewLinkText(matchid,hometeam+' - ' + awayteam,getMatchDateString(milliseconds));
    }
    return '';
}
function getPreviewLinkText(matchid,text,dateofmatch)
{
    if(matchid !== undefined){
        return '<a href="#" onclick="getPreview('+matchid+');return false;">'+(dateofmatch != undefined ? dateofmatch +': ' : '')+''+text+' </a>';
    }
    return '';
}

function getEventTypeLink(eventtype)
{
    return '<a href="index.php?eventtype='+eventtype+'">'+getEventFromId(eventtype)+'</a>';
}

function getRefereeLink(referee_id,refereename)
{
    return '<a href="#" onclick="getRefereeId('+referee_id+');return false;">'+refereename+'</a>';
    //return '<a href="index.php?page=referee&referee_id='+referee_id+'">'+refereename+'</a>';
}

function getOverlib(overlibtext, value)
{
    return '<a href="javascript:void(0);" onmouseover="return overlib(\''+overlibtext+'\');" onmouseout="return nd();">'+value+'</a>';
}
function getOverlibMatchLink(overlibtext, value, link, matchid)
{
    return '<a target="blank" href=\"'+link+'\" onmouseover="return overlib(\''+overlibtext+'\', WIDTH, 350);" onmouseout="return nd();" onclick="setExternalMatchHit('+matchid+')">'+value+'</a>';
}
function getOverlibWidth(overlibtext, value, width)
{
    return '<a href="javascript:void(0);" onmouseover="return overlib(\''+overlibtext+'\', WIDTH, '+width+');" onmouseout="return nd();">'+value+'</a>';
}
function getOverlibLink(overlibtext, value, link)
{
    return '<a href=\"'+link+'\" onmouseover="return overlib(\''+overlibtext+'\');" onmouseout="return nd();">'+value+'</a>';
}

function getLineupString(toptext,lineuparray){
    var tableString = '<b>'+toptext+'</b><br/>';
    if(lineuparray[0].length == 1){
        tableString += '<br/>Keeper: ' + lineuparray[0] + ' <br/> ';
    }else{
        
    }
    tableString += 'Forsvar: ';
    tableString += lineuparray[1].join(', ') + '<br/>';
    tableString += 'Midtbane: ';
    tableString += lineuparray[2].join(', ') + '<br/>';
    tableString += 'Angrep: ';
    tableString += lineuparray[3].join(', ') + '<br/>';
    
    if(lineuparray[4].length > 0){
        tableString += '<br/>Ukjent posisjon: ';
        tableString += lineuparray[4].join(', ');
    }
    tableString += '<br/><br/><b>NB: Posisjoner baseres på spillerenes primærposisjoner<br/>og ikke på faktisk posisjon i kamp.';
    return tableString;
}
function getOverlibLineup(toptext, lineuparray, value)
{
    return getOverlibWidth(getLineupString(toptext,lineuparray),value,'450');
}
function getOverlibLineupLink(toptext,lineuparray, value, link, matchid){
    return getOverlibMatchLink(getLineupString(toptext,lineuparray),value,link,matchid);
}
function getLineupArray(array){
    
    var all = [];
    var attackers = [];
    var defenders = [];
    var midfielders = [];
    var goalkeeper= [];
    var unknowns = [];
    
    for(var player in array){
        
        var playername = array[player].playername;
        if(array[player].starts != undefined){
            playername += ' ('+array[player].starts+')';
        }
        var pos = array[player].position;
        if(pos == 'Angrep'){
            attackers.push(playername);
        }else if(pos == 'Midtbane'){
            midfielders.push(playername);
        }else if(pos == 'Forsvar'){
            defenders.push(playername);
        }else if(pos == 'Keeper'){
            goalkeeper.push(playername);
        }else{
            unknowns.push(playername);
        }
    }
    all.push(goalkeeper);
    all.push(defenders);
    all.push(midfielders);
    all.push(attackers);
    all.push(unknowns);
    return all;
}
function getTableHeader(array)
{
    for(var key in array){
        var value = array[key];
        array[key] = value.replace(" ","&nbsp;");
    }
    var arrayString = '<th>'+array.join('</th><th>')+'</th>';
    return '<thead>'+arrayString+'</thead>';
}
function getTableRow(array, i)
{
    var arrayString = '<td>'+array.join('</td><td>')+'</td>';
    return '<tr '+(i % 2 == 0 ? 'class=odd' : '')+'>'+arrayString+'</tr>';
}
function getTableRowId(array, i, id)
{
    var arrayString = '<td>'+array.join('</td><td>')+'</td>';
    return '<tr id='+id+' ' +(i % 2 == 0 ? 'class=\'odd\'' : '')+'>'+arrayString+'</tr>';
}
function getTableRowSelected(array)
{
    var arrayString = '<td>'+array.join('</td><td>')+'</td>';
    return '<tr class=selected>'+arrayString+'</tr>';
}
function getEventFromId(eventid)
{
    if(eventid == '4,8'){
        eventid = 10;
    }
    switch(eventid)
    {
        case 1:
            return 'Rødt&nbspkort&nbsp(to&nbspgule)'
        case 2:
            return 'Gule&nbspkort';
        case 3:
            return 'Rødt&nbspkort&nbsp(direkte)';
        case 4:
            return 'Spillemål';
        case 6:
            return 'Byttet&nbspinn';
        case 7:
            return 'Byttet&nbsput';
        case 8:
            return 'Straffemål';
        case 9:
            return 'Selvmål';
        case 10:
            return 'Toppscorer'
    }
}
function getLeagueName(leagueid)
{
    if(leagueid == getAndreDivAll()){
        leagueid = getAndreDiv();
    }
    leagueid = parseInt(leagueid);
    switch(leagueid)
    {
        case 0:
            return 'Norge';
        case getTippeligaen():
            return 'Tippeligaen';
        case getAdeccoligaen():
            return 'Adeccoligaen';
        case getAndreDiv():
            return '2.divisjon';
        case getAndreDiv1():
            return '2.divisjon&nbspavdeling&nbsp1';
        case getAndreDiv2():
            return '2.divisjon&nbspavdeling&nbsp2';
        case getAndreDiv3():
            return '2.divisjon&nbspavdeling&nbsp3';
        case getAndreDiv4():
            return '2.divisjon&nbspavdeling&nbsp4';
    }
}
function getDateString(date)
{
    return date;
    //var jdate = new Date(date);
    //alert(jdate);
    //alert(date);
   // var monthNames = [ "jan", "feb", "mars", "april", "mai", "juni", "juli", "aug", "sep", "okt", "nov", "des" ];
    //return jdate.getDate() + ". "+monthNames[jdate.getMonth()] + " " +jdate.getFullYear() + " " +getDoubleDigit(jdate.getHours()) + ":" + getDoubleDigit(jdate.getMinutes());
}

function getMatchDateString(milliseconds)
{
    var date = new Date();
    date.setTime(milliseconds);
    var weekday=new Array(7);
    
    weekday[0]="Søndag";
    weekday[1]="Mandag";
    weekday[2]="Tirsdag";
    weekday[3]="Onsdag";
    weekday[4]="Torsdag";
    weekday[5]="Fredag";
    weekday[6]="Lørdag";

    var n = weekday[date.getDay()];
    return n + ' ' + getDoubleDigit(date.getHours()) + ":" + getDoubleDigit(date.getMinutes());
}

function getDoubleDigit(digit){
    var digitString = ''+digit;
    if(digitString.length <= 1){
        return '0'+digitString;
    }
    return digitString;
}
function setTeamLogo(id,teamid){
    id.attr("src",'images/logos/'+teamid+'.png');
    id.attr("onclick",'getTeam(0,'+teamid+')');
    id.css("cursor",'pointer');
    id.error(function (){
        id.attr("src",'images/logos/blank.png');
    });
    id.show();
}
function setExternalMatchHit(matchid)
{
     $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {
            action: "setExternalMatchHit", 
            matchid: matchid
        },
        
        success: function() {
           
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
function getOverlibStreakString(matches,teamid)
{
    var matchString = [];
    for(var key in matches){
        if(matches[key].teamwonid == matches[key].homeid){
            var matchInfo = matches[key].dateofmatch + '<br/><b>'+ matches[key].homename + '</b> - ' +  matches[key].awayname + ' ' + matches[key].result; 
        }else if(matches[key].teamwonid == matches[key].awayid){
            matchInfo = matches[key].dateofmatch + '<br/>'+ matches[key].homename + ' - <b>' +  matches[key].awayname + '</b> ' + matches[key].result; 
        }else{
            matchInfo = matches[key].dateofmatch + '<br/>'+ matches[key].homename + ' - ' +  matches[key].awayname + ' ' + matches[key].result; 
        }

        if(matches[key].teamwonid == teamid){
            matchString.push(getOverlib(matchInfo,'V'));
        }else if(matches[key].teamwonid == 0){
            matchString.push(getOverlib(matchInfo,'U'));
        }else{
            matchString.push(getOverlib(matchInfo,'T'));
        }
    }
    return matchString;
}

function getHistoryBack(haystack, needle, selectedid)
{
    var needleLength = needle.length;
    var found = haystack.indexOf(needle);
    if(found > 0){
        var id = haystack.substr(found+needleLength);
        if(id !== selectedid){
            return id;
        }else{
            return -1;
        }
    }
    return -1;
}