<?php

session_start();
//print_r(array_keys($_SESSION));
//print_r(array_values($_SESSION));

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$button =$_POST['button'];
	
	if($button=="Inventory Report")
	{
		/* redirect to the View Profile page */
		header('Location: inventoryReport.php');
		exit();
	}
	else if($button=="Customer Report - Last 30 Days")
	{
		/* redirect to the Check Tool Availability page */
		header('Location: customerReport.php');
		exit();
	}
	else if($button=="Clerk Of The Month")
	{
		/* redirect to the Make Reservation page */
		header('Location: clerkReport.php');
		exit();
	}
	else
	{
		/* redirect to the login page */
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

					<h2>Reports</h2>
					<hr>
					<form action="generateReports.php" method="post">
   						<br>
						<input type="submit" name="button" value="Inventory Report"> 
						<br>
						<br>
						<input type="submit" name="button" value="Customer Report - Last 30 Days">
						<br>   
						<br>
						<input type="submit" name="button" value="Clerk Of The Month">     
						<br>   
						<br>
						<input type="submit" name="button" value="Back To Main Menu">                                                             
					<form/>                 	   
				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>