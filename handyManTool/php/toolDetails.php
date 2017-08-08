<?php

session_start();
//print_r(array_keys($_SESSION));
//print_r(array_values($_SESSION));

$toolTypeIdMapping = Array();
$toolTypeIdMapping[1] = 'Hand Tool';
$toolTypeIdMapping[2] = 'Construction Equipment';
$toolTypeIdMapping[3] = 'Power Tool';

// Get Login
$toolId = $_SESSION['partId'];
$errorMsg = "";
$originatorPage = $_SESSION['originatorPage'];

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

$query = "SELECT * FROM tools WHERE ToolId = $toolId";

$result = mysql_query($query);

if (mysql_num_rows($result) == 0)
{
	/* View Profile failed */
	$errorMsg = "No Tool Details Available.";	
}
else
{

	// Profile Data
	$row = mysql_fetch_assoc($result);
	$toolId = $row['ToolId'];
	$toolTypeId = $toolTypeIdMapping[$row['ToolTypeId']];
	$abbDescription = $row['AbbDescription'];
	$fullDescription = $row['FullDescription'];
	$purchasePrice = $row['PurchasePrice'];
	$rentalPricePerDay = $row['RentalPricePerDay'];
	$depositAmount = $row['DepositAmount'];
	$availableForSale = $row['AvailableForSale'];
	$toolSold = $row['ToolSold'];

}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$button =$_POST['button'];

	if($button=="Back To Tool Availability")
	{
		/* redirect to the Tool Availability page */
		header('Location: toolAvailability.php');
		exit();
	}
	if($button=="Back To Pick Up Reservation" or $button=="Back To Drop Off Reservation")
	{
		/* redirect to Pick Up Reservation page */
		header('Location: ' . $originatorPage);
		exit();
	}
	if($button=="Back To Tool Availability")
	{
		header('Location: ' . $originatorPage);
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
				  <h1>Tool Details</h1>
				  <hr>
				  <?php
				  print "<strong>Tool Id:</strong> $toolId";
				  print "<br>";
				  print "<strong>Tool Type:</strong> $toolTypeId";
				  print "<br>";
				  print "<strong>Abbr. Description:</strong> $abbDescription";
				  print "<br>";
				  print "<strong>Full Description:</strong> $fullDescription";
				  print "<br>";
				  print "<strong>Purchase Price:</strong> $purchasePrice";
				  print "<br>";
				  print "<strong>Rental Price Per Day:</strong> $rentalPricePerDay";
				  print "<br>";
				  print "<strong>Deposit Amount:</strong> $depositAmount";
				  print "<br>";
				  print "<strong>Available For Sale:</strong> $availableForSale";
				  print "<br>";
				  print "<strong>Tool Sold:</strong> $toolSold";
				  ?>
				  <br>
				  <?php
					if (!empty($errorMsg)) {
						print "<div class='login_form_row' style='color:red'>$errorMsg</div>";
					}
					?>

					<br>
					<form action="toolDetails.php" method="post">
					<?php
					if(empty($originatorPage))
					{
						print "<input type=\"submit\" name=\"button\" value=\"Back To Tool Availability\">";
					}
					else if($originatorPage=="pickUpReservation.php")
					{
						print "<input type=\"submit\" name=\"button\" value=\"Back To Pick Up Reservation\">";
					}
					else if($originatorPage=="dropOffReservation.php")
					{
						print "<input type=\"submit\" name=\"button\" value=\"Back To Drop Off Reservation\">";
					}
					else
					{
						print "<input type=\"submit\" name=\"button\" value=\"Back To Tool Availability\">";
					}
					?>
					<form/>  

				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>