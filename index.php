<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
	
<!--
	Filename:	index.php
	Author:		Mike Blackmore
	Background: Estate Agent Website.
	
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
	
	include 'dbinfo.php';	// The function dbconnect() used to connect to mblackmoredb database on bimserver2

	//---Main-----------------------------------------------------------------------------------------------------------------------------------------

		session_start();	// Using sessions to retain values between posts
		
		$debug=FALSE;		// TRUE displays additional messages. set to FALSE to turn off messages
		
		
		echo '<p><br /></p><hr /><h1>Home Ltd - Estate Agents</h1><hr />';	
		
		$_SESSION['StartRecord']=(int)$_SESSION['StartRecord'];						// The record number to begin search from when splitting results across pages
		
		if ( $_POST['SaleOrLet'] == "Let" )  	$radio_let='checked="checked"';		// Make sure the radio button on search options defaults to last type of search		
		else									$radio_sale='checked="checked"';
		
		// Display form to allow user to filter properties
	
			echo '<form method="post" action="index.php" >
					<table border="1" style="width:600px">
						<tr>
							<td colspan="2"><strong>Property Search:</strong></td>
							<td colspan="2"><input type="radio" ' . $radio_sale . ' name="SaleOrLet" value="Sale"/> For Sale <input type="radio" ' . $radio_let . ' name="SaleOrLet" value="Let"/> To Let</td>
						</tr>	
						<tr>
							<td style="width:10px">Type: </td><td style="width:200px"><select name="PropertyType" style="width:200px"><option value="All">All</option><option value="Detached">Detached</option><option value="Semi-Detached">Semi-Detached</option><option value="Terraced">Terraced</option></select></td>
							<td align="left" style="width:10px">Beds: </td><td style="width:10px"><select name="Beds" style="width:120px"><option value="NA">Not Specified</option><option value="1">1 or more</option><option value="2">2 or more</option><option value="3">3 or more</option><option value="4">4 or more</option></select></td>
						</tr>
						<tr>
							<td style="width:10px">Town: </td><td><select name="Town" style="width:200px"><option value="NA">Not Specified</option>';
			
								dbconnect();
							
								// Get a list of valid town for the search form based on db
								
								$sql="SELECT DISTINCT town FROM `property`";
							
								$result=mysql_query($sql);
							
								if ( $result )
									{
									while ($row = mysql_fetch_row($result))
										{
										echo '<option value="' . $row[0] .'">' . $row[0] .'</option>';
										}					   
									}
									
								mysql_free_result($result);
				
				echo '</select></td>
							<td style="font-size:80%">Minimum Price:</td><td><select name="MinPrice" style="width:120px"><option value="0">No Minimum</option><option value="100000">&pound;100,000</option><option value="150000">&pound;150,000</option><option value="200000">&pound;200,000</option><option value="250000">&pound;250,000</option><option value="0">No Minimum</option></select></td> 
						</tr>
						<tr>
							<td style="width:10px">Sort: </td><td style="width:200px"><select name="Sort" style="width:200px"><option value="Price_Asc">Price (ascending)</option><option value="Price_Desc">Price (decending)</option><option value="Oldest">Newness (oldest first)</option><option value="Newest">Newness (newest first)</option><option value="Beds">Number bedrooms</option></select></td>
							<td style="font-size:80%">Maximum Price:</td><td> <select name="MaxPrice" style="width:120px"><option value="0">No Maximum</option><option value="300000">&pound;300,000</option><option value="400000">&pound;400,000</option><option value="500000">&pound;500,000</option><option value="0">No Maximum</option></select></td>
						</tr>

					</table>
					<div><input type="hidden" name="Page" value="0"/></div>
					<p><input type="submit" value="Search" /></p>
					</form><hr />';

		if ( $debug ) 
			{
			echo '<p>INFO: POST<br />';
			foreach ($_POST as $key => $value)
				{
				echo "\$$key = $value<br />";
				}
			echo '</p>';
			}
			
		$filter=FALSE;
		$string=" ";
		
		if ( $_POST['Page'] > 0 )
			{
			// Keep existing $sql query, just amend the starting record
			
			$_SESSION['StartRecord']=($_POST['Page']-1)*3;			
			$_SESSION['sql']=$_SESSION['base'].$_SESSION['StartRecord'].",3";
			
			if ( $debug ) echo '<p>SQL: ' . $_SESSION['sql'] . "</p>";
			}
		else
			{
			
			$_SESSION['StartRecord']=0;
			
			// Build SQL Query based on any filters POSTED
			
			if ( $_POST['SaleOrLet'] )
				{
				$filter=TRUE;
				if ( $_POST['SaleOrLet'] == "Sale" )
					{
					$text=" For Sale ";
					$string=" WHERE sale_or_let!=2 ";
					$_SESSION['SaleOrLet']="Sale";				
					}
				else if ( $_POST['SaleOrLet'] == "Let" )
					{
					$text=" To Let ";
					$string=" WHERE sale_or_let!=1 ";
					$_SESSION['SaleOrLet']="Let";
					}
				}
			else				// Fix to resolve bug clicking on index.php link after property update - should update the link and use $GET...
				{
				$filter=TRUE;
				$text=" For Sale ";
				$string=" WHERE sale_or_let!=2 ";
				$_SESSION['SaleOrLet']="Sale";
				}
			
			
			if ( $_SESSION['SaleOrLet'] )
				{
				$filter=TRUE;
				if      ( $_SESSION['SaleOrLet'] == "Sale" )
					{
					$text="For Sale ";
					$string=" WHERE sale_or_let!=2 ";
					$_SESSION['SaleOrLet']="Sale";				
					}
				else if ( $_SESSION['SaleOrLet'] == "Let" )
					{
					$text=" To Let ";
					$string=" WHERE sale_or_let!=1 ";
					$_SESSION['SaleOrLet']="Let";					
					}
				}
				
			if ( $_POST['PropertyType'] )
				{
				if ( $_POST['PropertyType'] != "All" )
					{
					$string=$string . ' AND type="' . $_POST['PropertyType'] .'"';
					if ( $debug )  printf("<p>INFO: STRING %s</p>", $string);
					}
				}
				
			if ( $_POST['Beds'] )	
				{
				if ( $_POST['Beds'] != "NA" )
					{
					if ( $filter )
						{
						$string=$string . ' AND beds>=' . $_POST['Beds'];
						}
					else
						{
						$filter=TRUE;
						$string=' WHERE beds>=' . $_POST['Beds'];
						}
					}
				}
				
			if ( $_POST['Town'] )	
				{
				if ( $_POST['Town'] != "NA" )
					{
					$town="in " . $_POST['Town'];
					if ( $filter )
						{
						$string=$string . ' AND town="' . $_POST['Town'] .'"';
						}
					else
						{
						$filter=TRUE;
						$string=' WHERE town="' . $_POST['Town'] .'"';
						}
					}
				}
		
			if ( $_POST['MinPrice'] )	
				{
				if ( $_POST['MinPrice'] != "0" )
					{
					if ( $filter )
						{
						$string=$string . ' AND price>=' . $_POST['MinPrice'];
						if ( $debug )  printf("<p>INFO: STRING %s</p>", $string);
						}
					else
						{
						$filter=TRUE;
						$string=' WHERE price>=' . $_POST['MinPrice'];
						if ( $debug )  printf("<p>INFO: STRING %s</p>", $string);
						}
					}
				}
				
			if ( $_POST['MaxPrice'] )	
				{
				if ( $_POST['MaxPrice'] != "0" )
					{
					if ( $filter )
						{
						$string=$string . ' AND price<=' . $_POST['MaxPrice'];
						if ( $debug )  printf("<p>INFO: STRING %s</p>", $string);
						}
					else
						{
						$filter=TRUE;
						$string=' WHERE price<=' . $_POST['MaxPrice'];
						if ( $debug )  printf("<p>INFO: STRING %s</p>", $string);
						}
					}
				}			
				
			if ( $_POST['Sort'] )
				{
				if 		( $_POST['Sort'] == "Price_Asc" )   $order=" ORDER by price DESC LIMIT "; 
				else if ( $_POST['Sort'] == "Price_Desc" )  $order=" ORDER by price ASC LIMIT "; 
				else if ( $_POST['Sort'] == "Oldest" )		$order=" ORDER by id DESC LIMIT "; 
				else if ( $_POST['Sort'] == "Newest" )		$order=" ORDER by id ASC LIMIT "; 
				else if ( $_POST['Sort'] == "Beds" )		$order=" ORDER by beds DESC LIMIT "; 		
				}
			else $order=" ORDER by id DESC LIMIT ";	
			
			//		
			// Add extra SQL to exclude sold or let properties?
			//
			//
			
			if ( !$filter )
				{
				// Query to return 3 properties from the property table from requested record number
				$text="For Sale";
				$_SESSION['base']="SELECT * FROM property WHERE sale_or_let!=2 "; //ORDER BY id DESC LIMIT ";
				$_SESSION['sql']=$_SESSION['base'].$order.$_SESSION['StartRecord'] .",3";
				$_SESSION['count']="SELECT COUNT(id) FROM property WHERE sale_or_let!=2";
				$_SESSION['SaleOrLet']="Sale";				
				}
			else
				{
				$text=$text . $town;
				$_SESSION['base']="SELECT * FROM property " . $string . $order; // ORDER BY id DESC LIMIT ";
				$_SESSION['sql']=$_SESSION['base'].$_SESSION['StartRecord'].",3";
				$_SESSION['count']="SELECT COUNT(id) FROM property " . $string;
				}
			}

			
		if ( $debug ) printf("<p>INFO: SQL %s</p>", $_SESSION['sql']);
		if ( $debug ) printf("<p>INFO: SQL %s</p>", $_SESSION['count']);
		
		// Display links to get records from different start

		dbconnect();
				
		$result=mysql_query($_SESSION['count']);
		
		if ( $result )
			{
			$records=mysql_fetch_row($result);
			if ( $records[0] == 1 ) echo "<h3>There is " . $records[0] . " property " . $text . "</h3>";
			else					echo "<h3>There are " . $records[0] . " properties " . $text . "</h3>";
			}
		
		if ( $_POST['Page'] ) echo '<p>Page ' . $_POST['Page'] . '</p>';
		else echo '<p>Page 1</p>';
		
		
		dbconnect();
		
		$result=mysql_query($_SESSION['sql']);
		
		if ( $result )
		{
			if ( $debug ) echo "<p>Query Success</p>";
						
			if ( $_POST['SaleOrLet'] != "Let" ) 
				{
				while ($row = mysql_fetch_row($result))
					{
					echo '<table>
			<tr><th align="left" style="width:80%">' . htmlspecialchars($row[5], ENT_QUOTES) . ', ' . htmlspecialchars($row[6], ENT_QUOTES) .' ' . htmlspecialchars($row[9], ENT_QUOTES) . '</th><th align="right" style="width:20%">&pound;' . number_format($row[1]) . '</th></tr>
		</table>		
		<table>
			<tr>
				<td align="center" valign="middle" rowspan="3" style="height:150px; width:30%" ><div id="popup' . $row[0] . '">
				<a class="thumbnail" href="#"><img src="./properties/' . $row[0] . '.jpg" alt="Awaiting Photo" style="width:160px;height:140px" /><span><img src="./properties/' . $row[0] . '.jpg" alt="Awaiting Photo" /></span></a></div></td>
				<td align="left"  style="width:35%; font-size:90%; color:black">' . $row[3] . ' Property</td>
				<td align="right" style="width:35%; font-size:90%; color:black">' . $row[4] . ' Bedrooms</td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="2" style="font-size:80%; color:black">' . htmlspecialchars($row[7], ENT_QUOTES) . '</td>
			</tr>
			<tr>
				<td align="left"  style="width:30% ; font-size:80%; color:blue">' . $row[8] .'</td>
				<td align="right" style="width:30% ; font-size:80%; color:blue"><form method="post" action="details.php" ><div><input type="submit" name="' . $row[0] . '" value="View Details" /></div></form></td>
			</tr>
		</table>';
					}
				}
			else
				{
				while ($row = mysql_fetch_row($result))
					{
					echo '<table>
			<tr><th align="left" style="width:80%">' . htmlspecialchars($row[5], ENT_QUOTES) . ', ' . htmlspecialchars($row[6], ENT_QUOTES) . ' ' . htmlspecialchars($row[9], ENT_QUOTES) .'</th><th align="right" style="width:20%">&pound;' . number_format($row[2]) . ' pcm</th></tr>
		</table>		
		<table>
			<tr>
				<td align="center" valign="middle" rowspan="3" style="height:150px; width:30%" ><img src="./properties/' . $row[0] .'.jpg" height="150" width="200"
			alt="Awaiting Photo" /></td>
				<td align="left"  style="width:35%; font-size:90%; color:black">' . $row[3] . ' Property</td>
				<td align="right" style="width:35%; font-size:90%; color:black">' . $row[4] . ' Bedrooms</td>
			</tr>
			<tr>
				<td align="left" valign="top" colspan="2" style="font-size:80%; color:black">' . htmlspecialchars($row[7], ENT_QUOTES) . '</td>
			</tr>
			<tr>
				<td align="left"  style="width:30% ; font-size:80%; color:blue">' . $row[8] . '</td>
				<td align="right" style="width:30% ; font-size:80%; color:blue"><form method="post" action="details.php" ><div><input type="submit" name="' . $row[0] . '" value="View Details" /></div></form></td>
			</tr>
		</table>';
					}
				}
		
			mysql_free_result($result);
			
		}
		else
			{
			echo '<p class="error"><strong>ERROR:</strong>Query Failed</p>';
			exit;
			}
			
		// Display links to get records from different start

		if ( $records[0] ) // Display links to get records from different start
			{
			echo '<hr /><form method="post" action="index.php" ><p>Go To Page ';
			
			for ( $i=1, $page=1 ; $i<=$records[0] ; $i=$i+3, $page++ )
				{
				echo ' <input type="submit" value="' . $page . '" name="Page" />';
				}
			
			echo '</p></form>';
			
			}	
		
		
		if ( $_SESSION['userid'] )												// User has already successfully logged in
			{
			printf("<p>Logged in as: %s, click <a href=\"./logout.php\" title=\"here\">here</a> to logout.</p>", $_SESSION['userid']);
			}	
			
	//------------------------------------------------------------------------------------------------------------------------------------------------
	
	?>
	
		<!-- Link to Login Page -->
		
		<p>Click <a href="./login.php" title="here">here</a> for staff admin page.</p>
		
		<p><a href="http://validator.w3.org/check?uri=referer"><img
				src="http://www.w3.org/Icons/valid-xhtml10-blue"
				alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a></p>	
	</body>
</html>
