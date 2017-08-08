<?php

session_start();
//print_r(array_keys($_SESSION));
//print_r(array_values($_SESSION));

// Get Login
$login = $_SESSION['login'];
$toolTypeId = $_SESSION['toolTypeId'];
$customerReservationStartDate = $_SESSION['customerReservationStartDate'];
$customerReservationEndDate = $_SESSION['customerReservationEndDate'];
$toolIdAvailableValidation = Array();
$errorMsg = "";

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

 $query="SELECT 
    ToolId, AbbDescription, DepositAmount, RentalPricePerDay
FROM
    Tools
WHERE
    ToolId NOT IN (SELECT 
            ToolId
        FROM
            Reserved
                INNER JOIN
            Reservation ON Reserved.ReservationId = Reservation.ReservationId
        WHERE
            ((Reservation.StartDate <= '$customerReservationStartDate'
                AND Reservation.EndDate >= '$customerReservationEndDate')))
        AND ToolId NOT IN (SELECT 
            ToolId
        FROM
            servicetool
        WHERE
            (servicetool.StartDate <= '$customerReservationStartDate'
                AND servicetool.EndDate >= '$customerReservationEndDate'))
        AND Tools.ToolTypeId = $toolTypeId";

$result = mysql_query($query);

if (mysql_num_rows($result) == 0)
{
	/* View Profile failed */
	$errorMsg = "There Are No Tools Available For That Date Range";	
}
else
{

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
}

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$button = $_POST['button'];
	$partId = $_POST['partId'];

	if($button=="Back To Tool Availability Menu")
	{
		/* redirect to the Customer Main Menu page */
		header('Location: checkToolAvailability.php');
		exit();
	}
	else
	{
		/* redirect to the View Tool Details page */
		$_SESSION['partId'] = $partId;

	    for ($i = 0; $i < count($rows); $i++) {
     		array_push($toolIdAvailableValidation,$rows[$i]['ToolId']);
	    }

	    //print $partId;
		//print_r(array_keys($toolIdAvailableValidation));
		//print "<br>";
		//print_r(array_values($toolIdAvailableValidation));

		if (in_array($partId, $toolIdAvailableValidation)) 
		{
		
			$_SESSION['originatorPage'] = "toolAvailability.php";
			header('Location: toolDetails.php');
			exit();
		}
		else
		{
				$errorMsg = "Tool ID: $partId, Is Not Available";
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
				  <h1>Tool Availability</h1>
				  <hr>
					<?php

					require_once 'HTML/Table.php';

					$data = Array();

				    for ($i = 0; $i < count($rows); $i++) {
				         	$tempArray = array($rows[$i]['ToolId'],
				         					 $rows[$i]['AbbDescription'],
				         					 $rows[$i]['DepositAmount'], 
				         					 $rows[$i]['RentalPricePerDay']);
				         	array_push($data, $tempArray);
				         	array_push($toolIdAvailableValidation,$rows[$i]['ToolId']);
				    }

					$attrs = array('width' => '1000');
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
					$table->setHeaderContents(0, 1, 'Tool ID');
					$table->setHeaderContents(0, 2, 'Abbr. Description');
					$table->setHeaderContents(0, 3, 'Deposit ($)');
					$table->setHeaderContents(0, 4, 'Price/Day ($)');

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
					<form action="toolAvailability.php" method="post">
						<div class="tool_details_row">
							<label class="tool_details_label">Part #:</label>
							<input type="text" name="partId" class="tool_availability_input" />
							<input type="submit" name="button" value="View Details">
						</div>
						<br>
						<input type="submit" name="button" value="Back To Tool Availability Menu">
					<form/>  

				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>