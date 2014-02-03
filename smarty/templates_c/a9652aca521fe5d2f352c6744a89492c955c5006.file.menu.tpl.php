<?php /* Smarty version Smarty-3.1.12, created on 2013-12-23 23:18:51
         compiled from "smarty\templates\menu.tpl" */ ?>
<?php /*%%SmartyHeaderCode:3245450ad1b4d2d7528-96294350%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a9652aca521fe5d2f352c6744a89492c955c5006' => 
    array (
      0 => 'smarty\\templates\\menu.tpl',
      1 => 1387840561,
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
    'searcharray' => 0,
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
                
                $('#tags').autocomplete({
                    source:<?php echo $_smarty_tpl->tpl_vars['searcharray']->value;?>
,
                    select: function( event, ui ) { 
                        if(ui.item.type == 'player'){
                            getPlayerSearch(ui.item.id);
                        }else if(ui.item.type == 'team'){
                            getTeamInfoSearch(ui.item.id);
                        }
                    }
                });
                                
            });
        </script> 
    </head>
    <body>
        <div class="menubody">
            <ul id="breadcrumbs">
                <li> </li>
            </ul>
        </div>
    </body>
</html><?php }} ?>