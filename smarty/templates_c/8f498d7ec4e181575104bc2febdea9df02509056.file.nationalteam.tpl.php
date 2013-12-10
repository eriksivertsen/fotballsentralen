<?php /* Smarty version Smarty-3.1.12, created on 2013-11-18 17:38:42
         compiled from "smarty\templates\nationalteam.tpl" */ ?>
<?php /*%%SmartyHeaderCode:9885288a1a2439182-04762546%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '8f498d7ec4e181575104bc2febdea9df02509056' => 
    array (
      0 => 'smarty\\templates\\nationalteam.tpl',
      1 => 1384796301,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '9885288a1a2439182-04762546',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5288a1a243e4d6_53219614',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5288a1a243e4d6_53219614')) {function content_5288a1a243e4d6_53219614($_smarty_tpl) {?><img id="nationalteam_logo" style="margin-left:25px;margin-right: 15px; margin-top:15px;float: left; vertical-align: middle;">

<div id="nationalteam_tops">
    <table id="nationalteam_tops_table" style="font-size: 9pt;">
        <thead>
            <td><h4><text id="nationalteamname"></text></h4></td>
        </thead>
        <tr>
            <td>Toppscorer:</td>
            <td><text id="nationalteam_topscorer"></text></td>
        </tr>
        <tr>
            <td>Flest minutter:</td>
            <td><text id="nationalteam_minutes"></text></td>
        </tr>
        <tr>
            <td>Flest gule kort:</td>
            <td><text id="nationalteam_yellow"></text></td>
        </tr>
        <tr>
            <td>Flest røde kort:</td>
            <td><text id="nationalteam_red"></text></td>
        </tr>
        <tr>
            <td>Beste seiersprosent: </td>
            <td><text id="nationalteam_winpercentage"></text></td>
        </tr>
        <tr>
            <td>Spillere brukt:</td>
            <td><text id="nationalteam_players_used"></text></td>
        </tr>
        <tr>
            <td>Mål scoret:</td>
            <td><text id="nationalteam_scored"></text></td>
        </tr>
        <tr>
            <td>Mål sluppet inn:</td>
            <td><text id="nationalteam_conceded"></td>
        </tr>
        <tr>
            <td>Clean sheets:</td>
            <td><text id="nationalteam_cleansheets"></text></td>
        </tr>
        <tr>
            <td>Gule kort:</td>
            <td><text id="nationalteam_yellowcard"></text></td>
        </tr>
        <tr>
            <td>Røde kort:</td>
            <td><text id="nationalteam_redcard"></text></td>
        </tr>
        <tr>
            <td>Over 2.5 mål:</td>
            <td><text id="nationalteam_over3"></text></td>
        </tr>
        <tr>
            <td>Over 3.5 mål:</td>
            <td><text id="nationalteam_over4"></text></td>
        </tr>
        <tr>
            <td>Hjemmebane:</td>
            <td><text id="nationalteam_home"></text></td>
        </tr>
        <tr>
            <td>Bortebane:</td>
            <td><text id="nationalteam_away"></text></td>
        </tr>
        <tr>
            <td>Tilskuersnitt:</td>
            <td><text id="nationalteam_attendance_avg"></text></td>
        </tr>
        <tr>
            <td>Tilskuerrekord:</td>
            <td><text id="nationalteam_attendance_max"></text></td>
        </tr>
        <tr>
            <td>Spiller på:</td>
            <td><text id="nationalteam_surface"></text></td>
        </tr>
    </table>

<br/>

<table id="national_teamplayerinfo" class="tablesorter playerinfo"></table>
<?php }} ?>