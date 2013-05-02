<html>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <head>
        
        <script type="text/javascript" src="js/jquery-1.8.2.js"></script>  
        <script type="text/javascript" src="js/jquery.tablesorter.js"></script> 
        <script type="text/javascript" src="js/jMenu.jquery.js"></script>
        <script type="text/javascript" src="js/jquery-breadcrumbs.js"></script>
        <script type="text/javascript" src="js/jquery.sparkline.js"></script>
        <script type="text/javascript" src="js/jquery.stalker.js"></script>
        <script type="text/javascript" src="js/spin.js"></script>
        <script type="text/javascript" src="js/jquery.eventCalendar.min.js"></script>
        <script type="text/javascript" src="js/flot-flot-8760ee7/jquery.flot.js"></script>
        <script type="text/javascript" src="js/flot-flot-8760ee7/jquery.flot.pie.js"></script>
        
        <script type="text/javascript" src="js/common.js"></script>
        
        <link href="favicon.ico" rel="icon" type="image/x-icon" />
        
        <link rel="stylesheet" href="css/smoothness/jquery-ui-1.9.2.custom.css" >
        
	<script src="js/jquery-ui-1.9.2.custom.js"></script>
    </head>
    <body>
        <div style="margin: 15px">
            <table>
                <tr>
                    <td>KampID:</td>
                    <td> <input id="matchid" type="text"></input></td>
                </tr>
                <tr>
                    <td>LigaID:</td>
                    <td> <input id="leagueid" type="text"></input></td>
                </tr>
            </table>
            <input type="button" value="Hent kampinfo" onclick="getMatchInfo()" </input>           
        </div>
    </body>
</html>