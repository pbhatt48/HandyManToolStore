<?php

session_start();
//print_r(array_keys($_SESSION));
//print_r(array_values($_SESSION));

// Get Login
$login = $_SESSION['login'];
$reservationId = $_SESSION['reservationId'];
$errorMsg = "";

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

$query = "SELECT 
    Reservation.ReservationId,
    Reservation.StartDate,
    Reservation.EndDate,
    CONCAT(Clerk.FirstName, ' ', Clerk.LastName) as 'Clerk Name',
    Reservation.CreditCard,
    CONCAT(Customer.FirstName,
            ' ',
            Customer.LastName) as 'Customer Name',
    Tools.ToolId,
    Tools.AbbDescription,
    Tools.RentalPricePerDay * DATEDIFF(Reservation.EndDate,
            Reservation.StartDate) AS EstimatedRental,
    Tools.DepositAmount AS DepositHeld
FROM
    Customer
        INNER JOIN
    Reservation ON Customer.CustomerLogin = Reservation.CustomerLogin
        INNER JOIN
    Reserved ON Reservation.ReservationId = Reserved.ReservationId
        INNER JOIN
    Tools ON Tools.ToolId = Reserved.ToolId
        INNER JOIN
    Clerk ON Reservation.PickUpClerk = Clerk.ClerkLogin
WHERE
    Reservation.ReservationId = $reservationId
GROUP BY Tools.ToolId";


$result = mysql_query($query);


$rows = Array();
while($row = mysql_fetch_array($result)){
	array_push($rows, $row);
  	$estimatedRental += ($row['EstimatedRental']);
  	$depositHeld += ($row['DepositHeld']);
}

$clerkName=$rows[0]['Clerk Name'];
$customerName=$rows[0]['Customer Name'];
$creditCard=$rows[0]['CreditCard'];
$startDate=$rows[0]['StartDate'];
$endDate=$rows[0]['EndDate'];

//print_r(array_keys($rows));
//print "<br>";
//print_r(array_values($rows));
//print "<br>";
//echo count($rows);
//echo count($rows[0]);


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$button =$_POST['button'];

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
				<h2>HANDYNAN TOOLS RENTAL CONTRACT</h2>
				<hr>
				  <?php
				  print "<strong>Reservation Number:</strong> $reservationId ";
				  print "<br>";
				  print "<strong>Clerk On Duty:</strong> $clerkName";
				  print "<br>";
				  print "<strong>Customer Name:</strong> $customerName";
				  print "<br>";
				  print "<strong>Credit Card #:</strong> $creditCard";
				  print "<br>";
				  print "<strong>Start Date:</strong> $startDate";
				  print "<br>";
				  print "<strong>End Date:</strong> $endDate";
				  print "<br>";
				  print "<br>";
				  print "<strong>Tools Rented: </strong>";
				  
				  require_once 'HTML/Table.php';

					$data = Array();

					    for ($i = 0; $i < count($rows); $i++) {
					         	$tempArray = array($rows[$i]['ToolId'],
					         					 $rows[$i]['AbbDescription']);
					         	array_push($data, $tempArray);
					    }

					$attrs = array('width' => '600');
					$table = new HTML_Table($attrs);
					$table->setAutoGrow(true);
					$table->setAutoFill('n/a');

					for ($nr = 0; $nr < count($data); $nr++) {
					  $table->setHeaderContents($nr+1, 0, (string)$nr);
					  for ($i = 0; $i < 3; $i++) {
					    if ('' != $data[$nr][$i]) {
					      $table->setCellContents($nr+1, $i+1, $data[$nr][$i]);
					    }
					  }
					}

					$table->setHeaderContents(0, 0, '###');
					$table->setHeaderContents(0, 1, 'Tool ID');
					$table->setHeaderContents(0, 2, 'AbbDescription');

					$hrAttrs = array('bgcolor' => 'silver');
					$table->setRowAttributes(0, $hrAttrs, true);
					$table->setColAttributes(0, $hrAttrs);

					echo $table->toHtml();
			
				  print "<br>";
				  print "<strong>Deposit Held:</strong> $depositHeld";
				  print "<br>";
				  print "<strong>Estimated Rental:</strong> $estimatedRental";
				  print "<br>";
				  print "<strong>Signature:</strong> ___________________________________________";
				  print "<br>";
				  print "<br>";
				  ?>
					<form action="rentalContract.php" method="post">
						<div class="create_form_row">
						<input type="submit" name="button" value="Back To Main Menu">  
						</div>	                                                            
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
