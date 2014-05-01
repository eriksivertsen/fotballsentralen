
<table style="width:100%">
    <tr>
        <td>
            <img id="futsal_player_logo" style="margin-left:15px;margin-right: 15px; float: left; vertical-align: middle;"/>
        </td>
        <td style="width: 40%;vertical-align: top;">
            <h3><text id="futsal_player_name"></text></h3>
            <table id="futsal_player_table" style="font-size: 12px;">
                               
                <tr>
                    <td>Mål:</td>
                    <td><text id="futsal_player_totalgoals"></text></td>
                </tr>
                <tr>
                    <td>Gule kort:</td>
                    <td><text id="futsal_player_yellowcard"></text></td>
                </tr>
                <tr>
                    <td>Røde kort:</td>
                    <td><text id="futsal_player_redcard"></text></td>
                </tr>
                <tr>
                    <td>Ekstra:</td>
                    <td><text id="futsal_player_extra_1"></text></td>
                </tr>
                <tr>
                    <td></td>
                    <td><text id="futsal_player_extra_2"></text></td>
                </tr>
                <tr>
                    <td></td>
                    <td><text id="futsal_player_extra_3"></text></td>
                </tr>
            </table>
        </td>
        <td style="width:35%">
        </td>
    </tr>
</table>
<br/>
<br/>
<br/>
<br/>
<label id="futsal_player_label" class="selectlabel">
    <select id="futsal_player_teamselect" style="margin: 20px" onchange="selectFutsalPlayerTeam()"></select>
</label>
<br/>
<text id="futsal_player_nb" style="font-size:12px;margin-left:15px">NB: Kun kamper med hendelser (mål eller kort) finnes på denne listen. Desverre ingen tropp/startoppstilling for futsal.</text>
<br/>
<br/>
<table id="futsal_player_info" class="tablesorter playerinfo"></table>