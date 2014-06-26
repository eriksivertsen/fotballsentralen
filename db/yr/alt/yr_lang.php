<?php
//Languagefile for yr.no-script
// lang start
echo "<!-- yrno-langfile loaded -->\n";

if($lang == "en") {
define('CHOOSECITY', 'Choose city');
define('FORECAST_FOR', 'Forecast for');
define('LASTUPD', 'Last update');
define('NEXTUPD', 'Next update');
define('PROVIDED_BY', 'Provided by');
define('MADE_BY', 'Made by');
define('NORMETEO', 'Norwegian meteorlogian institue');
define('TOMORROW', 'Tomorrow');
define('TODAY', 'Today');
define('NEXTFOUR', 'Forecast for next 4 days');
define('LASTDAYS', 'Forecast for days 6 - 10');
define('DAY', 'Day');
define('TEMPE', 'Temperature');
define('PRECIP', 'Precipitation');
define('BAROI', 'Pressure');
define('WINDI', 'Wind');
define('WEXT', '');
define('FROM', 'from');

function windspeed($raw) {
$txtspeeds =  array(  
'Calm' => 'Calm',
'Light air' => 'Light air',
'Light breeze' => 'Light breeze',
'Gentle breeze' => 'Gentle breeze',
'Moderate breeze' => 'Moderate breeze',
'Fresh breeze' => 'Fresh breeze',
'Strong breeze' => 'Strong breeze',
'Near Gale' => 'Near Gale',
'Fresh Gale' => 'Fresh Gale',
'Strong Gale' => 'Strong Gale',
'Storm' => 'Storm',
'$raw' => '$raw');
return $txtspeeds[$raw];
}

function winddirs($raw) {
$txtdirs =  array(  
'south' => 'south',
'south-southwest' => 'south-southwest',
'southwest' => 'southwest',
'west-southwest' => 'west-southwest',
'west' => 'west',
'west-northwest' => 'west-northwest',
'northwest' => 'west-northwest',
'north-northwest' => 'north-northwest',
'north' => 'north',
'north-northeast' => 'north-northeast',
'northeast' => 'northeast',
'east-northeast' => 'east-northeast',
'east' => 'east',
'east-southeast' => 'east-southeast',
'southeast' => 'southeast',
'south-southeast' => 'south-southeast',
'$raw' => '$raw');
return $txtdirs[$raw];
}

} else  {  // Default language

define('CHOOSECITY', 'Choose city');
define('FORECAST_FOR', 'Forecast for');
define('LASTUPD', 'Last update');
define('NEXTUPD', 'Next update');
define('PROVIDED_BY', 'Provided by');
define('MADE_BY', 'Made by');
define('NORMETEO', 'Norwegian meteorlogian institue');
define('TOMORROW', 'Tomorrow');
define('TODAY', 'Today');
define('NEXTFOUR', 'Forecast for next 4 days');
define('LASTDAYS', 'Forecast for days 6 - 10');
define('DAY', 'Day');
define('TEMPE', 'Temperature');
define('PRECIP', 'Precipitation');
define('BAROI', 'Pressure');
define('WINDI', 'Wind');
define('WEXT', '');
define('FROM', 'from');

function windspeed($raw) {
$txtspeeds =  array(  
'Calm' => 'Calm',
'Light air' => 'Light air',
'Light breeze' => 'Light breeze',
'Gentle breeze' => 'Gentle breeze',
'Moderate breeze' => 'Moderate breeze',
'Fresh breeze' => 'Fresh breeze',
'Strong breeze' => 'Strong breeze',
'Near Gale' => 'Near Gale',
'Fresh Gale' => 'Fresh Gale',
'Strong Gale' => 'Strong Gale',
'Storm' => 'Storm',
'$raw' => '$raw');
return $txtspeeds[$raw];
}

function winddirs($raw) {
$txtdirs =  array(  
'south' => 'south',
'south-southwest' => 'south-southwest',
'southwest' => 'southwest',
'west-southwest' => 'west-southwest',
'west' => 'west',
'west-northwest' => 'west-northwest',
'northwest' => 'west-northwest',
'north-northwest' => 'north-northwest',
'north' => 'north',
'north-northeast' => 'north-northeast',
'northeast' => 'northeast',
'east-northeast' => 'east-northeast',
'east' => 'east',
'east-southeast' => 'east-southeast',
'southeast' => 'southeast',
'south-southeast' => 'south-southeast',
'$raw' => '$raw');
return $txtdirs[$raw];
}

} // EOF DEFAULT
//lang end
?>
