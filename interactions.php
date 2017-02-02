<?php 
include("config.php");
#before we include top, we must set a couple variables to indicate that this is a pathguide page and not the admin page (compare to admin pages)
$websiteTitle = $pathguideTitle;
$tabs         = "top_tabs.php";	
$logo         = "logo.gif";
$menu         = "top_menu.php";
$isDBInteractions = true;
include("top.php");
?>

<p><font class="Title">Database Interactions</font></p>
<p>
	<font class="FormLabel">Network</font> 
	<select id="NetworksSelect" onchange="changeNetwork();">
		<option value="AllPathwaysDB">All (Pathways) Databases</option>
		<option value="ExchangeFormats">Exchange Formats</option>
		<option value="InteractionDB">Interaction Databases</option>
		<option value="MetaminingDB">Metamining Databases</option>
		<option value="PathwaysDB">Pathway Databases</option>
		<option value="PredictiveInteractionDB">Predictive Interaction Databases</option>
		<option value="UnifyingEffortsDB">Unifying Efforts Databases</option>
	</select>
</p>
<p id="NetworkDescription"> </p>
<p class="Note">Selecting node(s) shows a summary of database information below the network, with linkouts to database details from Pathguide, and to the database itself.</p>
<div id="NetworkVis"></div>
<div id="NetworkVisMenu"></div>
<br>
<table cellspacing="0" cellpadding="2" width="100%">
	<tr><td class="CategoryTitles">Resources</td><td class="CategoryTitlesEnder"> </td><td width="10" class="CategoryTitles"> </td></tr>
</table>
<table cellspacing="0" cellpadding="2" width="100%" class="ColumnTitles">
	<tr>
		<td>Database Name</td>
		<td width="166" align="left">Categories</td>
		<td width="70" align="center">Full Record</td>
		<td width="50" align="center">Availability</td>
		<td width="50" align="center">Standards</td>
	</tr>
</table>
<table cellspacing="0" cellpadding="2" width="100%">
	<tr><td style="height:6px;"> </td></tr>
</table>
<table id="SelectedNetRes" width="100%" cellspacing="0" cellpadding="2"></table>
<table cellspacing="0" cellpadding="2" width="100%">
	<tr><td style="height:6px;"> </td></tr>
</table>
<table width="100%" class="HorizontalDivider">
	<tr><td width="100%" align="right"><a href="#top">Back to the Top</a></td></tr>
</table>
<br>

<table cellspacing="0" cellpadding="2" width="100%">
	<tr><td class="CategoryTitles">Legends</td><td class="CategoryTitlesEnder"> </td><td width="10" class="CategoryTitles"> </td></tr>
</table>
<table cellspacing="0" cellpadding="2" width="100%">
	<tr><td style="height:6px;"> </td></tr>
	<tr><td style="text-align:center;">
		<div id="NetworkLegendsBox">
		<div id="ResourceTypeLegend"> 
			<table class="NetworkLegend" cellspacing="0" cellpadding="0" width="100%">
				<tr><th colspan="2">Resource Type</th></tr>
				<tr><td class="LegendResTypeInter IconResType" style="width=18px;"> </td><td>Interactions</td></tr>
				<tr><td class="LegendResTypePath IconResType"> </td><td>Pathways</td></tr>
				<tr><td class="LegendResTypePred IconResType"> </td><td>Predictive interactions</td></tr>
				<tr><td class="LegendResTypeMeta IconResType"> </td><td>Metamining</td></tr>
				<tr><td class="LegendResTypeExch IconResType"> </td><td>Exchange format language</td></tr>
				<tr><td class="LegendResTypeUnif IconResType"> </td><td>Unifying efforts</td></tr>
				<tr><td class="LegendResTypeNot IconResType"> </td><td>Not categorized</td></tr>
			</table>
		</div>
		<div id="InteractionTypeLegend"> 
			<table class="NetworkLegend" cellspacing="0" cellpadding="0" width="100%">
				<tr><th colspan="3">Interaction Type</th></tr>
				<tr title="The source database is mined by the connected database"><td>Source </td><td class="IconInterTypeMines"> </td><td> Mining source data</td></tr>
				<tr title="The source database is mapped by the connected database"><td>Source </td><td class="IconInterTypeMaps"> </td><td> Maps to source</td></tr>
				<tr title="The two databases have an exchange agreement where data can be shared in both directions"><td> </td><td class="IconInterTypeExch"> </td><td> Bidirectional exchange agreement</td></tr>
			</table>
		</div>
		</div>
	</td></tr>
</table>
<table width="100%" class="HorizontalDivider">
	<tr><td width="100%" align="right"><a href="#top">Back to the Top</a></td></tr>
</table>
<hr/>

<table>
	<tr>
		<td valign="top"><img src="<?php print $imagesURL; ?>icon_note_large.gif" alt=""></td>
		<td class="Note">The networks are adapted from:
			<b>A graphical view of the world of protein-protein interaction databases</b>, by <b>Tomas Klingstr&ouml;m</b> and 
			<a href="http://www.icm.edu.pl/~darman">Dariusz Plewczynski</a>.
			ICM, Interdisciplinary Centre  for Mathematical and Computational Modelling, <a href="http://www.uw.edu.pl/en/">University of Warsaw</a>, Poland.
			The work was supported by Polish Ministry of Science and Higher Education N301 159735 grant.<br/>
	    </td>
	</tr>
	<tr>
		<td valign="top"><img src="<?php print $imagesURL; ?>icon_note_large.gif" alt=""></td>
		<td class="Note">The networks are visualized with <a href="http://cytoscapeweb.cytoscape.org/">Cytoscape Web</a>.</td>
	</tr>
</table>

<table id="HiddenResources" width="100%" cellspacing="0" cellpadding="2">
	<tr id="ResourceRow_TEMPLATE" class="ResourceRow" onmouseout="unhighlight($(this))" onmouseover="highlight($(this))">
		<td> </td>
		<td width="166"> </td>
		<td width="70" align="center"> </td>
		<td width="50" align="center"> </td>
		<td width="50" align="center"> </td>
	</tr>

<?php
include('functions_for_categories.php');

$sql = "SELECT names.DBID, ShortName, FullName, URL, GROUP_CONCAT(CONCAT('. ',CategoryDescription) SEPARATOR '<br>') AS Categories "
     . "FROM (names LEFT JOIN dbs_in_categories USING (DBID)) "
     . "LEFT JOIN db_categories USING (CategoryID) "
     . "GROUP BY names.DBID;";

$result = mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}

$row_count = 0;
while ($row = mysql_fetch_row($result)) {
	$DBID		= $row[0];
	$ShortName	= $row[1];
	$FullName	= $row[2];
	$URL		= $row[3];
	$Categories	= $row[4];
	
	getAvailabilityAndStandards($DBID, $ShortName, $FullName, $URL, $Categories, $cn, $standard_container_left, $standard_container_right, $green, $rootURL, $imagesURL, $row_count, "alphabetic", $debugMode);
	$row_count += 1;
}
?>
</table>

<script type="text/javascript">
$(document).ready(function() {
	changeNetwork();
});
</script>

<?php
$search_helper = true;
include("bottom.php");
?>