<?php
/*
 File: WLCOM-defs.php

 Purpose: provide a bridge to naming of weather variables from the WeatherLink.com to
          Weather-Display names used in common scripts like ajax-dashboard.php and ajax-gizmo.php

 
 Author: Ken True - webmaster@saratoga-weather.org


//Version WLCOM-defs.php - V1.01 - 09-Jan-2018

*/
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
$WXsoftware = 'WL';  
// this has WD $varnames = $WX['WL-varnames']; equivalents
 
$uomtemp = $WX['tempUnit'];
$uombaro = $WX['barUnit'];
$uomwind = $WX['windUnit'];
$uomrain = $WX['rainUnit'];
$time = $WX['time'];
$date = $WX['date'];
$sunrise = $WX['sunriseTime'];
$sunset = $WX['sunsetTime'];
#$moonphasename = $WX['MoonPhaseStr'];
$stationaltitude = $WX['StationElevation'];
$stationlatitude = $WX['StationLatitude'];
$stationlongitude = $WX['StationLongitude'];
$noaacityname = $WX['StationCity'];
$temperature = $WX['outsideTemp'];
$tempnodp  = round(preg_replace('|,|','.',$WX['outsideTemp']),0); // calculated value
$humidity = $WX['outsideHumidity'];
$dewpt = $WX['outsideDewPt'];
$maxtemp = $WX['hiOutsideTemp'];
$maxtempt = $WX['hiOutsideTempTime'];
$mintemp = $WX['lowOutsideTemp'];
$mintempt = $WX['lowOutsideTempTime'];
$heati = $WX['outsideHeatIndex'];
$windch = $WX['windChill'];
$avgspd = $WX['windSpeed'];
$gstspd = $WX['windHigh5'];
$maxgst = $WX['hiWindSpeed'];
$maxgstt = $WX['hiWindSpeedTime'];
$dirdeg = $WX['windDir'];
$dirlabel = $WX['windDirection'];
$baro = $WX['barometer'];
$pressuretrendname = $WX['BarTrend'];
$pressuretrendname3hour = $WX['BarTrend'];
if(isset($WX['ForecastStr'])) {$vpforecasttext = $WX['ForecastStr'];}
$dayrn = $WX['dailyRain'];
$monthrn = $WX['monthlyRain'];
$yearrn = $WX['totalRain'];
$currentrainratehr = $WX['rainRate'];
$maxrainrate = $WX['hiRainRate'];
$maxrainratehr = $WX['hiRainRateHour'];
if(isset($WX['hiRainRateTime'])) {$maxrainratetime = $WX['hiRainRateTime'];}
$vpstormrain = $WX['stormRain'];
$VPsolar = $WX['solarRad'];
$VPuv = $WX['uv'];
if(isset($WX['hiSolarRad'])) {$highsolar = $WX['hiSolarRad'];}
if(isset($WX['hiUV'])) {$highuv = $WX['hiUV'];}
if(isset($WX['hiSolarRadTime'])) {$highsolartime = $WX['hiSolarRadTime'];}
if(isset($WX['hiUVTime'])) {$highuvtime = $WX['hiUVTime'];}
$mrecordwindgust = $WX['hiMonthlyWindSpeed'];
$VPet = $WX['dailyEt'];
$VPetmonth = $WX['monthlyEt'];
$highbaro = $WX['hiBarometer'];
$highbarot = $WX['hiBarometerTime'];
$minwindch = $WX['lowWindchill'];
$minwindcht = $WX['lowWindchillTime'];
$mrecordhighbaro = $WX['hiYearlyBarometer'];
$mrecordhightemp = $WX['hiMonthlyOutsideTemp'];
$mrecordlowchill = $WX['lowMonthlyWindchill'];
$mrecordlowtemp = $WX['lowMonthlyOutsideTemp'];
$yrecordhighbaro = $WX['hiYearlyBarometer'];
$yrecordhightemp = $WX['hiYearlyOutsideTemp'];
$yrecordlowchill = $WX['lowYearlyWindchill'];
$yrecordlowtemp = $WX['lowYearlyOutsideTemp'];
$yrecordwindgust = $WX['hiYearlyWindSpeed'];
$yrecordlowbaro = $WX['lowYearlyBarometer'];
$maxdew = $WX['hiDewpoint'];
$maxdewt = $WX['hiDewpointTime'];
$mindew = $WX['lowDewpoint'];
$mindewt = $WX['lowDewpointTime'];
$mrecordhighdew = $WX['hiMonthlyDewpoint'];
$mrecordlowdew = $WX['lowMonthlyDewpoint'];
$yrecordhighdew = $WX['hiYearlyDewpoint'];
$yrecordlowdew = $WX['lowYearlyDewpoint'];
$maxheat = $WX['hiHeatindex'];
$maxheatt = $WX['hiHeatindexTime'];
$mrecordhighheatindex = $WX['hiMonthlyHeatindex'];
$yrecordhighheatindex = $WX['hiYearlyHeatindex'];
$lowbaro = $WX['lowBarometer'];
$lowbarot = $WX['lowBarometerTime'];
$monthtodatemaxbaro = $WX['hiMonthlyBarometer'];
$monthtodateminbaro = $WX['lowMonthlyBarometer'];
$mrecordhighsolar = $WX['hiMonthlySolarRad'];
$yrecordhighsolar = $WX['hiYearlySolarRad'];
$mrecordhighuv = $WX['hiMonthlyUV'];
$yrecordhighuv = $WX['hiYearlyUV'];
$highhum = $WX['hiHumidity'];
$highhumt = $WX['hiHumTime'];
$lowhum = $WX['lowHumidity'];
$lowhumt = $WX['lowHumTime'];
$mrecordhighhum = $WX['hiMonthlyHumidity'];
$mrecordlowhum = $WX['lowMonthlyHumidity'];
$yrecordhighhum = $WX['hiYearlyHumidity'];
$yrecordlowhum = $WX['lowYearlyHumidity'];
$yrecordrainrate = $WX['hiYearlyRainRate'];
$mrecordrainrate = $WX['hiMonthlyRainRate'];

