<?php
/**
* 
*/
class Comment 
{
	
	public static function createComment($comment, $postid, $userid)
	{
		if (strlen($comment) > 160 || strlen($comment) < 1) {
			die('Incorrect length!');
		}
			if (!DB::query('SELECT post_id FROM post WHERE post_id=:postid', array(':postid'=>$postid))){
				echo "Invalid post ID";
			}else{
				DB::query('INSERT INTO comments VALUES (\'\', :comment, :userid, NOW(), :postid)', array(':comment'=>$comment,':userid'=>$userid,':postid'=>$postid));
			}
	}

	public static function displayComment($postid)
	{
		$comments = DB::query('SELECT username, comment FROM users, comments WHERE users.id = comments.postedby_id AND comments.post_id=:postid', array(':postid'=>$postid));
		foreach ($comments as $comment) {
			echo $comment['comment']."~".$comment['username']. "<hr/>";
		}
	}
}
?>