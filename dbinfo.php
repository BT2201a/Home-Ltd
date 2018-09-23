<?php
	/*
	Name: Mike Blackmore
	File: dbinfo.php
	Date: 14/05/2011
	Info: This file is used by all PHP scripts that need a connection to the MySQL database on bimserver2
	
	*/
	
	$debug=TRUE;
	
	function dbconnect()
		{
		// Link to the dbms server
		$link=mysql_connect("mblackmoredb.bimserver2.com","mblackmoredb","H4ck3r");
			if ( $link )
				{
				if ( $debug ) echo "<p>MySQL Link ID: ".$link."</p>";
				}
			else
				{
				echo "<p class=\"error\">Error:MySQL connection failed</p>";
				exit;
				}
				
		// Connect to mblackmoredb database
			$result=mysql_select_db("mblackmoredb", $link);		
			if ( $result )
				{
				if ( $debug ) echo "<p>Database Connection: OK</p>";
				}
			else
				{
				echo "<p class=\"error\"><strong>ERROR:</strong> Database connection failed</p>";
				exit;
				}
		}
?>