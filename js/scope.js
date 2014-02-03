var scopeEvents = new Array();
var tempName = '';

if(!sessionStorage.scopeEvents) {
    clearCache();
}
else{
    scopeEvents = JSON.parse(sessionStorage.scopeEvents);
}

String.prototype.hashCode = function() {
    var hash = 0;
    if (this.length == 0) return hash;
    for (i = 0; i < this.length; i++) {
      var chara = this.charCodeAt(i);
      hash = ((hash << 5) - hash) + chara;
      hash = hash & hash; // Convert to 32bit integer
    }
    return hash;
  }
 function clearCache(){
    scopeEvents[0] = {graphtype:0,eventid:-1,type:0,limit:10};
    scopeEvents[1] = {graphtype:0,eventid:-1,type:0,limit:10};
    scopeEvents[2] = {graphtype:0,eventid:-1,type:0,limit:10};
    scopeEvents[3] = {graphtype:0,eventid:-1,type:0,limit:10};
    scopeEvents[4] = {graphtype:0,eventid:-1,type:0,limit:10};
    scopeEvents[5] = {graphtype:0,eventid:-1,type:0,limit:10};
    scopeEvents[6] = {graphtype:0,eventid:-1,type:0,limit:10};
    scopeEvents[7] = {graphtype:0,eventid:-1,type:0,limit:10};
    scopeEvents[8] = {graphtype:0,eventid:-1,type:0,limit:10};
    scopeEvents[9] = {leagueid:0,publicscope:1};
    scopeEvents[10] = {from:0};
    scopeEvents[11] = {to:-1};
    var jsonString = JSON.stringify(scopeEvents);
    sessionStorage.scopeEvents = jsonString;
  }
function clearScope(){
    clearCache();
    getScopeCurrent();
    $('#scope_name').val('Oversikt uten navn');
    $('#scope_name').removeAttr('style');
    $('#scope_public').attr('checked','true');
    $('#scope_name').removeAttr('disabled');
    window.location.hash = '/'+season+'/page/scope/';
}
function openScopeInfo(){
    $('#scope_form').show('fast');
    $('#info_scope_click').hide();
}
function closeScopeInfo(){
    $('#scope_form').hide('fast');
    $('#info_scope_click').show();
}
function closeNewGraphEdit(id){
    if(scopeEvents[id].eventid == -1){
        closeTable(id);
    }else{
        if(scopeEvents[id].graphtype == 0){
            if(scopeEvents[id].eventid == 11){
                updatePlayerMinutes(eventArray,$('#scope_event'+id),id);
            }else{
                updateEventTable(eventArray, $('#scope_event'+id),scopeEvents[id].eventid,scopeEvents[id].type, true, id, leagueidselected);
            }
        }else{
            updateLeagueTableScoped(eventArray, $('#scope_event'+id), scopeEvents[id].eventid, id, true);
        }
    }
}
function closeTable(id){
    scopeEvents[id] = {graphtype:0,eventid:-1,type:0,limit:10};
    sessionStorage.scopeEvents = JSON.stringify(scopeEvents);
    $('#scope_event'+id).hide();
    $('#graphedit_'+id).hide();
    createEmptyTable($('#scope_event'+id));
    $('#newgraph_'+id).show();
}

function addMonthToSlider(tick){
    var currentRightTick = $("#slider-range").slider("values", 1);
    var currentLeftTick = $("#slider-range").slider("values", 0);
    var newRightTick = currentRightTick + tick;
    var newLeftTick = currentLeftTick + tick;
    if(newLeftTick < 0){
        newLeftTick = 0;
    }
    $("#slider-range").slider("values", 1, newRightTick);
    $("#slider-range").slider("values", 0, newLeftTick);
    $('#time').html(getMonthYear($("#slider-range").slider("values", 0)) + " - " + getMonthYear($("#slider-range").slider("values", 1)));
}
function addYearToSlider(tick){
    var currentRightTick = $("#slider-range").slider("values", 1);
    var currentLeftTick = $("#slider-range").slider("values", 0);
    var newRightTick = currentRightTick + (tick * 9);
    var newLeftTick = currentLeftTick + (tick * 9);
    
    if(newLeftTick < 0){
        newLeftTick = 0;
    }
    $("#slider-range").slider("values", 1, newRightTick);
    $("#slider-range").slider("values", 0, newLeftTick);
    $('#time').html(getMonthYear($("#slider-range").slider("values", 0)) + " - " + getMonthYear($("#slider-range").slider("values", 1)));
}
function setZoom(tick){
    var currentRightTick = $("#slider-range").slider("values", 1);
    // Tick is difference between right and left.
    $("#slider-range").slider("values", 0, currentRightTick-tick);
    $("#slider-range").slider("values", 1, currentRightTick);
    $('#time').html(getMonthYear($("#slider-range").slider("values", 0)) + " - " + getMonthYear($("#slider-range").slider("values", 1)));
}


