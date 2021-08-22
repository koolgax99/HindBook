<html>

<head>
    <title></title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/comments.css">
    <script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/demo.js"></script>
    <script src="assets/js/jquery.Jcrop.js"></script>
    <script src="assets/js/jcrop_bits.js"></script>
    <link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />
</head>

<body>

    <style type="text/css">
        * {
            font-family: Arial, Helvetica, Sans-serif;
        }

        body {
            background-color: #fff;
        }

        form {
            position: absolute;
            top: 0;
        }
    </style>

    <?php
    require 'config/config.php';
    include("includes/classes/User.php");
    include("includes/classes/PostVideo.php");

    if (isset($_SESSION['username'])) {
        $userLoggedIn = $_SESSION['username'];
        $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
        $user = mysqli_fetch_array($user_details_query);
    } else {
        header("Location: register.php");
    }

    //Get id of post
    if (isset($_GET['post_id'])) {
        $post_id = $_GET['post_id'];
    }

    $get_likes = mysqli_query($con, "SELECT likes, added_by FROM videos WHERE id='$post_id'");
    $row = mysqli_fetch_array($get_likes);
    $total_likes = $row['likes'];
    $user_liked = $row['added_by'];

    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$user_liked'");
    $row = mysqli_fetch_array($user_details_query);
    $total_user_likes = $row['num_likes'];

    //Like button
    if (isset($_POST['like_button'])) {
        $total_likes++;
        $query = mysqli_query($con, "UPDATE videos SET likes='$total_likes' WHERE id='$post_id'");
        $total_user_likes++;
        $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
        $insert_user = mysqli_query($con, "INSERT INTO likes VALUES (NULL, '$userLoggedIn','0','$post_id')");
    }
    //Unlike button
    if (isset($_POST['unlike_button'])) {
        $total_likes--;
        $query = mysqli_query($con, "UPDATE videos SET likes='$total_likes' WHERE id='$post_id'");
        $total_user_likes--;
        $user_likes = mysqli_query($con, "UPDATE users SET num_likes='$total_user_likes' WHERE username='$user_liked'");
        $insert_user = mysqli_query($con, "DELETE FROM likes WHERE username='$userLoggedIn' AND video_post_id='$post_id'");
    }

    //Check for previous likes
    $check_query = mysqli_query($con, "SELECT * FROM likes WHERE username='$userLoggedIn' AND video_post_id='$post_id'");
    $num_rows = mysqli_num_rows($check_query);

    if ($num_rows > 0) {
        echo '<form action="like_video.php?post_id=' . $post_id . '" method="POST">
				<br>
				<button type="submit" class="btn btn-primary" name="unlike_button" value="Unlike">
				<i class="fa fa-thumbs-down fa-lg"></i> Unlike ' . $total_likes . '
				</button>
			</form>
		';
    } else {
        echo '<form action="like_video.php?post_id=' . $post_id . '" method="POST">
				<br>
				<button type="submit" class="btn btn-primary" name="like_button" value="Like">
					<i class="fa fa-thumbs-up fa-lg"></i> Like ' . $total_likes . '
				</button>
			</form>
		';
    }
    ?>
</body>

</html>