<html>
    <head>
        <title>Tipsway</title>
        <meta charset="utf-8">
        <link rel="stylesheet" type="text/css" href="css/tipsway/styles.css">
        <link rel="stylesheet" href="css/tipsway/tipsway.css">

        <script type="text/javascript" src="http://code.jquery.com/jquery-latest.min.js"></script>
        <script type="text/javascript" src="js/matchobserver.js"></script>
        <script type="text/javascript" src="js/admin.js"></script>
        <script type="text/javascript" src="js/overlib/overlib.js"></script>
        <script type="text/javascript" src="js/Utils.js"></script>
        <script type="text/javascript" src="js/jquery-ui.js"></script>  
        <script type="text/javascript" src="js/spin.js"></script> 
        <script type="text/javascript" src="js/expander/jquery.expander.js"></script>
        <script type="text/javascript" src="js/readmore.js"></script>
        <script type="text/javascript" src="js/common.js"></script>  
        <script>
            $(document).ready(function() {
                $('#match_period').tabs();
                $('#match_basic_div').tabs();
                $('#settings').tabs();
                $('[id^="slider_"]').slider();
                getInfo();
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
        </script>
    </head>
    <body>
        <div id="loader" class="loader"></div>
        <div id="doc3" class="yui-t7">
            <!--
            <div id="hd">
                <div id="header">
                    <div id="info" style="display: inline-block;">
                        <center>
                        <h1 id="header_match"></h1>
                        </center>
                    </div>
                </div>
            </div>-->
            <div id="bd">
                <div id="yui-main">
                    <div class="yui-b">
                        <div class="yui-gb">
                            <div class="yui-u first">
                                <div class="content">
                                    <text class="content-header">Kampliste</text>
                                    {include file="matchobserver/matchlist.tpl"}
                                </div>
                            </div>
                            <div class="yui-u">
                                <div class="content">
                                    <text class="content-header">Informasjon</text>
                                    {include file="matchobserver/match_basic.tpl"}
                                </div>
                            </div>
                            <div class="yui-u">
                                <div class="content">
                                    <text class="content-header">Pinnacle Odds</text>
                                    {include file="matchobserver/odds.tpl"}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                                <!--
                <div class="yui-b">
                    <div id="secondary"></div>
                </div>
                                -->
                <div id="bd">
                    <div id="yui-main">
                        <div class="yui-b">
                            <div class="yui-gb">
                                <div class="yui-u first">
                                    <div class="content">
                                        <text class="content-header">Siste nyheter // </text> <text class="content-header" style="background-color:yellow">Inneholder mulig tropp </text>
                                        {include file="matchobserver/news.tpl"}
                                    </div>
                                </div>
                                <div class="yui-u">
                                    <div class="content">
                                        <text class="content-header">Mer info</text>
                                        {include file="matchobserver/news_detail.tpl"}
                                    </div>
                                </div>
                                <div class="yui-u">
                                    <div class="content">
                                        <text class="content-header">Innstillinger</text>
                                        {include file="matchobserver/settings.tpl"}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </body>
</html>
