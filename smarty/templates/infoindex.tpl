<html>
    <head>        
        <script type="text/javascript">
            
            !function(d,s,id){ var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){ js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
            
            $(document).ready(function() {

                
                var player_id = '{$player_id}';
                var team_id = '{$team_id}';
                var season = '{$season}';
                var league_id = '{$league_id}';
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
                else{
                    getTeam(0,0);
                }

                if(league_id == '' && team_id == '' && player_id == '' && page == ''){
                    $('#welcometext').show();
                }
                
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
        </script> 
        <title>FotballSentralen.com</title>
    </head>
    <body>
        
        <div id="fb-root"></div>
        
        <script>(function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/nb_NO/all.js#xfbml=1";
        fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));</script>
        
        
        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
        <div id="loader" class="loader"></div>
        {if $page != 'populare' && $page != 'suspension'}
        <input id="next" type="image" src="images/arrow_next.png" style="position:absolute;bottom:45%;right:55px" title="Neste sesong" onclick="nextSeason()">
        <input id="previous" type="image" src="images/arrow_prev.png" style="position:absolute;bottom:45%;left:55px;" title="Forrige sesong" onclick="previousSeason()">
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
                Siden er stadig under utvikling, og har du tips eller innspill tas de gjerne imot <a href="mailto:kontakt@fotballsentralen.com">her<a>.
                <br/>
                <br/>
                </div>
            
            <div id="eventoverview">
                
                <table id="league_table" style="font-size: 9pt; margin-left:16px;">
                    <tr>
                        <td>Liga: </td> 
                        <td><b><text id="league_name"></text></b></td> 
                    </tr>
                    <tr>
                        <td>Toppscorer: </td> 
                        <td><text id="league_topscorer"></text></td> 
                    </tr>
                     <!--
                    <tr>
                       <td>Formlag: </td>
                        <td><text id="league_formteam"></text></td> 
                    </tr>
                      -->
                    <tr>
                        <td>Beste hjemmelag: </td> 
                        <td><text id="league_hometeam"></text></td> 
                    </tr>
                    <tr>
                        <td>Beste bortelag: </td> 
                        <td><text id="league_awayteam"></text></td> 
                    </tr>
               </table>
                
                
                <table id="playingminutes" class="tablesorter"></table>
                <table id="goals" class="tablesorter"> </table>
                <table id="yellowcard" class="tablesorter"> </table>
                <table id="redcard" class="tablesorter"> </table>
                <table id="penalty" class="tablesorter"> </table>
                <table id="owngoal" class="tablesorter"> </table>
                <table id="subsout" class="tablesorter"> </table>
                <table id="subsin" class="tablesorter"> </table>
            </div>
            
            <div id="team">
                <img id="team_logo" style="margin-left:15px;margin-right: 15px; float: left; vertical-align: middle;">
                <div id="team_tops">
                    <table id="team_tops_table" style="font-size: 9pt;">
                        <thead>
                            <td><h4><text id="teamname"></text></h4></td>
                        </thead>
                        <tr>
                            <td>Toppscorer:</td>
                            <td><text id="team_topscorer"></text></td>
                        </tr>
                        <tr>
                            <td>Flest minutter:</td>
                            <td><text id="team_minutes"></text></td>
                        </tr>
                        <tr>
                            <td>Flest gule kort:</td>
                            <td><text id="team_yellow"></text></td>
                        </tr>
                        <tr>
                            <td>Flest røde kort:</td>
                            <td><text id="team_red"></text></td>
                        </tr>
                        <tr>
                            <td>Spillere brukt:</td>
                            <td><text id="team_players_used"></text></td>
                        </tr>
                        <tr>
                            <td>Mål scoret:</td>
                            <td><text id="team_scored"></text></td>
                        </tr>
                        <tr>
                            <td>Mål sluppet inn:</td>
                            <td><text id="team_conceded"></text></td>
                        </tr>
                        <tr>
                            <td>Clean sheets:</td>
                            <td><text id="team_cleansheets"></text></td>
                        </tr>
                        <tr>
                            <td>Over 2.5 mål:</td>
                            <td><text id="team_over3"></text></td>
                        </tr>
                        <tr>
                            <td>Over 3.5 mål:</td>
                            <td><text id="team_over4"></text></td>
                        </tr>
                        <tr>
                            <td>Hjemmebane:</td>
                            <td><text id="team_home"></text></td>
                        </tr>
                        <tr>
                            <td>Bortebane:</td>
                            <td><text id="team_away"></text></td>
                        </tr>
                    </table>
                    
                </div>
                
                <table id="team_latestmatches" class="tablesorter matchinfo"></table>
                <table id="team_nextmatches" class="tablesorter matchinfo"></table>
                <br/>
                
                <table id="teamplayerinfo" class="tablesorter playerinfo"></table>
                
                <div id="pies">
                    <text style="margin-left: 250px; font-size: 10pt; font-weight: bold">Mål for</text>
                    <text style="margin-left: 250px; font-size: 10pt; font-weight: bold">Mål mot</text>
                    <br/>
                    <br/>
                    <div id="scoringminute" style="width: 410px; height: 150px;float:left;z-index: 1; "></div>
                    
                    <div id="concededminute" style="width: 410px; height: 150px;float:left;z-index: 1; "></div>

                    <div id="infoWindow" class="infoWindow">
                        <table id="infoTable" class="infoTable"></table>
                    </div>

                    <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>

                </div>

                <ul id="rankingteam" class="ranking" style="margin-left:15px;"></ul>
                <table id="team_allmatches" class="tablesorter playerinfo"></table>
            </div>
            <div id="player">
                <img id="player_logo" style="margin-left:15px;margin-right: 15px; float: left; vertical-align: middle;">
                <table id="player_table" style="font-size: 9pt;">
                        <thead>
                            <td><h4><text id="playername"></text></h4></td>
                        </thead>
                        <tr>
                            <td>Spilletid i {$season}:</td>
                            <td><text id="player_playingminutes"></text></td>
                        </tr
                        
                        <tr>
                            <td>Seiersprosent med:</td>
                            <td><text id="player_winpercentage"></text></td>
                        </tr>
                        <tr>
                            <td>Mål:</td>
                            <td><text id="player_totalgoals"></text></td>
                        </tr>
                        <tr>
                            <td><text id="player_dateofbirth_text">Født:</text></td>
                            <td><text id="player_dateofbirth"></text></td>
                        </tr>
                        <tr>
                            <td><text id="player_height_text">Høyde:</text></td>
                            <td><text id="player_height"></text></td>
                        </tr>
                        <tr>
                            <td><text id="player_position_text">Primærposisjon:</text></td>
                            <td><text id="player_position"></text></td>
                        </tr>
                        <tr>
                            <td> </td>
                            <td><text id=""></text></td>
                        </tr>
                       
                    </table>
                    <br/>
                    <br/>
                    <br/>
                <table id="playerinfo" class="tablesorter playerinfo"></table>
                <center><text id="noData" style="font-size: 9pt">Ingen data denne sesongen!</text></center>
                <ul id="ranking" class="ranking" style="margin-left:15px;"></ul>
                <br/>
                <div id="similar">
                    <center><h5>Lignende spillere:</h5>
                    <text id="similarplayers" style="margin-left:20px;margin-right: 20px;font-size:9pt"></text><center>
                </div>
            </div>
            <div id="events">
                <center> 
                    <div id="radio">
                        <input id="teamradio" type="radio" value="team" name="type" onclick="getEventsTotalTeamSelected()"></input>
                        <label for="teamradio"><text style="font-size:10pt">Lag</text></label>
                        <input id="playerradio" type="radio" value="player" name="type" onclick="getEventsTotalSelected()"></input>
                        <label for="playerradio"><text style="font-size:10pt">Spillere</text></label>
                    </div>
                    <div style="text-align: -moz-center">
                        <table id="allEvents" class="tablesorter" style="width:auto;table-layout: fixed;"></table>
                    </div>
                </center>
            </div>
            <div id="playerminutes">
                <center> 
                    <div style="text-align: -moz-center">
                        <table id="playerminutes_table" class="tablesorter" style="width:auto;table-layout: fixed;"></table>
                    </div>
                </center>
            </div>
            <div id="populare">
                <table id="popularePlayers" class="tablesorter" style=""></table>
                <table id="populareTeams" class="tablesorter" style="float:left; "></table>
            </div>

            <div id="suspensionList">
                <text id="suspensionText" style="margin-left:20px"></text>
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
                <div class="fb-like" data-send="false" data-width="400" data-show-faces="true"></div>
                <br/>
                <a href="https://twitter.com/share" class="twitter-share-button" data-lang="en">Tweet</a>
            </div>
            <br/>
            <br/>
        </div>
    </body>
</html>