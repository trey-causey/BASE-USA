<?php
#################################################################################
# Program: include-wxsummary.php
#
# Purpose: generate a WeatherLink Summary page from native WeatherLink $WX[] variable
#          names.  This script works ONLY with the WL-plugin or WLCOM-plugin and is
#          not compatible with other weather software.
#
# Author:  Ken True - Saratoga-weather.org - 22-Apr-2017
# 
# Version 1.00 - 22-Apr-2017 - initial release
#
$Version = 'include-wxsummary.php - Version 1.00 - 22-Apr-2017';

print "<!-- $Version -->\n";
if (isset($_REQUEST['sce']) && strtolower($_REQUEST['sce']) == 'view' ) {
   //--self downloader --
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');
   readfile($filenameReal);
   exit;
}

include_once("Settings.php");

if(!isset($SITE['WXsoftware']) or 
   (substr($SITE['WXsoftware'],0,2) !== 'WL')) {
		 print "<p>Note: the Summary information is only available with ";
		 print "WeatherLink or WeatherLink.com weather station software. </p>\n";
		 return;
	 }
	 
$Units = array( // lookup table for units display for variables
  'Temp' => trim($SITE['uomTemp']),  // ='&deg;C', ='&deg;F'
  'Hum' => '%',  // percent
  'Baro' => trim($SITE['uomBaro']),  // =' hPa', =' mb', =' inHg'
  'Wind' => trim($SITE['uomWind']),  // =' km/h', =' kts', =' m/s', =' mph'
  'Rain' => trim($SITE['uomRain']),  // =' mm', =' in'
  'UV' => 'Index',
  'Solar' => 'W/m<sup>2</sup>',  
  'Moist' => 'cb',  
  'Wetness' => ' ',  
  'PerHr' => trim($SITE['uomRain']).trim($SITE['uomPerHour']),  //'/hr'
);
# UOMs USED
# Text 	 15 times
# Time 	 73 times
# Temp 	 68 times
# Hum 	 35 times
# WindDir 	 1 times
# Wind 	 7 times
# Rain 	 12 times
# DMY 	 1 times
# Baro 	 7 times
# Moist 	 12 times
# Wetness 	 6 times