function setSlider(year)
{
    switch(parseInt(year))
    {
        case 2011:$("#slider-range").slider("values", 0, 0);
                   $("#slider-range").slider("values", 1, 8);
                   $('#time').html(getMonthYear(0) + " - " + getMonthYear(8));
                   break;
                    
        case 2012:$("#slider-range").slider("values", 0, 9);
                   $("#slider-range").slider("values", 1, 17);
                   $('#time').html(getMonthYear(9) + " - " + getMonthYear(17));
                   break;

        case 2013:$("#slider-range").slider("values", 0, 18);
                   $("#slider-range").slider("values", 1, 26);
                   $('#time').html(getMonthYear(18) + " - " + getMonthYear(26));
                   break;
                   
        case 0:$("#slider-range").slider("values", 0, 0);
                   $("#slider-range").slider("values", 1, 26);
                   $('#time').html(getMonthYear(0) + " - " + getMonthYear(26));
                   break;

        default:break;
    }
}
function getScopeCurrent()
{
    getScope($("#slider-range").slider("values", 0),$("#slider-range").slider("values", 1), undefined);
}
function getScopeDatabase(urlhash){
    if(!allowClicks){
        return;
    }
    startLoad();
    if(leagueidselected === undefined){
        leagueidselected = 0;
    }
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getScopeDatabase", urlhash: urlhash},
        error: function () {
            stopLoad()
        },
        success: function(data) {
            stopLoad();
            // Set scopeevents, set leagueidselected
            // getScope with data.from, data.to
            scopeEvents = data.scopeEvents;
            leagueidselected = data.leagueid;
            var jsonString = JSON.stringify(scopeEvents);
            sessionStorage.scopeEvents = jsonString;
            $('#scope_name').val(data.name);
            $('#scope_name').attr('style','color:black;');
            getScope(data.from,data.to,urlhash);
            $('#scope_name').attr('disabled','disabled');
            $('#scope_select').val(0);
            if(data.scopepublic == 1){
                $('#scope_public').attr('checked','checked');
            }else{
                $('#scope_public').removeAttr('checked');
            }
        }
    }); 
    $('#scope').show();
    $('#next').hide();
    $('#previous').hide();
}
function getScope(from, to, hash)
{
    if(!allowClicks){
        return;
    }
    if(leagueidselected === undefined){
        leagueidselected = 0;
    }
    
    startLoad();
    $('#info_scope_click').show();
    if(hash == undefined){
        window.location.hash = '/'+season+'/page/scope';
    }
    $('#feedback_page').val('Scope');
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getScope", leagueid: leagueidselected, from: from, to: to, scopeEvents: scopeEvents},
        error: function () {
            stopLoad()
        },
        success: function(data) {              
              var json = data;
              for(var i=0;i<9;i++){
                  if(scopeEvents[i].eventid != -1){
                      $('#newgraph_'+i).hide();
                        var eventArray = eval('json.scope_event'+i);
                        if(scopeEvents[i].graphtype == 0){
                            updateEventTable(eventArray, $('#scope_event'+i),scopeEvents[i].eventid,scopeEvents[i].type, hash === undefined, i, leagueidselected);
                        }else{
                            updateLeagueTableScoped(eventArray, $('#scope_event'+i), scopeEvents[i].eventid, i, hash === undefined);
                        }
                    }else{
                        if(hash == undefined){
                            createEmptyTable($('#scope_event'+i));
                            $('#scope_event'+i+'_div').show();
                        }else{
                            $('#scope_event'+i+'_div').hide();
                        }
                    }
              }
//            $('#scope_twitter').attr('data-url','http://www.fotballsentralen.com/#/page/scope/'+hash);
            $('#scope_league').val(leagueidselected);
            $("#slider-range").slider("values", 0, from);
            $("#slider-range").slider("values", 1, to);
            $('#time').html(getMonthYear($("#slider-range").slider("values", 0)) + " - " + getMonthYear($("#slider-range").slider("values", 1)));
            stopLoad();
        }
    }); 
    $('#scope').show();
    $('#next').hide();
    $('#previous').hide();
}
function getRandomScope(){
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "getRandomScope"},
        error: function () {
            stopLoad()
        },
        success: function(data) {              
            window.location.hash = '/'+season+'/page/scope/'+data;
        }
    }); 
}
function getScopeList(){
    window.location.hash = '/'+season+'/page/scope/'+$('#scope_select').val();
}

