<div id="match_basic_div">
    <ul>
        <li><a href="#info">Kampinfo</a></li>
        <li><a href="#odds">Odds</a></li>     
        <li><a href="#odds_history">Odds History</a></li>   
    </ul>
    <div id="info">
        <table id="match_basic" class="table">
            <thead>
                <tr>
                    <td colspan="2">
                        Kampinfo
                    </td>
                </tr>
            </thead>
            <tbody id="basic_body">

            </tbody>

            <thead>
                <tr>
                    <td colspan="2">
                        <text id="basic_hometeam_name"></text>
                    </td>
                </tr>
            </thead>
            <tbody id="basic_hometeam_body">

            </tbody>
            <thead>
                <tr>
                    <td colspan="2">
                        <text id="basic_awayteam_name"></text>
                    </td>
                </tr>
            </thead>
            <tbody id="basic_awayteam_body">

            </tbody>
        </table>
    </div>
    <div id="odds">
        {include file='matchobserver/odds.tpl'}
    </div>
    <div id="odds_history">
        {include file='matchobserver/odds_history.tpl'}
    </div>
</div>