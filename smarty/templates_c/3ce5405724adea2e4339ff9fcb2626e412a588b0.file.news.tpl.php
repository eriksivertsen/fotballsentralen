<?php /* Smarty version Smarty-3.1.12, created on 2014-05-26 09:35:07
         compiled from "smarty\templates\matchobserver\news.tpl" */ ?>
<?php /*%%SmartyHeaderCode:28889535b5e2a994930-93724740%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '3ce5405724adea2e4339ff9fcb2626e412a588b0' => 
    array (
      0 => 'smarty\\templates\\matchobserver\\news.tpl',
      1 => 1401094606,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '28889535b5e2a994930-93724740',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535b5e2a999d64_50449213',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535b5e2a999d64_50449213')) {function content_535b5e2a999d64_50449213($_smarty_tpl) {?><table id="match_news" class="table">
    <thead>
        <tr>
            <td colspan="3">
                <a style="font-weight: bold;" id="home_team"></a>
                <!--
                <input type="checkbox" id="home_team_include_source" style="margin:0px;margin-left:4px"></input>
                <label for="home_team_include_source">Kun offsielle nyheter</label>
                -->
            </td>
        </tr>
        <tr>
            <td>
                Tid
            </td>
            <td>
                Overskrift
            </td>
            <td>
                Kilde
            </td>
        </tr>
    </thead>
    <tbody id="match_home_body">
    </tbody>
    <thead>
        <tr>
            <td colspan="3" style="border-top: 1px solid black">
                <a style="font-weight: bold;" id="away_team"></a>
                <!--
                <input type="checkbox" id="away_team_include_source" style="margin:0px;margin-left:4px"></input>
                <label for="away_team_include_source">Kun offsielle nyheter</label>
                -->
            </td>
        </tr>
        <tr>
            <td>
                Tid
            </td>
            <td >
                Overskrift
            </td>
            <td>
                Kilde
            </td>
        </tr>
    </thead>
    <tbody id="match_away_body">
    </tbody>
</table>
<?php }} ?>