<?php /* Smarty version Smarty-3.1.12, created on 2014-03-22 00:17:28
         compiled from "smarty\templates\futsal\team.tpl" */ ?>
<?php /*%%SmartyHeaderCode:8145329aa7bc9ddc1-76523103%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'b69e17fcb11d7fd43a0599132746e781f5ac338e' => 
    array (
      0 => 'smarty\\templates\\futsal\\team.tpl',
      1 => 1395447446,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '8145329aa7bc9ddc1-76523103',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5329aa7bc9ea38_79974695',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5329aa7bc9ea38_79974695')) {function content_5329aa7bc9ea38_79974695($_smarty_tpl) {?><table style="width:100%">
    <tr>
        <td style="width:25%;vertical-align: top;">
            <center>
                <img id="futsal_team_logo" style="margin-top:10px">
            </center>
        </td>
        <td style="width: 40%;vertical-align: top;">
            <table id="futsal_team_tops_table" style="font-size: 12px;">
                <thead>
                    <td><h2><text id="futsal_team_teamname"></text></h2></td>
                </thead>
                <tr>
                    <td>Toppscorer:</td>
                    <td><text id="futsal_team_topscorer"></text></td>
                </tr>
                <tr>
                    <td>Flest gule kort:</td>
                    <td><text id="futsal_team_yellow"></text></td>
                </tr>
                <tr>
                    <td>Flest røde kort:</td>
                    <td><text id="futsal_team_red"></text></td>
                </tr>
                <tr>
                    <td>Mål scoret:</td>
                    <td><text id="futsal_team_scored"></text></td>
                </tr>
                <tr>
                    <td>Mål sluppet inn:</td>
                    <td><text id="futsal_team_conceded"></text></td>
                </tr>
                <tr>
                    <td>Gule kort:</td>
                    <td><text id="futsal_team_yellowcard"></text></td>
                </tr>
                <tr>
                    <td>Røde kort:</td>
                    <td><text id="futsal_team_redcard"></text></td>
                </tr>
            </table>
        </td>
        <td style="width: 35%;">
            <div id="futsal_team_table">
                <table id="futsal_team_tables">
                    <tr>
                        <td>
                            <table id="futsal_team_leaguetable" class="tablesorter playerinfo"> </table>
                        </td>
                    </tr>
                </table>
            </div>
        </td>
    </tr>
</table>

<table>
    <tr>
        <td>
            <table id="futsal_team_latestmatches" class="tablesorter matchinfo"></table>
            <table id="futsal_team_nextmatches" class="tablesorter matchinfo"></table>
        </td>
    </tr>
</table>
<text id="futsal_team_nb" style="font-size:12px;margin-left:15px">NB: Kun spillere med hendelser (mål eller kort) finnes på denne listen. Desverre ingen tropp/startoppstilling for futsal.</text>
<table id="futsal_team_teamplayerinfo" class="tablesorter playerinfo"></table>
<table id="futsal_team_allmatches" class="tablesorter playerinfo"></table><?php }} ?>