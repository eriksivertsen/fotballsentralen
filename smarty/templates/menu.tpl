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
                
                $('#tags').autocomplete({
                    source:{$searcharray},
                    select: function( event, ui ) { 
                        if(ui.item.type == 'player'){
                            getPlayerSearch(ui.item.id);
                        }else if(ui.item.type == 'team'){
                            getTeamInfoSearch(ui.item.id);
                        }
                    }
                });
                                
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