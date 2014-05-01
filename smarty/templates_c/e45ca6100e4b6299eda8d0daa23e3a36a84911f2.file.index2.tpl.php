<?php /* Smarty version Smarty-3.1.12, created on 2014-04-20 09:05:44
         compiled from "smarty\templates\matchobserver\index2.tpl" */ ?>
<?php /*%%SmartyHeaderCode:21865535384c6800a97-07111031%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'e45ca6100e4b6299eda8d0daa23e3a36a84911f2' => 
    array (
      0 => 'smarty\\templates\\matchobserver\\index2.tpl',
      1 => 1397984742,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '21865535384c6800a97-07111031',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535384c6897466_55589883',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535384c6897466_55589883')) {function content_535384c6897466_55589883($_smarty_tpl) {?><html>
    <head>
        <title>Tipsway</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/tipsway/styles.css">
        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script type="text/javascript" src="js/matchobserver.js"></script>
        <script type="text/javascript" src="js/overlib/overlib.js"></script>
        <script type="text/javascript" src="js/Utils.js"></script>
        <script type="text/javascript" src="js/jquery-ui.js"></script>  
        <script type="text/javascript" src="js/spin.js"></script> 
        <script type="text/javascript" src="js/expander/jquery.expander.js"></script>
        <script type="text/javascript" src="js/readmore.js"></script>
        <script type="text/javascript" src="js/common.js"></script>  
        <script>
            $(document).ready(function() {
            $( "#tabs" ).tabs().addClass( "ui-tabs-vertical ui-helper-clearfix" );
            $( "#tabs li" ).removeClass( "ui-corner-top" ).addClass( "ui-corner-left" );
            getInfo();
        });
        </script>
    </head>
    <body>
        <div id="doc3" class="yui-t7">
            <div id="hd">
                <div id="header"><h1 id="header_match"></h1></div>
            </div>
            <div id="bd">
                <div id="loader" class="loader"></div>
                <div id="yui-main">
                    <div class="yui-b">
                        <div class="yui-gb">
                            <div class="yui-u first">
                                <div class="content">
                                    <ul id="matches" style="font-size: 10pt">
                                        <li><a><b>Tippeligaen</b></a></li>
                                        <li><a href="#tippeligaen-1">Nunc tincidunt</a></li>
                                        <li><a href="#tippeligaen-2">Proin dolor</a></li>
                                        <li><a href="#tippeligaen-3">Aenean lacinia</a></li>
                                        <li><a href="#tippeligaen-4">Nunc tincidunt</a></li>
                                        <li><a href="#tippeligaen-5">Proin dolor</a></li>
                                        <li><a href="#tippeligaen-6">Aenean lacinia</a></li>
                                        <li><a href="#tippeligaen-7">Nunc tincidunt</a></li>
                                        <li><a href="#tippeligaen-8">Proin dolor</a></li>
                                        <li><a><b>1.divisjon</b></a></li>
                                        <li><a href="#1div-1">Nunc tincidunt</a></li>
                                        <li><a href="#1div-2">Proin dolor</a></li>
                                        <li><a href="#1div-3">Aenean lacinia</a></li>
                                        <li><a href="#1div-4">Nunc tincidunt</a></li>
                                        <li><a href="#1div-5">Proin dolor</a></li>
                                        <li><a href="#1div-6">Aenean lacinia</a></li>
                                        <li><a href="#1div-7">Nunc tincidunt</a></li>
                                        <li><a href="#1div-8">Proin dolor</a></li>
                                    </ul>
                                </div>
                            </div>
                            <div class="yui-u">
                                <div class="content">
                                    <text style="float:top">Siste nyheter</text>
                                    <?php echo $_smarty_tpl->getSubTemplate ("matchobserver/match.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

                                </div>
                            </div>
                            <div class="yui-u">
                                <div class="content"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="yui-b">
                    <div id="secondary"></div>
                </div>
            </div>
            <div id="ft">
                <div id="footer"></div>
            </div>
        </div>
    </body>
</html>
<?php }} ?>