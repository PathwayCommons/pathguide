<?php

$host					= "localhost";					#Used for database connections (config.php and config_admin.php)
#$host					= "127.0.0.1";					#this works better on the mac

$db					= "database";					#the name of the database, used in connection

$user					= "user";					#regular visitor, allowed to SELECT
$password				= "password";					#regular visitor's password

$user_admin				= "useradmin";					#admin user, has full range of functions -> DELETE, INSERT, UPDATE etc
$password_admin				= "passwordadmin";				#the admin password

#$rootURL				= "http://$host/~vukpavlovic/pathguide/prl/";
$rootURL				= "http://pathguide.org/";	#Used in images and linking of files
#$rootURL				= "http://192.168.81.175/";	#Used in images and linking of files

$debugMode				= false;						#this variable tells the code if sql query errors should be announced (true) or not (false)

?>
