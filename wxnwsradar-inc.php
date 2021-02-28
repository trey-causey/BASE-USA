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
}?>
<div id="selectorsarea">
<div>
<?php
ini_set("allow_url_fopen", true);
	if (!isset($radar)) { // For debugging set some defaults if 'wxnwsradar-inc.php' is called alone
		echo '<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js" type="text/javascript"></script>';
		$radar = 'N0R'; // Default radar type is set here
		$radarLoc = 'oax'; // IMPORTAMT!!! Default radar location is set here
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
  if (isset($_POST['radar'])) {
    $radar = $_POST['radar'];
  }
  if (isset($_POST['radarLoc'])) {
    $radarLoc = $_POST['radarLoc'];
  }
  if (isset($_POST['imageWidth'])) {
    $imageWidth = $_POST['imageWidth'];
  }
  if (isset($_POST['iframeWidth'])) {
    $iframeWidth = $_POST['iframeWidth'];
  }
  if (isset($_POST['iframeHeight'])) {
    $iframeHeight = $_POST['iframeHeight'];
  }
  if (isset($_POST['autoRefresh'])) {
    $autoRefresh = $_POST['autoRefresh'];
  }
  if (isset($_POST['autoRefreshTime'])) {
    $autoRefreshTime = $_POST['autoRefreshTime'];
  }
  if (isset($_POST['bgndColor'])) {
    $bgndColor = $_POST['bgndColor'];
  }
  if (isset($_POST['autoRefreshOff'])) {
    $autoRefreshOff = $_POST['autoRefreshOff'];
  }
  if (isset($_POST['btnColor'])) {
    $btnColor = $_POST['btnColor'];
  }
  if (isset($_POST['btnTextColor'])) {
    $btnTextColor = $_POST['btnTextColor'];
  }
  if (isset($_POST['animRate'])) {
    $animRate = $_POST['animRate'];
  }
  if (isset($_POST['pauseSeconds'])) {
    $pauseSeconds = $_POST['pauseSeconds'];
  }
  if (isset($_POST['numbImages'])) {
    $numbImages = $_POST['numbImages'];
  }
  if (isset($_POST['smoothingOn'])) {
    $smoothingOn = $_POST['smoothingOn'];
  }

?>
    <table width="600px">
    
    <tr style="vertical-align:bottom"><td width="<?php echo $autoRefresh?25:32?>%" align="center" style="padding:3px;">
    
    <span style="color:black; font-size:13px; font-family:arial, times new roman;">
    <b>Radar Site</b> &nbsp;
    </span>
    <select id="radarLoc">
<?php
$radarList = load_radarsitelist();  // load the State:Radar Site listing (array specified at end of script)
# Generate the radar selection list
foreach ($radarList as $S => $sList) {
	print '  <optgroup label="--'.$S.'--">'."\n";
	  foreach($sList as $R => $rName) {
			 $sel = ($radarLoc==$R)?' selected="selected"':'';
			 print '    <option value="'.$R.'"'.$sel.'>'.$rName.'</option>'."\n";
		}
	print "  </optgroup>\n";
}

?>
   </select>
	</td>
	<td width="<?php echo $autoRefresh?16:24?>%" align="center" style="padding:3px;">
    
    <span style="color:#000000; font-size:13px; font-family:arial, times new roman;">
    <b>Radar Type</b> &nbsp;
    </span>
    <select id="radar">
    <option value="N0R"<?php echo ($radar=="N0R"?' selected="selected"':'')?>>Base Reflectivity</option>
    <option value="NCR"<?php echo ($radar=="NCR"?' selected="selected"':'')?>>Composite Reflectivity</option>
    <option value="N0V"<?php echo ($radar=="N0V"?' selected="selected"':'')?>>Base Velocity</option>
    <option value="N0S"<?php echo ($radar=="N0S"?' selected="selected"':'')?>>Storm Relative Velocity</option>
    <option value="N1P"<?php echo ($radar=="N1P"?' selected="selected"':'')?>>1 Hr. Precipitation</option>
    <option value="NTP"<?php echo ($radar=="NTP"?' selected="selected"':'')?>>Storm Precipitation</option>
    </select>
		</td>

    <td width="<?php echo $autoRefresh?30:25?>%" align="center" style="padding:3px;">
      <span style="color:#000000; font-size:13px; font-family:arial, times new roman;">
      <b>Show # Images</b> <br/>
			</span>  
      <select id="numbImages">
      <?php for ($i=3; $i<=25; $i++) {
				echo '<option value="'.$i.'"'.(($i==$numbImages)?' selected="selected"':'').'>'.$i.'</option>';
			} ?>
      </select>
    </td>

    <?php if ($autoRefresh==1) { ?>
    <td width="<?php echo $autoRefresh?16:28?>%" align="center" style="padding:3px;">
    
      <span style="color:#000000; font-size:13px; font-family:arial, times new roman;">
      <b>AutoRefresh</b> &nbsp;
			</span>  
      <select id="autorefreshoff">
      	<option value="0"<?php echo ($autoRefreshOff==0)?' selected="selected"':''?>>ON</option>
        <option value="1"<?php echo ($autoRefreshOff==1)?' selected="selected"':''?>>OFF</option>
      </select>
    </td>
    <td width="18%" align="center" style="padding:3px;">
      
      <span style="color:#000000; font-size:13px; font-family:arial, times new roman;">
      <b>AutoRefresh Interval</b> <br/>
      </span>
      <select id="interval">
        <option value="2"<?php echo $autoRefreshTime==2?' selected="selected"':''?>>2 Minutes</option>
        <option value="3"<?php echo $autoRefreshTime==3?' selected="selected"':''?>>3 Minutes</option>
        <option value="4"<?php echo $autoRefreshTime==4?' selected="selected"':''?>>4 Minutes</option>
        <option value="5"<?php echo $autoRefreshTime==5?' selected="selected"':''?>>5 Minutes</option>
        <option value="6"<?php echo $autoRefreshTime==6?' selected="selected"':''?>>6 Minutes</option>
        <option value="8"<?php echo $autoRefreshTime==8?' selected="selected"':''?>>8 Minutes</option>
        <option value="10"<?php echo $autoRefreshTime==10?' selected="selected"':''?>>10 Minutes</option>
        <option value="15"<?php echo $autoRefreshTime==15?' selected="selected"':''?>>15 Minutes</option>
        <option value="20"<?php echo $autoRefreshTime==20?' selected="selected"':''?>>20 Minutes</option>
        <option value="30"<?php echo $autoRefreshTime==30?' selected="selected"':''?>>30 Minutes</option>
      </select>
      

    <?php } else { ?>
      <td width="<?php echo $autoRefresh?12:20; ?>%" align="center" style="padding:3px;">
    
        <span style="color:#000000; font-size:13px; font-family:arial, times new roman;">
          &nbsp; <br/>
        </span>
        <button style="border:1px solid #000000; background-color:<?php echo $btnColor?>;border-radius:7px;color:<?php echo $btnTextColor?>;" id="refresh">
        <b>Refresh</b></button>
    
    <?php } ?>
    
	</td></tr></table>
  
  <?php 
	$jsConstants = array(
			'var imageWidth = "'.$imageWidth.'";',
			'var iframeWidth = "'.$iframeWidth.'";',
			'var iframeHeight = "'.$iframeHeight.'";',
			'var autoRefresh = "'.$autoRefresh.'";',
			'var bgndColor = "'.$bgndColor.'";',
			'var btnColor = "'.$btnColor.'";',
			'var btnTextColor = "'.$btnTextColor.'";',
			'var animRate = "'.$animRate.'";',
			'var pauseSeconds = "'.$pauseSeconds.'";',
			'var autoRefreshOff = "'.($autoRefreshOff?'1':'0').'";',
			'var smoothingOn = "'.($smoothingOn?'1':'0').'";'
	);?>
	
	<script type="text/javascript">
		var $jq = jQuery.noConflict();
    $jq("#radar").change(function () {
	
			<?php for ($i=0; $i<count($jsConstants); $i++) {
				echo $jsConstants[$i]."\n";
			}?>
			var numbImages = $jq("#numbImages").val();
			var autoRefreshTime = $jq("#interval").val();
			var radarLoc = $jq("#radarLoc").val();
      var radar = $jq(this).val();
      showRadarjs(radar,radarLoc,imageWidth,iframeWidth,iframeHeight,autoRefresh,autoRefreshTime,bgndColor,autoRefreshOff,btnColor,btnTextColor,pauseSeconds,animRate,numbImages,smoothingOn);
		});

    $jq("#radarLoc").change(function () {
			<?php for ($i=0; $i<count($jsConstants); $i++) {
				echo $jsConstants[$i]."\n";
			}?>
			var numbImages = $jq("#numbImages").val();
			var autoRefreshTime = $jq("#interval").val();
			var radar = $jq("#radar").val();
      var radarLoc = $jq(this).val();
      showRadarjs(radar,radarLoc,imageWidth,iframeWidth,iframeHeight,autoRefresh,autoRefreshTime,bgndColor,autoRefreshOff,btnColor,btnTextColor,pauseSeconds,animRate,numbImages,smoothingOn);
		});

    $jq("#interval").change(function () {
			<?php for ($i=0; $i<count($jsConstants); $i++) {
				echo $jsConstants[$i]."\n";
			}?>
			var numbImages = $jq("#numbImages").val();
      var radarLoc = $jq("#radarLoc").val();
 			var radar = $jq("#radar").val();
			var autoRefreshTime = $jq(this).val();
     showRadarjs(radar,radarLoc,imageWidth,iframeWidth,iframeHeight,autoRefresh,autoRefreshTime,bgndColor,autoRefreshOff,btnColor,btnTextColor,pauseSeconds,animRate,numbImages,smoothingOn);
		});

    $jq("#refresh").bind("click", function () {
			<?php for ($i=0; $i<count($jsConstants); $i++) {
				echo $jsConstants[$i]."\n";
			}?>
			var numbImages = $jq("#numbImages").val();
			var autoRefreshTime = $jq("#interval").val();
			var radar = $jq("#radar").val();
			var radarLoc = $jq("#radarLoc").val();
      showRadarjs(radar,radarLoc,imageWidth,iframeWidth,iframeHeight,autoRefresh,autoRefreshTime,bgndColor,autoRefreshOff,btnColor,btnTextColor,pauseSeconds,animRate,numbImages,smoothingOn);
		});

    $jq("#autorefreshoff").change(function () {
			<?php for ($i=0; $i<count($jsConstants); $i++) {
				echo $jsConstants[$i]."\n";
			}?>
			var autoRefreshTime = $jq("#interval").val();
			var numbImages = $jq("#numbImages").val();
      var radarLoc = $jq("#radarLoc").val();
			var radar = $jq("#radar").val();
			var autoRefreshOff = $jq(this).val();
      showRadarjs(radar,radarLoc,imageWidth,iframeWidth,iframeHeight,autoRefresh,autoRefreshTime,bgndColor,autoRefreshOff,btnColor,btnTextColor,pauseSeconds,animRate,numbImages,smoothingOn);
		});

    $jq("#numbImages").change(function () {
			<?php for ($i=0; $i<count($jsConstants); $i++) {
				echo $jsConstants[$i]."\n";
			}?>
			var autoRefreshTime = $jq("#interval").val();
      var radarLoc = $jq("#radarLoc").val();
			var radar = $jq("#radar").val();
			var numbImages = $jq(this).val();
      showRadarjs(radar,radarLoc,imageWidth,iframeWidth,iframeHeight,autoRefresh,autoRefreshTime,bgndColor,autoRefreshOff,btnColor,btnTextColor,pauseSeconds,animRate,numbImages,smoothingOn);
		});

    </script>

<?php
$ridgeRadars = array (
    "N0R" => "Base Reflectivity",
    "NCR" => "Composite Reflectivity",
    "N0V" => "Base Velocity",
    "N0S" => "Storm Relative Velocity",
    "N1P" => "1 Hr. Precipitation",
    "NTP" => "Storm Precipitation"
);
$ourRadar = '';
$ourState = '';
$ourCity = '';
$ourRadar=$ridgeRadars[$radar];
foreach ($radarList as $key => $liststate) {
	$ourState = $key;
	foreach ($liststate as $key => $listcity) {
		if ($key === $radarLoc) {
			$ourCity = $listcity;
			break;
		}
	}
	if (strlen($ourCity)>0) break;
}
$radarPrefix = ($ourState=='Hawaii'or $ourState=='Alaska' or $ourState=='Guam')?'P':'K';
$radarPrefix = ($ourState=='Puerto Rico')?'T':$radarPrefix;
$statRadar = $radarPrefix.strtoupper($radarLoc);
print "<!-- ourCity='$ourCity' ourRadar='$ourRadar' statRadar='$statRadar' -->\n";
?>
<script type="text/javascript">
	function reloadIframe() {
		var now = new Date();
		var myframe;
		myframe = window.frames["wxradarshanis"];
		<?php if ($autoRefreshOff==0) { ?>
			if (myframe!==null) {
				myframe.location.reload();
			} else alert("No myframe found");
			setTimeout('reloadIframe()',<?php echo $autoRefreshTime?>*60000);
		<?php } ?>
	}
	<?php if ($autoRefreshOff==0) { ?>
	setTimeout('reloadIframe()',<?php echo $autoRefreshTime?>*60000);
	<?php } ?>
</script>
</div>
<div class="center" align="center" style="width:620px">
<?php include_once("radar-status.php");?>
<h3 align="center"><?php echo $ourCity?>, <?php echo $ourState?> <?php echo $ourRadar?> Radar</h3>
		<iframe name="wxradarshanis" width="<?php echo $iframeWidth?>" height="<?php echo $iframeHeight?>" src="./wxnwsradar-iframe.php?radar=<?php echo $radar?>&amp;radarLoc=<?php echo $radarLoc?>&amp;imageWidth=<?php echo $imageWidth?>&amp;bgndColor=<?php echo $bgndColor?>&amp;btnColor=<?php echo $btnColor?>&amp;btnTextColor=<?php echo $btnTextColor?>&amp;animRate=<?php echo $animRate?>&amp;pauseSeconds=<?php echo $pauseSeconds?>&amp;numbImages=<?php echo $numbImages?>&amp;smoothingOn=<?php echo $smoothingOn?>" scrolling="no" style="border:none"></iframe>
<hr/>

  <span style="font-size:9px; text-align:center">The above images are produced by <a href="http://radar.weather.gov/" target="_blank" title="Off Site">NWS Radar</a> -- Animation by <a href="https://www.ssec.wisc.edu/hanis/index.html" target="_blank" title="New Tab">HAniS</a> &copy;2014-<?php echo date("Y")?> by Tom Whittaker<br/>
NOAA Ridge Radar <a href="https://www.weather.gov/jetstream/radarfaq" target="_blank" title="New Tab">FAQ</a>&nbsp;&nbsp;
Current NOAA Radar <a href="https://radar3pub.ncep.noaa.gov" target="_blank" title="Off Site">Status</a><br/>
Script by <a href="https://www.gwwilkins.org" title="Off Site" target="_blank">SE Lincoln Weather</a> and 
 <a href="https://saratoga-weather.org/" target="_blank" title="Off Site">Saratoga-Weather</a><br/>
  (<a href="<?php echo file_exists("wxadvisory.php")?"wxadvisory.php":"https://www.weather.gov/"?>" title="New Tab" target="_blank">NWS Current Advisories</a>)</span>
</div> <!-- end center -->
</div> <!-- End #selectorsarea id -->
<?php 
	#ini_set("allow_url_fopen", false);
	
function load_radarsitelist() {

# current radar list from https://radar.weather.gov/index.htm
# updated Thu, 21 May 2020 14:56:18 -0700
# generated by noaa-radar-list.php - V1.00 - 22-May-2020

$StateList = array (
  'Alabama' => 
  array (
    'bmx' => 'Birmingham',
    'mxx' => 'E. Alabama',
    'eox' => 'Fort Rucker',
    'mob' => 'Mobile',
    'htx' => 'Nrn. Alabama',
  ),
  'Alaska' => 
  array (
    'abc' => 'Bethel',
    'ahg' => 'Kenai',
    'akc' => 'King Salmon',
    'aih' => 'Middleton Is.',
    'aec' => 'Nome',
    'apd' => 'Pedro Dome',
    'acg' => 'Sitka',
  ),
  'Arizona' => 
  array (
    'fsx' => 'Flagstaff',
    'iwa' => 'Phoenix',
    'emx' => 'Tucson',
    'yux' => 'Yuma',
  ),
  'Arkansas' => 
  array (
    'lzk' => 'Little Rock',
    'srx' => 'W. Ark./Ft. Smith',
  ),
  'California' => 
  array (
    'bbx' => 'Beale AFB',
    'eyx' => 'Edwards AFB',
    'bhx' => 'Eureka',
    'vtx' => 'Los Angeles',
    'dax' => 'Sacramento',
    'nkx' => 'San Diego',
    'mux' => 'San Francisco',
    'hnx' => 'San Joaquin Vly.',
    'sox' => 'Santa Ana Mtns',
    'vbx' => 'Vandenberg AFB',
  ),
  'Colorado' => 
  array (
    'ftg' => 'Denver/Boulder',
    'gjx' => 'Grand Junction',
    'pux' => 'Pueblo',
  ),
  'Delaware' => 
  array (
    'dox' => 'Dover AFB',
  ),
  'Florida' => 
  array (
    'jax' => 'Jacksonville',
    'byx' => 'Key West',
    'mlb' => 'Melbourne',
    'amx' => 'Miami',
    'evx' => 'NW Florida',
    'tlh' => 'Tallahassee',
    'tbw' => 'Tampa Bay Area',
  ),
  'Georgia' => 
  array (
    'vax' => 'Moody AFB',
    'ffc' => 'Peachtree City',
    'jgx' => 'Robins AFB',
  ),
  'Guam' => 
  array (
    'gua' => 'Andersen AFB',
  ),
  'Hawaii' => 
  array (
    'hki' => 'Kauai',
    'hkm' => 'Kohala',
    'hmo' => 'Molokai',
    'hwa' => 'South Shore',
  ),
  'Idaho' => 
  array (
    'cbx' => 'Boise',
    'sfx' => 'Pocatello',
  ),
  'Illinois' => 
  array (
    'ilx' => 'Central IL',
    'lot' => 'Chicago',
  ),
  'Indiana' => 
  array (
    'vwx' => 'Evansville',
    'ind' => 'Indianapolis',
    'iwx' => 'Nrn. Indiana',
  ),
  'Iowa' => 
  array (
    'dmx' => 'Des Moines',
    'dvn' => 'Quad Cities',
  ),
  'Kansas' => 
  array (
    'ddc' => 'Dodge City',
    'gld' => 'Goodland',
    'twx' => 'Topeka',
    'ict' => 'Wichita',
  ),
  'Kentucky' => 
  array (
    'hpx' => 'Fort Cambell',
    'jkl' => 'Jackson',
    'lvx' => 'Louisville',
    'pah' => 'Paducah',
  ),
  'Louisiana' => 
  array (
    'poe' => 'Fort Polk',
    'lch' => 'Lake Charles',
    'lix' => 'New Orleans',
    'shv' => 'Shreveport',
  ),
  'Maine' => 
  array (
    'cbw' => 'Caribou',
    'gyx' => 'Portland',
  ),
  'Maryland' => 
  array (
    'lwx' => 'Baltimore',
  ),
  'Massachusetts' => 
  array (
    'box' => 'Boston',
  ),
  'Michigan' => 
  array (
    'dtx' => 'Detroit',
    'apx' => 'Gaylord',
    'grr' => 'Grand Rapids',
    'mqt' => 'Marquette',
  ),
  'Minnesota' => 
  array (
    'dlh' => 'Duluth',
    'mpx' => 'Minneapolis',
  ),
  'Mississippi' => 
  array (
    'gwx' => 'Columbus AFB',
    'dgx' => 'Jackson/Brandon',
  ),
  'Missouri' => 
  array (
    'eax' => 'Kansas City',
    'sgf' => 'Springfield',
    'lsx' => 'St. Louis',
  ),
  'Montana' => 
  array (
    'blx' => 'Billings',
    'ggw' => 'Glasgow',
    'tfx' => 'Great Falls',
    'msx' => 'Missoula',
  ),
  'Nebraska' => 
  array (
    'uex' => 'Hastings',
    'lnx' => 'North Platte',
    'oax' => 'Omaha',
  ),
  'Nevada' => 
  array (
    'lrx' => 'Elko',
    'esx' => 'Las Vegas',
    'rgx' => 'Reno',
  ),
  'New Jersey' => 
  array (
    'dix' => 'Mt. Holly',
  ),
  'New Mexico' => 
  array (
    'abx' => 'Albuquerque',
    'fdx' => 'Cannon AFB',
    'hdx' => 'Holloman AFB',
  ),
  'New York' => 
  array (
    'enx' => 'Albany',
    'bgm' => 'Binghamton',
    'buf' => 'Buffalo',
    'tyx' => 'Montague',
    'okx' => 'Upton',
  ),
  'North Carolina' => 
  array (
    'mhx' => 'Morehead City',
    'rax' => 'Raleigh',
    'ltx' => 'Wilmington',
  ),
  'North Dakota' => 
  array (
    'bis' => 'Bismarck',
    'mvx' => 'Grand Forks',
    'mbx' => 'Minot AFB',
  ),
  'Ohio' => 
  array (
    'cle' => 'Cleveland',
    'iln' => 'Wilmington',
  ),
  'Oklahoma' => 
  array (
    'fdr' => 'Frederick',
    'tlx' => 'Oklahoma City',
    'inx' => 'Tulsa',
    'vnx' => 'Vance AFB',
  ),
  'Oregon' => 
  array (
    'max' => 'Medford',
    'pdt' => 'Pendleton',
    'rtx' => 'Portland',
  ),
  'Pennsylvania' => 
  array (
    'dix' => 'Philadelphia',
    'pbz' => 'Pittsburgh',
    'ccx' => 'State College',
  ),
  'Puerto Rico' => 
  array (
    'jua' => 'Puerto Rico/V.I.',
  ),
  'South Carolina' => 
  array (
    'clx' => 'Charleston',
    'cae' => 'Columbia',
    'gsp' => 'Greer',
  ),
  'South Dakota' => 
  array (
    'abr' => 'Aberdeen',
    'udx' => 'Rapid City',
    'fsd' => 'Sioux falls',
  ),
  'Tennessee' => 
  array (
    'nqa' => 'Memphis',
    'ohx' => 'Nashville',
    'mrx' => 'Tri Cities',
  ),
  'Texas' => 
  array (
    'ama' => 'Amarillo',
    'bro' => 'Brownsville',
    'grk' => 'Central Texas',
    'crp' => 'Corpus Christi',
    'dyx' => 'Dyess AFB',
    'epz' => 'El Paso',
    'fws' => 'Fort Worth',
    'hgx' => 'Houston',
    'dfx' => 'Laughlin AFB',
    'lbb' => 'Lubbock',
    'maf' => 'Midland/Odessa',
    'sjt' => 'San Angelo',
    'ewx' => 'San Antonio',
  ),
  'Utah' => 
  array (
    'icx' => 'Cedar City',
    'mtx' => 'Salt Lake City',
  ),
  'Vermont' => 
  array (
    'cxx' => 'Burlington',
  ),
  'Virginia' => 
  array (
    'fcx' => 'Roanoke',
    'lwx' => 'Sterling',
    'akq' => 'Wakefield',
  ),
  'Washington' => 
  array (
    'lgx' => 'Langley Hill',
    'otx' => 'Spokane',
    'atx' => 'Tacoma',
  ),
  'Washington DC' => 
  array (
    'lwx' => 'Washington',
  ),
  'West Virginia' => 
  array (
    'rlx' => 'Charleston',
  ),
  'Wisconsin' => 
  array (
    'grb' => 'Green Bay',
    'arx' => 'La Crosse',
    'mkx' => 'Milwaukee',
  ),
  'Wyoming' => 
  array (
    'cys' => 'Cheyenne',
    'riw' => 'Riverton',
  ),
); // end of $StateList

return($StateList);

}
?>
<!-- end nwsradar-inc.php -->
