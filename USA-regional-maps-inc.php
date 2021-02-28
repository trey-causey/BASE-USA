<?php
############################################################################
# A Project of Saratoga-Weather.org (Base-USA template set)
############################################################################
#
#   Project:    Sample Included Website Design
#   Module:     USA-regional-map-inc.php
#   Purpose:    Provide regional map displays on wxindex.php above ajax-dashboard.php
#   Author:     Ken True - Saratoga-weather.org
#
# 	Copyright:	(c) 2020 Copyright Saratoga-weather.org.
############################################################################
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA
############################################################################
#	This document uses Tab 4 Settings
############################################################################
//Version 1.00 - 10-Jun-2020 - initial release
//Version 1.01 - 17-Dec-2020 - fix for new composite NWS radar images names/locations
//Version 1.02 - 06-Jan-2021 - added support for NWS-regional-radar-animate.php regional radars
############################################################################
# Settings area (not normally needed.. use entries in Settings.php to configure)
$leftMap = 'sw'; // normally a NWS Regional Radar image
$rightMap = 'us';// normally a WU IR Regional Satellite image
# end of settings area
############################################################################
#
# please do not modify anything below here
//--self downloader --
if(isset($_REQUEST['sce']) and strtolower($_REQUEST['sce']) == 'view') {
   $filenameReal = __FILE__;
   $download_size = filesize($filenameReal);
   header('Pragma: public');
   header('Cache-Control: private');
   header('Cache-Control: no-cache, must-revalidate');
   header("Content-type: text/plain,charset=ISO-8859-1");
   header("Accept-Ranges: bytes");
   header("Content-Length: $download_size");
   header('Connection: close');
   
   readfile($filenameReal);
   exit;
 }

if(file_exists("Settings.php")) {
	include_once("Settings.php");
	global $SITE;
}

$NWSregions = array(
# original NOAA regional loops
 'ak' => 'Alaska|https://radar.weather.gov/ridge/lite/ALASKA_loop.gif',
 'nw' => 'Northwest|useradar',
 'nr' => 'Northern Rockies|useradar',
 'nm' => 'North Mississippi Valley|useradar',
 'nc' => 'Central Great Lakes|useradar',
 'ne' => 'Northeast|useradar',
 'hi' => 'Hawaii|https://radar.weather.gov/ridge/lite/HAWAII_loop.gif',
 'sw' => 'Southwest|useradar',
 'sr' => 'Southern Rockies|useradar',
 'sc' => 'Southern Plains|useradar',
 'sm' => 'South Mississippi Valley|useradar',
 'se' => 'Southeast|useradar',
 # additional loop details V1.02
	'us' => 'Contiguous US|useradar',
	'lws' => 'LWS: Lewiston, ID sector|useradar',
	'wmc' => 'WMC: Winnemucca, NV sector|useradar',
	'las' => 'LAS: Las Vegas, NV sector|useradar',
	'cod' => 'COD: Cody, WY sector|useradar',
	'den' => 'DEN: Denver, CO sector|useradar',
	'abq' => 'ABQ: Albuquerque, NM sector|useradar',
	'pir' => 'PIR: Pierre, SD sector|useradar',
	'ict' => 'ICT: Wichita, KS sector|useradar',
	'aus' => 'AUS: Austin, TX sector|useradar',
	'msp' => 'MSP: Minneapolis, MN sector|useradar',
	'dtw' => 'DTW: Detroit, MI sector|useradar',
	'evv' => 'EVV: Evansville, IN sector|useradar',
	'lit' => 'LIT: Little Rock, AR sector|useradar',
	'alb' => 'ALB: Albany, NY sector|useradar',
	'bwi' => 'BWI: Baltimore, MD sector|useradar',
	'clt' => 'CLT: Charlotte, NC sector|useradar',
	'mgm' => 'MGM: Mongomery, AL sector|useradar',
	'tpa' => 'TPA: Tampa, FL sector|useradar',
 
 
);

$WUregions = array(
 'us' => 'United States|https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/conus/animate.png',
 'nw' => 'North West|https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/usanw/animate.png',
 'nc' => 'North Central|https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/usanc/animate.png',
 'ne' => 'North East|https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/usane/animate.png',
 'wc' => 'West Central|https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/usawc/animate.png',
 'ce' => 'Central|https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/usace/animate.png',
 'ec' => 'East Central|https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/usaec/animate.png',
 'sw' => 'South West|https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/usasw/animate.png',
 'sc' => 'South Central|https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/usasc/animate.png',
 'se' => 'South East|https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/usase/animate.png',
);
# overrides from Settings.php if valid
if(isset($SITE['NWSregion'])) { $leftMap = $SITE['NWSregion']; }
if(isset($SITE['leftMap']))   { $leftMap = $SITE['leftMap']; }

if(isset($SITE['WUsatellite'])) { $rightMap = $SITE['WUsatellite']; }
if(isset($SITE['rightMap']))    { $rightMap = $SITE['rightMap']; }
#
print "<!-- USA-regional-maps-inc.php V1.02 - 06-Jan-2021 -->\n";

