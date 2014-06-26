<?php
// Template for showing yr.no's xml-forecast by Henkka, http://www.nordicweather.net
// Vers. Oct 16 2009
//
// Requirements:
// - PHP 5//*****************************************
// CONFIG START

// Units
$useC = 1;		// Use Celsius, else F
$useKMH = 0;		// Use Km/h
$useMPH = 0;		// Use Mph, if both KMH and MPH is zero is default m/s used
$useHPA = 1;		// Use hPa, else inHg
$useMM= 1;		// Use mm, else in

// Timesettings, normal PHP/date-tags used
$datestyle = "d.m.Y";		// Style of short date
$timestyle = "H:i";		// Style of time
$longdate = "d.m.Y @ H:i";	// Style of long time + date

// Cachesettings etc. settings
$cachedir = "cache/";		// Cachefolder, remember to create it!	
$qarefetchSeconds = 3600;	// How often it refresh the cache, 3600 recommended
$imgdir = "yricons/";	// Icons-folder

// Pulldown-settings
// This is the tricky part... :p
// To get right settings here go to yr.no and find city you want and take the URL of it, ex.
// http://www.yr.no/place/Finland/Western_Finland/Halikko/
// Then add the stuff after place/ from the url to the array, without last slash.

$citys = array(

'Finland/Western_Finland/Halikko',
'Finland/Laponia/Salla',
'Finland/Laponia/Kilpisjärvi',

);

$default = 2;		// Default forecast to show if no choosed
$WantPulldown = 1;	// Do you want the pulldown? If not is default forecast shown

// CONFIG END
//**********************************************

$includemode = 0; 
if(!preg_match('|yr_xml|', $_SERVER["PHP_SELF"])) { 
$includemode = 1; 
}

