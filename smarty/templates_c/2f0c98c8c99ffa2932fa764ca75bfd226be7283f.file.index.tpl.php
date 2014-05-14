<?php /* Smarty version Smarty-3.1.12, created on 2014-05-12 08:42:57
         compiled from "smarty\templates\matchobserver\index.tpl" */ ?>
<?php /*%%SmartyHeaderCode:256695348fd519fdef6-15201992%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2f0c98c8c99ffa2932fa764ca75bfd226be7283f' => 
    array (
      0 => 'smarty\\templates\\matchobserver\\index.tpl',
      1 => 1399884055,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '256695348fd519fdef6-15201992',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5348fd51c09ef4_65961656',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5348fd51c09ef4_65961656')) {function content_5348fd51c09ef4_65961656($_smarty_tpl) {?><html>
    <head>
        <title>Tipsway</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/tipsway/styles.css">
        <link rel="stylesheet" href="css/tipsway/tipsway.css">

        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script type="text/javascript" src="js/matchobserver.js"></script>
        <script type="text/javascript" src="js/admin.js"></script>
        <script type="text/javascript" src="js/overlib/overlib.js"></script>
        <script type="text/javascript" src="js/Utils.js"></script>
        <script type="text/javascript" src="js/jquery-ui.js"></script>  
        <script type="text/javascript" src="js/spin.js"></script> 
        <script type="text/javascript" src="js/expander/jquery.expander.js"></script>
        <script type="text/javascript" src="js/readmore.js"></script>
        <script type="text/javascript" src="js/common.js"></script>  
        <script>
            $(document).ready(function() {
            $('#match_period').tabs();
            $('#match_basic_div').tabs();
            $('#matchlist_div').tabs();
            $('#team_detail').tabs();
            //                $('[id^="slider_"]').slider();
            getInfo();
            //                getUsersLeague();
            //                $("input:checkbox").click(function() {
            //                    if ($(this).is(":checked")) {
            //                        var group = "input:checkbox[name='" + $(this).attr("name") + "']";
            //                        $(group).prop("checked", false);
            //                        $(this).prop("checked", true);
            //                    } else {
            //                        $(this).prop("checked", false);
            //                    }
            //                });
        });
        </script>
    </head>
    <body>
        <div id="loader" class="loader"></div>
        <div id="doc3" class="yui-t7">
            <div id="bd">
                <div id="yui-main">
                    <div class="yui-b">
                        <div class="yui-gc">
                            <div class="yui-u first">
                                <div class="content">
                                    <text class="content-header">Kampliste</text>
                                    <?php echo $_smarty_tpl->getSubTemplate ("matchobserver/matchlist.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                                </div>
                            </div>
                            <div class="yui-u">
                                <div class="content">
                                    <text class="content-header">Informasjon</text>
                                    <?php echo $_smarty_tpl->getSubTemplate ("matchobserver/match_basic.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="bd">
                    <div id="yui-main">
                        <div class="yui-b">
                            <div class="yui-gc">
                                <div class="yui-u first">
                                    <div class="content">
                                        <text class="content-header">Detaljer </text>
                                        <?php echo $_smarty_tpl->getSubTemplate ("matchobserver/news_detail.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                                    </div>
                                </div>
                                <div class="yui-u">
                                    <div class="content">
                                        <text class="content-header">Mer info // </text>  </text> <text class="content-header" style="background-color:yellow">Inneholder mulig tropp </text>
                                        <?php echo $_smarty_tpl->getSubTemplate ("matchobserver/news.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </body>
    <a style="color: white" href="settings.php">Innstillinger</a>
</html>
<?php }} ?>