var playerArray = "";
var surfaceArray = "";

function getSettings(){
    $.ajax({
        type: "POST",
        url: "db/DatabaseSettings.class.php",
        dataType: "json",
        data: {
            action: "getSettings",
            userid: $('#userid').val()
        },
        success: function(json) {
            playerArray = json.players;
            surfaceArray = json.surface;
            updateSettings(json);
        }
    });
}


function updateSettings(array) {
    
    var selectBox1 = '<select id="derby_teamid1">';
    var selectBox2 = '<select id="derby_teamid2">';
    
    var row ='';
    
    for(var league in surfaceArray){
        for(var k in surfaceArray[league]) {
            var team = surfaceArray[league][k];
            selectBox1 += '<option value='+team.teamid+'>'+team.teamname+'</option>';
            selectBox2 += '<option value='+team.teamid+'>'+team.teamname+'</option>';
        }
    }
    
    $('#derby_body').empty();
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
    
    $('#warning_body').empty();
    for(var k in array.settings){
        var setting = array.settings[k];
        row = '<tr><td>'+setting.desc+'</td><td><input size=3 type=text id="setting_'+setting.key+'" value='+setting.value+'></input></td></tr>';
        $('#warning_body').append(row);
    }
    savebutton = '<input type=button onclick=postSettings() value=Lagre></input>';
    $('#warning_body').append('<tr><td colspan=2>'+savebutton+'</td></tr>');
}

function selectSurfaceLeague(){
    var leagueid = $('#surface_league').val();
    $('#surface_match_body').empty();
    
    
    for(var k in surfaceArray[leagueid]) {
        var surface = surfaceArray[leagueid][k];
        
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
}

function selectPlayerLeague(){
    $('#league_teams').empty();
    $('#league_teams').append('<option value="0" selected=true>Velg lag</option>');
    var leagueid = $('#players_league').val();
    for(var k in playerArray[leagueid]){
        var team = playerArray[leagueid][k][0];
        $('#league_teams').append('<option value="'+team.teamid+'">'+team.teamname+'</option>');
    }
}

function selectTeam(){
    
    var leagueid = $('#players_league').val();
    var teamid = $('#league_teams').val();
    
    $('#players_body').empty();
    for(var k in playerArray[leagueid][teamid]){
        var player = playerArray[leagueid][teamid][k];
        var row = '<tr><td>'+player.playername+'</td><td>'+player.teamname+'</td><td><input size=3 onblur=updateKey('+player.playerid+') type=text id="key_'+player.playerid+'" value='+player.key+'></input></td></tr>';
        $('#players_body').append(row);
    }

    
}

function postSettings(){
    $('[id^="setting_"]').each(function() {
        
        var value = $(this).val();
        var key = $(this).attr('id');
        key = key.substring(key.indexOf("_")+1, key.length);
        
        $.ajax({
            type: "POST",
            url: "db/DatabaseSettings.class.php",
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
        url: "db/DatabaseSettings.class.php",
        dataType: "json",
        data: {
            action: "setKey",
            playerid: playerid,
            key: newKey,
            userid: $('#userid').val()
        },
        success: function() {
            getSettings();
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
        url: "db/DatabaseSettings.class.php",
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
        url: "db/DatabaseSettings.class.php",
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
        url: "db/DatabaseSettings.class.php",
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
        url: "db/DatabaseSettings.class.php",
        dataType: "json",
        data: {
            action: "saveDerbyLevel",
            derbyid: derbyid,
            level: level
        }
    });
}

