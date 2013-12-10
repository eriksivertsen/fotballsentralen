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
                
//                $.ajax({
//                    type: "POST",
//                    url: "receiver.php",
//                    dataType: "json",
//                    timeout: timeout,
//                    data: {
//                        action: "getLatestMatches",
//                        season: $('#season').val()
//                    },
//                    error: function () {
//                        stopLoad()
//                    },
//                    success: function(json) {
//                        var scorerarray = json.scorers;
//                        for(var league in json){
//                            for(var match in json[league]){
//                                var m = json[league][match];
//                                $('#js-news_'+league).append('<li class="news-item">'+getDateStringMilliNoYear(m.timestamp) + ': ' 
//                                    + getTeamLink(m.homeid, m.homename)+' - ' + getTeamLink(m.awayid,m.awayname)+' ' 
//                                    + getMatchResultLink(m.matchid, m.result) + " | " 
//                                    + getScorerString(m.matchid,scorerarray) +'</a></li>');
//                            }
//                            $('#js-news_'+league).ticker({
//                                titleText: 'Kamper', 
//                                speed: 0.20,
//                                pauseOnItems: 2500
//                            });  
//                        }
//                           
//                    }
//                });
                
            });
        </script> 
    </head>
    <body>
        <div class="menubody">
            <ul id="breadcrumbs">
                <li> </li>
            </ul>
        </div>
    </body>
</html>