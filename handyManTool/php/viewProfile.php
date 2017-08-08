<?php

session_start();
//print_r(array_keys($_SESSION));
//print_r(array_values($_SESSION));

// Get Login
$login = $_SESSION['login'];
$errorMsg = "";

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

$query = "SELECT * FROM customer WHERE CustomerLogin = '$login'";

$queryReservation = "SELECT 
    CustomerLogin, 
    Name,
    HomePhoneLocalNumber,
    WorkPhoneLocalNumber,
    Address,
    ReservationId,
    AbbDescription,
    StartDate,
    EndDate,
    RentalPricePerDay,
    DepositAmount,
    PickupClerkName,
    CONCAT(Clerk.FirstName, ' ', Clerk.LastName) AS DropOffClerkName
FROM
    (SELECT 
        CustomerLogin,
            Name,
            HomePhoneLocalNumber,
            WorkPhoneLocalNumber,
            Address,
            ReservationId,
            AbbDescription,
            StartDate,
            EndDate,
            RentalPricePerDay,
            DepositAmount,
            PickUpClerk,
            DropOffClerk,
            CONCAT(Clerk.FirstName, ' ', Clerk.LastName) AS PickupClerkName
    FROM
        (SELECT 
        Customer.CustomerLogin,
            CONCAT(Customer.FirstName, ' ', Customer.LastName) AS Name,
            Customer.WorkPhoneLocalNumber,
            Customer.HomePhoneLocalNumber,
            Customer.Address,
            Reservation.ReservationId,
            Reservation.StartDate,
            Reservation.EndDate,
            Tools.AbbDescription,
            Tools.DepositAmount,
            Tools.RentalPricePerDay,
            Reservation.PickUpClerk,
            Reservation.DropOffClerk
    FROM
        Customer
    INNER JOIN Reservation ON Customer.CustomerLogin = Reservation.CustomerLogin
    INNER JOIN Reserved ON Reserved.ReservationId = Reservation.ReservationId
    INNER JOIN Clerk ON Reservation.PickUpClerk = Clerk.ClerkLogin
    INNER JOIN Tools ON Reserved.ToolId = Tools.ToolId
    WHERE
        Customer.CustomerLogin = '$login'
    ORDER BY StartDate ASC) S
    INNER JOIN Clerk ON Clerk.ClerkLogin = S.PickUpClerk) U
        INNER JOIN
    Clerk ON Clerk.ClerkLogin = U.DropOffClerk;";


$result = mysql_query($query);
$reservationData = mysql_query($queryReservation);

if (mysql_num_rows($result) == 0)
{
	/* View Profile failed */
	$errorMsg = "View Profile failed.  Unable To Find Customer.";	
}
else
{

	// Profile Data
	$row = mysql_fetch_assoc($result);
	$login = $row['CustomerLogin'];
	$name = $row['FirstName'] . " " . $row['LastName'];
	$homePhone = "(" . $row['HomePhoneCountryCode'] .")" . " " . $row['HomePhoneLocalNumber'];
	$workPhone = "(" . $row['WorkPhoneCountryCode'] .")" . " " . $row['WorkPhoneLocalNumber'];
	$address = $row['Address'];

	$rows = Array();
	while($row = mysql_fetch_array($reservationData)){
	  array_push($rows, $row);
	}

	//print_r(array_keys($rows));
	//print "<br>";
	//print_r(array_values($rows));
	//print "<br>";
	//echo count($rows);
	//echo count($rows[0]);
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
				  <h1>Profile</h1>
				  <hr>
				  <?php
				  print "<strong>Email Address:</strong> $login";
				  print "<br>";
				  print "<strong>Name:</strong> $name";
				  print "<br>";
				  print "<strong>Home Phone:</strong> $homePhone";
				  print "<br>";
				  print "<strong>Work Phone:</strong> $workPhone";
				  print "<br>";
				  print "<strong>Address:</strong> $address";
				  ?>
				  <hr>
				  <h1>Reservation History</h1>
				  <h4>*******************</h4>

					<?php

					require_once ('../../../../../pear/share/pear/HTML/Table.php');
					require_once ('../../../../../pear/share/pear/PEAR.php');

					$data = Array();

					    for ($i = 0; $i < count($rows); $i++) {
					         	$tempArray = array($rows[$i]['ReservationId'],
					         					 $rows[$i]['AbbDescription'],
					         					 $rows[$i]['StartDate'], 
					         					 $rows[$i]['EndDate'], 
					         					 $rows[$i]['RentalPricePerDay'], 
					         					 $rows[$i]['DepositAmount'], 
					         					 $rows[$i]['PickupClerkName'], 
					         					 $rows[$i]['DropOffClerkName']);
					         	array_push($data, $tempArray);
					    }

					$attrs = array('width' => '1200');
					$table = new HTML_Table($attrs);
					$table->setAutoGrow(true);
					$table->setAutoFill('n/a');

					for ($nr = 0; $nr < count($data); $nr++) {
					  $table->setHeaderContents($nr+1, 0, (string)$nr);
					  for ($i = 0; $i < 8; $i++) {
					    if ('' != $data[$nr][$i]) {
					      $table->setCellContents($nr+1, $i+1, $data[$nr][$i]);
					    }
					  }
					}

					$table->setHeaderContents(0, 0, '###');
					$table->setHeaderContents(0, 1, 'Res #');
					$table->setHeaderContents(0, 2, 'Tools');
					$table->setHeaderContents(0, 3, 'Start');
					$table->setHeaderContents(0, 4, 'End');
					$table->setHeaderContents(0, 5, 'Rental Price');
					$table->setHeaderContents(0, 6, 'Deposit');
					$table->setHeaderContents(0, 7, 'Pick-Up Clerk');
					$table->setHeaderContents(0, 8, 'Drop-Off Clerk');

					$hrAttrs = array('bgcolor' => 'silver');
					$table->setRowAttributes(0, $hrAttrs, true);
					$table->setColAttributes(0, $hrAttrs);

					echo $table->toHtml();

					?>

					<?php
					if (!empty($errorMsg)) {
						print "<div class='login_form_row' style='color:red'>$errorMsg</div>";
					}
					?>

					<br>
					<form action="viewProfile.php" method="post">
					<input type="submit" name="button" value="Back To Main Menu">
					<form/>  

				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>