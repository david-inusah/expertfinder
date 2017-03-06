<?php
include('classes/DB.php');
include('classes/Login.php');
include('classes/Post.php');
include('classes/Comment.php');
$showTimeline=false;

if (Login::isLoggedIn()){
	$userid = Login::isLoggedIn();
	$showTimeline=true;
} else{
	echo "Not logged in";
}

if (isset($_GET['postid'])) {
	Post::likePost($_GET['postid'], $userid);
}
if (isset($_POST['comment'])) {
	Comment::createComment($_POST['commentbody'], $_GET['postid'], $userid);
}
if(isset($_POST['delete'])){
			Post::deletePost($_GET['postid'],Login::isLoggedIn(), $userid);
		}

$followingposts = DB::query('SELECT users.username, post.post_id, post.postbody,post.likes, followers.user_id FROM post, users, followers WHERE users.id=post.postedby_id AND followers.user_id=users.id AND follower_id=8 ORDER BY `post`.`likes` DESC');


foreach ($followingposts as $post) {
	echo $post['postbody']."~".$post["username"];
	echo "<form action='index.php?postid=".$post['post_id']."' method='post'>";
	if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND liker_id=:likerid', array(':postid'=>$post['post_id'], ':likerid'=>$userid))) {
		echo "<input type='submit' name='like' value='Like'>";
	}else{
		echo "<input type='submit' name='unlike' value='Unlike'>";
	}
	echo "<span>".$post['likes']." likes</span>
</form>
<form action='index.php?postid=".$post['post_id']."' method='post'>
	<p><textarea name='commentbody' rows='3' cols='50'></textarea></p>
	<p><input type='submit' name='comment' value='Comment'></p>
</form>";
	Comment::displayComment($post['post_id']);
echo "
<hr /></br />";
}
?>