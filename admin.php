<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	admin.php
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
					
	<?php
	include 'dbinfo.php';	// The function dbconnect() used to connect to mblackmoredb database on bimserver2
	
	$debug=FALSE;

	
	//---Main-----------------------------------------------------------------------------------------------------------------------------------------

		session_start();															// Using sessions to retain values between posts
		
		if ( $_SESSION['userid'] )													// Only show info is the user has successfully logged in
			{
			printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
		
			echo '<h3>Reset User Password Page</h3><hr />';
			
			if ( $_POST['username'] )												// If username passed, must be admin user resetting other users password
				{
				if ( $debug ) printf("<p>INFO: username passed - %s </p>", $_POST['username']);
				
				if ( $_POST['newpassword1'] ) 
					{
					if ( $_POST['newpassword1'] == $_POST['newpassword2'] )			// new passwords match
						{
						if ( $debug ) printf("<p>INFO: New passwords match.");		// update the database
							
						dbconnect();											// function to connect to the database
						
						$username=$_POST['username'];
						$password=$_POST['newpassword1'];																											
						$sql=" UPDATE user SET password = '$password' WHERE name = '$username'";
							
						if ( $debug ) printf("<p>INFO: SQL: %s</p>",$sql);

						$result=mysql_query($sql);
				
						if ( $result )
							{
							echo "<p>Update Query Success</p>";
							}
						else
							{
							echo "<p>Update Query Failed</p>";
							}
						}
					else
						{
						echo "<p>Passwords did not match</p>";
						}
					}
				}
							
			if ( $_SESSION['access'] == "admin" )									// double check that user is an admin user before displaying form to reset any user password
				{
				if ( $debug ) printf("<p>INFO: Admin User</p>");
																					// Get list of all users in the database to populate list in form
					dbconnect();													// function to connect to the database

					$sql="SELECT name from user ORDER BY name";						// Build SQL query
					
					if ( $debug ) printf("<p>INFO: SQL: %s</p>",$sql);

					$result=mysql_query($sql);
				
					if ( $result )
						{
						if ( $debug ) echo "<p>Name Lookup Query Success</p>"; // Display Admin Users Reset Password Form
						
						echo '<form method="post" action="admin.php" >						
								<table border="0">
									<tr>
										<td>Select User: </td><td><select name="username">';
							
										while ($row = mysql_fetch_row($result))
											{
											if ( $row[0] != $_SESSION['userid'] )					// remove current user from the list (use main change password form)
												{
												printf("<option value=\"%s\">%s</option>",$row[0], $row[0]);
												}
											}
											
										mysql_free_result($result);
							
						echo '				</select></td>
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
					else
						{
						if ( $debug ) echo "<p>Name Lookup Query Failed</p>";
						}
												}
			else
				{
				echo '<p>Not an <strong>admin</strong> user. No permission to change other user passwords</p>';
				}
			}
		else
			{
			echo '<h3>Reset User Password Page</h3><hr />
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
