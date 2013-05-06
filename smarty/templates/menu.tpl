<html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <head>
        <script type="text/javascript">       
            function removeText(obj) {   obj.value = ''; $('#tags').removeAttr('style'); } 
            function addText() { $('#tags').html('Søk '); $('#tags').css('font-style','italic'); $('#tags').css('color','grey'); }
            
            $(document).ready(function() {
                
                $("#breadcrumbs").breadcrumbs("home");
                
                var season = '{$season}';

                if(season != '') {
                    setSeason(season);
                }
                
                getLeagues();
                $("#jMenu").jMenu({
                    
                    animatedText : true,
                    paddingLeft: 2
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
                            source:json,
                            
                            select: function( event, ui ) { 
                                window.location.href = 'index.php?season={$season}&'+ui.item.type+'_id='+ui.item.id;
                            }
                        });
                    }
                });
                
            });
        </script> 
    </head>
    <body>
        <div class="menubody">
            <center>
                <ul id="jMenu">
                    <li><a class="fNiv" href="index.php">Hjem</a></li>                        
                    
                    <li><a class="fNiv" href="index.php?season={$season}&league_id=1">Tippeligaen</a>
                        <ul id="tippeligaen" style="display: none;"></ul>
                    </li>

                    <li><a class="fNiv" href="index.php?season={$season}&league_id=2">Adeccoligaen</a>
                        <ul id="1div" style="display: none;"></ul>
                    </li>

                    <li><a class="fNiv" href="index.php?season={$season}&league_id=8">2. divisjon</a>
                        <ul>
                            <li class="arrow"></li>
                            <li><a href="index.php?season={$season}&league_id=3">Avdeling 1</a>
                                <ul id="2div1" style="display: none;"></ul>
                            </li>
                            <li><a href="index.php?season={$season}&league_id=4">Avdeling 2</a>
                                <ul id="2div2" style="display: none;"></ul>
                            </li>
                            <li><a href="index.php?season={$season}&league_id=5">Avdeling 3</a>
                                <ul id="2div3" style="display: none;"></ul>
                            </li>
                            <li><a href="index.php?season={$season}&league_id=6">Avdeling 4</a>
                                <ul id="2div4" style="display: none;"></ul>
                            </li>
                        </ul>
                    </li>

                    <li><a class="fNiv" href="index.php?page=populare">Populære</a></li>
                    <!--<li><a class="fNiv" href="index.php?page=preview">Forhåndsstoff</a></li>-->
                    <li><a class="fNiv" href="index.php?page=suspension&league_id=134365">Suspensjonsliste</a></li>
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