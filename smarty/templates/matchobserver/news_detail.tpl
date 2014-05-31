<div id="team_detail">
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
        <a id="hometeam_source"></a><input id="hometeam_source_button" type="button" value="Fjern som kilde" style="font-size:0.9em;margin:3px"></input>
        <div id="hometeam_input" style="font-size: 10pt">
            <textarea id="hometeam_input_textarea" style="width: 100%; height:30%"></textarea>
            <input id="hometeam_input_button" type="button" value="Sett streng som lagoppstilling" onclick="setTextAreaTeam('home')"></input>
        </div>
        <text id="hometeam_text">Kilde: </text>
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
        <div id="homesquad_input" style="font-size: 10pt">
            <textarea id="homesquad_input_textarea" style="width: 100%; height:30%"></textarea>
            <input id="homesquad_input_button" type="button" value="Sett streng som kilde" onclick="setTextAreaSource('home')"></input>
        </div>
        <text id="homesquad_text">Kilde: </text>
        <a id="homesquad_source"></a><input id="homesquad_source_button" type="button" value="Fjern som kilde" style="font-size:0.9em;margin:3px"></input>
        <br/>
        <table id="hometeam_missing_basic" class="table">
            <thead>
                <tr>
                    <td colspan="10">
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
                    <td>
                        Endre
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
                    <td colspan="10">
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
                    <td>
                        Fjern
                    </td>
                </tr>
            </thead>
            <tbody id="hometeam_squad_body_team">

            </tbody>
        </table>
    </div>
    <div id="awayteam">
        <text id="awayteam_text">Kilde:</text>
        <a id="awayteam_source"></a>
        <div id="awayteam_input" style="font-size: 10pt">
            <textarea id="awayteam_input_textarea" style="width: 100%; height:30%"></textarea>
            <input id="awayteam_input_button" type="button" value="Sett streng som lagoppstilling" onclick="setTextAreaTeam('away')"></input>
        </div>
        <a id="awayteam_source"></a><input id="awayteam_source_button" type="button" value="Fjern som kilde"  style="font-size:0.9em;margin:3px"></input>
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
        <div id="awaysquad_input" style="font-size: 10pt">
            <textarea id="awaysquad_input_textarea" style="width: 100%; height:30%"></textarea>
            <input id="awaysquad_input_button" type="button" value="Sett streng som kilde" onclick="setTextAreaSource('away')"></input>
        </div>
        <text id="awaysquad_text">Kilde:</text>
        <a id="awaysquad_source"></a><input id="awaysquad_source_button" type="button" value="Fjern som kilde"  style="font-size:0.9em;margin:3px"></input>
        <br/>
        <table id="awayteam_missing_basic" class="table">
            <thead>
                <tr>
                    <td colspan="10">
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
                    <td>
                        Endre
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
                    <td colspan="10">
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
                    <td>
                        Fjern
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
</div>