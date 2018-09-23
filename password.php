<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	password.php
	Author:		Mike Blackmore
	Background: Page to allow staff at estate agent to change password and admin users to create new users or change forgotten passwords
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
		<!-- Display header information -->
		<!--<h1>Mike Blackmore - Assignment 4</h1>
		<h2>Home Ltd. Estate Agents</h2>-->
		
	<?php
	include 'dbinfo.php';	// The function dbconnect() used to connect to mblackmoredb database on bimserver2
	$debug=FALSE;

	function ShowPasswordForm($message)
		{
		printf("<p>%s</p>",$message);
		
		echo '<form method="post" action="password.php" >						
				<table border="0">
					<tr>
						<td>Old Password: </td><td><input type="password" value="" name="old_password" /></td>
					</tr>
					<tr>
						<td>New Password: </td><td><input type="password" value="" name="newpassword1" /></td>
					</tr>
					<tr>
						<td>New Password: </td><td><input type="password" value="" name="newpassword2" /></td>
					</tr>
				</table>
				<p><input type="submit" value="Change" /></p>
			  </form>';
		}
		
	//---Main-----------------------------------------------------------------------------------------------------------------------------------------

		session_start();															// Using sessions to retain values between posts
		
		if ( $_SESSION['userid'] )													// Only show info is the user has successfully logged in
			{
			printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
			
			echo '<h3>Change Password Page</h3><hr/>';
		
			if ( $_POST['old_password'] )											// passed old password, must be trying to make change
				{
				if ( $_POST['old_password'] == $_SESSION['password'] )				// old password matches current password
					{
					if ( $debug ) printf("<p>INFO: Running Change Password");
					
					if ( $_POST['newpassword1'] ) 
						{
						if ( $_POST['newpassword1'] == $_POST['newpassword2'] )		// new passwords match
							{
							if ( $debug ) printf("<p>INFO: New passwords match.");  // update the database
							
							dbconnect();											// function to connect to the database
							
																					// Build SQL to update password for logged in user		
							$username=$_SESSION['userid'];
							$password=$_POST['newpassword1'];																											
							$sql=" UPDATE user SET password = '$password' WHERE name = '$username'";
							
							if ( $debug ) printf("<p>INFO: SQL: %s</p>",$sql);

							$result=mysql_query($sql);
				
							if ( $result )
								{
								echo "<p>Update Query Success</p>";
								$_SESSION['password'] = $password;					// Update cached password with new password 
								}
							else
								{
								echo "<p>Update Query Failed</p>";
								}
							}
						else														// new password not verified properly
							{
							if ( $debug ) printf("<p>INFO: New passwords differ");
							ShowPasswordForm("New passwords did not match.");
							}
						}
					else															// User supplied old password but not new password
						{
						if ( $debug ) printf("<p>INFO: new password missing.");
						ShowPasswordForm("Please enter old password, new password and verify password");
						}
					}
				}
			else 																	// Display change password form 
				{
				ShowPasswordForm("To change password, enter old and matching new passwords.");
				}
				
			}
		else
			{
			echo '<h3>Change Password Page</h3><hr/>
			<p>Unauthorised Access. Please Login</p>';
			}
		
	//------------------------------------------------------------------------------------------------------------------------------------------------
	
	?>

		<p>Click <a href="./index.php" title="here">here</a> to return to main page.</p>		<!-- Link back to the index page -->
		
		<p><a href="http://validator.w3.org/check?uri=referer"><img
				src="http://www.w3.org/Icons/valid-xhtml10-blue"
				alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a></p>	
	</body>
</html>
