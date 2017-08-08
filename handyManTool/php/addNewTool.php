<?php
session_start();

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

$errorMsg = "";
$numberOfAccesories = 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	//print "<hr>";
	//print_r(array_keys($_POST));
	//print "<br>";
	//print_r(array_values($_POST));
	//print "<br>";
	//echo count($_POST);
	//print "<hr>";

	$button =$_POST['button'];
	$numberOfAccesories = $_POST['numberOfAccesories'];
	$powerToolAccessories = Array();
	$toolTypeId = $_POST['toolTypeId'];

	for($i=0;$i<$numberOfAccesories;$i++)
	{
		$name = 'powerToolAccessories'.$i;
		//print $name;
		$tempValue = $_POST[$name];
		array_push($powerToolAccessories, $tempValue);
	}
	
	if($button=="Back To Main Menu")
	{
		/* redirect to the View Profile page */
		header('Location: clerkMainMenu.php');
		exit();
	}

	//if($button=="Add Accessories")
	//{
	//	$numberOfAccesories = $numberOfAccesories + 1;
	//}

	//print "<hr>";
	//print $numberOfAccesories;
	//print "<hr>";

	$abbDescription = mysql_real_escape_string($_POST['abbDescription']);
	$purchasePrice = mysql_real_escape_string($_POST['purchasePrice']);
	$rentalPrice = mysql_real_escape_string($_POST['rentalPrice']);
	$depositAmount = mysql_real_escape_string($_POST['depositAmount']);
	$fullDescription = mysql_real_escape_string($_POST['fullDescription']);
	$toolTypeId = mysql_real_escape_string($_POST['toolTypeId']);

	//print "<hr>";
	//print "powerToolAccessories";
	//print_r(array_keys($powerToolAccessories));
	//print "<br>";
	//print_r(array_values($powerToolAccessories));
	//print "<br>";
	//echo count($powerToolAccessories);
	//echo count($powerToolAccessories[0]);
	//print "<hr>";

	if($button=="Submit New Tool")
	{
		if (empty($_POST['abbDescription']) or empty($_POST['purchasePrice']) or empty($_POST['rentalPrice']) 
			or empty($_POST['depositAmount']) or empty($_POST['fullDescription']) or empty($_POST['toolTypeId'])) 
		{
			$errorMsg = "All selection is mandatory";		
		}
		else 
		{  
			
			if($abbDescription != "" or $purchasePrice != "" or $rentalPrice != "" or $depositAmount != "" or $fullDesription != "" or $toolType != "")
			{
				
				$query = "INSERT INTO Tools (ToolTypeId, AbbDescription, FullDescription, PurchasePrice, RentalPricePerDay, DepositAmount, AvailableForSale, ToolSold ) 
				VALUES ($toolTypeId, '$abbDescription','$fullDescription' , $purchasePrice, $rentalPrice,$depositAmount, 1, 0)";		
				
				$result = mysql_query($query);

				if($result==1)
				{

					if($toolTypeId!=3)
					{
						$successMsg = "Success Adding the new tool";		
						$abbDescription = "";
						$purchasePrice = "";
						$rentalPrice = "";
						$depositAmount = "";
						$fullDescription = "";
					}
					else
					{
						//print "<hr>";
						//print "powerToolAccessories";
						//print_r(array_keys($powerToolAccessories));
						//print "<br>";
						//print_r(array_values($powerToolAccessories));
						//print "<br>";
						//echo count($powerToolAccessories);
						//echo count($powerToolAccessories[0]);
						//print "<hr>";

						$toolId = mysql_insert_id();

						$query = "INSERT INTO powertool_accesories (ToolId,Accesories) VALUES ";

						for($i=0;$i<count($powerToolAccessories)-1;$i++)
						{

							$tempAccessory=$powerToolAccessories[$i];
							$query = $query . "($toolId,'$tempAccessory'),";
						}		
						
						$query = substr($query, 0, -1);
						$result = mysql_query($query);

						if($result==1)
						{
							/* redirect to the Reservation Summary page */
							$successMsg = "Success Adding the new tool";
							$abbDescription = "";
							$purchasePrice = "";
							$rentalPrice = "";
							$depositAmount = "";
							$fullDescription = "";
						}
						else
						{
							$errorMsg = "Adding tool failed. Error Inserting to Database";
						}
					}
				}
				else
				{
					$errorMsg = "Adding tool failed. Error Inserting to Database";
				}
			}
		}
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
				<h2>Add New Tool
					<br>
				</h2>
					<form action="addNewTool.php" method="post">
						
						<div class="create_form_row">
							<label class="Abbreviated_Description">Abbreviated Description:</label>
							<?php
								if(empty($abbDescription))
								{
									print "<input type=\"text\" name=\"abbDescription\" class=\"addNewTool\">";
								}
								else
								{
									print "<input type=\"text\" name=\"abbDescription\" class=\"addNewTool\" value='$abbDescription'>";
								}
							?>
							
						</div>
										
						<div class="create_form_row">
							<label class="Purchase_price">Purchase Price:	$</label>
							<?php
								if(empty($purchasePrice))
								{
									print "<input type=\"number\" step =\"any\" name=\"purchasePrice\" class=\"addNewTool\">";
								}
								else
								{
									print "<input type=\"number\" step =\"any\" name=\"purchasePrice\" class=\"addNewTool\" value=$purchasePrice>";
								}
							?>
						</div>
						<div class="create_form_row">
							<label class="Rental_price">Rental Price( per day):	$</label>
							<?php
								if(empty($rentalPrice))
								{
									print "<input type=\"number\" step=\"any\" name=\"rentalPrice\" class=\"addNewTool\">";
								}
								else
								{
									print "<input type=\"number\" step=\"any\" name=\"rentalPrice\" class=\"addNewTool\" value=$rentalPrice>";
								}
							?>
						</div>
						<div class="create_form_row">
							<label class="Deposit_amount">Deposit Amount:	$</label>
							<?php
								if(empty($depositAmount))
								{
									print "<input type=\"number\" step=\"any\" name=\"depositAmount\" class=\"addNewTool\">";
								}
								else
								{
									print "<input type=\"number\" step=\"any\" name=\"depositAmount\" class=\"addNewTool\" value=$depositAmount>";
								}
							?>

						</div>
						<div class="create_form_row">
  							Full Description<br>
  							<?php
								if(empty($fullDescription))
								{
									print "<input type=\"text\" step=\"any\" name=\"fullDescription\" style=\"width: 300px;height:100px;\" class=\"addNewTool\">";
								}
								else
								{
									print "<input type=\"text\" step=\"any\" name=\"fullDescription\" style=\"width: 300px;height:100px;\" class=\"addNewTool\" value='$fullDescription'>";
								}
							?>
  						</div>

						<br>
						<div class="create_form_row">
  							Tool Type  
  							<select name="toolTypeId" list="toolTypeId">
  							<?php

  								print "ToolTypeId: " . $toolTypeId;
	  							if($toolTypeId==1)
	  							{
	  								print "<option value=\"1\" selected=true>Hand Tool</option>";
	  							}
	  							else
	  							{
	  								print "<option value=\"1\">Hand Tool</option>";
	  							}

	  							if($toolTypeId==2)
	  							{
	  								print "<option value=\"2\" selected=true>Construction Equipment</option>";
	  							}
	  							else
	  							{
	  								print "<option value=\"2\">Construction Equipment</option>";
	  							}

	  							if($toolTypeId==3)
	  							{
	  								print "<option value=\"3\" selected=true>Power Tool</option>";
	  							}
	  							else
	  							{
	  								print "<option value=\"3\">Power Tool</option>";
	  							}
  							?>
  							</select>
  						</div>
  						<br>
  						If new item is a Power Tool, Then include Accessories.
						<input type="submit" name="button" value="Add Accessories">
						<br>

						<?php
							if($toolTypeId==3 and $button=="Add Accessories")
							{
								print "<div class=\"create_form_row\">";
								for($k=0; $k < $numberOfAccesories; $k++){
									print "<br>";
									print "<label class=\"powerToolAccessories\">Accessory Name:</label>";
									$name="powerToolAccessories$k";
									$tempValue=$_POST[$name];
									//print $_POST[$name];
									//print $tempValue;
									print "<input type=\"Text\" name=\"powerToolAccessories$k\" class=\"powerToolAccessories\" value=$tempValue>";
									print "<br>";
								}
								print "</div>";
							}
							else
							{
								if($numberOfAccesories>0 and $button=="Add Accessories")
								{
									$errorMsg = "Tool Is Not A Power Tool";
								}

								$numberOfAccesories = 0;
							}
						?>

						<br>
						<input type="submit" name="button" value="Submit New Tool"> 
						<input type="submit" name="button" value="Back To Main Menu">  

						<?php
							$tempValue = $numberOfAccesories + 1;
							print "<input type=\"text\" name=\"numberOfAccesories\" value=$tempValue hidden=true>"
						?>


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