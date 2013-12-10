<?php /* Smarty version Smarty-3.1.12, created on 2013-12-09 21:50:46
         compiled from "smarty\templates\scope.tpl" */ ?>
<?php /*%%SmartyHeaderCode:142135298b62dd3ec34-05328486%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '399a4adc6e76a431410b99d4c4d9b22af7336f68' => 
    array (
      0 => 'smarty\\templates\\scope.tpl',
      1 => 1386625568,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '142135298b62dd3ec34-05328486',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5298b62dd41b67_34889749',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5298b62dd41b67_34889749')) {function content_5298b62dd41b67_34889749($_smarty_tpl) {?><div align="center">   
    
    <input id="scope_name" value="Oversikt uten navn" onclick="changeName()" onblur="storeName()"  class="scope_header" title="Klikk for å endre"></input><br/><br/>
    
    <span id="time"></span><br/><br/>
    
    
    <label id="label_league" class="selectlabel">
        <select id="scope_league" style="margin: 4px" onchange="setLeagueSelected()">
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
    
    <br/><br/>
    
    <span style="font-size:8pt;">Zoom:
        <a href="#" onclick="setZoom(1);return false">1 mnd</a> - 
        <a href="#" onclick="setZoom(3);return false">3 mnd</a> -
        <a href="#" onclick="setZoom(6);return false">6 mnd</a> - 
        <a href="#" onclick="setZoom(9);return false">1 år</a>
    </span>
    <div>
        <div style="font-size:8pt;margin-right:10px;display:inline-block">«  
            <a href="#" onclick="addYearToSlider(-1);return false">1 år</a>  
            <a href="#" onclick="addMonthToSlider(-1);return false">1 måned</a> 
        </div>
        <div id="slider-range" style="width:450px;background:#b5b5b5;display:inline-block">
        </div>
         <div style="font-size:8pt;margin-left:10px;display:inline-block">
            <a href="#" onclick="addMonthToSlider(1);return false">1 måned</a> 
            <a href="#" onclick="addYearToSlider(1);return false">1 år</a>»  
        </div>
    </div>
    <span style="font-size:8pt;">Hurtigvalg:
        <a href="#" onclick="setSlider(2011);return false">2011</a> - 
        <a href="#" onclick="setSlider(2012);return false">2012</a> -
        <a href="#" onclick="setSlider(2013);return false">2013</a> - 
        <a href="#" onclick="setSlider(0);return false">Alle</a>
    </span>
    
    <br/><br/>
    
    <button onclick="getScopeCurrent()">Scope it!</button>
    <button onclick="saveScope()">Save it!</button>
    <button onclick="getRandomScope()">Tilfeldig oversikt, takk!</button>
    
    <div id="scope_events" class="category">
        <div class="categoryheader" style="margin-top: 5px"></div>
        <div id="scope_event0_div" style="display: inline-table">
            <table id="scope_event0" class="tablesorter playerinfo"></table>
        </div>
        <div id="scope_event1_div" style="display: inline-table">
            <table id="scope_event1" class="tablesorter playerinfo"></table>
        </div>
        <div id="scope_event2_div" style="display: inline-table">
            <table id="scope_event2" class="tablesorter playerinfo"></table>
        </div>
    </div>
    
    <div id="scope_events1" class="category">
        <div class="categoryheader" style="margin-top: 5px"></div>
        <div id="scope_event3_div" style="display: inline-table">
            <table id="scope_event3" class="tablesorter playerinfo"></table>
        </div>
        <div id="scope_event4_div" style="display: inline-table">
            <table id="scope_event4" class="tablesorter playerinfo"></table>
        </div>
        <div id="scope_event5_div" style="display: inline-table">
            <table id="scope_event5" class="tablesorter playerinfo"></table>
        </div>
    </div>
    
    <div id="scope_events2" class="category">
        <div class="categoryheader" style="margin-top: 5px"></div>
        <div id="scope_event6_div" style="display: inline-table">
            <table id="scope_event6" class="tablesorter playerinfo"></table>
        </div>
        <div id="scope_event7_div" style="display: inline-table">
            <table id="scope_event7" class="tablesorter playerinfo"></table>
        </div>
        <div id="scope_event8_div" style="display: inline-table">
            <table id="scope_event8" class="tablesorter playerinfo"></table>
        </div>
    </div>
</div><?php }} ?>