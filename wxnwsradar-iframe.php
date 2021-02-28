<?php
############################################################################
#
# this script is a port of Jerry Wilkins wxusradars-hanis3.php Version 5a (20190511)
# with additional mods by Ken True for inclusion in the Saratoga Base-USA template.
# Many thanks to Jerry for allowing the kind use of his code!
#
# Version 1.00 - 22-May-2020 - initial release
############################################################################
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
} ?>
<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
  <title>HAniS US Radar Animation</title>
  <script type="text/javascript" src="./hanis_min.js"> </script>
<?php if (!isset($radar)) { // Load jquery library if needed
echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js" type="text/javascript"></script>';
} ?>
  </head>
<?php 
if (!isset($radar)) { // To test load some defaults if not called by 'wxnwsradar-inc.php'
	$radar = 'NCR'; // Default radar type is set here
	$radarLoc = 'mux'; // IMPORTAMT!!! Default radar location is set here
	$imageWidth = 600; // Width of radar images
	$iframeWidth = 617; // Default IFrame Width -- adjust as needed
	$iframeHeight = 620; // Default IFrame Height -- adjust as needed
	$autoRefresh = true; // Use Autorefresh? true or false -- Determines whether AutoRefresh even appears
	$autoRefreshTime = 8; // Number of minutes between autorefreshes.  IMPORTANT: use 2, 3, 4, 5, 6, 8, 10, 15, 20, or 30 ONLY!!!
	$autoRefreshOff = false; // Begin with Autorefresh Off? true or false -- 'OFF' or 'ON"
	$bgndColor = 'silver'; // Set HAniS Background Color Here
	$btnColor = 'darkslategray'; // Set Button Color here
	$btnTextColor = 'white'; // Set Button Text Color here
	############################ New in Version 2 ##########################
	$pauseSeconds = 2; // Pause on last image, in seconds
	$animRate = 20; // Frame Rate of animation: 5 is glacial, 10 is slow, 15 is leisurely, 20 is good, and 50 is fast - set with integer
	$numbImages = 12; // Number of Radar Images to Animate - 3 to around 25
	############################ New in Version 3 ##########################
	$smoothingOn = true; // Enable image smoothing - new in HAniS 2.5
}
	if (isset($_GET['radar'])) {
    $radar = $_GET['radar'];
  }
  if (isset($_GET['imageDir'])) {
    $imageDir = $_GET['imageDir'];
  }
  if (isset($_GET['radarLoc'])) {
    $radarLoc = $_GET['radarLoc'];
  }
  if (isset($_GET['imageWidth'])) {
    $imageWidth = $_GET['imageWidth'];
  }
  if (isset($_GET['bgndColor'])) {
    $bgndColor = $_GET['bgndColor'];
  }
  if (isset($_GET['btnColor'])) {
    $btnColor = $_GET['btnColor'];
  }
  if (isset($_GET['btnTextColor'])) {
    $btnTextColor = $_GET['btnTextColor'];
  }
  if (isset($_GET['animRate'])) {
    $animRate = $_GET['animRate'];
  }
  if (isset($_GET['pauseSeconds'])) {
    $pauseSeconds = $_GET['pauseSeconds'];
  }
  if (isset($_GET['numbImages'])) {
    $numbImages = $_GET['numbImages'];
  }
  if (isset($_GET['smoothingOn'])) {
    $smoothingOn = $_GET['smoothingOn'];
  }
$radInfo = array();
$errorMessage = get_overlay_fnames($radar,$radarLoc,$listFiles=false);
if ($errorMessage=='Radar Images Currently Unavailable!') {
  echo '<b>No Radar Images Are Currently Available From Here!</b></div>';
  exit;
} else if ($errorMessage=='Too Many Images Requested') {
  echo '<b>Too Many Images Requested from here.  Try Requesting '.$goodImages.' Images.</b></div>';
	exit;
}

?>
  <body style="width:<?php echo $imageWidth?>px" onload="HAniS.setup(
