<?php /* Smarty version Smarty-3.1.12, created on 2013-11-29 14:12:02
         compiled from "smarty\templates\allevents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2463751af145612af00-72046140%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '453b95cdd79bacf5f3ed3534e6c64df36da8b373' => 
    array (
      0 => 'smarty\\templates\\allevents.tpl',
      1 => 1385734320,
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
<?php if ($_valid && !is_callable('content_51af1456132d27_61389948')) {function content_51af1456132d27_61389948($_smarty_tpl) {?><center>
<div id="select_div" style="display:inline; font-size:10pt"'>
    <label id="label_event" class="selectlabel">
        <select id="allEventsSelectType" style="margin: 4px" onchange="selectTotalEventsType()">
            <option value="50">Seiersprosent</option>
            <option value="11">Spilleminutter</option>
            <option value="80">Spilletid i prosent</option>
            <option value="10">Toppscorer</option>
            <option value="60">Måleffektivitet</option>
            <option value="8">Straffemål</option>
            <option value="12">Clean sheets</option>
            <option value="70">Mål som innbytter</option>
            <option value="4">Spillemål</option>
            <option value="9">Selvmål</option>
            <option value="2">Gule kort</option>
            <option value="3">Rødt kort (direkte)</option>
            <option value="1">Rødt kort (to gule)</option>
            <option value="6">Byttet inn</option>
            <option value="7">Byttet ut</option>
        </select>
    </label>
    <br/>
    <label id="label_league" class="selectlabel">
        <select id="allEventsSelect" style="margin: 4px" onchange="selectTotalEvents()">
            <option value="0">Norge</option>
            <option value="1">Tippeligaen</option>
            <option value="2">Adeccoligaen</option>
            <option value="8">2.divisjon</option>
            <option value="3">2.div avdeling 1</option>
            <option value="4">2.div avdeling 2</option>
            <option value="5">2.div avdeling 3</option>
            <option value="6">2.div avdeling 4</option>
            <option value="11">Nord-Norge United</option>
        </select> 
    </label>
</div>
    <div id="radio">
        <input id="teamradio" type="radio" value="team" name="type" onclick="getEventsTotalTeamSelected()"></input>
        <label for="teamradio"><text style="font-size:10pt">Lag</text></label>
        <input id="playerradio" type="radio" value="player" name="type" onclick="getEventsTotalSelected()"></input>
        <label for="playerradio"><text style="font-size:10pt">Spillere</text></label>
    </div>
    <div style="text-align: -moz-center">
        <table id="allEvents" class="tablesorter playerinfo" style="width:auto;table-layout: fixed;"></table>
    </div>
    
     <div id="playerminutes">
        <center> 
            <div style="text-align: -moz-center">
                <table id="playerminutes_table" class="tablesorter playerinfo" style="width:auto;table-layout: fixed;"></table>
            </div>
        </center>
    </div>
</center><?php }} ?>