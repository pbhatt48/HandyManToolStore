<?php

/* connect to database */	
$connect = mysql_connect("127.0.0.1:3306", "hmtuser", "password");
if (!$connect) {
	die("Failed to connect to database");
}
mysql_select_db("handymantool") or die( "Unable to select database");

$errorMsg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST')
{

	$button =$_POST['button'];
	
	if($button=="Enter")
	{
		if (empty($_POST['login']) or empty($_POST['password'])) 
		{
			$errorMsg = "Please provide both login and password.";		
		}
		else 
		{  
			
			$login = mysql_real_escape_string($_POST['login']);
			$password = mysql_real_escape_string($_POST['password']);
			$userType = $_POST['userType'];

			$query="";


				if($userType=="clerk")
				{
					$query = "SELECT * FROM clerk WHERE ClerkLogin = '$login' AND Password = '$password'";
				}
				else
				{
					$query = "SELECT * FROM customer WHERE CustomerLogin = '$login' AND Password = '$password'";
				}

				$result = mysql_query($query);
				
				if (mysql_num_rows($result) == 0) {
					/* login failed */
					$errorMsg = "Login failed.  Please try again.";
					
				}
				else {
					/* login successful */
					session_start();
					$_SESSION['login'] = $login;
					
					/* redirect to the profile page */
					if($userType=="clerk")
					{
						header('Location: clerkMainMenu.php');
						exit();
					}
					else
					{
						header('Location: customerMainMenu.php');
						exit();
					}
				}
		}
	}
	else
	{
		if($button=="Customer Sign Up")
		{
			/* redirect to the profile page */
			header('Location: createCustomerProfile.php');
			exit();
		}
		else
		{
			/* redirect to the profile page */
			header('Location: createClerkProfile.php');
			exit();
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
					<h1>Welcome to HandyManTool</h1>

					<form action="login.php" method="post">

						<div class="login_form_row">
							<label class="login_label"><strong>Login:</strong></label>
							<input type="text" name="login" class="login_input" />
						</div>
										
						<div class="login_form_row">
							<label class="login_label"><strong>Password:</strong></label>
							<input type="password" name="password" class="login_input" />
						</div>

						<input type="radio" name="userType" value="clerk">Clerk
   						<input type="radio" name="userType" value="customer">Customer
   						<br>
						<input type="submit" name="button" value="Enter"> 
						<br>
						<h4>Sign Up: </h4>
						<input type="submit" name="button" value="Clerk Sign Up"> 
						<br>
						<input type="submit" name="button" value="Customer Sign Up">   
						                                                            
					<form/>
				  
					<?php
					if (!empty($errorMsg)) {
						print "<div class='login_form_row' style='color:red'>$errorMsg</div>";
					}
					?>                    	   
				</div>
				<div class="clear"><br/></div> 
			</div>    
		</div>
	</body>
</html>