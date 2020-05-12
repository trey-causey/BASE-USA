<?php
# WLrealtime.php script
# invoke the WLCOMtags.php to create the WLrealtime.txt file for use by ajaxWLCOM.js script 
#
# Version 1.00 - 22-Apr-2017 - initial release
#
if(file_exists("Settings.php") and file_exists("WLCOMtags.php")) {

	include_once("Settings.php");
  $genRealtime = true;
  include_once("WLCOMtags.php");
}
