<script type="text/JavaScript">

<?php
#once a search is made, this code reselects those criteria in the search fields for the users reference and search modification
?>
document.getElementById("organisms<?php print $organisms ; ?>").selected				= true;
document.getElementById("availability<?php print $availability ; ?>").selected			= true;
document.getElementById("standards<?php print $standards ; ?>").selected				= true;

<?php
#to store the user's order listing preference we submit the value in the hidden input in the search form
?>

function formSubmit(reset, action, dbid){
	<?php
	#just incase the search parameters were changed, we do not want to submit the form with the new parameters without actually pressing search, since here we are pressing Details or Back to List, the search parameters should not change, so we must reset them to the page default
	#clicking Home however resets the variables, as does clicking on the complete list
	?>
	theForm																				= document.getElementById("search");
	if(reset=='true'){
		document.getElementById("organismsall").selected								= true;
		document.getElementById("availabilityall").selected								= true;
		document.getElementById("standardsall").selected								= true;
	}else{
		document.getElementById("organisms<?php print $organisms ; ?>").selected		= true;
		document.getElementById("availability<?php print $availability ; ?>").selected	= true;
		document.getElementById("standards<?php print $standards ; ?>").selected		= true;
	}
	<?php
	#now we set the action of the form to the full record page and since the hidden input 'order' has the value of the current list type, clicking 'details' submits the info and stores it
	?>
	theForm.action																		= action;
	document.getElementById("DBID").value												= ''+dbid;
	theForm.submit();
}

</script>