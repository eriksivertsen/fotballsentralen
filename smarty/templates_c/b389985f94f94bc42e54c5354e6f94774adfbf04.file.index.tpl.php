<?php /* Smarty version Smarty-3.1.12, created on 2012-11-21 18:17:57
         compiled from "smarty\templates\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3077650a5159c418032-61418315%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b389985f94f94bc42e54c5354e6f94774adfbf04' => 
    array (
      0 => 'smarty\\templates\\index.tpl',
      1 => 1353521875,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3077650a5159c418032-61418315',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a5159c84d841_99327821',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a5159c84d841_99327821')) {function content_50a5159c84d841_99327821($_smarty_tpl) {?><html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <head>
        <link rel="stylesheet" href="css/style.css" type="text/css"/>
        
        <script type="text/javascript" src="js/jquery-1.8.2.js"></script>  
        <script type="text/javascript" src="js/jquery.tablesorter.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/jMenu.jquery.js"></script>
        <script type="text/javascript">       
            
        $(document).ready(function() {
            
            fillTeams();
            $("#jMenu").jMenu();
                               
                
            $('[name="league"]').change(function() {
                getTeam($('[name="league"]').val(),0); //All teams
            });
            
            $('[name="team"]').change(function() {
                getTeam($('[name="name"]').val());
            });

        });
        function getLeague(leagueid)
        {
                $.ajax({
                    type: "POST",
                    url: "receiver.php",
                    dataType: "json",
                    data: { action: "getTeams", leagueid: leagueid },
                    success: function(json) {
                        $('[name="team"]').empty();
                        var array = json;
                        for (var i=0; i<array.length; i++) {
                            $('#'+leagueid).append('<li><a onclick="getTeam(0,'+array[i].teamid+')">'+array[i].teamname+'</a></li>');
                        }
                    }
                }); 
               
        }
        function fillTeams()
        {
            getLeague(129933);
            getLeague(129939);
            getLeague(130244);
            getLeague(130245);
            getLeague(130246);
            getLeague(130247);
        }
        function getTeam(leagueid,teamid)
        {
            setBreadcrumb(leagueid,teamid);
            getEventsFromDB(leagueid,teamid,2 ,$('#yellowcard'), 'Gule kort');
            getEventsFromDB(leagueid,teamid,3, $('#redcard'), 'Røde kort');
            getEventsFromDB(leagueid,teamid,4, $('#goals'), 'Spillemål');
            getEventsFromDB(leagueid,teamid,8, $('#penalty'), 'Straffemål');
            getEventsFromDB(leagueid,teamid,9, $('#owngoal'), 'Selvmål');
            getEventsFromDB(leagueid,teamid,7, $('#subsout'), 'Byttet ut');
            getEventsFromDB(leagueid,teamid,6, $('#subsin'), 'Byttet inn');
            getPlayerMinutes(leagueid,teamid);    
        }
        
        function getEventsFromDB(leagueid, teamid, eventtype, table, tableheader)
        {
        $.ajax({
                type: "POST",
                url: "receiver.php",
                dataType: "json",
                data: { action: "getEvents", teamid: teamid, eventtype : eventtype, leagueid: leagueid },
                success: function(json) {
                    table.empty();
                    var array = json;
                    table.append('<caption class="tableheader">'+tableheader+'</caption>');
                    table.append('<thead><tr><td><b>Spillernavn</td><td><b>Lag<b></td><td><b>Antall<b></td></tr></thead>');
                    for (var i=0; i<array.length; i++) {
                        table.append('<tr><td><a href="player.php?playerid='+array[i].playerid+'">' +array[i].playername+ '</a></td>'+
                            '<td><a href="#" onclick="getTeam(0,'+array[i].teamid+')">' +array[i].teamname+ '</a></td>'+
                            '<td>'+array[i].eventcount+'</td></tr>');
                    }
                }
            });
        }
        function getPlayerMinutes(leagueid,teamid)
        {
            
            $.ajax({
                    type: "POST",
                    url: "receiver.php",
                    dataType: "json",
                    data: { action: "getPlayingMinutes", teamid: teamid, leagueid : leagueid },
                    success: function(json) {
                        $('[name="playingminutes"]').empty();
                        var array = json;
                        $('[name="playingminutes"]').append('<caption class="tableheader">Spilleminutter</caption>');
                        $('[name="playingminutes"]').append('<thead><tr><td><b>Spillernavn</td><td><b>Antall minutter<b></td></tr></thead>');
                        $('[name="playingminutes"]').append('<tbody>');
                        for (var i=0; i<array.length; i++) {
                            $('[name="playingminutes"]').append('<tr><td><a href="player.php?playerid='+array[i].playerid+'">' +array[i].playername+ '</a></td>'+
                                '<td><a href="#" onclick="getTeam(0,'+array[i].teamid+')">' +array[i].teamname+ '</a></td>'+
                                '<td>'+array[i].minutesplayed+'</td></tr>');
                        }
                        $('[name="playingminutes"]').append('</tbody>');
                    }
                });
         }
        
        </script> 
        <title>Norge - Fotballstatistikker</title>
    </head>
    <body>
        <div class="body">
        <center>
            <ul id="jMenu">
                 <li><a class="fNiv" onclick="getTeam(0,0)">Alle lag</a></li>
                 <li><a class="fNiv">Topplister</a></li>
                <li><a class="fNiv" onclick="getTeam(129933,0)">Tippeligaen</a>
                    <ul id="129933" style="display: none;"></ul>
                </li>

            <li><a class="fNiv" onclick="getTeam(129939,0)">Adeccoligaen</a>
                <ul id="129939" style="display: none;"></ul>
            </li>

            <li><a class="fNiv" onclick="getTeam(22012,0)">2. divisjon</a>
                    <ul>
                            <li class="arrow"></li>
                            <li><a onclick="getTeam(130244,0)">Avdeling 1</a>
                            <ul id="130244" style="display: none;"></ul>
                            </li>
                            <li><a onclick="getTeam(130245,0)">Avdeling 2</a>
                            <ul id="130245" style="display: none;"></ul>
                            </li>
                            <li><a onclick="getTeam(130246,0)">Avdeling 3</a>
                                <ul id="130246" style="display: none;"></ul>
                            </li>
                            <li><a onclick="getTeam(130247,0)">Avdeling 4</a>
                            <ul id="130247" style="display: none;"></ul>
                            </li>
                    </ul>
            </li>


            <li><a class="fNiv" onclick="getLeague(32012)">3. divisjon</a>
                    <ul>
                            <li class="arrow"></li>
                            <li><a>Category 5.2</a>
                                    <ul>
                                            <li><a>Category 5.3</a></li>
                                            <li><a>Category 5.3</a></li>
                                            <li><a>Category 5.3</a></li>
                                            <li><a>Category 5.3</a></li>
                                    </ul>
                            </li>
                            <li><a>Category 5.2</a></li>
                            <li><a>Category 5.2</a></li>
                            <li><a>Category 5.2</a></li>
                    </ul>
                </li>
            </ul>
        </center>
            <table style="display: inline;" id="yellowcard" class="tablesorter"> </table>
            <table style="display: inline;" id="redcard" class="tablesorter"> </table>
            <table style="display: inline;" id="goals" class="tablesorter"> </table>
            <table style="display: inline;" id="penalty" class="tablesorter"> </table>
            <table style="display: inline;" id="owngoal" class="tablesorter"> </table>
            <table style="display: inline;" id="subsout" class="tablesorter"> </table>
            <table style="display: inline;" id="subsin" class="tablesorter"> </table>
            <table style="display: inline;"name="playingminutes" class="tablesorter"></table>
                </div>
    </body>

</html><?php }} ?>