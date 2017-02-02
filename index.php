<?php 
include("config.php");
unset($_SESSION['categoryIDsArray']);
unset($_SESSION['categoryNamesArray']);
#before we include top, we must set a couple variables to indicate that this is a pathguide page and not the admin page (compare to admin pages)
$websiteTitle								= $pathguideTitle;
$tabs										= "top_tabs.php";	
$logo										= "logo.gif";
$menu										= "top_menu.php";
include("top.php");
?>
<table cellspacing="0" cellpadding="0" border="0" width="100%">
	<tr><td valign="top">
<?php
if($organisms=="all" && $availability=="all" && $standards=="all"){
?>
		<p><font class=Title>Complete Listing of All Pathguide Resources</font></p>
		<p>Pathguide contains information about
		<?php
		$sql								= "SELECT COUNT(DBID) FROM names WHERE URL!=''";
		$resultDbCount						= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		$dbResultDbCount					= mysql_fetch_row($resultDbCount);
		print " <b>$dbResultDbCount[0]</b> ";
		$sql								= "SELECT content FROM extras WHERE component='welcome'";
		$resultWelcome						= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		$dbResultWelcome					= mysql_fetch_row($resultWelcome);
		print "$dbResultWelcome[0]";
		?>
		</p>
<?php
}else{
	if($organisms!="all"){
		$sql								= "SELECT scientific_name, common_name, taxonomy_ID FROM organism_types WHERE organism_ID='$organisms'";
		$result								= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		$row								= mysql_fetch_row($result);
		$organisms_description				= "<a href=\"$pubmed_link_taxonomy"."$row[2]\">".$row[0];
		$organisms_common					= " (".$row[1].")</a>";
	}else{
		$organisms_description				= "all";
		$organisms_common					= "";
	}
	if($availability!="all"){
		$sql								= "SELECT description FROM availability_types WHERE availability_ID='$availability'";
		$result								= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		$row								= mysql_fetch_row($result);
		$availability_description	 		= $row[0];
	}else{
		$availability_description			= "all";
	}
	if($standards!="all"){
		$sql								= "SELECT description FROM download_types WHERE download_ID='$standards'";
		$result								= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		$row								= mysql_fetch_row($result);
		$standards_description				= $row[0];
	}else{
		$standards_description				= "all";
	}
?>
		<p><font class="Title">Pathguide Resource Search</font></p>
		<p>Your search returned <span id="num_of_results">...</span><span id="num_of_categories"></span> with the following search parameters:
		<ul>
			<li>Organisms: <?php print $organisms_description; print $organisms_common; ?></li>
			<li>Availability: <?php print $availability_description; ?></li>
			<li>Standards: <?php print $standards_description; ?></li>
		</ul>

		<span id="no_results_help" class="Note"></span>

<?php
}
		#in order to tell the user how many results their search produced, we initialize the variable at 0 and create the opening statement whose inner html will be modified using javascript once all the results are counted (at the bottom of this document)
		$num_of_results = 0;
?>
	</td>

		<?php
		$sqlNews							= "SELECT content FROM extras WHERE component='news'";
		$resultNews							= mysql_query($sqlNews, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlNews,$debugMode);}
		while ($rowResultNews				= mysql_fetch_row($resultNews)) {
			if ($rowResultNews[0]!="" && !is_null($rowResultNews[0])){
				print "<td valign=\"top\" style=\"padding:0 0 0 20;\">";
				include("news.php");
				print "</td>";
			}
		}
		?>

	</tr>
</table>
<br><hr><br>

<?php
include('functions_for_categories.php');

$sqlAlphabetic								= "SELECT db_categories.CategoryID, CategoryDescription, names.DBID, ShortName, FullName, URL, dbs_in_categories.CategoryID IS NULL AS CategoryOther "
											. "FROM (names LEFT JOIN dbs_in_categories USING (DBID)) "
											. "LEFT JOIN db_categories USING (CategoryID)".$from_search_criteria
											. "WHERE URL!=''".$where_search_criteria
											. "ORDER BY CategoryOther, CategoryID, ShortName;";

$resultAlphabetic							= mysql_query($sqlAlphabetic,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlAlphabetic,$debugMode);}

$sqlPopularity								= "SELECT dbs_in_categories.CategoryID, names.DBID, ShortName, FullName, URL, Score, dbs_in_categories.CategoryID IS NULL AS CategoryOther "
											. "FROM (names LEFT JOIN dbs_in_categories USING (DBID)) "
											. "LEFT JOIN scores USING (DBID)".$from_search_criteria
											. "WHERE URL!=''".$where_search_criteria
											. "ORDER BY CategoryOther, CategoryID, Score DESC, ShortName;";

$resultPopularity							= mysql_query($sqlPopularity,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlPopularity,$debugMode);}

$currentCatID								= 0;
$current_category_num						= 0;
$row_count                                  = 0;

$categoryStarted							= false;
$listStarted								= false;

