<?php
include("config_admin.php");
$websiteTitle									= $adminTitle."|Resource Management";
$tabs											= "admin/top_tabs_admin.php";
$logo											= "logo_admin.gif";
$menu											= "admin/top_menu_admin_resource.php";
include("../top.php");

#if the user has selected a resource to edit, we must get that DBID from the $_GET and print "SELECTED" in the approrpiate option
if(isset($_GET["resource"])){
	$choice										= $_GET["resource"];
}elseif(isset($_POST["resource"]) && $_POST["PressedButton"]=="Save"){
	#if, however, the user has edited a resource and pressed save than we must find out which resource it is from the $_POST
	$choice										= $_POST["resource"];
}else{
	#if delete was pressed or no resource is being edited or viewed then...
	$choice										= "0";
}

/***************************
SAVING TO THE DATABASE            
***************************/

#PROCESSING THE POSTED INPUTS
#if the user has edited a resource and pressed save or delete, then the variables are stored in $_POST and must be retrieved and stored in the database
#first must set some variables that would cause undefined errors otherwise
$PressedButton										= "";
$DBID												= "";

if(isset($_POST["DBID"])){
	$PressedButton									= $_POST["PressedButton"];
	$DBID											= $_POST["DBID"];

	if($PressedButton=="Save"){
		$ShortName									= strFix($_POST["ShortName"]);
		$FullName									= strFix($_POST["FullName"]);
		$URL										= strFix($_POST["URL"]);
		$AvailabilityChoice							= strFix($_POST["AvailabilityMenu"]);
		$SampleDataURL								= strFix($_POST["SampleDataURL"]);
		$Description								= strFix($_POST["Description"]);
		
		# Since there is no limit on the number of pubmed entries, we must make arrays depending on the posted hidden input
		# Because the user can create/delete any entry at any time, the naming of the entries will have gaps in the numbering so we run a loop to the highest number but only store those that actually exist
		$PubMedEntries								= $_POST["PubMedEntries"];
		$HighestEntry								= $_POST["HighestEntry"];
		$PubMedID									= array($PubMedEntries);
		$PubMedTitle								= array($PubMedEntries);
		$PubMedAuthors								= array($PubMedEntries);
		$PubMedCitation								= array($PubMedEntries);
		# Because our storage arrays should not have gaps, they have their own counter that only counts existing entries
		$i											= 0;
		for($j=1; $j<=$HighestEntry; $j++){
			if(isset($_POST["PubMedID$j"])){
				$PubMedID[$i]						= strFix($_POST["PubMedID$j"]);
				$PubMedTitle[$i]					= strFix($_POST["PubMedTitle$j"]);
				$PubMedAuthors[$i]					= strFix($_POST["PubMedAuthors$j"]);
				$PubMedCitation[$i]					= strFix($_POST["PubMedCitation$j"]);
				$i++; #storage occured, so we can move up to the next element in the arrays
			}
		}
	
		$Contents_SMS								= $_POST["Contents_SMS"];
		$Contents_GPS								= $_POST["Contents_GPS"];
		$Contents_IRS								= $_POST["Contents_IRS"];
		$Contents_PWS								= $_POST["Contents_PWS"];
		$Contents_XPS								= $_POST["Contents_XPS"];
		$Contents_LastUpdate						= $_POST["Contents_LastUpdate"];
	
		if (isset($_POST["OrganismMenu"])) $OrganismChoices = $_POST["OrganismMenu"];
	
		$DataSource									= $_POST["DataSource"];
		$HostTrackerID								= $_POST["HostTrackerID"];

		# Translate menu choices
	
		# Translate $OrganismChoices to organism IDs
		$Organisms									= array();
		if(isset($OrganismChoices)){
			foreach($OrganismChoices as $OC){
				$sql								= "SELECT organism_ID FROM organism_types WHERE scientific_name='$OC'";
				$result								= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
				$row								= mysql_fetch_row($result);
				$Organisms[$row[0]]					= 1;
			}
		}
	
		# Create function to translate choices from a checkbox group into an array
		function get_checkboxes($query, $cn, $_POST, $field){
			$TempArray								= array();
			$r										= mysql_query($query, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$query,$debugMode);}
			while($dbn								= mysql_fetch_row($r)){
				$Num								= $dbn[0];
				$Fld								= "$field" . "_" . "$Num";
				if(isset($_POST["$Fld"]) && $_POST["$Fld"] == "on"){
					$TempArray[$Num]				= 1;
				}
			}
			return $TempArray;
		}
	
		# Find out which data categories, tools, and downloads were checked
		$Categories									= get_checkboxes("SELECT * from db_categories", $cn, $_POST, "category");
		$Tools										= get_checkboxes("SELECT * from tool_types", $cn, $_POST, "toolID");
		$Downloads									= get_checkboxes("SELECT * from download_types", $cn, $_POST, "downloadID");
	
		###GENERATING AND RUNNING THE QUERIES
		# Create names SQL query (depending on if it's a new entry or an existing one
		if($choice == "0"){
			$sql									= "INSERT INTO names (DBID, ShortName, FullName, URL, DataSource, Description, SampleDataURL, HostTrackerID) ";
			$sql								   .= "VALUES (\"$DBID\", \"$ShortName\", \"$FullName\", \"$URL\", \"$DataSource\", \"$Description\", \"$SampleDataURL\", \"$HostTrackerID\")";
		}else{
			$sql  									= "UPDATE names SET ShortName=\"$ShortName\",";
			$sql								   .= " FullName = \"$FullName\",";
			$sql								   .= " URL= \"$URL\",";
			$sql								   .= " DataSource= \"$DataSource\",";
			$sql								   .= " Description= \"$Description\",";
			$sql								   .= " SampleDataURL= \"$SampleDataURL\",";
			$sql								   .= " HostTrackerID= \"$HostTrackerID\" WHERE DBID=\"$DBID\" LIMIT 1";
		}
				
		# Run names SQL query
		$result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
	
		# Create dbs_in_availability_types SQL query (depending on if its a new entry or an existing one
		$sql										= "SELECT * FROM dbs_in_availability_types WHERE DBID='$DBID'";
		$r1											= mysql_query($sql, $cn);
		$result										= mysql_num_rows($r1);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		if ($result > 1) {print "ERROR! Multiple tuples found for $DBID in dbs_in_availability_types!";}
		if ($result == 0) {$newEntry				= 1;}
		if ($result == 1) {$newEntry				= 0;}
		if ($newEntry) {
			$sql									= "INSERT INTO dbs_in_availability_types (DBID, Availability_ID) ";
			$sql								   .= "VALUES (\"$DBID\", \"$AvailabilityChoice\")";
		}else{
			$sql									= "UPDATE dbs_in_availability_types SET Availability_ID=\"$AvailabilityChoice\" WHERE DBID= \"$DBID\" LIMIT 1";
		}
				
		# Run dbs_in_availability types SQL query
		$result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
	
		# Create and run PMIDs query
		# Delete old PMIDs since the number of id's can vary
		$sql										= "DELETE FROM pmids WHERE DBID='$DBID'";
		$result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
	
		# Write new PMIDs
		if ($PubMedID) {
			for($a=0;$a<$PubMedEntries;$a++){
				$sql								= "INSERT INTO pmids (DBID, PMID, title, authors, citation) ";
				$sql							   .= "VALUES (\"$DBID\", \"$PubMedID[$a]\", \"$PubMedTitle[$a]\", \"$PubMedAuthors[$a]\", \"$PubMedCitation[$a]\")";
				$result								= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
			}
		}
		# Create and run db_categories, dbs_in_tool_types, dbs_in_download_types, and dbs_in_organism_types SQL queries
		# First, drop old
		$sql										= "DELETE FROM dbs_in_categories WHERE DBID='$DBID';";
		$sql2										= "DELETE FROM dbs_in_tool_types WHERE DBID='$DBID';";
		$sql3										= "DELETE FROM dbs_in_organism_types WHERE DBID='$DBID';";
		$sql4										= "DELETE FROM dbs_in_download_types WHERE DBID='$DBID';";
	
		$result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		$result2									= mysql_query($sql2,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql2,$debugMode);}
		$result3									= mysql_query($sql3,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql3,$debugMode);}
		$result4									= mysql_query($sql4,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql4,$debugMode);}
	
		# Next, add new
		$sql										= "SELECT * FROM db_categories";
		$result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		while ($dbname								= mysql_fetch_row($result)) {
			$CatNum									= $dbname[0];
			if (isset($Categories) && isset($Categories[$CatNum]) && $Categories[$CatNum] == 1) {
				$sql								= "INSERT INTO dbs_in_categories (DBID, CategoryID) ";
				$sql							   .= "VALUES (\"$DBID\", \"$CatNum\")";
				$result2							= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
			}
		}
	
		$sql										= "SELECT * FROM tool_types";
		$result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		while ($dbname								= mysql_fetch_row($result)) {
			$ToolNum								= $dbname[0];
			if (isset($Tools) && isset($Tools[$ToolNum]) && $Tools[$ToolNum] == 1) {
				$sql								= "INSERT INTO dbs_in_tool_types (DBID, tool_ID) ";
				$sql							   .= "VALUES (\"$DBID\", \"$ToolNum\")";
				$result2							= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
			}
		}
	
		$sql										= "SELECT * FROM organism_types";
		$result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		while ($dbname								= mysql_fetch_row($result)) {
			$OrgNum									= $dbname[0];
			if (isset($Organisms) && isset($Organisms[$OrgNum]) && $Organisms[$OrgNum] == 1) {
				$sql								= "INSERT INTO dbs_in_organism_types (DBID, organism_ID) ";
				$sql							   .= "VALUES (\"$DBID\", \"$OrgNum\")";
				$result2							= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
			}
		}
	
		$sql										= "SELECT * FROM download_types";
		$result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		while ($dbname								= mysql_fetch_row($result)) {
			$DlNum									= $dbname[0];
			if (isset($Downloads) && isset($Downloads[$DlNum]) && $Downloads[$DlNum] == 1) {
				$sql								= "INSERT INTO dbs_in_download_types (DBID, download_ID) ";
				$sql							   .= "VALUES (\"$DBID\", \"$DlNum\")";
				$result2							= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
			}
		}
	
	
		# Create contents SQL query (depending on if its a new entry or an existing one
		# Fix date first
		if ($Contents_LastUpdate == "") {$Contents_LastUpdate = "NULL";}
		$sql										= "SELECT * FROM contents WHERE DBID='$DBID'";
		$r1											= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		$result										= mysql_num_rows($r1);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		if ($result > 1) {print "ERROR! Multiple tuples found for $DBID in contents!";}
		if ($result == 0) {$newEntry				= 1;}
		if ($result == 1) {$newEntry				= 0;}
		if ($newEntry) {
			$sql									= "INSERT INTO contents (DBID, SMS, GPS, IRS, PWS, XPS, LastUpdate) ";
			$sql								   .= "VALUES (\"$DBID\", \"$Contents_SMS\", \"$Contents_GPS\", \"$Contents_IRS\", \"$Contents_PWS\", \"$Contents_XPS\", \"$Contents_LastUpdate\")";
		}else{
			$sql									= "UPDATE contents SET SMS='$Contents_SMS',";
			$sql								   .= " GPS = '$Contents_GPS',";
			$sql								   .= " IRS = '$Contents_IRS',";
			$sql								   .= " PWS = '$Contents_PWS',";
			$sql								   .= " XPS = '$Contents_XPS',";
			$sql								   .= " LastUpdate = \"$Contents_LastUpdate\" WHERE DBID='$DBID' LIMIT 1";
		}
		# Run contents SQL query
		$result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
	}else{
		# If a database is not being saved then it is being deleted
		$sql1										= "DELETE FROM contents WHERE DBID='$DBID';";
		$sql2										= "DELETE FROM dbs_in_availability_types WHERE DBID='$DBID';";
		$sql3										= "DELETE FROM dbs_in_categories WHERE DBID='$DBID';";
		$sql4										= "DELETE FROM dbs_in_download_types WHERE DBID='$DBID';";
		$sql5										= "DELETE FROM dbs_in_organism_types WHERE DBID='$DBID';";
		$sql6										= "DELETE FROM dbs_in_tool_types WHERE DBID='$DBID';";
		$sql7										= "DELETE FROM names WHERE DBID='$DBID';";
		$sql8										= "DELETE FROM pmids WHERE DBID='$DBID';";
		$sql9										= "DELETE FROM scores WHERE DBID='$DBID';";
		$sql10										= "DELETE FROM scores_archives WHERE DBID='$DBID';";
		$result										= mysql_query($sql1, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql1,$debugMode);}
		$result										= mysql_query($sql2, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql2,$debugMode);}
		$result										= mysql_query($sql3, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql3,$debugMode);}
		$result										= mysql_query($sql4, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql4,$debugMode);}
		$result										= mysql_query($sql5, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql5,$debugMode);}
		$result										= mysql_query($sql6, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql6,$debugMode);}
		$result										= mysql_query($sql7, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql7,$debugMode);}
		$result										= mysql_query($sql8, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql8,$debugMode);}
		$result										= mysql_query($sql9, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql9,$debugMode);}
		$result										= mysql_query($sql10, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql10,$debugMode);}
	}
}#ends if(isset($_POST))
?>

