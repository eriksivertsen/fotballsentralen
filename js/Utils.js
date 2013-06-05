
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
    return '<a href="#" onclick="getPlayer('+playerid+')">'+playername+'</a>'
    //return '<a href="index.php?season='+season+'&player_id='+playerid+'">'+playername+'</a>';
}
function getTeamLink(teamid,teamname)
{
    return '<a href="#" onclick="getTeam(0,'+teamid+')">'+teamname+'</a>'
    //return '<a href="index.php?season='+season+'&team_id='+teamid+'">'+teamname+'</a>';
}
function getLeagueLink(leagueid)
{
    return '<a href="#" onclick="getTeam('+leagueid+',0)">'+getLeagueName(leagueid)+'</a>'
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
function getMatchLinkText(matchid,text){
    if(matchid !== undefined){
        return '<a target="blank" href="http://www.fotball.no/System-pages/Kampfakta/?matchId='+matchid+'" onclick="setExternalMatchHit('+matchid+')">'+text+'</a>';
    }
    return '';
}
function getPreviewLink(matchid,hometeam,awayteam, dateofmatch)
{
    if(matchid !== undefined){
        return getPreviewLinkText(matchid,hometeam+' - ' + awayteam,dateofmatch);
    }
    return '';
}
function getPreviewLinkText(matchid,text,dateofmatch)
{
    if(matchid !== undefined){
        return '<a href="#" onclick="getPreview('+matchid+')">'+(dateofmatch != undefined ? dateofmatch +': ' : '')+''+text+' </a>';
    }
    return '';
}

function getEventTypeLink(eventtype)
{
    return '<a href="index.php?eventtype='+eventtype+'">'+getEventFromId(eventtype)+'</a>';
}

function getRefereeLink(referee_id,refereename)
{
    return '<a href="#" onclick="getRefereeId('+referee_id+')">'+refereename+'</a>';
    //return '<a href="index.php?page=referee&referee_id='+referee_id+'">'+refereename+'</a>';
}

function getOverlib(overlibtext, value)
{
    return '<a href="javascript:void(0);" onmouseover="return overlib(\''+overlibtext+'\');" onmouseout="return nd();">'+value+'</a>';
}
function getOverlibWidth(overlibtext, value, width)
{
    return '<a href="javascript:void(0);" onmouseover="return overlib(\''+overlibtext+'\', WIDTH, '+width+');" onmouseout="return nd();">'+value+'</a>';
}
function getOverlibLink(overlibtext, value, link)
{
    
    return '<a href=\"'+link+'\" onmouseover="return overlib(\''+overlibtext+'\');" onmouseout="return nd();">'+value+'</a>';
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
    return '<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+arrayString+'</tr>';
}
function getTableRowId(array, i, id)
{
    var arrayString = '<td>'+array.join('</td><td>')+'</td>';
    return '<tr id='+id+' class='+(i % 2 == 0 ? 'odd' : '')+'>'+arrayString+'</tr>';
}
function getEventFromId(eventid)
{
    switch(eventid)
    {
        case 1:
            return 'Rødt kort (to gule)'
        case 2:
            return 'Gule kort';
        case 3:
            return 'Rødt kort (direkte)';
        case 4:
            return 'Spillemål';
        case 6:
            return 'Byttet inn';
        case 7:
            return 'Byttet ut';
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
            return 'Hele Norge';
        case getTippeligaen():
            return 'Tippeligaen';
        case getAdeccoligaen():
            return 'Adeccoligaen';
        case getAndreDiv():
            return '2.divisjon';
        case getAndreDiv1():
            return '2.divisjon avdeling 1';
        case getAndreDiv2():
            return '2.divisjon avdeling 2';
        case getAndreDiv3():
            return '2.divisjon avdeling 3';
        case getAndreDiv4():
            return '2.divisjon avdeling 4';
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

function getDoubleDigit(digit){
    if(digit.length <= 1){
        return '0'+digit;
    }
    return digit;
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