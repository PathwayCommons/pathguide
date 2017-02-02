<?php
include("config_admin.php");
$websiteTitle									= $adminTitle."|Extras";
$tabs											= "admin/top_tabs_admin.php";
$logo											= "logo_admin.gif";
$menu											= "admin/top_menu_admin_extras.php";
include ("../top.php");

#store the posted/saved values into the database
$sqlUpdate1										= "";
$sqlUpdate2a									= "";
$sqlUpdate2b									= "";

if(isset($_POST['newsContent'])){
	$content									= $_POST['newsContent'];
	$sqlUpdate1									= "UPDATE extras SET content='".strFix($content)."' WHERE component='news'";
}elseif(isset($_POST['popnoteContent'])){
	$content									= $_POST['popnoteContent'];
	$sqlUpdate1									= "UPDATE extras SET content='".strFix($content)."' WHERE component='popnote'";
}elseif(isset($_POST['welcomeContent'])){
	$content									= $_POST['welcomeContent'];
	$sqlUpdate1									= "UPDATE extras SET content='".strFix($content)."' WHERE component='welcome'";
}elseif(isset($_POST['statsContent'])){
	$content									= $_POST['statsContent'];
	$sqlUpdate1									= "UPDATE extras SET content='".strFix($content)."' WHERE component='stats'";
}elseif(isset($_POST['footerContent'])){
	$content									= $_POST['footerContent'];
	$sqlUpdate1									= "UPDATE extras SET content='".strFix($content)."' WHERE component='footer'";
}elseif(isset($_POST['emailContent'])){
	$content									= $_POST['emailContent'];
	$sqlUpdate1									= "UPDATE extras SET content='".strFix($content)."' WHERE component='email'";
}elseif(isset($_POST['websitewidthContent'])){
	$contentWebsitewidth						= $_POST['websitewidthContent'];
	$contentMenuwidth							= $_POST['menuwidthContent'];
	$sqlUpdate2a								= "UPDATE extras SET content='$contentWebsitewidth' WHERE component='websitewidth'";
	$sqlUpdate2b								= "UPDATE extras SET content='$contentMenuwidth' WHERE component='menuwidth'";
}elseif(isset($_POST['htpasswordContent'])){
	$contentHTPassword							= $_POST['htpasswordContent'];
	$sqlUpdate1									= "UPDATE extras SET content='".strFix($contentHTPassword)."' WHERE component='htpassword'";
}elseif(isset($_POST['saving'])){
	print "<br><br><b>POST is working but save failed.</b><br><br>";
}else{
	#print "<br><br><b>No POST variables are set.</b><br><br>";
}

if($sqlUpdate1 != ""){
	$resultUpdate								= mysql_query($sqlUpdate1, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlUpdate1,$debugMode);}
}elseif($sqlUpdate2a != ""){
	$resultUpdate								= mysql_query($sqlUpdate2a, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlUpdate2a,$debugMode);}
	$resultUpdate								= mysql_query($sqlUpdate2b, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlUpdate2b,$debugMode);}
}

#retrieve the values out of the database
$sqlNews										= "SELECT content FROM extras WHERE component='news'";
$resultNews										= mysql_query($sqlNews, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlNews,$debugMode);}
$rowResultNews									= mysql_fetch_row($resultNews);

$sqlWelcome										= "SELECT content FROM extras WHERE component='welcome'";
$resultWelcome									= mysql_query($sqlWelcome, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlNews,$debugMode);}
$rowResultWelcome								= mysql_fetch_row($resultWelcome);

$sqlStats										= "SELECT content FROM extras WHERE component='stats'";
$resultStats									= mysql_query($sqlStats, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlNews,$debugMode);}
$rowResultStats									= mysql_fetch_row($resultStats);

$sqlPopNote										= "SELECT content FROM extras WHERE component='popnote'";
$resultPopNote									= mysql_query($sqlPopNote, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlPopNote,$debugMode);}
$rowResultPopNote								= mysql_fetch_row($resultPopNote);

