README-2021-01-06 Base-USA Updates

This update adds a new script to create animated GIF images of NWS regional radar
for display in the left pane of the homepage.

Scripts changed/added:

Added:  NWS-regional-radar-animate.php V1.00 - 06-Jan-2021
        This script uses regional radar images from aviationweather.gov/radar/ to create/cache
        animated GIF images. 

        NOTE: the aviationweather.gov/radar/ site lists 'hi','ak' as available maps
        but instead displays 'Not Available'.  So Hawaii and Alaska selection will 
        create a Not Available map display.
        
Change: USA-regional-maps-inc.php V1.01 - 06-Jan-2021
        This script was modified to include support for the new animated GIF displays.
        
Change: wxnwsradar.php V2.00 - 06-Jan-2021
        This script discontinued the (now defunct) displays of radar from radar.weather.gov
        in favor of displaying two animated GIF displays, one for the selected region and
        one for the USA (CONUS).
        
There are settings inside the NWS-regional-radar-animate.php which likely need no changes
for normal operation, but you should check these and change as appropriate for your install:

#--------------------------------------------------------------------------------
# Settings:
#
$NWSregion = 'wmc'; // see below $validRegions entries for valid regions to use 
# Note: 2 letter old $NWSregion regions will be translated to 3 letter regions
#
# 'ak', 'hi', 'carib' sites are not displaying data on aviationweather.gov site
#
# Select radar type:
$NWStype = 'rala';  // ='rala' for 'Reference at low altitude'
#                   // ='cref' for 'Composite Reflectivity'
#                   // ='tops-18' for 'Echo Tops - 18dbz'

$refetchSeconds = 300;  // look for new images every 5 minutes (300 seconds)
$cacheFileDir = './cache/'; // directory for cache files

# for Zulu to local times:
$ourTZ = 'America/Los_Angeles'; // Timezone for display
$timeFormat = 'M d h:ia T';  // Jan 05 09:58am PST
#--------------------------------------------------------------------------------

Note that NWS-regional-radar-animate.php DOES NOT access Settings.php (to increase
speed with some Plugin configurations), so as a minimum, you should change

$ourTZ = 'America/Los_Angeles'; // Timezone for display

to your website's timezone spec (same as used in Settings.php $SITE['tz']) for
proper display of 'local' time on the animated GIF.

The left pane on the [wx]index.php page controlled by USA-regional-maps-inc.php
will use the Settings.php entry of $SITE['NWSregion'] to select which animated
GIF will be presented in the left pane.  You can still use $SITE['leftMap'] to
override the default setting.

Since the aviationweather.gov/radar/ site slices the regional radars a bit differently,
the old $SITE['NWSregion'] designations are now used to select a map close to the old
radar.weather.gov regions.  You may also use the following designations in $SITE['NWSregion']
in case the map displayed is not quite what you had in mind.

For example, the $SITE['NWSregion'] = 'sw' (old designation) actually uses the 'las' (Las Vegas)
view, which doesn't display Central California/Bay Area well, so I prefer using
$SITE['NWSregion'] = 'wmc'; (Winnemucca, NV) for my default site.

For reference, here are the new designations available:

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
  
The USA-regional-maps-inc.php script has been changed:
1) support the new map designations and use NWS-regional-radar-animate.php if available
2) use $SITE['noaaradar'] if $SITE['GR3radar'] was not properly set
3) 'ak' and 'hi' will display local radar as the regional radar images are not available.

The wxnwsradar.php page was updated to display a regional and a CONUS animated GIF if
NWS-regional-radar-animate.php is installed (otherwise, an error message is displayed)
The prior wxnwsradar.php used radar.weather.gov images which are no longer available.

Ken True - webmaster@saratoga-weather.org
06-Jan-2021
