<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	booking.php
	Author:		Mike Blackmore
	Background: Page to allow customers to register viewing request
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
	
		$debug=FALSE;
		
			echo '<h3>Viewing Request Form</h3><hr />';
			
		if ( $_POST['Property'] )
			{
											
			if ( $debug ) 
					{
					echo '<p>INFO: POST<br />';
					foreach ($_POST as $key => $value)
					{
					echo "\$$key = $value<br />";
					}
					echo '</p>';
					}

			if ( $_POST['email'] )
				{
				// Save to bookings table
				
				dbconnect();
				
				$sql="INSERT INTO viewing VALUES ('', '" . $_POST['Property'] . "', '" . addslashes($_POST['name']) . "', '" . addslashes($_POST['email']) . "', '" . addslashes($_POST['mobile']) . "')";
				
				if ( $debug ) echo '<p>INFO SQL ' . $sql . '</p>';
				
				$result=mysql_query($sql);											// Insert the viewing request into the table
								
				if  ( $result ) 
					{
					echo '<p>Booking Request Successful</p>' ;
					}
				else
					{
					echo '<p>An Error Occured. Please call the Office on 07540388741</p>' ;
					}
				
				}
			else
				{
				// Display the bookings form	

				echo '<p>If you would like to arrange a viewing to the selected property, please complete the attached form <br />and click <strong>Request Booking</strong>.</p>
				<p>A <strong>Home Ltd</strong> representative will then contact you and the vendor to arrange a suitable time.</p>
				<p>Thankyou.</p><br />

				<form enctype="multipart/form-data" method="post" action="booking.php" >
						<div><input type="hidden" name="Property" value="' . $_POST['Property'] . '"/></div>
						<table border="1" style="width:640px">
								
							<tr>
								<td style="width:20px">Name:</td><td style="width:350px"><input type="text" name="name" style="width:250px"/></td>								
							</tr>							
							<tr>
								<td style="width:20px">EMail:</td><td style="width:350px"><input type="text" name="email" style="width:350px"</td>
							</tr>							
							<tr>
								<td style="width:20px">Mobile:</td><td style="width:350px"><input type="text" name="mobile" style="width:350px"</td>
							</tr>
						</table>
						<p><input type="submit" value="Request Booking" /></p>
						</form>';					
			
				}
			}
		else
			{
			echo '<p>No Property selected.';
			}
			
	//------------------------------------------------------------------------------------------------------------------------------------------------
	
	?>

		
		<p>Click <a href="./index.php" title="here">here</a> to return to main page.</p>		<!-- Link back to the index page -->
		
		<p><a href="http://validator.w3.org/check?uri=referer"><img
				src="http://www.w3.org/Icons/valid-xhtml10-blue"
				alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a></p>
		
	
	</body>
</html>
