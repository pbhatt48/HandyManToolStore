<?php
session_start();

// Get Login
$login = $_SESSION['login'];
$errorMsg = "";
$numberOfTools = 1;
$toolListManager=Array();
$toolIdAssociatedToReservation=Array();

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

$query = "select Name from tooltype";

$result = mysql_query($query);

if (mysql_num_rows($result) == 0)
{
	/* View Profile failed */
	$errorMsg = "Unable To Retrieve Tool Types";	
}
else
{

	$toolTypeArray = Array();
	while($row = mysql_fetch_array($result)){
	  array_push($toolTypeArray, $row);
	}

	//print_r(array_keys($toolTypeArray));
	//print "<br>";
	//print_r(array_values($toolTypeArray));
	//print "<br>";
	//echo count($toolTypeArray);
	//print $toolTypeArray[0][0];
}


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{
	$button =$_POST['button'];
	$numberOfTools = $_POST['numberOfTools'];

	$toolTypeDropDown = Array();
	for($i=0;$i<$numberOfTools;$i++)
	{
		$name = 'toolTypeDropDown'.$i;
		//print $name;
		$tempValue = $_POST[$name];
		array_push($toolTypeDropDown, $tempValue);
	}

	$toolListDropDown = Array();
	for($i=0;$i<$numberOfTools;$i++)
	{
		$name = 'toolListDropDown'.$i;
		//print $name;
		$tempValue = $_POST[$name];
		array_push($toolListDropDown, $tempValue);
		$tempArrayToolId = explode('-', $tempValue);
		array_push($toolIdAssociatedToReservation,$tempArrayToolId[0]);
	}

	//print "<hr>";
	//print_r(array_keys($toolIdAssociatedToReservation));
	//print "<br>";
	//print_r(array_values($toolIdAssociatedToReservation));
	//print "<br>";
	//echo count($toolIdAssociatedToReservation);
	//print "<hr>";

	//print "<hr>";
	//print_r(array_keys($toolTypeDropDown));
	//print "<br>";
	//print_r(array_values($toolTypeDropDown));
	//print "<br>";
	//echo count($toolTypeDropDown);
	//print "<hr>";

	//print "<hr>";
	//print_r(array_keys($toolListDropDown));
	//print "<br>";
	//print_r(array_values($toolListDropDown));
	//print "<br>";
	//echo count($toolListDropDown);
	//print "<hr>";

	//$toolTypeDropDown = $_POST['toolTypeDropDown'];
	
	$customerReservationStartDate = $_POST['customerReservationStartDate'];
	$customerReservationEndDate = $_POST['customerReservationEndDate'];

	//print_r(array_keys($_POST));
	//print "<br>";
	//print_r(array_values($_POST));
	//print "<br>";
	//echo count($_POST);

	if($button=="Back To Main Menu")
	{
		/* redirect to the View Profile page */
		header('Location: customerMainMenu.php');
		exit();
	}
	
	if($button=="Calculate Total")
	{
		$query = "INSERT INTO reservation (CustomerLogin ,StartDate,EndDate) 
	    	VALUES ('$login','$customerReservationStartDate','$customerReservationEndDate')";		
	    $result = mysql_query($query);

	    if($result==1)
		{
			$reservationId = mysql_insert_id();

			$query = "INSERT INTO reserved (ReservationId ,ToolId) VALUES ";

			for($i=0;$i<count($toolIdAssociatedToReservation);$i++)
			{

				$tempToolId=$toolIdAssociatedToReservation[$i];
				$query = $query . "($reservationId,$tempToolId),";
			}	

			$query = substr($query, 0, -1);

			$result = mysql_query($query);

			if($result==1)
			{
				/* redirect to the Reservation Summary page */
				$_SESSION['reservationId'] = $reservationId;
				header('Location: reSummary.php');
				exit();
			}
			else
			{
				$errorMsg = "Error Creating Reservation/Reserved Association";
			}

		}
		else
		{
			$errorMsg = "Error Creating Reservation";
		}


	}

	if($button=="Add More Tools")
	{
		$numberOfTools = $numberOfTools + 1;
	}

	if($button=="Remove Last Tool")
	{
		$numberOfTools = $numberOfTools - 1;
	}

	if($numberOfTools<1)
	{
		$numberOfTools = 1;
	}

	// Tool List
	if (empty($customerReservationStartDate) or empty($customerReservationEndDate)) 
	{
		$errorMsg = "Please Enter a Start and End Date To Populate Tool List Drop Down";
	}
	else
	{

		//print "<hr>";
		//print "toolTypeDropDown";
		//print_r(array_keys($toolTypeDropDown));
		//print "<br>";
		//print_r(array_values($toolTypeDropDown));
		//print "<br>";
		//echo count($toolTypeDropDown);
		//echo count($toolList[0]);
		//print "<hr>";
		
		for($i=0;$i<count($toolTypeDropDown);$i++)
		{
			//print $toolTypeDropDown[$i];
			if($toolTypeDropDown[$i] == "Handy Tools")
			{
				$toolTypeId = 1;
			}
			else if($toolTypeDropDown[$i] == "Construction Equipment")
			{
				$toolTypeId = 2;
			}
			else
			{
				$toolTypeId = 3;
			}
				$queryToolsAvailable = "SELECT 
				    ToolId, AbbDescription, DepositAmount, RentalPricePerDay
				FROM
				    Tools
				WHERE
				    ToolId NOT IN (SELECT 
				            ToolId
				        FROM
				            Reserved
				                INNER JOIN
				            Reservation ON Reserved.ReservationId = Reservation.ReservationId
				        WHERE
				            ((Reservation.StartDate <= '$customerReservationStartDate'
				                AND Reservation.EndDate >= '$customerReservationEndDate')))
				        AND ToolId NOT IN (SELECT 
				            ToolId
				        FROM
				            servicetool
				        WHERE
				            (servicetool.StartDate <= '$customerReservationStartDate'
				                AND servicetool.EndDate >= '$customerReservationEndDate'))
				        AND Tools.ToolTypeId = $toolTypeId";

				$result = mysql_query($queryToolsAvailable);

				if (mysql_num_rows($result) == 0)
				{

					$errorMsg = "There Are No Tools Available For That Date Range";	
				}
				else
				{

					$toolList = Array();
					while($row = mysql_fetch_array($result)){
					  array_push($toolList, $row);
					}
					array_push($toolListManager, $toolList);

					//print_r(array_keys($toolList));
					//print "<br>";
					//print_r(array_values($toolList));
					//print "<br>";
					//echo count($toolList);
					//echo count($toolList[0]);
				}
		}

		//print_r(array_keys($toolListManager));
		//print "<br>";
		//print "<br>";
		//print_r(array_values($toolListManager));
		//print "<br>";
		//print "<br>";
		//echo count($toolListManager);
		//echo count($toolListManager[0]);
	}
}
  
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<title>HandyManTool</title>
		<SCRIPT language=JavaScript>
		function reload(form)
		{
			var ddl = document.getElementById("toolTypeDropDown0");
 			var selectedValue = ddl.options[ddl.selectedIndex].value;
			console.log("Tool Type: " +selectedValue);
			form.submit();
		}
		</script>
	</head>
	<body>
		<div id="main_container">
			<div id="header">     
			</div>
			<div class="center_content">
				<div class="text_box">
				<h2>Make Reservation</h2>
				<hr>
					<form action="makeReservation.php" method="post" name="makeReservation">

						<div class="create_form_row">
							<label class="start_date"><strong>Starting Date:</strong></label>
							
							<?php
							if(empty($customerReservationStartDate))
							{
								print "<input id= \"customerStartDate\" type=\"date\" name=\"customerReservationStartDate\" class=\"makeReservation\">";
							}
							else
							{
								print "<input id= \"customerStartDate\" type=\"date\" name=\"customerReservationStartDate\" class=\"makeReservation\" value=$customerReservationStartDate>";
							}

							?>
						</div>
										
						<div class="create_form_row">
							<label class="end_date"><strong>Ending Date:</strong></label>

							<?php
							if(empty($customerReservationEndDate))
							{
								print "<input id= \"customerEndDate\" type=\"date\" name=\"customerReservationEndDate\" class=\"makeReservation\" >";
							}
							else
							{
								print "<input id= \"customerEndDate\" type=\"date\" name=\"customerReservationEndDate\" class=\"makeReservation\" value=$customerReservationEndDate>";
							}
							?>
						</div>
						<hr>
						<?php
						for($k=0; $k < $numberOfTools; $k++){
							print '<div class="create_form_row">';
								print '<label class="type_tool"><strong>Type Of Tool:</strong></label>';
								print '<br>';
								
									print "<select id=\"toolTypeDropDown$k\" name=\"toolTypeDropDown$k\" onchange=\"reload(this.form)\">";

									print "<option >Select a Tool Type</option>";
									for ($i = 0; $i < count($toolTypeArray); $i++) {
										$tempValue = $toolTypeArray[$i][0];
										if($tempValue == $toolTypeDropDown[$k])
										{
											print "<option value='$tempValue' selected=true>$tempValue</option>";
										}
										else
										{
							     			print "<option value='$tempValue'>$tempValue</option>";
							     		}
								    }
								    print "</select>";
								
								print '<br>';
								print '<br>';
								print '<label class="type_tool"><strong>Tool:</strong></label>';
								print '<br>';

									print "<select id=\"toolListDropDown$k\" name=\"toolListDropDown$k\">";

									if(count($toolList)==0)
									{
										print "<option >Tool List</option>";
									}
									$toolList = $toolListManager[$k];
									for ($i = 0; $i < count($toolList); $i++) {
										
										$tempValue = $toolList[$i]['ToolId'] . " - " . $toolList[$i]['AbbDescription'] . " - ". $toolList[$i]['RentalPricePerDay'];

										if($tempValue == $toolListDropDown[$k])
										{
											print "<option value='$tempValue' selected=true>$tempValue</option>";
										}
										else
										{
							     			print "<option value='$tempValue'>$tempValue</option>";
							     		}
								    }
								    print "</select>";

								print '<br>';
								print '<hr>';
							print '</div>';
						}
						?>

						<br>
						<div class="create_form_row">
							<input type="submit" name="button" value="Add More Tools">
							<input type="submit" name="button" value="Remove Last Tool">
							<?php
								$tempValue = $numberOfTools;
								print "<input type=\"text\" name=\"numberOfTools\" value=$tempValue hidden=true>"
							?>
						</div>
						<br>
						<input type="submit" name="button" value="Calculate Total">
						<br>
						<br>
						<input type="submit" name="button" value="Back To Main Menu">

					<form/>
				  
					<?php
					if (!empty($errorMsg)) {
						print "<div class='create_form_row' style='color:red'>$errorMsg</div>";
					}
					?>                    	   
				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>