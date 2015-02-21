<?php
	$dbhost = "localhost";
	$dbname = "gintrest";
	$dbuser = "root";
	$dbpass = "";
	
	$con = new PDO("mysql:host=$dbhost;dbname=$dbname",$dbuser,$dbpass);
	$con->setAttribute( PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION );
?>