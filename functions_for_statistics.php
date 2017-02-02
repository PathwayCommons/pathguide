<?php

function getResources($section, $table, $value, $cn){
	print " (<a href=\"javascript:toggle('$section')\"><span id=Toggle$section>show</span></a>)";
	print "<div id=\"List$section\" style=\"display:none;\"><ul>";
	$sql						= "SELECT names.DBID, shortname, fullname FROM names, dbs_in_$table"."_types WHERE dbs_in_$table"."_types.$table"."_ID='$value' AND dbs_in_$table"."_types.DBID=names.DBID ORDER BY shortname";
	$resultCount2				= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
	while($rowResultCount2		= mysql_fetch_row($resultCount2)){
		print "<li><a class=\"ListLink\" href=\"javascript:editResource('$rowResultCount2[0]')\">$rowResultCount2[1]: $rowResultCount2[2]</a></li>";
	}
	print "</ul></div>";
}

function printTitle($tag, $title){
	print "<a name=\"$tag\"></a>";
	print "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\"><tr><td class=\"CategoryTitles\">$title</td><td class=\"CategoryTitlesEnder\">&nbsp;</td><td class=\"CategoryTitles\" width=\"10\">&nbsp;</td></tr></table>";
	print "<table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td style=\"height:6;\"></td></tr><tr><td class=\"TableListTop\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"0\"><tr><td class=\"TableListBottom\"><table width=\"100%\" cellspacing=\"0\" cellpadding=\"2\">";
}

function getContentsEnumeration($column, $text, $row_color, $unhighlight_color, $cn){
	$sql						= "SELECT COUNT($column), SUM($column) FROM contents WHERE $column!='0'";
	$resultCount				= mysql_query($sql, $cn);if (mysql_errno()) {repErr(mysql_errno(),mysql_error(),$sql,$debugMode);}
	$rowResultCount				= mysql_fetch_row($resultCount);
	print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>Resources containing $text</td><td align=right>$rowResultCount[0]</td></tr>";
	print "<tr bgcolor=\"$row_color\" onMouseOver=\"highlight($(this))\" onMouseOut=\"unhighlight($(this))\"><td>Total $text</td><td align=\"right\">".number_format($rowResultCount[1])."</td></tr>";
}

?>