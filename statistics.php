<?php
if(!(isset($adminURL))){
	include ("config.php");
	$websiteTitle						= $pathguideTitle;
	$tabs								= "top_tabs.php";
	$logo								= "logo.gif";
	$menu								= "top_menu.php";
}

include ("$backfolder"."top.php");
include ("$backfolder"."functions_for_statistics.php");

if(isset($adminURL)){
	?>
<font class=Title>Pathguide Administration Section.</font><br><br>
Here you will find a statistical summary of all Pathguide resources.<br>
To create new resource entries or to edit existing ones, go to <a href="<?php print $adminURL; ?>resource_mgmt.php">Resource Management</a>.
To modify Pathguide website components such as the News, go to <a href="<?php print $adminURL; ?>extras.php">Extras</a>.<br><br><hr><br>
	<?php
}
?>
<font class=Title>Statistical Summary of Pathguide Resources</font><br><br>
<?php
$sql											= "SELECT content FROM extras WHERE component='stats'";
$resultStats									= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$dbResultStats									= mysql_fetch_row($resultStats);
print "$dbResultStats[0]<br><br>";
		
if(isset($adminURL)){
	?>
Several enumerations can also display the list of all included resources via the 'show' link.  Clicking a resource will directly take you to Resource Management where you can edit its record.<br><br>
	<?php
}
?>

<table width="100%"><tr><td width="100%" align="center">
	<div style='float: left; width: 49%;'>

<?php printTitle('1', 'General');

$row_color										= "$gray_lite";
$unhighlight_color								= "Gray";

include ("$backfolder"."color_switch_mechanism.php");

$sql											= "SELECT COUNT(DBID) FROM names;";
$resultCount									= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$rowResultCount									= mysql_fetch_row($resultCount);
$totalResourceCount								= $rowResultCount[0];

print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>Total Resources</td><td align=\"right\">$totalResourceCount</td></tr>";

