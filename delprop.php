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
	
		$debug=FALSE;

		if ( $debug ) 
					{
					echo '<p>INFO: POST<br />';
					foreach ($_POST as $key => $value)
					{
					echo "\$$key = $value<br />";
					}
					echo '</p>';
					}
					
		$roomdir='/home/mblackmore/mblackmore.bimserver2.com/home-ltd/rooms/';			// folder for room images
		$homedir='/home/mblackmore/mblackmore.bimserver2.com/home-ltd/properties/';		// folder for house images
		
		if ( $_SESSION['userid'] )
			{
			printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
			
			echo '<h3>Delete Property Information</h3><hr />';
			
			if ( $_POST['Confirm'] )													// Confirmed deletion OK
				{
																						// First delete any rooms and room photos associated to the property				
																						
				if ( $debug ) echo '<p>Property to delete is ' .$_POST['Confirm'].'</p>';				
				$sql='SELECT id from room WHERE propertyid="' . $_POST['Confirm'].'"';				
				if ( $debug ) echo '<p>INFO: SELECT ROOMS SQL ' .$sql.'</p>';
				
				dbconnect();
				$result=mysql_query($sql);
				
				if ( $result )
					{
					while ( $row = mysql_fetch_row($result) )
						{
						echo '<p>Deleting Room ' . $row[0] . '...';
												
						$sql="DELETE FROM room where id='" . $row[0] . "'";

						if ( $debug ) echo '<p>INFO ROOM DEL SQL ' . $sql . '</p>';
			
						dbconnect();
				
						$result2=mysql_query($sql);
				
						if ( $result2 )
							{
							echo 'Room successfully deleted</p>';
							// now delete the photo
							$filename=$roomdir.$row[0].'.jpg';						// name of photo
							
							if ( $debug ) echo '<p> Delete photo'.$filename.'</p>';
							
							if ( file_exists($filename) )							// Delete the file if it exists
								{
								echo '<p>Deleting room photo: ' . $filename . '...';
								$result3=unlink($filename);
								if ( $result3 ) echo 'Photo also deleted</p>';
								else		    echo 'Failed to delete photo</p>';
								}					 
							}
						else echo 'Failed to delete room.</p>';
						}
					}
				else if ( $debug ) echo '<p>No rooms associated with property</p>';
				
				// All rooms should now be deleted	
							
				// Now delete the property
				
				$sql='DELETE FROM property where id="'. $_POST['Confirm'].'"';
				
				if ( $debug ) echo '<p>INFO: DELETE ROOM SQL ' .$sql.'</p>';
				
				dbconnect();
				
				$result=mysql_query($sql);
				
				if ( $result ) 
					{
					echo '<p>Property successfully deleted</p>';
					
					$filename=$homedir.$_POST['Confirm'].'.jpg';						// name of photo
					
					if ( $debug ) echo '<p> Delete photo'.$filename.'</p>';
					
					if ( file_exists($filename) )										// Delete the file if it exists
						{
						echo '<p>Deleting property photo: ' . $filename . '...';
						$result2=unlink($filename);
						if ( $result2 ) echo 'Photo also deleted</p>';
						else		   echo 'Failed to delete photo</p>';
						}					 									
					}
				}
			else if ( $_POST['Property'] )
				{
				// Clear any unprocessed viewing requests relating to the property
				
				$sql="SELECT COUNT(id) from viewing WHERE propertyid='" . $_POST['Property'] . "' ORDER by id ASC";
				
				if ( $debug ) echo '<p>INFO: SQL ' .$sql.'</p>';
				dbconnect();
				
				$result=mysql_query($sql);
				
				if ( $result ) $row = mysql_fetch_row($result);
				
				if ( $row[0] )					
					{
					$sql="SELECT * from viewing WHERE propertyid='" . $_POST['Property'] . "' ORDER by id ASC";
				
					if ( $debug ) echo '<p>INFO: SQL ' .$sql.'</p>';
					dbconnect();
				
					$result=mysql_query($sql);
					
					echo '<p>Please clear the following outanding viewing requests before deleting this property</p>
					<table>
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
				else
					{
					
					$sql='SELECT address,town,postalcode from property WHERE id="' . $_POST['Property'] .'"';	// Get address line for property
				
					if ( $debug ) echo '<p>INFO: SQL ' .$sql.'</p>';
					
					dbconnect();
				
					$result=mysql_query($sql);
				
					if ( $result )
						{
						while ( $row = mysql_fetch_row($result) ) echo '
						<p>Are you sure you want to delete the property: ' . $row[0].','.$row[1].','.$row[2].'?</p>
						<form method="post" action="delprop.php"><input type="hidden" name="Confirm" value="'.$_POST['Property'].'"/><input type="submit" value="Delete" />';
						}					
					}
				}
			}
		else
			{
			echo '<h3>Delete Property Information</h3><hr />
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
