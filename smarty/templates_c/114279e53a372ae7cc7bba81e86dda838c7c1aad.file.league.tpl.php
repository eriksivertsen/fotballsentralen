<?php /* Smarty version Smarty-3.1.12, created on 2014-03-24 10:39:10
         compiled from "smarty\templates\futsal\league.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1656653299f28e8c396-54504010%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '114279e53a372ae7cc7bba81e86dda838c7c1aad' => 
    array (
      0 => 'smarty\\templates\\futsal\\league.tpl',
      1 => 1395657548,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1656653299f28e8c396-54504010',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_53299f28e8ed23_50480584',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_53299f28e8ed23_50480584')) {function content_53299f28e8ed23_50480584($_smarty_tpl) {?>
<div align="center" class="categoryheader" style="font-size:15pt;text-transform: uppercase">
    <text id="futsal_league_name"/>
</div>

<div align="center">
    <div id="futsal_league_overall" class="category">
        <div class="categoryheader" style="margin-top: 5px">Oversikt</div>
        <table id="futsal_league_table" class="tablesorter playerinfo"></table>
        <table id="futsal_league_totalgoals" class="tablesorter playerinfo"></table>
    </div>
    
    <div id="futsal_league_overall" class="category">
        <div class="categoryheader" style="margin-top: 5px">Kort</div>
        <table id="futsal_league_yellowcard" class="tablesorter playerinfo"></table>
        <table id="futsal_league_redcard" class="tablesorter playerinfo"></table>
    </div>
</div>


<?php }} ?>