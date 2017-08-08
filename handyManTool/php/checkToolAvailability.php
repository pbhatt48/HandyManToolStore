<?php
session_start();

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
	
	if($button=="Back To Main Menu")
	{
		/* redirect to the View Profile page */
		header('Location: customerMainMenu.php');
		exit();
	}

	if (empty($_POST['toolTypeId']) or
		empty($_POST['customerReservationStartDate']) or empty($_POST['customerReservationEndDate'])) 
	{
		$errorMsg = "Please Select a Tool Type and Enter a Start and End Date";		
	}
	else 
	{  
		
		$toolTypeId = mysql_real_escape_string($_POST['toolTypeId']);
		$customerReservationStartDate = mysql_real_escape_string($_POST['customerReservationStartDate']);
		$customerReservationEndDate = mysql_real_escape_string($_POST['customerReservationEndDate']);

		if($customerReservationStartDate < $customerReservationEndDate)
		{
			$_SESSION['toolTypeId'] = $toolTypeId;
			$_SESSION['customerReservationStartDate'] = $customerReservationStartDate;
			$_SESSION['customerReservationEndDate'] = $customerReservationEndDate;
			header('Location: toolAvailability.php');
			exit();
		}
		else
		{
			$errorMsg = "End Date cannot be the same or less than Start Date. Reservation should be at least for a day.";
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
				<h2>Select Tool Category
					<br>
				</h2>
				____________________________________

				<br>
				<br>

					<form action="checkToolAvailability.php" method="post">
						<input type="radio" name="toolTypeId" value="1">Hand Tools
						<br>
   						<input type="radio" name="toolTypeId" value="2">Construction Equipment
   						<br>
   						<input type="radio" name="toolTypeId" value="3">Power Tools
   						<br>
   						____________________________________
   						<br>
   						<br>
   						<br>
						<div class="create_form_row">
							<label class="start_date"><strong>Start Date:</strong></label>
							<input type="date" name="customerReservationStartDate" class="checkAvailiability" />
						</div>
										
						<div class="create_form_row">
							<label class="end_date"><strong>End Date:</strong></label>
							<input type="date" name="customerReservationEndDate" class="checkAvailiability" />
						</div>
						<br>
						<input type="submit" name="button" value="Submit">
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