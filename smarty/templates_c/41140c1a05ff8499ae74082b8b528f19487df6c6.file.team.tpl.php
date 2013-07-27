<?php /* Smarty version Smarty-3.1.12, created on 2013-07-16 20:05:52
         compiled from "smarty\templates\team.tpl" */ ?>
<?php /*%%SmartyHeaderCode:17686519c9f3b2cd230-75441698%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '41140c1a05ff8499ae74082b8b528f19487df6c6' => 
    array (
      0 => 'smarty\\templates\\team.tpl',
      1 => 1373123773,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '17686519c9f3b2cd230-75441698',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_519c9f3b338ab7_79561069',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_519c9f3b338ab7_79561069')) {function content_519c9f3b338ab7_79561069($_smarty_tpl) {?><img id="team_logo" style="margin-left:25px;margin-right: 15px; margin-top:15px;float: left; vertical-align: middle;">
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
            <td>Mål for/mot:</td>
            <td><text id="team_scored"></text> - <text id="team_conceded"></td>
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
        <tr>
            <td>Tilskuersnitt:</td>
            <td><text id="team_attendance_avg"></text></td>
        </tr>
        <tr>
            <td>Tilskuerrekord:</td>
            <td><text id="team_attendance_max"></text></td>
        </tr>
        <tr>
            <td>Spiller på:</td>
            <td><text id="team_surface"></text></td>
        </tr>
    </table>
</div>
    <br/>
<div id="team_table">
    <!--
    <div id="team_radio" style="width:50%; margin: 0 auto;">
        <input id="team_all" type="radio" value="team_all" name="type" onclick=""></input>
        <label for="team_all"><text style="font-size:10pt">Alle</text></label>
        <input id="team_home" type="radio" value="team_home" name="type" onclick=""></input>
        <label for="team_home"><text style="font-size:10pt">Hjemme</text></label>
        <input id="team_away" type="radio" value="team_away" name="type" onclick=""></input>
        <label for="team_away"><text style="font-size:10pt">Borte</text></label>
    </div>
    -->
    
    <table id="team_tables">
        <tr>
            <td>
                <table id="team_leaguetable" class="tablesorter playerinfo"> </table>
            </td>
            <td>
                <table id="team_latestmatches" class="tablesorter matchinfo"></table>
                <table id="team_nextmatches" class="tablesorter matchinfo"></table>
            </td>
        </tr>
    </table>
</div>

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
<table id="team_allmatches" class="tablesorter playerinfo"></table><?php }} ?>