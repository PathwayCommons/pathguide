<?php
include("config.php");
$websiteTitle				= $pathguideTitle;
$tabs						= "top_tabs.php";	
$logo						= "logo.gif";
$menu						= "top_menu.php";
include("top.php");
?>
<font class=Title>Contact Pathguide</font><br><br>
<?php
$subject					= "";
$body						= "";
$from						= "";

if(isset($_POST["z"])){
	#send out the email
	$sqlEmail				= "SELECT content FROM extras WHERE component='email'";
	$resultEmail			= mysql_query($sqlEmail, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlEmail,$debugMode);}
	$rowEmail				= mysql_fetch_row($resultEmail);
	$toEmail				= $rowEmail[0];
	$subject				= $_POST["y"];
	$body					= $_POST["z"];
	$from					= $_POST["x"];
	$fromEmail				= $from;
	
	#validate the given from line, if it is not a valid email, use a valid bogus email
	if(!eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $fromEmail)) {
		$fromEmail			= "guest@pathguide.org";
	}
	
	#check that no links are being sent to prevent spamming
	if (preg_match("/http/i", $from) || preg_match("/http/i", $subject) || preg_match("/http/i", $body)) {
		echo("<p><b>Your message could not be sent!<br>If you are including links please omit the \"http://\" portion.</b></p>");
	}else{
		if(mail($toEmail, "Pathguide: ".$subject, $from." says: \n\n".$body, "From: ".$fromEmail)){
			echo("<p><b>Message successfully sent!</b></p>");
			$subject 		= "";
			$body 			= "";
			$from 			= "";
		}else{
			echo("<p><b>Message delivery failed...Please try again.</b></p>");
		}
	}
	print "<hr>";
}
#print out e-mail form
print "Please complete all fields. You may enter your e-mail in the 'From' field if you wish to be contacted in regards to your message, otherwise a name will suffice.<br><br>If you are including links, please omit the \"http://\" portion.<br><br>";
?>

	Thanks for helping make Pathguide better, your input is appreciated!<br><br>
	
	<form action="" method="POST">
	<table cellpadding="0" cellspacing="0" width="100%">
		<tr>
			<td>				From:		</td><td align="right"><input type=text		name="x"	id="x"	style="width:<?php print $input_width; ?>"	value="<?php print str_replace('"', '&#34;', $from); ?>"></td>
		</tr>
		<tr><td>&nbsp;</td><td></td></tr>
		<tr>
			<td>				Subject:	</td><td align="right"><input type=text		name="y"	id="y"	style="width:<?php print $input_width; ?>"	value="<?php print str_replace('"', '&#34;', $subject); ?>"></td>
		</tr>
		<tr><td>&nbsp;</td><td></td></tr>
		<tr>
			<td valign="top">	Message:	</td><td align="right"><textarea			name="z"	id="z"	style="width:<?php print $input_width; ?>; height:200;" cols="20" rows="10"><?php print $body; ?></textarea></td>
		</tr>
	</table><br>
	<table class="HorizontalDivider" width="100%">
		<tr><td width="100%" align="right"><input type="button" value="Clear" onClick="clearContactForm()"><input type="submit" value="Send"></td></tr>
	</table>
	</form>
	
	<script type="text/javascript">
	<!--
	function clearContactForm() {
		if (confirm('Are you sure you wish to discard this e-mail?')) {
			document.getElementById("x").value = "";
			document.getElementById("y").value = "";
			document.getElementById("z").innerHTML = "";
		}
	}
	//-->
	</script>

<?php
$search_helper			= true;
include("bottom.php");
?>