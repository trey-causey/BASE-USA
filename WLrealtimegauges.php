<?php
# WLrealtime.php script
# invoke the WLCOMtags.php to create the WLrealtimegauges.txt file for use by Steel Series Gauges 
#
# Version 1.00 - 22-Apr-2017 - initial release
#
if(file_exists("Settings.php") and file_exists("WLCOMtags.php")) {

	include_once("Settings.php");
  $genSSG = true;
  include_once("WLCOMtags.php");
}