$Vars = array( // table of available WeatherLink/WeatherLink.com variable names
# Variable|WX[]|UOM|Descr|Notes (tab delimited)
 'StationElevation' => 'Text|Elevation (from NOAA Setup)|',
 'StationLatitude' => 'Text|Latitude (from NOAA Setup)|',
 'StationCity' => 'Text|City (from NOAA Setup)|',
 'StationLongitude' => 'Text|Longitude (from NOAA Setup)|',
 'StationName' => 'Text|Name of Station|',
 'sunriseTime' => 'Time|Sunrise Time|',
 'sunsetTime' => 'Time|Sunset Time|',
 'outsideTemp' => 'Temp|Outside Temperature|',
 'hiOutsideTemp' => 'Temp|High Outside Temperature|',
 'hiOutsideTempTime' => 'Time|Time of High Outside Temperature|',
 'lowOutsideTemp' => 'Temp|Low Outside Temperature|',
 'lowOutsideTempTime' => 'Time|Time of Low Outside Temperature|',
 'hiInsideTemp' => 'Temp|High Inside Temperature|',
 'hiMonthlyInsideTemp' => 'Temp|High Monthly Inside Temperature|',
 'hiInsideTempTime' => 'Time|Time of High Inside Temperature|',
 'hiMonthlyInsideTempTime' => 'Time|High Monthly Inside Temperature|Not in Tags',
 'lowInsideTemp' => 'Temp|Low Inside Temperature|',
 'lowInsideTempTime' => 'Time|Time of Low Inside Temperature|',
 'insideTemp' => 'Temp|Inside Temperature|',
 'lowMonthlyInsideTemp' => 'Temp|Low Monthly Inside Temperature|',
 'hiYearlyInsideTemp' => 'Temp|High Yearly Inside Temperature|',
 'lowYearlyInsideTemp' => 'Temp|Low Yearly Inside Temperature|',
 'hiMonthlyOutsideTemp' => 'Temp|High monthly Outside Temperature|',
 'lowMonthlyOutsideTemp' => 'Temp|Low monthly Outside Temperature|',
 'hiYearlyOutsideTemp' => 'Temp|High yearly Outside Temperature|',
 'lowYearlyOutsideTemp' => 'Temp|Low yearly Outside Temperature|',
 'outsideHumidity' => 'Hum|Outside Humidity|',
 'hiHumidity' => 'Hum|High Humidity|',
 'hiHumTime' => 'Time|Time of High Humidity|',
 'lowHumidity' => 'Hum|Low Humidity|',
 'lowHumTime' => 'Time|Time of Low Humidity|',
 'insideHumidity' => 'Hum|Inside Humidity|',
 'hiInsideHumidity' => 'Hum|High Inside Humidity|',
 'hiInsideHumidityTime' => 'Time|High Inside Humidity Time|',
 'lowInsideHumidity' => 'Hum|Low Inside Humidity|',
 'lowInsideHumidityTime' => 'Time|Low Inside Humidity Time|',
 'hiMonthlyInsideHumidity' => 'Hum|High Monthly Inside Humidity|',
 'lowMonthlyInsideHumidity' => 'Hum|Low Monthly Inside Humidity|',
 'lowYearlyHumidity' => 'Hum|Low Yearly Humidity|',
 'hiYearlyInsideHumidity' => 'Hum|High Yearly Inside Humidity|',
 'lowYearlyInsideHumidity' => 'Hum|Low Yearly Inside Humidity|',
 'hiMonthlyHumidity' => 'Hum|High Monthly Humidity|',
 'lowMonthlyHumidity' => 'Hum|Low Monthly Humidity|',
 'hiYearlyHumidity' => 'Hum|High Yearly Humidity|',
 'outsideDewPt' => 'Temp|Outside Dew Point|',
 'hiDewpoint' => 'Temp|High Dew Point|',
 'hiDewpointTime' => 'Time|Time of High Dew Point|',
 'lowDewpoint' => 'Temp|Low Dew Point|',
 'lowDewpointTime' => 'Time|Time of Low Dew Point|',
 'hiMonthlyDewpoint' => 'Temp|High Monthly Dew Point|',
 'lowMonthlyDewpoint' => 'Temp|Low Monthly Dew Point|',
 'hiYearlyDewpoint' => 'Temp|High Yearly Dew Point|',
 'lowYearlyDewpoint' => 'Temp|Low Yearly Dew Point|',
 'outsideHeatIndex' => 'Temp|Outside Heat Index|',
 'hiHeatindex' => 'Temp|High Heat Index|',
 'hiHeatindexTime' => 'Time|Time of High Heat Index|',
 'hiMonthlyHeatindex' => 'Temp|High Monthly Heat Index|',
 'hiYearlyHeatindex' => 'Temp|High Yearly Heat Index|',
 'windChill' => 'Temp|Wind Chill|',
 'lowWindchill' => 'Temp|Low Wind Chill|',
 'lowWindchillTime' => 'Time|Time of Low Wind Chill|',
 'lowMonthlyWindchill' => 'Temp|Low Monthly Wind Chill|',
 'lowYearlyWindchill' => 'Temp|Low Yearly Wind Chill|',
 'windDir' => 'Text|Wind Direction|',
 'windDirection' => 'WindDir|Wind Direction Sector (16-point compass)|We Convert',
 'windSpeed' => 'Wind|Wind Speed|',
 'hiWindSpeed' => 'Wind|High Wind Speed|',
 'hiWindSpeedTime' => 'Time|Time of High Wind Speed|',
 'hiMonthlyWindSpeed' => 'Wind|High Monthly Wind Speed|',
 'wind10Avg' => 'Wind|10 Minute Average Speed|',
 'windAvg10' => 'Wind|10-minute Wind Speed Average|',
 'windHigh10' => 'Wind|10-minute Wind High Speed|',
 'hiYearlyWindSpeed' => 'Wind|High Yearly Wind Speed|',
 'dailyEt' => 'Rain|ET|',
 'monthlyEt' => 'Rain|Monthly ET|',
 'yearlyEt' => 'Rain|Yearly ET|',
 'dailyRain' => 'Rain|Rain|',
 'monthlyRain' => 'Rain|Monthly Rain|',
 'hiRainRate' => 'PerHr|High Rain Rate|',
 'hiRainRateTime' => 'Time|Time of High Rain Rate|',
 'hiRainRateHour' => 'PerHr|High Rain Rate Hour|',
 'rainRate' => 'PerHr|Rain Rate|',
 'hiMonthlyRainRate' => 'PerHr|High Monthly Rain Rate|',
 'hiYearlyRainRate' => 'PerHr|High Yearly Rain Rate|',
 'stormRain' => 'Rain|Storm Rain|',
 'stormRainDate' => 'DMY|Storm Rain Start Date|Added-not WL native',
 'totalRain' => 'Rain|Yearly Rain|',
 'barometer' => 'Baro|Barometer|',
 'hiBarometer' => 'Baro|High Barometer|',
 'hiBarometerTime' => 'Time|Time of High Barometer|',
 'lowBarometer' => 'Baro|Low Barometer|',
 'lowBarometerTime' => 'Time|Time of Low Barometer|',
 'hiMonthlyBarometer' => 'Baro|High Monthly Barometer|',
 'lowMonthlyBarometer' => 'Baro|Low Monthly Barometer|',
 'BarTrend' => 'Text|3-Hour Barometer Trend|',
 'hiYearlyBarometer' => 'Baro|High Yearly Barometer|',
 'lowYearlyBarometer' => 'Baro|Low Yearly Barometer|',
 'solarRad' => 'Solar|Solar Radiation|',
 'hiSolarRad' => 'Solar|High Solar Radiation|',
 'hiSolarRadTime' => 'Time|Time of High Solar Radiation|',
 'hiMonthlySolarRad' => 'Solar|High Monthly Solar Radiation|',
 'hiYearlySolarRad' => 'Solar|High Yearly Solar Radiation|',
 'uv' => 'UV|UV|',
 'hiUV' => 'UV|High UV|',
 'hiUVTime' => 'Time|Time of High UV|',
 'hiMonthlyUV' => 'UV|High monthly UV|',
 'hiYearlyUV' => 'UV|High yearly UV|',
 'Temperature2' => 'Temp|Temperature 2|',
 'hiTemp2' => 'Temp|High Temperature 2|',
 'hiTempTime2' => 'Time|Time of High Temperature 2|',
 'lowTemp2' => 'Temp|Low Temperature 2|',
 'lowTempTime2' => 'Time|Time of Low Temperature 2|',
 'Temperature3' => 'Temp|Temperature 3|',
 'hiTemp3' => 'Temp|High Temperature 3|',
 'hiTempTime3' => 'Time|High Temperature 3 Time|',
 'lowTemp3' => 'Temp|Low Temperature 3|',
 'lowTempTime3' => 'Time|Low Temperature 3 Time|',
 'Temperature4' => 'Temp|Temperature 4|',
 'hiTemp4' => 'Temp|High Temperature 4|',
 'hiTempTime4' => 'Time|High Temperature 4 Time|',
 'lowTemp4' => 'Temp|Low Temperature 4|',
 'lowTempTime4' => 'Time|Low Temperature 4 Time|',
 'Temperature5' => 'Temp|Temperature 5|',
 'hiTemp5' => 'Temp|High Temperature 5|',
 'hiTempTime5' => 'Time|High Temperature 5 Time|',
 'lowTemp5' => 'Temp|Low Temperature 5|',
 'lowTempTime5' => 'Time|Low Temperature 5 Time|',
 'Temperature6' => 'Temp|Temperature 6|',
 'hiTemp6' => 'Temp|High Temperature 6|',
 'hiTempTime6' => 'Time|High Temperature 6 Time|',
 'lowTemp6' => 'Temp|Low Temperature 6|',
 'lowTempTime6' => 'Time|Low Temperature 6 Time|',
 'Temperature7' => 'Temp|Temperature 7|',
 'hiTemp7' => 'Temp|High Temperature 7|',
 'hiTempTime7' => 'Time|High Temperature 7 Time|',
 'lowTemp7' => 'Temp|Low Temperature 7|',
 'lowTempTime7' => 'Time|Low Temperature 7 Time|',
 'Temperature8' => 'Temp|Temperature 8|',
 'hiTemp8' => 'Temp|High Temperature 8|',
 'hiTempTime8' => 'Time|High Temperature 8 Time|',
 'lowTemp8' => 'Temp|Low Temperature 8|',
 'lowTempTime8' => 'Time|Low Temperature 8 Time|',
 'Humidity2' => 'Hum|Humidity 2|',
 'hiHum2' => 'Hum|High Humidity 2|',
 'hiHumTime2' => 'Time|High Humidity 2 Time|',
 'lowHum2' => 'Hum|Low Humidity 2|',
 'lowHumTime2' => 'Time|Low Humidity Time 2|',
 'Humidity3' => 'Hum|Humidity 3|',
 'hiHum3' => 'Hum|High Humidity 3|',
 'hiHumTime3' => 'Time|High Humidity 3 Time|',
 'lowHum3' => 'Hum|Low Humidity 3|',
 'lowHumTime3' => 'Time|Low Humidity 3 Time|',
 'Humidity4' => 'Hum|Humidity 4|',
 'hiHum4' => 'Hum|High Humidity 4|',
 'hiHumTime4' => 'Time|High Humidity 4 Time|',
 'lowHum4' => 'Hum|Low Humidity 4|',
 'lowHumTime4' => 'Time|Low Humidity 4 Time|',
 'Humidity5' => 'Hum|Humidity 5|',
 'hiHum5' => 'Hum|High Humidity 5|',
 'hiHumTime5' => 'Time|High Humidity 5 Time|',
 'lowHum5' => 'Hum|Low Humidity 5|',
 'lowHumTime5' => 'Time|Low Humidity 5 Time|',
 'Humidity6' => 'Hum|Humidity 6|',
 'hiHum6' => 'Hum|High Humidity 6|',
 'hiHumTime6' => 'Time|High Humidity 6 Time|',
 'lowHum6' => 'Hum|Low Humidity 6|',
 'lowHumTime6' => 'Time|Low Humidity 6 Time|',
 'Humidity7' => 'Hum|Humidity 7|',
 'hiHum7' => 'Hum|High Humidity 7|',
 'hiHumTime7' => 'Time|High Humidity 7 Time|',
 'lowHum7' => 'Hum|Low Humidity 7|',
 'lowHumTime7' => 'Time|Low Humidity 7 Time|',
 'Humidity8' => 'Hum|Humidity 8|',
 'hiHum8' => 'Hum|High Humidity 8|',
 'hiHumTime8' => 'Time|High Humidity 8 Time|',
 'lowHum8' => 'Hum|Low Humidity 8|',
 'lowHumTime8' => 'Time|Low Humidty 8 Time|',
 'SoilMoisture1' => 'Moist|Soil Moisture 1|',
 'hiSoilMoist1' => 'Moist|High Soil Moisture 1|',
 'hiSoilMoistTime1' => 'Time|High Soil Moisture 1 Time|',
 'lowSoilMoist1' => 'Moist|Low Soil Moisture 1|',
 'lowSoilMoistTime1' => 'Time|Low Soil Moisture 1 Time|',
 'SoilMoisture2' => 'Moist|Soil Moisture 2|',
 'hiSoilMoist2' => 'Moist|High Soil Moisture 2|',
 'hiSoilMoistTime2' => 'Time|High Soil Moisture 2 Time|',
 'lowSoilMoist2' => 'Moist|Low Soil Moisture 2|',
 'lowSoilMoistTime2' => 'Time|Low Soil Moisture 2 Time|',
 'SoilMoisture3' => 'Moist|Soil Moisture 3|',
 'hiSoilMoist3' => 'Moist|High Soil Moisture 3|',
 'hiSoilMoistTime3' => 'Time|High Soil Moisture 3 Time|',
 'lowSoilMoist3' => 'Moist|Low Soil Moisture 3|',
 'lowSoilMoistTime3' => 'Time|Low Soil Moisture 3 Time|',
 'SoilMoisture4' => 'Moist|Soil Moisture 4|',
 'hiSoilMoist4' => 'Moist|High Soil Moisture 4|',
 'hiSoilMoistTime4' => 'Time|High Soil Moisture 4 Time|',
 'lowSoilMoist4' => 'Moist|Low Soil Moisture 4|',
 'lowSoilMoistTime4' => 'Time|Low Soil Moisture 4 Time|',
 'SoilTemp1' => 'Temp|Soil Temperature 1|',
 'hiSoilTemp1' => 'Temp|High Soil Temp 1|',
 'hiSoilTempTime1' => 'Time|High Soil Temp 1 Time|',
 'lowSoilTemp1' => 'Temp|Low Soil Temp 1|',
 'lowSoilTempTime1' => 'Time|Low Soil Temp 1 Time|',
 'SoilTemp2' => 'Temp|Soil Temperature 2|',
 'hiSoilTemp2' => 'Temp|High Soil Temp 2|',
 'hiSoilTempTime2' => 'Time|High Soil Temp 2 Time|',
 'lowSoilTemp2' => 'Temp|Low Soil Temp 2|',
 'lowSoilTempTime2' => 'Time|Low Soil Temp 2 Time|',
 'SoilTemp3' => 'Temp|Soil Temperature 3|',
 'hiSoilTemp3' => 'Temp|High Soil Temp 3|',
 'hiSoilTempTime3' => 'Time|High Soil Temp 3 Time|',
 'lowSoilTemp3' => 'Temp|Low Soil Temp 3|',
 'lowSoilTempTime3' => 'Time|Low Soil Temp 3 Time|',
 'SoilTemp4' => 'Temp|Soil Temperature 4|',
 'hiSoilTemp4' => 'Temp|High Soil Temp 4|',
 'hiSoilTempTime4' => 'Time|High Soil Temp 4 Time|',
 'lowSoilTemp4' => 'Temp|Low Soil Temp 4|',
 'lowSoilTempTime4' => 'Time|Low Soil Temp 4 Time|',
 'LeafWetness1' => 'Wetness|Leaf Wetness 1|',
 'hiLeaf1' => 'Wetness|High Leaf Wetness 1|',
 'hiLeafTime1' => 'Time|High Leaf Wetness 1 Time|',
 'lowLeaf1' => 'Wetness|Low Leaf Wetness 1|',
 'lowLeafTime1' => 'Time|Low Leaf Wetness 1 Time|',
 'LeafWetness2' => 'Wetness|Leaf Wetness 2|',
 'hiLeaf2' => 'Wetness|High Leaf Wetness 2|',
 'hiLeafTime2' => 'Time|High Leaf Wetness 2 Time|',
 'lowLeaf2' => 'Wetness|Low Leaf Wetness 2|',
 'lowLeafTime2' => 'Time|Low Leaf Wetness Time 2|',
 'LeafTemp1' => 'Temp|Leaf Temp 1|',
 'hiLeafTemp1' => 'Temp|High Leaf Temp 1|',
 'hiLeafTempTime1' => 'Time|High Leaf Temp 1 Time|',
 'lowLeafTemp1' => 'Temp|Low Leaf Temp 1|',
 'lowLeafTempTime1' => 'Time|Low Leaf Temp 1 Time|',
 'LeafTemp2' => 'Temp|Leaf Temp 2|Missing in WL defs',
 'hiLeafTemp2' => 'Temp|High Leaf Temp 2|Missing in WL defs',
 'hiLeafTempTime2' => 'Time|High Leaf Temp 2 Time|Missing in WL defs',
 'lowLeafTemp2' => 'Temp|Low Leaf Temp 2|Missing in WL defs',
 'lowLeafTempTime2' => 'Time|Low Leaf Temp 2 Time|Missing in WL defs',
 'thswHigh' => 'Temp|THSW|Missing in WL defs',
 'thswHighTime' => 'Time|THSW High Time|Missing in WL defs',
); // end of $Vars list

