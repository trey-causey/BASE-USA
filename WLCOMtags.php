<?php
/*
 File: WLCOMtags.php 

 Purpose: load WLCOM variables into a $WX[] array for use with the Canada/World/USA template sets

 Author: Ken True - webmaster@saratoga-weather.org
 
 My hearty thanks to Wim at Leuven-weather for developing the original code to use the 
   http://weatherlink.com/xml.php?... query for current WeatherLinkIP stations
	 This code is an adaptation of his tagsWLCOM.php script Leuven V28 template.

 (created by gen-WLtags.php - V1.00 - 20-Apr-2017)
*/
# Version 1.00 - 20-Apr-2017 - initial release
# Version 1.01 - 09-Jan-2018 - update for WeatherLink.com V2.0 API
# Version 1.02 - 17-Oct-2019 - fix several missing temperature conversions
// --------------------------------------------------------------------------

// allow viewing of generated source

if (isset($_REQUEST["sce"]) and strtolower($_REQUEST["sce"]) == "view" ) {
//--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header("Pragma: public");
   header("Cache-Control: private");
   header("Cache-Control: no-cache, must-revalidate");
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header("Connection: close");
   
   readfile($filenameReal);
   exit;
}
if(file_exists("Settings.php")) {include_once("Settings.php");}
if(isset($SITE['TZ'])) { date_default_timezone_set($SITE['TZ']); }
if(isset($_REQUEST['debug']) or 
   (isset($_REQUEST["sce"]) and strtolower($_REQUEST["sce"]) == "dump") ) {
	$doDebug = true; 
	} else {
	$doDebug = false;
}
global $doDebug;
# check for weatherlink.com userid/password being setup
if(!isset($SITE['WLCOMdid']) or !isset($SITE['WLCOMpw']) or !isset($SITE['WLCOMkey']) or 
   (isset($SITE['WLCOMdid']) and strpos($SITE['WLCOMdid'],'-device-id-') !== false) or
   (isset($SITE['WLCOMkey']) and strpos($SITE['WLCOMkey'],'-key-') !== false) or
   (isset($SITE['WLCOMpw']) and strpos($SITE['WLCOMpw'],'-password-') !== false) ) {
		 print "<p>Warning: WeatherLink.com password/device-id/key not set in Settings-weather.php.<br/>\n";
		 print "Update \$SITE['WLCOMdid'], \$SITE['WLCOMkey']and \$SITE['WLCOMpw'] variables in Settings-weather.php ";
		 print "with your weatherlink.com credentials.</p>\n";
		 exit;
	 }

$WXsoftware = 'WLCOM';  
$defsFile = 'WLCOM-defs.php';  // filename with $varnames = $WX['WL-varnames']; equivalents
$tagsWLCOM = $WXsoftware.'defs.php';
$startEcho = '<!-- ';
$endEcho   = ' -->';
//$WLCOMurl = 'http://www.weatherlink.com/xml.php?user='.$SITE['WLCOMuid'].'&pass='.$SITE['WLCOMpw'];
$WLCOMurl = 'http://api.weatherlink.com/v1/NoaaExt.xml?user='.$SITE['WLCOMdid'] . 
    '&pass=' . $SITE['WLCOMpw'] .
		'&apiToken=' . $SITE['WLCOMkey'];

//$WLCOMurlDisplay = 'http://www.weatherlink.com/xml.php?user='.$SITE['WLCOMuid'].'&pass=********';
$WLCOMurlDisplay = 'http://api.weatherlink.com/v1/NoaaExt.xml?user='.$SITE['WLCOMdid'] . 
    '&pass=********' . 
		'&apiToken=' . $SITE['WLCOMkey'];
$uomTemp	= trim($SITE['uomTemp']);
$uomBaro	= trim($SITE['uomBaro']);
$uomRain	= trim($SITE['uomRain']);
$uomSnow	= trim($SITE['uomSnow']);
$uomDistance = trim($SITE['uomDistance']);
$uomWind	= trim($SITE['uomWind']);
$uomPerHour	= trim($SITE['uomPerHour']);
$loaded_current = false;
$local_current  = $SITE['cacheFileDir'].'weatherlinkcom.xml';
$cacheAllowed = $SITE['WLCOMcacheDW'];

 
if (isset($_REQUEST['force']) && strtolower($_REQUEST['force']) == 'tags') {
	echo $startEcho.$tagsWLCOM.': data freshly loaded while "force" was used.'.$endEcho.PHP_EOL;
	$loaded_current =  false;
} elseif (file_exists($local_current) ){
	$file_time      = filemtime($local_current);
	$now            = time();
	$diff           = ($now-$file_time);
	$cacheAllowed   = $SITE['WLCOMcacheDW'];
        if ($doDebug) {echo  "<!-- 
$tagsWLCOM ($local_current)
  cache time   = ".date('c',$file_time)." from unix time $file_time
  current time = ".date('c',$now)." from unix time $now 
  difference   = $diff (seconds)
  diff allowed = $cacheAllowed (seconds) -->".PHP_EOL;
				}
	if ($diff <= $cacheAllowed){
		$rawXML =  file_get_contents($local_current);
    if ($doDebug) {echo $startEcho.$tagsWLCOM.': data loaded from '.$local_current.$endEcho.PHP_EOL;}
   $loaded_current =   true;
 # print_r ($ws); # exit;
   
	} else {
		if ($doDebug) {echo $startEcho.$tagsWLCOM.": data to old, will be loaded from url ".$endEcho.PHP_EOL;}
	}
}
if ($loaded_current == false) {
	if ($doDebug) {echo $startEcho.$tagsWLCOM.': data loaded from url: '.$WLCOMurlDisplay.$endEcho.PHP_EOL;}
	$ch = curl_init();
	curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt ($ch, CURLOPT_URL, $WLCOMurl);
	curl_setopt ($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (WLCOMtags.php - saratoga-weather.org)');
	curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 4);
	curl_setopt ($ch, CURLOPT_TIMEOUT, 4);
  curl_setopt ($ch, CURLOPT_RETURNTRANSFER, true);              // return the data transfer
  curl_setopt ($ch, CURLOPT_NOBODY, false);                     // set nobody
  curl_setopt ($ch, CURLOPT_HEADER, true);                      // include header information
	$rawHTML = curl_exec ($ch);

  if(curl_error($ch) <> '') {                                  // IF there is an error
   if ($doDebug) {echo "<!-- curl Error: ". curl_error($ch) ." -->\n";}        //  display error notice
  }

  $cinfo = curl_getinfo($ch);                                  // get info on curl exec.

  if ($doDebug) { echo "<!-- HTTP stats: " .
    " RC=".$cinfo['http_code'] .
    " dest=".$cinfo['primary_ip'] ;
	if(isset($cinfo['primary_port'])) { 
	  echo " port=".$cinfo['primary_port'] ;
	}
	if(isset($cinfo['local_ip'])) {
	  echo " (from sce=" . $cinfo['local_ip'] . ")";
	}
	echo 
	"\n      Times:" .
    " dns=".sprintf("%01.3f",round($cinfo['namelookup_time'],3)).
    " conn=".sprintf("%01.3f",round($cinfo['connect_time'],3)).
    " pxfer=".sprintf("%01.3f",round($cinfo['pretransfer_time'],3));
	if($cinfo['total_time'] - $cinfo['pretransfer_time'] > 0.0000) {
	  echo
	  " get=". sprintf("%01.3f",round($cinfo['total_time'] - $cinfo['pretransfer_time'],3));
	}
    echo " total=".sprintf("%01.3f",round($cinfo['total_time'],3)) .
    " secs -->\n";
	}
	#echo $rawXML;
	curl_close ($ch);
	
	list($headers,$rawXML) = explode("\r\n\r\n",$rawHTML."\r\n\r\n");
	
	if($cinfo['http_code'] !== 200 ) {
		print "<p>Warning: weatherlink.com data fetch unsuccessful. RC='".$cinfo['http_code']."'<br/>";
		print "Headers returned are:<br/>\n<pre>";
		print $headers;
		print "</pre></p>\n";
		exit;
	}
	
	if(file_put_contents($local_current,$rawXML)) {
		if ($doDebug) {echo "<!-- saved $local_current ".strlen($rawXML)." bytes -->\n";}
	} else {
		if ($doDebug) {echo "<!-- Warning: unable to save $local_current cache file -->\n";}
	}

} 
if ($loaded_current == false && trim($rawXML) == '') {
	if ($diff <= 3* $cacheAllowed){
		$rawXML     =  file_get_contents($local_current);
    if ($doDebug) {echo $startEcho.$tagsWLCOM.': data loaded from '.$local_current.' after upping cache time'.$endEcho.PHP_EOL;}
    $loaded_current =   true;
 #  print_r ($ws); # exit;
    return;         // ?????
 }
 echo '<h3>Input file from weatherlink.com has no contents - program ends, please reload page.</h3>';
 return;
}

$davis  = new SimpleXMLElement($rawXML);     // all XML in $davis
$xml    = $davis->davis_current_observation; // current obs in $xml

$WX = array();
global $WX;

# --- these variables are only available via our processing or definitions

$datefmt = $SITE['WDdateMDY']?'m/d/y g:ia':'d/m/y g:ia';
# set the date and time in WeatherLink format
list($WX['date'],$WX['time']) = explode(' ',
   date($datefmt,strtotime ( (string)$davis->observation_time_rfc822) ) );
# clone our units since the XML is all in F,mph,in,inHg units and we'll convert
# to the requested units from $SITE[]
#	 
$WX['tempUnit'] = $uomTemp;
$WX['windUnit'] = $uomWind;
$WX['barUnit']  = $uomBaro;
$WX['rainUnit'] = $uomRain;
 

# ---------------------------------------------------------------------
# --- start section created by gen-WLCOMtags.php ----------------------

# -------- Station variables --------
$WX['StationElevation'] = (string)$davis->elevation; /* Elevation (from NOAA Setup)   */
$WX['StationLatitude'] = (string)$davis->latitude; /* Latitude (from NOAA Setup)   */
$WX['StationCity'] = (string)$davis->location; /* City (from NOAA Setup)   */
$WX['StationLongitude'] = (string)$davis->longitude; /* Longitude (from NOAA Setup)   */
$WX['StationName'] = (string)$davis->station_id; /* Name of Station  */
$WX['sunriseTime'] = (string)$xml->sunrise; /* Sunrise Time   */
$WX['sunsetTime'] = (string)$xml->sunset; /* Sunset Time  */

