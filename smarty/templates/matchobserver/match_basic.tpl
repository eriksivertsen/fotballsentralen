<div id="match_basic_div">
    <ul>
        <li><a href="#info">Kampinfo</a></li>
        <li><a href="#odds">Odds</a></li>      
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
                        <text id="hometeam_name"></text>
                    </td>
                </tr>
            </thead>
            <tbody id="hometeam_body">

            </tbody>
            <thead>
                <tr>
                    <td colspan="2">
                        <text id="awayteam_name"></text>
                    </td>
                </tr>
            </thead>
            <tbody id="awayteam_body">

            </tbody>
        </table>
    </div>
    <div id="odds">
        {include file='matchobserver/odds.tpl'}
    </div>
</div>