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
    Name, PickUps, DropOffs, SUM(PickUps + DropOffs) AS Total
FROM
    (SELECT 
        Name, SUM(PickUps) AS PickUps, SUM(DropOffs) AS DropOffs
    FROM
        ((SELECT 
        CONCAT(Clerk.FirstName, ' ', Clerk.LastName) AS Name,
            COUNT(*) AS Pickups,
            0 AS DropOffs
    FROM
        Clerk
    WHERE
        Clerk.ClerkLogin IN (SELECT 
                PickUpClerk
            FROM
                Reservation)
    GROUP BY Clerk.ClerkLogin) UNION ALL (SELECT 
        CONCAT(Clerk.FirstName, ' ', Clerk.LastName) AS Name,
            0 AS Pickups,
            COUNT(*) AS DropOffs
    FROM
        Clerk
    WHERE
        Clerk.ClerkLogin IN (SELECT 
                DropOffClerk
            FROM
                Reservation)
    GROUP BY Clerk.ClerkLogin)) T
    GROUP BY NAME) S
GROUP BY Name
ORDER BY Total DESC";


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
				<h2>Clerk Report</h2>
				<hr>
				  <?php
				  
				  require_once 'HTML/Table.php';

					$data = Array();

					    for ($i = 0; $i < count($rows); $i++) {
					         	$tempArray = array($rows[$i]['Name'],
					         					 $rows[$i]['PickUps'],
					         					 $rows[$i]['DropOffs'],
					         					 $rows[$i]['Total']);
					         	array_push($data, $tempArray);
					    }

					$attrs = array('width' => '600');
					$table = new HTML_Table($attrs);
					$table->setAutoGrow(true);
					$table->setAutoFill('n/a');

					for ($nr = 0; $nr < count($data); $nr++) {
					  $table->setHeaderContents($nr+1, 0, (string)$nr);
					  for ($i = 0; $i < 5; $i++) {
					    if ('' != $data[$nr][$i]) {
					      $table->setCellContents($nr+1, $i+1, $data[$nr][$i]);
					    }
					  }
					}

					$table->setHeaderContents(0, 0, '###');
					$table->setHeaderContents(0, 1, 'Name');
					$table->setHeaderContents(0, 2, 'Pick-Ups');
					$table->setHeaderContents(0, 3, 'Drop-Offs');
					$table->setHeaderContents(0, 4, 'Total');

					$hrAttrs = array('bgcolor' => 'silver');
					$table->setRowAttributes(0, $hrAttrs, true);
					$table->setColAttributes(0, $hrAttrs);

					echo $table->toHtml();

					print "<br>";
					print "<br>";
					if(count($data)>0)
					{
					  	print "<h1>!!! CONGRATULATIONS !!!</h1>";
					  	print "<h3>Clerk Of The Month: </h3>";
					  	print $rows[0]['Name'];
					  	print "<br>";
						print "<br>";
					}
				  ?>

					<form action="clerkReport.php" method="post">
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