# -------- Temp variables --------
$WX['outsideTemp'] = WLCOM_convertTemp((string)$davis->temp_f,'F',$uomTemp); /* Outside Temperature   */
$WX['hiOutsideTemp'] = WLCOM_convertTemp((string)$xml->temp_day_high_f,'F',$uomTemp); /* High Outside Temperature  */
$WX['hiOutsideTempTime'] = (string)$xml->temp_day_high_time; /* Time of High Outside Temperature   */
$WX['lowOutsideTemp'] = WLCOM_convertTemp((string)$xml->temp_day_low_f,'F',$uomTemp); /* Low Outside Temperature  */
$WX['lowOutsideTempTime'] = (string)$xml->temp_day_low_time; /* Time of Low Outside Temperature   */
$WX['hiInsideTemp'] = WLCOM_convertTemp((string)$xml->temp_in_day_high_f,'F',$uomTemp); /* High Inside Temperature   */
$WX['hiMonthlyInsideTemp'] = WLCOM_convertTemp((string)$xml->temp_in_day_high_f,'F',$uomTemp); /* High Monthly Inside Temperature   */
$WX['hiInsideTempTime'] = (string)$xml->temp_in_day_high_time; /* Time of High Inside Temperature   */
$WX['hiMonthlyInsideTempTime'] = (string)$xml->temp_in_day_high_time; /* High Monthly Inside Temperature  Not in Tags */
$WX['lowInsideTemp'] = WLCOM_convertTemp((string)$xml->temp_in_day_low_f,'F',$uomTemp); /* Low Inside Temperature  */
$WX['lowInsideTempTime'] = (string)$xml->temp_in_day_low_time; /* Time of Low Inside Temperature   */
$WX['insideTemp'] = WLCOM_convertTemp((string)$xml->temp_in_f,'F',$uomTemp); /* Inside Temperature  */
$WX['lowMonthlyInsideTemp'] = WLCOM_convertTemp((string)$xml->temp_in_month_low_f,'F',$uomTemp); /* Low Monthly Inside Temperature   */
$WX['hiYearlyInsideTemp'] = WLCOM_convertTemp((string)$xml->temp_in_year_high_f,'F',$uomTemp); /* High Yearly Inside Temperature   */
$WX['lowYearlyInsideTemp'] = WLCOM_convertTemp((string)$xml->temp_in_year_low_f,'F',$uomTemp); /* Low Yearly Inside Temperature   */
$WX['hiMonthlyOutsideTemp'] = WLCOM_convertTemp((string)$xml->temp_month_high_f,'F',$uomTemp); /* High monthly Outside Temperature   */
$WX['lowMonthlyOutsideTemp'] = WLCOM_convertTemp((string)$xml->temp_month_low_f,'F',$uomTemp); /* Low monthly Outside Temperature  */
$WX['hiYearlyOutsideTemp'] = WLCOM_convertTemp((string)$xml->temp_year_high_f,'F',$uomTemp); /* High yearly Outside Temperature  */
$WX['lowYearlyOutsideTemp'] = WLCOM_convertTemp((string)$xml->temp_year_low_f,'F',$uomTemp); /* Low yearly Outside Temperature  */

# -------- Hum variables --------
$WX['outsideHumidity'] = (string)$davis->relative_humidity; /* Outside Humidity   */
$WX['hiHumidity'] = (string)$xml->relative_humidity_day_high; /* High Humidity   */
$WX['hiHumTime'] = (string)$xml->relative_humidity_day_high_time; /* Time of High Humidity   */
$WX['lowHumidity'] = (string)$xml->relative_humidity_day_low; /* Low Humidity   */
$WX['lowHumTime'] = (string)$xml->relative_humidity_day_low_time; /* Time of Low Humidity  */
$WX['insideHumidity'] = (string)$xml->relative_humidity_in; /* Inside Humidity   */
$WX['hiInsideHumidity'] = (string)$xml->relative_humidity_in_day_high; /* High Inside Humidity   */
$WX['hiInsideHumidityTime'] = (string)$xml->relative_humidity_in_day_high_time; /* High Inside Humidity Time   */
$WX['lowInsideHumidity'] = (string)$xml->relative_humidity_in_day_low; /* Low Inside Humidity   */
$WX['lowInsideHumidityTime'] = (string)$xml->relative_humidity_in_day_low_time; /* Low Inside Humidity Time   */
$WX['hiMonthlyInsideHumidity'] = (string)$xml->relative_humidity_in_month_high; /* High Monthly Inside Humidity   */
$WX['lowMonthlyInsideHumidity'] = (string)$xml->relative_humidity_in_month_low; /* Low Monthly Inside Humidity   */
$WX['lowYearlyHumidity'] = (string)$xml->relative_humidity_in_month_low; /* Low Yearly Humidity  */
$WX['hiYearlyInsideHumidity'] = (string)$xml->relative_humidity_in_year_high; /* High Yearly Inside Humidity   */
$WX['lowYearlyInsideHumidity'] = (string)$xml->relative_humidity_in_year_low; /* Low Yearly Inside Humidity  */
$WX['hiMonthlyHumidity'] = (string)$xml->relative_humidity_month_high; /* High Monthly Humidity  */
$WX['lowMonthlyHumidity'] = (string)$xml->relative_humidity_month_low; /* Low Monthly Humidity  */
$WX['hiYearlyHumidity'] = (string)$xml->relative_humidity_year_high; /* High Yearly Humidity  */

# -------- Temp-DP variables --------
$WX['outsideDewPt'] = WLCOM_convertTemp((string)$davis->dewpoint_f,'F',$uomTemp); /* Outside Dew Point   */
$WX['hiDewpoint'] = WLCOM_convertTemp((string)$xml->dewpoint_day_high_f,'F',$uomTemp); /* High Dew Point   */
$WX['hiDewpointTime'] = (string)$xml->dewpoint_day_high_time; /* Time of High Dew Point   */
$WX['lowDewpoint'] = WLCOM_convertTemp((string)$xml->dewpoint_day_low_f,'F',$uomTemp); /* Low Dew Point   */
$WX['lowDewpointTime'] = (string)$xml->dewpoint_day_low_time; /* Time of Low Dew Point   */
$WX['hiMonthlyDewpoint'] = WLCOM_convertTemp((string)$xml->dewpoint_month_high_f,'F',$uomTemp); /* High Monthly Dew Point  */
$WX['lowMonthlyDewpoint'] = WLCOM_convertTemp((string)$xml->dewpoint_month_low_f,'F',$uomTemp); /* Low Monthly Dew Point  */
$WX['hiYearlyDewpoint'] = WLCOM_convertTemp((string)$xml->dewpoint_year_high_f,'F',$uomTemp); /* High Yearly Dew Point   */
$WX['lowYearlyDewpoint'] = WLCOM_convertTemp((string)$xml->dewpoint_year_low_f,'F',$uomTemp); /* Low Yearly Dew Point   */

# -------- Temp-HI variables --------
$WX['outsideHeatIndex'] = WLCOM_convertTemp((string)$davis->heat_index_f,'F',$uomTemp); /* Outside Heat Index   */
$WX['hiHeatindex'] = WLCOM_convertTemp((string)$xml->heat_index_day_high_f,'F',$uomTemp); /* High Heat Index   */
$WX['hiHeatindexTime'] = (string)$xml->heat_index_day_high_time; /* Time of High Heat Index   */
$WX['hiMonthlyHeatindex'] = WLCOM_convertTemp((string)$xml->heat_index_month_high_f,'F',$uomTemp); /* High Monthly Heat Index  */
$WX['hiYearlyHeatindex'] = WLCOM_convertTemp((string)$xml->heat_index_year_high_f,'F',$uomTemp); /* High Yearly Heat Index   */

# -------- Temp-WC variables --------
$WX['windChill'] = WLCOM_convertTemp((string)$davis->windchill_f,'F',$uomTemp); /* Wind Chill   */
$WX['lowWindchill'] = WLCOM_convertTemp((string)$xml->windchill_day_low_f,'F',$uomTemp); /* Low Wind Chill   */
$WX['lowWindchillTime'] = (string)$xml->windchill_day_low_time; /* Time of Low Wind Chill   */
$WX['lowMonthlyWindchill'] = WLCOM_convertTemp((string)$xml->windchill_month_low_f,'F',$uomTemp); /* Low Monthly Wind Chill   */
$WX['lowYearlyWindchill'] = WLCOM_convertTemp((string)$xml->windchill_year_low_f,'F',$uomTemp); /* Low Yearly Wind Chill  */

# -------- Wind variables --------
$WX['windDir'] = (string)$davis->wind_degrees; /* Wind Direction In Degrees   */
if($WX['windDir'] == '') {$WX['windDir'] = '0.0';}
$WX['windDirection'] = WLCOM_convertWindDir((string)$davis->wind_degrees); /* Wind Direction Sector (16-point compass) We Convert */
if($WX['windDirection'] == '') {$WX['windDirection'] = 'calm'; }
$WX['windSpeed'] = WLCOM_convertWind((string)$davis->wind_mph,'mph',$uomWind); /* Wind Speed   */
$WX['hiWindSpeed'] = WLCOM_convertWind((string)$xml->wind_day_high_mph,'mph',$uomWind); /* High Wind Speed   */
$WX['hiWindSpeedTime'] = (string)$xml->wind_day_high_time; /* Time of High Wind Speed   */
$WX['hiMonthlyWindSpeed'] = WLCOM_convertWind((string)$xml->wind_month_high_mph,'mph',$uomWind); /* High Monthly Wind Speed   */
$WX['wind10Avg'] = WLCOM_convertWind((string)$xml->wind_ten_min_avg_mph,'mph',$uomWind); /* 10 Minute Average Wind Speed*  */
$WX['windAvg10'] = WLCOM_convertWind((string)$xml->wind_ten_min_avg_mph,'mph',$uomWind); /* 10-minute Wind Speed Average  */
$WX['windHigh10'] = WLCOM_convertWind((string)$xml->wind_ten_min_gust_mph,'mph',$uomWind); /* 10-minute Wind High Speed  */
$WX['hiYearlyWindSpeed'] = WLCOM_convertWind((string)$xml->wind_year_high_mph,'mph',$uomWind); /* High Yearly Wind Speed   */

