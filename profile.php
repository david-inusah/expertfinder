<?php
include('classes/DB.php');
include('classes/Login.php');
include('classes/Post.php');
include('classes/Comment.php');

$username="";
$userid="";
$followerid="";
$multimedia="";
$postbody="";
$isFollowing = false;
if (isset($_GET['username'])){
	if (DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))){
		$username=DB::query('SELECT username FROM users WHERE username=:username', array(':username'=>$_GET['username']))[0]['username'];
		$userid=DB::query('SELECT id FROM users WHERE username=:username',array(':username'=>$_GET['username']))[0]['id'];
		$followerid= Login::isLoggedIn();

		if(isset($_POST['follow'])){
			if ($userid!=$followerid){

				if (!DB::query('SELECT follower_id FROM followers WHERE user_id=:userid', array(':userid'=>$userid))) {
					DB::query('INSERT INTO followers VALUES (\'\', :userid, :followerid)', array(':userid'=>$userid,':followerid'=>$followerid));
				}else {
					echo "Already following";
				}
				$isFollowing=true;
			}
		}
		if(isset($_POST['unfollow'])){
			if ($userid!=$followerid){

				if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid and follower_id=:followerid', array(':userid'=>$userid,':followerid'=>$followerid))) {
					DB::query('DELETE FROM followers WHERE user_id=:userid and follower_id=:followerid', array(':userid'=>$userid,':followerid'=>$followerid));
				}
				$isFollowing=false;
			}
		}

		if (DB::query('SELECT follower_id FROM followers WHERE user_id=:userid and follower_id=:followerid', array(':userid'=>$userid,':followerid'=>$followerid))) {
			$isFollowing=true;	
		}

		if(isset($_POST['post'])){
			Post::createPost($_POST['postbody'], $_POST['multimedia'], Login::isLoggedIn(), $userid);
		}
	}
	if (isset($_GET['postid'])) {
		Post::likePost($_GET['postid'], $followerid);
	}

	$post = Post::displayPost($userid, $username,  $followerid);

	if(isset($_POST['delete'])){
			Post::deletePost($_GET['postid'],Login::isLoggedIn(), $userid);
		}

}else {
	die('User not found');
}
?>
<h1><?php echo $username; ?>'s Profile </h1>
<form action="profile.php?username=<?php echo $username; ?>" method="POST">
	<?php
	if ($userid!=$followerid){
		if ($isFollowing){
			echo "<input type='submit' name='unfollow' value='Unfollow'>";
		}else{
			echo "<input type='submit' name='follow' value='Follow'>";
		}
	}else {
		echo "Welcome $username.
	</form>
	<form action='profile.php?username=$username'; method='POST'>
		<p><textarea name='postbody' rows='8' cols='80' placeholder='create a note...'></textarea></p>
		<H4>Share Video, Audio and image formats</H4>
		<p><input type='file' name='multimedia'></p>
		<p><input type='submit' name='post' value='Post'></p>
	</form>
	<div class='posts'>
		$post; 
	</div>
	";
}

?>