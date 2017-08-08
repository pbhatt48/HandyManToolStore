<?php
session_start();

$destinationPage = $_SESSION['destinationPage'];

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
		header('Location: clerkMainMenu.php');
		exit();
	}
	
	if($button=="Enter")
	{
		$reservationId = mysql_real_escape_string($_POST['reservationId']);

		$query="Select * from reservation Where ReservationId=$reservationId";

			$result = mysql_query($query);
			
			if (mysql_num_rows($result) == 0) 
			{
				/* login failed */
				$errorMsg = "Invalid Reservation Number. Please try again.";
				
			}
			else 
			{
				/* login successful */

				$_SESSION['reservationId'] = $reservationId;
				
				/* redirect to the profile page */
				if($destinationPage=="pickUpReservation.php")
				{
					header('Location: pickUpReservation.php');
					exit();
				}
				else
				{
					header('Location: dropOffReservation.php');
					exit();
				}
			}
	}
	else
	{
		if($button=="Customer Sign Up")
		{
			/* redirect to the profile page */
			header('Location: createCustomerProfile.php');
			exit();
		}
		else
		{
			/* redirect to the profile page */
			header('Location: createClerkProfile.php');
			exit();
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
					<h1>Reservation - Pick Up Details</h1>

					<form action="enterReservationNumber.php" method="post">

						<div class="reservation_form_row">
							<label class="reservation_label"><strong>Reservation Number:</strong></label>
							<input type="text" name="reservationId" class="reservation_input" />
						</div>
   						<br>
						<input type="submit" name="button" value="Enter"> 
						<br> 
						<br>
						<input type="submit" name="button" value="Back To Main Menu">
						                                                            
					<form/>
				  
					<?php
					if (!empty($errorMsg)) {
						print "<div class='reservation_form_row' style='color:red'>$errorMsg</div>";
					}
					?>                    	   
				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>