$sqlFooter										= "SELECT content FROM extras WHERE component='footer'";
$resultFooter									= mysql_query($sqlFooter, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlFooter,$debugMode);}
$rowResultFooter								= mysql_fetch_row($resultFooter);

$sqlEmail										= "SELECT content FROM extras WHERE component='email'";
$resultEmail	 								= mysql_query($sqlEmail, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlEmail,$debugMode);}
$rowResultEmail									= mysql_fetch_row($resultEmail);

$sqlHTPassword									= "SELECT content FROM extras WHERE component='htpassword'";
$resultHTPassword 								= mysql_query($sqlHTPassword, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlHTPassword,$debugMode);}
$rowResultHTPassword							= mysql_fetch_row($resultHTPassword);

$sqlWebsitewidth								= "SELECT content FROM extras WHERE component='websitewidth'";
$resultWebsitewidth	 							= mysql_query($sqlWebsitewidth, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlWebsiteWidth,$debugMode);}
$rowResultWebsitewidth							= mysql_fetch_row($resultWebsitewidth);

$sqlMenuwidth									= "SELECT content FROM extras WHERE component='menuwidth'";
$resultMenuwidth								= mysql_query($sqlMenuwidth, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlMenuWidth,$debugMode);}
$rowResultMenuwidth								= mysql_fetch_row($resultMenuwidth);
?>

<a name="news"></a>
<font class=Title>News</font>
<form action="<?php print $adminURL; ?>extras.php#news" method="POST">
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td valign="top"><b>Final Result</b></td>
		<td align="center">
			
			<table width="180"><tr><td>
			<?php include("../news.php"); ?>
			</td></tr></table>
			<br><br>

		</td>
	</tr>
	<tr>
		<td valign="top"><b>HTML Editor</b></td>
		<td>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td align="right">
						<div id="newsOriginal" style="display:none;"><?php print $rowResultNews[0]; ?></div>
						<textarea id="newsInput" class="Input" onKeyUp="convert('news')" name="newsContent"></textarea>
					</td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td></td>
		<td align="right"><input type="button" value="Clear" onClick="clearInput('news')"><input type="button" value="Reset" onClick="resetInput('news')"><input type="submit" value="Save"></td>
	</tr>
</table><br>
<input type="hidden" name="saving">
</form>

<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>

<a name="welcome"></a>
<font class=Title>Welcome Message</font>
<form action="<?php print $adminURL; ?>extras.php#welcome" method="POST">
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td valign="top"><b>Final Result</b></td>
		<td align="left">
			Pathguide contains information about <b>###</b> 
			<div id="welcomeOutput"><?php print $rowResultWelcome[0]; ?></div><br>
		</td>
	</tr>
	<tr>
		<td width="<?php print $row_header_width; ?>" valign="top"><b>HTML Editor</b></td>
		<td>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td align="right">
						<div id="welcomeOriginal" style="display:none;"><?php print $rowResultWelcome[0]; ?></div>
						<textarea id="welcomeInput" class="Input" onKeyUp="convert('welcome')" name="welcomeContent"></textarea>
					</td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td></td>
		<td align="right"><input type="button" value="Clear" onClick="clearInput('welcome')"><input type="button" value="Reset" onClick="resetInput('welcome')"><input type="submit" value="Save"></td>
	</tr>
</table><br>
<input type="hidden" name="saving">
</form>

<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>