'filenames = <?php get_file_names($radarLoc,'Topo',', ')?> \n\
overlay_filenames = <?php get_overlay_fnames($radar,$radarLoc,$listFiles=true)?>,<?php get_file_names($radarLoc,'County','& ')?>,<?php get_file_names($radarLoc,'Rivers','& ')?>,<?php get_file_names($radarLoc,'Highways','& ')?>,<?php get_file_names($radarLoc,'Cities','& ')?>,<?php get_overlay_warnings($radarLoc)?>,<?php get_legends($radarLoc,$radar)?> \n\
overlay_transparent_amount = 100 \n\
overlay_labels = Radar/on,Counties/on,Rivers,Highways/on,Cities/on,Warnings/on,Legends/on \n\
controls = startstop, speed, step, looprock, zoom, overlay \n\
controls_style = display:flex;flex-flow:row;background-color:<?php echo $bgndColor?>; \n\
speed_labels = Slower, Faster \n\
rate = <?php echo $animRate?> \n\
pause = <?php echo ($pauseSeconds*1000)?> \n\
skip_missing = 0 \n\
skip_missing_color = #800000 \n\
enable_smoothing = <?php echo ($smoothingOn?'t':'f')?> \n\
overlay_labels_style=font-family:arial;font-size:12px;color:<?php echo $btnColor?>;background-color:<?php echo $bgndColor?>; \n\
background_static = y \n\
overlay_static = n,y,y,y,y,n,n \n\
overlay_zoom = y,y,y,y,y,y,n \n\
buttons_style = flex:auto;margin:2px;background-color:<?php echo $btnColor?>;border-radius:7px;color:<?php echo $btnTextColor?>; \n\
bottom_controls = toggle \n\
toggle_colors = <?php echo $btnColor?>, red, orange \n\
bottom_controls_tooltip = Toggle frames on/off \n\
bottom_controls_style = background-color:<?php echo $bgndColor?>;' ,
'handiv')">

  <div id="handiv" style="width:<?php echo $imageWidth?>px;background-color:#808080;">
  </div>

  </body>
  
 </html>
