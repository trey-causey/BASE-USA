This Base-USA update adds a new method to establish which map graphics are displayed above the
ajax-dashboard on the (wx)index.php page.  A new script USA-regional-maps.inc replaces
the HTML on (wx)index.php page to allow easy configuration of regional radar (NWS) and
IR Satellite (WU) maps by specifications in Settings.php. See below for using custom images
(webcam, lightning, etc) with links to full pages from the thumbnails.

Basic configuration:

The new NWS map specification in Settings.php uses:

$SITE['NWSregion'] = 'sw'; // NOAA/NWS regional radar maps
// 'ak' = Alaska,
// 'nw' = Northwest, 'nr' = Northern Rockies, 'nm' = North Mississippi Valley, 
// 'nc' = Central Great Lakes,  'ne' = Northeast,
// 'hi' = Hawaii,
// 'sw' = Southwest, 'sr' = Southern Rockies, 'sc' = Southern Plains,
// 'sm' = South Mississippi Valley, 'se' = Southeast

for the left-hand map.

The WU IR Satellite map specification uses (the existing):

$SITE['WUsatellite'] = 'sw'; // Wunderground regional satellite maps
// ="nw" North West, ="nc" North Central, ="ne" North East,
// ="wc" West Central, ="ce" Central, ="ec" East Central,
// ="sw" South West, ="sc" South Central, ="se" South East,
// ="us" United States (Conus)

for the right-hand map.

Either or both can be overridden by custom settings:

$SITE['leftMap']  = '<name>|<URL>[|<link-to-page>]';
$SITE['rightMap'] = '<name>|<URL>[|<link-to-page>]';

where:
<name> is the name of the map to display on mouseover (in alt= and title=)
<URL>  is the URL (fully qualified or relative) of the img file to display
<link-to-page> is the OPTIONAL URL (fully qualified or relative) that will be
       used in an <a href=... with a title="Click to open <name> page in new tab."

Example of override for right-hand map:

$SITE['rightMap'] = 'Blitzortung Lightning|./BOmap/cache/BONorthAmerica-sm-ani.gif|/NA-lightning.php|';
(above assumes a NA-lightning.php page at the document root of your website)

$SITE['rightMap'] = 'Webcam|http://saratogawx.dyndns.org:18080/netcam.php?nocache|/netcam.php|';

-------------manual installation instructions -----------------

Edit (wx)index.php and replace:
------
  <table width="99%" style="border: none">
  <tr><td align="center">
    <img src="https://icons.wunderground.com/data/640x480/<?php echo $SITE['WUregion']; ?>_rd_anim.gif" alt="Regional Radar" width="320" height="240" style="margin: 0px; padding: 0px; border: none" />
  </td>
  <td align="center">
    <img src="https://s.w-x.co/staticmaps/wu/wu/satir1200_cur/usa<?php echo $SITE['WUsatellite']; ?>/animate.png" alt="Regional Infrared Satellite"  
      width="320" height="240" style="margin: 0px; padding: 0px; border: none" />  </td>
  </td>
  </tr>
  <tr><td colspan="2" align="center"><small>Radar/Satellite images courtesy of <a href="http://www.weatherunderground.com">Weather Underground</a>.</small></td></tr>
  </table>
------

with
------
<?php if(file_exists('USA-regional-maps-inc.php')) { include_once('USA-regional-maps-inc.php'); } ?>
------
and save/upload the file.

Edit Settings.php and add (near the $SITE['WUsatellite'] entry):

------
$SITE['NWSregion'] = 'sw'; // NOAA/NWS regional radar maps
// 'ak' = Alaska,
// 'nw' = Northwest, 'nr' = Northern Rockies, 'nm' = North Mississippi Valley, 
// 'nc' = Central Great Lakes,  'ne' = Northeast,
// 'hi' = Hawaii,
// 'sw' = Southwest, 'sr' = Southern Rockies, 'sc' = Southern Plains,
// 'sm' = South Mississippi Valley, 'se' = Southeast
------
and change the setting to one of the selected regional map entries shown in the comments.

You should already have in your Settings.php an entry:
------
$SITE['WUsatellite'] = 'sw'; // Wunderground regional satellite maps
// ="nw" North West, ="nc" North Central, ="ne" North East,
// ="wc" West Central, ="ce" Central, ="ec" East Central,
// ="sw" South West, ="sc" South Central, ="se" South East,
// ="us" United States (Conus)
------
already configured for the WU IR Satellite map of your choice.. if not, add that and change
the entry to your selected map to use.

Save Settings.php and upload to your site.  Your (wx)index.php should now show the maps
you selected.   Note: if you make a selection not in the list, the 'sw' entry will be the
default display.

