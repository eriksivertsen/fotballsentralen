<?php /* Smarty version Smarty-3.1.12, created on 2013-09-15 09:41:48
         compiled from "smarty\templates\events.tpl" */ ?>
<?php /*%%SmartyHeaderCode:6705519c9f3b113720-42590549%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '97909604d98e0a50eafe6000722a870744d61ea0' => 
    array (
      0 => 'smarty\\templates\\events.tpl',
      1 => 1379238107,
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
    <div class="categoryheader"><text id="league_name"></text></div>
    <table id="league_table" style="font-size: 9pt; width:100%">
        <tr>
            <td align="center">Toppscorer: </td> 
            <td align="center">Beste hjemmelag: </td> 
            <td align="center">Beste bortelag: </td> 
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
    <div id="news_0">
        <ul id="js-news_0" class="js-hidden">
            <li class="news-item"> </li>
        </ul>
    </div>
    <div id="news_1">
        <ul id="js-news_1" class="js-hidden">
            <li class="news-item"> </li>
        </ul>
    </div>
    <div id="news_2">
        <ul id="js-news_2" class="js-hidden">
            <li class="news-item"> </li>
        </ul>
    </div>
    <div id="news_3">
        <ul id="js-news_3" class="js-hidden">
            <li class="news-item"> </li>
        </ul>
    </div>
    <div id="news_4">
        <ul id="js-news_4" class="js-hidden">
            <li class="news-item"> </li>
        </ul>
    </div>
    <div id="news_5">
        <ul id="js-news_5" class="js-hidden">
            <li class="news-item"> </li>
        </ul>
    </div>
    <div id="news_6">
        <ul id="js-news_6" class="js-hidden">
            <li class="news-item"> </li>
        </ul>
    </div>
    <div id="news_8">
        <ul id="js-news_8" class="js-hidden">
            <li class="news-item"> </li>
        </ul>
    </div>
     <div id="overall" class="category">
        <div class="categoryheader" style="margin-top: 5px">Tabeller</div>
        <table id="leaguetable" class="tablesorter playerinfo" style="display: inline-table;"> </table>
        <table id="leaguetablehome" class="tablesorter playerinfo" style="display: inline-table;"> </table>
        <table id="leaguetableaway" class="tablesorter playerinfo" style="display: inline-table;"> </table>
    </div>
    
    <div id="mins" class="category">
        <div class="categoryheader" style="margin-top: 5px">Spilletid</div>
        <table id="playingminutes" class="tablesorter"></table>
        <table id="subsout" class="tablesorter"> </table>
        <table id="subsin" class="tablesorter"> </table>
    </div>

    <div id="goal" class="category">
        <div class="categoryheader" style="margin-top: 5px">Mål</div>
        <table id="totalgoals" class="tablesorter"> </table>
        <!--<table id="goals" class="tablesorter"> </table>-->
        <table id="penalty" class="tablesorter"> </table>
        <table id="owngoal" class="tablesorter"> </table>
    </div>

    <div id="dicipline" class="category">
        <div class="categoryheader" style="margin-top: 5px">Disiplin</div>
        <table id="yellowcard" class="tablesorter"> </table>
        <table id="yellow_red" class="tablesorter"> </table>
        <table id="redcard" class="tablesorter"> </table>
    </div>

</div><?php }} ?>