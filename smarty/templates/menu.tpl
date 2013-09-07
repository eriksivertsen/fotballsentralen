<html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <head>
        <script type="text/javascript">       
            function removeText(obj) {   obj.value = ''; $('#tags').removeAttr('style'); } 
            function addText() { $('#tags').html('Søk '); $('#tags').css('font-style','italic'); $('#tags').css('color','grey'); }
            
            $(document).ready(function() {
                
                $("#breadcrumbs").breadcrumbs("home");
                $('#tags').val('Søk ');
                var season = '{$season}';
                if(season != '') {
                    setSeason(season);
                }
                getLeagues();
                $("#jMenu").jMenu({
                    ulWidth : '120',
                    animatedText : true
                });
                
                $.ajax({
                    type: "POST",
                    url: "receiver.php",
                    dataType: "json",
                    timeout: timeout,
                    data: {
                        action: "getSearchArray"
                    },
                    error: function () {
                        stopLoad()
                    },
                    success: function(json) {                       
                        
                        $('#tags').autocomplete({
                            source:json.searcharray,
                            select: function( event, ui ) { 
                                if(ui.item.type == 'player'){
                                    getPlayerSearch(ui.item.id);
                                }else if(ui.item.type == 'team'){
                                    getTeamInfoSearch(ui.item.id);
                                }
                            }
                        });
                        
                        for(var league in json.latestresults){
                            for(var match in json.latestresults[league]){
                                var m = json.latestresults[league][match];
                                $('#js-news_'+league).append('<li class="news-item">'+getDateStringMilli(m.timestamp) + ': ' + getTeamLink(m.homeid, m.homename)+' - ' + getTeamLink(m.awayid,m.awayname)+' ' + getMatchResultLink(m.matchid, m.result) +'</a></li>');
                            }
                            $('#js-news_'+league).ticker({
                                titleText: 'Siste resultater', 
                                speed: 0.20
                            });  
                        }
                           
                    }
                });
                $.ajax({
                    type: "POST",
                    url: "receiver.php",
                    dataType: "json",
                    timeout: timeout,
                    data: {
                        action: "getLatestMatches",
                        season: $('#season').val()
                    },
                    error: function () {
                        stopLoad()
                    },
                    success: function(json) {
                        var scorerarray = json.scorers;
                        for(var league in json){
                            for(var match in json[league]){
                                var m = json[league][match];
                                $('#js-news_'+league).append('<li class="news-item">'+getDateStringMilliNoYear(m.timestamp) + ': ' 
                                    + getTeamLink(m.homeid, m.homename)+' - ' + getTeamLink(m.awayid,m.awayname)+' ' 
                                    + getMatchResultLink(m.matchid, m.result) + " | " 
                                    + getScorerString(m.matchid,scorerarray) +'</a></li>');
                            }
                            $('#js-news_'+league).ticker({
                                titleText: 'Kamper', 
                                speed: 0.20,
                                pauseOnItems: 2500
                            });  
                        }
                           
                    }
                });
                
            });
        </script> 
    </head>
    <body>
        <div class="menubody">
            <center>
                <ul id="jMenu">
                    <li><a class="fNiv" href="#" onclick="getTeam(0,0);return false;">Hjem</a></li>                        
                    
                    <li><a class="fNiv" href="#" onclick="getTeam(1,0);return false;">Tippeligaen</a>
                        <ul id="tippeligaen" style="display: none;"></ul>
                    </li>

                    <li><a class="fNiv" href="#" onclick="getTeam(2,0);return false;">Adeccoligaen</a>
                        <ul id="1div" style="display: none;"></ul>
                    </li>

                    <li><a class="fNiv" href="#" onclick="getTeam(8,0);return false;">2. divisjon</a>
                        <ul>
                            <li class="arrow"></li>
                            <li><a href="#" onclick="getTeam(3,0);return false;">Avdeling 1</a>
                                <ul id="2div1" style="display: none;"></ul>
                            </li>
                            <li><a href="#" onclick="getTeam(4,0);return false;">Avdeling 2</a>
                                <ul id="2div2" style="display: none;"></ul>
                            </li>
                            <li><a href="#" onclick="getTeam(5,0);return false;">Avdeling 3</a>
                                <ul id="2div3" style="display: none;"></ul>
                            </li>
                            <li><a href="#" onclick="getTeam(6,0);return false;">Avdeling 4</a>
                                <ul id="2div4" style="display: none;"></ul>
                            </li>
                        </ul>
                    </li>
                    <li><a class="fNiv" href="#" onclick="getPopulare();return false;">Populære</a></li>
                    <li><a class="fNiv" href="#" onclick="getPreviewMatches();return false;">Forhåndsstoff</a></li>
                    <li><a class="fNiv" href="#">Annet</a>
                        <ul>
                            <li><a href="#" onclick="getReferee();return false;">Dommere</a></li>
                            <li><a href="#" onclick="getTransfers();return false;">Overganger</a></li>
                            <li><a href="#">Topplister</a>
                                <ul>
                                    <li><a href="#" onclick="getTotalPlayerMinutes();return false;">Spilleminutter</a></li>
                                    <li><a href="#" onclick="getEventsTotal(10,0);return false;">Toppscorer</a></li>
                                    <li><a href="#" onclick="getEventsTotal(8,0);return false;">Straffemål</a></li>
                                    <li><a href="#" onclick="getEventsTotal(11,0);return false;">Clean sheets</a></li>
                                    <li><a href="#" onclick="getEventsTotal(4,0);return false;">Spillemål</a></li>
                                    <li><a href="#" onclick="getEventsTotal(9,0);return false;">Selvmål</a></li>
                                    <li><a href="#" onclick="getEventsTotal(2,0);return false;">Gule&nbspkort</a></li>
                                    <li><a href="#" onclick="getEventsTotal(3,0);return false;">Rødt&nbspkort&nbsp(direkte)</a></li>
                                    <li><a href="#" onclick="getEventsTotal(1,0);return false;">Rødt&nbspkort&nbsp(to&nbspgule)</a></li>
                                    <li><a href="#" onclick="getEventsTotal(6,0);return false;">Byttet&nbspinn</a></li>
                                    <li><a href="#" onclick="getEventsTotal(7,0);return false;">Byttet&nbsput</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><a class="fNiv" href="#" onclick="getSuspensionList(134365);return false;">Suspensjonsliste</a></li>
                    
                    
                </ul>
            </center>
            <ul id="breadcrumbs">
                <li> </li>
            </ul>
            <div name="season" id="seasonselect" style="margin:5px;">
                <select id="season" onchange="selectSeason()">
                    <option value="2011">2011</option>
                    <option value="2012">2012</option>
                    <option value="2013">2013</option>
                </select>
                
                <input id="tags" type="text" style="font-style: italic;color: grey;" value="Søk " onfocus="removeText(this)"  onblur="addText()"></input>
                
            </div>      
        </div>
    </body>
</html>