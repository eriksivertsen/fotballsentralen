<div id="settings">
    <div id="loader_settings" class="loader"></div>
    <ul>
        <li><a href="#surface">Underlag</a></li>
        <li><a href="#derby">Derby</a></li>
        <li><a href="#players">Spillere</a></li>
        <li><a href="#warning">Vær</a></li>
        <li><a href="#mailsender">Mailsender</a></li>
    </ul>
    <div id="surface">
        <table id="surface_match" class="table">
            <thead>
                <tr>
                    <td>
                        Lag
                    </td>
                    <td>
                        Underlag
                    </td>
                    <td>
                        Tilstand
                    </td>
                </tr>
            </thead>
            <tbody id="surface_match_body">

            </tbody>
        </table>
    </div>
    <div id="derby">
        <table id="derby_table" class="table">
            <thead>
                <tr>
                    <td>
                        Lag
                    </td>
                    <td>
                        Lag
                    </td>
                    <td colspan="2">
                        Derbygrad
                    </td>
                </tr>
            </thead>
            <tbody id="derby_body">

            </tbody>
        </table>
    </div>
    <div id="players">
        <table id="players_table" class="table">
            <thead>
                <tr>
                    <td>
                        Navn
                    </td>
                    <td>
                        Lag
                    </td>
                    <td>
                        Key
                    </td>
                </tr>
            </thead>
            <tbody id="players_body">

            </tbody>
        </table>
    </div>
    <div id="warning">
        <table id="warning_table" class="table">
            <thead>
                <tr>
                    <td>
                        Beskrivelse
                    </td>
                    <td>
                        Kriterie
                    </td>
                </tr>
            </thead>
            <tbody id="warning_body">

            </tbody>
        </table>
    </div>
    <div id="mailsender">
        <table id="settings_table" class="table">
            <thead>
                <tr>
                    <td>Liga</td><td>Aktiv</td><td>Tropp hvert lag</td><td>Tropp begge lag</td><td>Lagoppstilling hvert lag</td><td>Lagoppstilling begge lag</td>
                </tr>
            </thead>
            <tr>
                <td>
                    Premier League
                </td>
                <td align="center">
                    <input id="100_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="100_squad_single" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="100_squad_double" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="100_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="100_lineup_double" type="checkbox" disabled="true"/>
                </td>
            </tr>
            <!--
            <tr>
                <td>
                Champions League (beta, engelske lag)
                </td>
                <td align="center">
                    <input id="200_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="200_squad_single" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="200_squad_double" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="200_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="200_lineup_double" type="checkbox" disabled="true"/>
                </td>
            </tr>
            <tr>
                <td>
                Europa League (beta, engelske lag)
                </td>
                <td align="center">
                    <input id="300_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="300_squad_single" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="300_squad_double" type="checkbox" disabled="true"/>
                </td>
                <td align="center">
                    <input id="300_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="300_lineup_double" type="checkbox" disabled="true"/>
                </td>
            </tr>
            -->
            <tr>
                <td>
                    Tippeligaen
                </td>
                <td align="center">
                    <input id="1_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="1_squad_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="1_squad_double" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="1_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="1_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td>
                    1.divisjon
                </td>
                <td align="center">
                    <input id="2_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="2_squad_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="2_squad_double" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="2_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="2_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td>
                    2.divisjon avdeling 1
                </td>
                <td align="center">
                    <input id="3_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="3_squad_single" type="checkbox" />
                </td>
                <td align="center">
                    <input id="3_squad_double" type="checkbox" />
                </td>
                <td align="center">
                    <input id="3_lineup_single" type="checkbox" />
                </td>
                <td align="center">
                    <input id="3_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td>
                    2.divisjon avdeling 2
                </td>
                <td align="center">
                    <input id="4_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="4_squad_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="4_squad_double" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="4_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="4_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td>
                    2.divisjon avdeling 3
                </td>
                <td align="center">
                    <input id="5_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="5_squad_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="5_squad_double" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="5_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="5_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td>
                    2.divisjon avdeling 4
                </td>
                <td align="center">
                    <input id="6_active" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="6_squad_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="6_squad_double" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="6_lineup_single" type="checkbox"/>
                </td>
                <td align="center">
                    <input id="6_lineup_double" type="checkbox"/>
                </td>
            </tr>
            <tr>
                <td colspan="6">
                    <input type="button" onclick="saveMailSettings()" value="Lagre"/>
                    <input type="button" onclick="changePassword()" value="Bytt passord"/>
                </td>
            </tr>
        </table>
        
    </div>
</div>