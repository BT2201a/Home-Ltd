<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	details.php
	Author:		Mike Blackmore
	Background: Estate Agent Website.
				This script displays the details for a specific property
	
	Created:	24/04/2011 - Initial script created as a place marker
	Modified:	
	
	NOTE: Move final assignment to: http://userid.bimserver2.com/home-ltd/
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
	
	//---Functions
	
	include 'dbinfo.php';	// The function dbconnect() used to connect to mblackmoredb database on bimserver2
	
	
	//---Main-----------------------------------------------------------------------------------------------------------------------------------------

		session_start();														// Using sessions to retain values between posts
		
		if ( $_SESSION['userid'] ) printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
		
		echo '<h3>Property Details</h3><hr />	
		<form method="post" action="index.php" ><p><input type="submit" name="' . $_SESSION['SaleOrLet'] . '" value="Back" /></p></form>';
		
		$debug=FALSE;	// TRUE displays additional messages. set to FALSE to turn off messages
		
					
		if ( $debug )  echo '<p>INFO: POST<br />';
			
			foreach ($_POST as $key => $value)
				{
				if ( $debug )  echo "\$$key = $value<br />";
				$propertyid=$key;
				}
		if ( $debug ) echo '</p>';
						
		if ( $debug ) 
			{
			echo '<p>INFO: SESSION<br />';
			foreach ($_SESSION as $key => $value)
				{
				echo "\$$key = $value<br />";				
				}
			echo '</p>';
			}

			
		if ( $debug ) echo '<p>Property ID:' . $propertyid .'</p>'; 
		if ( $debug ) echo '<p>Type: ' . $_SESSION['SaleOrLet'] .'</p>';
		
		$sql="SELECT * from property WHERE id=" . $propertyid;

		if ( $debug ) echo '<p>SQL:' . $sql .'</p>'; 
		// Redisplay The Original Property Details
		
		dbconnect();
		
		$result=mysql_query($sql);						
		
		if ( $result )
			{
			if ( $debug ) echo "<p>Query Success</p>";
						
			if ( $_SESSION['SaleOrLet'] == "Sale" ) 
				{
				$row = mysql_fetch_row($result);
					
				echo '<table>
							<tr><th align="left" style="width:65%">FOR SALE: ' . htmlspecialchars($row[5], ENT_QUOTES) . ', ' . htmlspecialchars($row[6], ENT_QUOTES) .'</th><th align="right" style="width:35%">Price: &pound;' . number_format($row[1]) . '</th></tr>
							<tr><td align="center" colspan="2"><img src="./properties/' . $row[0] .'.jpg" height="300" width="400" alt="PropertyImage" /></td></tr>
							<tr><td align="left" colspan="2" style="font-size:90%; color:black"><strong>Full Address:</strong><br /> ' . htmlspecialchars($row[5], ENT_QUOTES) . '<br/> ' . htmlspecialchars($row[6], ENT_QUOTES) . '<br/> ' . htmlspecialchars($row[9], ENT_QUOTES) . '<br/><br/>' . htmlspecialchars($row[7], ENT_QUOTES) .'</td></tr>
							<tr><td colspan="2"> <hr /></td></tr>	
							<tr><td align="left"  style="font-size:90%; color:black"><strong>Current Status: </strong> ' . $row[8] . '</td>
							    <td align="right" style="font-size:90%; color:black"><strong>Bedrooms: </strong> ' . $row[5] . '</td></tr>
						</table>';
					
				}
			else
				{
				$row = mysql_fetch_row($result);
					
				echo '<table>
							<tr><th align="left" style="width:65%">TO LET: ' . htmlspecialchars($row[5], ENT_QUOTES) . ', ' . htmlspecialchars($row[6], ENT_QUOTES) .'</th><th align="right" style="width:35%">Price: &pound;' . number_format($row[2]) . ' PCM</th></tr>
							<tr><td align="center" colspan="2"><img src="./properties/' . $row[0] .'.jpg" height="300" width="400" alt="PropertyImage" /></td></tr>
							<tr><td align="left" colspan="2" style="font-size:90%; color:black"><strong>Full Address:</strong><br /> ' . htmlspecialchars($row[5], ENT_QUOTES) . '<br/> ' . htmlspecialchars($row[6], ENT_QUOTES) . '<br/> ' . htmlspecialchars($row[9], ENT_QUOTES) . '<br/><br/>' . htmlspecialchars($row[7], ENT_QUOTES) .'</td></tr>
							<tr><td colspan="2"> <hr /></td></tr>	
							<tr><td align="left"  style="font-size:90%; color:black"><strong>Current Status: </strong> ' . $row[8] . '</td>
							    <td align="right" style="font-size:90%; color:black"><strong>Bedrooms: </strong> ' . $row[4] . '</td></tr>
						</table>';
				}
			echo '<hr />';	
			}
		else
			{
			echo '<p class="error"><strong>ERROR:</strong>Query Failed</p>';
			exit;
			}
			
		mysql_free_result($result);			

			
		// Now display room details				

		$sql="SELECT * from room WHERE propertyid=" . $propertyid .  ' ORDER by id ASC';

		if ( $debug ) echo '<p>SQL:' . $sql .'</p>'; 
		
		$result=mysql_query($sql);						
		
		if ( $result )
			{
			if ( $debug ) echo "<p>Query Success</p>";

			while ($row = mysql_fetch_row($result))
				{
				
				echo '<table>
						<tr>
							<td align="center" valign="middle" rowspan="2" style="height:150px; width:30%" ><img src="./rooms/' . $row[0] .'.jpg" height="150" width="200" alt="Image Unavailable" /></td>
							<td align="left"  style="width:60%; font-size:90%; color:black "><strong>' . htmlspecialchars($row[2], ENT_QUOTES) . ':</strong></td>
						</tr>
						<tr>
	
							<td align="left" valign="top" style="font-size:80%; color:black">' . htmlspecialchars($row[3], ENT_QUOTES) . '</td>
							';
							
							if ( $_SESSION['userid'] )												// Display Admin options
								{
								echo '<td align="right" style="width:10%"> <form method="post" action="modroom.php"><div><input type="hidden" name="Property" value="'. $propertyid . '"/><input type="hidden" name="roomid" value="'. $row[0] . '"/><input type="submit" name="Modify" value="Modify"/></div></form></td>
									  <td align="right" style="width:10%"> <form method="post" action="delroom.php"><div><input type="hidden" name="roomid" value="'. $row[0] . '"/><input type="submit" name="Delete" value="Delete"/></div></form></td>';
								}
				echo '		</tr>
					</table>';
				}
			}
		else
			{
			echo '<p class="error"><strong>ERROR:</strong>Query Failed</p>';
			exit;
			}

		mysql_free_result($result);			
						
		echo '<hr />
			<table>
				<tr>
					<td>
					<form method="post" action="index.php" ><div><input type="submit" name="' . $_SESSION['SaleOrLet'] . '" value="Back" /></div></form>
					</td>';
						
		if ( $_SESSION['userid'] )												// User has already successfully logged in
			{
																				// Dispay controls to delete property, modify property and add additional room to property
			echo '<td style="width:20px">
					<form method="post" action="delprop.php" ><div><input type="hidden" name="Property" value="'. $propertyid . '"/><input type="submit" name="Delete Property" value="Delete Property" /></div></form>
				  </td>
				  <td style="width:20px">
					<form method="post" action="modprop.php" ><div><input type="hidden" name="Property" value="'. $propertyid . '"/><input type="submit" name="Update Property" value="Update Property" /></div></form>
				  </td>				  
				  <td style="width:20px">
					 <form method="post" action="addroom.php" ><div><input type="hidden" name="Property" value="'. $propertyid . '"/><input type="submit" name="Add New Room" value="Add New Room" /></div></form>
				  </td>
				  <td style="width:20px">
					<form method="post" action="addprop.php" ><div><input type="submit" name="Add New Property" value="Add New Property" /></div></form>
				  </td>	
				</tr>
			  </table>';

			}	
		else																	// Link to Login Page
			{
			
			echo '</tr>
				</table>
				<p>Click <a href="./login.php" title="here">here</a> for staff login page.</p>';
			}
			
	//------------------------------------------------------------------------------------------------------------------------------------------------
	
	?>		
		<p><a href="http://validator.w3.org/check?uri=referer"><img
				src="http://www.w3.org/Icons/valid-xhtml10-blue"
				alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a></p>	
	</body>
</html>
