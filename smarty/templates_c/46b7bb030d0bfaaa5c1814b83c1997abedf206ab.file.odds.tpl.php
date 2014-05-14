<?php /* Smarty version Smarty-3.1.12, created on 2014-05-09 22:23:38
         compiled from "smarty\templates\matchobserver\odds.tpl" */ ?>
<?php /*%%SmartyHeaderCode:500535398c5a01463-05785159%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '46b7bb030d0bfaaa5c1814b83c1997abedf206ab' => 
    array (
      0 => 'smarty\\templates\\matchobserver\\odds.tpl',
      1 => 1399674215,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '500535398c5a01463-05785159',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535398c5a0f994_31909172',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535398c5a0f994_31909172')) {function content_535398c5a0f994_31909172($_smarty_tpl) {?><div id="match_period">
    <table id="odds_match" class="table">
        <thead>
            <tr>
                <td>
                    1
                </td>
                <td>
                    X
                </td>
                <td colspan=2">
                    2
                </td>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <text id="match_home_price"></text>
                </td>
                <td>
                    <text id="match_draw_price"></text>
                </td>
                <td colspan="2">
                    <text id="match_away_price"></text>
                </td>
            </tr>
        </tbody>
        <thead>
            <tr>
                <td>
                    Homespread
                </td>
                <td>
                    Homeprice
                </td>
                <td>
                    Awayspread
                </td>
                <td>
                    Awayprice
                </td>
            </tr>
        </thead>
        <tbody id="spreadbody_match">
        </tbody>
        <thead>
            <tr>
                <td>
                    Points
                </td>
                <td>
                    Underprice
                </td>
                <td>
                    Overprice
                </td>
                <td>
                    &nbsp;
                </td>
            </tr>
        </thead>
        <tbody id="totalbody_match">
        </tbody>
    </table>
</div><?php }} ?>