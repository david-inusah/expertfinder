<?php
class Post{
	public static function createPost($postbody,$multimedia, $loggedInUserID,$profileuserID ){
		if (strlen($postbody) > 160 || strlen($postbody) < 1) {
			die('Incorrect length!');
		}
		if ($loggedInUserID == $profileuserID) {
			DB::query('INSERT INTO post VALUES (\'\', :postbody, :multimedia, NOW(), :userid, 0)', array(':postbody'=>$postbody, 'multimedia'=>$multimedia,':userid'=>$profileuserID));
		} else {
			die('Incorrect user!');
		}
	}
	public static function deletePost($postid, $loggedInUserID){
		if (DB::query('SELECT postedby_id FROM post WHERE postedby_id=:loggedInUserID', array(':loggedInUserID'=>$loggedInUserID))) {
			DB::query('DELETE FROM post WHERE post_id=:postid', array(':postid'=>$postid));
		// } elseif($loggedInUserID == $profileuserID){
		// 	DB::query('DELETE * FROM post WHERE post_id=:postid', array(':postid'=>$postid));
		}else {
			die('Incorrect user!');
		}
	}

	public static function likePost ($postid, $likerid){
		if (!DB::query('SELECT liker_id FROM post_likes WHERE post_id=:postid AND liker_id=:likerid', array(':postid'=>$postid, 
			':likerid'=>$likerid))) {
			DB::query('UPDATE post SET likes=likes+1 WHERE post_id=:postid', array(':postid'=>$postid));
		DB::query('INSERT INTO post_likes VALUES (\'\', :postid, :likerid)', array(':postid'=>$postid, ':likerid'=>$likerid));
	} else {
		DB::query('UPDATE post SET likes=likes-1 WHERE post_id=:postid', array(':postid'=>$postid));
		DB::query('DELETE FROM post_likes WHERE post_id=:postid AND liker_id=:likerid', array(':postid'=>$postid, ':likerid'=>$likerid));
	}
}
public static function displayPost($userid, $username, $loggedInUserID){
	$post = "";
	$dbposts =DB::query('SELECT * from post WHERE postedby_id=:postedbyid ORDER BY post_id DESC', array(':postedbyid'=>$userid));

	foreach ($dbposts as $key){
		if (!DB::query('SELECT post_id FROM post_likes WHERE post_id=:postid AND liker_id=:likerid', array(':postid'=>$key['post_id'], ':likerid'=>$loggedInUserID))) {
			$post .= htmlspecialchars($key['postbody'])."
			<form action='profile.php?username=$username&postid=".$key['post_id']."' method='post'>
				<input type='submit' name='like' value='Like'>
				<span>".$key['likes']." likes</span>
			</form>
			<form action='profile.php?username=$username&postid=".$key['post_id']."' method='post'>
				<p><textarea name='commentbody' rows='3' cols='50'></textarea></p>
				<input type='submit' name='comment' value='Comment'>
			</form>
			<form action='profile.php?username=$username&postid=".$key['post_id']."' method='post'>
				<input type='submit' name='delete' value='Delete'>
			</form>";
			Comment::displayComment($key['post_id']);
			echo "
			<hr /></br />";
		} else {
			$post .= htmlspecialchars($key['postbody'])."
			<form action='profile.php?username=$username&postid=".$key['post_id']."' method='post'>
				<input type='submit' name='unlike' value='Unlike'>
				<span>".$key['likes']." likes</span>
			</form>
			<form action='profile.php?username=$username&postid=".$key['post_id']."' method='post'>
				<p><textarea name='commentbody' rows='3' cols='50'></textarea></p>
				<input type='submit' name='comment' value='Comment'>
			</form>
			<form action='profile.php?username=$username&postid=".$key['post_id']."' method='post'>
				<input type='submit' name='delete' value='Delete'>
			</form>";
			Comment::displayComment($key['post_id']);
			echo "
			<hr /></br />";
		}
	}
	return $post;
}
} 

?>