<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	modprop.php
	Author:		Mike Blackmore
	Background: Page to allow staff at estate agent to change the property details
	Created:	16/05/2011 
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
		$valid=TRUE;
		
		$uploaddir='/home/mblackmore/mblackmore.bimserver2.com/home-ltd/properties/';	// folder for house images
		
		
		if ( $_SESSION['userid'] )													// Only show info is the user has successfully logged in
			{
			printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
			echo '<h3>Modify Property</h3><hr />';
			
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
				
				// Validate fields ?
				
				$_POST['Address']=addslashes($_POST['Address']);
				$_POST['Details']=addslashes($_POST['Details']);
				$_POST['Postalcode']=addslashes($_POST['Postalcode']);
				
				dbconnect();

				$sql="UPDATE property SET `price`='" . $_POST[Price] . "', `rent`='" . $_POST[Rent] . "', `type`='" . $_POST[PropertyType] . "', `beds`='" . $_POST[Beds] . "', `address`='" . $_POST[Address] . "', `town`='" . $_POST[Town] . "', `description`='" . $_POST[Details] . "', `status`='" . $_POST[Status] . "', `postalcode`='" . $_POST[Postalcode] ."', `sale_or_let`='" . $_POST[SaleOrLet] ."', `vendor`='" . $_POST[Vendor] ."', `phone`='" . $_POST[Contact] .   "' WHERE id='" . $_POST['id'] . "'";
				
				if ( $debug ) echo '<p>' . $sql . '</p>';
				
				$result=mysql_query($sql);											// Insert the property into the table
				
				if ( $result )
					{
					echo '<p>Update Successful</p>';
					$_POST['Property']=$_POST['id'];								// Quick fix to allow form to be displayed AFTER update
					$filename=$_POST['id'].'.jpg';
							
					echo '<form method="post" action="addroom.php" ><div><input type="hidden" name="Property" value="' . $_POST['id'] . '"/></div><p>To add room information to the property <input type="submit" value="Click Here" /></p></form>';	
							
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
													// Insert link to add room details to the property ?
							}
						}
					}
				else
					{
					die('Invalid Query: ' . mysql_error());							// An error has occured
					}					
				}

				// Always re-display details

				if ( $_POST['Property'] )
					{
					if ( $debug ) echo '<p>Property ID: ' . $_POST['Property'] . '</p>';

					$sql='Select * from property WHERE id="' . $_POST['Property'] .'"';	
					
					dbconnect();
					
					$result=mysql_query($sql);
				
				// Get current details  from property table
																					
					while ($row = mysql_fetch_row($result))
						{
						$id=$row[0];
						$price=$row[1];
						$rent=$row[2];
						$type=$row[3];
						$beds=$row[4];
						$address=$row[5];
						$town=$row[6];
						$description=$row[7];
						$status=$row[8];
						$postalcode=$row[9];
						$sale_or_let=$row[10];		// if 1 for sale, 2= to let, 3 is both
						$vendor=$row[11];
						$contact=$row[12];		
						}
					}
				// Configure default for radio button and the list fields
				// Convert $sale_or_let value from numeric to the html string required in the form
				
					if ( $debug ) echo '<p> INFO: SoL: ' . $sale_or_let . '</p>';
					if      ( $sale_or_let == "1" ) $radio_sale=' checked="checked" ';
					else if ( $sale_or_let == "2" ) $radio_let=' checked="checked" ';
					else if ( $sale_or_let == "3" ) $radio_both=' checked="checked" ';
					
				// And do the same for the $status	where default should be selected="selected"
				
					if ( $debug ) echo '<p> INFO: Status: ' . $status . '</p>';
					if      ( $status == "For Sale" )    $status_forsale=' selected="selected" ';
					else if ( $status == "Under Offer" ) $status_underof=' selected="selected" ';
					else if ( $status == "Sold STC" )    $status_soldstc=' selected="selected" ';
					else if ( $status == "To Let" )      $status_tolet=' selected="selected" ';
					else if ( $status == "Let" )         $status_let=' selected="selected" ';
					else echo '<p>Error: Could not set status</p>';

				// And for the type of property
				
					if ( $debug ) echo '<p> INFO: Type: ' . $type . '</p>';
					if      ( $type == "Detached" )      $type_detached=' selected="selected" ';
					else if ( $type == "Semi-Detached" ) $type_semi_det=' selected="selected" ';
					else if ( $type == "Terraced" )      $type_terraced=' selected="selected" ';
					else echo '<p>Error: Could not set type</p>';
				
				// And the number of beds
				
					if ( $debug ) echo '<p> INFO: Beds: ' . $beds . '</p>';
					if      ( $beds == "0" )      $beds0=' selected="selected" ';
					else if ( $beds == "1" )      $beds1=' selected="selected" ';
					else if ( $beds == "2" )      $beds2=' selected="selected" ';
					else if ( $beds == "3" )      $beds3=' selected="selected" ';
					else if ( $beds == "4" )      $beds4=' selected="selected" ';
					else if ( $beds == "5" )      $beds5=' selected="selected" ';
					else if ( $beds == "6" )      $beds6=' selected="selected" ';
					else echo '<p>Error: Could not set beds</p>';
					
				// Display the form
												
				echo '<form enctype="multipart/form-data" method="post" action="modprop.php" >
						<div><input type="hidden" name="MAX_FILE_SIZE" value="50000"/></div>
						<div><input type="hidden" name="id" value="' . $id .'"/></div>
						<table border="1" style="width:640px">
							<tr>
								<td style="width:20px"></td>
								<td colspan="3"><input type="radio" ' . $radio_sale . ' name="SaleOrLet" value="1"/> For Sale <input type="radio" ' . $radio_let . 'name="SaleOrLet" value="2"/> To Let <input type="radio" ' . $radio_both . 'name="SaleOrLet" value="3"/>Both</td>
							</tr>
							
							<tr>
								<td style="width:20px">Price:</td><td style="width:145px"><input type="text" value="' . $price . '" name="Price" style="width:150px"/></td>
								<td style="width:20px">Rent:</td><td style="width:145px"><input type="text" value="' . $rent . '" name="Rent" style="width:150px"/></td>
							</tr>
							<tr>
								<td style="width:20px">Type:</td><td style="width:150px"><select name="PropertyType" style="width:150px"><option value="Detached" ' . $type_detached . ' >Detached</option><option value="Semi-Detached" ' . $type_semi_det . ' >Semi-Detached</option><option value="Terraced" ' .  $type_terraced . ' >Terraced</option></select></td>
								<td style="width:20px">Beds:</td><td style="width:150px"><select name="Beds" style="width:50px"><option value="0" ' . $beds0 . ' >0</option><option value="1" ' . $beds1 . ' >1</option><option value="2" ' . $beds2 . ' >2</option><option value="3" ' . $beds3 . ' >3</option><option value="4" ' . $beds4 . ' >4</option><option value="5" ' . $beds5 . ' >5</option><option value="6" ' . $beds6 . ' >6</option></select></td>
							</tr>

							<tr>
								<td></td>
							</tr>
							<tr>
								<td style="width:20px">Street:</td><td colspan="3"><input type="text" value="' . $address . '" name="Address" style="width:450px"/></td>
							</tr>
							<tr>
								<td style="width:20px">Town:  </td><td colspan="3"><input type="text" value="' . $town . '" name="Town" style="width:450px"/></td>
							</tr>
							<tr>
								<td style="width:20px">PostCode:</td><td colspan="3"><input type="text" value="' . $postalcode . '" name="Postalcode" style="width:150px"/></td>
							</tr>
							<tr>
								<td style="width:20px">Details:</td><td colspan="3"><textarea name="Details" cols="57" rows="5">' . $description . '</textarea></td>
							</tr>
							
							<tr>
								<td style="width:20px">Status:</td><td colspan="3"><select name="Status"><option value="For Sale" '. $status_forsale .' >For Sale</option><option value="Under Offer" ' . $status_underof . ' >Under Offer</option><option value="Sold STC" ' . $status_soldstc . ' >Sold STC</option><option value="To Let" ' . $status_tolet . ' >To Let</option><option value="Let" ' .  $status_let . ' >Let</option></select></td>
							</tr>
							<tr>
								<td style="width:20px">Vendor:</td><td colspan="3"><input type="text" value="' . $vendor . '" name="Vendor" style="width:150px"/></td>
							</tr>
							<tr>
								<td style="width:20px;font-size:80%">Contact Info:</td><td colspan="3"><input type="text" value="' . $contact . '" name="Contact" style="width:150px"/></td>
							</tr>
							<tr>
								<td style="width:20px">Photograph:</td><td colspan="3" style="width:300px"><input type="file" name="file" id="file"/></td>
							</tr>
												
						</table>
						<p><input type="submit" value="Update Property" /></p>
						</form><hr />';
					
			}
		else
			{
			echo '<h3>Modify Property</h3><hr />
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
