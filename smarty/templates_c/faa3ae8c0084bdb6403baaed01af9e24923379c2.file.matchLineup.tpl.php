<?php /* Smarty version Smarty-3.1.12, created on 2013-04-15 20:32:21
         compiled from "smarty\templates\matchLineup.tpl" */ ?>
<?php /*%%SmartyHeaderCode:13051516c624bd010c2-80222583%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'faa3ae8c0084bdb6403baaed01af9e24923379c2' => 
    array (
      0 => 'smarty\\templates\\matchLineup.tpl',
      1 => 1366057940,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '13051516c624bd010c2-80222583',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_516c624c107693_91614336',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_516c624c107693_91614336')) {function content_516c624c107693_91614336($_smarty_tpl) {?><html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <head>
        
        <script type="text/javascript" src="js/jquery-1.8.2.js"></script>  
        <script type="text/javascript" src="js/jquery.tablesorter.js"></script> 
        <script type="text/javascript" src="js/jMenu.jquery.js"></script>
        <script type="text/javascript" src="js/jquery-breadcrumbs.js"></script>
        <script type="text/javascript" src="js/jquery.sparkline.js"></script>
        <script type="text/javascript" src="js/jquery.stalker.js"></script>
        <script type="text/javascript" src="js/spin.js"></script>
        <script type="text/javascript" src="js/jquery.eventCalendar.min.js"></script>
        <script type="text/javascript" src="js/flot-flot-8760ee7/jquery.flot.js"></script>
        <script type="text/javascript" src="js/flot-flot-8760ee7/jquery.flot.pie.js"></script>
        
        <script type="text/javascript" src="js/common.js"></script>
        
        <link href="favicon.ico" rel="icon" type="image/x-icon" />
        
        <link rel="stylesheet" href="css/smoothness/jquery-ui-1.9.2.custom.css" >
        
	<script src="js/jquery-ui-1.9.2.custom.js"></script>
    </head>
    <body>
        <div style="margin: 15px">
            <table>
                <tr>
                    <td>KampID:</td>
                    <td> <input id="matchid" type="text"></input></td>
                </tr>
                <tr>
                    <td>LigaID:</td>
                    <td> <input id="leagueid" type="text"></input></td>
                </tr>
            </table>
            <input type="button" value="Hent kampinfo" onclick="getMatchInfo()" </input>           
        </div>
    </body>
</html><?php }} ?>