# -------- ET variables --------
if(isset($xml->et_day)) {
  $WX['dailyEt'] = WLCOM_convertRain((string)$xml->et_day,'in',$uomRain); /* Daily ET   */
}
if(isset($xml->et_month)) {
  $WX['monthlyEt'] = WLCOM_convertRain((string)$xml->et_month,'in',$uomRain); /* Monthly ET  */
}
if(isset($xml->et_year)) {
  $WX['yearlyEt'] = WLCOM_convertRain((string)$xml->et_year,'in',$uomRain); /* Yearly ET   */
}

# -------- Rain variables --------
$WX['dailyRain'] = WLCOM_convertRain((string)$xml->rain_day_in,'in',$uomRain); /* Daily Rain   */
$WX['monthlyRain'] = WLCOM_convertRain((string)$xml->rain_month_in,'in',$uomRain); /* Monthly Rain   */
$WX['hiRainRate'] = WLCOM_convertRain((string)$xml->rain_rate_day_high_in_per_hr,'in',$uomRain); /* High Rain Rate   */
if(isset($xml->rain_rate_day_high_time)) {
  $WX['hiRainRateTime'] = (string)$xml->rain_rate_day_high_time; /* Time of High Rain Rate  */
}
$WX['hiRainRateHour'] = WLCOM_convertRain((string)$xml->rain_rate_hour_high_in_per_hr,'in',$uomRain); /* High Rain Rate Hour  */
$WX['rainRate'] = WLCOM_convertRain((string)$xml->rain_rate_in_per_hr,'in',$uomRain); /* Rain Rate   */
$WX['hiMonthlyRainRate'] = WLCOM_convertRain((string)$xml->rain_rate_month_high_in_per_hr,'in',$uomRain); /* High Monthly Rain Rate  */
$WX['hiYearlyRainRate'] = WLCOM_convertRain((string)$xml->rain_rate_year_high_in_per_hr,'in',$uomRain); /* High Yearly Rain Rate  */
$WX['stormRain'] = WLCOM_convertRain((string)$xml->rain_storm_in,'in',$uomRain); /* Storm Rain   */
if(isset($xml->rain_storm_start_date)) {
  $WX['stormRainDate'] = (string)$xml->rain_storm_start_date; /* Storm Rain Start Date Added-not WL native */
}
$WX['totalRain'] = WLCOM_convertRain((string)$xml->rain_year_in,'in',$uomRain); /* Yearly Rain   */

# -------- Baro variables --------
$WX['barometer'] = WLCOM_convertBaro((string)$davis->pressure_in,'inHg',$uomBaro); /* Barometer  */
$WX['hiBarometer'] = WLCOM_convertBaro((string)$xml->pressure_day_high_in,'inHg',$uomBaro); /* High Barometer  */
$WX['hiBarometerTime'] = (string)$xml->pressure_day_high_time; /* Time of High Barometer  */
$WX['lowBarometer'] = WLCOM_convertBaro((string)$xml->pressure_day_low_in,'inHg',$uomBaro); /* Low Barometer  */
$WX['lowBarometerTime'] = (string)$xml->pressure_day_low_time; /* Time of Low Barometer  */
$WX['hiMonthlyBarometer'] = WLCOM_convertBaro((string)$xml->pressure_month_high_in,'inHg',$uomBaro); /* High Monthly Barometer  */
$WX['lowMonthlyBarometer'] = WLCOM_convertBaro((string)$xml->pressure_month_low_in,'inHg',$uomBaro); /* Low Monthly Barometer  */
$WX['BarTrend'] = (string)$xml->pressure_tendency_string; /* 3-Hour Barometer Trend*  */
$WX['hiYearlyBarometer'] = WLCOM_convertBaro((string)$xml->pressure_year_high_in,'inHg',$uomBaro); /* High Yearly Barometer  */
$WX['lowYearlyBarometer'] = WLCOM_convertBaro((string)$xml->pressure_year_low_in,'inHg',$uomBaro); /* Low Yearly Barometer  */

# -------- Solar variables --------
if(isset($xml->solar_radiation)) {
  $WX['solarRad'] = (string)$xml->solar_radiation; /* Solar Radiation   */
}
if(isset($xml->solar_radiation_day_high)) {
  $WX['hiSolarRad'] = (string)$xml->solar_radiation_day_high; /* High Solar Radiation  */
}
if(isset($xml->solar_radiation_day_high_time)) {
  $WX['hiSolarRadTime'] = (string)$xml->solar_radiation_day_high_time; /* Time of High Solar Radiation  */
}
if(isset($xml->solar_radiation_month_high)) {
  $WX['hiMonthlySolarRad'] = (string)$xml->solar_radiation_month_high; /* High Monthly Solar Radiation  */
}
if(isset($xml->solar_radiation_year_high)) {
  $WX['hiYearlySolarRad'] = (string)$xml->solar_radiation_year_high; /* High Yearly Solar Radiation  */
}

# -------- UV variables --------
if(isset($xml->uv_index)) {
  $WX['uv'] = (string)$xml->uv_index; /* UV  */
}
if(isset($xml->uv_index_day_high)) {
  $WX['hiUV'] = (string)$xml->uv_index_day_high; /* High UV  */
}
if(isset($xml->uv_index_day_high_time)) {
  $WX['hiUVTime'] = (string)$xml->uv_index_day_high_time; /* Time of High UV  */
}
if(isset($xml->uv_index_month_high)) {
  $WX['hiMonthlyUV'] = (string)$xml->uv_index_month_high; /* High monthly UV  */
}
if(isset($xml->uv_index_year_high)) {
  $WX['hiYearlyUV'] = (string)$xml->uv_index_year_high; /* High yearly UV  */
}

# -------- Xtra-Temp variables --------
if(isset($xml->temp_extra_1)) {
  $WX['Temperature2'] = WLCOM_convertTemp((string)$xml->temp_extra_1,'F',$uomTemp); /* Temperature 2  */
}
if(isset($xml->temp_extra_1_day_high)) {
  $WX['hiTemp2'] = WLCOM_convertTemp((string)$xml->temp_extra_1_day_high,'F',$uomTemp); /* High Temperature 2   */
}
if(isset($xml->temp_extra_1_day_high_time)) {
  $WX['hiTempTime2'] = (string)$xml->temp_extra_1_day_high_time; /* Time of High Temperature 2  */
}
if(isset($xml->temp_extra_1_day_low)) {
  $WX['lowTemp2'] = WLCOM_convertTemp((string)$xml->temp_extra_1_day_low,'F',$uomTemp); /* Low Temperature 2  */
}
if(isset($xml->temp_extra_1_day_low_time)) {
  $WX['lowTempTime2'] = (string)$xml->temp_extra_1_day_low_time; /* Time of Low Temperature 2  */
}
if(isset($xml->temp_extra_2)) {
  $WX['Temperature3'] = WLCOM_convertTemp((string)$xml->temp_extra_2,'F',$uomTemp); /* Temperature 3   */
}
if(isset($xml->temp_extra_2_day_high)) {
  $WX['hiTemp3'] = WLCOM_convertTemp((string)$xml->temp_extra_2_day_high,'F',$uomTemp); /* High Temperature 3  */
}
if(isset($xml->temp_extra_2_day_high_time)) {
  $WX['hiTempTime3'] = (string)$xml->temp_extra_2_day_high_time; /* High Temperature 3 Time   */
}
if(isset($xml->temp_extra_2_day_low)) {
  $WX['lowTemp3'] = WLCOM_convertTemp((string)$xml->temp_extra_2_day_low,'F',$uomTemp); /* Low Temperature 3  */
}
if(isset($xml->temp_extra_2_day_low_time)) {
  $WX['lowTempTime3'] = (string)$xml->temp_extra_2_day_low_time; /* Low Temperature 3 Time   */
}
if(isset($xml->temp_extra_3)) {
  $WX['Temperature4'] = WLCOM_convertTemp((string)$xml->temp_extra_3,'F',$uomTemp); /* Temperature 4   */
}
if(isset($xml->temp_extra_3_day_high)) {
  $WX['hiTemp4'] = WLCOM_convertTemp((string)$xml->temp_extra_3_day_high,'F',$uomTemp); /* High Temperature 4  */
}
if(isset($xml->temp_extra_3_day_high_time)) {
  $WX['hiTempTime4'] = (string)$xml->temp_extra_3_day_high_time; /* High Temperature 4 Time   */
}
if(isset($xml->temp_extra_3_day_low)) {
  $WX['lowTemp4'] = WLCOM_convertTemp((string)$xml->temp_extra_3_day_low,'F',$uomTemp); /* Low Temperature 4  */
}
if(isset($xml->temp_extra_3_day_low_time)) {
  $WX['lowTempTime4'] = (string)$xml->temp_extra_3_day_low_time; /* Low Temperature 4 Time   */
}
if(isset($xml->temp_extra_4)) {
  $WX['Temperature5'] = WLCOM_convertTemp((string)$xml->temp_extra_4,'F',$uomTemp); /* Temperature 5  */
}
if(isset($xml->temp_extra_4_day_high)) {
  $WX['hiTemp5'] = WLCOM_convertTemp((string)$xml->temp_extra_4_day_high,'F',$uomTemp); /* High Temperature 5  */
}
if(isset($xml->temp_extra_4_day_high_time)) {
  $WX['hiTempTime5'] = (string)$xml->temp_extra_4_day_high_time; /* High Temperature 5 Time   */
}
if(isset($xml->temp_extra_4_day_low)) {
  $WX['lowTemp5'] = WLCOM_convertTemp((string)$xml->temp_extra_4_day_low,'F',$uomTemp); /* Low Temperature 5  */
}
if(isset($xml->temp_extra_4_day_low_time)) {
  $WX['lowTempTime5'] = (string)$xml->temp_extra_4_day_low_time; /* Low Temperature 5 Time   */
}
if(isset($xml->temp_extra_5)) {
  $WX['Temperature6'] = WLCOM_convertTemp((string)$xml->temp_extra_5,'F',$uomTemp); /* Temperature 6   */
}
if(isset($xml->temp_extra_5_day_high)) {
  $WX['hiTemp6'] = WLCOM_convertTemp((string)$xml->temp_extra_5_day_high,'F',$uomTemp); /* High Temperature 6  */
}
if(isset($xml->temp_extra_5_day_high_time)) {
  $WX['hiTempTime6'] = (string)$xml->temp_extra_5_day_high_time; /* High Temperature 6 Time   */
}
if(isset($xml->temp_extra_5_day_low)) {
  $WX['lowTemp6'] = WLCOM_convertTemp((string)$xml->temp_extra_5_day_low,'F',$uomTemp); /* Low Temperature 6  */
}
if(isset($xml->temp_extra_5_day_low_time)) {
  $WX['lowTempTime6'] = (string)$xml->temp_extra_5_day_low_time; /* Low Temperature 6 Time   */
}
if(isset($xml->temp_extra_6)) {
  $WX['Temperature7'] = WLCOM_convertTemp((string)$xml->temp_extra_6,'F',$uomTemp); /* Temperature 7  */
}
if(isset($xml->temp_extra_6_day_high)) {
  $WX['hiTemp7'] = WLCOM_convertTemp((string)$xml->temp_extra_6_day_high,'F',$uomTemp); /* High Temperature 7  */
}
if(isset($xml->temp_extra_6_day_high_time)) {
  $WX['hiTempTime7'] = (string)$xml->temp_extra_6_day_high_time; /* High Temperature 7 Time   */
}
if(isset($xml->temp_extra_6_day_low)) {
  $WX['lowTemp7'] = WLCOM_convertTemp((string)$xml->temp_extra_6_day_low,'F',$uomTemp); /* Low Temperature 7  */
}
if(isset($xml->temp_extra_6_day_low_time)) {
  $WX['lowTempTime7'] = (string)$xml->temp_extra_6_day_low_time; /* Low Temperature 7 Time   */
}
if(isset($xml->temp_extra_7)) {
  $WX['Temperature8'] = WLCOM_convertTemp((string)$xml->temp_extra_7,'F',$uomTemp); /* Temperature 8   */
}
if(isset($xml->temp_extra_7_day_high)) {
  $WX['hiTemp8'] = WLCOM_convertTemp((string)$xml->temp_extra_7_day_high,'F',$uomTemp); /* High Temperature 8  */
}
if(isset($xml->temp_extra_7_day_high_time)) {
  $WX['hiTempTime8'] = (string)$xml->temp_extra_7_day_high_time; /* High Temperature 8 Time   */
}
if(isset($xml->temp_extra_7_day_low)) {
  $WX['lowTemp8'] = WLCOM_convertTemp((string)$xml->temp_extra_7_day_low,'F',$uomTemp); /* Low Temperature 8  */
}
if(isset($xml->temp_extra_7_day_low_time)) {
  $WX['lowTempTime8'] = (string)$xml->temp_extra_7_day_low_time; /* Low Temperature 8 Time   */
}

