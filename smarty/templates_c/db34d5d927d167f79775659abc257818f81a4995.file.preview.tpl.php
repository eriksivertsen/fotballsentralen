<?php /* Smarty version Smarty-3.1.12, created on 2013-05-27 12:03:12
         compiled from "smarty\templates\preview.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1765751963eaa16a358-42013132%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'db34d5d927d167f79775659abc257818f81a4995' => 
    array (
      0 => 'smarty\\templates\\preview.tpl',
      1 => 1369656100,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '1765751963eaa16a358-42013132',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_51963eaa1728c8_10840550',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_51963eaa1728c8_10840550')) {function content_51963eaa1728c8_10840550($_smarty_tpl) {?><br/>
<ul id="preview_matches" class="ranking" style="margin-left:15px;"></ul>
    <center>
    <table id="preview_table" style="font-size: 9pt;">
        <tr>
            <td><center><h4><text id="preview_home_name"></text></center></h4>
            <img id="preview_home_logo" style="margin-left:15px;margin-right: 15px; float: bottom; vertical-align: middle;"></td>
            <td align="center"><b>mot</b></td>
            <td><h4><center><text id="preview_away_name"></text></center></h4>
            <img id="preview_away_logo" style="margin-left:15px;margin-right: 15px; float: bottom; vertical-align: middle;"></td>
        </tr>
        <tr>
            <td></td>
            <td align="center"><text id="preview_dateofmatch"></text></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td align="center"><text id="preview_officallink"></text></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td align="center"><text id="preview_referee"></text></td>
            <td></td>
        </tr>
        <tr>
            <td align="center"></td>
            <td align="center"><text id="preview_previous"></td>
            <td align="center"></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_form"></text></td>
            <td align="center">Hjemme/bortestatistikk</td>
            <td align="center"><text id="preview_away_form"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_lastfive"></text></td>
            <td align="center">Siste fem kamper</td>
            <td align="center"><text id="preview_away_lastfive"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_suspensions"></text></td>
            <td align="center">Suspensjoner</td>
            <td align="center"><text id="preview_away_suspensions"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_over3"></text></td>
            <td align="center">Over 2.5 mål</td>
            <td align="center"><text id="preview_away_over3"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_over4"></text></td>
            <td align="center">Over 3.5 mål</td>
            <td align="center"><text id="preview_away_over4"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_fsscore"></text></td>
            <td align="center">FS-score</td>
            <td align="center"><text id="preview_away_fsscore"></text></td>
        </tr>


    </table>
</center>
                <?php }} ?>