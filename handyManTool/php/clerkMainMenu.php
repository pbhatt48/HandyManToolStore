<?php

session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$button =$_POST['button'];
	
	if($button=="Pick-Up Reservation")
	{
		/* redirect to the Pick-Up Reservation page */
		$_SESSION['destinationPage'] = "pickUpReservation.php";
		header('Location: enterReservationNumber.php');
		exit();
	}
	else if($button=="Drop-Off Reservation")
	{
		/* redirect to the Drop-Off Reservation page */
		$_SESSION['destinationPage'] = "dropOffReservation.php";
		header('Location: enterReservationNumber.php');
		exit();
	}
	else if($button=="Service Order")
	{
		/* redirect to the Service Order page */
		header('Location: serviceOrder.php');
		exit();		
	}
	else if($button=="Add New Tool")
	{
		/* redirect to the Add New Tool page */
		header('Location: addNewTool.php');
		exit();	
	}
	else if($button=="Sell Tool")
	{
		/* redirect to the Sell Tool page */
		header('Location: sellTool.php');
		exit();			
	}
	else if($button=="Generate Reports")
	{
		/* redirect to the Generate Reports page */
		header('Location: generateReports.php');
		exit();				
	}
	else
	{
		/* redirect to the login page */
		header('Location: login.php');
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

					<h2>Main Menu</h2>
					
					<form action="clerkMainMenu.php" method="post">
   						<br>
						<input type="submit" name="button" value="Pick-Up Reservation"> 
						<br>
						<input type="submit" name="button" value="Drop-Off Reservation">
						<br>   
						<input type="submit" name="button" value="Service Order">     
						<br>   
						<input type="submit" name="button" value="Add New Tool">    
						<br>
						<input type="submit" name="button" value="Sell Tool">
						<br>
						<input type="submit" name="button" value="Generate Reports">   
						<br>
						<input type="submit" name="button" value="Exit">                                                         
					<form/>                 	   
				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>