if(isset($adminURL)){

	$sql										= "SELECT COUNT(DBID) FROM names WHERE URL=''";
	$resultCount								= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
	$rowResultCount								= mysql_fetch_row($resultCount);
	
	if ($rowResultCount[0]>0){
		include ("$backfolder"."color_switch_mechanism.php");
		
		print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>Resources without links (<a href=\"javascript:toggle('NoLinks')\"><span id=\"ToggleNoLinks\">show</span></a>)";
		print "<div style=\"display:none;\" id=\"ListNoLinks\"><ul>";
		$sql									= "SELECT DBID, shortname, fullname FROM names WHERE URL='' ORDER BY shortname;";
		$resultCount2							= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		while($rowResultCount2					= mysql_fetch_row($resultCount2)){
			print "<li><a class=\"ListLink\" href=\"javascript:editResource('$rowResultCount2[0]')\">$rowResultCount2[1]: $rowResultCount2[2]</a></li>";
		}
		print "</ul></div></td><td valign=\"top\" align=\"right\">$rowResultCount[0]</td></tr>";
	}
	
	$sql										= "SELECT COUNT(DISTINCT names.DBID) FROM names, pmids WHERE names.DBID=pmids.DBID AND title!='' AND authors!='' AND citation!='';";
	$resultCount								= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
	$rowResultCount								= mysql_fetch_row($resultCount);
	
	if ($totalResourceCount - $rowResultCount[0]>0) {
		include ("$backfolder"."color_switch_mechanism.php");

		print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>Resources with incomplete publication information (<a href=\"javascript:toggle('IncompletePMInfo')\"><span id=\"ToggleIncompletePMInfo\">show</span></a>)";
		print "<div style=\"display:none;\" id=\"ListIncompletePMInfo\"><ul>";

		$sql									= "SELECT names.DBID, shortname, fullname FROM names LEFT JOIN pmids ON names.DBID=pmids.DBID WHERE pmids.DBID IS NULL UNION SELECT names.DBID, shortname, fullname FROM names, pmids WHERE names.DBID=pmids.DBID AND (title='' OR authors='' OR citation='') ORDER BY shortname;";
		$resultCount2							= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		while($rowResultCount2					= mysql_fetch_row($resultCount2)){
			print "<li><a class=\"ListLink\" href=\"javascript:editResource('$rowResultCount2[0]')\">$rowResultCount2[1]: $rowResultCount2[2]</a></li>";
		}
		print "</ul></div></td><td valign=\"top\" align=\"right\">".($totalResourceCount-$rowResultCount[0])."</td></tr>";
	}
	
	$sql										= "SELECT COUNT(DBID) FROM names WHERE DataSource='Unknown'";
	$resultCount								= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
	$rowResultCount								= mysql_fetch_row($resultCount);
	
	if ($rowResultCount[0]>0) {
		include ("$backfolder"."color_switch_mechanism.php");

		print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>Resources with Unknown Data Sources (<a href=\"javascript:toggle('UnknownDataSources')\"><span id=\"ToggleUnknownDataSources\">show</span></a>)";
		print "<div style=\"display:none;\" id=\"ListUnknownDataSources\"><ul>";
		
		$sql									= "SELECT DBID, shortname, fullname FROM names WHERE DataSource='Unknown' ORDER BY shortname;";
		$resultCount2							= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		while($rowResultCount2					= mysql_fetch_row($resultCount2)){
			print "<li><a class=\"ListLink\" href=\"javascript:editResource('$rowResultCount2[0]')\">$rowResultCount2[1]: $rowResultCount2[2]</a></li>";
		}
		print "</ul></div></td><td valign=\"top\" align=\"right\">$rowResultCount[0]</td></tr>";
	}
}

include ("$backfolder"."color_switch_mechanism.php");

$sql											= "SELECT COUNT(DBID) FROM names WHERE DataSource='Primary'";
$resultCount									= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$rowResultCount									= mysql_fetch_row($resultCount);

print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>Resources with Primary Data Sources</td><td align=\"right\">$rowResultCount[0]</td></tr>";

include ("$backfolder"."color_switch_mechanism.php");

$sql											= "SELECT COUNT(DBID) FROM names WHERE DataSource='Secondary'";
$resultCount									= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$rowResultCount									= mysql_fetch_row($resultCount);

print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>Resources with Secondary Data Sources</td><td align=\"right\">$rowResultCount[0]</td></tr>";



#for the next couple statistics sections we use the same sql statement so this function constructs it
function getSQL($table1, $table2, $text, $joinID){
	$sqlStatement								= "SELECT COUNT(t1.DBID) AS number, t2.$text FROM $table1 AS t1 INNER JOIN $table2 AS t2 ON t1.$joinID=t2.$joinID GROUP BY t1.$joinID ORDER BY number DESC";
	return $sqlStatement;
}
		
 print "</table></td></tr></table></td></tr></table>";
 printTitle('2', 'Availability');

#row color is initialized as gray so that the first row will be white due to the color switching mechanism
$row_color										= "$gray_lite";
$unhighlight_color								= "Gray";

$sql											= getSQL("dbs_in_availability_types", "availability_types", "description", "availability_ID");
$resultCount									= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}



while($rowResultCount							= mysql_fetch_row($resultCount)){
	include ("$backfolder"."color_switch_mechanism.php");
	print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>$rowResultCount[1]";

	if(isset($adminURL)){

		if($rowResultCount[1]=="Unknown"){
			getResources('AvailabilityUnknown', 'availability', '101',	$cn);
		}elseif($rowResultCount[1]=="Not currently available"){
			getResources('AvailabilityNone',	'availability', '4',	$cn);
		}

	}

	print "</td><td valign=\"top\" align=\"right\">$rowResultCount[0]</td></tr>";
}

print "</table></td></tr></table></td></tr></table>";
printTitle('3', 'Data Access Methods');

#row color is initialized as gray so that the first row will be white due to the color switching mechanism
$row_color										= "$gray_lite";
$unhighlight_color								= "Gray";

$sql											= getSQL("dbs_in_download_types", "download_types", "description", "download_ID");
$resultCount									= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
while($rowResultCount							= mysql_fetch_row($resultCount)){
	include ("$backfolder"."color_switch_mechanism.php");
	print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>$rowResultCount[1]</td><td align=\"right\">$rowResultCount[0]</td></tr>";
}

print "</table></td></tr></table></td></tr></table>";
printTitle('4', 'Tools');

#row color is initialized as gray so that the first row will be white due to the color switching mechanism
$row_color										= "$gray_lite";
$unhighlight_color								= "Gray";

$sql											= getSQL("dbs_in_tool_types", "tool_types", "description", "tool_ID");
$resultCount									= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
while($rowResultCount							= mysql_fetch_row($resultCount)){
	include ("$backfolder"."color_switch_mechanism.php");
	print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>$rowResultCount[1]</td><td align=\"right\">$rowResultCount[0]</td></tr>";
}

print "</table></td></tr></table></td></tr></table>";
print "</div><div style='float: right; width: 49%;'>";

printTitle('5', 'Organisms');

#row color is initialized as gray so that the first row will be white due to the color switching mechanism
$row_color										= "$gray_lite";
$unhighlight_color								= "Gray";

$sql											= getSQL("dbs_in_organism_types", "organism_types", "scientific_name", "organism_ID");
$resultCount									= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
while($rowResultCount							= mysql_fetch_row($resultCount)){
	include ("$backfolder"."color_switch_mechanism.php");
	print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>$rowResultCount[1]</td><td align=\"right\">$rowResultCount[0]</td></tr>";
}

print "</table></td></tr></table></td></tr></table>";
printTitle('6', 'Categories');

#row color is initialized as gray so that the first row will be white due to the color switching mechanism
$row_color										= "$gray_lite";
$unhighlight_color								= "Gray";

$sql											= getSQL("dbs_in_categories", "db_categories", "CategoryDescription", "CategoryID");
$resultCount									= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
while($rowResultCount							= mysql_fetch_row($resultCount)){
	include ("$backfolder"."color_switch_mechanism.php");
	print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>$rowResultCount[1]</td><td align=\"right\">$rowResultCount[0]</td></tr>";
}
#also have to include resources categorized as 'other'
include ("$backfolder"."color_switch_mechanism.php");

$sql											= "SELECT COUNT(names.DBID) FROM names LEFT JOIN dbs_in_categories USING (DBID) WHERE dbs_in_categories.DBID IS NULL";
$resultCount									= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
$rowResultCount									= mysql_fetch_row($resultCount);
if($rowResultCount[0]!="0"){
	print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>Other";
	if(isset($adminURL)){
		print " (<a href=\"javascript:toggle('CategoriesOther')\"><span id=ToggleCategoriesOther>show</span></a>)";
		print "<div id=ListCategoriesOther style=\"display:none;\"><ul>";
		$sql									= "SELECT names.DBID, names.shortname, names.fullname FROM names LEFT JOIN dbs_in_categories USING (DBID) WHERE dbs_in_categories.DBID IS NULL ORDER BY names.ShortName";
		$resultDBs								= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		while($rowResultDBs						= mysql_fetch_row($resultDBs)){
			print "<li><a class=\"ListLink\" href=\"javascript:editResource('$rowResultDBs[0]')\">$rowResultDBs[1]: $rowResultDBs[2]</a></li>";
		}
		print "</ul></div>";
	}
	print "</td><td align=\"right\" valign=\"top\">$rowResultCount[0]</td></tr>";
}

print "</table></td></tr></table></td></tr></table>";
printTitle('7', 'Contents');

#row color is initialized as gray so that the first row will be white due to the color switching mechanism
$row_color										= "$gray_lite";
$unhighlight_color								= "Gray";

include ("$backfolder"."color_switch_mechanism.php");
getContentsEnumeration('SMS',	'small molecules',			$row_color, $unhighlight_color, $cn);
include ("$backfolder"."color_switch_mechanism.php");
getContentsEnumeration('GPS',	'genes/proteins',			$row_color, $unhighlight_color, $cn);
include ("$backfolder"."color_switch_mechanism.php");
getContentsEnumeration('IRS',	'interactions/reactions',	$row_color, $unhighlight_color, $cn);
include ("$backfolder"."color_switch_mechanism.php");
getContentsEnumeration('PWS',	'pathways',					$row_color, $unhighlight_color, $cn);
include ("$backfolder"."color_switch_mechanism.php");
getContentsEnumeration('XPS',	'experiments',				$row_color, $unhighlight_color, $cn);
include ("$backfolder"."color_switch_mechanism.php");

if(isset($adminURL)){
	#Finally, all the resources that have no entries for contents other than the date they were last modified at
	$sql										= "SELECT COUNT(DBID) FROM contents WHERE SMS=0 and GPS=0 and IRS=0 and PWS=0 and XPS=0";
	$resultCount								= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
	$rowResultCount								= mysql_fetch_row($resultCount);
	print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>Resources lacking any contents information</td><td align=\"right\">$rowResultCount[0]</td></tr>";
}

print "</table></td></tr></table></td></tr></table>";
print "</div></td></tr></table>";

if(!(isset($adminURL))){
	print "<table><tr><td style=\"height:6;\"></td></tr></table><table class=\"HorizontalDivider\" width=\"100%\"><tr><td width=\"100%\" align=\"right\"><a href=\"javascript:formSubmit('false','$rootURL','none')\">Back to the List</a></td></tr></table><br>";
}

?>

<form action="resource_mgmt.php" method="GET" id="goEdit">
<input type="hidden" id="resource" value="dbid" name="resource">
</form>

<script type="text/javascript">

function toggle(section){
	var toggle											= "Toggle"+section;
	var list											= "List"+section;

	var span											= document.getElementById(toggle);
	var div												= document.getElementById(list);

	if(span.innerHTML=="show"){
		div.style.display								= "";
		span.innerHTML									= "hide";
	}else{
		div.style.display								= "none";
		span.innerHTML									= "show";
	}
}

function editResource(DBID){
	document.getElementById('resource').value			= DBID;
	document.getElementById('goEdit').submit();
}

</script>

<?php
if(!(isset($adminURL))){
	$search_helper=true;
}
include ("$backfolder"."bottom.php");
?>