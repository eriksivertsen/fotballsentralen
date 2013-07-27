 <center>
    <div id="radio">
        <input id="teamradio" type="radio" value="team" name="type" onclick="getEventsTotalTeamSelected()"></input>
        <label for="teamradio"><text style="font-size:10pt">Lag</text></label>
        <input id="playerradio" type="radio" value="player" name="type" onclick="getEventsTotalSelected()"></input>
        <label for="playerradio"><text style="font-size:10pt">Spillere</text></label>
    </div>
    <div style="text-align: -moz-center">
        <table id="allEvents" class="tablesorter" style="width:auto;table-layout: fixed;"></table>
    </div>
</center>