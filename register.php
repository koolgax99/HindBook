<?php
require 'config/config.php';
require 'includes/form_handlers/register_handler.php';
require 'includes/form_handlers/login_handler.php';
?>

<!DOCTYPE html>
<html>

<head>
	<title>Welcome to HindBook!</title>
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="stylesheet" type="text/css" href="assets/css/register_style.css">
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
	<script src="assets/js/register.js"></script>
</head>

<body>
	<?php
	if (isset($_POST['register_button'])) {
		echo '
		<script>
		$(document).ready(function() {
			$("#first").hide();
			$("#second").show();
		});
		</script>
		';
	}
	?>

	<div class="wrapper">
		<div class="login_box" style="background-color: pink; text-align:center"> 
			<!-- <div class="login_header" style="background-color: pink;"> -->
			<h1 style="font-size:35px; font-family: 'Staatliches', cursive; font-weight: 600;letter-spacing: 2px;">H!ndB<span style="color:#3FD2C7;">oo</span>k</h1>
			<!-- </div> -->
		</div>
		<div class="login_box">
			<div class="login_header">
				<h1>Login</h1>
				<p style="font-family: 'Montserrat', sans-serif;; color:black;">Login or sign up below!</p>
			</div>
			<br>
			<div class="form" id="first">
				<form action="register.php" method="POST">
					<div class="input_field">
						<input type="email" class="input" name="log_email" placeholder="Email Address" value="<?php
																												if (isset($_SESSION['log_email'])) {
																													echo $_SESSION['log_email'];
																												}
																												?>" required>
					</div>
					<div class="input_field">
						<input type="password" class="input" name="log_password" placeholder="Password">
						<br>
						<?php if (in_array("Email or password was incorrect<br>", $error_array)) echo  "Email or password was incorrect<br>"; ?>
					</div>
					<div class="login_button">
						<input type="submit" class="login_button" name="login_button" value="Login">
					</div>
					<a href="#" id="signup" class="signup" style="font-family: 'Montserrat', sans-serif; color:black; text-align: center"><br>Need an account? Register here!</a>
				</form>
			</div>
			<div class="form" id="second">
				<form action="register.php" method="POST">
					<div class="input_field">

						<input type="text" class="input" name="reg_fname" placeholder="First Name" value="<?php
																											if (isset($_SESSION['reg_fname'])) {
																												echo $_SESSION['reg_fname'];
																											}
																											?>" required>
					</div>
					<?php if (in_array("Your first name must be between 2 and 25 characters<br>", $error_array)) echo "Your first name must be between 2 and 25 characters<br>"; ?>
					<div class="input_field">
						<input type="text" class="input" name="reg_lname" placeholder="Last Name" value="<?php
																											if (isset($_SESSION['reg_lname'])) {
																												echo $_SESSION['reg_lname'];
																											}
																											?>" required>
					</div>
					<?php if (in_array("Your last name must be between 2 and 25 characters<br>", $error_array)) echo "Your last name must be between 2 and 25 characters<br>"; ?>
					<div class="input_field">
						<input type="email" class="input" name="reg_email" placeholder="Email" value="<?php
																										if (isset($_SESSION['reg_email'])) {
																											echo $_SESSION['reg_email'];
																										}
																										?>" required>
					</div>
					<div class="input_field">

						<input type="email" class="input" name="reg_email2" placeholder="Confirm Email" value="<?php
																												if (isset($_SESSION['reg_email2'])) {
																													echo $_SESSION['reg_email2'];
																												}
																												?>" required>
					</div>
					<?php if (in_array("Email already in use<br>", $error_array)) echo "Email already in use<br>";
					else if (in_array("Invalid email format<br>", $error_array)) echo "Invalid email format<br>";
					else if (in_array("Emails don't match<br>", $error_array)) echo "Emails don't match<br>"; ?>
					<div class="input_field">

						<input type="password" class="input" name="reg_password" placeholder="Password" required>
					</div>
					<div class="input_field">

						<input type="password" class="input" name="reg_password2" placeholder="Confirm Password" required>
					</div>
					<?php if (in_array("Your passwords do not match<br>", $error_array)) echo "Your passwords do not match<br>";
					else if (in_array("Your password can only contain english characters or numbers<br>", $error_array)) echo "Your password can only contain english characters or numbers<br>";
					else if (in_array("Your password must be betwen 5 and 30 characters<br>", $error_array)) echo "Your password must be betwen 5 and 30 characters<br>"; ?>
					<div class="signup_button">

						<input type="submit" class="signup_button" name="register_button" value="Register">
					</div>
					<?php if (in_array("<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>", $error_array)) echo "<span style='color: #14C800;'>You're all set! Go ahead and login!</span><br>"; ?>
					<a href="#" id="signin" class="signin"><br>Already have an account? Sign in here!</a>
				</form>
			</div>
		</div>
	</div>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.11.2/js/all.min.js" integrity="sha256-qM7QTJSlvtPSxVRjVWNM2OfTAz/3k5ovHOKmKXuYMO4=" crossorigin="anonymous"></script>

</html>