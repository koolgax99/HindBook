<?php  
include("../../config/config.php");
include("../classes/User.php");
include("../classes/PostVideo.php");

$limit = 10; //Number of posts to be loaded per call

$posts = new PostVideo($con, $_REQUEST['userLoggedIn']);
$posts->getAllVideos($_REQUEST, $limit);
?>