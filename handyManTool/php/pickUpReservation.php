<?php
session_start();

$reservationId = $_SESSION['reservationId'];
$login = $_SESSION['login'];
$toolIdAvailableValidation = Array();

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

$errorMsg = "";

$query = "SELECT 
	    Reservation.ReservationId,
	    Tools.ToolId,
	    Tools.AbbDescription,
	    Tools.RentalPricePerDay * DATEDIFF(Reservation.EndDate,
	            Reservation.StartDate) AS EstimatedCost,
	    Tools.DepositAmount AS DepositRequired
	FROM
	    Reserved
	        INNER JOIN
	    Tools ON Reserved.ToolId = Tools.ToolId
	        INNER JOIN
	    Reservation ON Reserved.ReservationId = Reservation.ReservationId
	WHERE
	    (Reservation.ReservationId = $reservationId)
	GROUP BY Tools.ToolId";

$result = mysql_query($query);

if(mysql_num_rows($result)==0)
{
	$errorMsg = "Error Retrieving Reservation Information";
}
else
{

	$pickUpTools = Array();
	while($row = mysql_fetch_array($result)){
		$estimatedCost += ($row['EstimatedCost']);
		$depositRequired += ($row['DepositRequired']);
		array_push($pickUpTools, $row);
		array_push($toolIdAvailableValidation,$row['ToolId']);
	}

	for ($i = 0; $i < count($rows); $i++) {
     		array_push($toolIdAvailableValidation,$rows[$i]['ToolId']);
	    }
}

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
	
	if($button=="Back To Enter Reservation Number")
	{
		/* redirect to the View Profile page */

		header('Location: enterReservationNumber.php');
		exit();
	}

	if($button == "View Details")
	{
		$partId = $_POST['toolID'];
		$_SESSION['partId'] = $partId;
		$_SESSION['originatorPage'] = "pickUpReservation.php";

		if (in_array($partId, $toolIdAvailableValidation)) 
		{
		
			header('Location: toolDetails.php');
			exit();
		}
		else
		{
				$errorMsg = "Tool ID: $partId, Is Not Part Of This Reservation";
		}
	}

	if($button == "Complete Pick-Up")
	{
		if (empty($_POST['creditCardNumber']) or empty($_POST['creditCardExpiration'])) 
		{
			$errorMsg = "Enter All information for the Pick up Reservation.";		
		}
		else 
		{  
			
			$creditCardNumber = mysql_real_escape_string($_POST['creditCardNumber']);
			$creditCardExpiration = mysql_real_escape_string($_POST['creditCardExpiration']);

			$query = "UPDATE reservation 
					SET 
					    CreditCard = '$creditCardNumber',
					    CreditCardExpirationDate = '$creditCardExpiration',
					    PickUpClerk = '$login'
					WHERE
					    ReservationId = $reservationId";
			
			$result = mysql_query($query);

			if($result==1)
			{
				header('Location: rentalContract.php');
				exit();
			}
			else
			{
				$errorMsg = "Error Updating Reservation Details.";
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
				<h1>Pick up Reservation
					<br>
				</h1>
				<hr>
				  <?php
				  print "<strong>Reservation Number:</strong> $reservationId";
				  print "<br>";
				  print "<br>";

				  print "<strong>Tools Required:</strong> ";
				  print "<br>";
				 for ($i = 0; $i < count($pickUpTools); $i++) {
					 	$rowNum = $i + 1;
					 	$tempValue = $rowNum . ". " . " " . $pickUpTools[$i]['ToolId']  . " " . $pickUpTools[$i]['AbbDescription'];
					 	print $tempValue;
					 	print "<br>";
				 	}

				  print "<br>";
				  print "<strong>Deposit Required:</strong> $ $depositRequired";
				  print "<br>";
				  print "<strong>Estimated Cost:</strong> $ $estimatedCost";
				  print "<br>";
				  print "<br>";
				 
				  ?>


				  <hr>
				  <br>
					<form action="pickUpReservation.php" method="post" name="pickUpReservation">
						
						<div class="create_form_row">
							<label class="ToolID_Details">Tool ID </label>
							<input type="number" step="1" name="toolID" class="Pickup" />
							<input type="submit" name="button" value="View Details">
						</div>
						<br>
						<hr>
						<br>

						<div class="create_form_row">
							<label class="CreditCard_Number">Credit Card Number </label>
							<input type="number" step="1" maxlength="16" name="creditCardNumber" class="Pickup" />
							
						</div>

						<div class="create_form_row">
							<label class="CreditCard_Expiration">Expiration Date </label>
							<input type="date" name="creditCardExpiration" class="Pickup" />
							
						</div>

						<br>


						<input type="submit" name="button" value="Complete Pick-Up"> 
						<br> 
						<br>
						<input type="submit" name="button" value="Back To Enter Reservation Number">


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