<?php /* Smarty version Smarty-3.1.12, created on 2013-09-22 14:51:09
         compiled from "smarty\templates\infoindex.tpl" */ ?>
<?php /*%%SmartyHeaderCode:2783150ad1bad17d3d9-99298101%%*/if(!defined('SMARTY_DIR')) exit('no direct access allowed');
$_valid = $_smarty_tpl->decodeProperties(array (
  'file_dependency' => 
  array (
    '279da6dfa57648cbbd671987e62eec3cbf48ae9c' => 
    array (
      0 => 'smarty\\templates\\infoindex.tpl',
      1 => 1379861466,
      2 => 'file',
    ),
  ),
  'nocache_hash' => '2783150ad1bad17d3d9-99298101',
  'function' => 
  array (
  ),
  'version' => 'Smarty-3.1.12',
  'unifunc' => 'content_50ad1bad24f056_48494864',
  'variables' => 
  array (
    'page' => 0,
  ),
  'has_nocache_code' => false,
),false); /*/%%SmartyHeaderCode%%*/?>
<?php if ($_valid && !is_callable('content_50ad1bad24f056_48494864')) {function content_50ad1bad24f056_48494864($_smarty_tpl) {?><html>
    <head>     
        <title>FotballSentralen.com</title>
        <script type="text/javascript">
            
            !function(d,s,id){ var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){ js=d.createElement(s);js.id=id;js.src="https://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
            
            (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/nb_NO/all.js#xfbml=1";
        fjs.parentNode.insertBefore(js, fjs);
        }(document, 'script', 'facebook-jssdk'));
        
            $(document).ready(function() {
            
                $(window).on('hashchange', function() {
                   controlHash();
                });
            
                controlHash();
                
                //Hover arrows functions
                
                $('#previous').hover(function () {
                    this.src = 'images/arrow_prev_shadow.png';
                }, function () {
                    this.src = 'images/arrow_prev.png';
                });$('#next').hover(function () {
                    this.src = 'images/arrow_next_shadow.png';
                }, function () {
                    this.src = 'images/arrow_next.png';
                });
                
                
                
        })
        

        function submitFeedback(){
            var name = $('#feedback_name').val();
            var mail = $('#feedback_mail').val();
            var msg = $('#feedback_msg').val();
            var page = $('#feedback_page').val();
            var rating = $('input[name=group]:radio:checked').attr('value');
            
            if(name == '' || msg == ''){
                alert('Husk navn og kommentar!');
                return;
            }
            $.ajax({
                type: "POST",
                url: "receiver.php",
                dataType: "json",
                timeout: timeout,
                data: {
                    action: "submitFeedback",
                    name: name,
                    mail: mail,
                    msg: msg,
                    page: page,
                    rating: rating
                },
                error: function () {
                    stopLoad()
                },
                success: function() {
                    alert('Takk for tilbakemeldingen!');
                    closePopup();
                }
            });
            
        }
        // POPup functions:
        function showPopup(){
            $('#feedback_form').show('fast');
            $('#feedback_name').focus();
            $('#feedback_click').hide();
        }
        function closePopup(){
            $('#feedback_form').hide('fast');
            $('#feedback_click').show();
        }
  
        function controlHash()
        {
//            var str = window.location.href;
//            var indexOf = str.indexOf('index.php?');
//            var indexOfHash = str.indexOf('#');
//            if(indexOf != -1 && indexOfHash != -1){
//                var params = str.substring(indexOf+10,indexOfHash);
//                var array = params.split("&");
//                for(var val in array){
//                    var obj = array[val].split("=");
//                    type = obj[0].split("_")[0];
//                    id = obj[1];
//                }
//            }
            
            var paramArray =  window.location.hash.split("/");
            if(paramArray == ''){
                setSeason(2013);
                getTeam(0, 0);
                return;
            }
            setSeason(paramArray[1]);
            var type = paramArray[2];
            var id = paramArray[3];
            var specialid = paramArray[4];
                   
            if(type == 'player'){
                if(id != playeridselected || type != typeselected){
                    getPlayer(id);
                }
            }
            if(type == 'events'){
                if(id != eventselected || type != typeselected){
                    if(leagueidselected == '' || leagueidselected == undefined){
                        leagueidselected = 0;
                    }
                    getEventsTotal(id,leagueidselected);
                }
            }
            if(type == 'eventsteam'){
                if(id != eventselected || type != typeselected){
                    if(leagueidselected == '' || leagueidselected == undefined){
                        leagueidselected = 0;
                    }
                    getEventsTotalTeam(id,leagueidselected);
                }
            }
            if(type == 'team'){
                if(id != teamidselected || type != typeselected ){
                    getTeamInfo(id,0);
                }
            }
            if(type == 'league'){
                if(id != leagueidselected || type != typeselected){
                    getTeam(id,0);
                }
            }
            if(type == 'page'){
                if(id == 'populare'){
                    getPopulare();
                }
                else if(id == 'suspension'){
                    getSuspensionList(specialid);
                }
                else if(id == 'transfers'){
                    getTransfers();
                }                
                else if(id == 'preview'){
                    if(specialid != undefined){
                        getPreview(specialid);
                    }else{
                        getPreviewMatches();
                    }
                }
                else if(id == 'referee'){
                    if(specialid == undefined){
                        getReferee();
                    }else{
                        getRefereeId(specialid);
                    }
                }
                else if(id == 'match'){
                    getMatch(specialid);
                }
            }
        }
        
        </script> 
        
    </head>
    
    <body>
        <div id="fb-root"></div>
        <div id="overDiv" style="position:absolute; visibility:hidden; z-index:1000;"></div>
        <div id="loader" class="loader"></div>
        <?php if ($_smarty_tpl->tpl_vars['page']->value==''){?>
        <input id="next" type="image" src="images/arrow_next.png" style="position:absolute;bottom:35%;right:55px" title="Neste sesong" onclick="next()">
        <input id="previous" type="image" src="images/arrow_prev.png" style="position:absolute;bottom:35%;left:55px;" title="Forrige sesong" onclick="previous()">
        <?php }?>
        <div class="indexbody">
            <div id="feedback_form" hidden="true" align="right" style="font-size:9pt">
                <table style="font-size:9pt;">
                    <tr>
                       <td>Navn: <text style="color:red"> *</text></td> 
                       <td>
                           <input type="text" id="feedback_name"></input> 
                       </td>  
                    </tr>
                    <tr>
                        <td>Mail:</td>
                        <td>
                            <input type="text" title="Kun hvis du ønsker svar" id="feedback_mail"></input>  
                        </td>
                    </tr>
                    <tr>
                        <td>Gjelder side:</td>
                            <td>
                                <input type="text" id="feedback_page" disabled="true"><text ></text></input> 
                            </td>
                    </tr>
                    <tr>
                        <td>Kommentar:</td>
                        <td>
                            <textarea id="feedback_msg" style="width:240px;height:100px;resize: none;"></textarea> 
                        </td>
                    </tr>
                    <tr>
                        <td>
                            Rangering:
                        </td>
                        <td>    
                            <input type="radio" name="group" title="Håpløst" value="1" id="feedback_radio_1"></input>
                            <input type="radio" name="group" title="Dårlig" value="2" id="feedback_radio_2"></input>
                            <input type="radio" name="group" title="Middels" value="3" id="feedback_radio_3" checked></input>
                            <input type="radio" name="group" title="Bra" value="4" id="feedback_radio_4"></input>
                            <input type="radio" name="group" title="Strålende" value="5" id="feedback_radio_5"></input>
                        </td>
                    </tr>
                </table>
                <button id="feedback_close" onclick="closePopup();return false;">Lukk</button>
                <button id="feedback_send" onclick="submitFeedback();return false;">Send inn</button>
            </div>
            <div align="right" id="feedback_index" style="margin-right: 10px">
                <a href="#" id="feedback_click" style="font-size: 8pt" title="Hjelp FotballSentralen bli bedre!" onclick="showPopup();return false;">Feedback</a>
            </div>
            <div id="welcometext" style="font-size: 10pt; margin-left:16px;margin-right:20px; background-color: #8dbdd8 ">
                <b>Velkommen til FotballSentralen.com!</b>
                <br/>
                <br/>
                Denne nystartede siden samler offisielle kampfakta og resultater fra fotball.no, og sorterer dette slik at du 
                enkelt kan bla deg gjennom fotball-Norge! Kort sagt er dette en oversikt over spilleminutter, bytter, mål og kort for 
                ALLE spillere fra 2.divisjon og opp. Per dags dato ligger det statistikker fra 2011 (Tippeligaen og Adeccoligaen) 
                samt alle 2.divisjons-avdelingene fra 2012-sesongen.
                <br/>
                <br/>
                Siden er stadig under utvikling, og har du tips eller innspill tas de gjerne imot <a href="mailto:kontakt@fotballsentralen.com">her</a>.
                <br/>
                <br/>
                </div>
            
            <div id="eventoverview">
                <?php echo $_smarty_tpl->getSubTemplate ("events.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

            </div>
            <div id="team">
                <?php echo $_smarty_tpl->getSubTemplate ("team.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

            </div>
            <div id="player">
                <?php echo $_smarty_tpl->getSubTemplate ("player.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

            </div>
            <div id="events">
               <?php echo $_smarty_tpl->getSubTemplate ("allevents.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

            </div>
            <table id="populare" align="center" width="100%">
                <tr>
                    <td><table id="trending" class="tablesorter playerinfo" style="float:left; "></table></td>
                    <td><table id="popularePlayers" class="tablesorter playerinfo" style="float:left;"></table></td>
                    <td><table id="populareTeams" class="tablesorter playerinfo" style="float:left; "></table></td>
                </tr>
                <br/>
                <br/>
            </table>
            <div id="transfer_div">
                <text id="transfer_text" style="margin-left:20px"></text>
                <table id="transfer_table" align="center" width="100%">
                    <tr>
                        <td><table id="transfer_transfer" class="tablesorter playerinfo" style="float:left; "></table></td>
                    </tr>
                </table>
            </div>
            <div id="preview">
                <?php echo $_smarty_tpl->getSubTemplate ("preview.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

            </div>
            
            <div id="match_main">
                <?php echo $_smarty_tpl->getSubTemplate ("match.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, null, null, array(), 0);?>

            </div>
            
            <div id="referee">
                <table id="referee_table" class="tablesorter playerinfo"></table>
                <table id="referee_table_specific" class="tablesorter playerinfo"></table>
            </div>

            <div id="suspensionList">
                <br/>
                <select id="suspensionSelect" style="margin: 20px" onchange="selectSuspendedLeague()">
                    <option value="134365">Tippeligaen</option>
                    <option value="134367">Adeccoligaen</option>
                    <option value="134371">2.divisjon avd 1</option>
                    <option value="134372">2.divisjon avd 2</option>
                    <option value="134373">2.divisjon avd 3</option>
                    <option value="134374">2.divisjon avd 4</option>
                </select>
                <br/>
                <table id="suspensionTable" class="tablesorter" style="float: none; width:auto;"></table>
                <br/>
                <br/>
                <table id="suspensionTableDanger" class="tablesorter" style="float: none; width:auto;"></table>
                
            </div>
            <text id="lastupdate" style="font-size: 7pt;margin-left: 15px;" >Sist oppdatert: </text>
            <br/>
            <br/>
            <div style="margin-left:15px" id="social">
                <div class="fb-like" data-href="http://www.facebook.com/fotballsentral1" data-send="false" data-width="400" data-show-faces="true"></div>
                <br/>
                <a href="https://twitter.com/share" class="twitter-share-button" data-lang="en">Tweet</a>
            </div>
            <br/>
            <br/>
        </div>
    </body>
</html><?php }} ?>