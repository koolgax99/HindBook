<?php
include("includes/header.php");


if (isset($_REQUEST['post'])) {
    $uploadOk = 1;
    $errorMessage = "";
    $post_text = $_POST['post_text'];
    $videoName = $_FILES['uploadvideo']['name'];

    if ($videoName != "") {
        $type = $_FILES['uploadvideo']['type'];
        $targetDir = "assets/videos/posts/";
        $videoName = $targetDir . uniqid() . basename($videoName);


        if ($uploadOk) {
            if (move_uploaded_file($_FILES['uploadvideo']['tmp_name'], $videoName)) {
                echo "Your video " . $videoName . " has been successfully uploaded";
            } else {
                $uploadOk = 0;
            }
        }
    }

    if ($uploadOk) {
        $post = new PostVideo($con, $userLoggedIn);
        $post->submitVideo($post_text, $videoName);
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

            .col-lg-3 {
                margin-top: 100px;
            }
        }

        video {
            width: 100% !important;
            height: auto !important;
        }

        #comment_iframe {
            max-height: 250px;
            width: 100%;
            margin-top: 5px;
        }
    </style>
</head>

<main style="margin-top: 40px;">
    <div class="container">
        <div class="row">
            <div class="col-lg-3">
                <div class="card shadow p-3 mb-2 bg-white rounded" style="padding: 10px; border-bottom:solid #99DDFF; border-left:solid #99DDFF;">
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
                <div class="card shadow p-3 mb-2 bg-white rounded" style="padding: 10px;">
                    <form name="video" enctype="multipart/form-data" method="post" action="">
                        <input name="MAX_FILE_SIZE" value="100000000000000" type="hidden" />

                        <div class="form-group">
                            <input type="file" name="uploadvideo" />
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
                url: "includes/handlers/ajax_load_videos.php",
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