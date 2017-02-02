		<table cellspacing="0" cellpadding="0" border="0" style="height:6;" width="100%">
			<tr><td style="height:6;" bgcolor="#FFFFFF"></td></tr>
		</table>
		<table cellspacing="0" cellpadding="0" border="0" id="Navigation" class="Menu" width="100%">
			<tr><td class="MenuHeader">Navigation</td></tr>
				<?php
				#if we are looking at the index page these arrays are unset and refilled during the title printing (functions.php), however if we are looking at a full record page then the categories are printed from the session
				if(isset($_SESSION['categoryIDsArray'])){
					$categoryIDsArray					= $_SESSION['categoryIDsArray'];
					$categoryNamesArray					= $_SESSION['categoryNamesArray'];
					$num								= count($categoryIDsArray);
					for($i=0; $i<$num; $i++){
						print "<tr><td>";
						print "<a class=\"MenuLink\" href=\"$rootURL#$categoryIDsArray[$i]\">$categoryNamesArray[$i]</a>";
						print "</td></tr>";
					}
				}
				?>
		</table>
			<?php
			if($organisms!="all" || $availability!="all" || $standards!="all"){
			?>
				<table cellspacing="0" cellpadding="0" border="0" width="100%">
					<tr><td class="MenuSearchItem" width=<?php print $menu_width; ?>><input type="button" value="Complete List" style="width:100%;" onClick="javascript:formSubmit('true','<?php print $rootURL; ?>','none')"></td></tr>
				</table>
			<?php
			}
			?>
			
		<form action="<?php print $rootURL; ?>" method="GET" id="search" style="margin:0;">
		<table cellspacing="0" cellpadding="0" border="0" width="100%">
			<tr><td style="height:6;" bgcolor="#FFFFFF"></td></tr>
			<tr><td class="MenuHeader">Search</td></tr>
			<tr><td width="<?php print $menu_width; ?>">
				<div class="MenuSearchItem">Organisms<br>
					<select style="width:100%;" name="organisms" id="organisms">
						<option value="all" id="organismsall">		All					</option>
						<?php
						#this statement looks for organism names by combining the organism id in the organism_types and dbs_in_organism_types tables, this way only those organisms which actually exist in the databases are printed, all non used organisms are ignored
						$sql							= "SELECT DISTINCT organism_types.organism_ID,scientific_name, common_name FROM organism_types, dbs_in_organism_types WHERE dbs_in_organism_types.organism_ID=organism_types.organism_ID ORDER BY scientific_name";
						$result							= mysql_query($sql,$cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}

						while($organism					= mysql_fetch_row($result)){
							$binomial_nomenclature		= explode(" ", $organism[1]);
							if(sizeof($binomial_nomenclature) > 2){
								$string					= $organism[2];
							}else{
								$string					= "" . $binomial_nomenclature[0]{0} . ". " . $binomial_nomenclature[1];
							}
							print "\n<option value=\"$organism[0]\" id=\"organisms$organism[0]\">$string</option>";
						}
						?>
					</select>
				</div>
			</td></tr>
			<tr><td width="<?php print $menu_width; ?>">
				<div class="MenuSearchItem">Availability<br>
					<select style="width:100%;" name="availability" id="availability">
						<option value="all" id="availabilityall">	All					</option>
						<option value="1"	id="availability1">		Free for all users	</option>
						<option value="2"	id="availability2">		Academic			</option>
						<option value="3"	id="availability3">		License purchase	</option>
					</select>
				</div>
			</td></tr>
			<tr><td width="<?php print $menu_width; ?>">
				<div class="MenuSearchItem">Standards<br>
					<select style="width:100%;" name="standards" id="standards">
						<option value="all" id="standardsall">		All					</option>
						<option value="5"	id="standards5">		BioPAX				</option>
						<option value="8"	id="standards8">		CellML				</option>
						<option value="4"	id="standards4">		PSI-MI				</option>
						<option value="7"	id="standards7">		SBML				</option>
					</select>
				</div>
			</td></tr>
			<tr><td width="<?php print $menu_width; ?>">
				<div class="MenuSearchItem">
					<table cellspacing="0" cellpadding="0" border="0" width="100%"><tr><td>
						<input type="hidden" value="<?php print $order; ?>" name="order" id="order">
						<input type="hidden" value="none" name="DBID" id="DBID">
						<input type="reset" value="Reset" name="reset" style="width:100%;">
					</td><td>
						<input type="submit" value="Search" style="width:100%;">
					</td></tr></table>
				</div>
			</td></tr>
			<tr><td style="height:6;" bgcolor="#FFFFFF"></td></tr>
		</table>
		</form>
		<table cellspacing="0" cellpadding="0" border="0" class="Menu" width="100%">
			<tr><td class="MenuHeader">Analysis</td></tr>
			<tr><td><a class="MenuLink" href="<?php print $rootURL; ?>statistics.php">Statistics</a></td></tr>
			<tr><td><a class="MenuLink" href="<?php print $rootURL; ?>interactions.php">Database Interactions</a></td></tr>
			<tr><td style="height:6;" bgcolor="#FFFFFF"></td></tr>
			<tr><td class="MenuHeader">Contact</td></tr>
			<tr><td><a class="MenuLink" href="<?php print $rootURL; ?>contact.php">Comments, Questions, Suggestions are Always Welcome!</a></td></tr>
			<tr><td style="height:6;" bgcolor="#FFFFFF"></td></tr>
		</table>