<?php /* Smarty version Smarty-3.1.12, created on 2014-03-24 11:12:03
         compiled from "smarty\templates\futsal\player.tpl" */ ?>
<?php /*%%SmartyHeaderCode:86175329aa7bc93c69-12505084%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'edf0c1271a09ebeb3a1ed778ab143ece8ab214b1' => 
    array (
      0 => 'smarty\\templates\\futsal\\player.tpl',
      1 => 1395659522,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '86175329aa7bc93c69-12505084',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5329aa7bc96406_30429818',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5329aa7bc96406_30429818')) {function content_5329aa7bc96406_30429818($_smarty_tpl) {?><img id="futsal_player_logo" style="margin-left:15px;margin-right: 15px; float: left; vertical-align: middle;"/>
<table style="width:100%">
    <tr>
        <td style="width: 40%;vertical-align: top;">
            <h3><text id="futsal_player_name"></text></h3>
            <table id="futsal_player_table" style="font-size: 12px;">
                               
                <tr>
                    <td>Mål:</td>
                    <td><text id="futsal_player_totalgoals"></text></td>
                </tr>
                <tr>
                    <td>Gule kort:</td>
                    <td><text id="futsal_player_yellowcard"></text></td>
                </tr>
                <tr>
                    <td>Røde kort:</td>
                    <td><text id="futsal_player_redcard"></text></td>
                </tr>
                <tr>
                    <td>Ekstra:</td>
                    <td><text id="futsal_player_extra_1"></text></td>
                </tr>
                <tr>
                    <td></td>
                    <td><text id="futsal_player_extra_2"></text></td>
                </tr>
                <tr>
                    <td></td>
                    <td><text id="futsal_player_extra_3"></text></td>
                </tr>
            </table>
        </td>
        <td style="width:35%">
        </td>
    </tr>
</table>
<br/>
<br/>
<br/>
<br/>
<label id="futsal_player_label" class="selectlabel">
    <select id="futsal_player_teamselect" style="margin: 20px" onchange="selectFutsalPlayerTeam()"></select>
</label>
<br/>
<text id="futsal_player_nb" style="font-size:12px;margin-left:15px">NB: Kun kamper med hendelser (mål eller kort) finnes på denne listen. Desverre ingen tropp/startoppstilling for futsal.</text>
<table id="futsal_player_info" class="tablesorter playerinfo"></table><?php }} ?>