if(!$includemode) {
echo '
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Yr.no forecast</title>
<meta http-equiv="content-type" content="text/html; charset=ISO-8859-1" />
<style type="text/css">
body {background:#fff; color:#303030; font:72% verdana, sans-serif; margin:0; padding:0;}
</style>
<link type="text/css" rel="stylesheet" href="yrno_css.css" />
</head>'."\n".'<body>'."\n";
}

$g = $_GET[tl];
$g = preg_replace('|<[^>]+>|is','',$g);
if(!preg_match('|nordicweather|', $_SERVER["SERVER_NAME"])) { 
include 'yr_lang.php';
}


if((! is_numeric($g)) && (! in_array($g, $citys))) { $g = $default; }

$dir = strrchr($citys[$g], "/");
$dir = str_replace('/', "", $dir);
$qacacheName = ($cachedir.$dir.".xml"); 

$URL = ("http://www.yr.no/place/". $citys[$g]."/");  	// URL to the ENGLISH page on yr.no where location is

$URLS = $URL . 'varsel.xml';

$extlink = ' <img src="img/extlink.gif" alt="extlink" />';

// refresh cached copy of page if needed
// fetch/cache code by Tom at carterlake.org

if ($useC) { $uomtemp = "&deg;C"; } else { $uomtemp = "&deg;F"; }
if ($useKMH) { $uomwind = " km/h"; } else if ($useMPH) { $uomwind = " mph"; } else { $uomwind = " m/s";}
if ($useHPA) { $uombaro = " hPa"; } else { $uombaro = " inHg"; }
if ($useMM) { $uomrain = " mm"; } else { $uomrain = " in"; }

if (file_exists($qacacheName) and filemtime($qacacheName) + $qarefetchSeconds > time()) {
      $WhereLoaded = "from cachefile: $qacacheName";
      $html = implode('', file($qacacheName));
    } else {
      $WhereLoaded = "from URL: $URLS";
      $html = fetchqaUrlWithoutHanging($URLS);
      $fp = fopen($qacacheName, "w"); 
	  if($fp) {
	list($headers,$content) = explode("\r\n\r\n",$html);  // split headers from html
      	$i = strpos($html,"\r\n\r\n");
	  $headers = substr($html,0,$i-1);
	  $content = substr($html,$i+2);
	  //$content = parseToXML($content);
          $content = trim($content);
        $write = fputs($fp, $content); 
        fclose($fp);
	  } else {
	    print "<!--Unable to write cache $qacacheName -->\n";
	  }
	}

print "<!-- yr.no data load from $WhereLoaded -->\n";

if(strlen($html) < 50 ) {
  print "<!-- data not available -->\n";
  return;
}

$xml=xml2ary(file_get_contents($qacacheName));
$xml = $xml['weatherdata'][_c]; 

//print_r($xml);

$location = $xml[location];
$credit = $xml[credit];
$city = $location[_c][name][_v];
$country = $location[_c][country][_v];
$alt = $location[_c][location][_a][altitude];
$lat = $location[_c][location][_a][latitude];
$lon = $location[_c][location][_a][longitude];
$off = $location[_c][timezone][_a][utcoffsetMinutes];
$credit = $credit[_c][link][_a][text];


//echo "$lat $lon $off <br/>";

$offset = ($off / 60);
$zenith=90+33/60;
$sunri = date_sunrise(time(), SUNFUNCS_RET_STRING, $lat, $lon, $zenith, $offset);
$sunse = date_sunset(time(), SUNFUNCS_RET_STRING, $lat, $lon, $zenith, $offset);
$sunr = strtotime($sunri);
$suns = strtotime($sunse);

if($sunri == '') { $sunri = "--"; }
if($sunse == '') { $sunse = "--"; }
 
//echo "$offset $sunr $suns <br/>";

$meta = $xml[meta];
$lupd = $meta[_c][lastupdate][_v];
$lupd = date($longdate, parse_time($lupd));
$nupd = $meta[_c][nextupdate][_v];
$nupd = date($longdate, parse_time($nupd));

$forecasts = $xml[forecast][_c][tabular][_c][time];
$howmany = count($forecasts);

echo "<!-- $howmany forecasts available -->\n";
//echo "$city $country $alt $lupd $nupd <br/><br/>";

$tablehead = '<table class="yrtable" cellpadding="0" cellspacing="0"><tr style="background:url('.$imgdir.'back3.gif) repeat-x;">
<td class="yrrow tbl_header" style="text-align: left;"><b>' . DAY . '</b></td>
<td class="yrrow tbl_header"><b>&nbsp;</b></td>
<td class="yrrow tbl_header"><b>&nbsp;</b></td>
<td class="yrrow tbl_header"><b>' . TEMPE . '</b></td>
<td class="yrrow tbl_header"><b>' . PRECIP . '</b></td>
<td class="yrrow tbl_header"><b>' . BAROI . '</b></td>
<td class="yrrow tbl_header" style="text-align: left;"><b>' . WINDI . '</b></td>
<td class="yrrow tbl_header"><b>&nbsp;</b></td>
</tr>
';

$table = '
<table class="yrtable" cellpadding="0" cellspacing="0" style="border:0;"><tr>
<td class="yrrow" style="text-align: left; padding: 10px 15px 10px 15px; width: 60%;">
<h1 style="font-size: 18px;">' . FORECAST_FOR . ' ' . $city . ', ' . $country . '</h1>
</td>
<td class="yrrow" style="text-align: right; padding: 10px 15px 10px 15px; width: 40%;">
';

if ($WantPulldown) {
$table .= '<form action="" method="get">
 <fieldset>
 <legend>' . CHOOSECITY . '</legend>                
<select name="tl">';

$hoemany = count($citys);
for ($i = 0; $i < $hoemany; $i++) {
if($g == $i) {$s = 'selected="selected"';} else {$s = '';}
$dir = strrchr($citys[$i], "/");
$dir = str_replace('/', "", $dir);
$dir = str_replace('_', " ", $dir);
$table .= '<option value="' . $i . '" ' . $s . '>' . $dir . '</option>'."\n";
}
$table .= '</select>';
if(isset($lang)) {$table .= '<input type="hidden" name="lang" value="' . $lang .'" />';}
if(isset($sivu)) {$table .= '<input type="hidden" name="sivu" value="43" />';}
$table .= '        <input type="submit" value="submit" />
        </fieldset>

</form>
<br/><br/>';
}else{
$table .= '&nbsp;';
}

$table .= '</td>
</tr><tr>
<td class="yrrow" style="text-align: left; padding: 10px 15px 10px 15px; width: 60%;">
<b>' . LASTUPD . ':</b> ' . $lupd . '<br/>
<b>' . NEXTUPD . ':</b> ' . $nupd . '
</td>
<td class="yrrow" style="text-align: right; padding: 10px 15px 10px 15px; width: 40%;">
' . PROVIDED_BY . ': <a href="' . $URL . '" target="_new"><img src="'.$imgdir.'yrno.png" alt="22"/></a><br/>
</td></tr></table>
';
$table .= '<table class="yrtable" cellpadding="0" cellspacing="0">';


for ($i = 0; $i < $howmany; $i++) {

$validfr = $forecasts[$i][_a][from];
$validfrd = date($datestyle, parse_time($validfr));
$validfrt = date($timestyle, parse_time($validfr));
$validto = $forecasts[$i][_a][to];
$validtot = date($timestyle, parse_time($validto));
$period = $forecasts[$i][_a][period];
$period = $period + 1;

$icon = $forecasts[$i][_c][symbol][_a][number];

$micons = array('1', '2', '3', '5', '6', '7', '8');
$rh = date('H', parse_time($validto));
$sh = date('H', parse_time($validfr));

if ((date('H', $sunr)) > ($rh - 3)) {
$usunrise = 1;
} else if (((date('H', $suns)) < ($sh + 3)) && ($sh > 18)) {
$usunrise = 1;
} else {
$usunrise = 0;
}


if (($usunrise == 1) && (in_array($icon, $micons))) { 
$nicon = $icon . 'n.png'; 
} else {
$nicon = $icon . '.png'; 
}


$precip = $forecasts[$i][_c][precipitation][_a][value];
$dir = $forecasts[$i][_c][windDirection][_a][code];
$dirname = $forecasts[$i][_c][windDirection][_a][name];
$spd = $forecasts[$i][_c][windSpeed][_a][mps];
$spdname = $forecasts[$i][_c][windSpeed][_a][name];
$temp = $forecasts[$i][_c][temperature][_a][value];
$baro = $forecasts[$i][_c][pressure][_a][value];

if($temp <= 0) {
$tcolor = "below";
} else {
$tcolor = "over";
}

if (!$useC) { $temp = CtoF($temp); } 
if ($useMPH) { $spd = MStoMPH($spd); } 
if ($useKMH) { $spd = MStoKMH($spd); }
if (!$useHPA) { $baro = HPAtoIN($baro); } else {round($baro);}

if ($useMM) {
if(($precip > 0) && ($precip <= 1)) {
$prc = "< 1 mm";
} else if( $precip > 1) {
$prc = round($precip) . "  mm";
} else {
$prc = "&nbsp;";
}
} else {
$precip = mmToin($precip);
if(($precip > 0) && ($precip <= 0.01)) {
$prc = "< 0.01 in";
} else if( $precip > 0.01) {
$prc = $precip . "  in";
} else {
$prc = "&nbsp;";
}
}


$wind = windspeed($spdname) . ' ' . FROM . ' ' . winddirs(strtolower($dirname)) . '' . WEXT;

$curr = date('d.m.Y', time());

if($curr == $validfrd) {
$day = TODAY;
$ik = 0;
} else if(($i < 1) && ($validfrd <> $curr)) {
$day = TOMORROW;
$ik = 0;
} else {
$day = $validfrd;
$ik = 1;
}

$pr = parse_daytime($period);

if ($validfrd <> $vlc) {
$table .= '';
}

if (($validfrd <> $vlc) && ($i > 0) && ($i < 5)) {
$table .= '</table>
<br/>
<img src="' . $URL . 'avansert_meteogram.png" alt="meteogram" title="" height="295" width="810" />
<div><br/><h4>' . NEXTFOUR . '</h4></div>
' .$tablehead . '';
}

if (($validfrd <> $vlc) && ($plc == 4) && ($period ==3)) {
$table .= '</table>
<br/><h4>' . LASTDAYS . '</h4><br/>
' .$tablehead . '';
}

if($i % 2 == 1) {
$bgcolor = "#F7F7F7";
} else {
$bgcolor = "#E8E8E8";
}


if (($validfrd <> $vlc) && ($period == 3)) {
$table .= '
<tr>
<td class="yrrow_a" style="background-color: '.$bgcolor.';"><b>' . $day . '</b></td>
<td class="yrrow_a" style="background-color: '.$bgcolor.';">&nbsp;</td>
<td class="yrrow_a" style="background-color: '.$bgcolor.';"><img src="'.$imgdir.'' . $nicon . '" alt=""/></td>
<td class="yrrow_a" style="background-color: '.$bgcolor.';"><b><span class="' . $tcolor . '">' . $temp . $uomtemp . '</span></b></td>
<td class="yrrow_a" style="background-color: '.$bgcolor.';">' . parseTohtml($prc) . '</td>
<td class="yrrow_a" style="background-color: '.$bgcolor.';">' . $baro . $uombaro . '</td>
<td class="yrrow_a" style="text-align: left;background-color: '.$bgcolor.';">' . $wind . '</td>
<td class="yrrow_a" style="text-align: right;background-color: '.$bgcolor.';">' . round($spd) . $uomwind . '</td>
</tr>
';

} else if (($validfrd <> $vlc) && ($i == 0)) {

$table .= '
<tr style="background:url('.$imgdir.'back3.gif) repeat-x;">
<td width="100%" colspan="13" class="tbl_header">
<div style="padding: 1px 0 0 5px"><b>
' . $day . '
</b></div>
</td>
</tr>
<tr>
<td class="yrrow" style="background-color: '.$bgcolor.';">&nbsp;</td>
<td class="yrrow" style="background-color: '.$bgcolor.';">' . $validfrt . ' - ' . $validtot . '</td>
<td class="yrrow" style="background-color: '.$bgcolor.';"><img src="'.$imgdir.'' . $nicon . '" alt=""/></td>
<td class="yrrow" style="background-color: '.$bgcolor.';"><b><span class="' . $tcolor . '">' . $temp .  $uomtemp . '</span></b></td>
<td class="yrrow" style="background-color: '.$bgcolor.';">' . parseTohtml($prc) . '</td>
<td class="yrrow" style="background-color: '.$bgcolor.';">' . $baro . $uombaro . '</td>
<td class="yrrow" style="text-align: left;background-color: '.$bgcolor.';">' . $wind . '</td>
<td class="yrrow" style="text-align: right;background-color: '.$bgcolor.';">' . round($spd) . $uomwind . '</td>
</tr>
';

} else if (($validfrd <> $vlc)) {
$table .= '
<tr>
<td class="yrrow_a" style="background-color: '.$bgcolor.';"><b>' . $day . '</b></td>
<td class="yrrow_a" style="background-color: '.$bgcolor.';">' . $validfrt . ' - ' . $validtot . '</td>
<td class="yrrow_a" style="background-color: '.$bgcolor.';"><img src="'.$imgdir.'' . $nicon . '" alt=""/></td>
<td class="yrrow_a" style="background-color: '.$bgcolor.';"><b><span class="' . $tcolor . '">' . $temp . $uomtemp .  '</span></b></td>
<td class="yrrow_a" style="background-color: '.$bgcolor.';">' . parseTohtml($prc) . '</td>
<td class="yrrow_a" style="background-color: '.$bgcolor.';">' . $baro . $uombaro . '</td>
<td class="yrrow_a" style="text-align: left;background-color: '.$bgcolor.';">' . $wind . '</td>
<td class="yrrow_a" style="text-align: right;background-color: '.$bgcolor.';">' . round($spd) . $uomwind .'</td>
</tr>
';

} else {
$table .= '
<tr>
<td class="yrrow" style="background-color: '.$bgcolor.';">&nbsp;</td>
<td class="yrrow" style="background-color: '.$bgcolor.';">' . $validfrt . ' - ' . $validtot . '</td>
<td class="yrrow" style="background-color: '.$bgcolor.';"><img src="'.$imgdir.'' . $nicon . '" alt=""/></td>
<td class="yrrow" style="background-color: '.$bgcolor.';"><b><span class="' . $tcolor . '">' . $temp .  $uomtemp . '</span></b></td>
<td class="yrrow" style="background-color: '.$bgcolor.';">' . parseTohtml($prc) . '</td>
<td class="yrrow" style="background-color: '.$bgcolor.';">' . $baro . $uombaro . '</td>
<td class="yrrow" style="text-align: left;background-color: '.$bgcolor.';">' . $wind . '</td>
<td class="yrrow" style="text-align: right;background-color: '.$bgcolor.';">' . round($spd) . $uomwind . '</td>
</tr>
';
}


$plc = $period;
$vlc = $validfrd;
} 


 

$table .= '</table><div style="text-align: left;"><br/><small><a href="' . $URL . '" target="_new">' . $credit . '</a><br/>Script by <a href="http://www.nordicweather.net" target="_new">nordicweather.net</a></small></div>';
echo $table;

if(!$includemode) {
echo '
</body>
</html>
';
}

// ----------------------------functions ----------------------------------- 

function MStoMPH ($ms, $prec=0) {
	$prec = (integer)$prec;
	$mph = (float)(2.236936292 * $ms);
	return round($mph, $prec);
}

function mmToin ($mm) {
  $in = $mm * .0394;
  return sprintf("%01.2f",$in);
}

function MStoKMH ($ms, $prec=0) {
	$prec = (integer)$prec;
	$kmh = (float)(3.6 * $ms);
	return round($kmh, $prec);
}

function HPAtoIN ($baro) {
	//$prec = (integer)$prec;
	$ibaro = 0.0295333727 * $baro;
	return sprintf("%01.2f",$ibaro);
}

function CtoF ($cTemp, $prec=0) {
	$prec = (integer)$prec;
	$fTemp = (float)(1.8 * $cTemp) + 32;
	return round($fTemp, $prec);
}


function parse_daytime($raw) {

if($raw == 1) { $dttxt = NIGHT; }
if($raw == 2) { $dttxt = MORNING; }
if($raw == 3) { $dttxt = DAY; }
if($raw == 4) { $dttxt = EVENING; }

return $dttxt;
}

function wspeed($spd) {
$spd = round($spd);
if($spd == 0) { $spdtxt = CALM; }
if(($spd > 0) && ($spd < 1.4)) { $spdtxt = WWLIGHT; }
if(($spd > 1.4) && ($spd < 3.5)) { $spdtxt = WLIGHT; }
if(($spd >= 3.5) && ($spd < 5.5)) { $spdtxt = WBLIGHT; }
if(($spd >= 5.5) && ($spd < 8)) { $spdtxt = WMLIGHT; }
if(($spd >= 8) && ($spd < 13.9)) { $spdtxt = WMODERATE; }
if(($spd >= 13.9) && ($spd < 20.8)) { $spdtxt = WHARD; }
if($spd >= 20.8) { $spdtxt = WSTORM; }

return $spdtxt;
}

function parse_time($raw) {

$yr = substr($raw,0,4);
$mt = substr($raw,5,2);
$da = substr($raw,8,2);
$hr = substr($raw,11,2);
$mi = substr($raw,14,2);

$tm = mktime($hr,$mi,00,$mt,$da,$yr);
return $tm;
}

function parseToXML($htmlStr) 
{ 
$xmlStr=str_replace('<','&lt;',$htmlStr); 
$xmlStr=str_replace('>','&gt;',$xmlStr); 
$xmlStr=str_replace('"','&quot;',$xmlStr); 
$xmlStr=str_replace("'",'&#39;',$xmlStr); 
$xmlStr=str_replace("&",'&amp;',$xmlStr); 
return $xmlStr; 
} 

function parseTohtml($htmlStr) 
{ 
$xmlStr=str_replace('<','&lt;',$htmlStr); 
return $xmlStr; 
} 


 function fetchqaUrlWithoutHanging($url) // thanks to Tom at Carterlake.org for this script fragment
   {
   // Set maximum number of seconds (can have floating-point) to wait for feed before displaying page without feed
   $numberOfSeconds=4;   

   // Suppress error reporting so Web site visitors are unaware if the feed fails
   error_reporting(0);

   // Extract resource path and domain from URL ready for fsockopen

   $url = str_replace("http://","",$url);
   $urlComponents = explode("/",$url);
   $domain = $urlComponents[0];
   $resourcePath = str_replace($domain,"",$url);

   // Establish a connection
   $socketConnection = fsockopen($domain, 80, $errno, $errstr, $numberOfSeconds);

   if (!$socketConnection)
       {
       // You may wish to remove the following debugging line on a live Web site
        print("<!-- Network error: $errstr ($errno) -->\n");
       }    // end if
   else    {
       $xml = '';
       fputs($socketConnection, "GET $resourcePath HTTP/1.0\r\nHost: $domain\r\n\r\n");
   
       // Loop until end of file
       while (!feof($socketConnection))
           {
           $xml .= fgets($socketConnection, 4096);
           }    // end while

       fclose ($socketConnection);

       }    // end else
	  

   return($xml);

   }    // end function

// XML to Array
function xml2ary(&$string) {
    $parser = xml_parser_create();
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parse_into_struct($parser, $string, $vals, $index);
    xml_parser_free($parser);

    $mnary=array();
    $ary=&$mnary;
    foreach ($vals as $r) {
        $t=$r['tag'];
        if ($r['type']=='open') {
            if (isset($ary[$t])) {
                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                $cv=&$ary[$t][count($ary[$t])-1];
            } else $cv=&$ary[$t];
            if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
            $cv['_c']=array();
            $cv['_c']['_p']=&$ary;
            $ary=&$cv['_c'];

        } elseif ($r['type']=='complete') {
            if (isset($ary[$t])) { // same as open
                if (isset($ary[$t][0])) $ary[$t][]=array(); else $ary[$t]=array($ary[$t], array());
                $cv=&$ary[$t][count($ary[$t])-1];
            } else $cv=&$ary[$t];
            if (isset($r['attributes'])) {foreach ($r['attributes'] as $k=>$v) $cv['_a'][$k]=$v;}
            $cv['_v']=(isset($r['value']) ? $r['value'] : '');

        } elseif ($r['type']=='close') {
            $ary=&$ary['_p'];
        }
    }    
    
    _del_p($mnary);
    return $mnary;
}

// _Internal: Remove recursion in result array
function _del_p(&$ary) {
    foreach ($ary as $k=>$v) {
        if ($k==='_p') unset($ary[$k]);
        elseif (is_array($ary[$k])) _del_p($ary[$k]);
    }
}

// Array to XML
function ary2xml($cary, $d=0, $forcetag='') {
    $res=array();
    foreach ($cary as $tag=>$r) {
        if (isset($r[0])) {
            $res[]=ary2xml($r, $d, $tag);
        } else {
            if ($forcetag) $tag=$forcetag;
            $sp=str_repeat("\t", $d);
            $res[]="$sp<$tag";
            if (isset($r['_a'])) {foreach ($r['_a'] as $at=>$av) $res[]=" $at=\"$av\"";}
            $res[]=">".((isset($r['_c'])) ? "\n" : '');
            if (isset($r['_c'])) $res[]=ary2xml($r['_c'], $d+1);
            elseif (isset($r['_v'])) $res[]=$r['_v'];
            $res[]=(isset($r['_c']) ? $sp : '')."</$tag>\n";
        }
        
    }
    return implode('', $res);
}

// Insert element into array
function ins2ary(&$ary, $element, $pos) {
    $ar1=array_slice($ary, 0, $pos); $ar1[]=$element;
    $ary=array_merge($ar1, array_slice($ary, $pos));
}

   
// ----------------------------------------------------------
     
?>
