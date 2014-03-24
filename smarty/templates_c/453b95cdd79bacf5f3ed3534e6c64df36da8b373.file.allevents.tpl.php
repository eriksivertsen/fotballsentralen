<?php /* Smarty version Smarty-3.1.12, created on 2014-03-24 11:36:36
         compiled from "smarty\templates\allevents.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2463751af145612af00-72046140%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '453b95cdd79bacf5f3ed3534e6c64df36da8b373' => 
    array (
      0 => 'smarty\\templates\\allevents.tpl',
      1 => 1395660994,
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
<?php if ($_valid && !is_callable('content_51af1456132d27_61389948')) {function content_51af1456132d27_61389948($_smarty_tpl) {?>    <table style="margin-left:30px;width:100%">
        <tr>
            <td style="vertical-align: top;width:40%;">
                <center>
                    <div id="select_div" style="display:inline; font-size:10pt">
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
                                <option value="13">Røde kort</option>
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
                                <option value="12">Eliteserien Futsal</option>
                            </select> 
                        </label>
                    </div>
                    <div id="radio">
                        <input id="teamradio" type="radio" value="team" name="type" onclick="getEventsTotalTeamSelected()"></input>
                        <label for="teamradio"><text style="font-size:10pt">Lag</text></label>
                        <input id="playerradio" type="radio" value="player" name="type" onclick="getEventsTotalSelected()"></input>
                        <label for="playerradio"><text style="font-size:10pt">Spillere</text></label>
                    </div>
                    <br/>
                <table id="event_top_table" style="border-collapse: collapse;border:1px solid black;border-bottom: 1px solid black">
                    <caption class="captionheader">Topp 3</caption>
                        <tr style=" background-color: #e8e8e8;border-bottom: 1px solid black">
                            <td style="width:30%;">
                                <img id="event_top_logo_1" style="margin:10px;width:80%;height:auto"/>
                            </td>
                            <td>
                                <text id="event_top_1_info"/>
                            </td>
                        </tr>
                        <tr style=" background-color: #ffffff;border-bottom: 1px solid black">
                            <td>
                                <img id="event_top_logo_2"  style="margin:10px;width:80%;height:auto"/>
                            </td>
                            <td>
                                <text id="event_top_2_info"></text>
                            </td>
                        </tr>
                        <tr style=" background-color: #e8e8e8;border-bottom: 1px solid black">
                            <td>
                                <img id="event_top_logo_3" style="margin:10px;width:80%;height:auto"/>
                            </td>
                            <td>
                                <text id="event_top_3_info"/>
                            </td>
                        </tr>
                    </table>
                </center>
            </td>
            <td>
                <center>
                    <table id="allEvents" class="tablesorter playerinfo" style="width:auto;table-layout: fixed;"></table>
                    <div id="playerminutes">
                        <center> 
                            <table id="playerminutes_table" class="tablesorter playerinfo" style="width:auto;table-layout: fixed;"></table>
                        </center>
                    </div>
                </center>
            </td>
        </tr>
    </table>
    
    <?php }} ?>