<?php /* Smarty version Smarty-3.1.12, created on 2014-04-05 11:05:44
         compiled from "smarty\templates\admin.tpl" */ ?>
<?php /*%%SmartyHeaderCode:313885242e61f351b06-27550423%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'a8a74b0a098e0da7d00b2eda68d1edb9bc71ff10' => 
    array (
      0 => 'smarty\\templates\\admin.tpl',
      1 => 1396695912,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '313885242e61f351b06-27550423',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_5242e61f381655_85793561',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_5242e61f381655_85793561')) {function content_5242e61f381655_85793561($_smarty_tpl) {?><head>
    <meta charset="utf-8">
    <title>MatchObserver</title>
    <link rel="stylesheet" type="text/css" href="css/admin.css"/>
    <link href="favicon.ico" rel="icon" type="image/x-icon" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/admin.js"></script>
    
    <script type="text/javascript">
        
        $(document).ready(function() {
            getUsersLeague();
            $("input:checkbox").click(function() {
                if ($(this).is(":checked")) {
                    var group = "input:checkbox[name='" + $(this).attr("name") + "']";
                    $(group).prop("checked", false);
                    $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", false);
                }
            });
        });
         
        function logout(){
            $.post('admin/logout.php');
            window.location.href = "admin.php";
        }
    </script>
</head>
<body>
    <div style="margin:50px">
        <table id="settings_table" class="settings">
            <tr>
                <td>Liga</td><td>Aktiv</td><td>Tropp hvert lag</td><td>Tropp begge lag</td><td>Lagoppstilling hvert lag</td><td>Lagoppstilling begge lag</td>
            </tr>
            
            <tr>
                <td>
                Premier League
                </td>
                <td align="center">
                    <input id="100_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="100_squad_single" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="100_squad_double" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="100_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="100_lineup_double" type="checkbox" disabled="true"/>
                </td>
            </tr>
            <!--
            <tr>
                <td>
                Champions League (beta, engelske lag)
                </td>
                <td align="center">
                    <input id="200_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="200_squad_single" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="200_squad_double" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="200_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="200_lineup_double" type="checkbox" disabled="true"/>
                </td>
            </tr>
            <tr>
                <td>
                Europa League (beta, engelske lag)
                </td>
                <td align="center">
                    <input id="300_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="300_squad_single" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="300_squad_double" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="300_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="300_lineup_double" type="checkbox" disabled="true"/>
                </td>
            </tr>
            -->
            <tr>
                <td>
                Tippeligaen
                </td>
                <td align="center">
                    <input id="1_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="1_squad_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="1_squad_double" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="1_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="1_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td>
                Adeccoligaen
                </td>
                <td align="center">
                    <input id="2_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="2_squad_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="2_squad_double" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="2_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="2_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td>
                2.divisjon avdeling 1
                </td>
                <td align="center">
                    <input id="3_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="3_squad_single" type="checkbox" />
                </td>
                <td align="center">
                    <input id="3_squad_double" type="checkbox" />
                </td>
                <td align="center">
                    <input id="3_lineup_single" type="checkbox" />
                </td>
                <td align="center">
                    <input id="3_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td>
                2.divisjon avdeling 2
                </td>
                <td align="center">
                    <input id="4_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="4_squad_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="4_squad_double" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="4_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="4_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td>
                2.divisjon avdeling 3
                </td>
                <td align="center">
                    <input id="5_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="5_squad_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="5_squad_double" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="5_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="5_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td>
                2.divisjon avdeling 4
                </td>
                <td align="center">
                    <input id="6_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="6_squad_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="6_squad_double" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="6_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="6_lineup_double" type="checkbox"/>
                </td>
            </tr>
        </table>
        <input type="button" onclick="saveSettings()" value="Lagre" style="margin:7px"/>
        <input type="button" onclick="changePassword()" value="Bytt passord" style="margin:7px"/>
        <input type="button" onclick="logout()" value="Logg ut" style="margin:7px"/>
    </div>
</body>
</html>

<?php }} ?>