<?php
include("includes/header.php");

if (isset($_GET['username'])) {
	$username = $_GET['username'];
} else {
	$username = $userLoggedIn;
}
?>

<head>
	<style>
		@media (max-width: 768px) {
			.col-lg-3 {
				margin-top: 100px;
			}
		}
	</style>
</head>

<main style="margin-top: 40px;">
	<div class="container">
		<div class="row">
			<div class="col-lg-3">
				<div class="card shadow p-3 mb-2 bg-white rounded" style="padding: 10px;">
					<div class="user_details column">
						<div class="row">
							<?php
							$user_obj = new User($con, $username);
							foreach ($user_obj->getFriendsList() as $friend) {
								$friend_obj = new User($con, $friend);
								echo "<a href='$friend'>
										<img class='profilePicSmall' src='" .
									$friend_obj->getProfilePic() .
									"'>"
									. $friend_obj->getFirstAndLastName() .
									"</a>
									<br>";
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</main>