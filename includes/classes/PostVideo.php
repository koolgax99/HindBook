<?php
class PostVideo
{
    private $user_obj;
    private $con;

    public function __construct($con, $user)
    {
        $this->con = $con;
        $this->user_obj = new User($con, $user);
    }

    public function submitVideo($body, $videoName)
    {
        $body = strip_tags($body); //removes html tags 
        $body = mysqli_real_escape_string($this->con, $body);
        $body = str_replace('\r\n', "\n", $body);
        $body = nl2br($body);
        $check_empty = preg_replace('/\s+/', '', $body);

        if ($check_empty != "" && $videoName != "") {
            //Current date and time
            $date_added = date("Y-m-d H:i:s");
            //Get username
            $added_by = $this->user_obj->getUsername();

            $query = mysqli_query($this->con, "INSERT INTO videos VALUES (NULL, '$body', '$added_by', '$date_added', 'no','0', '$videoName')");

            $returned_id = mysqli_insert_id($this->con);
        }
    }

    public function getAllVideos($data, $limit)
    {
        $page = $data['page'];

        if ($page == 1)
            $start = 0;
        else
            $start = ($page - 1) * $limit;

        $str = ""; //String to return 
        $data_query = mysqli_query($this->con, "SELECT * FROM videos ORDER BY id DESC");

        if (mysqli_num_rows($data_query) > 0) {
            $num_iterations = 0; //Number of results checked (not necasserily posted)
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];
                $videoPath = $row['videos'];


                if ($num_iterations++ < $start)
                    continue;


                //Once 10 posts have been loaded, break
                if ($count > $limit) {
                    break;
                } else {
                    $count++;
                }

                $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];


?>
                <script>
                    function toggle<?php echo $id; ?>(event) {
                        var target = $(event.target);

                        if (!target.is('a') || !target.is('button')) {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");

                            if (element.style.display == "block")
                                element.style.display = "none";
                            else
                                element.style.display = "block";
                        }

                    }

