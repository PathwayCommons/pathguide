<?php
# this file maintains the session variables so that searches and listing orders are constant throughout the surfer's visit
# from these session variables, the appropriate mysql statements are produced so that the listing shows only searched resources

session_start();

include('db.php');
$cn									= mysql_connect($host, $user, $password);
mysql_select_db($db,$cn);

$backfolder							= "";

include('functions.php');
include('skin.php'); #color variables, images directory, web component widths etc...

$organisms							= "all";
$availability						= "all";
$standards							= "all";
$order								= "alphabetic";
$fullRecordDBID						= "none";
$pubmed_link_taxonomy				= "http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=";

#if a search was conducted then we must store the form values (from top.html)
if (isset($_GET['organisms']))			$_SESSION['organisms']		= $_GET['organisms'];
if (isset($_GET['availability']))		$_SESSION['availability']	= $_GET['availability'];
if (isset($_GET['standards']))			$_SESSION['standards']		= $_GET['standards'];
if (isset($_GET['order']))				$_SESSION['order']			= $_GET['order'];

if (isset($_GET['DBID']))				$fullRecordDBID				= $_GET['DBID'];
	
if(isset($_SESSION['organisms']))		$organisms					= $_SESSION['organisms'];
if(isset($_SESSION['availability']))	$availability				= $_SESSION['availability'];
if(isset($_SESSION['standards']))		$standards					= $_SESSION['standards'];
if(isset($_SESSION['order']))			$order						= $_SESSION['order'];

#in order to perform the search with the user's specified criteria, both the FROM and WHERE clauses of the sql statement need to be augmented with the above inputs
$from_search_criteria				= " ";
$where_search_criteria				= " ";
#if the criteria indicates 'all', this means that nothing was specified and this criteria is thus ignored
if($organisms!="all"){	
	#first must add the appropriate database to look in
	$from_search_criteria		   .= "JOIN dbs_in_organism_types USING (DBID) ";
	#then must indicate the particular value we are searching for and join the database records by the DBID
	$where_search_criteria		   .= "AND dbs_in_organism_types.organism_ID='$organisms' ";
}
if($availability!="all"){
	$from_search_criteria		   .= "JOIN dbs_in_availability_types USING (DBID) ";
	$where_search_criteria		   .= "AND dbs_in_availability_types.Availability_ID='$availability' ";
}
if($standards!="all"){
	$from_search_criteria		   .= "JOIN dbs_in_download_types USING (DBID) ";
	$where_search_criteria		   .= "AND dbs_in_download_types.Download_ID='$standards' ";
}

?>