#-----------------------------------------------------------------------------
# Generate the table for display 9-columns used (2 blank columns for styling)
#
print "<p>".$WX['StationCity'].
    " - (".$WX['StationName'].
		") - ".$WX['date'].
    " ".$WX['time']."</p>\n";
print "<table>\n";
print "<thead><tr><th style=\"text-align: left\">&nbsp;</th>";
print "<th>".langtransstr('Units')."</th><th>".langtransstr('Current')."</th><th>&nbsp;</th>";
print "<th>".langtransstr('Today\'s')."</th><th>".langtransstr('Highs')."</th><th>&nbsp;</th>";
print "<th>".langtransstr('Today\'s')."</th><th>".langtransstr('Lows')."</th></tr></thead>\n";
print "<tbody>\n";

# --- temperatures ---
if(isset($showInsideData) and $showInsideData) {
  gen_row('insideTemp','hiInsideTemp','hiInsideTempTime','lowInsideTemp','lowInsideTempTime');
}
gen_row('outsideTemp','hiOutsideTemp','hiOutsideTempTime','lowOutsideTemp','lowOutsideTempTime');
for ($i=2;$i<=8;$i++) {
  gen_row('Temperature'.$i,'hiTemp'.$i,'hiTempTime'.$i,'lowTemp'.$i,'lowTempTime'.$i);
}
gen_divider(9);

