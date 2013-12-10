<head>
    <meta charset="utf-8">
    <title>MatchObserver</title>
    <link rel="stylesheet" type="text/css" href="css/admin.css"/>
    <link href="favicon.ico" rel="icon" type="image/x-icon" />
    <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
    <script type="text/javascript" src="js/admin.js"></script>
    
    <script type="text/javascript">
        
        $(document).ready(function() {
            getUsersLeague();
            $("input:checkbox").click(function() {
                if ($(this).is(":checked")) {
                    var group = "input:checkbox[name='" + $(this).attr("name") + "']";
                    $(group).prop("checked", false);
                    $(this).prop("checked", true);
                } else {
                    $(this).prop("checked", false);
                }
            });
        });
         
        function logout(){
            $.post('admin/logout.php');
            window.location.href = "admin.php";
        }
    </script>
</head>
<body>
    <div style="margin:50px">
        <table id="settings_table" class="settings">
            <tr>
                <td>Liga</td><td>Aktiv</td><td>Tropp Enkel</td><td>Tropp Dobbel</td><td>Lagoppstilling Enkel</td><td>Lagoppstilling Dobbel</td>
            </tr>
            <tr>
                <td>
                Premier League (beta)
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
                Adeccoligaen
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
        </table>
        <input type="button" onclick="saveSettings()" value="Lagre" style="margin:7px"/>
        <input type="button" onclick="changePassword()" value="Bytt passord" style="margin:7px"/>
        <input type="button" onclick="logout()" value="Logg ut" style="margin:7px"/>
    </div>
</body>
</html>

