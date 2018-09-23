<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	login.php
	Author:		Mike Blackmore
	Background: Page to allow staff at estate agent to login to the website to add properties
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
	
	function DisplayLogonForm()
		{
		// Display Login form using a table to ensure formatted OK
		
		echo '<h3>Login Page</h3><hr />
		
		<form method="post" action="login.php" >
					<table border="0">
						<tr>
							<td>Username:</td> <td><input type="text" value="Enter name" name="username" /></td>
						</tr>
						<tr>
							<td>Password:</td> <td><input type="password" value="" name="password" /></td>
						</tr>
					</table>
					<p><input type="submit" value="Login" /></p>
			  </form>';		
		}
		
	function DisplayMenu()
		{
		// Display options for staff members
			echo '<h3>Admin Page</h3><hr />
			<p>Click <a href="./addprop.php" title="here">here</a> to add a new property.</p>
				  <p>Click <a href="./password.php" title="here">here</a> to change own password.</p>
				  <p>Click <a href="./viewings.php" title="here">here</a> to process viewing requests.</p>';
						
			if ( $_SESSION['access'] == "admin" ) echo '<p>Click <a href="./admin.php" title="here">here</a> to change users password.</p>';
						
			echo '<p>Click <a href="./logout.php" title="here">here</a> to logout.</p>';
		}
				
	$debug=FALSE;
	
	//---Main-----------------------------------------------------------------------------------------------------------------------------------------

		session_start();														// Using sessions to retain values between posts
		
		if ( $_SESSION['userid'] )												// User has already successfully logged in
			{
			printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
			
			DisplayMenu();
			}
		else if ( $_POST['username'] )											// User passed userid to authenticate
			{
			if ( $debug ) printf("<p>INFO: username: %s</p>", $_POST['username']);
			
			if ( $_POST['password'] )											// Password also submitted, check against the DB
				{
				if ( $debug ) printf("<p>INFO: password: %s</p>", $_POST['password']);
				
				// Check submitted details against user table in the database
				
				$username=$_POST['username'];
				$password=$_POST['password'];
				
				if ( $debug ) printf("<p>INFO: \$username: %s</p>",$username);
				
				dbconnect();				
						
				// Lookup row using passed name and password
				
				$sql="SELECT * FROM user WHERE name = '$username' AND password = '$password' ";
				
				$result=mysql_query($sql);
				
				if ( $result )
					{
					if ( $debug ) echo "<p>Query Success</p>";
					
					if ( $row = mysql_fetch_row($result) )								// If a row has been returned, the username and password combination was valid  
						{
						if ( $debug ) printf("<p>%s %s %s</p>",$row[0], $row[1], $row[2]);
																		
						$_SESSION['userid']=$row[0];									// set the userid to the successfully validated username
						$_SESSION['password']=$row[1];									// store current password for use on change password form
						$_SESSION['access']=$row[2];									// set the access level to the value returned from the database (admin or standard)
						
						printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
						
						DisplayMenu();
						
						}
					else
						{
						echo '<p>Invalid username or password please try again.</p>';	// No rows returned, so username and password combination was invalid
						
						DisplayLogonForm();
						
						}
					
					mysql_free_result($result);
					}	
				}
			else																// Password omitted, redisplay form and an error message
				{
				echo '<p>Details omitted, please enter a valid <strong>username</strong> and valid <strong>password</strong></p>';
				
				DisplayLogonForm();
							  
				}
			}
		else																	// Display login form for the first time
			{
			echo '<p>Please enter a valid <strong>username</strong> and <strong>password</strong></p>';
			
			DisplayLogonForm();
			}
	
	//------------------------------------------------------------------------------------------------------------------------------------------------
	
	?>

		<p>Click <a href="./index.php" title="here">here</a> to return to main page.</p>		<!-- Link back to the index page -->
		
		<p><a href="http://validator.w3.org/check?uri=referer"><img
				src="http://www.w3.org/Icons/valid-xhtml10-blue"
				alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a></p>	
	</body>
</html>
