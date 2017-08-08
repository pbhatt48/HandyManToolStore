<?php

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

$errorMsg = "";
$successMsg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$button =$_POST['button'];

	if($button=="Back To Login Page")
	{
		/* redirect to the View Profile page */
		header('Location: login.php');
		exit();
	}
	
	if (empty($_POST['login']) or empty($_POST['password']) or empty($_POST['confirmPassword']) or
		empty($_POST['firstName']) or empty($_POST['lastName'])) 
	{
		$errorMsg = "Please provide Login, Password, Password Confirmation, First Name and Last Name";		
	}
	else 
	{  
		
		$login = mysql_real_escape_string($_POST['login']);
		$password = mysql_real_escape_string($_POST['password']);
		$confirmPassword = mysql_real_escape_string($_POST['confirmPassword']);
		$firstName = mysql_real_escape_string($_POST['firstName']);
		$lastName = mysql_real_escape_string($_POST['lastName']);
		$homePhoneContryCode = mysql_real_escape_string($_POST['homePhoneContryCode']);
		$homePhoneNumber = mysql_real_escape_string($_POST['homePhoneNumber']);
		$workPhoneContryCode = mysql_real_escape_string($_POST['workPhoneContryCode']);
		$workPhoneNumber = mysql_real_escape_string($_POST['workPhoneNumber']);
		$address = mysql_real_escape_string($_POST['address']);

		if($password == $confirmPassword)
		{

			$query = "SELECT * FROM customer WHERE CustomerLogin = '$login'";
			
			$result = mysql_query($query);
					
			if (mysql_num_rows($result) == 0)
			{
				
				$query = "INSERT INTO customer (CustomerLogin,Password,FirstName,LastName,Address,
					WorkPhoneCountryCode,WorkPhoneLocalNumber,HomePhoneCountryCode,HomePhoneLocalNumber) 
				VALUES ('$login','$password','$firstName','$lastName','$address','$workPhoneContryCode',
					'$workPhoneNumber','$homePhoneContryCode','$homePhoneNumber')";		
				$result = mysql_query($query);

				if($result==1)
				{
					$successMsg = "Success Creating Customer Profile";
					/* redirect to the login page */
					header('Location: login.php');
					exit();
				}
				else
				{
					$errorMsg = "Customer Profile Creation Failed. Error Inserting to Database";
				}
			}
			else
			{
				$errorMsg = "Customer Profile Creation Failed. Customer Login Already Exists";
			}
		}
		else
		{
			$errorMsg = "Customer Profile Creation Failed. Passwords do not match";
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
				<h2>Create a Profile</h2>
				<h4>Handyman Tools Rental requires a valid profile for every user before they can make reservations.</h4>

					<form action="createCustomerProfile.php" method="post">

						<div class="create_form_row">
							<label class="login_label">Email Address(Login):</label>
							<input type="text" name="login" class="login_input" />
						</div>
										
						<div class="create_form_row">
							<label class="login_label">Password:</label>
							<input type="password" name="password" class="login_input" />
						</div>

						<div class="create_form_row">
							<label class="login_label">Confirm Password:</label>
							<input type="password" name="confirmPassword" class="login_input" />
						</div>

						<div class="create_form_row">
							<label class="login_label">First Name:</label>
							<input type="text" name="firstName" class="login_input" />
						</div>

						<div class="create_form_row">
							<label class="login_label">Last Name:</label>
							<input type="text" name="lastName" class="login_input" />
						</div>

						<div class="create_form_row">
							<label class="login_label">Home Phone:</label>
							<input type="text" name="homePhoneContryCode" class="login_input" />
							<input type="text" name="homePhoneNumber" class="login_input" />
						</div>

						<div class="create_form_row">
							<label class="login_label">Work Phone:</label>
							<input type="text" name="workPhoneContryCode" class="login_input" />
							<input type="text" name="workPhoneNumber" class="login_input" />
						</div>

						<div class="create_form_row">
							<label class="login_label">Address:</label>
							<input type="text" name="address" class="login_input" />
						</div>

						<input type="submit" name="button" value="Submit">
						<br>
						<br>
						<input type="submit" name="button" value="Back To Login Page">                                                                
					<form/>
				  
					<?php
					if (!empty($errorMsg)) {
						print "<div class='create_form_row' style='color:red'>$errorMsg</div>";
					}
					if (!empty($successMsg)) {
						print "<div class='create_form_row' style='color:black'>$successMsg</div>";
					}
					?>                    	   
				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>