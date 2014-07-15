<?php /* Smarty version Smarty-3.1.12, created on 2014-07-15 18:41:21
         compiled from "smarty\templates\matchobserver\settings.tpl" */ ?>
<?php /*%%SmartyHeaderCode:194595358045554edf8-39777332%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '0ee520d9f42686cb7346bf8caef817acb263e321' => 
    array (
      0 => 'smarty\\templates\\matchobserver\\settings.tpl',
      1 => 1405449680,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '194595358045554edf8-39777332',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535804555596e1_01165225',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535804555596e1_01165225')) {function content_535804555596e1_01165225($_smarty_tpl) {?><html>
    <head>
        <title>Tipsway</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/tipsway/styles.css">
        <link rel="stylesheet" href="css/tipsway/tipsway.css">

        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script type="text/javascript" src="js/matchobserver_settings.js"></script>
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
            $('#settings').tabs();
            getSettings();
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
        </script>
    </head>
    <body>
        <a style="color: white" href="admin.php">Tilbake</a>
        <div id="settings" style="background-color: #F8F8F8 ">
            <div id="loader_settings" class="loader"></div>
            <ul>
                <li><a href="#warning">Varsler</a></li>
                <li><a href="#surface">Underlag</a></li>
                <li><a href="#derby">Derby</a></li>
                <li><a href="#players">Spillere</a></li>
            </ul>
            <div id="surface">
                <div id="select" style="font-size: 14px;text-align: left;margin:5px; ">
                    <select id="surface_league" onchange="selectSurfaceLeague()">
                        <option value="0" selected="true">Velg liga</option>
                        <option value="1">Tippeligaen</option>
                        <option value="2">1.divisjon</option>
                    </select>
                </div>


                <table id="surface_match" class="table auto">
                    <thead>
                        <tr>
                            <td>
                                Lag
                            </td>
                            <td>
                                Underlag
                            </td>
                            <td>
                                Tilstand
                            </td>
                        </tr>
                    </thead>
                    <tbody id="surface_match_body">

                    </tbody>
                </table>
            </div>
            <div id="derby">
                <table id="derby_table" class="table auto">
                    <thead>
                        <tr>
                            <td>
                                Lag
                            </td>
                            <td>
                                Lag
                            </td>
                            <td colspan="2">
                                Derbygrad
                            </td>
                        </tr>
                    </thead>
                    <tbody id="derby_body">

                    </tbody>
                </table>
            </div>
            <div id="players">

                <div id="select" style="font-size: 14px;text-align: left;margin:5px; ">
                    <select id="players_league" onchange="selectPlayerLeague()">
                        <option value="0" selected="true">Velg liga</option>
                        <option value="1">Tippeligaen</option>
                        <option value="2">1.divisjon</option>
                    </select>

                    <select id="league_teams" onchange="selectTeam()"></select>

                </div>


                <table id="players_table" class="table auto">
                    <thead>
                        <tr>
                            <td>
                                Navn
                            </td>
                            <td>
                                Lag
                            </td>
                            <td>
                                Key
                            </td>
                        </tr>
                    </thead>
                    <tbody id="players_body">

                    </tbody>
                </table>
            </div>
            <div id="warning">
                <table id="warning_table" class="table auto">
                    <thead>
                        <tr>
                            <td>
                                Beskrivelse
                            </td>
                            <td>
                                Kriterie
                            </td>
                        </tr>
                    </thead>
                    <tbody id="warning_body">

                    </tbody>
                </table>
            </div>
            <!--
            <div id="mailsender">
                <table id="settings_table" class="table auto">
                    <thead>
                        <tr>
                            <td>Liga</td><td>Aktiv</td><td>Tropp hvert lag</td><td>Tropp begge lag</td><td>Lagoppstilling hvert lag</td><td>Lagoppstilling begge lag</td>
                        </tr>
                    </thead>
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
                            1.divisjon
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
                    <tr>
                        <td colspan="6">
                            <input type="button" onclick="saveMailSettings()" value="Lagre"/>
                            <input type="button" onclick="changePassword()" value="Bytt passord"/>
                        </td>
                    </tr>
                   
                </table>
            </div>
             -->
        </div>
    </body>
</html>



<?php }} ?>