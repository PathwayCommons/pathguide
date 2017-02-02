<?php

$imagesURL						= "$rootURL"."images/";						#used to provide a direct path to images in the page so that both the root and admin can see them

$pathguideTitle					= "Pathguide: the pathway resource list";	#the title of the page in the root directory
$adminTitle						= "Pathguide: administration";				#the title of the page in the admin directory

#COLOR VARIABLES

$gray_lite						= "#F3F3F3";								#used for alternating rows and menu
$gray_med						= "#C0C0C0";								#used for lines and dividers
$gray_dark						= "#808080";								#used for supplemental text like notes and footers
$blue_bright					= "#3366FF";								#used for links
$blue_dull						= "#4D6673";								#used for visited resource links
$blue_lite						= "#E1E8F0";								#used for the menu
$blue_med						= "#99BBCC";								#used for standards
$blue_dark						= "#336699";								#used for titles
$blue_xdark						= "#12394D";								#used for resource links and menu items
$green							= "#9EB637";								#used for availability

#COMPONENT WIDTHS

$sqlWidth						= "SELECT content FROM extras WHERE component='websitewidth'";
$resultWidth					= mysql_query($sqlWidth, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlWidth,$debugMode);}
$rowResultWidth					= mysql_fetch_row($resultWidth);
$website_width					= $rowResultWidth[0];

$sqlWidth						= "SELECT content FROM extras WHERE component='menuwidth'";
$resultWidth					= mysql_query($sqlWidth, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlWidth,$debugMode);}
$rowResultWidth					= mysql_fetch_row($resultWidth);
$menu_width						= $rowResultWidth[0];

$row_header_width				= "150"; 									#used for full record display
$input_width					= $website_width-$menu_width-200;			#used for all the input fields on the contact.php, resource_mgmt.php and extras.php pages

#OTHER

$standard_container_left		= "<div style='cursor:default;'><table cellspacing=\"0\" cellpadding=\"0\"><tr style=\"height:1;\"><td></td><td></td><td></td></tr><tr><td><img src=\"$imagesURL"."standard_left.gif\" alt=\"\"></td><td class=\"Standard\">";
$standard_container_right		= "</td><td><img src=\"$imagesURL"."standard_right.gif\" alt=\"\"></td></tr><tr style=\"height:1;\"><td></td><td></td><td></td></tr></table></div>";

?>