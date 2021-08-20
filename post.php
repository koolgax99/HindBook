<?php
include("includes/header.php");

if (isset($_GET['id'])) {
	$id = $_GET['id'];
} else {
	$id = 0;
}
?>

<head>
	<style>
		#comment_iframe {
			max-height: 250px;
			width: 100%;
			margin-top: 5px;
		}

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
				<div class="card shadow p-3 mb-5 bg-white rounded" style="padding: 10px;">
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
			</div>
			<div class="col-1">
			</div>
			<div class="col-lg-8">
				<div>
					<div class="posts_area">
						<?php
						$post = new Post($con, $userLoggedIn);
						$post->getSinglePost($id);
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</main>