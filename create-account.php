<?php
include('classes/DB.php');

if(isset($_POST['createaccount'])){
	$username=$_POST['username'];
	$password=$_POST['password'];
	$email=$_POST['email'];

	if (!DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$username))){

		if(strlen($username)>=3 && strlen($username)<=32) {

			if (preg_match('/[a-zA-Z0-9_]+/', $username)){

				if(strlen($password)>=4 && strlen($password)<=60) {

					if (filter_var($email,FILTER_VALIDATE_EMAIL)){

						if(!DB::query('SELECT Email FROM users WHERE email=:email',array(':email'=>$email))){

							DB::query('INSERT INTO USERS VALUES (\'\',:username,:password,:email)', array(':username'=>$username,
								':password'=>password_hash($password, PASSWORD_BCRYPT),':email'=>$email));
							echo "Success!";
						}else {
							echo "Unavailable email address";
						}
					}
					else {
						echo "Invalid Email Address";
					}
				}
				else {
					echo "Invalid Password";
				}
			}
			else {
				echo "Invalid Username";
			}
		}
		else {
			echo "Invalid Username";
		}
	}
	else {
		echo "User already exists";
	}
}
?>

	<h1>Sign Up</h1>
	<form action="create-account.php" method="post">
		<p>	<input type="text" name="username" value="" placeholder="Username"></p>
		<p><input type="password" name="password" value="" placeholder="Password"></p>
		<p><input type="email" name="email" value="" placeholder="somethere@gool.com"></p>
		<p><input type="submit" name="createaccount" value="Create Account"></p>
	</form>