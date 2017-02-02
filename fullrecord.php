<?php
if(!(isset($adminURL))){
	include ("config.php");
	$websiteTitle								= $pathguideTitle."|Full Record";
	$tabs										= "top_tabs.php";
	$logo										= "logo.gif";
	$menu										= "top_menu.php";
}

include("$backfolder"."top.php");

$sql											= "SELECT MAX(DBID) FROM names";
$result											= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$largest_DBID									= mysql_fetch_row($result);

$DBID											= $fullRecordDBID;
settype($DBID, "integer");
if ($DBID > $largest_DBID[0]) {$DBID			= $largest_DBID[0];}
if ($DBID < 1) {$DBID							= 1;}

$sql											= "SELECT ShortName FROM names WHERE DBID='$DBID'";
$result											= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$sn 											= mysql_fetch_row($result);

print "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\"><tr><td class=\"CategoryTitles\">";
if(!(isset($adminURL))){
	print "Full Record: ";
}else{
	print "Preview: ";
}
print "$sn[0]</td><td class=\"CategoryTitlesEnder\">&nbsp;</td><td class=\"CategoryTitles\" width=\"10\">&nbsp;</td></tr></table>";

print "\n<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"height:6;\"></td></tr><tr><td class=\"TableListTop\" style=\"background-position: 120 0;\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"TableListBottom\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">";


$row_color										= "";

# Print everything from names table
$sql 											= "SELECT DBID, ShortName AS 'Short Name', FullName AS 'Full Name', URL, DATE_FORMAT(timestamp,'%M %D, %Y') AS 'Last Observed', Description, SampleDataURL AS 'Sample Data URL', DataSource AS 'Data Source' FROM names WHERE DBID='$DBID'";
$result											= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$resultFields 									= array();
$i												= 0;
while ($i < mysql_num_fields($result)) {
    $meta										= mysql_fetch_field($result);
    if (!$meta) {
        #echo "No information available<br />\n";
    }
    else {
        $resultFields["$meta->name"]			= 1;
    }
    $i++;
}
$myRow											= mysql_fetch_row($result);
$keys_array										= array_keys($resultFields);
for ($counter=0; $counter < count($keys_array); $counter++) {
    $sam										= $keys_array[$counter];
	if ($sam != "DBID") {
		print "<tr><td class=\"TableSideHeader\">$sam</td><td bgcolor='";
		include ("$backfolder"."color_switch_mechanism.php");
    	print "$row_color' >";
    	if ($myRow[$counter]) {
			if ($sam == "URL" || $sam == "Sample Data URL") {
				print "<a href='$myRow[$counter]'>";
				print substr($myRow[$counter], 0, 80);
				if(substr($myRow[$counter], 80)){
					print "...";
				}
				print "</a>";
			}else{
				print "$myRow[$counter]";
			}
    	}else{
			print "&nbsp;";
		}
    	print "</td></tr>";
    }
}

# Print Avialability
$sql											= "SELECT availability_types.description FROM availability_types, dbs_in_availability_types WHERE dbs_in_availability_types.DBID='$DBID' AND dbs_in_availability_types.Availability_ID=availability_types.Availability_ID";
$result											= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$myRow 											= mysql_fetch_row($result);
print "<tr><td class=\"TableSideHeader\">Availability</td><td bgcolor='";
include ("$backfolder"."color_switch_mechanism.php");
print "$row_color' >";
print "$myRow[0]</td></tr>";

# Print PMIDs
$myRows											= "";
$sql 											= "SELECT PMID, title, authors, citation FROM pmids WHERE DBID='$DBID'";
$result 										= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
while ($myRow									= mysql_fetch_row($result)) {
    $myRows									   .= "<a href='" . "http://www.ncbi.nlm.nih.gov/entrez/query.fcgi?cmd=Retrieve&db=pubmed&dopt=Abstract&list_uids=" . $myRow[0] . "'>";
	if($myRow[1]!=""){
		$myRows								   .= "$myRow[1]</a><br><font class=\"Note\">$myRow[2]<br>$myRow[3]</font><br><br>";
	}else{
		$myRows								   .= "$myRow[0]</a><br>";
	}
}

print "<tr><td class=\"TableSideHeader\">PubMed Articles</td><td bgcolor='";
include ("$backfolder"."color_switch_mechanism.php");
print "$row_color' >";
$myRowWithoutLineBreak							= "";
if ($myRows!="") $myRowWithoutLineBreak			= substr($myRows, 0, -4);
print "$myRowWithoutLineBreak</td></tr>";

# Print dbs_in_categories
$myRows											= "";
$sql											= "SELECT CategoryDescription FROM db_categories, dbs_in_categories WHERE dbs_in_categories.DBID='$DBID' AND dbs_in_categories.CategoryID=db_categories.CategoryID";
$result 										= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
while ($myRow									= mysql_fetch_row($result)) {
    $myRows									   .= "$myRow[0], ";
}
print "<tr><td class=\"TableSideHeader\">Types of Data</td><td bgcolor='";
include ("$backfolder"."color_switch_mechanism.php");
print "$row_color' >";
$myRowWithoutTrailingComma						= "";
if ($myRows!="") $myRowWithoutTrailingComma		= substr($myRows, 0, -2);
print "$myRowWithoutTrailingComma</td></tr>";

