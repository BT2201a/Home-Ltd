<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	viewings.php
	Author:		Mike Blackmore
	Background: Page to allow staff at process viewing requests
	Created:	17/05/2011 
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
				
		if ( $_SESSION['userid'] )													// Only show info is the user has successfully logged in
			{
			printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
			echo '<h3>Property Viewings</h3><hr />';
			
			if ( $debug ) 
				{
				echo '<p>INFO: POST<br />';
				foreach ($_POST as $key => $value)
					{
					echo "\$$key = $value<br />";
					}
				echo '</p>';
				}
					
			if ( $_POST['id'] )														// Has posted information into the form
				{
				// Delete the viewing request
				dbconnect();
				
				$sql="DELETE FROM viewing where id='" . $_POST['id'] . "'";
				if ( $debug ) echo '<p>' . $sql . '</p>';
				
				$result=mysql_query($sql);											// Insert the property into the table
				
				if ( $result )
					{
					echo '<p>Viewing Request ' . $_POST['id'] . ' has been successfully deleted.</p>';
					}
				}
			
			// Display outstanding viewing requests
				
			dbconnect();

			$sql="SELECT * from viewing ORDER by id ASC";
				
				if ( $debug ) echo '<p>' . $sql . '</p>';
				
				$result=mysql_query($sql);											// Insert the property into the table
				
				if ( $result )
					{
					echo '<table>
							<tr>
								<th style="width:10%">Request No.</th><th>Property</th><th>Name</th><th>EMail</th><th>Telephone</th>
							</tr>';
					
					while ($row = mysql_fetch_row($result))
						{
						echo '<tr>
								<td style="width:10%" align="center">'.$row[0].'</td><td align="center"><form method="post" action="details.php" ><div><input type="submit" name="'. $row[1].'" value="'.$row[1].'" /></div></form></td><td>'.$row[2].'</td><td>'.$row[3].'</td><td>'.$row[4].'</td>
								<td><form method="post" action="viewings.php"><div><input type="hidden" name="id" value="'. $row[0] . '"/><input type="submit" name="Delete" value="Delete"/></div></form></td>
							  </tr>';
						}
					echo '</table>';
					}
				}
			else
				{
				echo '<h3>Property Viewings</h3><hr />
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
