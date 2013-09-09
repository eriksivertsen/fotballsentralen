<html>
    <head>     
        <title>FotballSentralen.com</title>
        
        
        <script type="text/javascript">
            
            !function(d,s,id){ var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){ js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
            
            (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/nb_NO/all.js#xfbml=1";
        fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        
            $(document).ready(function() {
            
                $(window).on('hashchange', function() {
                   controlHash();
                });
            
                var player_id = '{$player_id}';
                var team_id = '{$team_id}';
                var season = '{$season}';
                var league_id = '{$league_id}';
                var matchid = '{$matchid}';
                var refereeid = '{$refereeid}';
                var page = '{$page}';
                
                
                if(season != '') {
                    setSeason(season);
                }
                if(player_id != ''){
                    getPlayer(player_id);
                }
                else if(team_id != '') {
                    getTeam(0,team_id);
                }
                else if(league_id != '' && page == '') {
                    getTeam(league_id,0);
                }
                else if(page == 'populare'){
                    getPopulare();
                }
                else if(page == 'suspension'){
                    getSuspensionList(league_id);
                }
                else if(page == 'transfers'){
                    getTransfers();
                }                
                else if(page == 'preview'){
                    if(matchid != ''){
                        getPreview(matchid);
                    }else{
                        getPreviewMatches();
                    }
                }
                else if(page == 'report' && matchid != ''){
                    getReport(matchid);
                }
                else if(page == 'referee'){
                    if(refereeid == ''){
                        getReferee();
                    }else{
                        getRefereeId(refereeid);
                    }
                }
                else if(page == 'match'){
                    getMatch(matchid);
                }
                else{
                    getTeam(0,0);
                }

                if(league_id == '' && team_id == '' && player_id == '' && page == ''){
                    $('#welcometext').show();
                }
                controlHash();
                
                //Hover arrows functions
                
                $('#previous').hover(function () {
                    this.src = 'images/arrow_prev_shadow.png';
                }, function () {
                    this.src = 'images/arrow_prev.png';
                });$('#next').hover(function () {
                    this.src = 'images/arrow_next_shadow.png';
                }, function () {
                    this.src = 'images/arrow_next.png';
                });
                
                
        });
        
        function controlHash()
        {
//            var str = window.location.href;
//            var indexOf = str.indexOf('index.php?');
//            var indexOfHash = str.indexOf('#');
//            if(indexOf != -1 && indexOfHash != -1){
//                var params = str.substring(indexOf+10,indexOfHash);
//                var array = params.split("&");
//                for(var val in array){
//                    var obj = array[val].split("=");
//                    type = obj[0].split("_")[0];
//                    id = obj[1];
//                }
//            }
            
            var paramArray =  window.location.hash.split("/");
            var type = paramArray[2];
            var id = paramArray[3];
            var specialid = paramArray[4];
                   
            if(type == 'player'){
                if(id != playeridselected || type != typeselected){
                    getPlayer(id);
                }
            }
            if(type == 'events'){
                if(id != eventselected || type != typeselected){
                    getEventsTotal(id,leagueidselected);
                }
            }
            if(type == 'eventsteam'){
                if(id != eventselected || type != typeselected){
                    getEventsTotalTeam(id,leagueidselected);
                }
            }
            if(type == 'team'){
                if(id != teamidselected || type != typeselected ){
                    getTeamInfo(id,0);
                }
            }
            if(type == 'league'){
                if(id != leagueidselected || type != typeselected){
                    getTeam(id,0);
                }
            }
            if(type == 'page'){
                if(id == 'populare'){
                    getPopulare();
                }
                else if(id == 'suspension'){
                    getSuspensionList(specialid);
                }
                else if(id == 'transfers'){
                    getTransfers();
                }                
                else if(id == 'preview'){
                    if(specialid != undefined){
                        getPreview(specialid);
                    }else{
                        getPreviewMatches();
                    }
                }
                else if(id == 'referee'){
                    if(specialid == undefined){
                        getReferee();
                    }else{
                        getRefereeId(specialid);
                    }
                }
                else if(id == 'match'){
                    getMatch(specialid);
                }
            }
        }
        
        </script> 
        
    </head>
    
    <body>
        <div id="fb-root"></div>
        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
        <div id="loader" class="loader"></div>
        {if $page == ''}
        <input id="next" type="image" src="images/arrow_next.png" style="position:absolute;bottom:35%;right:55px" title="Neste sesong" onclick="nextSeason()">
        <input id="previous" type="image" src="images/arrow_prev.png" style="position:absolute;bottom:35%;left:55px;" title="Forrige sesong" onclick="previousSeason()">
        {/if}
        <div class="indexbody">
            <div id="welcometext" style="font-size: 10pt; margin-left:16px;margin-right:20px; background-color: #8dbdd8 ">
                <b>Velkommen til FotballSentralen.com!</b>
                <br/>
                <br/>
                Denne nystartede siden samler offisielle kampfakta og resultater fra fotball.no, og sorterer dette slik at du 
                enkelt kan bla deg gjennom fotball-Norge! Kort sagt er dette en oversikt over spilleminutter, bytter, mål og kort for 
                ALLE spillere fra 2.divisjon og opp. Per dags dato ligger det statistikker fra 2011 (Tippeligaen og Adeccoligaen) 
                samt alle 2.divisjons-avdelingene fra 2012-sesongen.
                <br/>
                <br/>
                Siden er stadig under utvikling, og har du tips eller innspill tas de gjerne imot <a href="mailto:kontakt@fotballsentralen.com">her</a>.
                <br/>
                <br/>
                </div>
            
            <div id="eventoverview">
                {include file="events.tpl"}
            </div>
            <div id="team">
                {include file="team.tpl"}
            </div>
            <div id="player">
                {include file="player.tpl"}
            </div>
            <div id="events">
               {include file="allevents.tpl"}
            </div>
            <table id="populare" align="center" width="100%">
                <tr>
                    <td><table id="trending" class="tablesorter playerinfo" style="float:left; "></table></td>
                    <td><table id="popularePlayers" class="tablesorter playerinfo" style="float:left;"></table></td>
                    <td><table id="populareTeams" class="tablesorter playerinfo" style="float:left; "></table></td>
                </tr>
                <br/>
                <br/>
            </table>
            <div id="transfer_div">
                <text id="transfer_text" style="margin-left:20px"></text>
                <table id="transfer_table" align="center" width="100%">
                    <tr>
                        <td><table id="transfer_transfer" class="tablesorter playerinfo" style="float:left; "></table></td>
                    </tr>
                </table>
            </div>
            <div id="preview">
                {include file="preview.tpl"}
            </div>
            
            <div id="match_main">
                {include file="match.tpl"}
            </div>
            
            <div id="referee">
                <table id="referee_table" class="tablesorter playerinfo"></table>
                <table id="referee_table_specific" class="tablesorter playerinfo"></table>
            </div>

            <div id="suspensionList">
                <br/>
                <select id="suspensionSelect" style="margin: 20px" onchange="selectSuspendedLeague()">
                    <option value="134365">Tippeligaen</option>
                    <option value="134367">Adeccoligaen</option>
                    <option value="134371">2.divisjon avd 1</option>
                    <option value="134372">2.divisjon avd 2</option>
                    <option value="134373">2.divisjon avd 3</option>
                    <option value="134374">2.divisjon avd 4</option>
                </select>
                <br/>
                <table id="suspensionTable" class="tablesorter" style="float: none; width:auto;"></table>
                <br/>
                <br/>
                <table id="suspensionTableDanger" class="tablesorter" style="float: none; width:auto;"></table>
                
            </div>
            <text id="lastupdate" style="font-size: 7pt;margin-left: 15px;" >Sist oppdatert: </text>
            <br/>
            <br/>
            <div style="margin-left:15px" id="social">
                <div class="fb-like" data-href="http://www.facebook.com/fotballsentral1" data-send="false" data-width="400" data-show-faces="true"></div>
                <br/>
                <a href="https://twitter.com/share" class="twitter-share-button" data-lang="en">Tweet</a>
            </div>
            <br/>
            <br/>
        </div>
    </body>
</html>