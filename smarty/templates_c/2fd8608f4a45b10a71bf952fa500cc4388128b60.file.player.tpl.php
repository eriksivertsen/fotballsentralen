<?php /* Smarty version Smarty-3.1.12, created on 2014-03-21 23:29:35
         compiled from "smarty\templates\player.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2302350a8208dbc1742-06689911%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2fd8608f4a45b10a71bf952fa500cc4388128b60' => 
    array (
      0 => 'smarty\\templates\\player.tpl',
      1 => 1395444495,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2302350a8208dbc1742-06689911',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a8208dc0c425_23197720',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a8208dc0c425_23197720')) {function content_50a8208dc0c425_23197720($_smarty_tpl) {?><img id="player_logo" style="margin-left:15px;margin-right: 15px; float: left; vertical-align: middle;">
<table id="player_table" style="font-size: 9pt;">
        <thead>
            <td><h4><text id="playername"></text> <text id="player_number"></text></h4></td>
        </thead>
        <tr>
            <td>Spilletid <text id="player_playingminutes_year"></text>:</td>
            <td><text id="player_playingminutes"></text></td>
        </tr>

        <tr>
            <td>Seiersprosent med:</td>
            <td><text id="player_winpercentage"></text></td>
        </tr>
        <tr>
            <td><text id="player_totalgoals_text"></td>
            <td><text id="player_totalgoals"></text></td>
        </tr>
        <tr>
            <td>Født:</td>
            <td><text id="player_dateofbirth"></text></td>
        </tr>
        <tr>
            <td>Høyde:</td>
            <td><text id="player_height"></text></td>
        </tr>
        <tr>
            <td>Primærposisjon:</td>
            <td><text id="player_position"></text></td>
        </tr>
        <tr>
            <td>Land:</td>
            <td><text id="player_country"></text></td>
        </tr>
        <tr>
            <td>Landskamper:</td>
            <td><text id="player_nationalmatches"></text></td>
        </tr>
        <tr>
            <td>Landslagsmål:</td>
            <td><text id="player_nationalgoals"></text></td>
        </tr>
        <tr>
            <td> </td>
            <td><text id=""></text></td>
        </tr>

    </table>
    <br/>
    <br/>
    <label id="player_label" class="selectlabel">
        <select id="teamSelect" style="margin: 20px" onchange="selectPlayerTeam()">
            <option value="0">Alle lag</option>
        </select>
    </label>
    <br/>
<table id="playerinfo" class="tablesorter playerinfo"></table>
<ul id="ranking" class="ranking" style="margin-left:15px;"></ul>
<br/>
<div id="similar">
    <center><h5>Lignende spillere:</h5>
    <text id="similarplayers" style="margin-left:20px;margin-right: 20px;font-size:9pt"></text><center>
</div><?php }} ?>