function changeName(){
    if($('#scope_name').val() != 'Oversikt uten navn'){
        tempName = $('#scope_name').val();
    }
    $('#scope_name').val('');
}

function storeName(){
    if($('#scope_name').val() == ''){
        $('#scope_name').val(tempName);
    }
}
function saveScope(){

    if(leagueidselected === undefined){
        leagueidselected = 0;
    }
    var checked = $('#scope_public').is(':checked');
    scopeEvents[9] = {leagueid:leagueidselected};
    scopeEvents[10] = {from:$("#slider-range").slider("values", 0)};
    scopeEvents[11] = {to:$("#slider-range").slider("values", 1)};
    var name = $('#scope_name').val();
    
    if(name == 'Oversikt uten navn' || name == ''){
        alert('Mangler navn. ');
        return;
    }
    var exist = false;
    for(var i=0;i<9;i++){
        if(scopeEvents[i].eventid != -1){
            exist = true;
        }
    }
    if(!exist){
        alert('Ingen data funnet! Trykk "Legg til data" og velg en datatype!');
        return;
    }
    
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        timeout: timeout,
        data: {action: "saveScope",  scopeEvents: scopeEvents,scopeHash: JSON.stringify(scopeEvents).hashCode(),name:name,scopepublic:(checked == true ? 1 : 0)},
        error: function () {
            stopLoad()
        },
        success: function(data) {
            window.location.hash = '/'+season+'/page/scope/'+data.url;
            stopLoad();
        }
    }); 
}
function updateLeagueTableScoped(leaguetable, tablename, typeid, scopeeventid,edit)
{
    var closeTable = '<a href="#" onclick="closeTable(\''+(scopeeventid >= 0 ? scopeeventid : '')+'\');return false;"><img style="margin-left:3px" src="images/x.png"></img></a>';
    
    tablename.empty();
    tablename.attr('class','tablesorter playerinfo');
    tablename.append('<caption class="tableheader">'+tableArray[typeid]+(edit ? closeTable : '')+'</caption>');
    tablename.append(getTableHeader(["#","Lag","S","V","U","T","Mål","+/-","P"]));
    tablename.append('<tbody>');
    var pos = 0;
    for(var key in leaguetable){
        pos++;
        var value = leaguetable[key];
        tablename.append(getTableRow([pos,getTeamLink(value.teamid,value.teamname.toString().substring(0,12)),value.played,value.wins,value.draws,value.loss,""+value.goals+"-"+ value.conceded+"",value.mf,value.points],pos));
    }
    tablename.append('</tbody>');
    tablename.show();
}