# --- HI/THW/Wind Chill ---
gen_row('outsideHeatIndex','hiHeatindex','hiHeatindexTime','null','null');
gen_row('null','thswHigh','thswHighTime','null','null');
gen_row('windChill','null','null','lowWindchill','lowWindchillTime');
gen_divider(9);

# --- Humidity ---
if(isset($showInsideData) and $showInsideData) {
	gen_row('insideHumidity','hiInsideHumidity',
	   'hiInsideHumidityTime','lowInsideHumidity','lowInsideHumidityTime');
}
gen_row('outsideHumidity','hiHumidity','hiHumTime','lowHumidity','lowHumTime');
for ($i=2;$i<=8;$i++) {
	gen_row('Humidity'.$i,'hiHum'.$i,'hiHumTime'.$i,'lowHum'.$i,'lowHumTime'.$i);
}
if(isset($showInsideData) and $showInsideData) {
  gen_row('insideDewPt','hiInsideDewPt','hiInsideDewPtTime','lowInsideDewPt','lowInsideDewPtTime');
}
gen_row('outsideDewPt','hiDewpoint','hiDewpointTime','lowDewpoint','lowDewpointTime');
gen_divider(9);

# --- Wind ---
gen_row('windSpeed','hiWindSpeed','hiWindSpeedTime','null','null');
gen_row('wind10Avg','null','null','null','null');
gen_row('windDir','windDirection','null','null','null');
gen_divider(9);

