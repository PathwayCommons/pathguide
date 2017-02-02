<?php

session_start();

include('../db.php');

$adminURL							= $rootURL."admin/"; #used for linking to other admin sites and also for determining if the viewer is an admin (for the statistics page which is viewed by both the admin and visitors, but displays more content to the admin)
$fullRecordDBID						= "none";

$cn									= mysql_connect($host, $user_admin, $password_admin);
mysql_select_db($db,$cn);

include('../functions.php');
include('../skin.php');

$pubmed_link_taxonomy				= "http://www.ncbi.nlm.nih.gov/Taxonomy/Browser/wwwtax.cgi?id=";

$fullRecordDBID						= 0;
if (isset($_GET['resource'])){
	$fullRecordDBID					= $_GET['resource'];
}

$backfolder							= "../";
?>