if(file_exists('NWS-regional-radar-animate.php')) {
	// v1.02 fix to add NWS-regional-radar-animate.php regional maps
	if(isset($SITE['GR3radar'])) { $localRadar = $SITE['GR3radar']; }
	$regionalRadarURL = 'NWS-regional-radar-animate.php?region=';
	
	foreach ($NWSregions as $key => $val) {
		$NWSregions[$key] = str_replace('useradar',$regionalRadarURL.$key,$val);
	}
	// v1.02 end fix
} else {
	// v1.01 fix to substitute local radar for missing regional composite images
	$localRadar = 'kmux';
	if(isset($SITE['GR3radar'])) { $localRadar = $SITE['GR3radar']; }
	if(isset($SITE['noaaradar']) and substr(strtoupper($localRadar),1,3) !== $SITE['noaaradar']) {
		# oops.. the GR3radar wasn't set correctly.. use noaaradar instead
			  $localRadar = 'K'.$SITE['noaaradar']; //V1.01a fix
	}
	$localRadar = strtoupper($localRadar);
	$localRadarURL = 'https://radar.weather.gov/ridge/lite/'.$localRadar.'_loop.gif';
	
	foreach ($NWSregions as $key => $val) {
		$NWSregions[$key] = str_replace('useradar',$localRadarURL,$val);
	}
	// v1.01 end fix
}

if(strlen($leftMap) >= 2) { // NOAA region specified
	if (isset($NWSregions[$leftMap])) {
		list($leftTitle,$leftURL,$leftLink) = explode('|',$NWSregions[$leftMap].'||');
		$leftTitle = 'NWS '.$leftTitle.' Regional Radar';
		print "<!-- using $leftURL for left map of $leftTitle -->\n";
	} else {
		print "<!-- incorrect leftMap='$leftMap' for left map - using 'sw' map instead. -->\n";
		list($leftTitle,$leftURL,$leftLink) = explode('|',$NWSregions['sw'].'||');
		$leftTitle = 'NWS '.$leftTitle.' Regional Radar';
	}
} else { // custom URL specified
	list($leftTitle,$leftURL,$leftLink) = explode('|',$leftMap.'||');
	if(!empty($leftLink)) {$leftTitle = 'Click to open '.$leftTitle. ' page in new tab.'; }
	if(empty($leftURL)) {
		print "<!-- incorrect leftMap='$leftMap' for left map - using 'sw' map instead. -->\n";
		list($leftTitle,$leftURL,$leftLink) = explode('|',$NWSregions['sw'].'||');
		$leftTitle = 'NWS '.$leftTitle.' Regional Radar';
	}
	print "<!-- using $leftURL for left map of $leftTitle -->\n";
}

if(strlen($rightMap) == 2) { // WU region specified
	if (isset($WUregions[$rightMap])) {
		list($rightTitle,$rightURL,$rightLink) = explode('|',$WUregions[$rightMap].'||');
		$rightTitle = 'WU '.$rightTitle.' Satellite';
		print "<!-- using $rightURL for left map of $rightTitle -->\n";
	} else {
		print "<!-- incorrect rightMap='$rightMap' for right map - using 'sw' map instead. -->\n";
		list($rightTitle,$rightURL,$rightLink) = explode('|',$NWSregions['sw'].'||');
	}
} else { // custom URL specified
	list($rightTitle,$rightURL,$rightLink) = explode('|',$rightMap.'||');
	if(!empty($rightLink)) {$rightTitle = 'Click to open '.$rightTitle. ' page in new tab.'; }
	if(empty($rightURL)) {
		print "<!-- incorrect rightMap='$rightMap' for right map - using 'sw' map instead. -->\n";
		list($rightTitle,$rightURL,$rightLink) = explode('|',$WUregions['sw'].'||');
		$rightTitle = 'WU '.$rightTitle.' Satellite';
	}
	print "<!-- using $rightURL for left map of $rightTitle -->\n";
}
?>
  <table width="99%" style="border: none">
  <tr><td align="center">
	<?php if(!empty($leftLink)) { ?>
    <a href="<?php echo $leftLink; ?>" target="_blank" title="<?php echo $leftTitle; ?>">
  <?php } ?>
    <img src="<?php echo $leftURL; ?>" alt="<?php echo $leftTitle; ?>" title="<?php echo $leftTitle; ?>" 
      width="320" height="240" style="margin: 0px; padding: 0px; border: none"
      />
<?php if(!empty($leftLink)) { echo "</a>\n"; } ?>
  </td>
  <td align="center">
	<?php if(!empty($rightLink)) { ?>
    <a href="<?php echo $rightLink; ?>" target="_blank" title="<?php echo $rightTitle; ?>">
  <?php } ?>
    <img src="<?php echo $rightURL; ?>" alt="<?php echo $rightTitle; ?>" title="<?php echo $rightTitle; ?>" 
      width="320" height="240" style="margin: 0px; padding: 0px; border: none"
      />
<?php if(!empty($rightLink)) { echo "</a>\n"; } ?>
  </td>
  </tr>
  <tr><td colspan="2" align="center"><small>Radar/Satellite images courtesy of
  <a href="https://radar.weather.gov">NOAA/NWS</a> and  
  <a href="https://www.weatherunderground.com">Weather Underground</a>.</small></td></tr>
  </table>