# -------- Xtra-Hum variables --------
if(isset($xml->relative_humidity_1)) {
  $WX['Humidity2'] = (string)$xml->relative_humidity_1; /* Humidity 2   */
}
if(isset($xml->relative_humidity_1_day_high)) {
  $WX['hiHum2'] = (string)$xml->relative_humidity_1_day_high; /* High Humidity 2  */
}
if(isset($xml->relative_humidity_1_day_high_time)) {
  $WX['hiHumTime2'] = (string)$xml->relative_humidity_1_day_high_time; /* High Humidity 2 Time  */
}
if(isset($xml->relative_humidity_1_day_low)) {
  $WX['lowHum2'] = (string)$xml->relative_humidity_1_day_low; /* Low Humidity 2  */
}
if(isset($xml->relative_humidity_1_day_low_time)) {
  $WX['lowHumTime2'] = (string)$xml->relative_humidity_1_day_low_time; /* Low Humidity Time 2  */
}
if(isset($xml->relative_humidity_2)) {
  $WX['Humidity3'] = (string)$xml->relative_humidity_2; /* Humidity 3  */
}
if(isset($xml->relative_humidity_2_day_high)) {
  $WX['hiHum3'] = (string)$xml->relative_humidity_2_day_high; /* High Humidity 3  */
}
if(isset($xml->relative_humidity_2_day_high_time)) {
  $WX['hiHumTime3'] = (string)$xml->relative_humidity_2_day_high_time; /* High Humidity 3 Time   */
}
if(isset($xml->relative_humidity_2_day_low)) {
  $WX['lowHum3'] = (string)$xml->relative_humidity_2_day_low; /* Low Humidity 3  */
}
if(isset($xml->relative_humidity_2_day_low_time)) {
  $WX['lowHumTime3'] = (string)$xml->relative_humidity_2_day_low_time; /* Low Humidity 3 Time  */
}
if(isset($xml->relative_humidity_3)) {
  $WX['Humidity4'] = (string)$xml->relative_humidity_3; /* Humidity 4   */
}
if(isset($xml->relative_humidity_3_day_high)) {
  $WX['hiHum4'] = (string)$xml->relative_humidity_3_day_high; /* High Humidity 4  */
}
if(isset($xml->relative_humidity_3_day_high_time)) {
  $WX['hiHumTime4'] = (string)$xml->relative_humidity_3_day_high_time; /* High Humidity 4 Time   */
}
if(isset($xml->relative_humidity_3_day_low)) {
  $WX['lowHum4'] = (string)$xml->relative_humidity_3_day_low; /* Low Humidity 4   */
}
if(isset($xml->relative_humidity_3_day_low_time)) {
  $WX['lowHumTime4'] = (string)$xml->relative_humidity_3_day_low_time; /* Low Humidity 4 Time  */
}
if(isset($xml->relative_humidity_4)) {
  $WX['Humidity5'] = (string)$xml->relative_humidity_4; /* Humidity 5   */
}
if(isset($xml->relative_humidity_4_day_high)) {
  $WX['hiHum5'] = (string)$xml->relative_humidity_4_day_high; /* High Humidity 5  */
}
if(isset($xml->relative_humidity_4_day_high_time)) {
  $WX['hiHumTime5'] = (string)$xml->relative_humidity_4_day_high_time; /* High Humidity 5 Time   */
}
if(isset($xml->relative_humidity_4_day_low)) {
  $WX['lowHum5'] = (string)$xml->relative_humidity_4_day_low; /* Low Humidity 5  */
}
if(isset($xml->relative_humidity_4_day_low_time)) {
  $WX['lowHumTime5'] = (string)$xml->relative_humidity_4_day_low_time; /* Low Humidity 5 Time  */
}
if(isset($xml->relative_humidity_5)) {
  $WX['Humidity6'] = (string)$xml->relative_humidity_5; /* Humidity 6   */
}
if(isset($xml->relative_humidity_5_day_high)) {
  $WX['hiHum6'] = (string)$xml->relative_humidity_5_day_high; /* High Humidity 6  */
}
if(isset($xml->relative_humidity_5_day_high_time)) {
  $WX['hiHumTime6'] = (string)$xml->relative_humidity_5_day_high_time; /* High Humidity 6 Time   */
}
if(isset($xml->relative_humidity_5_day_low)) {
  $WX['lowHum6'] = (string)$xml->relative_humidity_5_day_low; /* Low Humidity 6  */
}
if(isset($xml->relative_humidity_5_day_low_time)) {
  $WX['lowHumTime6'] = (string)$xml->relative_humidity_5_day_low_time; /* Low Humidity 6 Time  */
}
if(isset($xml->relative_humidity_6)) {
  $WX['Humidity7'] = (string)$xml->relative_humidity_6; /* Humidity 7   */
}
if(isset($xml->relative_humidity_6_day_high)) {
  $WX['hiHum7'] = (string)$xml->relative_humidity_6_day_high; /* High Humidity 7  */
}
if(isset($xml->relative_humidity_6_day_high_time)) {
  $WX['hiHumTime7'] = (string)$xml->relative_humidity_6_day_high_time; /* High Humidity 7 Time   */
}
if(isset($xml->relative_humidity_6_day_low)) {
  $WX['lowHum7'] = (string)$xml->relative_humidity_6_day_low; /* Low Humidity 7  */
}
if(isset($xml->relative_humidity_6_day_low_time)) {
  $WX['lowHumTime7'] = (string)$xml->relative_humidity_6_day_low_time; /* Low Humidity 7 Time  */
}
if(isset($xml->relative_humidity_7)) {
  $WX['Humidity8'] = (string)$xml->relative_humidity_7; /* Humidity 8   */
}
if(isset($xml->relative_humidity_7_day_high)) {
  $WX['hiHum8'] = (string)$xml->relative_humidity_7_day_high; /* High Humidity 8  */
}
if(isset($xml->relative_humidity_7_day_high_time)) {
  $WX['hiHumTime8'] = (string)$xml->relative_humidity_7_day_high_time; /* High Humidity 8 Time   */
}
if(isset($xml->relative_humidity_7_day_low)) {
  $WX['lowHum8'] = (string)$xml->relative_humidity_7_day_low; /* Low Humidity 8   */
}
if(isset($xml->relative_humidity_7_day_low_time)) {
  $WX['lowHumTime8'] = (string)$xml->relative_humidity_7_day_low_time; /* Low Humidty 8 Time  */
}

