
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
    return '<a href="index.php?season='+season+'&player_id='+playerid+'">'+playername+'</a>';
}
function getTeamLink(teamid,teamname)
{
    return '<a href="index.php?season='+season+'&team_id='+teamid+'">'+teamname+'</a>';
}
function getLeagueLink(leagueid)
{
    return '<a href="index.php?season='+season+'&league_id='+leagueid+'">'+getLeagueName(leagueid)+'</a>';
}

function getEventTypeLink(eventtype)
{
    return '<a href="index.php?eventtype='+eventtype+'">'+getEventFromId(eventtype)+'</a>';
}

function getOverlib(text, value)
{
    return '<a href="javascript:void(0);" onmouseover="return overlib(\''+text+'\');" onmouseout="return nd();">'+value+'</a>';
}
function getTableHeader(array)
{
    var arrayString = '<th>'+array.join('</th><th>')+'</th>';
    return '<thead>'+arrayString+'</thead>';
}
function getTableRow(array, i)
{
    var arrayString = '<td>'+array.join('</td><td>')+'</td>';
    return '<tr class='+(i % 2 == 0 ? 'odd' : '')+'>'+arrayString+'</tr>';
}
function getEventFromId(eventid)
{
    switch(eventid)
    {
        case 1:
            return 'To gule kort'
        case 2:
            return 'Gule kort';
        case 3:
            return 'Rødt kort';
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
    var monthNames = [ "jan", "feb", "mars", "april", "mai", "juni",
            "juli", "aug", "sep", "okt", "nov", "des" ];
    var dateA = new Date(date);
    return dateA.getDate() + ". "+monthNames[dateA.getMonth()] + " " +dateA.getFullYear();
}