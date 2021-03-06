<?php
include("includes/header.php");
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

        #comment_iframe {
            max-height: 250px;
            width: 100%;
            margin-top: 5px;
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
                <div>
                    <div class="posts_area"></div>
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
                url: "includes/handlers/ajax_load_products.php",
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