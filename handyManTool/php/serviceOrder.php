<?php

session_start();
$login = $_SESSION['login'];

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$button =$_POST['button'];
	
	if($button=="Submit")
	{
		if (empty($_POST['ToolId']) or empty($login)or empty($_POST['StartDate']) or
		empty($_POST['EndDate']) or empty($_POST['EstRepairCost'])) 
		{
			$errorMsg = "Please provide ToolId, StartDate, EndDate and EstRepairCost";		
		}
		else 
		{  
			
			$ToolId = mysql_real_escape_string($_POST['ToolId']);
			$ClerkLogin = $login;
			$StartDate = mysql_real_escape_string($_POST['StartDate']);
			$EndDate = mysql_real_escape_string($_POST['EndDate']);
			$EstRepairCost = mysql_real_escape_string($_POST['EstRepairCost']);

				if($StartDate < $EndDate)
				{
					
					$query = "SELECT * FROM tools WHERE ToolId= '$ToolId'";
					$result = mysql_query($query);
				
					if (mysql_num_rows($result) == 0) 
					{
						/* login failed */
						$errorMsg = "Invalid ToolId, Please Enter A Valid One";
					}
					else 
					{

						$query="SELECT 
						    min(StartDate) as StartDate,max(EndDate) as EndDate
						FROM
						    serviceTool
						WHERE
						    ToolId = $ToolId";

						$result = mysql_query($query);
						$row = mysql_fetch_assoc($result);
						$minStartDate=$row['StartDate'];
						$maxEndDate=$row['EndDate'];

						if(empty($minStartDate) and empty($maxEndDate))
						{
							$minStartDate=$StartDate;
							$maxEndDate=$StartDate;

							/* The tool Exist*/
							$query = "INSERT INTO servicetool (ToolId ,ClerkLogin,startDate,endDate,EstRepairCost) 
						    VALUES ('$ToolId','$ClerkLogin','$StartDate','$EndDate','$EstRepairCost')";		
						    $result = mysql_query($query);
						    
						    if($result==1)
							{
								$successMsg = "Success Creating Service Order";
							}
							else
							{
								$errorMsg = "Error Creating Service Order";
							}
						}
						else
						{
							$query = "SELECT 
									    *
									FROM
									    serviceTool
									WHERE
									    ToolId = $ToolId
									        and ('$minStartDate' <= '$StartDate'
									        and '$StartDate' <= '$maxEndDate')";
							$result = mysql_query($query);

							if (mysql_num_rows($result) > 0)
							{
								$errorMsg = "Tool Already In Service Tool";
							} 
							else
							{
								/* The tool Exist*/
								$query = "INSERT INTO servicetool (ToolId ,ClerkLogin,startDate,endDate,EstRepairCost) 
							    VALUES ('$ToolId','$ClerkLogin','$StartDate','$EndDate','$EstRepairCost')";		
							    $result = mysql_query($query);
							    
							    if($result==1)
								{
									$successMsg = "Success Creating Service Order";
								}
								else
								{
									$errorMsg = "Error Creating Service Order";
								}
							}
						}
					}
				}
				else
				{
					$errorMsg = "End Date cannot be the same or less than Start Date";
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
				<h2>Service Order Request</h2>

					<form action="serviceOrder.php" method="post">

						<div class="create_form_row">
							<label class="ToolId_label">Tool Id:</label>
							<input type="text" name="ToolId" class="ToolId_input" />
						</div>

						<div class="create_form_row">
							<label class="startDate_label">Start Date:</label>
							<input type="date" name="StartDate" class="startDate_input" />
						</div>

						<div class="create_form_row">
							<label class="endDatelabel">End Date:</label>
							<input type="date" name="EndDate" class="endDate_input" />
						</div>

						<div class="create_form_row">
							<label class="login_label">Estimated Cost of Repair $:</label>
							<input type="text" name="EstRepairCost" class="EstRepairCost_input" />
						</div>
						<input type="submit" name="button" value="Submit">  
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
