<?php
include("includes/header.php");

$message_obj = new Message($con, $userLoggedIn);

if (isset($_GET['profile_username'])) {
  $username = $_GET['profile_username'];
  $user_details_query = mysqli_query($con, "SELECT * FROM users WHERE username='$username'");

  if (mysqli_num_rows($user_details_query) == 0) {
    echo "User does not exist";
    exit();
  }

  $user_array = mysqli_fetch_array($user_details_query);

  $num_friends = (substr_count($user_array['friend_array'], ",")) - 1;
}


if (isset($_POST['remove_friend'])) {
  $user = new User($con, $userLoggedIn);
  $user->removeFriend($username);
}

if (isset($_POST['add_friend'])) {
  $user = new User($con, $userLoggedIn);
  $user->sendRequest($username);
}

if (isset($_POST['respond_request'])) {
  header("Location: requests.php");
}

if (isset($_POST['cancel_request'])) {
  $user = new User($con, $userLoggedIn);
  $user->cancelRequest($username);
}

if (isset($_POST['post_message'])) {
  if (isset($_POST['message_body'])) {
    $body = mysqli_real_escape_string($con, $_POST['message_body']);
    $date = date("Y-m-d H:i:s");
    $message_obj->sendMessage($username, $body, $date);
  }

  $link = '#profileTabs a[href="#messages_div"]';
  echo "<script> 
          $(function() {
              $('" . $link . "').tab('show');
          });
        </script>";
}

?>

<style type="text/css">
  .wrapper {
    margin-left: 0px;
    padding-left: 0px;
  }

  @media (max-width: 768px) {
    .col-lg-3 {
      margin-top: 100px;
    }
  }
</style>
<main style="margin-top: 40px;">
  <div class="container">
    <div class="row">
      <div class="col-lg-3">
        <div class="card shadow p-3 mb-2 bg-white rounded" style="padding: 10px;">
          <div class="user_details column">
            <div class="row">
              <div class="col">
                <img src=" <?php echo $user_array['profile_pic']; ?>">
              </div>
              <div class="col">
                <a href="<?php echo $userLoggedIn; ?>">
                  <?php
                  echo $user_array['first_name'] . " " . $user_array['last_name'];
                  ?>
                </a>
                <br>
                <?php echo "Posts: " . $user_array['num_posts'] . "<br>";
                echo "Likes: " . $user_array['num_likes'] . "<br>";
                echo "Friends: " . $num_friends . "<br>";
                $profile_user_obj = new User($con, $username);
                if ($profile_user_obj->isClosed()) {
                  header("Location: user_closed.php");
                }

                $logged_in_user_obj = new User($con, $userLoggedIn);
                if ($userLoggedIn != $username) {
                  echo "Mutual Friends: " . $logged_in_user_obj->getMutualFriends($username);
                }
                ?>
              </div>
            </div>
            <br>

            <div class="row">
              <form action="<?php echo $username; ?>" method="POST">
                <?php
                $profile_user_obj = new User($con, $username);
                if ($profile_user_obj->isClosed()) {
                  header("Location: user_closed.php");
                }

                $logged_in_user_obj = new User($con, $userLoggedIn);

                if ($userLoggedIn != $username) {

                  if ($logged_in_user_obj->isFriend($username)) {
                    echo '<button type="submit" name="remove_friend" class="btn btn-danger" value="Remove Friend">Remove Friend</button><br>';
                  } else if ($logged_in_user_obj->didReceiveRequest($username)) {
                    echo '<button type="submit" name="respond_request" class="btn btn-warning" value="Respond to Request">Respond to Request</button><br>';
                  } else if ($logged_in_user_obj->didSendRequest($username)) {
                    echo '<button type="submit" name="cancel_request" class="btn btn-primary" value="Request Sent">Request Sent</button><br>';
                  } else
                    echo '<button type="submit" name="add_friend" class="btn btn-success" value="Add Friend">Add Friend</button><br>';
                }
                ?>
              </form>
            </div>
          </div>
        </div>
      </div>
      <div class="col-1">
      </div>
      <div class="col-lg-8">
        <div class="card shadow p-3 mb-2 bg-white rounded" style="padding: 10px;">
          <ul class="nav nav-pills mb-2" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="pills-home-tab" data-bs-toggle="pill" data-bs-target="#pills-home" type="button" role="tab" aria-controls="pills-home" aria-selected="true">Home</button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="pills-profile-tab" data-bs-toggle="pill" data-bs-target="#pills-profile" type="button" role="tab" aria-controls="pills-profile" aria-selected="false">Messages</button>
            </li>
          </ul>
          <div class="tab-content" id="pills-tabContent">
            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
              <div class="posts_area"></div>
            </div>
            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
              <div role="tabpanel" class="tab-pane" id="messages_div">
                <?php
                echo "<h4>You and <a href='" . $username . "'>" . $profile_user_obj->getFirstAndLastName() . "</a></h4><hr><br>";
                echo "<div class='card shadow p-3 mb-2 bg-white rounded ' id='scroll_messages'>";
                echo $message_obj->getMessages($username);
                echo "</div>";
                ?>

                <div class="message_post">
                  <form action="" method="POST">
                    <textarea name='message_body' id='message_textarea' class="form-control" placeholder='Write your message ...'></textarea>
                    <button type='submit' name='post_message' class='btn btn-primary' id='message_submit' value='Send'>Send</button>
                  </form>
                </div>

                <script>
                  $('a[data-toggle="tab"]').on('shown.bs.tab', function() {
                    var div = document.getElementById("scroll_messages");
                    div.scrollTop = div.scrollHeight;
                  });
                </script>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- Optional JavaScript; choose one of the two! -->

<!-- Option 1: Bootstrap Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>

<!-- Option 2: Separate Popper and Bootstrap JS -->

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

<script>
  $(function() {
    var userLoggedIn = '<?php echo $userLoggedIn; ?>';
    var profileUsername = '<?php echo $username; ?>';
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
        url: "includes/handlers/ajax_load_profile_posts.php",
        type: "POST",
        data: "page=" + page + "&userLoggedIn=" + userLoggedIn + "&profileUsername=" + profileUsername,
        cache: false,
        success: function(response) {
          $('.posts_area').find('.nextPage').remove(); //Removes current .nextpage
          $('.posts_area').find('.noMorePosts').remove(); //Removes current .nextpage
          $('.posts_area').find('.noMorePostsText').remove(); //Removes current .nextpage
          $('#loading').hide();
          $(".posts_area").append(response);
          inProgress = false;
        }
      });
    }

    //Check if the element is in view
    function isElementInView(el) {
      if (el == null) {
        return;
      }
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