<a name="stats"></a>
<font class=Title>Statistics Info</font>
<form action="<?php print $adminURL; ?>extras.php#stats" method="POST">
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td valign="top"><b>Final Result</b></td>
		<td align="left">
			<div id="statsOutput"><?php print $rowResultStats[0]; ?></div><br>
		</td>
	</tr>
	<tr>
		<td width="<?php print $row_header_width; ?>" valign="top"><b>HTML Editor</b></td>
		<td>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td align="right">
						<div id="statsOriginal" style="display:none;"><?php print $rowResultStats[0]; ?></div>
						<textarea id="statsInput" class="Input" onKeyUp="convert('stats')" name="statsContent"></textarea>
					</td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td></td>
		<td align="right"><input type="button" value="Clear" onClick="clearInput('stats')"><input type="button" value="Reset" onClick="resetInput('stats')"><input type="submit" value="Save"></td>
	</tr>
</table><br>
<input type="hidden" name="saving">
</form>

<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>

<a name="popnote"></a>
<font class=Title>Web Popularity Note</font>
<form action="<?php print $adminURL; ?>extras.php#popnote" method="POST">
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td width="<?php print $row_header_width; ?>" valign="top"><b>Final Result</b></td>
		<td align="right">
			
			<table>
				<tr>
					<td valign="top"><img src="<?php print $imagesURL; ?>icon_note_large.gif" alt=""></td>
					<td class="Note">
						<div id="popnoteOutput"><?php print $rowResultPopNote[0]; ?></div>
					</td>
				</tr>
			</table><br>

		</td>
	</tr>
	<tr>
		<td width="<?php print $row_header_width; ?>" valign="top"><b>HTML Editor</b></td>
		<td>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td align="right">
						<div id="popnoteOriginal" style="display:none;"><?php print $rowResultPopNote[0]; ?></div>
						<textarea id="popnoteInput" class="Input" onKeyUp="convert('popnote')" name="popnoteContent"></textarea>
					</td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td></td>
		<td align="right"><input type="button" value="Clear" onClick="clearInput('popnote')"><input type="button" value="Reset" onClick="resetInput('popnote')"><input type="submit" value="Save"></td>
	</tr>
</table><br>
<input type="hidden" name="saving">
</form>

<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>

<a name="footer"></a>
<font class=Title>Footer</font>
<form action="<?php print $adminURL; ?>extras.php#footer" method="POST">
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td width="<?php print $row_header_width; ?>" valign="top"><b>Final Result</b></td>
		<td align="center">
			
			<div id="footerOutput" class="Footer"><?php print $rowResultFooter[0]; ?></div>
			<br><br>

		</td>
	</tr>
	<tr>
		<td width="<?php print $row_header_width; ?>" valign="top"><b>HTML Editor</b></td>
		<td>

			<table cellpadding="0" cellspacing="0" border="0" width="100%">
				<tr>
					<td align="right">
						<div id="footerOriginal" style="display:none;"><?php print $rowResultFooter[0]; ?></div>
						<textarea id="footerInput" class="Input" onKeyUp="convert('footer')" name="footerContent"></textarea>
					</td>
				</tr>
			</table>

		</td>
	</tr>
	<tr>
		<td></td>
		<td align="right"><input type="button" value="Clear" onClick="clearInput('footer')"><input type="button" value="Reset" onClick="resetInput('footer')"><input type="submit" value="Save"></td>
	</tr>
</table><br>
<input type="hidden" name="saving">
</form>

<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>

<a name="email"></a>
<font class=Title>Contact E-mail</font>
<form action="<?php print $adminURL; ?>extras.php#email" method="POST">
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td width="<?php print $row_header_width; ?>" valign="top"><b>Enter your e-mail</b></td>
		<td align="right">
			
			<input type="text" style="width:<?php print $input_width; ?>" value="<?php print $rowResultEmail[0]; ?>" id="email" name="emailContent">
			<br><br>

		</td>
	</tr>
	<tr>
		<td></td>
		<td align="right"><input type="button" value="Clear" onClick="document.getElementById('email').value='';"><input type="reset" value="Reset"><input type="submit" value="Save"></td>
	</tr>
</table><br>
<input type="hidden" name="saving">
</form>

<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>

