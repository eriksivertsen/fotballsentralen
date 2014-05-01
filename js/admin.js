function getUsersLeague () 
{
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        data: {
            action: "getUsersLeague",
            userid: $('#userid').val()
        },
        success: function(json) {
            updateMailsender(json);
        }
    });
}

function updateMailsender(array)
{
    // Update group radio buttons
    for(var i=0;i<7;i++){
        $('#'+i+'_squad_single').attr('name',i+'_squad');
        $('#'+i+'_squad_single').attr('title','Send mail når hvert ENKELT lag har tropp klar');
        $('#'+i+'_squad_double').attr('name',i+'_squad');
        $('#'+i+'_squad_double').attr('title','Send mail når BEGGE lag har tropp klar.');
        $('#'+i+'_lineup_single').attr('name',i+'_lineup');
        $('#'+i+'_lineup_single').attr('title','Send mail når hvert ENKELT lag har lagoppstilling klar.');
        $('#'+i+'_lineup_double').attr('name',i+'_lineup');
        $('#'+i+'_lineup_double').attr('title','Send mail når BEGGE lag har lagoppstilling klar.');
    }
    
    // Loop through result array
    for(var v in array){
        var league = array[v];
        var leagueid = league['leagueid'];
        if(league.active == 1){
            $('#'+leagueid+'_active').attr('checked','true');
            updateRadio(leagueid,league['sendsquad_single'],'_squad_single');
            updateRadio(leagueid,league['sendsquad_double'],'_squad_double');
            updateRadio(leagueid,league['sendlineup_single'],'_lineup_single');
            updateRadio(leagueid,league['sendlineup_double'],'_lineup_double');
        }else{
            $('#'+leagueid+'_lineup_double').removeAttr('checked');
            $('#'+leagueid+'_lineup_single').removeAttr('checked');
            $('#'+leagueid+'_squad_double').removeAttr('checked');
            $('#'+leagueid+'_squad_single').removeAttr('checked');
        }
    }
}

function updateRadio(leagueid, dbvar, radioid)
{
    if(dbvar == 1){
        $('#'+leagueid+radioid).attr('checked','true');
    }else{
        $('#'+leagueid+radioid).removeAttr('checked');
    }
}

function saveMailSettings()
{
    var leagueArray = Array();
    for(var i=1;i<7;i++){
        var league = new Object();
        if($('#'+i+'_active').is(':checked')){
            league.leagueid = i;
            league.active_ = 1;
            league.squad_single = ($('#'+i+'_squad_single').is(':checked') ? 1 : 0);
            league.squad_double = ($('#'+i+'_squad_double').is(':checked') ? 1 : 0);
            league.lineup_single = ($('#'+i+'_lineup_single').is(':checked') ? 1 : 0);
            league.lineup_double = ($('#'+i+'_lineup_double').is(':checked') ? 1 : 0);
        }else{
            league.leagueid = i;
            league.active_ = 0;
            league.squad_single = 0;
            league.squad_double = 0;
            league.lineup_single = 0;
            league.lineup_double = 0;
        }
        leagueArray.push(league);
    }
    leagueArray.push(getLeagueObject(100));
    leagueArray.push(getLeagueObject(200));
    leagueArray.push(getLeagueObject(300));
    
    $.ajax({
        type: "POST",
        url: "receiver.php",
        dataType: "json",
        data: {
            action: "saveSettings",
            userid: $('#userid').val(),
            settings: JSON.stringify(leagueArray)
        },
        success: function() {
            alert('Innstillinger lagret');
            getUsersLeague();
        }
    });
}
function getLeagueObject(leagueid)
{
    var league = new Object();
    if($('#'+leagueid+'_active').is(':checked')){
        league.leagueid = leagueid;
        league.active_ = 1;
        league.lineup_single = ($('#'+leagueid+'_lineup_single').is(':checked') ? 1 : 0);
    }else{
        league.leagueid = leagueid;
        league.active_ = 0;
        league.squad_single = 0;
        league.squad_double = 0;
        league.lineup_single = 0;
        league.lineup_double = 0;
    }
    return league;
}

function changePassword()
{
    var newPassword = prompt("Nytt passord:");
    if(newPassword != null && newPassword != ''){
        $.ajax({
            type: "POST",
            url: "receiver.php",
            dataType: "json",
            data: {
                action: "changePassword",
                userid: $('#userid').val(),
                password: newPassword
            },
            success: function() {
                alert('Passord endret');
            }
        });
    }
}
