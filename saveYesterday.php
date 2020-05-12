<?php
# saveYesterday script should be run via cron hourly at 58 after the hour.
# it will only do saving of the 'yesterday' value if the local time hour is 23
#
# Version 1.00 - 22-Apr-2017 - initial release
#
if(file_exists("Settings.php")) {

	include_once("Settings.php");
	date_default_timezone_set($SITE['tz']);
	$Hnow = date('H');
	if($Hnow == '23') {
	  #$tstamp = date('Y-m-d H:i:s T');
		#echo "saveYesterday.php local time is $tstamp\n";
    $saveYesterday = true;
    include_once("WLCOMtags.php");
		return;
	} else {
		#echo "saveYesterday.php $tstamp notice: Hour=$Hnow is not = 23 .. not running the save.\n";
		return;
	}
	
} else {
	echo "saveYesterday.php Warning: no Settings.php found.\n";
}
?>