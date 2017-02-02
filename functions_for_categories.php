<?php

function printCategoryTitle($categoryID, $category, $current_category_num, $rootURL, $imagesURL, $order){
	#the category title
	if ($order == "alphabetic") {
		$displayAlphabetic = "none";
		$displayPopularity = "";
	} else {
		$displayAlphabetic = "";
		$displayPopularity = "none";
	}
	?>
	<a name="<?php print $categoryID; ?>"></a>
	<table width="100%" cellspacing="0" cellpadding="2"><tr><td class="CategoryTitles"><?php print $category;?></td><td class="CategoryTitlesEnder">&nbsp;</td><td class="CategoryTitles" width="10">&nbsp;</td></tr></table>
	<?php
	#the column titles, only one of the spans is visible at any one time depending on whether alphabetic or popularity listing is selected, alphabetic is set by default at the bottom of the document by calling the javascript order function
	?>
	<table width="100%" cellspacing="0" cellpadding="2" class="ColumnTitles">
		<tr>
			<td>Database Name (Order:
				<span id='alphabeticlink<?php print $current_category_num; ?>' style="display:<?php print $displayAlphabetic; ?>;">
					<a href="javascript:order('alphabetic','popularity')">alphabetically</a>
					| by web popularity <a href="#PopularityNote"><img class="help" src="<?php print $imagesURL; ?>icon_note_small.gif" alt="Note" border="0" title="Note on Web Popularity"></a>
				</span>
				<span id='popularitylink<?php print $current_category_num; ?>' style="display:<?php print $displayPopularity; ?>;">
					alphabetically |
					<a href="javascript:order('popularity','alphabetic')">by web popularity</a> <a href="#PopularityNote"><img class="help" src="<?php print $imagesURL; ?>icon_note_small.gif" alt="Note" border="0" title="Note on Web Popularity"></a>
				</span>
				)
			</td>
			<td width="70" align="center">Full Record</td>
			<td width="50" align="center">Availability</td>
			<td width="50" align="center">Standards</td>
		</tr>
	</table>
	<?php
	#since the menu is only filled when a title is printed, the menu has no categores when we are looking at a full record page since there are now titles there, so these session arrays store the cummulative category titles, they are unset on top of the index page so that the list doesnt repeat and so that it is only used in the menu when it is set (look at top.html)
	if(isset($_SESSION['categoryIDsArray'])){
		$categoryIDsArray					= $_SESSION['categoryIDsArray'];
		$categoryNamesArray					= $_SESSION['categoryNamesArray'];
	}else{
		$categoryIDsArray					= array();
		$categoryNamesArray					= array();
	}
	array_push($categoryIDsArray, $categoryID);
	array_push($categoryNamesArray, $category);
	$_SESSION['categoryIDsArray']			= $categoryIDsArray;
	$_SESSION['categoryNamesArray']			= $categoryNamesArray;

	#here we add rows to the menu only after the category title has been printed
	?>
	<script type="text/javascript">
		var menu							= document.getElementById("Navigation");
		var row								= menu.insertRow(menu.rows.length);
		var cell							= row.insertCell(0);
		cell.innerHTML						= "<a class='MenuLink' href='<?php print $rootURL; ?>#<?php print $categoryID; ?>'><?php print $category; ?></a>";
	</script>
	<?php
	#beginning of the list
	?>
	<table width="100%" cellspacing="0" cellpadding="0">
		<tr><td style="height:6;"></td></tr>
		<tr><td class="TableListTop">
			<table width="100%" cellspacing="0" cellpadding="0">
				<tr><td class="TableListBottom">
	<?php
}

function startList($order, $current_category_num, $orderChoice){
	#each of the differently ordered lists is in its own div whose display property is toggled by the user
	$display = "";
	if ($order != $orderChoice) $display = "none";
	print "<div id='".$order."list".$current_category_num."' style='display:".$display.";'>";
	print "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">";
}

function getAvailabilityAndStandards($DBID, $ShortName, $FullName, $URL, $Categories, $cn, $standard_container_left, $standard_container_right, $green, $rootURL, $imagesURL, $row_count, $this_order, $debugMode){

	#having found all the databases in this category, want to find its availability
	$sqlAvailability						= "SELECT Availability_ID FROM dbs_in_availability_types WHERE dbs_in_availability_types.DBID='$DBID'";
	$resultAvailability						= mysql_query($sqlAvailability, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlAvailability,$debugMode);}
	while ($rowResultAvailability			= mysql_fetch_row($resultAvailability)) {
		#the result is later used to select an appropriate icon to display
		$freelyAvailable=$rowResultAvailability[0];
	}

	#next we find the database's download types (standards) -> not all databases have this set
	$sqlStandards							= "SELECT download_ID FROM dbs_in_download_types WHERE dbs_in_download_types.DBID='$DBID'";
	$resultStandards						= mysql_query($sqlStandards, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlStandards,$debugMode);}
	$standard								= "";
	while ($rowResultStandards				= mysql_fetch_row($resultStandards)) {
		#because there can be several standards to each database, they are cummulative, here each result number corresponds to a particular standard text
		switch ($rowResultStandards[0]) {
		case 5:
			$standard						= $standard.$standard_container_left."BioPAX".$standard_container_right;
			break;
		case 4:
			$standard						= $standard.$standard_container_left."PSI-MI".$standard_container_right;
			break;
		case 7:
			$standard						= $standard.$standard_container_left."SBML".$standard_container_right;
			break;
		case 8:
			$standard						= $standard.$standard_container_left."CellML".$standard_container_right;
			break;
		}
	}
	
	$rowClass = $row_count%2 == 0 ? "Odd" : "Even";
	
	#now we can print out the row, starting with the javascript events for highlighting and the linked database name
	print "\n<tr id=\"ResourceRow_".$DBID."\" class=\"".$rowClass."\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td><a class=\"ListLink\" href='$URL' target=\"_blank\">$ShortName - $FullName</a></td>";
	if($Categories!=null) {
	print "\n<td width=\"166\">$Categories</td>";
	}
	#the next 3 columns are centered and have a determined size
	print "<td width=\"70\" align=\"center\"><a class=\"ListLink\" href=\"javascript:formSubmit('false','$rootURL"."fullrecord.php','$DBID')\"><b>Details</b></a></td><td width=\"50\" align=\"center\">";
	if($freelyAvailable==1) {
		print "<div title='Free to all users' style='cursor:default;'><font color=\"$green\"><b>Free</b></font></div>";
	}elseif($freelyAvailable==2){
		print "<img src='$imagesURL"."icon_academic.gif' title='Free to academic users' alt='Academic'>";
	}elseif($freelyAvailable==3){
		print "<img src='$imagesURL"."icon_purchase.gif' title='License purchase required' alt='License'>";
	}elseif($freelyAvailable==4){
		print "<img src='$imagesURL"."icon_unavailable.gif' title='Currently unavailable' alt='Unavailable'>";
	}else{
		print "<img src='$imagesURL"."icon_unknown.gif' title='Availability is unknown' alt='Unknown'>";
	}
	print "</td><td width=\"50\" align=\"center\">";
	if($standard) {print $standard;}
	print "</td></tr>";
	unset($freelyAvailable); unset($standard);
}

?>