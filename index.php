<?php
include("includes/header.php");

if (isset($_POST['post'])) {

	$uploadOk = 1;
	$imageName = $_FILES['fileToUpload']['name'];
	$errorMessage = "";
	if ($_POST['postType'] == "shop") {
		$price = $_POST['productPrice'];
		$name = $_POST['productName'];
		$description = $_POST['post_text'];
		$post_text = 'Name: ' . $name
			. "\n\n"
			. 'Price: ' . $price
			. "\n\n"
			. 'Description: ' . $description;
	} else {
		$post_text = $_POST['post_text'];
	}

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
		$post->submitPost($post_text, 'none', $imageName);
	} else {
		echo "<div style='text-align:center;' class='alert alert-danger'>
				$errorMessage
			</div>";
	}
}
?>

<head>
	<style>
		.footer {
			position: fixed;
			left: 0;
			bottom: 0;
			width: 100%;
			background-color: white;
			color: white;
			text-align: center;
			z-index: -1;
		}

		.waves {
			position: static;
			width: 100%;
			height: 15vh;
			margin-bottom: -7px;
			/*Fix for safari gap*/
			min-height: 100px;
			max-height: 150px;
		}


		/* Animation */

		.parallax>use {
			animation: move-forever 25s cubic-bezier(.55, .5, .45, .5) infinite;
		}

		.parallax>use:nth-child(1) {
			animation-delay: -2s;
			animation-duration: 7s;
		}

		.parallax>use:nth-child(2) {
			animation-delay: -3s;
			animation-duration: 10s;
		}

		.parallax>use:nth-child(3) {
			animation-delay: -4s;
			animation-duration: 13s;
		}

		.parallax>use:nth-child(4) {
			animation-delay: -5s;
			animation-duration: 20s;
		}

		@keyframes move-forever {
			0% {
				transform: translate3d(-90px, 0, 0);
			}

			100% {
				transform: translate3d(85px, 0, 0);
			}
		}

		/*Shrinking for mobile*/
		@media (max-width: 768px) {
			.waves {
				height: 40px;
				min-height: 40px;
			}

			.content {
				height: 30vh;
			}

			h1 {
				font-size: 24px;
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
				<div class="card shadow p-3 mb-5 bg-white rounded" style=" border-bottom:solid #99DDFF; border-left:solid #99DDFF;">
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
					<form class="post_form" action="index.php" method="POST" enctype="multipart/form-data">
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<label class="input-group-text" for="postType">Choose Post Type</label>
							</div>
							<select class="custom-select" name="postType" id="postType">
								<option selected>Select Option</option>
								<option value="shop">Shop</option>
								<option value="feed">Feed</option>
							</select>
						</div>

						<div id="shopDiv"></div>
						<div class="form-group">
							<input type="file" name="fileToUpload" id="fileToUpload">
						</div>
						<br>
						<div class="form-row">
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
				</div>

			</div>
		</div>
	</div>
	</div>
</main>

<div class="footer">
	<div>
		<svg class="waves" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
			<defs>
				<path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
			</defs>
			<g class="parallax">
				<use xlink:href="#gentle-wave" x="48" y="0" fill="rgba(15, 255, 255,0.7" />
				<use xlink:href="#gentle-wave" x="48" y="3" fill="rgba(13, 73, 205,0.5)" />
				<use xlink:href="#gentle-wave" x="48" y="5" fill="rgba(58, 210, 255,0.3)" />
				<use xlink:href="#gentle-wave" x="48" y="7" fill="#99DDFF" />
			</g>
		</svg>
	</div>
</div>

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

		$('#postType').change(function() {
			if (document.getElementById('postType').value == 'shop') {
				document.getElementById('shopDiv').innerHTML = `<div class="form-row">
						<div class="form-group col-md-6">
							<label for="productPrice">Price of the Product (INR) </label>
							<input type="number" name="productPrice" class="form-control" id="productPrice" placeholder="5000">
						</div>
						<br>
						<div class="form-group col-md-6">
							<label for="productName">Name of the Product</label>
							<input type="text" name="productName" class="form-control" id="productName" placeholder="Zara Mens TShirt">
						</div>
					</div>
					<br>`;
			} else {
				document.getElementById('shopDiv').innerHTML = ``;
			}
		})
	});
</script>
</div>
</body>

</html>