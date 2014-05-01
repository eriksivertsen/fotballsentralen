<?php /* Smarty version Smarty-3.1.12, created on 2014-04-30 12:03:08
         compiled from "smarty\templates\matchobserver\match_basic.tpl" */ ?>
<?php /*%%SmartyHeaderCode:30664535e127b33b897-16258245%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'd2b6e2a5363a78f665d0e7f50def4a187c1e122b' => 
    array (
      0 => 'smarty\\templates\\matchobserver\\match_basic.tpl',
      1 => 1398859346,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '30664535e127b33b897-16258245',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535e127b33fed1_73019349',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535e127b33fed1_73019349')) {function content_535e127b33fed1_73019349($_smarty_tpl) {?><div id="match_basic_div">
    <ul>
        <li><a href="#info">Kampinfo</a></li>
        <li><a href="#home">Laginfo H</a></li>
        <li><a href="#away">Laginfo B</a></li>

    </ul>
    <div id="info">
        <table id="match_basic" class="table">
            <thead>
                <tr>
                    <td colspan="2">
                        Kampinfo
                    </td>
                </tr>
            </thead>
            <tbody id="basic_body">

            </tbody>

            <thead>
                <tr>
                    <td colspan="2">
                        <text id="hometeam_name"></text>
                    </td>
                </tr>
            </thead>
            <tbody id="hometeam_body">

            </tbody>
            <thead>
                <tr>
                    <td colspan="2">
                        <text id="awayteam_name"></text>
                    </td>
                </tr>
            </thead>
            <tbody id="awayteam_body">

            </tbody>
        </table>
    </div>
    <div id="home">
        <text id="home_text" hidden=hidden>Lag ikke klart enda!</text>
        <table id="hometeam_basic" class="table">
            <thead>
                <tr>
                    <td colspan="9">
                        <text id="hometeam_name_team"></text>
                    </td>
                </tr>
            </thead>
            <thead>
                <tr>
                    <td>
                        Navn
                    </td>
                    <td>
                        Key
                    </td>
                    <td>
                        Startet sist
                    </td>
                    <td>
                        F.11'er
                    </td>
                    <td>
                        Siste 5
                    </td>
                    <td>
                        Start i år
                    </td>
                    <td>
                        Tropp i år
                    </td>
                    <td>
                        Status
                    </td>
                    <td>
                        Spilletid
                    </td>
                </tr>
            </thead>
            <tbody id="hometeam_body_team">

            </tbody>
        </table>
    </div>
    <div id="away">
        <text id="away_text" hidden=hidden>Lag ikke klart enda!</text>
        <table id="awayteam_basic" class="table">
            <thead>
                <tr>
                    <td colspan="9">
                        <text id="awayteam_name_team"></text>
                    </td>
                </tr>
            </thead>
            <thead>
                <tr>
                    <td>
                        Navn
                    </td>
                    <td>
                        Key
                    </td>
                    <td>
                        Startet sist
                    </td>
                    <td>
                        F.11'er
                    </td>
                    <td>
                        Siste 5
                    </td>
                    <td>
                        Start i år
                    </td>
                    <td>
                        Tropp i år
                    </td>
                    <td>
                        Status
                    </td>
                    <td>
                        Spilletid
                    </td>
                </tr>
            </thead>
            <tbody id="awayteam_body_team">

            </tbody>
        </table>
    </div>
</div><?php }} ?>