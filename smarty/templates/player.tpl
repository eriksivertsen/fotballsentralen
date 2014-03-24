<img id="player_logo" style="margin-left:15px;margin-right: 15px; float: left; vertical-align: middle;">
<table id="player_table" style="font-size: 9pt;">
        <thead>
            <td><h4><text id="playername"></text> <text id="player_number"></text></h4></td>
        </thead>
        <tr>
            <td>Spilletid <text id="player_playingminutes_year"></text>:</td>
            <td><text id="player_playingminutes"></text></td>
        </tr>

        <tr>
            <td>Seiersprosent med:</td>
            <td><text id="player_winpercentage"></text></td>
        </tr>
        <tr>
            <td><text id="player_totalgoals_text"></td>
            <td><text id="player_totalgoals"></text></td>
        </tr>
        <tr>
            <td>Født:</td>
            <td><text id="player_dateofbirth"></text></td>
        </tr>
        <tr>
            <td>Høyde:</td>
            <td><text id="player_height"></text></td>
        </tr>
        <tr>
            <td>Primærposisjon:</td>
            <td><text id="player_position"></text></td>
        </tr>
        <tr>
            <td>Land:</td>
            <td><text id="player_country"></text></td>
        </tr>
        <tr>
            <td>Landskamper:</td>
            <td><text id="player_nationalmatches"></text></td>
        </tr>
        <tr>
            <td>Landslagsmål:</td>
            <td><text id="player_nationalgoals"></text></td>
        </tr>
        <tr>
            <td> </td>
            <td><text id=""></text></td>
        </tr>

    </table>
    <br/>
    <br/>
    <label id="player_label" class="selectlabel">
        <select id="teamSelect" style="margin: 20px" onchange="selectPlayerTeam()">
            <option value="0">Alle lag</option>
        </select>
    </label>
    <br/>
<table id="playerinfo" class="tablesorter playerinfo"></table>
<ul id="ranking" class="ranking" style="margin-left:15px;"></ul>
<br/>
<div id="similar">
    <center><h5>Lignende spillere:</h5>
    <text id="similarplayers" style="margin-left:20px;margin-right: 20px;font-size:9pt"></text><center>
</div>