# --- Barometer ---
gen_row('barometer','hiBarometer','hiBarometerTime','lowBarometer','lowBarometerTime');
gen_row('BarTrend','null','null','null','null');
gen_row('rainRate','hiRainRate','hiRainRateTime','null','null');
gen_divider(9);

if( chk_wxvar('solarRad') or chk_wxvar('uv') ) {
	gen_row('solarRad','hiSolarRad','hiSolarRadTime','null','null');
	gen_row('uv','hiUV','hiUVTime','null','null');
  gen_divider(9);
}

# Leaf measurements
if(isset($WX['LeafTemp1']) or isset($WX['LeafTemp2']))
{
  $didit = false;
  for ($i=1;$i<=2;$i++) {
		if(!chk_wxvar('LeafTemp'.$i)) {continue;} 
		gen_row('LeafTemp'.$i,'hiLeafTemp'.$i,'hiLeafTempTime'.$i,'lowLeafTemp'.$i,'lowLeafTempTime'.$i);
		$didit = true;
	}
  for ($i=1;$i<=2;$i++) {
		if(!chk_wxvar('LeafWetness'.$i)) {continue;} 
		gen_row('LeafWetness'.$i,'hiLeaf'.$i,'hiLeafTime'.$i,'lowLeaf'.$i,'lowLeafTime'.$i);
		$didit = true;
	}
	if($didit) {gen_divider(9);}
}

