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

$query = "SELECT 
    Customer.CustomerLogin,
    Customer.LastName,
    COUNT(*) AS NumberOfTool
FROM
    Customer
        INNER JOIN
    Reservation ON Reservation.CustomerLogin = Customer.CustomerLogin
        INNER JOIN
    Reserved ON Reservation.ReservationId = Reserved.ReservationId
        INNER JOIN
    Tools ON Reserved.ToolId = Tools.ToolId
WHERE
    ((Datediff(NOW(), Reservation.StartDate) >= 0
        AND Datediff(NOW(), Reservation.StartDate) <= 30)
        AND (Datediff(NOW(), Reservation.EndDate) >= 0
        AND Datediff(NOW(), Reservation.EndDate) <= 30))
GROUP BY Customer.CustomerLogin
ORDER BY NumberOfTool DESC , Customer.LastName ASC";


$result = mysql_query($query);


$rows = Array();
while($row = mysql_fetch_array($result)){
	array_push($rows, $row);
}

//print_r(array_keys($rows));
//print "<br>";
//print_r(array_values($rows));
//print "<br>";
//echo count($rows);
//echo count($rows[0]);


if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$button =$_POST['button'];

	if($button=="Back To Report Menu")
	{
		/* redirect to the View Profile page */
		header('Location: generateReports.php');
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
				<h2>Customer Report - Tools Rented In The Last 30 Days</h2>
				<hr>
				  <?php
				  
				  require_once 'HTML/Table.php';

					$data = Array();

					    for ($i = 0; $i < count($rows); $i++) {
					         	$tempArray = array($rows[$i]['CustomerLogin'],
					         					 $rows[$i]['LastName'],
					         					 $rows[$i]['NumberOfTool']);
					         	array_push($data, $tempArray);
					    }

					$attrs = array('width' => '600');
					$table = new HTML_Table($attrs);
					$table->setAutoGrow(true);
					$table->setAutoFill('n/a');

					for ($nr = 0; $nr < count($data); $nr++) {
					  $table->setHeaderContents($nr+1, 0, (string)$nr);
					  for ($i = 0; $i < 4; $i++) {
					    if ('' != $data[$nr][$i]) {
					      $table->setCellContents($nr+1, $i+1, $data[$nr][$i]);
					    }
					  }
					}

					$table->setHeaderContents(0, 0, '###');
					$table->setHeaderContents(0, 1, 'Customer Login');
					$table->setHeaderContents(0, 2, 'Last Name');
					$table->setHeaderContents(0, 3, 'Number Of Tools');

					$hrAttrs = array('bgcolor' => 'silver');
					$table->setRowAttributes(0, $hrAttrs, true);
					$table->setColAttributes(0, $hrAttrs);

					echo $table->toHtml();
			
				  ?>
				  	<br>
				  	<br>
					<form action="customerReport.php" method="post">
						<div class="create_form_row">
						<input type="submit" name="button" value="Back To Report Menu">  
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
