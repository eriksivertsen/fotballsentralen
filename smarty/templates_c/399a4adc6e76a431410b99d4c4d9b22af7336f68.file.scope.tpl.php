<?php /* Smarty version Smarty-3.1.12, created on 2013-12-23 10:43:46
         compiled from "smarty\templates\scope.tpl" */ ?>
<?php /*%%SmartyHeaderCode:142135298b62dd3ec34-05328486%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '399a4adc6e76a431410b99d4c4d9b22af7336f68' => 
    array (
      0 => 'smarty\\templates\\scope.tpl',
      1 => 1387555942,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '142135298b62dd3ec34-05328486',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5298b62dd41b67_34889749',
  'variables' => 
  array (
    'scopes' => 0,
    'scope' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5298b62dd41b67_34889749')) {function content_5298b62dd41b67_34889749($_smarty_tpl) {?><div align="center">   
    
    <input id="scope_name" size="60" value="Oversikt uten navn" onclick="changeName()" onblur="storeName()"  class="scope_header" title="Klikk for å endre"></input><br/><br/>
    
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
    
    <button onclick="getScopeCurrent()">Hent data</button>
    <!--<button onclick="getRandomScope()">Tilfeldig oversikt, takk!</button>-->
    <button onclick="clearScope()">Nullstill</button>
    <button onclick="saveScope()">Lagre</button>
    <br/>
    <label for="scope_public" style="font-size:8pt;">Offentlig:</label>
    <input type="checkbox" checked="true" value="1" id="scope_public" title="Hvis oversikten blir godkjent av FotballSentralen, vil den bli tilgjengelig for offentligheten."></input>
    <br/>
    <br/>
    <label id="label_league"  class="selectlabel">
        <select id="scope_select" style="margin: 4px" onchange="getScopeList()">
            <option value="">Velg oversikt</option>
        <?php  $_smarty_tpl->tpl_vars['scope'] = new Smarty_Variable; $_smarty_tpl->tpl_vars['scope']->_loop = false;
 $_from = $_smarty_tpl->tpl_vars['scopes']->value; if (!is_array($_from) && !is_object($_from)) { settype($_from, 'array');}
foreach ($_from as $_smarty_tpl->tpl_vars['scope']->key => $_smarty_tpl->tpl_vars['scope']->value){
$_smarty_tpl->tpl_vars['scope']->_loop = true;
?>
            <option value="<?php echo $_smarty_tpl->tpl_vars['scope']->value['url'];?>
"><?php echo $_smarty_tpl->tpl_vars['scope']->value['name'];?>
</option>
        <?php } ?>
        </select>
    </label>

    <div id="scope_events" class="category">
        <div class="categoryheader" style="margin-top: 5px"></div>
        <div id="scope_event0_div" style="display: inline-table;vertical-align:top">
            <table id="scope_event0" class="tablesorter playerinfo"></table>
            <div id="graphedit_0" style='display:none;width:300px;height:250px;'></div>
            <table id="newgraph_0" class="emptytable" style='display:none;'></table>
        </div>
        <div id="scope_event1_div" style="display: inline-table;vertical-align:top">
            <table id="scope_event1" class="tablesorter playerinfo"></table>
            <div id="graphedit_1" style='display:none;width:300px;height:250px;'></div>
            <table id="newgraph_1" class="emptytable" style='display:none;'></table>
        </div>
        <div id="scope_event2_div" style="display: inline-table;vertical-align:top">
            <table id="scope_event2" class="tablesorter playerinfo"></table>
            <div id="graphedit_2" style='display:none;width:300px;height:250px;'></div>
            <table id="newgraph_2" class="emptytable" style='display:none;'></table>
        </div>
    </div>
    
    <div id="scope_events1" class="category">
        <div class="categoryheader" style="margin-top: 5px"></div>
        <div id="scope_event3_div" style="display: inline-table;vertical-align:top">
            <table id="scope_event3" class="tablesorter playerinfo"></table>
            <div id="graphedit_3" style='display:none;width:300px;height:250px;'></div>
            <table id="newgraph_3" class="emptytable" style='display:none;'></table>
        </div>
        <div id="scope_event4_div" style="display: inline-table;vertical-align:top">
            <table id="scope_event4" class="tablesorter playerinfo"></table>
             <div id="graphedit_4" style='display:none;width:300px;height:250px;'></div>
             <table id="newgraph_4" class="emptytable" style='display:none;'></table>
        </div>
        <div id="scope_event5_div" style="display: inline-table;vertical-align:top">
            <table id="scope_event5" class="tablesorter playerinfo"></table>
            <div id="graphedit_5" style='display:none;width:300px;height:250px;'></div>
            <table id="newgraph_5" class="emptytable" style='display:none;'></table>
        </div>
    </div>
    
    <div id="scope_events2" class="category">
        <div class="categoryheader" style="margin-top: 5px"></div>
        <div id="scope_event6_div" style="display: inline-table;vertical-align:top">
            <table id="scope_event6" class="tablesorter playerinfo"></table>
            <div id="graphedit_6" style='display:none;width:300px;height:250px;'></div>
            <table id="newgraph_6" class="emptytable" style='display:none;'></table>
        </div>
        <div id="scope_event7_div" style="display: inline-table;vertical-align:top">
            <table id="scope_event7" class="tablesorter playerinfo"></table>
            <div id="graphedit_7" style='display:none;width:300px;height:250px;'></div>
            <table id="newgraph_7" class="emptytable" style='display:none;'></table>
        </div>
        <div id="scope_event8_div" style="display: inline-table;vertical-align:top">
            <table id="scope_event8" class="tablesorter playerinfo"></table>
            <div id="graphedit_8" style='display:none;width:300px;height:250px;'></div>
            <table id="newgraph_8" class="emptytable" style='display:none;'></table>
        </div>
    </div>
</div><?php }} ?>