# SOIL measurements
if(isset($WX['SoilTemp1']) or isset($WX['SoilTemp2']) or 
   isset($WX['SoilTemp3']) or isset($WX['SoilTemp4']))
{ 
  $didit = false;
  for ($i=1;$i<=4;$i++) {
		if(!chk_wxvar('SoilTemp'.$i)) {continue;} 
		gen_row('SoilTemp'.$i,'hiSoilTemp'.$i,'hiSoilTempTime'.$i,'lowSoilTemp'.$i,'lowSoilTempTime'.$i);
		$didit = true;
	}
  for ($i=1;$i<=4;$i++) {
		if(!chk_wxvar('SoilMoisture'.$i)) {continue;} 
		gen_row('SoilMoisture'.$i,'hiSoilMoist'.$i,'hiSoilMoistTime'.$i,'lowSoilMoist'.$i,'lowSoilMoistTime'.$i);
		$didit = true;
	}
	if($didit) {gen_divider(9);}
}

# --- rain/ET ---

print "<tr><th style=\"text-align: left\">&nbsp;</th><th>".langtransstr('Units')."</th>";
print "<th>".langtransstr('Daily')."</th><th>&nbsp;</th>";
print "<th>".langtransstr('Storm')."</th>";
print "<th>".langtransstr('Month')."</th><th>&nbsp;</th>";
print "<th>".langtransstr('Year')."</th><th>&nbsp;</th></tr>\n";

