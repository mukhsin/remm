<?php
	$hostname = "localhost";
	$username = "root";
	$password = "";
	$database = "remm";

	mysql_connect($hostname, $username, $password) or die ("Gagal");
	mysql_select_db($database) or die ("Gagal memilih database");
?>