# -------- Xtra-_Soil variables --------
if(isset($xml->soil_moisture_1)) {
  $WX['SoilMoisture1'] = (string)$xml->soil_moisture_1; /* Soil Moisture 1   */
}
if(isset($xml->soil_moisture_1_day_high)) {
  $WX['hiSoilMoist1'] = (string)$xml->soil_moisture_1_day_high; /* High Soil Moisture 1   */
}
if(isset($xml->soil_moisture_1_day_high_time)) {
  $WX['hiSoilMoistTime1'] = (string)$xml->soil_moisture_1_day_high_time; /* High Soil Moisture 1 Time  */
}
if(isset($xml->soil_moisture_1_day_low)) {
  $WX['lowSoilMoist1'] = (string)$xml->soil_moisture_1_day_low; /* Low Soil Moisture 1   */
}
if(isset($xml->soil_moisture_1_day_low_time)) {
  $WX['lowSoilMoistTime1'] = (string)$xml->soil_moisture_1_day_low_time; /* Low Soil Moisture 1 Time  */
}
if(isset($xml->soil_moisture_2)) {
  $WX['SoilMoisture2'] = (string)$xml->soil_moisture_2; /* Soil Moisture 2   */
}
if(isset($xml->soil_moisture_2_day_high)) {
  $WX['hiSoilMoist2'] = (string)$xml->soil_moisture_2_day_high; /* High Soil Moisture 2  */
}
if(isset($xml->soil_moisture_2_day_high_time)) {
  $WX['hiSoilMoistTime2'] = (string)$xml->soil_moisture_2_day_high_time; /* High Soil Moisture 2 Time  */
}
if(isset($xml->soil_moisture_2_day_low)) {
  $WX['lowSoilMoist2'] = (string)$xml->soil_moisture_2_day_low; /* Low Soil Moisture 2   */
}
if(isset($xml->soil_moisture_2_day_low_time)) {
  $WX['lowSoilMoistTime2'] = (string)$xml->soil_moisture_2_day_low_time; /* Low Soil Moisture 2 Time  */
}
if(isset($xml->soil_moisture_3)) {
  $WX['SoilMoisture3'] = (string)$xml->soil_moisture_3; /* Soil Moisture 3   */
}
if(isset($xml->soil_moisture_3_day_high)) {
  $WX['hiSoilMoist3'] = (string)$xml->soil_moisture_3_day_high; /* High Soil Moisture 3   */
}
if(isset($xml->soil_moisture_3_day_high_time)) {
  $WX['hiSoilMoistTime3'] = (string)$xml->soil_moisture_3_day_high_time; /* High Soil Moisture 3 Time   */
}
if(isset($xml->soil_moisture_3_day_low)) {
  $WX['lowSoilMoist3'] = (string)$xml->soil_moisture_3_day_low; /* Low Soil Moisture 3   */
}
if(isset($xml->soil_moisture_3_day_low_time)) {
  $WX['lowSoilMoistTime3'] = (string)$xml->soil_moisture_3_day_low_time; /* Low Soil Moisture 3 Time   */
}
if(isset($xml->soil_moisture_4)) {
  $WX['SoilMoisture4'] = (string)$xml->soil_moisture_4; /* Soil Moisture 4   */
}
if(isset($xml->soil_moisture_4_day_high)) {
  $WX['hiSoilMoist4'] = (string)$xml->soil_moisture_4_day_high; /* High Soil Moisture 4   */
}
if(isset($xml->soil_moisture_4_day_high_time)) {
  $WX['hiSoilMoistTime4'] = (string)$xml->soil_moisture_4_day_high_time; /* High Soil Moisture 4 Time   */
}
if(isset($xml->soil_moisture_4_day_low)) {
  $WX['lowSoilMoist4'] = (string)$xml->soil_moisture_4_day_low; /* Low Soil Moisture 4  */
}
if(isset($xml->soil_moisture_4_day_low_time)) {
  $WX['lowSoilMoistTime4'] = (string)$xml->soil_moisture_4_day_low_time; /* Low Soil Moisture 4 Time   */
}
if(isset($xml->temp_soil_1)) {
  $WX['SoilTemp1'] = WLCOM_convertTemp((string)$xml->temp_soil_1,'F',$uomTemp); /* Soil Temperature 1   */
}
if(isset($xml->temp_soil_1_day_high)) {
  $WX['hiSoilTemp1'] = (string)$xml->temp_soil_1_day_high; /* High Soil Temp 1   */
}
if(isset($xml->temp_soil_1_day_high_time)) {
  $WX['hiSoilTempTime1'] = (string)$xml->temp_soil_1_day_high_time; /* High Soil Temp 1 Time  */
}
if(isset($xml->temp_soil_1_day_low)) {
  $WX['lowSoilTemp1'] = WLCOM_convertTemp((string)$xml->temp_soil_1_day_low,'F',$uomTemp); /* Low Soil Temp 1  */
}
if(isset($xml->temp_soil_1_day_low_time)) {
  $WX['lowSoilTempTime1'] = (string)$xml->temp_soil_1_day_low_time; /* Low Soil Temp 1 Time  */
}
if(isset($xml->temp_soil_2)) {
  $WX['SoilTemp2'] = WLCOM_convertTemp((string)$xml->temp_soil_2,'F',$uomTemp); /* Soil Temperature 2   */
}
if(isset($xml->temp_soil_2_day_high)) {
  $WX['hiSoilTemp2'] = (string)$xml->temp_soil_2_day_high; /* High Soil Temp 2   */
}
if(isset($xml->temp_soil_2_day_high_time)) {
  $WX['hiSoilTempTime2'] = (string)$xml->temp_soil_2_day_high_time; /* High Soil Temp 2 Time   */
}
if(isset($xml->temp_soil_2_day_low)) {
  $WX['lowSoilTemp2'] = WLCOM_convertTemp((string)$xml->temp_soil_2_day_low,'F',$uomTemp); /* Low Soil Temp 2  */
}
if(isset($xml->temp_soil_2_day_low_time)) {
  $WX['lowSoilTempTime2'] = (string)$xml->temp_soil_2_day_low_time; /* Low Soil Temp 2 Time   */
}
if(isset($xml->temp_soil_3)) {
  $WX['SoilTemp3'] = WLCOM_convertTemp((string)$xml->temp_soil_3,'F',$uomTemp); /* Soil Temperature 3   */
}
if(isset($xml->temp_soil_3_day_high)) {
  $WX['hiSoilTemp3'] = (string)$xml->temp_soil_3_day_high; /* High Soil Temp 3  */
}
if(isset($xml->temp_soil_3_day_high_time)) {
  $WX['hiSoilTempTime3'] = (string)$xml->temp_soil_3_day_high_time; /* High Soil Temp 3 Time   */
}
if(isset($xml->temp_soil_3_day_low)) {
  $WX['lowSoilTemp3'] = WLCOM_convertTemp((string)$xml->temp_soil_3_day_low,'F',$uomTemp); /* Low Soil Temp 3  */
}
if(isset($xml->temp_soil_3_day_low_time)) {
  $WX['lowSoilTempTime3'] = (string)$xml->temp_soil_3_day_low_time; /* Low Soil Temp 3 Time  */
}
if(isset($xml->temp_soil_4)) {
  $WX['SoilTemp4'] = WLCOM_convertTemp((string)$xml->temp_soil_4,'F',$uomTemp); /* Soil Temperature 4  */
}
if(isset($xml->temp_soil_4_day_high)) {
  $WX['hiSoilTemp4'] = (string)$xml->temp_soil_4_day_high; /* High Soil Temp 4  */
}
if(isset($xml->temp_soil_4_day_high_time)) {
  $WX['hiSoilTempTime4'] = (string)$xml->temp_soil_4_day_high_time; /* High Soil Temp 4 Time   */
}
if(isset($xml->temp_soil_4_day_low)) {
  $WX['lowSoilTemp4'] = WLCOM_convertTemp((string)$xml->temp_soil_4_day_low,'F',$uomTemp); /* Low Soil Temp 4  */
}
if(isset($xml->temp_soil_4_day_low_time)) {
  $WX['lowSoilTempTime4'] = (string)$xml->temp_soil_4_day_low_time; /* Low Soil Temp 4 Time   */
}

# -------- Xtra-_Leaf variables --------
if(isset($xml->leaf_wetness_1)) {
  $WX['LeafWetness1'] = (string)$xml->leaf_wetness_1; /* Leaf Wetness 1   */
}
if(isset($xml->leaf_wetness_1_day_high)) {
  $WX['hiLeaf1'] = (string)$xml->leaf_wetness_1_day_high; /* High Leaf Wetness 1   */
}
if(isset($xml->leaf_wetness_1_day_high_time)) {
  $WX['hiLeafTime1'] = (string)$xml->leaf_wetness_1_day_high_time; /* High Leaf Wetness 1 Time  */
}
if(isset($xml->leaf_wetness_1_day_low)) {
  $WX['lowLeaf1'] = (string)$xml->leaf_wetness_1_day_low; /* Low Leaf Wetness 1   */
}
if(isset($xml->leaf_wetness_1_day_low_time)) {
  $WX['lowLeafTime1'] = (string)$xml->leaf_wetness_1_day_low_time; /* Low Leaf Wetness 1 Time  */
}
if(isset($xml->leaf_wetness_2)) {
  $WX['LeafWetness2'] = (string)$xml->leaf_wetness_2; /* Leaf Wetness 2   */
}
if(isset($xml->leaf_wetness_2_day_high)) {
  $WX['hiLeaf2'] = (string)$xml->leaf_wetness_2_day_high; /* High Leaf Wetness 2   */
}
if(isset($xml->leaf_wetness_2_day_high_time)) {
  $WX['hiLeafTime2'] = (string)$xml->leaf_wetness_2_day_high_time; /* High Leaf Wetness 2 Time   */
}
if(isset($xml->leaf_wetness_2_day_low)) {
  $WX['lowLeaf2'] = (string)$xml->leaf_wetness_2_day_low; /* Low Leaf Wetness 2  */
}
if(isset($xml->leaf_wetness_2_day_low_time)) {
  $WX['lowLeafTime2'] = (string)$xml->leaf_wetness_2_day_low_time; /* Low Leaf Wetness Time 2   */
}
if(isset($xml->temp_leaf_1)) {
  $WX['LeafTemp1'] = WLCOM_convertTemp((string)$xml->temp_leaf_1,'F',$uomTemp); /* Leaf Temp 1   */
}
if(isset($xml->temp_leaf_1_day_high)) {
  $WX['hiLeafTemp1'] = (string)$xml->temp_leaf_1_day_high; /* High Leaf Temp 1   */
}
if(isset($xml->temp_leaf_1_day_high_time)) {
  $WX['hiLeafTempTime1'] = (string)$xml->temp_leaf_1_day_high_time; /* High Leaf Temp 1 Time  */
}
if(isset($xml->temp_leaf_1_day_low)) {
  $WX['lowLeafTemp1'] = WLCOM_convertTemp((string)$xml->temp_leaf_1_day_low,'F',$uomTemp); /* Low Leaf Temp 1  */
}
if(isset($xml->temp_leaf_1_day_low_time)) {
  $WX['lowLeafTempTime1'] = (string)$xml->temp_leaf_1_day_low_time; /* Low Leaf Temp 1 Time  */
}
if(isset($xml->temp_leaf_2)) {
  $WX['LeafTemp2'] = WLCOM_convertTemp((string)$xml->temp_leaf_2,'F',$uomTemp); /* Leaf Temp 2 Missing in WL defs */
}
if(isset($xml->temp_leaf_2_day_high)) {
  $WX['hiLeafTemp2'] = (string)$xml->temp_leaf_2_day_high; /* High Leaf Temp 2  Missing in WL defs */
}
if(isset($xml->temp_leaf_2_day_high_time)) {
  $WX['hiLeafTempTime2'] = (string)$xml->temp_leaf_2_day_high_time; /* High Leaf Temp 2 Time Missing in WL defs */
}
if(isset($xml->temp_leaf_2_day_low)) {
  $WX['lowLeafTemp2'] = WLCOM_convertTemp((string)$xml->temp_leaf_2_day_low,'F',$uomTemp); /* Low Leaf Temp 2 Missing in WL defs */
}
if(isset($xml->temp_leaf_2_day_low_time)) {
  $WX['lowLeafTempTime2'] = (string)$xml->temp_leaf_2_day_low_time; /* Low Leaf Temp 2 Time Missing in WL defs */
}


