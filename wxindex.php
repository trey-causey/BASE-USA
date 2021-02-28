<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (Base-USA template set)
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
//Version 1.01 - 28-Jul-2012 - integrated support for nws-alerts scripts
//Version 1.02 - 22-Apr-2017 - updated WU map links
//Version 1.03 - 26-Jun-2019 - updated WU map links
//Version 1.04 - 10-Jun-2020 - use NWS radar map instead of WU - add USA-regional-maps-inc.php
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE= $SITE['organ'] . " - Home";
$showGizmo = true;  // set to false to exclude the gizmo
include("top.php");
############################################################################
?>
</head>
<body>
<?php
############################################################################
include("header.php");
############################################################################
include("menubar.php");
############################################################################
//
$useTopWarning = true;  // set to true to use only the rss-top-warning script
//                         set to false to use the rss-advisory script instead
?>

<div id="main-copy">
    
<?php // insert desired warning box at top of page

  if(isset($SITE['NWSalertsCodes']) and count($SITE['NWSalertsCodes']) > 0) {
	// Add nws-alerts alert box cache file
	include_once("nws-alerts-config.php");
	include($cacheFileDir.$aboxFileName);
	// Insert nws-alerts alert box
	echo $alertBox;
	?>
<script type="text/javascript" src="nws-alertmap.js"></script>
<?php
	  
  } else { // use atom scripts of choice
	if ($useTopWarning) {
	  include_once("atom-top-warning.php");
	} else {
	 print "      <div class=\"advisoryBox\">\n";
	 $_REQUEST['inc'] = 'y';
	 $_REQUEST['summary'] = 'Y';
	 include_once("atom-advisory.php");
	 print "      </div>\n";
	}
  }
?>
<div class="column-dark">
<div align="center">
  <br/>
<?php if(file_exists('USA-regional-maps-inc.php')) { include_once('USA-regional-maps-inc.php'); } ?>
	<img src="<?php echo $SITE['imagesDir']; ?>spacer.gif" alt="spacer"
	height="2" width="620" style="padding:0; margin:0; border: none" />
	<div align="center">
	<?php if(isset($SITE['ajaxDashboard']) and file_exists($SITE['ajaxDashboard']))
	 { include_once($SITE['ajaxDashboard']);
	   } else {
		print "<p>&nbsp;</p>\n";
		print "<p>&nbsp;</p>\n";
		print "<p>Note: ajax-dashboard not included since weather station not yet specified.</p>\n";
        for ($i=0;$i<5;$i++) { print "<p>&nbsp;</p>\n"; }
	}?>
    </div>
</div><!-- end center -->

</div><!-- end column-dark -->

</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>