// end of generation script
# WL unique functions included from WL-functions-inc.txt 
#-------------------------------------------------------------------------------------
# function processed WD variables
#-------------------------------------------------------------------------------------

if(isset($SITE['conditionsMETAR'])) { // override with METAR conditions for text and icon if requested.
	global $SITE;
	include_once("get-metar-conditions-inc.php");
	list($Currentsolardescription,$iconnumber) = mtr_conditions($SITE['conditionsMETAR'], $time, $sunrise, $sunset);
    if(isset($currentrainratehr) and 
      (!isset($SITE['overrideRain']) or (isset($SITE['overrideRain']) and $SITE['overrideRain'])) ) {
	  list($Currentsolardescription,$iconnumber) = 
	  WL_RainRateIcon($Currentsolardescription,$iconnumber,$currentrainratehr,$uomrain,$time,$sunrise,$sunset);
    }
}
# generate the separate date/time variables by dissection of input date/time and format
list($date_year,$date_month,$date_day,$time_hour,$time_minute,$monthname,$dayname)
  = WL_setDateTimes($date,$time,$SITE['WDdateMDY']);

$beaufortnum =  WL_beaufortNumber($WX['windSpeed'],$WX['windUnit']);
$bftspeedtext = WL_beaufortText($beaufortnum);

list($chandler,$chandlertxt,$chandlerimg) = WL_CBI($temperature,$uomtemp,$humidity);

if(!isset($wdversion) and isset($SITE['WXsoftwareVersion'])) {$wdversion = $SITE['WXsoftwareVersion']; }

list($feelslike,$heatcolourword) = WL_setFeelslike ($temperature,$windch,$heati,$uomtemp);

	$uoms		= $uomTemp.$uomBaro.$uomWind.$uomRain.$uomSnow.$uomDistance.$uomPerHour;
  $from		= array('/',' ','&deg;','.php');
  $to		= '';
  $ydayFile	= $SITE['cacheFileDir']. 
	 str_replace ($from, $to, 
	 'weatherlinkcom-yday-'.$SITE['WLCOMdid'].'_'.$uoms.'.txt');  // add uoms to filename

