<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	addroom.php
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
		<!-- Display header information -->
<!--		<h1>Mike Blackmore - Assignment 4</h1>
		<h2>Home Ltd. Estate Agents</h2> -->
		
	<?php
	
	include 'dbinfo.php';	// The function dbconnect() used to connect to mblackmoredb database on bimserver2
	

	//---Main-----------------------------------------------------------------------------------------------------------------------------------------

		session_start();															// Using sessions to retain values between posts
	
		$debug=FALSE;
		$valid=TRUE;
		
		$uploaddir='/home/mblackmore/mblackmore.bimserver2.com/home-ltd/rooms/';	// folder for room images
		
		
		if ( $_SESSION['userid'] )													// Only show info is the user has successfully logged in
			{
			printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
			
			echo '<h3>Add Room Information</h3><hr />';
							
			if ( $debug ) 
					{
					echo '<p>INFO: POST<br />';
					foreach ($_POST as $key => $value)
					{
					echo "\$$key = $value<br />";
					}
					echo '</p>';
					}
					
			if ( $_POST['name'] )														// Has posted information into the form
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
				
				

				if ( $_POST['Property'] )
					{
					if ( $debug ) echo '<p>Property ID: ' . $_POST['Property'] . '</p>';					
					}
				else
					{
					echo '<p>No property to link room to</p>';
					$valid=FALSE;
					}
				// Validate fields ?
				
				$_POST['name']=addslashes($_POST['name']);
				$_POST['details']=addslashes($_POST['details']);
				
				dbconnect();
				
				$sql="INSERT INTO room VALUES ('', '" . $_POST['Property'] . "', '" . $_POST['name'] . "', '" . $_POST['details'] . "')";
				
				if ( $debug ) echo '<p>' . $sql . '</p>';
				
				$result=mysql_query($sql);											// Insert the property into the table
								
				if  ( $result )
					{
					if ( $debug ) echo '<p>Query Success</p>' ;
					
					// Get the ID number of the property added
					
					$sql='SELECT id FROM room WHERE propertyid="' . $_POST[Property] . '" AND name="' . $_POST[name] . '" ORDER BY id DESC LIMIT 0,1';	// Get the last room for this property
					
					dbconnect();
					
					$result=mysql_query($sql);
					
					while ($row = mysql_fetch_row($result))
						{
						echo '<p> Room ID: ' . $row[0] . ' successfully added</p>';
						$filename=$row[0].'.jpg';
						
						//echo '<form method="post" action="addroom.php" ><input type="hidden" name="Property" value="' . $row[0] . '"/><p>To add room information to the new property <input type="submit" value="Click Here" /></p></form>';	
						}
					
					// If a photo filename was entered, upload the file to the properties folder with name equal to the record ID
					
					if ( $_FILES["file"]["name"] )						// If a photo filename was entered, upload the file to the properties folder with name equal to the record ID
						{
						
						if ($_FILES["file"]["error"] > 0)
							{
							echo "Error: " . $_FILES["file"]["error"] . "<br />";
							}
						else
							{
							if ( $debug ) echo '<p>Upload: ' . $_FILES["file"]["name"] . '<br />Type: ' . $_FILES["file"]["type"] . '<br />Size: ' . ($_FILES["file"]["size"] / 1024) . ' Kb<br />Stored in: ' . $_FILES["file"]["tmp_name"] .'</p>';
							
							$uploadfile = $uploaddir . $filename;
							
							if ( $debug ) echo '<p> INFO: filename = ' . $uploadfile . '</p>';
							
							if ( move_uploaded_file($_FILES["file"]["tmp_name"],$uploadfile) )
								{
								echo '<p>Photo ' . $uploadfile . ' has been successfully uploaded</p>';
								}
							}
						}
					}
				else
					{
					die('Invalid Query: ' . mysql_error());							// An error has occured
					}
					
				}
				
				if ( $_POST['Property'] )
					{
					if ( $debug ) echo '<p>Property ID: ' . $_POST['Property'] . '</p>';
					
					$sql='Select address,town,postalcode from property WHERE id="' . $_POST['Property'] .'"';	// Get address line for property
					
					dbconnect();
					
					$result=mysql_query($sql);
					
					while ($row = mysql_fetch_row($result))
						{
						echo '<p>Property: ' . $row[0] . ', ' . $row[1] . ', ' . $row[2] . '</p>';			// Display the address
						}
					}
																					// Always Display the form
				
				echo '<form enctype="multipart/form-data" method="post" action="addroom.php" >
						<div><input type="hidden" name="MAX_FILE_SIZE" value="50000"/></div>
						<div><input type="hidden" name="Property" value="' . $_POST['Property'] . '"/></div>
						<table border="1" style="width:640px">
								
							<tr>
								<td style="width:20px">Name:</td><td style="width:145px"><input type="text" name="name" style="width:150px"/></td>								
							</tr>							
							<tr>
								<td style="width:20px">Details:</td><td colspan="3"><textarea name="details" cols="57" rows="5"></textarea></td>
							</tr>							
							<tr>
								<td style="width:20px">Photograph:</td><td colspan="3" style="width:300px"><input type="file" name="file" id="file"/></td>
							</tr>
												
						</table>
						<p><input type="submit" value="Add Room" /></p>
						</form><form method="post" action="details.php"><div><input type="hidden" name="' . $_POST['Property'] . '" value="' . $_POST['Property'] . '"><input type="submit" value="Property Details" /></form><hr />';
					
			}
		else
			{
			echo '<h3>Add Room Information</h3><hr />
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