# --- end section created by gen-WLCOMtags.php ------------------------
# ---------------------------------------------------------------------
# these variables need adjustment after the $WX array is populated

$WX['windHigh5'] = $WX['windHigh10']; /* needed by -defs file */
if(isset($xml->thsw_index_day_high_f)) {
	$WX['thswHigh'] = WLCOM_convertTemp((string)$xml->thsw_index_day_high_f,'F',$uomTemp);
}
if(isset($xml->thsw_index_day_high_time)) {
	$WX['thswHighTime'] = (string)$xml->thsw_index_day_high_time;
}

if (isset($_REQUEST["sce"]) and strtolower($_REQUEST["sce"]) == "dump" ) {

  print "<pre>\n";
  print "// \$WX[] array size = ".count($WX)." entries.\n";
  foreach ($WX as $key => $val) {
	  print  "\$WX['$key'] = '$val';\n";
  }
  print "</pre>\n";
  return;
}
# -- save off data as 'yesterday' data for use tomorrow 
if (isset($saveYesterday) and $saveYesterday) {
	
	$WXYday = $WX;
	$uoms		= $uomTemp.$uomBaro.$uomWind.$uomRain.$uomSnow.$uomDistance.$uomPerHour;
  $from		= array('/',' ','&deg;','.php');
  $to		= '';
  $saveFile	= $SITE['cacheFileDir']. 
	str_replace ($from, $to, 
	 'weatherlinkcom-yday-'.$SITE['WLCOMdid'].'_'.$uoms.'.txt');  // add uoms to filename

//	$saveFile = $SITE['cacheFileDir'].'weatherlinkcom.yday.dat';
	if (!file_put_contents($saveFile, serialize($WXYday))){   
    echo $tagsWLCOM.": <br />Could not save YDay data (".$saveFile.
				  ") to cache. Please make sure your cache directory exists and is writable.".PHP_EOL;
  } else {
    echo $tagsWLCOM.": Yesterday data for ".date('Y-m-d')." saved at ".date('H:i:s T').
		     " to cache $saveFile".PHP_EOL;
  }
  return;
}

if ( isset($_REQUEST['ssg']) or 
    (isset($genSSG) and $genSSG) ) {
	$ssgJSON = WLCOM_genSSG($davis,$xml,$uomTemp,$uomWind,$uomBaro,$uomRain);
	header("Content-type: text/plain");
	header("Last-modified: ".gmdate('r', strtotime( (string)$davis->observation_time_rfc822) ) );
	print $ssgJSON;
	return;
}

if ( isset($_REQUEST['realtime']) or
    (isset($genRealtime) and $genRealtime) ) {
	$realtime = WLCOM_genRealtime($uomTemp,$uomWind,$uomBaro,$uomRain);
	header("Content-type: text/plain");
	header("Last-modified: ".gmdate('r', strtotime( (string)$davis->observation_time_rfc822) ) );
  print $realtime;
	return;
}

