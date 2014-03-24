<?php /* Smarty version Smarty-3.1.12, created on 2014-03-19 11:45:55
         compiled from "smarty\templates\events.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6705519c9f3b113720-42590549%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '97909604d98e0a50eafe6000722a870744d61ea0' => 
    array (
      0 => 'smarty\\templates\\events.tpl',
      1 => 1395229553,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '6705519c9f3b113720-42590549',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_519c9f3b169808_90813437',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_519c9f3b169808_90813437')) {function content_519c9f3b169808_90813437($_smarty_tpl) {?>
<div align="center">   
    <div class="categoryheader" style="font-size:15pt;text-transform: uppercase"><text id="league_name"></text></div>
    <table id="league_table" style="font-size: 11pt; width:100%">
        <tr>
            <td align="center" style="min-width: 300px">Toppscorer: </td> 
            <td align="center" style="min-width: 300px">Beste hjemmelag: </td> 
            <td align="center" style="min-width: 300px">Beste bortelag: </td> 
        </tr>
        <tr>
            <td align="center"><text id="league_topscorer"></text></td> 
            <td align="center"><text id="league_hometeam"></text></td> 
            <td align="center"><text id="league_awayteam"></text></td> 
        </tr>
        <tr>
            <td align="center"><img id="league_topscorer_logo"></img></td> 
            <td align="center"><img id="league_hometeam_logo"></img></td> 
            <td align="center"><img id="league_awayteam_logo"></img></td> 
        </tr>
        
        <br/>
        
    </table>
</div>

<div align="center">
    <div id="overall" class="category">
        <div class="categoryheader" style="margin-top: 5px">Tabeller</div>
        <table id="leaguetable" class="tablesorter playerinfo" > </table>
        <table id="leaguetablehome" class="tablesorter playerinfo" > </table>
        <table id="leaguetableaway" class="tablesorter playerinfo" > </table>
    </div>
    
    <div id="mins" class="category">
        <div class="categoryheader" style="margin-top: 5px">Spilletid</div>
        <table id="playingminutes" class="tablesorter playerinfo"></table>
        <table id="subsout" class="tablesorter playerinfo"> </table>
        <table id="subsin" class="tablesorter playerinfo"> </table>
    </div>

    <div id="goal" class="category">
        <div class="categoryheader" style="margin-top: 5px">Mål</div>
        <table id="totalgoals" class="tablesorter playerinfo"> </table>
        <!--<table id="goals" class="tablesorter"> </table>-->
        <table id="penalty" class="tablesorter playerinfo"> </table>
        <table id="owngoal" class="tablesorter playerinfo"> </table>
    </div>

    <div id="dicipline" class="category">
        <div class="categoryheader" style="margin-top: 5px">Disiplin</div>
        <table id="yellowcard" class="tablesorter playerinfo"> </table>
        <table id="yellow_red" class="tablesorter playerinfo"> </table>
        <table id="redcard" class="tablesorter playerinfo"> </table>
    </div>
</div><?php }} ?>