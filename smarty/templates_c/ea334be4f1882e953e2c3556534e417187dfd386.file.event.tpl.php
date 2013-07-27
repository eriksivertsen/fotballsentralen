<?php /* Smarty version Smarty-3.1.12, created on 2012-11-23 17:32:13
         compiled from "smarty\templates\event.tpl" */ ?>
<?php /*%%SmartyHeaderCode:778550ae6442788d83-79203864%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'ea334be4f1882e953e2c3556534e417187dfd386' => 
    array (
      0 => 'smarty\\templates\\event.tpl',
      1 => 1353691839,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '778550ae6442788d83-79203864',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50ae644278a923_72749790',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50ae644278a923_72749790')) {function content_50ae644278a923_72749790($_smarty_tpl) {?><html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <head>
        <link rel="stylesheet" href="css/style.css" type="text/css"/>
        <script type="text/javascript" src="js/jquery-1.8.2.js"></script>  
        <script type="text/javascript" src="js/jquery.tablesorter.js"></script> 
        <script type="text/javascript" src="js/jquery-ui.js"></script>
        <script type="text/javascript" src="js/jMenu.jquery.js"></script>
        <script type="text/javascript" src="js/common.js"></script>
        <script type="text/javascript">       
            
            $(document).ready(function() {
                var eventid = <?php echo $_GET['eventid'];?>
;
                getEventsFromDB(0, 0, eventid, $('#events'), getEventFromId(eventid));
            });
         
        </script> 
        <title>Norge - Fotballstatistikker</title>
    </head>
    <body>
    <center>
        <div class="indexbody">
            <table style="display: inline;" id="events" class="tablesorter"> </table>
        </div>
    </center>
</body>
</html><?php }} ?>