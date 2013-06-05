<?php /* Smarty version Smarty-3.1.12, created on 2013-06-03 11:43:29
         compiled from "smarty\templates\menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3245450ad1b4d2d7528-96294350%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a9652aca521fe5d2f352c6744a89492c955c5006' => 
    array (
      0 => 'smarty\\templates\\menu.tpl',
      1 => 1370259805,
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
                
                var season = '<?php echo $_smarty_tpl->tpl_vars['season']->value;?>
';

                if(season != '') {
                    setSeason(season);
                }
                
                getLeagues();
                $("#jMenu").jMenu({
                    
                    animatedText : true,
                    paddingLeft: 2
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
                                window.location.href = 'index.php?season=<?php echo $_smarty_tpl->tpl_vars['season']->value;?>
&'+ui.item.type+'_id='+ui.item.id;
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
                    <li><a class="fNiv" href="index.php">Hjem</a></li>                        
                    
                    <li><a class="fNiv" href="index.php?season=<?php echo $_smarty_tpl->tpl_vars['season']->value;?>
&league_id=1">Tippeligaen</a>
                        <ul id="tippeligaen" style="display: none;"></ul>
                    </li>

                    <li><a class="fNiv" href="index.php?season=<?php echo $_smarty_tpl->tpl_vars['season']->value;?>
&league_id=2">Adeccoligaen</a>
                        <ul id="1div" style="display: none;"></ul>
                    </li>

                    <li><a class="fNiv" href="index.php?season=<?php echo $_smarty_tpl->tpl_vars['season']->value;?>
&league_id=8">2. divisjon</a>
                        <ul>
                            <li class="arrow"></li>
                            <li><a href="index.php?season=<?php echo $_smarty_tpl->tpl_vars['season']->value;?>
&league_id=3">Avdeling 1</a>
                                <ul id="2div1" style="display: none;"></ul>
                            </li>
                            <li><a href="index.php?season=<?php echo $_smarty_tpl->tpl_vars['season']->value;?>
&league_id=4">Avdeling 2</a>
                                <ul id="2div2" style="display: none;"></ul>
                            </li>
                            <li><a href="index.php?season=<?php echo $_smarty_tpl->tpl_vars['season']->value;?>
&league_id=5">Avdeling 3</a>
                                <ul id="2div3" style="display: none;"></ul>
                            </li>
                            <li><a href="index.php?season=<?php echo $_smarty_tpl->tpl_vars['season']->value;?>
&league_id=6">Avdeling 4</a>
                                <ul id="2div4" style="display: none;"></ul>
                            </li>
                        </ul>
                    </li>

                    <li><a class="fNiv" href="#" onclick="getPopulare()">Populære</a></li>
                    <li><a class="fNiv" href="#" onclick="getPreviewMatches()">Forhåndsstoff</a></li>
                    <li><a class="fNiv" href="#" onclick="getSuspensionList(134365)">Suspensjonsliste</a></li>
                    <li><a class="fNiv" href="#" onclick="getReferee()">Dommere</a></li>
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