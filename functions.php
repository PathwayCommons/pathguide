<?php

function repErr($number, $mssg, $sql, $debugMode){
	if($debugMode){
        echo "<br><br><b>The query could not be processed...<br>";
        echo "Error $number: $mssg<br>";
        echo "</b><br><br>";
	}else{
		echo "<br><br><b>The requested information is temporarily unavailable, please try again later.  The administrator has been notified.</b><br><br>";
		error_log("MySQL Error\n\n".$number.": ".$mssg."\n\n\nStatement\n\n".$sql, 1, "chrtannus@gmail.com");
	}
}

function throw_error($message) {
  $error_page = "/err/error.php";

  $error_url = $error_page;
  $error_url .= "?REDIRECT_ERROR_NOTES=$message";
  $error_url .= "&REDIRECT_URL=" . $GLOBALS["PHP_SELF"];
  $error_url .= "&REDIRECT_REQUEST_METHOD=$REQUEST_METHOD";
  $error_url .= "&REDIRECT_STATUS=501";
  Header("Status: 501");
  Header("Location: $error_url");
  exit;
}

function strFix($string) {
	if (!get_magic_quotes_gpc()) {
		return addslashes($string);
	} else {
		return $string;
	}
}

?>