gen_row('dailyRain','stormRain','monthlyRain','totalRain','null');
if(isset($WX['dailyEt'])) {
	gen_row('dailyEt','null','monthlyEt','yearlyEt','null');
}
gen_divider(9);

print "</tbody>\n";
print "</table>\n";

function chk_wxvar ($basevar) {
	global $WX,$Vars;
	
	if(isset($WX[$basevar]) and 
	 isset($Vars[$basevar]) and 
	 $WX[$basevar] !== '---' and
	 substr($WX[$basevar],0,3) !== '<!-' ) { 
	 // only do ones we have data for
	   return true;
	 } else {
		 return false;
	 }
}
#---------------------------------------------------------------------
function gen_row ($var,$var2,$var3,$var4,$var5,$bottomline=false) {
	global $SITE,$WX,$Units,$Vars;
	
	list($basevar,$ajax) = explode('|',$var.'|');
	list($highvar,$ajax2) = explode('|',$var2.'|');
	list($hightime,$ajax3) = explode('|',$var3.'|');
	list($lowvar,$ajax4) = explode('|',$var4.'|');
	list($lowtime,$ajax5) = explode('|',$var5.'|');
#	print "<!-- basevar '$basevar' '$ajax' -->\n";
#	print "<!-- highvar '$highvar' '$ajax2' -->\n";
#	print "<!-- hightime '$hightime' '$ajax3' -->\n";
#	print "<!-- lowvar  '$lowvar' '$ajax4' -->\n";
#	print "<!-- lowtime '$lowtime' '$ajax5' -->\n";
	if(chk_wxvar($basevar) or chk_wxvar($highvar)) { 
		 // only do ones we have data for
    if($basevar !== 'null') {
		  list($UOM,$Descr,$Notes) = explode("|",$Vars[$basevar]);
		} else {
		  list($UOM,$Descr,$Notes) = explode("|",$Vars[$highvar]);
		}
		$Descr = langtransstr($Descr);  // allow for translations
		if(isset($Units[$UOM])) {$ourUOM = '['.$Units[$UOM].']'; } else {$ourUOM = ''; }
		if($bottomline) {
      $B = ' style="border-bottom: 1px solid"';
			$BLeft = ' style="text-align: left;border-bottom: 1px solid"';
		} else {
			$B = '';
			$BLeft = ' style="text-align: left;"';
		}
    print "<tr>\n";
		if($ajax !== '') {
			$ajaxPre = '<span class="ajax" id="'.$ajax.'">';
			$ajaxPost = '</span>';
		} else {
			$ajaxPre = '';
			$ajaxPost = '';
		}
		if($ajax2 !== '') {
			$ajaxPre2 = '<span class="ajax" id="'.$ajax2.'">';
			$ajaxPost2 = '</span>';
		} else {
			$ajaxPre2 = '';
			$ajaxPost2 = '';
		}
		if($ajax3 !== '') {
			$ajaxPre3 = '<span class="ajax" id="'.$ajax3.'">';
			$ajaxPost3 = '</span>';
		} else {
			$ajaxPre3 = '';
			$ajaxPost3 = '';
		}
		if($ajax4 !== '') {
			$ajaxPre4 = '<span class="ajax" id="'.$ajax4.'">';
			$ajaxPost4 = '</span>';
		} else {
			$ajaxPre4 = '';
			$ajaxPost4 = '';
		}
		if($ajax5 !== '') {
			$ajaxPre5 = '<span class="ajax" id="'.$ajax5.'">';
			$ajaxPost5 = '</span>';
		} else {
			$ajaxPre5 = '';
			$ajaxPost5 = '';
		}
		print "  <td$BLeft>$Descr</td><td$B>$ourUOM</td><td$B>$ajaxPre$WX[$basevar]$ajaxPost</td><td>&nbsp;</td>";
		if(isset($WX[$highvar]) and isset($Vars[$highvar])) {
			print "<td$B>$ajaxPre2$WX[$highvar]$ajaxPost2</td>";
		} else {
			print "<td$B>&nbsp;</td>";
		}
		if(isset($WX[$hightime]) and isset($Vars[$hightime])) {
			print "<td$B>$ajaxPre3$WX[$hightime]$ajaxPost3</td>";
		} else {
			print "<td$B>&nbsp;</td>";
		}
		print "<td>&nbsp;</td>";
		
		if(isset($WX[$lowvar]) and isset($Vars[$lowvar])) {
			print "<td$B>$ajaxPre4$WX[$lowvar]$ajaxPost4</td>";
		} else {
			print "<td$B>&nbsp;</td>";
		}
		if(isset($WX[$lowtime]) and isset($Vars[$lowtime])) {
			print "<td$B>$ajaxPre5$WX[$lowtime]$ajaxPost5</td>";
		} else {
			print "<td$B>&nbsp;</td>";
		}
		print "\n</tr>\n";
		
		
	} // end got var+data
	
} // end gen_row
# --------------------------------------
function gen_divider ($rows) {
  print "<tr><th colspan=\"$rows\" style=\"line-height: 1px;\">&nbsp;</th></tr>\n";

}
# end of wxsummary-inc.php