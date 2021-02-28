<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (WD-USA template set)
############################################################################
#
#   Project:    Sample Included Website Design
#   Module:     sample.php
#   Purpose:    Sample Page
#   Authors:    Kevin W. Reed <kreed@tnet.com>
#               TNET Services, Inc.
#
# 	Copyright:	(c) 1992-2007 Copyright TNET Services, Inc.
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
#
# this script is a port of Jerry Wilkins wxusradars-hanis3.php Version 5a (20190511)
# with additional mods by Ken True for inclusion in the Saratoga Base-USA template.
# Many thanks to Jerry for allowing the kind use of his code!
#
# Version 1.00 - 22-May-2020 - initial release
# Version 2.00 - 06-Jan-2021 - removed display of radar.weather.gov due to their website changes
############################################################################
$viewSource = false;
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
############################################################################
$standAlone				= false;			// false if we run in the template environment
	require_once("Settings.php");
	require_once("common.php");
############################################################################
$divWidth = 660;
$TITLE = langtransstr($SITE['organ']) . " - " .langtransstr('US and Territories Current NOAA Ridge Radar');
$showGizmo = true; // Set to false to exclude the gizmo
include("top.php");
############################################################################
?>
</head>
<body>
<?php
############################################################################
include("header.php");
include("menubar.php");
############################################################################
?>

<div id="main-copy">
  <div align="center">
  <?php if(file_exists('NWS-regional-radar-animate.php')) { ?>
  <h2 align="center">Regional Radar Loop</h2>
  <img src="NWS-regional-radar-animate.php?region=<?php echo $SITE['NWSregion']; ?>" alt="Regional Radar" width="620"/>  
    
  <h2 align="center">USA (CONUS) Radar Loop</h2> 
  <img src="NWS-regional-radar-animate.php?region=us" alt="USA (CONUS) Radar" width="620"/>  
  <?php } else { ?>
  <h2>The NWS Regional Radar Loop displays are not available.</h2>
  <p>The <i>NWS-regional-radar-animate.php</i> script was not found.</p>
  <?php } ?>
   
  </div><!-- end align=center -->
</div><!-- end main-copy -->

<?php 
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>