if(file_exists("WLCOM-defs.php")) { include_once("WLCOM-defs.php"); }
#
#-----------------------------------------------------------------------------
# support functions
#  adapted from Leuven wxFunctions.php by Wim
#-------------------------------------------------------------------------------------
#   converts (and translates) degrees to windlabels f.e. 2 degrees to North
function WLCOM_convertWindDir ($degrees) {
	global $doDebug;	
	$winddir = $degrees;
	if (!isset($winddir)) { 
		$return = "---"; 
	} elseif (!is_numeric($winddir)) { 
		$return = $winddir;
	} else {
		$windlabel = array ("N","NNE", "NE", "ENE", "E", "ESE", "SE", "SSE", "S",
		 "SSW","SW", "WSW", "W", "WNW", "NW", "NNW");
		$return = $windlabel[ fmod((($winddir + 11) / 22.5),16) ];
	}
	if ($doDebug) {
		echo "<!--  WLCOM_convertWindDir in = $degrees out = $return -->\n";
	}
	return $return;
} // eof WLCOM_convertWinddir
#
#-------------------------------------------------------------------------------------
#    Convert windspeed
function WLCOM_convertWind($amount, $usedunit,$reqUnit='') {
	global $SITE, $doDebug;
	$amount		=str_replace(',','.',$amount);
	$out 		= 0;	
	
	if ($reqUnit == '') {$toUnit = $SITE['uomWind'];} else {$toUnit = $reqUnit;}
	$repl = array ('/',' ','p');
	$with = array ('','','');
	$convertArr= array
			   ("kmh"=> array('kmh' => 1		, 'kts' => 0.5399568034557235	, 'ms' => 0.2777777777777778 	, 'mh' => 0.621371192237334 ),
				"kts"=> array('kmh' => 1.852	, 'kts' => 1 					, 'ms' => 0.5144444444444445 	, 'mh' => 1.1507794480235425),
				"ms" => array('kmh' => 3.6		, 'kts' => 1.9438444924406046	, 'ms' => 1 					, 'mh' => 2.236936292054402 ),
				"mh" => array('kmh' => 1.609344	, 'kts' => 0.8689762419006479	, 'ms' => 0.44704 				, 'mh' => 1 ));
	$from 	= trim(str_replace ($repl,$with,strtolower($usedunit)));
	$to   	= trim(str_replace ($repl,$with,strtolower($toUnit)));
	$error	= 'invalid UOM';
	if (($from ==='kmh') || ($from === 'kts') || ($from === 'ms') || ($from === 'mh')) {
		if (($to ==='kmh') || ($to === 'kts') || ($to === 'ms') || ($to === 'mh')) {
			$out = $convertArr[$from][$to];
			$error	= '';
			}  
	}
	if(is_numeric($out) and is_numeric($amount)) {
	  $return 	= round($out*$amount,1);
	} else {
		$return   = 0;
	}
	if ($doDebug) {
		echo "<!--  WLCOM_convertWind in = speed:$amount , unitFrom: $usedunit ,unitTo: $reqUnit, out = $return -->\n";
		if ($error <> '') {echo "<!-- ========== $error ============== -->".PHP_EOL;}
	}	
	return sprintf("%01.1f",$return);
} // eof convert windspeed
#
#-------------------------------------------------------------------------------------
#    Convert baro pressure
function WLCOM_convertBaro($amount, $usedunit,$reqUnit='') {
	global $SITE, $doDebug;
	$amount		= str_replace(',','.',$amount);
	$out		= 0;	
	if ($reqUnit == '') {$toUnit = $SITE['uomBaro'];} else {$toUnit = $reqUnit;}
	$repl		= array ('/',' ');
	$with		= array ('','');
	$convertArr	= array
			   ("mb" 	=> array('mb' => 1		, 'hpa' => 1 , 		'mmhg' => 0.75006 	, 'inhg' => 0.02953 ),
				"hpa"	=> array('mb' => 1		, 'hpa' => 1 , 		'mmhg' => 0.75006 	, 'inhg' => 0.02953),
				"mmhg"	=> array('mb' => 1.3332	, 'hpa' => 1.3332 , 'mmhg' => 1 		, 'inhg' => 0.03937 ),
				"inhg"	=> array('mb' => 33.864	, 'hpa' => 33.864 , 'mmhg' => 25.4 		, 'inhg' => 1));
	$from 		= trim(strtolower(str_replace ($repl,$with,$usedunit)));
	$to   		= trim(strtolower(str_replace ($repl,$with,$toUnit)));
	$error	= 'invalid UOM';
	if ($from ==='in')  {$from ='inhg';}
	if (($from ==='mb') || ($from === 'hpa') || ($from === 'mmhg') || ($from === 'inhg')  ) {
		if (($to ==='mb') || ($to === 'hpa') || ($to === 'mmhg') || ($to === 'inhg')) {
			$out = $convertArr[$from][$to];
			$error	= '';
		}  
	}
	if ($to == "hpa" || $to == "mb" ) {
		$return	= round($out*(float)$amount,1);
	} else {
		$return	= round($out*(float)$amount,2);
	}
	if ($doDebug) {
		echo "<!--  WLCOM_convertBaro in = pressure:$amount , unitFrom: $usedunit ,unitTo: $toUnit, out = $return -->\n";
		if ($error <> '') {echo "<!-- ========== $error ============== -->".PHP_EOL;}		
	}
	if($to == 'inhg') {
	  return sprintf("%01.2f",$return);
	} else {
	  return sprintf("%01.1f",$return);
	}
} // eof convert baropressure
#
#-------------------------------------------------------------------------------------
#    Convert rainfall
function WLCOM_convertRain($amount, $usedunit,$reqUnit='') {
	global $SITE, $doDebug;
	$amount		=str_replace(',','.',$amount);
	$out 		= 0;	
	if ($reqUnit == '') {$toUnit = $SITE['uomRain'];} else {$toUnit = $reqUnit;}
	$repl 		= array ('/',' ');
	$with 		= array ('','');
	$convertArr	= array
			   ("mm"=> array('mm' => 1		,'in' => 0.03937007874015748 	, 'cm' => 0.1 ),
				"in"=> array('mm' => 25.4	,'in' => 1						, 'cm' => 2.54),
				"cm"=> array('mm' => 10		,'in' => 0.3937007874015748 	, 'cm' => 1 )
				);
	$from 	= trim(strtolower(str_replace ($repl,$with,$usedunit)));
	$to   	= trim(strtolower(str_replace ($repl,$with,$toUnit)));
	$error	= 'invalid UOM';
	if ((  $from ==='mm') || ($from === 'in') || ($from === 'cm')) {
		if (($to ==='mm') ||   ($to === 'in') ||   ($to === 'cm')) {
			$out = $convertArr[$from][$to];
			$error = '';
		}  
	}
	if ($to == 'mm') {
		$return	= round($out*$amount,1);
	} else {
		$return	= round($out*$amount,3);
	}	
	if ($doDebug) {
		echo "<!--  WLCOM_convertRain in = rainfall: $amount , unitFrom: $usedunit ,unitTo: $reqUnit, out = $return -->\n";
		if ($error <> '') {echo "<!-- ========== $error ============== -->".PHP_EOL;}
	}
	if($to == 'in') {
	  return sprintf("%01.2f",$return);
	} else {
	  return sprintf("%01.1f",$return);
	}
} // eof convert rainfall
#
#-------------------------------------------------------------------------------------
#    Convert temperature rate of change so 9F = 5C
function WLCOM_convertTempRate($amount, $usedunit,$reqUnit='') {
	global $SITE, $doDebug;
	if (isset ($amount)) {
		$amount	= str_replace(',','.',$amount);
		$out 	= $amount*1.0;
	} else {
		$out	= 0;
	}
	if ($reqUnit == '') {$toUnit = $SITE['uomTemp'];} else {$toUnit = $reqUnit;}
	$repl 	= array ('/',' ','&deg;','elsius','¡');
	$with 	= array ('','','','','');
	$from 	= trim(strtolower(str_replace ($repl,$with,$usedunit)));
	$to   	= trim(strtolower(str_replace ($repl,$with,$toUnit)));
	if ($from == $to) {$return	= $out;}
	elseif (($from == 'c') && ($to = 'f')) {$out = 9*$amount/5;}
	elseif (($from == 'f') && ($to = 'c')) {$out = 5*$amount/9;}
	else { $error	= 'invalid UOM';}
	$return = round($out,1);
	if ($doDebug) {
		echo "<!--  WLCOM_convertTemp in = temperature: $amount , unitFrom: $usedunit ,unitTo: $reqUnit, out = $return -->\n";
		if (isset($error)) {echo "<!-- ========== $error invalid UOM -'.$usedunit.'-WLCOM_convertTempRate ============== -->".PHP_EOL;}
	}
	return sprintf("%01.1f",$return);
} // eof convert temperature
#
#-------------------------------------------------------------------------------------
#    Convert temperature and clean up input
function WLCOM_convertTemp($amount, $usedunit,$reqUnit='') {
#echo '<!-- $amount = '.$amount.' $usedunit = '.$usedunit.' $reqUnit = '.$reqUnit.'-->'.PHP_EOL;
	global $SITE, $doDebug;
	if (isset ($amount)) {
		$amount	= str_replace(',','.',$amount);
		$out 	= $amount*1.0;
	} else {
		$out	= 0;
	}
	if ($reqUnit == '') {$toUnit = $SITE['uomTemp'];} else {$toUnit = $reqUnit;}
	$repl 	= array ('&#176;','/',' ','&deg;','elsius','¡C');
	$with 	= array (''      ,     '' ,'' ,''     ,''      ,'c');
	$from 	= trim(strtolower(str_replace ($repl,$with,$usedunit)));
	$to   	= trim(strtolower(str_replace ($repl,$with,$toUnit)));
	if ($from == $to) {$return	= $out;}
	elseif (($from == 'c') && ($to = 'f')) {$out = 32 +(9*$amount/5);}
	elseif (($from == 'f') && ($to = 'c')) {$out = 5*($amount -32)/9;}
	else { $error	= 'invalid UOM';}
	$return = round($out,1);
	if ($doDebug) {
		if ($amount == '---') {$amount = '- - -';}
		echo "<!--  WLCOM_convertTemp in = temperature: $amount , unitFrom: $usedunit ,unitTo: $reqUnit, out = $return -->\n";
		if (isset($error)) {echo "<!-- ========== $error ============== -->".PHP_EOL;}
	}
	return sprintf("%01.1f",$return);
} // eof convert temperature
#
#-------------------------------------------------------------------------------------
#    Convert distance
function WLCOM_convertDistance($amount, $usedunit,$reqUnit='') {
	global $SITE, $doDebug;
	if (isset ($amount)) {
		$amount	=str_replace(',','.',$amount);
		$out 	= ((int)$amount)*1.0;
	} else {
		$out=0;
	}
	if ($reqUnit == '') {$toUnit = $SITE['uomDistance'];} else {$toUnit = $reqUnit;}
	$repl 	= array ('/',' ');
	$with 	= array ('','');
	$from 	= trim(strtolower(str_replace ($repl,$with,$usedunit)));
	$to   	= trim(strtolower(str_replace ($repl,$with,$toUnit)));
	$error	= 'invalid UOM';
	$convertArr= array  (
		"km"	=> array('km' => 1			, 'mi' => 0.621371192237	, 'nmi' => 0.540	, 'ft' => 3280.83989501 , 'm' => 1000 ),
		"mi"	=> array('km' => 1.609344000000865	, 'mi' => 1			, 'nmi' => 0.869	, 'ft' => 5280		, 'm' => 1609.344000000865 ),
		"nmi"	=> array('km' => 1.852			, 'mi' => 1.151			, 'nmi' => 1		, 'ft' => 6076.115	, 'm' => 1852 ),
		"ft"	=> array('km' => 0.0003048		, 'mi' => 0.000189393939394	, 'nmi' => 0.000165	, 'ft' => 1		, 'm' => 0.30480000000029017 ),
		"m"	=> array('km' => 0.001			, 'mi' => 0.000621371192237	, 'nmi' => 0.000540	, 'ft' => 3.28083989501 , 'm' => 1 )
		
	);
	if (($from ==='km') || ($from === 'mi') || ($from === 'ft') || ($from === 'm') || ($from === 'nmi') ) {
		if (($to ==='km') || ($to === 'mi') || ($to === 'ft') || ($to === 'm') || ($to === 'nmi') ) {
			$out = $convertArr[$from][$to];
			$error	= '';
			}  
	}      // invalid unit
	$return = round($out*$amount,1);
	if ($doDebug) {
		echo "<!--  WLCOM_convertDistance in = distance: $amount , unitFrom: $usedunit ,unitTo: $reqUnit ($toUnit), factor = $out ($return) -->\n";
		if ($error <> '') {echo "<!-- ========== $error ============== -->".PHP_EOL;}
	}	
	return $return;
} // eof convert distance
#
#-------------------------------------------------------------------------------------
#  convert knots to beaufort number
function WLCOM_getBeaufort ($rawwind) {
   global $Debug;
	 $WINDkts = round($rawwind);
// rawwind in Knots  
// return a number for the beaufort scale based on wind in knots
  if ($WINDkts < 1 ) {return(0); }
  if ($WINDkts < 4 ) {return(1); }
  if ($WINDkts < 7 ) {return(2); }
  if ($WINDkts < 11 ) {return(3); }
  if ($WINDkts < 17 ) {return(4); }
  if ($WINDkts < 22 ) {return(5); }
  if ($WINDkts < 28 ) {return(6); }
  if ($WINDkts < 34 ) {return(7); }
  if ($WINDkts < 41 ) {return(8); }
  if ($WINDkts < 48 ) {return(9); }
  if ($WINDkts < 56 ) {return(10); }
  if ($WINDkts < 64 ) {return(11); }
  if ($WINDkts >= 64 ) {return(12); }
  return("0");
} // end getBeaufortNumber

#
#-------------------------------------------------------------------------------------
#    Calculate Humidex
#

function WLCOM_calcHumidex ($temp,$humidity,$useunit) {
// Calculate Humidex from temperature (in C) and humidity
// Source of calculation: http://www.physlink.com/reference/weather.cfm	
  global $Debug;
	$T = $temp;
  $H = $humidity;
  
  $t=7.5*$T/(237.7+$T);
  $et=pow(10,$t);
  $e=6.112*$et*($H/100);
  $humidex=$T+(5/9)*($e-10);
  if ($humidex < $T) {
	 $humidex=$T;
     $Debug .= " set to T, ";
  }
  if(preg_match('|F|i',$useunit)) {
     # convert to F
     $humidex = sprintf("%01.1f",round((1.8 * $humidex) + 32.0,1));	  
  }
  $humidex = round($humidex,1);
  return($humidex);	
}


