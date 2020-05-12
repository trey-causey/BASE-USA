<?php
############################################################################
# A Project of TNET Services, Inc. and Saratoga-Weather.org (Canada/World-ML template set)
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
# Version 1.00 - 22-Apr-2017 - initial release
require_once("Settings.php");
require_once("common.php");
############################################################################
$TITLE = langtransstr($SITE['organ']) . " - " .langtransstr('Current Weather Summary');
$showGizmo = true;  // set to false to exclude the gizmo
include("top.php");
############################################################################
$showInsideData = true; // set =true; to show inside temp/hum, =false to suppress
?>
<style type="text/css">
#main-copy td,th {
  text-align: center;
}
#main-copy th {
  border-bottom: 1px solid;
}
</style>
</head>
<body>
<?php
############################################################################
include("header.php");
############################################################################
include("menubar.php");
############################################################################
?>

<div id="main-copy">
  
	<h1><?php langtrans('Current Weather Summary'); ?></h1>
  <p>&nbsp;</p>
  <?php
	if(file_exists("include-wxsummary.php")) {
		 include_once("include-wxsummary.php");
	} else {
		echo "<p>The Summary Weather page is not available.</p>\n";
	}
	?>
    
    
</div><!-- end main-copy -->

<?php
############################################################################
include("footer.php");
############################################################################
# End of Page
############################################################################
?>