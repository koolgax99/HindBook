<?php
include("includes/header.php");
include("includes/form_handlers/settings_handler.php");
?>

<main style="margin-top: 40px;">
	<div class="container" style="max-width: 600px;">
		<div class="row">
			<div class="card shadow p-3 mb-5 bg-white rounded" style="padding: 10px; border:solid #1778F2;  align-items:center">

				<h4>Account Settings</h4>
				<?php
				echo "<img class='img-fluid' src='" . $user['profile_pic'] . "'  style='width:100px;height:100px;'>";
				?>
				<br>
				<a href="upload.php">Upload new profile picture</a> <br>

				Modify the values and click 'Update Details'

				<?php
				$user_data_query = mysqli_query($con, "SELECT first_name, last_name, email FROM users WHERE username='$userLoggedIn'");
				$row = mysqli_fetch_array($user_data_query);

				$first_name = $row['first_name'];
				$last_name = $row['last_name'];
				$email = $row['email'];
				?>
				<div class="col">
					<form action="settings.php" method="POST">
						<div class="form-group row">
							<label for="settings_input" class="col-sm-4 col-form-label">First Name:</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" name="first_name" value="<?php echo $first_name; ?>" id="settings_input"><br>
							</div>
						</div>
						<div class="form-group row">
							<label for="settings_input" class="col-sm-4 col-form-label">Last Name:</label>
							<div class="col-sm-7">
								<input type="text" name="last_name" class="form-control" value="<?php echo $last_name; ?>" id="settings_input"><br>
							</div>
						</div>
						<div class="form-group row">
							<label for="settings_input" class="col-sm-4 col-form-label">Email:</label>
							<div class="col-sm-7">
								<input type="text" class="form-control" name="email" value="<?php echo $email; ?>" id="settings_input"><br>
							</div>
						</div>
						<?php echo $message; ?>

						<button type="submit" class="btn btn-primary" name="update_details" id="save_details" value="Update Details" class="info settings_submit">Update Details</button>
					</form>
				</div>
				<br>
				<br>
				<h4>Change Password</h4>
				<div class="col">
					<form action="settings.php" method="POST">
						<div class="form-group row">
							<label for="settings_input" class="col-sm-4 col-form-label">Old Password:</label>
							<div class="col-sm-7">
								<input type="password" class="form-control" name="old_password" id="settings_input"><br>
							</div>
						</div>
						<div class="form-group row">
							<label for="settings_input" class="col-sm-4 col-form-label">New Password:</label>
							<div class="col-sm-7">
								<input type="password" class="form-control" name="new_password_1" id="settings_input"><br>
							</div>
						</div>
						<div class="form-group row">
							<label for="settings_input" class="col-sm-4 col-form-label">Confirm New Password:</label>
							<div class="col-sm-7">
								<input type="password" class="form-control" name="new_password_2" id="settings_input"><br>
							</div>
						</div>
						<?php echo $password_message; ?>
						<button type="submit" class="btn btn-primary" name="update_password" id="save_details" value="Update Password" class="info settings_submit">Update Password</button><br>
					</form>
				</div>
				<br>
				<br>
				<h4>Close Account</h4>
				<form action="settings.php" method="POST">
					<button type="submit" class="btn btn-primary" name="close_account" id="close_account" value="Close Account" class="danger settings_submit">Close Account </button>
				</form>
			</div>
		</div>
	</div>
</main>