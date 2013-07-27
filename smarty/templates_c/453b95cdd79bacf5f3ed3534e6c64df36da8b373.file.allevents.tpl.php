<?php /* Smarty version Smarty-3.1.12, created on 2013-06-05 10:52:42
         compiled from "smarty\templates\allevents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2463751af145612af00-72046140%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '453b95cdd79bacf5f3ed3534e6c64df36da8b373' => 
    array (
      0 => 'smarty\\templates\\allevents.tpl',
      1 => 1370429560,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2463751af145612af00-72046140',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51af1456132d27_61389948',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51af1456132d27_61389948')) {function content_51af1456132d27_61389948($_smarty_tpl) {?> <center>
    <div id="radio">
        <input id="teamradio" type="radio" value="team" name="type" onclick="getEventsTotalTeamSelected()"></input>
        <label for="teamradio"><text style="font-size:10pt">Lag</text></label>
        <input id="playerradio" type="radio" value="player" name="type" onclick="getEventsTotalSelected()"></input>
        <label for="playerradio"><text style="font-size:10pt">Spillere</text></label>
    </div>
    <div style="text-align: -moz-center">
        <table id="allEvents" class="tablesorter" style="width:auto;table-layout: fixed;"></table>
    </div>
</center><?php }} ?>