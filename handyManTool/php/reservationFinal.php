<?php

session_start();
//print_r(array_keys($_SESSION));
//print_r(array_values($_SESSION));

// Get Login
$login = $_SESSION['login'];
$reservationId = $_SESSION['reservationId'];
$startDate="";
$endDate="";
$totalRentalPrice= 0;
$totalDeposit= 0 ;
$errorMsg = "";

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

$query = "SELECT 
    Tools.AbbDescription,
    Reservation.StartDate,
    Reservation.EndDate,
    Tools.RentalPricePerDay * DATEDIFF(Reservation.EndDate,
            Reservation.StartDate) AS TotalRentalPrice,
    Tools.DepositAmount AS TotalDeposit
FROM
    Reserved
        INNER JOIN
    Tools ON Reserved.ToolId = Tools.ToolId
        INNER JOIN
    Reservation ON Reserved.ReservationId = Reservation.ReservationId
WHERE
    (Reservation.ReservationId = $reservationId)
Group by Tools.ToolId;";

$result = mysql_query($query);

if (mysql_num_rows($result) == 0)
{
	/* View Profile failed */
	$errorMsg = "Failed To Retrieved Reservation Information";	
}
else
{

	$rows = Array();
	while($row = mysql_fetch_array($result)){
	  array_push($rows, $row);
	  $totalRentalPrice = $totalRentalPrice + $row['TotalRentalPrice'];
	  $totalDeposit = $totalDeposit + $row['TotalDeposit'];
	}

	//print "<hr>";
	//print_r(array_keys($rows));
	//print "<br>";
	//print_r(array_values($rows));
	//print "<br>";
	//echo count($rows);
	//print "<hr>";
	//echo count($rows[0]);

	$startDate = $rows[0]['StartDate'];
	$endDate = $rows[0]['EndDate'];
	//$totalRentalPrice = $rows[0]['TotalRentalPrice'];
	//$totalDeposit = $rows[0]['TotalDeposit'];
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$button =$_POST['button'];

	if($button=="Back To Main Menu")
	{
		/* redirect to the View Profile page */
		header('Location: customerMainMenu.php');
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
				  <h1>Reservation Final</h1>
				  <h2>Here are all the details:</h2>
				  <hr>
				  <?php
				  print "<h3>Reservation Number:</h3>";
				  print "$reservationId";
				  ?>
				  <h4>Tools Desired: </h4>

				  <?php
				  for($i=0;$i<count($rows);$i++)
				  {
				  	$rowNum=intval($i)+1;
				  	$row="$rowNum" . " " . $rows[$i]["AbbDescription"];
				  	print "$row";
				  	print "<br>";
				  }
				  

				  print "<br>";
				  print "<strong>Start Date:</strong> $startDate";
				  print "<br>";
				  print "<strong>End Date:</strong> $endDate";
				  print "<br>";
				  print "<strong>Total Rental Price:</strong> $totalRentalPrice";
				  print "<br>";
				  print "<strong>Total Deposit Required:</strong> $totalDeposit";
				  ?>
				  
					<?php
					if (!empty($errorMsg)) {
						print "<div class='login_form_row' style='color:red'>$errorMsg</div>";
					}
					?>

					<br>
					<br>
					<form action="reservationFinal.php" method="post">
					<input type="submit" name="button" value="Back To Main Menu">
					<form/>  

				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>