<script type="text/javascript">
if("<?php print $PressedButton; ?>"=="Save"){
	window.location								= '<?php print $adminURL; ?>resource_preview.php?resource=<?php print $DBID; ?>';
}
</script>

<font class=Title>Resource Management</font><br><br>
<form method="GET" action="resource_mgmt.php" id="resourceChooser">

<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td width="<?php print $row_header_width; ?>"><b>Select a resource:</b></td>
		<td align="left">
			<select name="resource" onChange="document.getElementById('resourceChooser').submit();">
				<option value="0">Create a new entry</option>
				<?php
				$sql							= "SELECT ShortName, DBID FROM names ORDER BY ShortName;";
				$result							= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
				while ($dbname					= mysql_fetch_row($result)) {
					print "<option value=\"$dbname[1]\"";
					if ($dbname[1] == $choice) {
						print " SELECTED";
					}
					print ">$dbname[0]</option> ";
				}
				?>
			</select>
		</td>
	</tr>
</table>
<br>
<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>
</form>

<form method="POST" action="resource_mgmt.php" id="resourceManager">
<?php

function timestamp2itdate($timestamp){
   $Months										= array("Nul","Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
   $MonthsIndex									= substr($timestamp, 5, 2);
   $MonthsIndex									= $MonthsIndex + 1 - 1;
   return( substr($timestamp, 8, 2) . '  ' . $Months[$MonthsIndex] . ', ' . substr($timestamp, 0, 4) ); 
}

$today											= date("M")." ".date("d").", ".date("Y");

### Get initial values of $choice-dependent fields:
### $DBID, $ShortName, $FullName, $URL, $Availability, $categories, 
print "<input type='hidden' name='resource' value=\"$choice\" />";
### $choice = Create new entry
if ($choice == "0" || $choice == "") {
	$sql										= "SELECT MAX(DBID) FROM names;";
    $result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
    $row										= mysql_fetch_row($result);
    
    $DBID										= $row[0] + 1;
    $LastMod									= "n/a";
    $ShortName									= "";
    $FullName									= "";
    $URL										= "";
    $Availability								= "Unknown";
    $DataSource									= "Unknown";

	$PubMedEntries								= 0;

    $SampleDataURL								= "";
    $Description								= "";
	$HostTrackerID								= "";

}else {
	$sql										= "SELECT * FROM names where names.DBID='$choice';";
    $result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
    $row										= mysql_fetch_row($result);
    $DBID										= $row[0];
    if ($row[1]){$ShortName						= $row[1];} else {$ShortName		= "";}
    if ($row[2]){$FullName						= $row[2];} else {$FullName			= "";}
    if ($row[3]){$URL							= $row[3];} else {$URL				= "";}
    if ($row[4]){$TimeStamp						= $row[4];} else {$TimeStamp		= "";}
    if ($row[5]){$DataSource					= $row[5];} else {$DataSource		= "Unknown";}
    if ($row[6]){$Description					= $row[6];} else {$Description		= "";}
    if ($row[7]){$SampleDataURL					= $row[7];} else {$SampleDataURL	= "";}
	if ($row[8]){$HostTrackerID					= $row[8];} else {$HostTrackerID	= "";}

    $LastMod = timestamp2itdate($TimeStamp);
    # Find availability type of this resource
    $sql										= "SELECT description FROM availability_types, dbs_in_availability_types WHERE dbs_in_availability_types.Availability_ID=availability_types.Availability_ID AND dbs_in_availability_types.DBID='$DBID';";
    $result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
    $A1											= mysql_fetch_row($result);
    if (!$A1) {$Availability					= "Unknown";}
    else {$Availability							= $A1[0];}
    ###
    
    # Get PMIDs
    $sql										= "SELECT PMID, title, authors, citation FROM pmids WHERE DBID='$DBID';";
    $result										= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
    $PubMedEntries								= mysql_num_rows ($result);
	$PubMedID									= array($PubMedEntries);
	$PubMedTitle								= array($PubMedEntries);
	$PubMedAuthors								= array($PubMedEntries);
	$PubMedCitation								= array($PubMedEntries);
    $i											= 0;
    while ($MyPMID								= mysql_fetch_row($result)) {
        $PubMedID[$i]							= $MyPMID[0];
		$PubMedTitle[$i]						= $MyPMID[1];
		$PubMedAuthors[$i]						= $MyPMID[2];
		$PubMedCitation[$i]						= $MyPMID[3];

        $i++;
    }
    
    # Find the categories this resource is in
    $Categories									= array();
    $sql										= "SELECT CategoryID FROM names, dbs_in_categories WHERE names.DBID=dbs_in_categories.DBID AND names.DBID='$DBID';";
    $result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
    while ($cat									= mysql_fetch_row($result)) {
		$Categories[$cat[0]]					= 1;
	}
        
    ###
    # Find the tool types this resource has
    $Tools										= array();
    $sql										= "SELECT tool_ID FROM names, dbs_in_tool_types WHERE names.DBID=dbs_in_tool_types.DBID AND names.DBID='$DBID';";
    $result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
    while ($tool								= mysql_fetch_row($result)) {
		$Tools[$tool[0]]						= 1;
	}
            
    ###
    # Find the download types this resource has
    $Downloads									= array();
    $sql										= "SELECT download_ID FROM names, dbs_in_download_types WHERE names.DBID=dbs_in_download_types.DBID AND names.DBID='$DBID';";
    $result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
    while ($download							= mysql_fetch_row($result)) {
		$Downloads[$download[0]]				= 1;
	}
            
    ###
    # Find contents of this resource
    $sql										= "SELECT * FROM contents WHERE contents.DBID='$DBID';";
    $result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
    $row										= mysql_fetch_row($result);
    if ($row[1]){$Contents_SMS					= $row[1];} else {$Contents_SMS			= "";}
    if ($row[2]){$Contents_GPS					= $row[2];} else {$Contents_GPS			= "";}
    if ($row[3]){$Contents_IRS					= $row[3];} else {$Contents_IRS			= "";}
    if ($row[4]){$Contents_PWS					= $row[4];} else {$Contents_PWS			= "";}
    if ($row[5]){$Contents_XPS					= $row[5];} else {$Contents_XPS			= "";}
    if ($row[6] && $row[6] != "0000-00-00"){
				 $Contents_LastUpdate			= $row[6];} else {$Contents_LastUpdate	= "";}
   
    ###
    # Find organismal coverage of this resource
    $myOrganisms								= array();
    $sql										= "SELECT organism_ID FROM dbs_in_organism_types WHERE DBID='$DBID';";
    $result										= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
    while ($orgID								= mysql_fetch_row($result)) {
        $myOrganisms[$orgID[0]]					= 1;
    }
    
    ### END 'Gather field values for existing entry'
}
print "<input type='hidden' name='DBID' value=\"$DBID\" />";
### Section 1: Basic Info
?>
<a name="basicinfo"></a>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr><td>Resource ID		</td><td align="left"><div style="text-align:left;"><?php print $DBID; ?></div>																						</td></tr>
	<tr><td>Last Modified	</td><td align="left"><div style="text-align:left;"><?php print $LastMod; ?></div>																						</td></tr>
	<tr><td>Short Name		</td><td align="left"><input type="text"			name="ShortName"		value="<?php print str_replace('"', '&#34;', $ShortName); ?>"		style="width:<?php print $input_width; ?>" id="ShortName">	</td></tr>
	<tr><td>Full Name		</td><td align="left"><input type="text"			name="FullName"			value="<?php print str_replace('"', '&#34;', $FullName); ?>"		style="width:<?php print $input_width; ?>">					</td></tr>
	<tr><td>URL				</td><td align="left"><input type="text"			name="URL"				value="<?php print str_replace('"', '&#34;', $URL); ?>"				style="width:<?php print $input_width; ?>">					</td></tr>
	<tr><td>Availability	</td><td align="left"><select						name="AvailabilityMenu">
		<?php
		$sql									= "SELECT * FROM availability_types ORDER BY Availability_ID;";
		$result									= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
		while ($dbname							= mysql_fetch_row($result)) {
			print "<option value='$dbname[0]'";
			if ($Availability == $dbname[1]) {print " SELECTED";}
			print ">$dbname[1]</option>";
		}
		?>
	</td></tr>

	<tr><td width="<?php print $row_header_width; ?>">Sample Data URL	</td><td align="left"><input type="text"			name="SampleDataURL"	value="<?php print str_replace('"', '&#34;', $SampleDataURL); ?>"	style="width:<?php print $input_width; ?>"></td></tr>
	<tr><td width="<?php print $row_header_width; ?>" valign="top">Description</td><td align="left"><textarea rows="10" cols="10" name="Description" value="" style="width:<?php print $input_width; ?>"><?php print $Description; ?></textarea></td></tr>
</table>
<br>
<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>
<a name="pubmed"></a>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr><td width="<?php print $row_header_width; ?>" valign="top">PubMed Publications</td>
		<td align="left">
			<input type="hidden" value="<?php print $PubMedEntries; ?>" name="PubMedEntries"	id="PubMedEntries">
			<input type="hidden" value="<?php print $PubMedEntries; ?>" name="HighestEntry"		id="HighestEntry">
			<table cellspacing="0" cellpadding="0" width="<?php print $input_width; ?>" class="PubMedTable" id="PubMedTable">
				<tr bgcolor="<?php print $gray_lite; ?>">
					<td width="65">		<font class="Note">PubMed ID	</font>	<input type="text"		value=""	style="width:100%"		id="newID">				</td>
					<td>				<font class="Note">Title		</font>	<input type="text"		value=""	style="width:100%"		id="newTitle">			</td>
					<td width="100">	<font class="Note">Authors		</font>	<input type="text"		value=""	style="width:100%"		id="newAuthors">		</td>
					<td width="70">		<font class="Note">Citation		</font>	<input type="text"		value=""	style="width:100%"		id="newCitation">		</td>
					<td width="24" valign="bottom">								<input type="button"	value="+"	style="width:100%"		onClick="addPubMed()">	</td>
				</tr>

				<?php
				#add rows if there are PubMed entries in the database
				for($i=0; $i<$PubMedEntries; $i++){
					$j=$i+1;
					print "<tr>";
					print "<td><input type=text		style='width:100%'	value='$PubMedID[$i]'											name='PubMedID$j'					id='pmID$j'>		</td>";
					print "<td><input type=text		style='width:100%'	value=\"".str_replace('"', '&#34;', $PubMedTitle[$i])."\"		name='PubMedTitle$j'				id='pmTitle$j'>		</td>";
					print "<td><input type=text		style='width:100%'	value=\"".str_replace('"', '&#34;', $PubMedAuthors[$i])."\"		name='PubMedAuthors$j'				id='pmAuthors$j'>	</td>";
					print "<td><input type=text		style='width:100%'	value=\"".str_replace('"', '&#34;', $PubMedCitation[$i])."\"	name='PubMedCitation$j'				id='pmCitation$j'>	</td>";
					print "<td><input type=button	style='width:100%'	value='-'														onClick=\"deletePubMed('$j', this)\">					</td>";
					print "</tr>";
				}
				?>

			</table>
		</td>
	</tr>
</table>
<br>
<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>
<a name="datatypes"></a>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" width="<?php print $row_header_width; ?>">Types of Data Available from Resource</td>
		<td align="left">
			<div style="width:<?php print $input_width; ?>; text-align:left;">
			<?php
			$sql								= "SELECT * FROM db_categories;";
			$result								= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
			while ($dbname						= mysql_fetch_row($result)) {
				echo '<input type="checkbox" name="category_';
				$CatNum							= $dbname[0];
				print("$dbname[0]");
				echo '"';
				if (isset($Categories) && isset($Categories[$CatNum]) && $Categories[$CatNum] == 1) {echo' checked';}
				echo '>';
				print("$dbname[1]");
				print '<br>';
			}
			?>
			</div>
		</td>
	</tr>
</table>
<br>
<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>
<a name="tools"></a>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" width="<?php print $row_header_width; ?>">Types of Tools Available from Resource</td>
		<td align="left">
			<div style="width:<?php print $input_width; ?>; text-align:left;">
			<?php
			$sql								= "SELECT * FROM tool_types;";
			$result								= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
			while ($dbname						= mysql_fetch_row($result)) {

				echo '<input type="checkbox" name="toolID_';
				$ToolNum						= $dbname[0];
				print("$dbname[0]");
				echo '"';
				if (isset($Tools) && isset($Tools[$ToolNum]) && $Tools[$ToolNum] == 1) {echo' checked';}
				echo '>';
				print("$dbname[1]");
				print '<br>';
			}
			?>
			</div>
		</td>
	</tr>
</table>
<br>
<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>
<a name="access"></a>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" width="<?php print $row_header_width; ?>">Data Access Methods</td>
		<td align="left">
			<div style="width:<?php print $input_width; ?>; text-align:left;">
			<?php
			$sql								= "SELECT * FROM download_types;";
			$result								= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
			while ($dbname						= mysql_fetch_row($result)) {
				echo '<input type="checkbox" name="downloadID_';
				$DownloadNum					= $dbname[0];
				print("$dbname[0]");
				echo '"';
				if (isset($Downloads) && isset($Downloads[$DownloadNum]) && $Downloads[$DownloadNum] == 1) {echo' checked';}
				echo '>';
				print("$dbname[1]");
				print '<br>';
			}
			?>
			</div>
		</td>
	</tr>
</table>
<br>
<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>
<a name="organisms"></a>
<table cellpadding="2"cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" width="<?php print $row_header_width; ?>">Major Organisms<br><font class="Note">(>100 interactions)</font></td>
		<td align="left">
			<?php
			print "<select name=\"OrganismMenu[]\" MULTIPLE size=\"7\">";
			$sql								= "SELECT * FROM organism_types ORDER BY scientific_name;";
			$result								= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
			while ($dbname						= mysql_fetch_row($result)) {
				print "<option value=\"$dbname[1]\"";
				if (isset($myOrganisms) && isset($myOrganisms[$dbname[0]]) && $myOrganisms[$dbname[0]] == 1) {print ' SELECTED';}
				print '>';
				print("$dbname[1] ($dbname[2])");
				echo '</option> ';
			}
			print '</select>';
			?>
		</td>
	</tr>
</table>
<br>
<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>
<a name="contents"></a>
<table cellpadding="2"cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" width="<?php print $row_header_width; ?>">Contents</td>
		<td align="left">
			<table cellspacing="0" cellpadding="0" width="<?php print $input_width; ?>" class="PubMedTable">
				<tr bgcolor="<?php print $gray_lite; ?>">
					<td width="20%">	<font class="Note">Small Molecules				</font></td>
					<td width="20%">	<font class="Note">Genes / Proteins				</font></td>
					<td>				<font class="Note">Interactions / Reactions		</font></td>
					<td width="20%">	<font class="Note">Pathways						</font></td>
					<td width="20%">	<font class="Note">Experiments / PMIDs			</font></td>
				</tr>
				<tr>
					<td><input type="text" name="Contents_SMS"	style="width:100%;"	value="<?php if (isset($Contents_SMS)) {print $Contents_SMS;} ?>"></td>
					<td><input type="text" name="Contents_GPS"	style="width:100%;"	value="<?php if (isset($Contents_GPS)) {print $Contents_GPS;} ?>"></td>
					<td><input type="text" name="Contents_IRS"	style="width:100%;"	value="<?php if (isset($Contents_IRS)) {print $Contents_IRS;} ?>"></td>
					<td><input type="text" name="Contents_PWS"	style="width:100%;"	value="<?php if (isset($Contents_PWS)) {print $Contents_PWS;} ?>"></td>
					<td><input type="text" name="Contents_XPS"	style="width:100%;"	value="<?php if (isset($Contents_XPS)) {print $Contents_XPS;} ?>"></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
	    <td width="<?php print $row_header_width; ?>">Most recent content update<br><font class="Note">(YYYY-MM-DD)</font></td>
	    <td align="left"><input type="text" name="Contents_LastUpdate" style="width:<?php print $input_width; ?>;" value="<?php if (isset($Contents_LastUpdate)) {print $Contents_LastUpdate;} ?>"></td>
	</tr>
</table>
<br>
<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>
<a name="datasource"></a>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" width="<?php print $row_header_width; ?>">Data Source</td>
		<td align="left">
			<div style="text-align:left;">
				<input type="radio" name="DataSource" value="Unknown" id="DataSourceUnknown" <?php if($DataSource=="Unknown") print "CHECKED";?>>Unknown<br>
				<input type="radio" name="DataSource" value="Primary" id="DataSourcePrimary" <?php if($DataSource=="Primary") print "CHECKED";?>>Primary<br>
				<input type="radio" name="DataSource" value="Secondary" id="DataSourceSecondary" <?php if($DataSource=="Secondary") print "CHECKED";?>>Secondary
			</div>
		</td>
	</tr>
</table>
<br>
<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>
<a name="hosttrackerid"></a>
<table cellpadding="2" cellspacing="0" border="0" width="100%">
	<tr>
		<td valign="top" width="<?php print $row_header_width; ?>">Host Tracker ID</td>
		<td align="left"><input type="text" name="HostTrackerID" value="<?php print $HostTrackerID; ?>" style="width:<?php print $input_width; ?>;"></td>
	</tr>
</table>
			
<br>
<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>
<table cellspacing="0" cellpadding="2" border="0" width="100%"><tr><td align="right">
	<input type="reset"		value="Reset"	onCLick="javascript:reload()" title="Undo all changes made to the form">
	<input type="button"	value="Save" 	onClick="javascript:submitForm('Save')" title="Save and preview the resource">
	<input type="button"	value="Delete"	onClick="javascript:submitForm('Delete')" title="Remove the resource from all records">
	<input type="hidden"	value=""		name="PressedButton" id="pressedButton">
</td></tr></table>
</form>

<script type="text/javascript">

function addPubMed(){
	var pubMedTable						= document.getElementById("PubMedTable");
	var pubMedEntries					= document.getElementById("PubMedEntries");
	var numEntries						= parseInt(pubMedEntries.value);
	var highestEntry					= document.getElementById("HighestEntry");
	var numHighestEntry					= parseInt(highestEntry.value);

	var newID							= document.getElementById("newID");
	var newTitle						= document.getElementById("newTitle");
	var newAuthors						= document.getElementById("newAuthors");
	var newCitation						= document.getElementById("newCitation");

	if(newID.value!=""){
		numHighestEntry				   += 1;
		highestEntry.value				= numHighestEntry;

		numEntries					   += 1;
		pubMedEntries.value				= numEntries;

		var row							= pubMedTable.insertRow(pubMedTable.rows.length);

		var cell						= row.insertCell(0);
		cell.innerHTML					= "<input type=text		style='width:100%'	value='"+newID.value+"'									name='PubMedID"+numHighestEntry+"'			id='pmID"+numHighestEntry+"'>";
		var cell						= row.insertCell(1);
		cell.innerHTML					= "<input type=text		style='width:100%'	value=\""+newTitle.value.replace(/\"/g, '&#34;')+"\"	name='PubMedTitle"+numHighestEntry+"'		id='pmTitle"+numHighestEntry+"'>";
		var cell						= row.insertCell(2);
		cell.innerHTML					= "<input type=text		style='width:100%'	value=\""+newAuthors.value.replace(/\"/g, '&#34;')+"\"	name='PubMedAuthors"+numHighestEntry+"'		id='pmAuthors"+numHighestEntry+"'>";
		var cell						= row.insertCell(3);
		cell.innerHTML					= "<input type=text		style='width:100%'	value=\""+newCitation.value.replace(/\"/g, '&#34;')+"\"	name='PubMedCitation"+numHighestEntry+"'	id='pmCitation"+numHighestEntry+"'>";
		var cell						= row.insertCell(4);
		cell.innerHTML					= "<input type=button	style='width:100%'	value='-'												onClick=\"deletePubMed('"+numHighestEntry+"',this)\">";

		newID.value						= "";
		newTitle.value					= "";
		newAuthors.value				= "";
		newCitation.value				= "";

	}else{
		alert("You must enter a PubMed ID");
	}
	newID.focus();
}

function deletePubMed(row, item){

	var pubMedTable						= document.getElementById("PubMedTable");
	var pubMedEntries					= document.getElementById("PubMedEntries");
	var numEntries						= parseInt(pubMedEntries.value);

	var newID							= document.getElementById("newID");
	var currentID						= document.getElementById("pmID"+row);

	var pmTitle							= document.getElementById("pmTitle"+row).value;
	var pmAuthors						= document.getElementById("pmAuthors"+row).value;
	var pmCitation						= document.getElementById("pmCitation"+row).value;
	
	var confirmation					= "Are you sure you wish to delete this entry?\n\n"+pmTitle+"\n"+pmAuthors+"\n"+pmCitation;

	if(confirm(confirmation)){
		//want to find the actual row of this input field
		while(item && item.tagName != "TR")
			item 						= item.parentNode;
		if(item)
			item.parentNode.removeChild(item);

		numEntries					   -= 1;
		pubMedEntries.value				= numEntries;

		newID.focus();
	}else{
		currentID.focus();
	}	
}

function reload(){
	if(confirm('Are you sure you want to discard all changes?')){
		document.getElementById("resourceChooser").submit();
	}
}

function submitForm(pressedButton){
	//must check if the form was completed
	var complete;
	var saved;
	
	if(document.getElementById("ShortName").value!=""){
		complete						= true;
	}else{
		complete						= false;
	}
	
	if(<?php print $choice; ?>==0){
		saved							= false;
	}else{
		saved							= true;
	}
	
	if(pressedButton=="Save"){
		if(complete && confirm("Are you sure you wish to save/modify this resource?")){
			pressedButtonInput			= document.getElementById("pressedButton")
			pressedButtonInput.value	= pressedButton;
			theForm 					= document.getElementById("resourceManager")
			theForm.submit()
		}else{
			alert("You must enter a 'Short Name' for the resource.");
		}
	}else if(pressedButton=="Delete"){
		if(saved && confirm("Are you sure you wish to delete this resource?")){
			pressedButtonInput			= document.getElementById("pressedButton")
			pressedButtonInput.value	= pressedButton;
			theForm 					= document.getElementById("resourceManager")
			theForm.submit()
		}else{
			alert("This resource has not been saved yet.");
		}
	}
}
</script>

<?php include("../bottom.php");?>
