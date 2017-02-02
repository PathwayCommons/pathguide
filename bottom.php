<!-- END OF CONTENTS -->
								</td>
							</tr>
							<tr>
								<td width="100%" align="center" valign="bottom"><br><hr>
									<font class="Footer">
									<?php
									$sqlFooter						= "SELECT content FROM extras WHERE component='footer'";
									$resultFooter					= mysql_query($sqlFooter, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sqlFooter,$debugMode);}
									while ($rowResultFooter			= mysql_fetch_row($resultFooter)) {
										print $rowResultFooter[0];
									}
									?>
									</font>
									</td>
							</tr>
						</table>
					<!-- END Main area -->
					</td>
				</tr>
			</table>

		</td>
		<td></td>
	</tr>
</table>

<script src="http://www.google-analytics.com/urchin.js" type="text/javascript">
</script>
<?php
if(isset($search_helper) && $search_helper){include("js/search_helper.js");}
?>
<script type="text/javascript">
_uacct = "UA-155159-3";
urchinTracker();
</script>
</BODY>

</html>