                    function copyUrl<?php echo $id; ?>(event) {
                        var target = $(event.target);

                        if (!target.is('a') || !target.is('button')) {
                            var element = document.getElementById("shareButton<?php echo $id; ?>");
                            const el = document.createElement('textarea');
                            el.value = window.location.protocol + "//" + window.location.host + "/single_video.php?id=" + <?php echo $id; ?>;
                            document.body.appendChild(el);
                            el.select();
                            document.execCommand('copy');
                            document.body.removeChild(el);
                            element.innerHTML = "Copied !";
                        }

                    }
                </script>
                <?php

                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); //Time of post
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

                if ($videoPath != "") {
                    $videoDiv = "
                    <video controls autoplay loop width='400'>
                        <source src='$videoPath' type='video/mp4'>
                        Your browser does not support HTML video.
                    </video>
                    ";
                } else {
                    $videoDiv = "";
                }

                $str .= "
				<div class='card shadow p-2 mb-2 bg-white rounded'>
                        <div>
                            <div style='float: left;'>
							<div class='row'>
                                <div class='col-3'>
                                <img class='img-fluid' src='$profile_pic' style='width:70px;' />
                                </div>
                                <div class='col' style='width: 270px;'>
                                    <a href='$added_by' style='margin-right: 20px;'> $first_name $last_name </a>
									<br>$time_message<br/>
                                </div>
							</div>
                            </div>
                        </div>
						<br>
						<div class='container' style='padding:0px'>
							<div class='row'>
								<p> 
									$body
								</p>
							</div>
							$videoDiv
							</div>
							<div class='row'>
							<div class='col-sm-4 col-4'>
								<iframe src='like_video.php?post_id=$id' scrolling='no' style='height: 62px; width: 131px;' ></iframe>
							</div>
							<div class='col-sm-4 col-4'>	
								<button class='btn btn-primary' style='margin-top: 24px;' onClick='javascript:toggle$id(event)'>Comment</button>
							</div>
							<div class='col-sm-4 col-4'>	
								<button class='btn btn-primary' id='shareButton$id' style='margin-top: 24px;' onClick='javascript:copyUrl$id(event)'>Share</button>
							</div>
						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none; padding:10px;  border: solid 1px; border-radius:5px;'>
							<iframe src='comment_frame.php?post_id=$id&post_type=video' id='comment_iframe' frameborder='0'></iframe>
						</div>
					</div>
					";
                ?>
            <?php
            } //End while loop
            if ($count > $limit)
                $str .= "<input type='hidden' class='nextPage' value='" . ($page + 1) . "'>
							<input type='hidden' class='noMorePosts' value='false'>";
            else
                $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;' class='noMorePostsText'> No more posts to show! </p>";
        }
        echo $str;
    }

    public function getSingleVideo($post_id)
    {
        $str = ""; //String to return 
        $data_query = mysqli_query($this->con, "SELECT * FROM videos WHERE id='$post_id'");

        if (mysqli_num_rows($data_query) > 0) {
            $num_iterations = 0; //Number of results checked (not necasserily posted)
            $count = 1;

            while ($row = mysqli_fetch_array($data_query)) {
                $id = $row['id'];
                $body = $row['body'];
                $added_by = $row['added_by'];
                $date_time = $row['date_added'];
                $videoPath = $row['videos'];


                $user_details_query = mysqli_query($this->con, "SELECT first_name, last_name, profile_pic FROM users WHERE username='$added_by'");
                $user_row = mysqli_fetch_array($user_details_query);
                $first_name = $user_row['first_name'];
                $last_name = $user_row['last_name'];
                $profile_pic = $user_row['profile_pic'];


            ?>
                <script>
                    function toggle<?php echo $id; ?>(event) {
                        var target = $(event.target);

                        if (!target.is('a') || !target.is('button')) {
                            var element = document.getElementById("toggleComment<?php echo $id; ?>");

                            if (element.style.display == "block")
                                element.style.display = "none";
                            else
                                element.style.display = "block";
                        }

                    }

                    function copyUrl<?php echo $id; ?>(event) {
                        var target = $(event.target);

                        if (!target.is('a') || !target.is('button')) {
                            var element = document.getElementById("shareButton<?php echo $id; ?>");
                            const el = document.createElement('textarea');
                            el.value = window.location.protocol + "//" + window.location.host + "/single_video.php?id=" + <?php echo $id; ?>;
                            document.body.appendChild(el);
                            el.select();
                            document.execCommand('copy');
                            document.body.removeChild(el);
                            element.innerHTML = "Copied !";
                        }

                    }
                </script>
                <?php

                //Timeframe
                $date_time_now = date("Y-m-d H:i:s");
                $start_date = new DateTime($date_time); //Time of post
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

                if ($videoPath != "") {
                    $videoDiv = "
                    <video controls autoplay loop width='400'>
                        <source src='$videoPath' type='video/mp4'>
                        Your browser does not support HTML video.
                    </video>
                    ";
                } else {
                    $videoDiv = "";
                }

                $str .= "
				<div class='card shadow p-2 mb-2 bg-white rounded'>
                        <div>
                            <div style='float: left;'>
							<div class='row'>
                                <div class='col-3'>
                                <img class='img-fluid' src='$profile_pic' style='width:70px;' />
                                </div>
                                <div class='col' style='width: 270px;'>
                                    <a href='$added_by' style='margin-right: 20px;'> $first_name $last_name </a>
									<br>$time_message<br/>
                                </div>
							</div>
                            </div>
                        </div>
						<br>
						<div class='container' style='padding:0px'>
							<div class='row'>
								<p> 
									$body
								</p>
							</div>
							$videoDiv
							</div>
							<div class='row'>
							<div class='col-sm-4 col-4'>
								<iframe src='like_video.php?post_id=$id' scrolling='no' style='height: 62px; width: 131px;' ></iframe>
							</div>
							<div class='col-sm-4 col-4'>	
								<button class='btn btn-primary' style='margin-top: 24px;' onClick='javascript:toggle$id(event)'>Comment</button>
							</div>
							<div class='col-sm-4 col-4'>	
								<button class='btn btn-primary' id='shareButton$id' style='margin-top: 24px;' onClick='javascript:copyUrl$id(event)'>Share</button>
							</div>
						</div>
						<div class='post_comment' id='toggleComment$id' style='display:none; padding:10px;  border: solid 1px; border-radius:5px;'>
							<iframe src='comment_frame.php?post_id=$id&post_type=video' id='comment_iframe' frameborder='0'></iframe>
						</div>
					</div>
					";
                ?>
<?php
            } //End while loop
            $str .= "<input type='hidden' class='noMorePosts' value='true'><p style='text-align: centre;' class='noMorePostsText'> No more posts to show! </p>";
        }
        echo $str;
    }
}
