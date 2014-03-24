<?php /* Smarty version Smarty-3.1.12, created on 2014-03-22 00:18:32
         compiled from "smarty\templates\preview.tpl" */ ?>
<?php /*%%SmartyHeaderCode:1765751963eaa16a358-42013132%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    'db34d5d927d167f79775659abc257818f81a4995' => 
    array (
      0 => 'smarty\\templates\\preview.tpl',
      1 => 1395447509,
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
            <td></td>
            <td align="center"><text id="preview_warning"></text></td>
            <td></td>
        </tr>
        <tr>
            <td align="center"><h4><text id="preview_home_name"></text></h4>
            <img id="preview_home_logo" style="margin-left:15px;margin-right: 15px; float: bottom; vertical-align: middle;"></td>
            <td align="center"><b>mot</b></td>
            <td align="center"><h4><text id="preview_away_name"></text></h4>
            <img id="preview_away_logo" style="margin-left:15px;margin-right: 15px; float: bottom; vertical-align: middle;"></td>
        </tr>
        
        <tr></tr>
        <tr>
            <td align="center"><text id="preview_home_lastlineup"></text></td>
            <td align="center"><text id="preview_dateofmatch"></text></td>
            <td align="center"><text id="preview_away_lastlineup"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_lineup"></text></td>
            <td align="center"><text id="preview_officallink"></text></td>
            <td align="center"><text id="preview_away_lineup"></text></td>
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
            <td align="center"></td>
            <td align="center"><text id="preview_cardrating"></td>
            <td align="center"></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_surface"></text></td>
            <td align="center">Spiller på</td>
            <td align="center"><text id="preview_away_surface"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_form"></text></td>
            <td align="center">Statistikk (hjemme/borte)</td>
            <td align="center"><text id="preview_away_form"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_position"></text></td>
            <td align="center">Tabellposisjon (hjemme/borte)</td>
            <td align="center"><text id="preview_away_position"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_lastfive"></text></td>
            <td align="center">Siste fem kamper</td>
            <td align="center"><text id="preview_away_lastfive"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_lastfive_home"></text></td>
            <td align="center">Siste fem kamper (hjemme/borte)</td>
            <td align="center"><text id="preview_away_lastfive_away"></text></td>
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
            <td align="center"><text id="preview_home_over3ha"></text></td>
            <td align="center">Over 2.5 mål (hjemme/borte)</td>
            <td align="center"><text id="preview_away_over3ha"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_over4ha"></text></td>
            <td align="center">Over 3.5 mål (hjemme/borte)</td>
            <td align="center"><text id="preview_away_over4ha"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_goalsscored"></text></td>
            <td align="center">Mål scoret (hjemme/borte)</td>
            <td align="center"><text id="preview_away_goalsscored"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_conceded"></text></td>
            <td align="center">Mål sluppet inn (hjemme/borte)</td>
            <td align="center"><text id="preview_away_conceded"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_firsthalf_g"></text></td>
            <td align="center">Mål scoret første omgang</td>
            <td align="center"><text id="preview_away_firsthalf_g"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_firsthalf_c"></text></td>
            <td align="center">Mål sluppet inn første omgang</td>
            <td align="center"><text id="preview_away_firsthalf_c"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_secondhalf_g"></text></td>
            <td align="center">Mål scoret andre omgang</td>
            <td align="center"><text id="preview_away_secondhalf_g"></text></td>
        </tr>
        <tr>
            <td align="center"><text id="preview_home_secondhalf_c"></text></td>
            <td align="center">Mål sluppet inn andre omgang</td>
            <td align="center"><text id="preview_away_secondhalf_c"></text></td>
        </tr>
    </table>
    <iframe id="preview_weather" width="468" height="290" frameborder="0" style="margin: 10px 10px 10px 10px;" scrolling="no"></iframe>
</center>
                <?php }} ?>