<?php
include("includes/header.php");
$message_obj = new Message($con, $userLoggedIn);

if (isset($_GET['u']))
	$user_to = $_GET['u'];
else {
	$user_to = $message_obj->getMostRecentUser();
	if ($user_to == false)
		$user_to = 'new';
}

if ($user_to != "new")
	$user_to_obj = new User($con, $user_to);

if (isset($_POST['post_message'])) {
	if (isset($_POST['message_body'])) {
		$body = mysqli_real_escape_string($con, $_POST['message_body']);
		$date = date("Y-m-d H:i:s");
		$message_obj->sendMessage($user_to, $body, $date);
	}
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
				<div class="card shadow p-3 mb-5 bg-white rounded" style="padding: 10px; border-bottom:solid #99DDFF; border-left:solid #99DDFF;">
					<div class="user_details column">
						<div class="row">
							<div class="col">
								<a href="<?php echo $userLoggedIn; ?>"> <img src="<?php echo $user['profile_pic']; ?>"> </a>
							</div>
							<div class="col">
								<a href="<?php echo $userLoggedIn; ?>">
									<?php
									echo $user['first_name'] . " " . $user['last_name'];
									?>
								</a>
								<br>
								<?php echo "Posts: " . $user['num_posts'] . "<br>";
								echo "Likes: " . $user['num_likes'];
								?>
							</div>
						</div>
					</div>
				</div>
				<br>
				<div class="card shadow mb-5 bg-white rounded" style=" border-bottom:solid #99DDFF; border-left:solid #99DDFF;">
					<div class="card-body">
						<h4>Conversations</h4>

						<div class="loaded_conversations">
							<?php echo $message_obj->getConvos(); ?>
						</div>
						<br>
						<a href="messages.php?u=new">New Message</a>
					</div>
				</div>
			</div>
			<div class="col-1">
			</div>
			<div class="col-lg-8">
				<div class="card shadow p-1 mb-5 bg-white rounded" style="padding: 10px;">
					<div>
						<div class="card shadow p-1 mb-5 bg-white rounded">
							<?php
							if ($user_to != "new") {
								echo "<h4>You and <a href='$user_to'>" . $user_to_obj->getFirstAndLastName() . "</a></h4><hr><br>";

								echo "<div class='loaded_messages' id='scroll_messages'>";
								echo $message_obj->getMessages($user_to);
								echo "</div>";
							} else {
								echo "<h4>New Message</h4>";
							}
							?>
						</div>
						<div class="message_post">
							<form action="" method="POST">
								<?php
								if ($user_to == "new") {
									echo "Select the friend you would like to message <br><br>";
								?>
									To: <input type='text' onkeyup='getUsers(this.value, "<?php echo $userLoggedIn; ?>")' name='q' placeholder='Name' autocomplete='off' id='seach_text_input'>

								<?php
									echo "<div class='results'></div>";
								} else {
									echo "<textarea name='message_body' class='form-control' id='message_textarea' placeholder='Write your message ...'></textarea>";
									echo "<button type='submit' name='post_message' class='btn btn-primary' id='message_submit' value='Send'>Send</button>";
								}

								?>
							</form>
						</div>

						<script>
							var div = document.getElementById("scroll_messages");
							div.scrollTop = div.scrollHeight;
						</script>
					</div>

				</div>
			</div>
		</div>
	</div>
</main>