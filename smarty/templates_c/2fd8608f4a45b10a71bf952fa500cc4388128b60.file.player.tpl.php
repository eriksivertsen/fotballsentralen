<?php /* Smarty version Smarty-3.1.12, created on 2012-11-22 17:13:23
         compiled from "smarty\templates\player.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2302350a8208dbc1742-06689911%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '2fd8608f4a45b10a71bf952fa500cc4388128b60' => 
    array (
      0 => 'smarty\\templates\\player.tpl',
      1 => 1353604168,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2302350a8208dbc1742-06689911',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50a8208dc0c425_23197720',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50a8208dc0c425_23197720')) {function content_50a8208dc0c425_23197720($_smarty_tpl) {?><html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <head>
        <link rel="stylesheet" href="css/style.css" type="text/css" media="screen" />
        <script type="text/javascript" src="js/jquery-1.8.2.js"></script>   
        <script type="text/javascript" src="js/jquery.tablesorter.js"></script> 
        <script type="text/javascript" src="js/jquery.metadata.js"></script> 
        <script type="text/javascript" src="js/jquery.tablesorter.min.js"></script> 
        <script type="text/javascript" src="js/jMenu.jquery.js"></script>
        <script type="text/javascript" src="js/common.js"></script> 
        <script type="text/javascript">       
            $(document).ready(function() {
                var playerid = <?php echo $_GET['playerid'];?>
;
                setPlayerHit(playerid);
                $.ajax({
                    type: "POST",
                    url: "receiver.php",
                    dataType: "json",
                    data: { action: "getPlayerInfo", playerid: playerid },
                    success: function(json) {
                        $('#playerinfo').empty();
                        var array = json;
                        
                        $('#playerinfo').append('<thead><th>Dato</th><th>Hjemmelag</th><th>Bortelag</th><th>Resultat</th><th>Mål</th><th>Straffemål</th><th>Gule kort</th><th>Røde kort</th><th>Fra start</th><th>Spilleminutter</th><th>Byttet inn</th><th>Byttet ut</th></thead>');
                        $('#playerinfo').append('<tbody>');
                        
                        
                        for (var i=0; i<array.length; i++) {
                            $('#playerinfo').append('<tr><td>'+array[i].dateofmatch+'</td>'+
                                '<td><a href="index.php?teamid='+array[i].homeid+'">'+array[i].hometeamname+'</a></td>'+
                                //'<td>-</td>'+
                                '<td><a href="index.php?teamid='+array[i].awayid+'">'+array[i].awayteamname+'</a></td>'+
                                '<td>'+array[i].result+'</td>'+
                                '<td>'+array[i].goals+'</td>'+
                                '<td>'+array[i].penalty+'</td>'+
                                '<td>'+array[i].yellowcards+'</td>'+
                                '<td>'+array[i].redcards+'</td>'+
                                '<td>'+array[i].start+'</td>'+
                                '<td>'+array[i].minutesplayed+'</td>'+
                                '<td>'+array[i].subbedin+'</td>'+
                                '<td>'+array[i].subbedoff+'</td>'+
                                '</tr>');
                        }
                        $('#playerinfo').append('</tbody>');
                        $("#playerinfo").tablesorter(); 
                    }
                }); 
            });
        
        </script> 
        <title>Norge - Spillerinfo</title>
    </head>
    <body>
        <div class="indexbody">
            <table id="playerinfo" class="tablesorter" style="display: inline; width:90%;"></table>
        </div>
    </body>
</html><?php }} ?>