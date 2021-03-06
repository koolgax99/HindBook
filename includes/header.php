<?php
require 'config/config.php';
include("includes/classes/User.php");
include("includes/classes/Post.php");
include("includes/classes/Message.php");
include("includes/classes/Notification.php");
include("includes/classes/PostVideo.php");

if (isset($_SESSION['username'])) {
    $userLoggedIn = $_SESSION['username'];
    $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$userLoggedIn'");
    $user = mysqli_fetch_array($user_details_query);
} else {
    header("Location: register.php");
}
?>

<!doctype html>
<html lang="en">

<head>
    <title>Welcome to HindBook</title>

    <!-- Javascript -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>

    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
    <link rel="stylesheet" type="text/css" href="assets/css/style.css">
    <script src="assets/js/bootbox.min.js"></script>
    <script src="assets/js/jquery.Jcrop.js"></script>
    <script src="assets/js/jcrop_bits.js"></script>
    <link rel="stylesheet" href="assets/css/jquery.Jcrop.css" type="text/css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu+Condensed&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Baloo+2:wght@500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Acme&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Staatliches&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;500&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Quicksand&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="assets/js/demo.js"></script>
    <style>
        body {
            background-color: #E7E9EB !important;
        }

        * {
            box-sizing: border-box;
        }

        /* Style the search field */
        form.example input[type=text] {
            padding: 10px;
            font-size: 17px;
            border: 1px solid grey;
            float: left;
            width: 80%;
            background: #f1f1f1;
        }

        /* Style the submit button */
        form.example button {
            float: left;
            width: 20%;
            padding: 10px;
            font-size: 17px;
            border: 1px solid grey;
            border-left: none;
        }

        form.example button:hover {
            background: #0b7dda;
        }

        /* Clear floats */
        form.example::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <header>
        <nav id="content-desktop" class="navbar navbar-expand-lg navbar-light" style="background-color:#1778F2; border-bottom:ridge; border-color:#99DDFF; box-shadow :#99DDFF;">
            <?php
            //Unread messages 
            $messages = new Message($con, $userLoggedIn);
            $num_messages = $messages->getUnreadNumber();

            //Unread notifications 
            $notifications = new Notification($con, $userLoggedIn);
            $num_notifications = $notifications->getUnreadNumber();

            //Unread notifications 
            $user_obj = new User($con, $userLoggedIn);
            $num_requests = $user_obj->getNumberOfFriendRequests();
            ?>

            <div class="container-fluid">
                <a id="content-desktop" class="navbar-brand" href="index.php" style="color:white; font-size:35px;font-family: 'Roboto', sans-serif; font-weight: 600;letter-spacing: 2px;">hindb<span style="color:#1778F2;background-color: white; border-radius:25px; ">o</span><span style="color:#1778F2;background-color: white; border-radius:25px;">o</span>k</a>

                <ul class="nav navbar-nav me-auto mb-2 mb-lg-0 navbar-items">
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')">
                            <span id="content-desktop" style="color:white; font-family: 'Quicksand', sans-serif; font-size:15px;  float:left; ">Messages&nbsp;</span>
                            <i class="bi bi-chat-dots" style="color:white">&nbsp;</i>
                            <?php
                            if ($num_messages > 0)
                                echo '<span class="notification_badge" id="unread_message">' . $num_messages . '</span>';
                            ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="javascript:void(0);" class="nav-link" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')">
                            <span id="content-desktop" style="color:white; font-family: 'Quicksand', sans-serif; font-size:15px; float:left;">Notifications&nbsp;</span>
                            <i class="bi bi-bell" style="color:white;">&nbsp;</i>
                            <?php
                            if ($num_notifications > 0)
                                echo '<span class="notification_badge" id="unread_notification">' . $num_notifications . '</span>';
                            ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="requests.php" class="nav-link">
                            <span id="content-desktop" style="color:white; font-family: 'Quicksand', sans-serif; font-size:15px;  float:left; ">Requests&nbsp;</span>
                            <i class="bi bi-people-fill" style="color:white;">&nbsp;</i>
                            <?php
                            if ($num_requests > 0)
                                echo '<span class="notification_badge" id="unread_requests">' . $num_requests . '</span>';
                            ?>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="www.whatsapp.com" class="nav-link">
                            <span id="content-desktop" style="color:white; font-family: 'Quicksand', sans-serif; font-size:15px;  float:left;">Whatsapp&nbsp;</span>
                            <i class="bi bi-whatsapp" style="color:white;">&nbsp;</i>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="video.php" class="nav-link">
                            <span id="content-desktop" style="color:white; font-family: 'Quicksand', sans-serif; font-size:15px; float:left;">Videos&nbsp;</span>
                            <i class="bi bi-play-circle" style="color:white;">&nbsp;</i>
                        </a>
                    </li>

                    <li class="nav-item">
                        <a href="includes/handlers/logout.php" class="nav-link">
                            <span id="content-desktop" style="color:white; font-family: 'Quicksand', sans-serif; font-size:15px; float:left;">Logout&nbsp;</span>
                            <i class="bi bi-box-arrow-right" style="color:white;"></i>
                        </a>
                    </li>

                </ul>

                <form class="d-flex search-bar" action="search.php" method="GET" name="search_form">
                    <input id="content-desktop" class="form-control me-2" type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input" aria-label="Search" style="border-width:2px; border-radius:50px;">
                    <button id="content-desktop" class="btn btn-outline-success" type="submit" style="border-radius:50px; border-color:white; background-color:white; font-family:'Quicksand', sans-serif; color:#1778F2; font-weight:bold;">Search</button>
                </form>
            </div>
            </div>
        </nav>



        <div id="content-mobile" class="new-icon-bar">
            <?php
            //Unread messages 
            $messages = new Message($con, $userLoggedIn);
            $num_messages = $messages->getUnreadNumber();

            //Unread notifications 
            $notifications = new Notification($con, $userLoggedIn);
            $num_notifications = $notifications->getUnreadNumber();

            //Unread notifications 
            $user_obj = new User($con, $userLoggedIn);
            $num_requests = $user_obj->getNumberOfFriendRequests();
            ?>
            <div class="row">
                <div class="col-9" style="background-color:#1778F2; padding:0px">
                    <h1 style="padding-top: 10px; color:white; background-color:#1778F2; font-size:35px;font-family: 'Roboto', sans-serif; font-weight: 600;letter-spacing: 2px; text-align:center">hindb<span style="color:#1778F2;background-color: white; border-radius:25px; ">o</span><span style="color:#1778F2;background-color: white; border-radius:25px;">o</span>k</h1>
                </div>
                <div class="col-3" style="background-color: #1778F2; padding:0px;">
                    <a href="includes/handlers/logout.php" style="background-color: #1778F2;"><i class="bi bi-box-arrow-right" style="color:#FA3E3E;"></i></a>
                </div>
            </div>
            <div class="row">
                <a href="index.php"><i class="bi bi-house" style="color:#1778F2;"></i></a>
                <a href="javascript:void(0);" class="nav-link" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'message')"><i class="bi bi-chat-dots " style="color:#1778F2;"></i></a>
                <a href="javascript:void(0);" class="nav-link" onclick="getDropdownData('<?php echo $userLoggedIn; ?>', 'notification')"><i class="bi bi-bell " style="color:#1778F2;"></i></a>
                <a href="requests.php" class="nav-link"><i class="bi bi-people-fill" style="color:#1778F2;"></i></a>
                <a href="search.php" class="nav-link"><i class="bi bi-search" style="color:#1778F2;"></i></a>
                <a href="shop.php" class="nav-link"><i class="bi bi-bag" style="color:#1778F2;"></i></a>
                <a href="video.php" class="nav-link"><i class="bi bi-play-circle" style="color:#1778F2;"></i></a>
                <a href="settings.php" class="nav-link"><i class="bi bi-whatsapp" style="color:#1778F2;"></i></a>
            </div>
        </div>

        <div class="search_results" id='content-desktop'>
        </div>

        <div class="search_results_footer_empty" id="content-desktop">
        </div>

        <div class="dropdown_data_window" id="content-desktop" style="height:0px; border:none;"></div>
        <input type="hidden" id="dropdown_data_type" value="">
    </header>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <!-- Option 2 Separate Popper and Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

    <script>
        $(function() {

            var userLoggedIn = '<?php echo $userLoggedIn; ?>';
            var dropdownInProgress = false;

            $(".dropdown_data_window").scroll(function() {
                var bottomElement = $(".dropdown_data_window a").last();
                var noMoreData = $('.dropdown_data_window').find('.noMoreDropdownData').val();

                // isElementInViewport uses getBoundingClientRect(), which requires the HTML DOM object, not the jQuery object. The jQuery equivalent is using [0] as shown below.
                if (isElementInView(bottomElement[0]) && noMoreData == 'false') {
                    loadPosts();
                }
            });

            function loadPosts() {
                if (dropdownInProgress) { //If it is already in the process of loading some posts, just return
                    return;
                }

                dropdownInProgress = true;

                var page = $('.dropdown_data_window').find('.nextPageDropdownData').val() || 1; //If .nextPage couldn't be found, it must not be on the page yet (it must be the first time loading posts), so use the value '1'

                var pageName; //Holds name of page to send ajax request to
                var type = $('#dropdown_data_type').val();

                if (type == 'notification')
                    pageName = "ajax_load_notifications.php";
                else if (type == 'message')
                    pageName = "ajax_load_messages.php";

                $.ajax({
                    url: "includes/handlers/" + pageName,
                    type: "POST",
                    data: "page=" + page + "&userLoggedIn=" + userLoggedIn,
                    cache: false,

                    success: function(response) {

                        $('.dropdown_data_window').find('.nextPageDropdownData').remove(); //Removes current .nextpage 
                        $('.dropdown_data_window').find('.noMoreDropdownData').remove();

                        $('.dropdown_data_window').append(response);

                        dropdownInProgress = false;
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

        // function toggleSearch() {
        //     console.log("working");
        //     if (document.getElementById('searchBar').style.display = "block") {
        //         document.getElementById('searchBar').style.display = "none";
        //     } 
        //     if(document.getElementById('searchBar').style.display = "none") {
        //         document.getElementById('searchBar').style.display = "block";
        //     }
        // }
    </script>