if(file_exists($ydayFile)) {
	$WXYday = unserialize(file_get_contents($ydayFile));
  $maxtempyest   = $WXYday['hiOutsideTemp'];
  $maxtempyestt  = $WXYday['hiOutsideTempTime'];
  $mintempyest   = $WXYday['lowOutsideTemp'];
  $mintempyestt  = $WXYday['lowOutsideTempTime'];
  $yesterdayrain = $WXYday['dailyRain'];
}

#-------------------------------------------------------------------------------------
# WL support function - WL_RainRateIcon
#-------------------------------------------------------------------------------------

function WL_RainRateIcon($inText,$inIcon,$inRate,$inUOM,$time,$sunrise,$sunset,$lastRain='0000-00-00T00:00:00') {
   global $Debug;
   
/*
Rainfall intensity is classified according to the rate of precipitation:

    Light rain — rate is < 2.5 mm (0.098 in) per hour
    Moderate rain — rate is between 2.5 mm (0.098 in) - 7.6 mm (0.30 in) or 10 mm (0.39 in) per hour
    Heavy rain — rate is > 7.6 mm (0.30 in) per hour, or between 10 mm (0.39 in) and 50 mm (2.0 in) per hour
    Violent rain — rate is > 50 mm (2.0 in) per hour
*/	
   $Debug .= "<!-- WL_RainRateIcon in='$inText' icon='$inIcon' rate='$inRate' uom='$inUOM' -->\n";
   $newText = '';  // assume no changes
   $newIcon = $inIcon;
   
   $rate = $inRate;
   if(preg_match('|in|i',$inUOM)) { // convert to mm/hr rate
     $rate = $inRate * 25.4;
   }
   
   if(substr($lastRain,0,4) <> '0000') {
	  if($rate < 0.001 and time()-strtotime($lastRain) < 30*60) {
		 $newText = 'Moderate Drizzle';
	  }
   }

   if ($rate > 0.0 and $rate < 2.5) { $newText = 'Light Rain'; }
   if ($rate >=2.5 and $rate < 7.6) { $newText = 'Moderate Rain'; }
   if ($rate >=7.6 and $rate < 50.0) { $newText = 'Heavy Rain'; }
   if ($rate >= 50.0)         { $newText = 'Violent Rain'; }
   
   if($newText <> '' or $rate == 0.0) {
	   if ($newText <> '' and $inText <> '') {$newText .= ', ';}
	   $newText .= 
	      preg_replace('/(Light|Moderate|Heavy|Violent|Extreme){0,1}\s*(Rain|Mist|Drizzle), /i','',$inText);
	   $newIcon = mtr_get_iconnumber ($time,$newText,$sunrise,$sunset); 
   } else {
	   $newText = $inText;
   }
   $Debug .= "<!-- WL_RainRateIcon out='$newText' icon='$newIcon' rate='$rate' mm/hr -->\n";
   return(array($newText,$newIcon));
}

#-------------------------------------------------------------------------------------
# WL support function - WLfixupTime
#-------------------------------------------------------------------------------------

function WLfixupTime ($intime) {
  global $Debug;
  $tfixed = preg_replace('/^(\S+)\s+(\S+)$/is',"$2",$intime);
  $t = explode(':',$tfixed);
  if (preg_match('/p/i',$tfixed)) { $t[0] = $t[0] + 12; }
  if ($t[0] > 23) {$t[0] = 12; }
  if (preg_match('/^12.*a/i',$tfixed)) { $t[0] = 0; }
  if ($t[0] < '10') {$t[0] = sprintf("%02d",$t[0]); } // leading zero on hour.
  $t2 = join(':',$t); // put time back to gether;
  $t2 = preg_replace('/[^\d\:]/is','',$t2); // strip out the am/pm if any
  $Debug .= "<!-- WLfixupTime in='$intime' tfixed='$tfixed' out='$t2' -->\n";
  return($t2);
  	
} // end WLfixupTime

#-------------------------------------------------------------------------------------
# WL support function - WL_setDateTimes
#-------------------------------------------------------------------------------------

