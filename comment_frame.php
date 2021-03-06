<html>

<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="assets/css/comments.css">
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
</head>

<body>

	<style type="text/css">
		* {
			font-size: 12px;
			font-family: Arial, Helvetica, Sans-serif;
		}

		.comment_section {
			padding: 0 5px 5px 5px;
		}

		.comment_section img {
			margin: 0 3px 3px 3px;
			border-radius: 3px;
		}

		#comment_form textarea {
			border-color: #D3D3D3;
			width: 85%;
			height: 35px;
			border-radius: 5px;
			color: #616060;
			font-size: 14px;
			margin: 3px 3px 3px 5px;
		}

		#comment_form input[type="submit"] {
			border: none;
			background-color: #20AAE5;
			color: #156588;
			border-radius: 5px;
			width: 13%;
			height: 35px;
			margin-top: 3px;
			position: absolute;
			font-family: 'Bellota-BoldItalic', sans-serif;
			text-shadow: #73B6E2 0.5px 0.5px 0px;
		}
	</style>

	<?php
	require 'config/config.php';
	include("includes/classes/User.php");
	include("includes/classes/Post.php");
	include("includes/classes/Notification.php");

	if (isset($_SESSION['username'])) {
		$userLoggedIn = $_SESSION['username'];
		$user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
		$user = mysqli_fetch_array($user_details_query);
	} else {
		header("Location: register.php");
	}

	?>
	<script>
		function toggle() {
			var element = document.getElementById("comment_section");

			if (element.style.display == "block")
				element.style.display = "none";
			else
				element.style.display = "block";
		}
	</script>

	<?php
	//Get id of post
	if (isset($_GET['post_id'])) {
		$post_id = $_GET['post_id'];
		$post_type = $_GET['post_type'];
	}

	if ($post_type == 'photo') {
		$user_query = mysqli_query($con, "SELECT added_by, user_to FROM posts WHERE id='$post_id'");
		$row = mysqli_fetch_array($user_query);

		$posted_to = $row['added_by'];
		$user_to = $row['user_to'];
	}

	if ($post_type == 'video') {
		$user_query = mysqli_query($con, "SELECT added_by FROM videos WHERE id='$post_id'");
		$row = mysqli_fetch_array($user_query);

		$posted_to = $row['added_by'];
		$user_to = 'none';
	}

	if (isset($_POST['postComment' . $post_id . $post_type])) {
		$post_body = $_POST['post_body'];
		$post_body = mysqli_escape_string($con, $post_body);
		$date_time_now = date("Y-m-d H:i:s");
		$insert_post = mysqli_query($con, "INSERT INTO comments VALUES (NULL, '$post_body', '$userLoggedIn', '$posted_to','$post_type', '$date_time_now', 'no','$post_id')");

		if ($posted_to != $userLoggedIn) {
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $posted_to, "comment");
		}

		if ($user_to != 'none' && $user_to != $userLoggedIn) {
			$notification = new Notification($con, $userLoggedIn);
			$notification->insertNotification($post_id, $user_to, "profile_comment");
		}


		$get_commenters = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' AND post_type='$post_type'");
		$notified_users = array();
		while ($row = mysqli_fetch_array($get_commenters)) {

			if (
				$row['posted_by'] != $posted_to && $row['posted_by'] != $user_to
				&& $row['posted_by'] != $userLoggedIn && !in_array($row['posted_by'], $notified_users)
			) {

				$notification = new Notification($con, $userLoggedIn);
				$notification->insertNotification($post_id, $row['posted_by'], "comment_non_owner");

				array_push($notified_users, $row['posted_by']);
			}
		}
		echo "<p>Comment Posted! </p>";
	}
	?>
	<form action="comment_frame.php?post_id=<?php echo $post_id; ?>&post_type=<?php echo $post_type; ?>" id="comment_form" name="postComment<?php echo $post_id . $post_type; ?>" method="POST">
		<div class="form-row">
			<div class="col-10">
				<textarea name="post_body" id="post_body" style="width:100%; border-radius:5px" placeholder="Got something to say?"></textarea>
			</div>
			<div class="col">
				<button class="btn btn-primary" type="submit" name="postComment<?php echo $post_id . $post_type; ?>" id="post_button" style="border-radius:20px; margin:5px">Comment</button>
			</div>
		</div>
	</form>

	<!-- Load comments -->
	<?php
	$get_comments = mysqli_query($con, "SELECT * FROM comments WHERE post_id='$post_id' AND post_type='$post_type' ORDER BY id ASC");
	$count = mysqli_num_rows($get_comments);

	if ($count != 0) {

		while ($comment = mysqli_fetch_array($get_comments)) {

			$comment_body = $comment['post_body'];
			$posted_to = $comment['posted_to'];
			$posted_by = $comment['posted_by'];
			$date_added = $comment['date_added'];
			$removed = $comment['removed'];

			//Timeframe
			$date_time_now = date("Y-m-d H:i:s");
			$start_date = new DateTime($date_added); //Time of post
			$end_date = new DateTime($date_time_now); //Current time
			$interval = $start_date->diff($end_date); //Difference between dates 
			if ($interval->y >= 1) {
				if ($interval->y == 1)
					$time_message = $interval->y . " year ago"; //1 year ago
				else
					$time_message = $interval->y . " years ago"; //1+ year ago
			} else if ($interval->m >= 1) {
				if ($interval->d == 0) {
					$days = " ago";
				} else if ($interval->d == 1) {
					$days = $interval->d . " day ago";
				} else {
					$days = $interval->d . " days ago";
				}


				if ($interval->m == 1) {
					$time_message = $interval->m . " month" . $days;
				} else {
					$time_message = $interval->m . " months" . $days;
				}
			} else if ($interval->d >= 1) {
				if ($interval->d == 1) {
					$time_message = "Yesterday";
				} else {
					$time_message = $interval->d . " days ago";
				}
			} else if ($interval->h >= 1) {
				if ($interval->h == 1) {
					$time_message = $interval->h . " hour ago";
				} else {
					$time_message = $interval->h . " hours ago";
				}
			} else if ($interval->i >= 1) {
				if ($interval->i == 1) {
					$time_message = $interval->i . " minute ago";
				} else {
					$time_message = $interval->i . " minutes ago";
				}
			} else {
				if ($interval->s < 30) {
					$time_message = "Just now";
				} else {
					$time_message = $interval->s . " seconds ago";
				}
			}
			$user_obj = new User($con, $posted_by);
	?>
			<div class="comment_section">
				<a href="<?php echo $posted_by ?>" target="_parent"><img src="<?php echo $user_obj->getProfilePic(); ?>" title="<?php echo $posted_by; ?>" style="float:left;" height="30"></a>
				<a href="<?php echo $posted_by ?>" target="_parent"> <b> <?php echo $user_obj->getFirstAndLastName(); ?> </b></a>
				&nbsp;&nbsp;&nbsp;&nbsp; <?php echo $time_message . "<br>" . $comment_body; ?>
				<hr>
			</div>
	<?php

		}
	} else {
		echo "<center><br><br>No Comments to Show!</center>";
	}

	?>
</body>

</html>