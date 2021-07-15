<?php
include("../../config/config.php");
include("../../includes/classes/User.php");

$query = $_POST['query'];
$userLoggedIn = $_POST['userLoggedIn'];

$names = explode(" ", $query);

//If query contains an underscore, assume user is searching for usernames
if(strpos($query, '_') !== false) 
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE username LIKE '$query%' AND user_closed='no' LIMIT 8");
//If there are two words, assume they are first and last names respectively
else if(count($names) == 2)
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' AND last_name LIKE '$names[1]%') AND user_closed='no' LIMIT 8");
//If query has one word only, search first names or last names 
else 
	$usersReturnedQuery = mysqli_query($con, "SELECT * FROM users WHERE (first_name LIKE '$names[0]%' OR last_name LIKE '$names[0]%') AND user_closed='no' LIMIT 8");


if($query != ""){

	while($row = mysqli_fetch_array($usersReturnedQuery)) {
		$user = new User($con, $userLoggedIn);

		if($row['username'] != $userLoggedIn)
			$mutual_friends = $user->getMutualFriends($row['username']) . " friends in common";
		else 
			$mutual_friends = "";

		echo "<div class='card shadow p-3 bg-white rounded' style='height:70px'>
				<a href='" . $row['username'] . "' style='color: #1485BD;' >
				<div class='row'>
					<div class='col-sm-4'>
						<img src='" . $row['profile_pic'] . "'  style='width:50px;  margin: 1px 12px 0 2px; '>
					</div>
					<div class='col-sm-8' >
						" . $row['first_name'] . " " . $row['last_name'] . "
						<p style='margin-left: 10px; font-size: 12px;'>" . $row['username'] . "</p>
						<p id='grey' style='margin-left: 10px; font-size: 12px;'>" . $mutual_friends . "</p>
					</div>
				</div>
				</a>
				</div>";
	}
}