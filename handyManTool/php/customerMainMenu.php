<?php

session_start();
//print_r(array_keys($_SESSION));
//print_r(array_values($_SESSION));

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$button =$_POST['button'];
	
	if($button=="View Profile")
	{
		/* redirect to the View Profile page */
		header('Location: viewProfile.php');
		exit();
	}
	else if($button=="Check Tool Availability")
	{
		/* redirect to the Check Tool Availability page */
		header('Location: checkToolAvailability.php');
		exit();
	}
	else if($button=="Make Reservation")
	{
		/* redirect to the Make Reservation page */
		header('Location: makeReservation.php');
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
					<hr>
					<form action="customerMainMenu.php" method="post">
   						<br>
						<input type="submit" name="button" value="View Profile"> 
						<br>
						<input type="submit" name="button" value="Check Tool Availability">
						<br>   
						<input type="submit" name="button" value="Make Reservation">     
						<br>   
						<input type="submit" name="button" value="Exit">                                                             
					<form/>                 	   
				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>