<?php
############## functions ##############
// ------------------------------------------------------------------
/* begin get_file_names */
function get_file_names($radarLoc,$overlay,$separator){

	global $numbImages;
	$overlay2 = ($overlay=='Cities')?'City':$overlay;
	$extent = ($overlay=='Topo')?'jpg':'gif';
	for ($i=1; $i<=$numbImages; $i++) {
		echo 'https://radar.weather.gov/ridge/Overlays/'.$overlay.'/Short/'.strtoupper($radarLoc).'_'.$overlay2.'_Short.'.$extent;
		echo $i<$numbImages?$separator:'';
	}
	//echo $separator.'https://radar.weather.gov/ridge/graphics/black.gif';

}
/* end get_file_names */
// ------------------------------------------------------------------
function get_overlay_fnames($radar,$radarLoc,$listFiles) {

	global $numbImages,$goodImages;
	$matches = array();
	$theData = get_data('https://radar.weather.gov/ridge/RadarImg/'.$radar.'/'.strtoupper($radarLoc).'/');
	preg_match_all("/(a href\=\")([^\/\?]*)(\")/i", $theData, $matches);
	$imageNumber = count($matches[2]);
/* Debug Code *
  echo '$imageNumber: '.$imageNumber.', count($matches[2]): '.count($matches[2]).'<br/>'; // Debug Code
	$i=0;
	foreach($matches[2] as $match) {
			echo 'File No '.$i.': '.$match . '<br>';
			$i++;
}
/* End Debug Code */
	if ($imageNumber<$numbImages) {
//    echo 'https://radar.weather.gov/ridge/RadarImg/'.$radar.'/'.strtoupper($radarLoc).'/';
    if ( $imageNumber==0) {
  		return 'Radar Images Currently Unavailable!';
		} else if ($imageNumber<$numbImages) {
			$goodImages = $imageNumber;
			return 'Too Many Images Requested';
		}
	} else if ($listFiles) {
  	$imageFile = array();
	  for ($i=($imageNumber-1),$j=$numbImages; $i>=($imageNumber-$numbImages); $i--,$j--) {
		  $imageFile[$j] = $matches[2][$i];
	  }
	  for ($i=1; $i<$numbImages; $i++) {
		  $image = 'https://radar.weather.gov/ridge/RadarImg/'.$radar.'/'.strtoupper($radarLoc).'/'.$imageFile[$i];
//		$image = getUniqueImageURL($image);
		  echo $image;
//		echo $i<11?'& ':'';
		  echo '& ';
		  $radInfo[$i] = $imageFile[$i];
	  }
	  echo ' '.'https://radar.weather.gov/ridge/RadarImg/'.$radar.'/'.strtoupper($radarLoc).'_'.$radar.'_0.gif';
  }
}
/* end get_overlay_fnames */
// ------------------------------------------------------------------
/* begin get_overlay_warnings */
function get_overlay_warnings($radarLoc) {

	global $numbImages;
	$matches = array();
	$theData = get_data('https://radar.weather.gov/ridge/Warnings/Short/'.strtoupper($radarLoc).'/');
	preg_match_all("/(a href\=\")([^\?\"]*)(\")/i", $theData, $matches);
	$imageNumber = count($matches[2]);
	$imageFile = array();
	for ($i=($imageNumber-2),$j=$numbImages; $i>($imageNumber-(($numbImages*2)+3)); $i-=2,$j--) {
		$imageFile[$j] = $matches[2][$i];
	}
/* Debug Code */
/*
	$i=0;
	foreach($matches[2] as $match) {
			echo 'File No '.$i.': '.$match . '<br>';
			$i++;
}
*/
/* End Debug Code */
	for ($i=1; $i<$numbImages; $i++) {
		$image = 'https://radar.weather.gov/ridge/Warnings/Short/'.strtoupper($radarLoc).'/'.$imageFile[$i];
//		$image = getUniqueImageURL($image);
		echo $image;
#		echo $i<11?'& ':'';
		echo '& ';
	}
	echo ' '.'https://radar.weather.gov/ridge/Warnings/Short/'.strtoupper($radarLoc).'_'.'Warnings_0.gif';
}
/* end get_overlay_warnings */
// ------------------------------------------------------------------
/* begin get_legends */
function get_legends($radarLoc,$radar) {

	global $numbImages;
	$matches = array();
	$theData = get_data('https://radar.weather.gov/ridge/Legend/'.strtoupper($radar).'/'.strtoupper($radarLoc).'/');
	preg_match_all("/(a href\=\")([^\?\"]*)(\")/i", $theData, $matches);
	$imageNumber = count($matches[2]);
	$imageFile = array();
	for ($i=($imageNumber-1),$j=$numbImages; $i>=($imageNumber-$numbImages); $i--,$j--) {
		$imageFile[$j] = $matches[2][$i];
	}
/* Debug Code *
	$i=0;
	for($i=1; $i<$numbImages; $i++) {
			echo 'File No '.$i.': '.$imageFile[$i] . '<br>';
//			$i++;
}
/* End Debug Code */
	for ($i=1; $i<$numbImages; $i++) {
		$image = 'https://radar.weather.gov/ridge/Legend/'.strtoupper($radar).'/'.strtoupper($radarLoc).'/'.$imageFile[$i];
		echo $image;
#		echo $i<11?'& ':'';
		echo '& ';
	}
	echo ' '.'https://radar.weather.gov/ridge/Legend/'.strtoupper($radar).'/'.strtoupper($radarLoc).'_'.strtoupper($radar).'_Legend_0.gif';
}
/* end get_legends */
// ------------------------------------------------------------------
/* Begin getUniqueImageURL */
function getUniqueImageURL($image_url){
	$timestamp = time();
	if(strpos($image_url, '?')){
		$image_url = str_replace('?', "?$timestamp&", $image_url);
	}
	else{
		$image_url .= "?$timestamp";
	}
	return $image_url;
}
/* end getUniqueImageURL */
// ------------------------------------------------------------------
/* Begin Function get_data */
function get_data($url)
{
  $ch = curl_init();
  $timeout = 5;
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);                 // don't verify peer certificate
  curl_setopt($ch, CURLOPT_TIMEOUT, 8);                        //  data timeout
  curl_setopt($ch, CURLOPT_NOBODY, false);                     // set nobody
  curl_setopt($ch, CURLOPT_HEADER, true);                      // include header information
  curl_setopt($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (wxnwsradar.php, saratoga-weather.org)');
  curl_setopt($ch,CURLOPT_URL,$url);
  curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
  curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
  $data = curl_exec($ch);
  curl_close($ch);
  return $data;
}
/* End Function get_data */
// ------------------------------------------------------------------

############ end functions ############
?>