function WL_setDateTimes ($indate,$intime,$MDYformat=true) {
// returns: $date_year,$date_month,$date_day,$time_hour,$time_minute,$date_month,$monthname,$dayname
  global $Debug;
  $Debug .= "<!-- WL_setDateTimes date='$indate' time=$intime' MDY=$MDYformat -->\n";
  $d = explode('/',$indate);
  if($d[2]<2000) {$d[2]+=2000;}
  if($MDYformat) { // mm/dd/yyyy
    $YMD = "$d[2]-$d[0]-$d[1]";
  } else {         // dd/mm/yyyy
    $YMD = "$d[2]-$d[1]-$d[0]";
  }
  $t = WLfixupTime($intime);
  
  $WLtime = strtotime("$YMD $t:00");
  $Debug .= "<!-- WL_setDateTimes WLtime='$YMD $t:00' assembled -->\n";
   
  $WLtime = date('Y m d H i F l',$WLtime);
  $Debug .= "<!-- WL_setDateTimes WLtime='$WLtime' values set -->\n";
  if(isset($_REQUEST['debug'])) {echo $Debug; } 
  return(explode(' ',$WLtime)); // results returned in array for list() assignment
  	
} // end WL_setDateTimes

#-------------------------------------------------------------------------------------
# WL support function - WL_beaufortNumber
#-------------------------------------------------------------------------------------

function WL_beaufortNumber ($rawwind,$usedunit) {
   global $Debug;
  
// first convert all winds to knots

   $WINDkts = 0.0;
   if       (preg_match('/kts|knot/i',$usedunit)) {
	   $WINDkts = $rawwind * 1.0;
   } elseif (preg_match('/mph/i',$usedunit)) {
	   $WINDkts = $rawwind * 0.8689762;
   } elseif (preg_match('/mps|m\/s/i',$usedunit)) {
	   $WINDkts = $rawwind * 1.94384449;
   } elseif  (preg_match('/kmh|km\/h/i',$usedunit)) {
	   $WINDkts = $rawwind * 0.539956803;
   } else {
	   $Debug .= "<!-- WL_beaufortNumber .. unknown input unit '$usedunit' for wind=$rawwind -->\n";
	   $WINDkts = $rawwind * 1.0;
   }

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
} // end WL_beaufortNumber

#-------------------------------------------------------------------------------------
# WL support function - WL_beaufortText
#-------------------------------------------------------------------------------------

function WL_beaufortText ($beaufortnumber) {

  $B = array( /* Beaufort 0 to 12 in English */
   "Calm", "Light air", "Light breeze", "Gentle breeze", "Moderate breeze", "Fresh breeze",
   "Strong breeze", "Near gale", "Gale", "Strong gale", "Storm",
   "Violent storm", "Hurricane"
  );

  if(isset($B[$beaufortnumber])) {
	return $B[$beaufortnumber];
  } else {
    return "Unknown $beaufortnumber Bft";
  }
	
	
} // end WL_beaufortText

#-------------------------------------------------------------------------------------
# WL support function - WL_setFeelslike
#-------------------------------------------------------------------------------------


