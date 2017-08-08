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
    K.ToolId,
    K.AbbDescription,
    K.DaysRented,
    K.RentalPricePerDay,
    K.PurchasePrice,
	K.ToolSold,
    K.ServiceCost,
    K.RentalProfit,
    K.CostOfTool,
    (K.RentalProfit - K.CostOfTool) AS TotalProfit
FROM
    (SELECT 
        P.ToolId,
            P.AbbDescription,
            P.DaysRented,
            P.RentalPricePerDay,
            P.PurchasePrice,
			P.ToolSold,
            P.ServiceCost,
            P.RentalPricePerDay * P.DaysRented AS RentalProfit,
            P.PurchasePrice + P.ServiceCost AS CostOfTool
    FROM
        (SELECT 
        T.ToolId,
            T.AbbDescription,
            T.RentalPricePerDay,
            T.PurchasePrice,
			T.ToolSold,
            T.DaysRented,
            E.ServiceCost
    FROM
        (SELECT 
        Tools.ToolId,
            Tools.AbbDescription,
            Tools.RentalPricePerDay,
            Tools.PurchasePrice,
			Tools.ToolSold,
            U.DaysRented
    FROM
        Tools, (SELECT 
        ToolId, SUM(DaysRented) AS DaysRented
    FROM
        (SELECT 
        Reserved.ToolId,
            Reservation.ReservationId,
            Reservation.StartDate,
            Reservation.EndDate,
            DATEDIFF(Reservation.EndDate, Reservation.StartDate) AS DaysRented
    FROM
        Reserved
    INNER JOIN Reservation ON Reserved.ReservationId = Reservation.ReservationId
    INNER JOIN Tools ON Reserved.ToolId = Tools.ToolId UNION ALL SELECT 
        Tools.ToolId,
            NULL AS ReservationId,
            NULL AS StartDate,
            NULL AS EndDate,
            0 AS DaysRented
    FROM
        Tools
    WHERE
        Tools.ToolId NOT IN (SELECT DISTINCT
                ToolId
            FROM
                Reserved)) S
    GROUP BY ToolId) U
    WHERE
        Tools.ToolId = U.ToolId) T, (SELECT 
        ToolId, SUM(EstRepairCost) AS ServiceCost
    FROM
        ServiceTool
    GROUP BY ToolId UNION ALL SELECT 
        Tools.ToolId, 0 AS ServiceCost
    FROM
        Tools
    WHERE
        ToolId NOT IN (SELECT DISTINCT
                ToolId
            FROM
                ServiceTool)
    GROUP BY ToolId) E
    WHERE
        T.ToolId = E.ToolId) P) K
ORDER BY TotalProfit DESC";


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
				<h2>Inventory Report</h2>
				<hr>
				  <?php
				  
				  require_once 'HTML/Table.php';

					$data = Array();

					    for ($i = 0; $i < count($rows); $i++) {

					    		if($rows[$i]['ToolSold']==1)
					    		{
					    			$totalProfit = floatval($rows[$i]['TotalProfit']);
					    			$tempCost = (floatval($rows[$i]['PurchasePrice'])/2.0);
					    			$totalProfit = $totalProfit + $tempCost;
					    		}
					    		else
					    		{
					    			$totalProfit = floatval($rows[$i]['TotalProfit']);
					    		}

					         	$tempArray = array($rows[$i]['ToolId'],
					         					 $rows[$i]['AbbDescription'],
					         					 $rows[$i]['DaysRented'],
					         					 $rows[$i]['RentalPricePerDay'],
					         					 $rows[$i]['PurchasePrice'],
					         					 $rows[$i]['ToolSold'],
					         					 $rows[$i]['ServiceCost'],
					         					 $rows[$i]['RentalProfit'],
					         					 $rows[$i]['CostOfTool'],
					         					 $totalProfit);


					         	array_push($data, $tempArray);
					    }

					$attrs = array('width' => '1000');
					$table = new HTML_Table($attrs);
					$table->setAutoGrow(true);
					$table->setAutoFill('n/a');

					for ($nr = 0; $nr < count($data); $nr++) {
					  $table->setHeaderContents($nr+1, 0, (string)$nr);
					  for ($i = 0; $i < 11; $i++) {
					    if ('' != $data[$nr][$i]) {
					      $table->setCellContents($nr+1, $i+1, $data[$nr][$i]);
					    }
					  }
					}

					$table->setHeaderContents(0, 0, '###');
					$table->setHeaderContents(0, 1, 'Tool ID');
					$table->setHeaderContents(0, 2, 'Abbr. Description');
					$table->setHeaderContents(0, 3, 'Days Rented');
					$table->setHeaderContents(0, 4, 'Rental Price Per Day');
					$table->setHeaderContents(0, 5, 'Purchase Price');
					$table->setHeaderContents(0, 6, 'Tool Sold');
					$table->setHeaderContents(0, 7, 'Service Cost');
					$table->setHeaderContents(0, 8, 'Rental Profit');
					$table->setHeaderContents(0, 9, 'Cost Of Tool');
					$table->setHeaderContents(0, 10, 'Total Profit');

					$hrAttrs = array('bgcolor' => 'silver');
					$table->setRowAttributes(0, $hrAttrs, true);
					$table->setColAttributes(0, $hrAttrs);

					echo $table->toHtml();
			
				  ?>
				  	<br>
				  	<br>
					<form action="inventoryReport.php" method="post">
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