#
#-------------------------------------------------------------------------------------
#    Generate SteelSeries Gauges JSON file
#
function WLCOM_genSSG($davis,$xml,$uomTemp,$uomWind,$uomBaro,$uomRain) {
	global $WX,$SITE,$doDebug;

	$JSONdata = array();
	
	$JSONdata["date"] 	    = $WX['time']; // (this is really 'time') WD Sample= '3:39 PM'
	$JSONdata["dateFormat"] = ($SITE['WDdateMDY'])?'m/d/y':'d/m/y'; // WD Sample= 'm/d/y'
	$JSONdata["temp"] 	    = $WX['outsideTemp']; // WD Sample= '64.7°F'
	$JSONdata["tempTL"]     = $WX['lowOutsideTemp']; // WD Sample= '34.4°F'
	$JSONdata["tempTH"]     = $WX['hiOutsideTemp']; // WD Sample= '64.7°F'
	$JSONdata["intemp"]     = $WX['insideTemp']; // WD Sample= '73.2'
	$JSONdata["dew"] 	      = $WX['outsideDewPt']; // WD Sample= '34.9°F'
	$JSONdata["dewpointTL"] = $WX['lowDewpoint']; // WD Sample= '30.1 °F'
	$JSONdata["dewpointTH"] = $WX['hiDewpoint']; // WD Sample= '40.8 °F'
	// Note: Meteobridge does not provide apparent temperature .. we substitute Humidex
	$humidex = WLCOM_calcHumidex((string)$davis->temp_c,$WX['outsideHumidity'],$uomTemp);
	$JSONdata["apptemp"]    = $humidex; // WD Sample= '63.4'
	$JSONdata["apptempTL"]  = $humidex; // WD Sample= '32.2'
	$JSONdata["apptempTH"]  = $humidex; // WD Sample= '72.3'
	$JSONdata["wchill"]     = $WX['windChill']; // WD Sample= '64.7°F'
	$JSONdata["wchillTL"]   = $WX['lowWindchill']; // WD Sample= '34.4 °F'
	$JSONdata["heatindex"]  = $WX['outsideHeatIndex']; // WD Sample= '64.7°F'
	$JSONdata["heatindexTH"] = $WX['hiHeatindex']; // WD Sample= '64.7 °F'
	$JSONdata["humidex"]    = $humidex; // WD Sample= '61.6°F'
	$JSONdata["wlatest"]    = $WX['windSpeed']; // WD Sample= '0.0 mph'
	$JSONdata["wspeed"]     = $WX['windAvg10']; // WD Sample= '0.4 mph'
	$JSONdata["wgust"]      = $WX['hiWindSpeed']; // WD Sample= '7.0 mph'
	$JSONdata["wgustTM"]    = $WX['hiWindSpeed']; // WD Sample= '11.0 mph'
	$JSONdata["bearing"]    = round($WX['windDir']); // WD Sample= '292 °'
	$JSONdata["avgbearing"] = round($WX['windDir']); // WD Sample= '311°'
	$JSONdata["press"]      = $WX['barometer']; // WD Sample= '30.138 in.'
	$JSONdata["pressTL"]    = $WX['lowBarometer']; // WD Sample= '30.124 in.'
	$JSONdata["pressTH"]    = $WX['hiBarometer']; // WD Sample= '30.229 in.'
	$JSONdata["pressL"]     = $WX['lowYearlyBarometer']; // WD Sample= '26.001'
	$JSONdata["pressH"]     = $WX['hiYearlyBarometer']; // WD Sample= '30.569'
	$JSONdata["rfall"]      = $WX['dailyRain']; // WD Sample= '0.00 in.'
	$JSONdata["rrate"]      = $WX['rainRate']; // WD Sample= '0.00'
	$JSONdata["rrateTM"]    = $WX['hiRainRate']; // WD Sample= '0.000'
	$JSONdata["hum"]        = $WX['outsideHumidity']; // WD Sample= '33'
	$JSONdata["humTL"]      = $WX['lowHumidity']; // WD Sample= '31'
	$JSONdata["humTH"]      = $WX['hiHumidity']; // WD Sample= '86'
	$JSONdata["inhum"]      = $WX['insideHumidity']; // WD Sample= '32'

  $JSONdata["inhumTL"]    = $WX['lowInsideHumidity']; // Ver 12
  $JSONdata["inhumTH"]    = $WX['hiInsideHumidity'];  // Ver 12

	$JSONdata["SensorContactLost"] = '0'; // WD Sample= '0'
	$JSONdata["forecast"]   = 'Conditions updated: '.$WX['time']; // WD Sample= 'increasing clouds and warmer. precipitation possible within 12 to 24 hrs. windy.'
	$JSONdata["tempunit"]   = $uomTemp; // WD Sample= 'F'
	$JSONdata["windunit"]   = $uomWind; // WD Sample= 'mph'
	$JSONdata["pressunit"]  = $uomBaro; // WD Sample= 'inHg'
	$JSONdata["rainunit"]   = $uomRain; // WD Sample= 'in'
	$JSONdata["temptrend"]  = '0.0'; // WD Sample= '+1.0 °F/last hr'
	$JSONdata["TtempTL"]    = $WX['lowOutsideTempTime']; // WD Sample= '7:40 AM'
	$JSONdata["TtempTH"]    = $WX['hiOutsideTempTime']; // WD Sample= '3:19 PM'
	$JSONdata["TdewpointTL"] = $WX['lowDewpointTime']; // WD Sample= '7:40 AM'
	$JSONdata["TdewpointTH"] = $WX['hiDewpointTime']; // WD Sample= '9:16 AM'
	$JSONdata["TapptempTL"] = '00:00'; // WD Sample= '7:13 AM'
	$JSONdata["TapptempTH"] = '00:00'; // WD Sample= '1:14 PM'
	$JSONdata["TwchillTL"]  = $WX['lowWindchillTime']; // WD Sample= '3:19 PM'
	$JSONdata["TheatindexTH"] = 'n/a'; // WD Sample= '3:19 PM'
	$JSONdata["TrrateTM"]   = '00:00'; // WD Sample= '00:00 AM'
	$JSONdata["ThourlyrainTH"] = '00:00'; // WD Sample= ''
	$JSONdata["LastRainTipISO"] = 'n/a'; // WD Sample= '1/12/2013 4:12 AM'
	$JSONdata["hourlyrainTH"]  = '0.0'; // WD Sample= '0.000'
	$JSONdata["ThumTL"]     = $WX['lowHumTime']; // WD Sample= '3:22 PM'
	$JSONdata["ThumTH"]     = $WX['hiHumTime']; // WD Sample= '8:05 AM'

  $JSONdata["TinhumTL"]      = $WX['lowInsideHumidityTime']; // Ver 12
  $JSONdata["TinhumTH"]      = $WX['hiInsideHumidityTime'];  // Ver 12

	$JSONdata["TpressTL"]   = $WX['lowBarometerTime']; // WD Sample= '2:18 PM'
	$JSONdata["TpressTH"]   = $WX['hiBarometerTime']; // WD Sample= '10:09 AM'
	$JSONdata["presstrendval"] = WLCOM_convertBaro((string)$davis->pressure_tendency_in,'inhg',$uomBaro); // WD Sample= '-0.019 in. '
	$JSONdata["Tbeaufort"]  = 'F'.WLCOM_getBeaufort($davis->wind_kt); // WD Sample= '3'
	$JSONdata["TwgustTM"]   = $WX['hiWindSpeedTime']; // WD Sample= '2:19 PM'
	$JSONdata["windTM"]     = $WX['hiWindSpeed']; // WD Sample= '6.2 mph'
	$JSONdata["bearingTM"]  = $WX['windDir']; // WD Sample= '315'
	$fixedTimestamp = strtotime((string)$davis->observation_time_rfc822);
	$JSONdata["timeUTC"]    = gmdate('Y,m,d,H,i,s',$fixedTimestamp); // WD Sample= '2013,01,20,23,39,59'
	$JSONdata["BearingRangeFrom10"] = '359'; // WD Sample= '289°'
	$JSONdata["BearingRangeTo10"] = '0'; // WD Sample= '6°'
	$JSONdata["UV"]         = isset($WX['uv'])?$WX['uv']:'0.0'; // WD Sample= '0.7'

  $JSONdata["UVTH"]       = isset($WX['hiUV'])?$$WX['hiUV']:'0.0'; // Ver 12

	$JSONdata["SolarRad"]   = isset($WX['solarRad'])?$WX['solarRad']:'0'; // WD Sample= '267'
	$JSONdata["CurrentSolarMax"] = isset($WX['solarRad'])?$WX['solarRad']:'0'; // WD Sample= '238'
	$JSONdata["SolarTM"]    = isset($WX['hiSolarRad'])?$WX['hiSolarRad']:0; // WD Sample= '560'
	$JSONdata["domwinddir"] = $WX['windDirection']; // WD Sample= 'Northwesterly'
	$JSONdata["WindRoseData"] = '[0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0]';
	// Note: Meteobridge does not collect/publish this windrose data 
	// a WD Sample='[22.0,23.0,7.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,0.0,3.0,233.0,139.0]'
	// $uomWindRun = (preg_match('|C|',$uomTemp))?'km/h':'mph';
	$JSONdata["windrun"]    = '0.0'; // new in ver=9 -- WLCOM does not collect windrun

  $JSONdata["cloudbasevalue"]     = '0'; // Ver 12 - cloudbase not available
  $JSONdata["cloudbaseunit"]      = 'm'; // Ver 12

	$JSONdata["version"]    = '6'; // WD Sample= '10.37R'
	$JSONdata["build"]      = '0.3'; // WD Sample= '45'
	$JSONdata["ver"]        = "12"; // constant);
	
	// JSON assembly done.  Output the JSON file+status
	if($doDebug) {
		print "<pre>\n";
		} else {
		header("Content-Type: text/plain; charset=ISO-8859-1");
		}
	$out = '';
	
	$out .= '{';
	$comma = '';
	foreach ($JSONdata as $key => $val) {
		$out .= $comma;
		$out .= "\"$key\":\"$val\"";
		$comma = ",\n";
	}
	$out .= "}\n";
  return $out;
	
}

#
#-------------------------------------------------------------------------------------
#    generate the WLrealtime.txt file
#
function WLCOM_genRealtime($uomTemp,$uomWind,$uomBaro,$uomRain) {
	global $WX;
	$rtvars = array(
		'date',
		'time',
		'outsideTemp',
		'outsideHumidity',
		'outsideDewPt',
		'wind10Avg',
		'windSpeed',
		'windDir',
		'rainRate',
		'dailyRain',
		'barometer',
		'windDirection',
		'-',
		$uomWind, //'windUnit',
		$uomTemp, //'tempUnit',
		$uomBaro, //'barUnit',
		$uomRain, // 'rainUnit',
		'-',
		'BarTrend',
		'monthlyRain',
		'totalRain',
		'-',
		'insideTemp',
		'insideHumidity',
		'windChill',
		'-',
		'hiOutsideTemp',
		'hiOutsideTempTime',
		'lowOutsideTemp',
		'lowOutsideTempTime',
		'hiWindSpeed',
		'hiWindSpeedTime',
		'hiWindSpeed',
		'-',
		'hiBarometer',
		'hiBarometerTime',
		'lowBarometer',
		'lowBarometerTime',
		'-',
		'-',
		'windHigh10',
		'outsideHeatIndex',
		'outsideHeatIndex',
		'uv',
		'dailyEt',
		'solarRad',
		'-',
		'-',
		'-',
		'-',
		'-',
		'-',
		'-',
		'-',
  );
	
	$tarray = array();
	foreach ($rtvars as $i => $varname) {
		if(isset($WX[$varname])) {
			$tarray[$i] = $WX[$varname];
		} else {
			$tarray[$i] = $varname;
		}
	}
	
	return(join('|',$tarray));
	
}

#
#  end WLCOMtags.php
