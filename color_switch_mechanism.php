<?php
#this is the row color switching mechanism -> gray or transparent (so that table corner fades show up)
if (!(isset($row_color)) || (isset($row_color) && $row_color=="")){
	$row_color					= "$gray_lite";
	$unhighlight_color			= "Gray";
}else{
	$row_color					= "";
	$unhighlight_color			= "White";
}
?>