<a name="htinfo"></a>
<font class=Title>Host-Tracker Info</font>
<form action="<?php print $adminURL; ?>extras.php#htinfo" method="POST">
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td>Host-Tracker User Name</td>
		<td align="right"><div style="text-align:left; width:<?php print $input_width; ?>;">pathguide</div></td>
	</tr>
	<tr>
		<td valign="top"><b>Host-Tracker Password</b></td>
		<td align="right">
			
			<input type="text" style="width:<?php print $input_width; ?>" value="<?php print $rowResultHTPassword[0]; ?>" id="htpassword" name="htpasswordContent">
			<br><br>

		</td>
	</tr>
	<tr>
		<td valign="top">Instructions</td>
		<td align="right"><div style="text-align:left; width:<?php print $input_width; ?>;">Go to <a href="http://host-tracker.com">http://host-tracker.com</a> and log in on the right side using the user name and password shown above. To add a new resource for monitoring, go to 'monitoring tasks' and click 'new' on the upper right.  There you can put the link of the homepage to be monitored and uncheck the 'on error, notify to:' feature.  Save the task and on the resulting page do a search/find for the newly added link.  When the hyperlink is found, hover the mouse over it and observe the status bar.  The 6 (or more) digit number following 'id=' is the unique ID of that site as assigned by host-tracker. Take note of this ID and enter it in the respective field on the resource management page.<br><br></div></td>
	</tr>
	<tr>
		<td></td>
		<td align="right"><input type="button" value="Clear" onClick="document.getElementById('htpassword').value='';"><input type="reset" value="Reset"><input type="submit" value="Save"></td>
	</tr>
</table><br>
<input type="hidden" name="saving">
</form>

<table class="HorizontalDivider" width="100%"><tr><td width="100%" align="right">&nbsp;</td></tr></table>

<a name="dimensions"></a>
<font class=Title>Website Dimensions</font>
<form action="<?php print $adminURL; ?>extras.php#dimensions" method="POST">
<table width="100%" cellspacing="0" cellpadding="2">
	<tr>
		<td valign="top"><b>Whole Site Width</b></td>
		<td align="right">
			
			<input type="text" style="width:<?php print $input_width; ?>;" value="<?php print $rowResultWebsitewidth[0]; ?>" id="websitewidth" name="websitewidthContent">

		</td>
	</tr>
	<tr>
		<td valign="top"><b>Menu Width</b></td>
		<td align="right">
			
			<input type="text" style="width:<?php print $input_width; ?>;" value="<?php print $rowResultMenuwidth[0]; ?>" id="menuwidth" name="menuwidthContent">

		</td>
	</tr>
	<tr>
		<td><br><br><br></td>
		<td align="right"><input type="button" value="Default" onClick="document.getElementById('websitewidth').value='780';document.getElementById('menuwidth').value='130';"><input type="reset" value="Reset"><input type="submit" value="Save"></td>
	</tr>
</table><br>
<input type="hidden" name="saving">
</form>


<script type="text/JavaScript">

function resetInput(section){
	var inputID					= ""+section+"Input";
	var originalID				= ""+section+"Original";

	var theInput				= document.getElementById(inputID);
	var theOriginal				= document.getElementById(originalID);

	theInput.value				= theOriginal.innerHTML;

	convert(section);
}

function clearInput(section){
	var inputID					= ""+section+"Input";

	var theInput				= document.getElementById(inputID);

	theInput.value				= "";
	theInput.innerHTML			= "";

	convert(section);
}

function convert(section){
	var inputID					= ""+section+"Input";
	var outputID				= ""+section+"Output";

	var theInput				= document.getElementById(inputID);
	var theOutput				= document.getElementById(outputID);

	theOutput.innerHTML	= theInput.value;
}

resetInput('news');
resetInput('welcome');
resetInput('stats');
resetInput('popnote');
resetInput('footer');

</script>

<?php
include("../bottom.php");
?>