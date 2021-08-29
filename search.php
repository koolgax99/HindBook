<?php

include("includes/header.php");

if (isset($_GET['q'])) {
	$query = $_GET['q'];
} else {
	$query = "";
}

if (isset($_GET['type'])) {
	$type = $_GET['type'];
} else {
	$type = "name";
}
?>

<head>
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
	<link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css">
	<style>
		.search-results {
			margin-top: 150px
		}

		#search-mobile {
			display: none;
		}

		@media screen and (max-width: 768px) {
			#search-mobile {
				display: block;
			}
		}
	</style>
</head>
<script>
	function getLiveSearchUsers(value, user) {

		$.post("includes/handlers/ajax_search.php", {
			query: value,
			userLoggedIn: user
		}, function(data) {

			if ($(".search_results_footer_empty")[0]) {
				$(".search_results_footer_empty").toggleClass("search_results_footer");
				$(".search_results_footer_empty").toggleClass("search_results_footer_empty");
			}

			$('.search_results').html(data);
			$('.search_results_footer').html("<a href='search.php?q=" + value + "'>See All Results</a>");

			if (data == "") {
				$('.search_results_footer').html("");
				$('.search_results_footer').toggleClass("search_results_footer_empty");
				$('.search_results_footer').toggleClass("search_results_footer");
			}

		});

	}
</script>
<main style="margin-top: 40px;">
	<div class="container">
		<div class="row search-results">
			<div class="card shadow p-3 mb-2 bg-white rounded" style="padding: 10px; border-bottom:solid #99DDFF; border-left:solid #99DDFF; color:#1778F2" id="search-mobile">
				<form class="example" action="search.php" method="GET" name="search_form">
					<input type="text" onkeyup="getLiveSearchUsers(this.value, '<?php echo $userLoggedIn; ?>')" name="q" placeholder="Search..." autocomplete="off" id="search_text_input" aria-label="Search" style="border-width:2px; border-radius:50px;">
					<button type="submit" style="border-radius:50px; border-color:white; background-color:#1778F2; font-family:'Quicksand', sans-serif; color:white; font-weight:bold;"><i class="fa fa-search"></i></button>
				</form>

				<div id="search_results"></div>
				<div id="search_results_footer"></div>
			</div>
			<div class="card shadow p-3 mb-2 bg-white rounded" style="padding: 10px; border-bottom:solid #99DDFF; border-left:solid #99DDFF;">
				<?php
				if ($query == "")
					echo "You must enter something in the search box.";
				else {

					//If query contains an underscore, assume user is searching for usernames
					if ($type == "username")
						$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
					//If there are two words, assume they are first and last names respectively
					else {

						$names = explode(" ", $query);

						if (count($names) == 3)
							$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[2]%') AND user_closed='no'");
						//If query has one word only, search first names or last names 
						else if (count($names) == 2)
							$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no'");
						else
							$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no'");
					}

					//Check if results were found 
					if (mysqli_num_rows($usersReturnedQuery) == 0)
						echo "We can't find anyone with a " . $type . " like: " . $query;
					else
						echo mysqli_num_rows($usersReturnedQuery) . " results found: <br> <br>";


					echo "<p id='grey'>Try searching for:</p>" .
						"<a href='search.php?q=" . $query . "&type=name'>Names</a><a href='search.php?q=" . $query . "&type=username'>Usernames</a><br><br><hr id='search_hr'>";

					while ($row = mysqli_fetch_array($usersReturnedQuery)) {
						$user_obj = new User($con, $user['username']);

						$button = "";
						$mutual_friends = "";

						if ($user['username'] != $row['username']) {

							//Generate button depending on friendship status 
							if ($user_obj->isFriend($row['username']))
								$button = "<button type='submit' name='" . $row['username'] . "' class='btn btn-danger' value='Remove Friend'>Remove Friend</button>";
							else if ($user_obj->didReceiveRequest($row['username']))
								$button = "<button type='submit' name='" . $row['username'] . "' class='btn btn-warning' value='Respond to request'>Respond to request</button>";
							else if ($user_obj->didSendRequest($row['username']))
								$button = "<button type='submit' class='btn btn-primary' value='Request Sent'>Request Sent</button>";
							else
								$button = "<button type='submit' name='" . $row['username'] . "' class='btn btn-success' value='Add Friend'>Add Friend</button>";

							$mutual_friends = $user_obj->getMutualFriends($row['username']) . " friends in common";


							//Button forms
							if (isset($_POST[$row['username']])) {

								if ($user_obj->isFriend($row['username'])) {
									$user_obj->removeFriend($row['username']);
									header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
								} else if ($user_obj->didReceiveRequest($row['username'])) {
									header("Location: requests.php");
								} else if ($user_obj->didSendRequest($row['username'])) {
								} else {
									$user_obj->sendRequest($row['username']);
									header("Location: http://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]");
								}
							}
						}

						echo "<div class='search_result'>
					<div class='searchPageFriendButtons'>
						<form action='' method='POST'>
							" . $button . "
							<br>
						</form>
					</div>


					<div class='result_profile_pic'>
						<a href='" . $row['username'] . "'><img src='" . $row['profile_pic'] . "' style='height: 100px;'></a>
					</div>

						<a href='" . $row['username'] . "'> " . $row['first_name'] . " " . $row['last_name'] . "
						<p id='grey'> " . $row['username'] . "</p>
						</a>
						" . $mutual_friends . "
				</div>
				<hr id='search_hr'>";
					} //End while
				}
				?>
			</div>
		</div>
	</div>
</main>