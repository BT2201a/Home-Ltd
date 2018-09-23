<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	addprop.php
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
<!--		<h1>Mike Blackmore - Assignment 4</h1>
		<h2>Home Ltd. Estate Agents</h2> -->
		
	<?php
	include 'dbinfo.php';	// The function dbconnect() used to connect to mblackmoredb database on bimserver2
	
	$debug=TRUE;

	//---Main-----------------------------------------------------------------------------------------------------------------------------------------

	
		session_start();															// Using sessions to retain values between posts
	
		$debug=FALSE;
		
		$uploaddir='/home/mblackmore/mblackmore.bimserver2.com/home-ltd/properties/';	// folder for house images
		
		
		if ( $_SESSION['userid'] )													// Only show info is the user has successfully logged in
			{
			printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
			
			echo '<h3>Add New Property</h3><hr />';
		
			if ( $_POST['SaleOrLet'] )														// Has posted information into the form
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
						
				// Validate fields ?

				$_POST['Address']=addslashes($_POST['Address']);
				$_POST['Details']=addslashes($_POST['Details']);
				$_POST['Postalcode']=addslashes($_POST['Postalcode']);
				$_POST['Vendor']=addslashes($_POST['Vendor']);
				$_POST['Contact']=addslashes($_POST['Contact']);
				
				dbconnect();
				
				$sql="INSERT INTO property VALUES ('', '$_POST[Price]', '$_POST[Rent]', '$_POST[PropertyType]', '$_POST[Beds]', '$_POST[Address]', '$_POST[Town]', '$_POST[Details]', '$_POST[Status]', '$_POST[Postalcode]', '$_POST[SaleOrLet]', '$_POST[Vendor]', '$_POST[Contact]' )";
				
				if ( $debug ) echo '<p>' . $sql . '</p>';
				
				$result=mysql_query($sql);											// Insert the property into the table
								
				if  ( $result )
					{
					if ( $debug ) echo '<p>Query Success</p>' ;
					
					// Get the ID number of the property added
					
					$sql='SELECT id FROM property WHERE address="' . $_POST[Address] . '" AND town="' . $_POST[Town] . '" ORDER BY id DESC LIMIT 0,1';	// Get the last property with this address
					
					$result=mysql_query($sql);
					
					while ($row = mysql_fetch_row($result))
						{
						echo '<p> Property ID: ' . $row[0] . ' successfully added</p>';
						$filename=$row[0].'.jpg';
						
						echo '<form method="post" action="addroom.php" ><div><input type="hidden" name="Property" value="' . $row[0] . '"/></div><p>To add room information to the new property <input type="submit" value="Click Here" /></p></form>';	
						}
					
					
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
							

							echo '<p>Click <a href="./addprop.php" title="here">here</a> to add another property.</p>';
											// Insert link to add room details to the property
						
							}
						}
					}
				else
					{
					die('Invalid Query: ' . mysql_error());							// An error has occured
					}
					
				}
			else																	// Display the form
				{
				echo '<form enctype="multipart/form-data" method="post" action="addprop.php" >
						<div><input type="hidden" name="MAX_FILE_SIZE" value="50000"/></div>
						<table border="1" style="width:640px">
							<tr>
								<td style="width:20px"></td>
								<td colspan="3"><input type="radio" checked="checked" name="SaleOrLet" value="1"/> For Sale <input type="radio" name="SaleOrLet" value="2"/> To Let <input type="radio" name="SaleOrLet" value="3"/>Both</td>
							</tr>
							
							<tr>
								<td style="width:20px">Price:</td><td style="width:145px"><input type="text" value="0" name="Price" style="width:150px"/></td>
								<td style="width:20px">Rent:</td><td style="width:145px"><input type="text" value="0" name="Rent" style="width:150px"/></td>
							</tr>
							<tr>
								<td style="width:20px">Type:</td><td style="width:150px"><select name="PropertyType" style="width:150px"><option value="Detached">Detached</option><option value="Semi-Detached">Semi-Detached</option><option value="Terraced">Terraced</option></select></td>
								<td style="width:20px">Beds:</td><td style="width:150px"><select name="Beds" style="width:50px"><option value="0">0</option><option value="1">1</option><option value="2">2</option><option value="3">3</option><option value="4">4</option><option value="5">5</option><option value="6">6</option></select></td>
							</tr>

							<tr>
								<td></td>
							</tr>
							<tr>
								<td style="width:20px">Street:</td><td colspan="3"><input type="text" name="Address" style="width:450px"/></td>
							</tr>
							<tr>
								<td style="width:20px">Town:  </td><td colspan="3"><input type="text" name="Town" style="width:450px"/></td>
							</tr>
							<tr>
								<td style="width:20px">PostCode:</td><td colspan="3"><input type="text" name="Postalcode" style="width:150px"/></td>
							</tr>
							<tr>
								<td style="width:20px">Details:</td><td colspan="3"><textarea name="Details" cols="57" rows="5"></textarea></td>
							</tr>
							
							<tr>
								<td style="width:20px">Status:</td><td colspan="3"><select name="Status"><option value="For Sale">For Sale</option><option value="Under Offer">Under Offer</option><option value="Sold STC">Sold STC</option><option value="To Let">To Let</option><option value="Let">Let</option></select></td>
							</tr>
							<tr>
								<td style="width:20px">Vendor:</td><td colspan="3"><input type="text" name="Vendor" style="width:150px"/></td>
							</tr>
							<tr>
								<td style="width:20px;font-size:80%">Contact Info:</td><td colspan="3"><input type="text" name="Contact" style="width:150px"/></td>
							</tr>
							<tr>
								<td style="width:20px">Photograph:</td><td colspan="3" style="width:300px"><input type="file" name="file" id="file"/></td>
							</tr>
												
						</table>
						<p><input type="submit" value="Add Property" /></p>
						</form><hr />';
				}	
			}
		else
			{
			echo '<h3>Add New Property</h3><hr />
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
