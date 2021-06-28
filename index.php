<?php
include("includes/header.php");

if (isset($_POST['post'])) {

	$uploadOk = 1;
	$imageName = $_FILES['fileToUpload']['name'];
	$errorMessage = "";

	if ($imageName != "") {
		$targetDir = "assets/images/posts/";
		$imageName = $targetDir . uniqid() . basename($imageName);
		$imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

		if ($_FILES['fileToUpload']['size'] > 10000000) {
			$errorMessage = "Sorry your file is too large";
			$uploadOk = 0;
		}

		if (strtolower($imageFileType) != "jpeg" && strtolower($imageFileType) != "png" && strtolower($imageFileType) != "jpg") {
			$errorMessage = "Sorry, only jpeg, jpg and png files are allowed";
			$uploadOk = 0;
		}

		if ($uploadOk) {

			if (move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName)) {
				//image uploaded okay
			} else {
				//image did not upload
				$uploadOk = 0;
			}
		}
	}

	if ($uploadOk) {
		$post = new Post($con, $userLoggedIn);
		$post->submitPost($_POST['post_text'], 'none', $imageName);
	} else {
		echo "<div style='text-align:center;' class='alert alert-danger'>
				$errorMessage
			</div>";
	}
}
?>


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
				<div class="card shadow p-3 mb-5 bg-white rounded">
					<div class="card-body">
						<h4>Popular</h4>
						<div class="trends">
							<?php
							$query = mysqli_query($con, "SELECT * FROM trends ORDER BY hits DESC LIMIT 9");

							foreach ($query as $row) {

								$word = $row['title'];
								$word_dot = strlen($word) >= 14 ? "..." : "";

								$trimmed_word = str_split($word, 14);
								$trimmed_word = $trimmed_word[0];

								echo "<div style'padding: 1px'>";
								echo $trimmed_word . $word_dot;
								echo "<br></div><br>";
							}

							?>
						</div>
					</div>
				</div>
			</div>
			<div class="col-1">
			</div>
			<div class="col-lg-8">
				<div class="card shadow p-3 mb-5 bg-white rounded" style="padding: 10px;">
					<!-- <div class="main_column column">

						<div class="posts_area"></div>
						<button id="load_more">Load More Posts</button> 
					<img id="loading" src="assets/images/icons/loading.gif">
				</div> -->

					<div>
						<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
							<div class="row">
								<input type="file" name="fileToUpload" id="fileToUpload">
							</div>
							<br>
							<div class="row">
								<div class="col-10">
									<textarea name="post_text" id="post_text" style="width:100%; border-radius:5px" placeholder="Got something to say?"></textarea>
								</div>
								<div class="col">
									<button class="btn btn-primary" type="submit" name="post" id="post_button" value="Post">Post</button>
								</div>
							</div>
							<hr>
						</form>
					</div>

					<div>
						<div class="posts_area"></div>
						<!-- <button id="load_more">Load More Posts</button>
						<img id="loading" src="assets/images/icons/loading.gif"> -->
					</div>

				</div>
			</div>
		</div>
	</div>
</main>

<script>
	$(function() {

		var userLoggedIn = '<?php echo $userLoggedIn; ?>';
		var inProgress = false;

		loadPosts(); //Load first posts

		$(window).scroll(function() {
			var bottomElement = $(".status_post").last();
			var noMorePosts = $('.posts_area').find('.noMorePosts').val();

			// isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
			if (isElementInView(bottomElement[0]) && noMorePosts == 'false') {
				loadPosts();
			}
		});

		function loadPosts() {
			if (inProgress) { //If it is already in the process of loading some posts, just return
				return;
			}

			inProgress = true;
			$('#loading').show();

			var page = $('.posts_area').find('.nextPage').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

			$.ajax({
				url: "includes/handlers/ajax_load_posts.php",
				type: "POST",
				data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
				cache: false,

				success: function(response) {
					$('.posts_area').find('.nextPage').remove(); //Removes current .nextpage 
					$('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage 
					$('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage 

					$('#loading').hide();
					$(".posts_area").html(response);

					inProgress = false;
				}
			});
		}

		//Check if the element is in view
		function isElementInView(el) {
			var rect = el.getBoundingClientRect();

			return (
				rect.top >= 0 &&
				rect.left >= 0 &&
				rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) && //* or $(window).height()
				rect.right <= (window.innerWidth || document.documentElement.clientWidth) //* or $(window).width()
			);
		}
	});
</script>
</div>
</body>

</html>