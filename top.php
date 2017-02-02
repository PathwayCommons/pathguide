<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN"><HTML>
<HEAD>

<!-- START META () -->
<META http-equiv="Content-Type" content="text/html; charset=ISO-8859-1">
<META NAME="FileType" CONTENT="html">
<META NAME="DocumentTitle" CONTENT="Pathguide: The Pathway Resource List">
<META NAME="Title" CONTENT="Home">
<META NAME="Description" CONTENT="Pathguide: The Pathway Resource List, 
provided by the Computational Biology Center at Memorial Sloan-Kettering Cancer 
Center (MSKCC) aims to provide a comprehensive catalog of 
biological pathway resources available on the internet.  Pathguide is a 
spin-off project of the BioPAX effort (http://www.biopax.org).">
<META NAME="Keywords" CONTENT="biological, pathway, biopax, databases, 
MSKCC, cbio, bioinformatics, software, tools, computational biology">
<link rel="icon" type="image/png" href="http://cbio.mskcc.org/favicon.ico">
<TITLE><?php print $websiteTitle; ?></TITLE>
<!-- END META () -->

<?php
include("$backfolder"."pathguide_style.css");
?>

<script type="text/javascript" src="<?php print $backfolder ?>js/jquery-1.3.2.min.js"></script>

<?php if ($isDBInteractions) { ?>
    <script type="text/javascript" src="js/json2.min.js"></script>
    <script type="text/javascript" src="js/AC_OETags.min.js"></script>
    <script type="text/javascript" src="js/cytoscapeweb.min.js"></script>
    <script type="text/javascript" src="js/interactions.js"></script>
<?php } ?>

<script type="text/javascript">
/*DYNAMIC HIGHLIGHTING*/
function highlight(item){ item.addClass("Highlight"); }
function unhighlight(item){ item.removeClass("Highlight"); }
</script>

</HEAD>

	
<BODY>
<a name="top"></a>
<table cellspacing="0" cellpadding="0" border="0" width="100%" style="height:100%; background: url('<?php print $imagesURL; ?>right_drop_shadow.gif'); background-repeat: repeat-y; background-position: <?php print $website_width; ?> 0;">
	<tr style="height:85;">
		<td width="<?php print $website_width; ?>" style="height:85;">

			<table width="<?php print $website_width; ?>" border="0" cellspacing="0" cellpadding="0">
				<tr><td width="100%" style="height:60; background: url('<?php print $imagesURL; ?>top_bg.gif');" align="left">
					<table cellspacing="0" cellpadding="0" border="0" style="height:100%;" width="100%"><tr>
						<td width="380" valign="bottom" align="right"><a href="javascript:formSubmit('true','<?php print $rootURL; ?>','none')"><img border="0" src="<?php print $imagesURL.$logo; ?>" alt="Pathguide: the pathway resource list"></a></td>
						<td align="right" valign="top">
							<?php
							include ($tabs);
							?>
							<noscript><br><b><font color='red'>Javascript is not enabled in your browser!</font></b></noscript>
						</td>
						<td width="10">&nbsp;</td>
						</tr>
					</table>
				</td></tr>
				<tr><td width="100%">
					<table width="100%" cellspacing="0" cellpadding="0" border="0">
						<tr>
							<td bgcolor="<?php print $blue_lite; ?>" width="<?php print $menu_width; ?>"><div class="AboveMenu"></div></td>
							<td width="25"><img src="<?php print $imagesURL; ?>round_corner.gif" alt=""></td>
							<td valign="top" style="background-image: url('<?php print $imagesURL; ?>top_stripe.gif'); background-position: top; background-repeat: repeat-x;"><img src="<?php print $imagesURL; ?>top_stripe.gif" alt=""></td>
						</tr>
					</table>
				</td></tr>
			</table>

		</td>
		<td></td>
	</tr>
	<tr>
		<td width=<?php print $website_width; ?> valign="top">
			<table width=<?php print $website_width; ?> border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" style="height:100%;">
				<tr>
					<td width="<?php print $menu_width; ?>" valign="top" style="background-color: <?php print $blue_lite; ?>;">
						<?php
						include ($menu);
						?>
					</td>
					<td width="6" bgcolor="#FFFFFF"></td>
					<td width="2" bgcolor="<?php print $blue_dark; ?>"></td>
					<td valign="top" style="padding:4 10 4 12;">

						<!-- Main area -->
						<table border="0" cellspacing="0" style="height:100%;" width="100%" bgcolor="#FFFFFF">
							<tr>
								<td width="100%" valign="top">

<!-- **************** -->
<!-- INSERT CONTENTS HERE -->
