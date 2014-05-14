<?php /* Smarty version Smarty-3.1.12, created on 2014-05-11 11:27:12
         compiled from "smarty\templates\matchobserver\news_detail.tpl" */ ?>
<?php /*%%SmartyHeaderCode:16951535b5f6d47eba8-08425471%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '9dbf86197ac9c9f5502e94659d1b42ab1ae6fa34' => 
    array (
      0 => 'smarty\\templates\\matchobserver\\news_detail.tpl',
      1 => 1399807580,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '16951535b5f6d47eba8-08425471',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_535b5f6d481e00_24768905',
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_535b5f6d481e00_24768905')) {function content_535b5f6d481e00_24768905($_smarty_tpl) {?><div id="team_detail">
    <ul>
        <li><a href="#news_detail">Nyhetssak</a></li>
        <li><a href="#hometeam">Hjemmelag lagoppstilling</a></li>
        <li><a href="#hometeam_squad">Hjemmelag tropp</a></li>
        <li><a href="#awayteam">Bortelag lagoppstilling</a></li>
        <li><a href="#awayteam_squad">Bortelag tropp</a></li>
        <li><a href="#referee">Dommerinfo</a></li>
    </ul>
    <div id="news_detail">
        <h2>
            <a id="news_header" style="margin-left: 10px;display:block"></a>
        </h2>
        <input id="source_button" type="button" value="Sett som kilde" style="font-size:0.9em;margin-left:4px"></input>
        <text id="news_text" style="padding: 10px;font-size: 14px;display: block">

        </text>
    </div>
    <div id="hometeam">
        Kilde: <a id="hometeam_source"></a>
        <text id="hometeam_text" hidden=hidden>Lag ikke klart enda!</text>
        <table id="hometeam_basic" class="table">
            <thead>
                <tr>
                    <td colspan="9">
                        <text id="hometeam_header"></text>
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
    <div id="hometeam_squad">
        Kilde: <a id="homesquad_source"></a><input id="homesquad_source_button" type="button" value="Fjern som kilde"></input>
        <br/>
        <text id="hometeam_text" hidden=hidden>Lag ikke klart enda!</text>
        <table id="hometeam_missing_basic" class="table">
            <thead>
                <tr>
                    <td colspan="9">
                        <text id="hometeam_missing_header"></text>
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
            <tbody id="hometeam_missing_body_team">

            </tbody>
        </table>
        <br/>
        <table id="hometeam_squad_basic" class="table">
            <thead>
                <tr>
                    <td colspan="9">
                        <text id="hometeam_squad_header"></text>
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
            <tbody id="hometeam_squad_body_team">

            </tbody>
        </table>
    </div>
    <div id="awayteam">
        <text id="awayteam_text" hidden=hidden>Lag ikke klart enda!</text>
        Kilde: <a id="awayteam_source"></a>
        <table id="awayteam_basic" class="table">
            <thead>
                <tr>
                    <td colspan="9">
                        <text id="awayteam_header"></text>
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
    <div id="awayteam_squad">
        <text id="awayteam_text" hidden=hidden>Lag ikke klart enda!</text>
        Kilde: <a id="awaysquad_source"></a><input id="awaysquad_source_button" type="button" value="Fjern som kilde"  style="font-size:0.9em;margin:3px"></input>
        <br/>
        <table id="awayteam_missing_basic" class="table">
            <thead>
                <tr>
                    <td colspan="9">
                        <text id="awayteam_missing_header"></text>
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
            <tbody id="awayteam_missing_body_team">

            </tbody>
        </table>
        <br/>
        <table id="awayteam_squad_basic" class="table">
            <thead>
                <tr>
                    <td colspan="9">
                        <text id="awayteam_squad_header"></text>
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
            <tbody id="awayteam_squad_body_team">

            </tbody>
        </table>
    </div>
    <div id="referee">
        
        <text id="referee_yellow"></text>
        <br/>
        <text id="referee_red"></text>
        
        <table id="referee_table" class="table">
           
        </table>
    </div>
</div><?php }} ?>