function selectGraphEvent(id){
    var val = $('#graphedit_eventid_'+id).val();
    if(eventArray[val].types.indexOf('1') === -1){
        $('#graphedit_type_'+id).val(0);
        $('#graphedit_type_'+id).attr('disabled','disabled');
    }else{
        $('#graphedit_type_'+id).removeAttr('disabled');
    }
    
}
function selectGraphType(id){
    var val = $('#graphedit_graphtype_'+id).val();
    var selectBox = $('#graphedit_eventid_'+id);
    var selectBox2 = $('#graphedit_type_'+id);
    selectBox.empty();
    selectBox2.empty();
    if(val == 1){
        // Tables
        selectBox.append("<option value='0'>Totalt</option>");
        selectBox.append("<option value='1'>Hjemme</option>");
        selectBox.append("<option value='2'>Borte</option>");
        
        selectBox2.append("<option value='0'>Poeng totalt</option>");
        selectBox2.append("<option value='1'>Poengsnitt</option>");
        
    }else if(val == 0){
        // Events
        var appendString = '';
        for(var eventid in eventArray){
            var name = eventArray[eventid].name;
            appendString += "<option value='"+eventid+"'>"+name+"</option>";
        }
        selectBox.append(appendString);
        
        selectBox2.append("<option value='0'>Spillere</option>");
        selectBox2.append("<option value='1'>Lag</option>");
        
    }
}
function openGraphEdit(id)
{
    var graphdiv = 
            "<table style='font-size:8pt;margin-top:55px'>"+
            "<tr>"+
                "<td>Graf:</td>"+
                "<td><label id='label_event' class='selectlabel'>"+
                    "<select id='graphedit_graphtype_"+id+"' style='margin: 4px' onchange=selectGraphType("+id+")>"+
                        "<option value='0'>Oversikt</option>"+
                        "<option value='1'>Tabell</option>"+
                    "</select>"+
                "</label></td>"+
                "<tr>"+
                    "<td>Tabell:</td>"+
                    "<td>"+
                        "<label id='label_event' class='selectlabel'>"+
                            "<select id='graphedit_eventid_"+id+"' style='margin: 4px' onchange=selectGraphEvent("+id+")>"+
                                "<option value='50'>Seiersprosent</option>"+
                                "<option value='11'>Spilleminutter</option>"+
                                "<option value='80'>Spilletid i prosent</option>"+
                                "<option value='10'>Toppscorer</option>"+
                                "<option value='60'>Måleffektivitet</option>"+
                                "<option value='8'>Straffemål</option>"+
                                "<option value='12'>Clean sheets</option>"+
                                "<option value='70'>Mål som innbytter</option>"+
                                "<option value='4'>Spillemål</option>"+
                                "<option value='9'>Selvmål</option>"+
                                "<option value='2'>Gule kort</option>"+
                                "<option value='3'>Rødt kort (direkte)</option>"+
                                "<option value='1'>Rødt kort (to gule)</option>"+
                                "<option value='6'>Byttet inn</option>"+
                                "<option value='7'>Byttet ut</option>"+
                            "</select>"+
                        "</label>"+
                    "</td>"+
                "</tr>"+
                "<tr id=grouped_by_row_"+id+">"+
                    "<td>Gruppert på:</td>"+
                    "<td>"+
                        "<label id='label_event' class='selectlabel'>"+
                            "<select id='graphedit_type_"+id+"' style='margin: 4px'>"+
                                "<option value='0'>Spillere</option>"+
                                "<option value='1'>Lag</option>"+
                            "</select>"+
                        "</label>"+
                    "</td>"+
                "</tr>"+
                "<tr>"+
                    "<td>Antall:</td>"+
                    "<td>"+
                        "<input id='graphedit_limit_"+id+"' type='text' class='search' style='width:40px' value='10'></input>"+
                    "</td>"+
                "</tr>"+
            "</table>"+
            "<button onclick='saveGraphEdit(\""+id+"\")' style='margin:5px'>Lagre</button>"+
            "<button onclick='closeNewGraphEdit(\""+id+"\")' style='margin:5px'>Avbryt</button>";
    
    $('#graphedit_'+id).html(graphdiv);
    $('#graphedit_'+id).show();
    $('#scope_event'+id).hide();
    $('#newgraph_'+id).hide();
    selectGraphType(id)
}
function saveGraphEdit(id){
    id = parseInt(id);
    var graphtypeval = $('#graphedit_graphtype_'+id).val();
    var limitval = $('#graphedit_limit_'+id).val();
    var typeval = $('#graphedit_type_'+id).val();
    var eventidval = $('#graphedit_eventid_'+id).val();
    if(limitval <= 0 || limitval === undefined){
        alert('Antall må være større enn 0.');
        return;
    }
    var obj = {graphtype:graphtypeval,eventid:eventidval,type:typeval,limit:limitval};
    
    $('#graphedit_'+id).hide();
//    $('#scope_name').val('Oversikt uten navn');
    $('#scope_name').removeAttr('style');
    var array = new Array();
    if(sessionStorage.scopeEvents){
       array = JSON.parse(sessionStorage.scopeEvents);
    }
    array[id] = obj;
    sessionStorage.scopeEvents = JSON.stringify(array);
    scopeEvents = array;
    getScopeCurrent();
}

function createEmptyTable(table)
{
    var string = 'Legg til data';
    var id = table.attr('id');
    id = id.replace(/\D/g, '');
    $('#newgraph_'+id).show();
    $('#scope_event'+id).hide();
    $('#newgraph_'+id).empty();
    $('#newgraph_'+id).append('<tr><td><a href=# onclick="openGraphEdit(\''+id+'\');return false;">'+string+'</a></td></tr>');
}


