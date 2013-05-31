<table id="league_table" style="font-size: 9pt; margin-left:16px;">
        <tr>
            <td>Liga: </td> 
            <td><b><text id="league_name"></text></b></td> 
        </tr>
        <tr>
            <td>Toppscorer: </td> 
            <td><text id="league_topscorer"></text></td> 
        </tr>
            <!--
        <tr>
            <td>Formlag: </td>
            <td><text id="league_formteam"></text></td> 
        </tr>
            -->
        <tr>
            <td>Beste hjemmelag: </td> 
            <td><text id="league_hometeam"></text></td> 
        </tr>
        <tr>
            <td>Beste bortelag: </td> 
            <td><text id="league_awayteam"></text></td> 
        </tr>
    </table>
    <br/>

<div align="center">
    <div id="mins" class="category">
        <div class="categoryheader" style="margin-top: 5px">Spilletid</div>
        <table id="playingminutes" class="tablesorter"></table>
        <table id="subsout" class="tablesorter"> </table>
        <table id="subsin" class="tablesorter"> </table>
    </div>

    <div id="goal" class="category">
        <div class="categoryheader" style="margin-top: 5px">Mål</div>
        <!--<table id="totalgoals" class="tablesorter"> </table>-->
        <table id="goals" class="tablesorter"> </table>
        <table id="penalty" class="tablesorter"> </table>
        <table id="owngoal" class="tablesorter"> </table>
    </div>

    <div id="dicipline" class="category">
        <div class="categoryheader" style="margin-top: 5px">Disiplin</div>
        <table id="yellowcard" class="tablesorter"> </table>
        <table id="yellow_red" class="tablesorter"> </table>
        <table id="redcard" class="tablesorter"> </table>
    </div>

    <div id="overall" class="category">
        <div class="categoryheader" style="margin-top: 5px">Tabeller</div>
        
        <table id="leaguetable" class="tablesorter playerinfo" style="display: inline-table;"> </table>
        <table id="leaguetablehome" class="tablesorter playerinfo" style="display: inline-table;"> </table>
        <table id="leaguetableaway" class="tablesorter playerinfo" style="display: inline-table;"> </table>
    </div>
</div>