# Print dbs_in_tool_types
$myRows											= "";
$sql											= "SELECT description FROM tool_types, dbs_in_tool_types WHERE dbs_in_tool_types.DBID='$DBID' AND dbs_in_tool_types.tool_ID=tool_types.tool_ID";
$result											= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
while ($myRow 									= mysql_fetch_row($result)) {
    $myRows									   .= "$myRow[0], ";
}
print "<tr><td class=\"TableSideHeader\">Types of Tools</td><td bgcolor='";
include ("$backfolder"."color_switch_mechanism.php");
print "$row_color' >";
$myRowWithoutTrailingComma						= "";
if ($myRows!="") $myRowWithoutTrailingComma		= substr($myRows, 0, -2);
print "$myRowWithoutTrailingComma</td></tr>";

# Print contents
print "<tr><td class=\"TableSideHeader\">Contents</td><td bgcolor='";
include ("$backfolder"."color_switch_mechanism.php");
print "$row_color' >";
print "<table>";
$noContents = true;
$sql 											= "SELECT SMS FROM contents WHERE DBID='$DBID'";
$result 										= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$myRow 											= mysql_fetch_row($result);
if ($myRow[0] > 0) {
	print "<tr><td>Small Molecules:&nbsp;</td><td>".number_format($myRow[0])."</td></tr>";
	$noContents = false;
}

$sql											= "SELECT GPS from contents WHERE DBID='$DBID'";
$result 										= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$myRow 											= mysql_fetch_row($result);
if ($myRow[0] > 0) {
	print "<tr><td>Genes / Proteins:&nbsp;</td><td>".number_format($myRow[0])."</td></tr>";
	$noContents = false;
}

$sql											= "SELECT IRS FROM contents WHERE DBID='$DBID'";
$result 										= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$myRow 											= mysql_fetch_row($result);
if ($myRow[0] > 0) {
	print "<tr><td>Interactions / Reactions:&nbsp;</td><td>".number_format($myRow[0])."</td></tr>";
	$noContents = false;
}

$sql 											= "SELECT PWS FROM contents WHERE DBID='$DBID'";
$result 										= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$myRow 											= mysql_fetch_row($result);
if ($myRow[0] > 0) {
	print "<tr><td>Pathways:&nbsp;</td><td>".number_format($myRow[0])."</td></tr>";
	$noContents = false;
}

$sql 											= "SELECT XPS FROM contents WHERE DBID='$DBID'";
$result 										= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$myRow 											= mysql_fetch_row($result);
if ($myRow[0] > 0) {
	print "<tr><td>Experiments / PubMed IDs:&nbsp;</td><td>".number_format($myRow[0])."</td></tr>";
	$noContents = false;
}

if ($noContents) print "<tr><td>Statistics are unavailable</td></tr>";

$sql 											= "SELECT DATE_FORMAT(LastUpdate,'%M %D, %Y') FROM contents WHERE DBID='$DBID'";
$result 										= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$myRow 											= mysql_fetch_row($result);
print "<tr><td>Last Content Update:&nbsp;</td><td>$myRow[0]";
if ($myRow[0] == "") print "unknown";
print "</td></tr>";
print "</table>";
print "</td></tr>";

# Print organisms
$myRows											= "";
$sql 											= "SELECT scientific_name, taxonomy_ID FROM organism_types, dbs_in_organism_types WHERE dbs_in_organism_types.DBID='$DBID' AND dbs_in_organism_types.organism_ID=organism_types.organism_ID";
$result 										= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
while ($myRow 									= mysql_fetch_row($result)) {
    $myRows									   .= "<a href=\"$pubmed_link_taxonomy"."$myRow[1]\">$myRow[0]</a>, ";
}
print "<tr><td class=\"TableSideHeader\">Major Organisms</td><td bgcolor='";
include ("$backfolder"."color_switch_mechanism.php");
print "$row_color' >";
$myRowWithoutTrailingComma						= "";
if ($myRows!="") $myRowWithoutTrailingComma		= substr($myRows, 0, -2);
print "$myRowWithoutTrailingComma</td></tr>";

# Print popularity
$sql											= "SELECT * FROM scores WHERE DBID='$DBID'";
$result											= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$myRow 											= mysql_fetch_row($result);
print "<tr><td class=\"TableSideHeader\">Relative Popularity</td><td bgcolor='";
include ("$backfolder"."color_switch_mechanism.php");
print "$row_color' >";
print "$myRow[1]</td></tr>";

# Print uptime
$sql											= "SELECT HostTrackerID FROM names WHERE DBID='$DBID' AND HostTrackerID!=''";
$result											= mysql_query($sql);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
print "<tr><td class=\"TableSideHeader\">Homepage Uptime Statistics</td><td bgcolor='";
include ("$backfolder"."color_switch_mechanism.php");
print "$row_color' >";
while($myRow									= mysql_fetch_row($result)){
	print "<a href='http://www.host-tracker.com/UptimeGraph/UptimeInfo/$myRow[0]/' target='_blank'><img id='HostTrackerInformer' width='80' height='15' border='0' title='Website tracker - uptime monitoring service Host-tracker.com' alt='Website tracker - uptime monitoring service Host-tracker.com' src='//i.h-t.co/website ping.png?id=$myRow[0]' /></a>";
}
print "</td></tr>";

print "</table></td></tr></table></td></tr><tr><td style=\"height:6;\"></td></tr></table><table class=\"HorizontalDivider\" width=\"100%\"><tr><td width=\"100%\" align=\"right\">";

if(isset($adminURL)){
?>

<form action="resource_mgmt.php" method="GET">
<input type="hidden" value="<?php print $DBID; ?>" name="resource">
<input type="submit" value="Edit Resource">
</form>

<?php

}else{
	print "<a href=\"javascript:formSubmit('false','$rootURL','none')\">Back to the List</a>";
	$search_helper								= true;
}
print "</td></tr></table><br>";

include("$backfolder"."bottom.php");
?>
