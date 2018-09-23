<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	delroom.php
	Author:		Mike Blackmore
	Background: Page to allow staff at estate agent to add rooms to a newly created property record
	Created:	24/04/2011 - 
	Modified:	
	
-->

<html xml:lang="en" xmlns="http://www.w3.org/1999/xhtml" lang="en">

	<head>
		<title>Assignment 4 - Home Ltd Estate Agents</title>
		
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
		<meta http-equiv="Author" content="Mike Blackmore"/>

		<link rel="stylesheet" type="text/css" href="styles.css" />
		
		<!-- Google Analytics -->
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-20775469-2']);
		  _gaq.push(['_trackPageview']);

		  (function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();
		</script>		
	</head>
	
	<body>
				
	
	<?php
	
	include 'dbinfo.php';	// The function dbconnect() used to connect to mblackmoredb database on bimserver2
	

	//---Main-----------------------------------------------------------------------------------------------------------------------------------------

		session_start();															// Using sessions to retain values between posts
	
		$debug=TRUE;

		$uploaddir='/home/mblackmore/mblackmore.bimserver2.com/home-ltd/rooms/';	// folder for room images
		
		if ( $_SESSION['userid'] )
			{
			printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
			echo '<h3>Delete Room Information</h3><hr />';
				
			if ( $_POST['roomid'] )
				{
				$sql="DELETE FROM room where id='" . $_POST['roomid'] . "'";
			
				echo '<p>Deleting room ' . $_POST['roomid'] . '</p>';
			
				if ( $debug ) echo '<p>INFO: SQL ' . $sql . '</p>';
				
				dbconnect();
				
				$result=mysql_query($sql);
				
				if ( $result )
					{
					echo '<p>Room successfully deleted</p>';
					
					$filename=$uploaddir.$_POST['roomid'].'.jpg';

					if ( file_exists($filename) )
						{
						echo '<p>Deleting room photo: ' . $filename . '</p>';
						$result=unlink($filename);
						if ( $result ) echo '<p>photo also deleted</p>';
						else		   echo '<p>Failed to delete photo</p>';
						}
					}
					
				else
					{
					echo '<p>Failed to delete the room</p>';
					
					}
				}
			}
		else
			{
			echo '<h3>Delete Room Information</h3><hr />
			<p>Unauthorised Access. Please Login</p>';;
			}

	//------------------------------------------------------------------------------------------------------------------------------------------------
	
	?>

		
		<p>Click <a href="./index.php" title="here">here</a> to return to main page.</p>		<!-- Link back to the index page -->
		
		<p><a href="http://validator.w3.org/check?uri=referer"><img
				src="http://www.w3.org/Icons/valid-xhtml10-blue"
				alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a></p>
		
	
	</body>
</html>
