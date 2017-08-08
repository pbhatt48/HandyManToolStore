<?php
/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");
$errorMsg = "";
$successMsg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$button =$_POST['button'];
	
	if($button=="Search")
	{
		if (empty($_POST['ToolId'])) 
		{
			$errorMsg = "Please provide ToolId";		
		}
		else 
		{  
			
			$ToolId = mysql_real_escape_string($_POST['ToolId']);
			$query = "SELECT * FROM tools WHERE ToolId = '$ToolId' AND AvailableForSale = 1 AND ToolSold = 0";
			$result = mysql_query($query);
			//print $query;
			
			if (mysql_num_rows($result) == 0) 
			{
			   /* login failed */
				$errorMsg = "Tool with Id: $ToolId , it is not available for sale or has been sold.";
			}
			else{
				
				$row = mysql_fetch_assoc($result);
				$ToolId = $row['ToolId'];
				$fullDescription = $row['FullDescription'];
				$sellPrice = floatval($row['PurchasePrice'])/2.0;
			}
		}
	}
	
	if($button=="Sell")
	{
		if (empty($_POST['ToolId'])) 
		{
			$errorMsg = "Please provide ToolId";		
		}
		else 
		{  
			
			$ToolId = mysql_real_escape_string($_POST['ToolId']);
			$query = "SELECT * FROM tools WHERE ToolId = '$ToolId' AND AvailableForSale = 1 AND ToolSold = 0";
			$result = mysql_query($query);
			//print $query;
			
			if (mysql_num_rows($result) == 0) 
			{
			   /* login failed */
				$errorMsg = "Tool with Id: $ToolId , it is not available for sale or has been sold.";
			}
			else
			{
				
				$row = mysql_fetch_assoc($result);
				$ToolId = $row['ToolId'];
				$fullDescription = $row['FullDescription'];
				$sellPrice = floatval($row['PurchasePrice'])/2.0;
				
				$query = "UPDATE tools SET ToolSold = 1 WHERE ToolId = '$ToolId'";
				//print $query;

				$result = mysql_query($query);

				if($result==1)
				{
					$successMsg = "The Tool was successfully sold";
				}
				else
				{
					$errorMsg = "Error Selling Tool.";
				}
			}
				
		}		
	}
	if($button=="Back To Main Menu")
	{
		/* redirect to the View Profile page */
		header('Location: clerkMainMenu.php');
		exit();
	}
}
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>HandyManTool</title>
	</head>
	<body>
		<div id="main_container">
			<div id="header">     
			</div>
			<div class="center_content">
				<div class="text_box">
				<h2>Sell Tool</h2>
				<hr>
				  <?php
				  print "Tool ID: $ToolId";
				  print "<br>";
				  print "Full Description: $fullDescription";
				  print "<br>";
				  print "Sell Price: $sellPrice";
				  print "<br>";
				  ?>
				 <hr> 
					<form action="sellTool.php" method="post">

						<div class="create_form_row">
							<h3>Please Enter the Tool ID </h3>
							<label class="ToolId_label">Tool Id:</label>
							<?php
								if(empty($ToolId))
								{
									print "<input type=\"text\" name=\"ToolId\" class=\"ToolId_input\">";
								}
								else
								{
									print "<input type=\"text\" name=\"ToolId\" class=\"ToolId_input\" value=$ToolId>";
								}
							?>
						</div>	
						<h3></h3>		
						<input type="submit" name="button" value="Search"> 
						<input type="submit" name="button" value="Sell">  
						<input type="submit" name="button" value="Back To Main Menu">                                                              
					<form/>
				  
					<?php
					if (!empty($errorMsg)) {
						print "<div class='create_form_row' style='color:red'>$errorMsg</div>";
					}
					if (!empty($successMsg)) {
						print "<div class='create_form_row' style='color:black'>$successMsg</div>";
					}
					?>                    	   
				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>
