<?php /* Smarty version Smarty-3.1.12, created on 2013-07-05 16:55:44
         compiled from "smarty\templates\menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3245450ad1b4d2d7528-96294350%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a9652aca521fe5d2f352c6744a89492c955c5006' => 
    array (
      0 => 'smarty\\templates\\menu.tpl',
      1 => 1373028692,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '3245450ad1b4d2d7528-96294350',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50ad1b4d3a99a2_93099756',
  'variables' => 
  array (
    'season' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50ad1b4d3a99a2_93099756')) {function content_50ad1b4d3a99a2_93099756($_smarty_tpl) {?><html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <head>
        <script type="text/javascript">       
            function removeText(obj) {   obj.value = ''; $('#tags').removeAttr('style'); } 
            function addText() { $('#tags').html('Søk '); $('#tags').css('font-style','italic'); $('#tags').css('color','grey'); }
            
            $(document).ready(function() {
                
                $("#breadcrumbs").breadcrumbs("home");
                $('#tags').val('Søk ');
                var season = '<?php echo $_smarty_tpl->tpl_vars['season']->value;?>
';
                if(season != '') {
                    setSeason(season);
                }
                getLeagues();
                $("#jMenu").jMenu({
                    ulWidth : '120',
                    animatedText : true
                });
                
                $.ajax({
                    type: "POST",
                    url: "receiver.php",
                    dataType: "json",
                    timeout: timeout,
                    data: {
                        action: "getSearchArray"
                    },
                    error: function () {
                        stopLoad()
                    },
                    success: function(json) {                       
                        
                        $('#tags').autocomplete({
                            source:json,
                            select: function( event, ui ) { 
                                if(ui.item.type == 'player'){
                                    getPlayerSearch(ui.item.id);
                                }else if(ui.item.type == 'team'){
                                    getTeamInfoSearch(ui.item.id);
                                }
                            }
                        });
                    }
                });
                
            });
        </script> 
    </head>
    <body>
        <div class="menubody">
            <center>
                <ul id="jMenu">
                    <li><a class="fNiv" href="#" onclick="getTeam(0,0)">Hjem</a></li>                        
                    
                    <li><a class="fNiv" href="#" onclick="getTeam(1,0)">Tippeligaen</a>
                        <ul id="tippeligaen" style="display: none;"></ul>
                    </li>

                    <li><a class="fNiv" href="#" onclick="getTeam(2,0)">Adeccoligaen</a>
                        <ul id="1div" style="display: none;"></ul>
                    </li>

                    <li><a class="fNiv" href="#" onclick="getTeam(8,0)">2. divisjon</a>
                        <ul>
                            <li class="arrow"></li>
                            <li><a href="#" onclick="getTeam(3,0)">Avdeling 1</a>
                                <ul id="2div1" style="display: none;"></ul>
                            </li>
                            <li><a href="#" onclick="getTeam(4,0)">Avdeling 2</a>
                                <ul id="2div2" style="display: none;"></ul>
                            </li>
                            <li><a href="#" onclick="getTeam(5,0)">Avdeling 3</a>
                                <ul id="2div3" style="display: none;"></ul>
                            </li>
                            <li><a href="#" onclick="getTeam(6,0)">Avdeling 4</a>
                                <ul id="2div4" style="display: none;"></ul>
                            </li>
                        </ul>
                    </li>
                    <li><a class="fNiv" href="#" onclick="getPopulare()">Populære</a></li>
                    <li><a class="fNiv" href="#" onclick="getPreviewMatches()">Forhåndsstoff</a></li>
                    
                    <li><a class="fNiv" href="#">Annet</a>
                        <ul>
                            <li><a href="#" onclick="getReferee()">Dommere</a></li>
                            <li><a href="#" onclick="getTransfers()">Overganger</a></li>
                            <li><a href="#">Topplister</a>
                                <ul>
                                    <li><a href="#" onclick="getTotalPlayerMinutes()">Spilleminutter</a></li>
                                    <li><a href="#" onclick="getEventsTotal(10,0)">Toppscorer</a></li>
                                    <li><a href="#" onclick="getEventsTotal(8,0)">Straffemål</a></li>
                                    <li><a href="#" onclick="getEventsTotal(4,0)">Spillemål</a></li>
                                    <li><a href="#" onclick="getEventsTotal(9,0)">Selvmål</a></li>
                                    <li><a href="#" onclick="getEventsTotal(2,0)">Gule&nbspkort</a></li>
                                    <li><a href="#" onclick="getEventsTotal(3,0)">Rødt&nbspkort&nbsp(direkte)</a></li>
                                    <li><a href="#" onclick="getEventsTotal(1,0)">Rødt&nbspkort&nbsp(to&nbspgule)</a></li>
                                    <li><a href="#" onclick="getEventsTotal(6,0)">Byttet&nbspinn</a></li>
                                    <li><a href="#" onclick="getEventsTotal(7,0)">Byttet&nbsput</a></li>
                                </ul>
                            </li>
                        </ul>
                    </li>
                    <li><a class="fNiv" href="#" onclick="getSuspensionList(134365)">Suspensjonsliste</a></li>
                    
                    
                </ul>
            </center>
            <ul id="breadcrumbs">
                <li> </li>
            </ul>
            <div name="season" id="seasonselect" style="margin:5px;">
                <select id="season" onchange="selectSeason()">
                    <option value="2011">2011</option>
                    <option value="2012">2012</option>
                    <option value="2013">2013</option>
                </select>
                
                <input id="tags" type="text" style="font-style: italic;color: grey;" value="Søk " onfocus="removeText(this)"  onblur="addText()"></input>
                
            </div>      
        </div>
    </body>
</html><?php }} ?>