while ($rowAlphabetic						= mysql_fetch_row($resultAlphabetic)) {
	if ($rowAlphabetic[6] == 0) {
		$CatID								= $rowAlphabetic[0];
		$CatName							= $rowAlphabetic[1];
	} else {
		$CatID								= 20;
		$CatName							= "Other";
	}
	
	if ($CatID != $currentCatID) {
		$currentCatID = $CatID;
	
		# here the variable increases by 1, the first category lists are alphabeticlist1 and popularitylist1, respective links (span) follow the same pattern
		$current_category_num			   += 1;
		
		if ($listStarted) {
			#here the list table is finished and a new popularity list is started
			print "\n</table></div>";
			$row_count                      = 0;
			startList("popularity", $current_category_num - 1, $order);
			for ($c=0;$c<$num_of_cat_results;$c++) {
				$rowPopularity				= mysql_fetch_row($resultPopularity);				
				$DBID						= $rowPopularity[1];
				$ShortName					= $rowPopularity[2];
				$FullName					= $rowPopularity[3];
				$URL						= $rowPopularity[4];
				getAvailabilityAndStandards($DBID, $ShortName, $FullName, $URL, null, $cn, $standard_container_left, $standard_container_right, $green, $rootURL, $imagesURL, $row_count, "popularity", $debugMode);
				$row_count += 1;
			}
			print "\n</table></div>";
			$listStarted					= false;
		}
		
		if ($categoryStarted) {
			print "</td></tr></table></td></tr><tr><td style=\"height:6;\"></td></tr></table><table class=\"HorizontalDivider\" width=\"100%\"><tr><td width=\"100%\" align=\"right\"><a href=\"#top\">Back to the Top</a></td></tr></table><br>";
		}
	
		printCategoryTitle($CatID, $CatName, $current_category_num, $rootURL, $imagesURL, $order);
		startList("alphabetic", $current_category_num, $order);
		$categoryStarted					= true;
		$listStarted						= true;

		#row color is initialized as gray so that the first row will be white due to the color switching mechanism
		$num_of_cat_results					= 0;
	}
	
	$DBID									= $rowAlphabetic[2];
	$ShortName								= $rowAlphabetic[3];
	$FullName								= $rowAlphabetic[4];
	$URL									= $rowAlphabetic[5];

	getAvailabilityAndStandards($DBID, $ShortName, $FullName, $URL, null, $cn, $standard_container_left, $standard_container_right, $green, $rootURL, $imagesURL, $row_count, "alphabetic", $debugMode);
	#each row is counted as a result for quantifying the search (this means that databases that appear in several categories will count as more than 1 result)
	$num_of_results+=1;
	$num_of_cat_results +=1;
	$row_count += 1;
}

if ($listStarted) {
	#here the list table is finished and a new popularity list is started
	print "\n</table></div>";
	#row color is initialized as gray so that the first row will be white due to the color switching mechanism
	$row_count                          = 0;
	startList("popularity", $current_category_num, $order);
	for ($c=0;$c<$num_of_cat_results;$c++) {
		$rowPopularity						= mysql_fetch_row($resultPopularity);				
		$DBID								= $rowPopularity[1];
		$ShortName							= $rowPopularity[2];
		$FullName							= $rowPopularity[3];
		$URL								= $rowPopularity[4];
		getAvailabilityAndStandards($DBID, $ShortName, $FullName, $URL, null, $cn, $standard_container_left, $standard_container_right, $green, $rootURL, $imagesURL, $row_count, "popularity", $debugMode);
		$row_count += 1;
	}
	print "\n</table></div>";
}
if ($categoryStarted) {
	print "</td></tr></table></td></tr><tr><td style=\"height:6;\"></td></tr></table><table class=\"HorizontalDivider\" width=\"100%\"><tr><td width=\"100%\" align=\"right\"><a href=\"#top\">Back to the Top</a></td></tr></table><br>";
}
?>
<a name="PopularityNote"></a>
<table>
	<tr>
		<td valign="top"><img src="<?php print $imagesURL; ?>icon_note_large.gif" alt=""></td>
		<td class="Note">
			<?php
			$sqlPopNote						= "SELECT content FROM extras WHERE component='popnote'";
			$resultPopNote					= mysql_query($sqlPopNote, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlPopNote,$debugMode);}
			while ($rowResultPopNote		= mysql_fetch_row($resultPopNote)) {
				print $rowResultPopNote[0];
			}
			?>
		</td>
	</tr>
</table>

<script type="text/javascript">

<?php
#now that all the results have been counted, the opening statement can be modified with the number and, depending on the number, the word result or results
?>
if(document.getElementById("num_of_results")){
	var num_of_results													= <?php print $num_of_results; ?>;
	if(num_of_results!=1){
		document.getElementById("num_of_results").innerHTML				= "<b>"+num_of_results+"</b> results";
	}else{
		document.getElementById("num_of_results").innerHTML				= "<b>1</b> result";
	}
	var num_of_categories												= <?php print $current_category_num; ?>;
	if(num_of_categories!=1){
		document.getElementById("num_of_categories").innerHTML			= "&nbsp;in <b>"+num_of_categories+"</b> categories";
	}else{
		document.getElementById("num_of_categories").innerHTML			= "&nbsp;in <b>1</b> category";
	}
	if(num_of_results==0){
		document.getElementById("no_results_help").innerHTML			= "Note: Consider expanding your search or modifying your perameters to find alternate resources.";
	}
}

<?php
#finally we want to show the alphabetic list and hide the popularity list since both are visible initially, the function can also be trigered by the user

#DYNAMIC ORDER TOGGLE
?>
function order(type,nottype){
	var typelink														= type+'link';
	var typelist														= type+'list';
	var nottypelink														= nottype+'link';
	var nottypelist														= nottype+'list';
	for (var i=1;i<=<?php print $current_category_num; ?>;i++){
		var current_typelink											= typelink+i;
		var current_typelist											= typelist+i;
		var current_nottypelink											= nottypelink+i;
		var current_nottypelist											= nottypelist+i;
		document.getElementById(current_typelist).style.display			= '';
		document.getElementById(current_nottypelist).style.display		= 'none';
		document.getElementById(current_typelink).style.display			= 'none';
		document.getElementById(current_nottypelink).style.display		= '';
	}

	document.getElementById("order").value								= type;
}
</script>

<?php
$search_helper = true;
include("bottom.php");
?>