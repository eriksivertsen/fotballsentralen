<?php /* Smarty version Smarty-3.1.12, created on 2014-02-05 12:33:32
         compiled from "smarty\templates\header.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2520451692e292e1372-85071265%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '6652bf75f43eb8665356b88e8a0e2401680ee1b9' => 
    array (
      0 => 'smarty\\templates\\header.tpl',
      1 => 1391603610,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2520451692e292e1372-85071265',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51692e2933d185_31253988',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51692e2933d185_31253988')) {function content_51692e2933d185_31253988($_smarty_tpl) {?><div class="top">
    
    <div class="home">
        <a title="Hjem" href="http://www.fotballsentralen.com/"> <img src="images/logo.png"/></a>
    </div>
    
    <div class="settings">
        <table style="font-size: 10pt">
            <tr>
                <td>
                    <label class="selectlabel">
                        <select id="season" onchange="selectSeason()">
                            <option value="0">Totalt</option>
                            <option value="2011">2011</option>
                            <option value="2012">2012</option>
                            <option value="2013">2013</option>
                            <option value="2014">2014</option>
                        </select>
                    </label>
                </td>
            </tr>
            <tr>
                <td>
                    <label class="selectlabel">
                        <select id="leagueselect" onchange="selectLeague()">
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
                </td>
            </tr>
            <tr>
                <td>
                    <input id="tags" type="text" class="search" value="Søk " onfocus="removeText(this)"  onblur="addText()"></input>
                </td>
            </tr>
        </table>
    </div>
    <div class="nav-buttons">
        <ul id="jMenu">
            <li class="menu">
                <a class="fNiv" href="#" onclick="getTeam(1,0);return false;">
                    <img onclick="getTeam(1,0)" title="Tippeligaen" src="images/tippeliga.png" onmouseover="this.src='images/tippeliga-hover.png'" onmouseout="this.src='images/tippeliga.png'" style="cursor:pointer;"/>
                </a>
                <ul id="tippeligaen" style="display: none;"></ul>
            </li>
            <li class="menu">
                <a class="fNiv" href="#" onclick="getTeam(2,0);return false;">
                    <img onclick="getTeam(2,0)" title="Adeccoligaen" src="images/adecco.png" onmouseover="this.src='images/adecco-hover.png'" onmouseout="this.src='images/adecco.png'" style="cursor:pointer;"/>
                </a>
                <ul id="1div" style="display: none;"></ul>
            </li>
            <li class="menu">
                <a class="fNiv" href="#" onclick="getTeam(8,0);return false;">
                    <img title="2.divisjon" src="images/oddsenligaen.png" onmouseover="this.src='images/oddsenligaen-hover.png'" onmouseout="this.src='images/oddsenligaen.png'" style="cursor:pointer;"/>
                </a>
                <ul>
                    <li class="arrow"></li>
                    <li><a href="#" onclick="getTeam(3,0);return false;">Avdeling 1</a>
                        <ul id="2div1" style="display: none;"></ul>
                    </li>
                    <li><a href="#" onclick="getTeam(4,0);return false;">Avdeling 2</a>
                        <ul id="2div2" style="display: none;"></ul>
                    </li>
                    <li><a href="#" onclick="getTeam(5,0);return false;">Avdeling 3</a>
                        <ul id="2div3" style="display: none;"></ul>
                    </li>
                    <li><a href="#" onclick="getTeam(6,0);return false;">Avdeling 4</a>
                        <ul id="2div4" style="display: none;"></ul>
                    </li>
                </ul>
            </li>
            <li class="menu">
                <a class="fNiv" href="#">
                    <img title="Topplister" src="images/toplist.png" onmouseover="this.src='images/toplist-hover.png'" onmouseout="this.src='images/toplist.png'"/>
                </a>
                <ul>
                    <li><a href="#" onclick="getEventsTotal(50,leagueidselected);return false;">Seiersprosent</a></li>
                    <li><a href="#" onclick="getEventsTotal(11,leagueidselected);return false;">Spilleminutter</a></li>
                    <li><a href="#" onclick="getEventsTotal(80,leagueidselected);return false;">Spilletid i prosent</a></li>
                    <li><a href="#" onclick="getEventsTotal(10,leagueidselected);return false;">Toppscorer</a></li>
                    <li><a href="#" onclick="getEventsTotal(60,leagueidselected);return false;">Måleffektivitet</a></li>
                    <li><a href="#" onclick="getEventsTotal(8,leagueidselected);return false;">Straffemål</a></li>
                    <li><a href="#" onclick="getEventsTotal(12,leagueidselected);return false;">Clean sheets</a></li>
                    <li><a href="#" onclick="getEventsTotal(4,leagueidselected);return false;">Spillemål</a></li>
                    <li><a href="#" onclick="getEventsTotal(70,leagueidselected);return false;">Mål&nbspsom&nbspinnbytter</a></li>
                    <li><a href="#" onclick="getEventsTotal(9,leagueidselected);return false;">Selvmål</a></li>
                    <li><a href="#" onclick="getEventsTotal(2,leagueidselected);return false;">Gule&nbspkort</a></li>
                    <li><a href="#" onclick="getEventsTotal(3,leagueidselected);return false;">Rødt&nbspkort&nbsp(direkte)</a></li>
                    <li><a href="#" onclick="getEventsTotal(1,leagueidselected);return false;">Rødt&nbspkort&nbsp(to&nbspgule)</a></li>
                    <li><a href="#" onclick="getEventsTotal(6,leagueidselected);return false;">Byttet&nbspinn</a></li>
                    <li><a href="#" onclick="getEventsTotal(7,leagueidselected);return false;">Byttet&nbsput</a></li>
                </ul>
            </li>
            <li class="menu">
                <a class="fNiv" href="#" onclick="getPreviewMatches();return false;">
                    <img title="Forhåndsstoff" src="images/preview.png" onmouseover="this.src='images/preview-hover.png'" onmouseout="this.src='images/preview.png'" style="cursor:pointer;"/>
                </a>
            </li>
            <li class="menu">
                <a class="fNiv" href="#" onclick="getSuspensionList(suspendedLeagueLand);return false;">
                    <img title="Suspensjonsliste" src="images/suspension.png" onmouseover="this.src='images/suspension-hover.png'" onmouseout="this.src='images/suspension.png'" style="cursor:pointer;"/>
                </a>
            </li>
            <li class="menu">
                <a class="fNiv" href="#">
                    <img title="Annet" src="images/other.png" onmouseover="this.src='images/other-hover.png'" onmouseout="this.src='images/other.png'" style="cursor:pointer;"/>
                </a>
                <ul>
                    <li><a href="#" onclick="getPopulare();return false;">Populære</a></li>
                    <li><a href="#" onclick="getReferee();return false;">Dommere</a></li>
                    <li><a href="#" onclick="getTransfers();return false;">Overganger</a></li>
                    <li><a href="#" onclick="getScopeCurrent();return false;">Statoskopet</a></li>
                </ul>
            </li>
            <li class="menu">
                <a class="fNiv" href="#" onclick="getTeam(11,0);return false;">
                    <img onclick="getTeam(11,0)" title="Nord-Norge United" src="images/nnunited.png" onmouseover="this.src='images/nnunited-hover.png'" onmouseout="this.src='images/nnunited.png'" style="cursor:pointer;"/>
                </a>
                <ul id="1div" style="display: none;"></ul>
            </li>
        </ul>
    </div>
    <div class="social-buttons">
        <a target="_blank" title="Stalk oss på Facebook" href="http://www.facebook.com/FotballSentral1"><img src="images/facebook.png" onmouseover="this.src='images/facebook-hover.png'" onmouseout="this.src='images/facebook.png'"/></a>
        <a target="_blank" title="Stalk oss på Twitter" href="http://www.twitter.com/OptaBrede"><img src="images/twitter-icon.png" onmouseover="this.src='images/twitter-icon-hover.png'" onmouseout="this.src='images/twitter-icon.png'"/></a>
    </div>
</div><?php }} ?>