function WL_setFeelslike ($temp,$windchill,$heatindex,$tempUOM) {
global $Debug;
// establish the feelslike temperature and return a word describing how it feels

$HeatWords = array(
 'Unknown', 'Extreme Heat Danger', 'Heat Danger', 'Extreme Heat Caution', 'Extremely Hot', 'Uncomfortably Hot',
 'Hot', 'Warm', 'Comfortable', 'Cool', 'Cold', 'Uncomfortably Cold', 'Very Cold', 'Extreme Cold' );

// first convert all temperatures to Centigrade if need be
  $TC = $temp;
  $WC = $windchill;
  $HC = $heatindex;
  
  if (preg_match('|F|i',$tempUOM))  { // convert F to C if need be
	 $TC = sprintf("%01.1f",round(($TC-32.0) / 1.8,1));
	 $WC = sprintf("%01.1f",round(($WC-32.0) / 1.8,1));
	 $HC = sprintf("%01.1f",round(($HC-32.0) / 1.8,1));
  }
 
 // Feelslike
 
  if ($TC <= 16.0 ) {
	$feelslike = $WC; //use WindChill
  } elseif ($TC >=27.0) {
	$feelslike = $HC; //use HeatIndex
  } else {
	$feelslike = $TC;   // use temperature
  }

  if (preg_match('|F|i',$tempUOM))  { // convert C back to F if need be
	$feelslike = (1.8 * $feelslike) + 32.0;
  }
  $feelslike = round($feelslike,0);

// determine the 'heat color word' to use  
 $hcWord = $HeatWords[0];
 $hcFound = false;
 if ($TC > 32 and $HC > 29) {
	if ($HC > 54 and ! $hcFound) { $hcWord = $HeatWords[1]; $hcFound = true;}
	if ($HC > 45 and ! $hcFound) { $hcWord = $HeatWords[2]; $hcFound = true; }
	if ($HC > 39 and ! $hcFound) { $hcWord = $HeatWords[4]; $hcFound = true; }
	if ($HC > 29 and ! $hcFound) { $hcWord = $HeatWords[6]; $hcFound = true; }
 } elseif ($WC < 16 ) {
	if ($WC < -18 and ! $hcFound) { $hcWord = $HeatWords[13]; $hcFound = true; }
	if ($WC < -9 and ! $hcFound)  { $hcWord = $HeatWords[12]; $hcFound = true; }
	if ($WC < -1 and ! $hcFound)  { $hcWord = $HeatWords[11]; $hcFound = true; }
	if ($WC < 8 and ! $hcFound)   { $hcWord = $HeatWords[10]; $hcFound = true; }
	if ($WC < 16 and ! $hcFound)  { $hcWord = $HeatWords[9]; $hcFound = true; }
 } elseif ($WC >= 16 and $TC <= 32) {
	if ($TC <= 26 and ! $hcFound) { $hcWord = $HeatWords[8]; $hcFound = true; }
	if ($TC <= 32 and ! $hcFound) { $hcWord = $HeatWords[7]; $hcFound = true; }
 }

 if(isset($_REQUEST['debug'])) {
  echo "<!-- WL_setFeelslike input T,WC,HI,U='$temp,$windchill,$heatindex,$tempUOM' cnvt T,WC,HI='$TC,$WC,$HC' feelslike=$feelslike hcWord=$hcWord -->\n";
 }

 return(array($feelslike,$hcWord));
	
} // end of WL_setFeelslike

#-------------------------------------------------------------------------------------
# WL support function - WL_CBI - Chandler Burning Index
#-------------------------------------------------------------------------------------

function WL_CBI($inTemp,$inTempUOM,$inHumidity) {
	// thanks to Chris from sloweather.com for the CBI calculation script
	// modified by Ken True for template usage
	
	preg_match('/([\d\.\+\-]+)/',$inTemp,$t); // strip non-numeric from inTemp if any
	$ctemp = $t[1];
	if(!preg_match('|C|i',$inTempUOM)) {
	  $ctemp = ($ctemp-32.0) / 1.8; // convert from Fahrenheit	
	}
	preg_match('/([\d\.\+\-]+)/',$inHumidity,$t); // strip non-numeric from inHumidity if any
	$rh = $t[1];

	// Start Index Calcs
	
	// Chandler Index
	$cbi = (((110 - 1.373 * $rh) - 0.54 * (10.20 - $ctemp)) * (124 * pow(10,-0.0142 * $rh) ))/60;
	// CBI = (((110 - 1.373*RH) - 0.54 * (10.20 - T)) * (124 * 10**(-0.0142*RH)))/60
	
	//Sort out the Chandler Index
	$cbi = round($cbi,1);
	if ($cbi > "97.5") {
		$cbitxt = "EXTREME";
		$cbiimg= "fdl_extreme.gif";
	
	} elseif ($cbi >="90") {
		$cbitxt = "VERY HIGH";
		$cbiimg= "fdl_vhigh.gif";
	
	} elseif ($cbi >= "75") {
		$cbitxt = "HIGH";
		$cbiimg= "fdl_high.gif";
	
	} elseif ($cbi >= "50") {
		$cbitxt = "MODERATE";
		$cbiimg= "fdl_moderate.gif";
	
	} else {
		$cbitxt="LOW";
		$cbiimg= "fdl_low.gif";
	}
	 $data = array($cbi,$cbitxt,$cbiimg);
	 return $data;
	 
} // end WL_CBI

#-------------------------------------------------------------------------------------
# end of WL support functions
#-------------------------------------------------------------------------------------
