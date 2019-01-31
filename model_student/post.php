<?php
namespace Model\Post;
use \Db;
use \PDOException;
use \PDO;
/**
 * Post
 *
 * This file contains every db action regarding the posts
 */

/**
 * Get a post in db
 * @param id the id of the post in db
 * @return an object containing the attributes of the post or false if error
 * @warning the author attribute is a user object
 * @warning the date attribute is a DateTime object
 */
function get($id) {
    $db = \Db::dbc();

    $query = $db->prepare("SELECT * FROM tweet WHERE idTweet = :id");
    $query->bindValue(":id", $id);
    $query->execute();
    $result = $query->fetch();
    if ($result == NULL) return NULL;
  	$post = (object) array
	(
		"id" => $result["idTweet"],
		"text" => $result["content"],
		"date" => $result["dateTweet"],
		"author" => \Model\User\get($result["idUser"])
	);
  
    return $post;
}

/**
 * Get a post with its likes, responses, the hashtags used and the post it was the response of
 * @param id the id of the post in db
 * @return an object containing the attributes of the post or false if error
 * @warning the author attribute is a user object
 * @warning the date attribute is a DateTime object
 * @warning the likes attribute is an array of users objects
 * @warning the hashtags attribute is an of hashtags objects
 * @warning the responds_to attribute is either null (if the post is not a response) or a post object
 */
function get_with_joins($id) {
    $db = \Db::dbc();

	
    $query = $db->prepare("SELECT * FROM tweet WHERE idTweet = :id");
    $query->bindValue(":id", $id);
    $query->execute();
    $result = $query->fetch();
    if ($result == NULL) return NULL;
    $post = (object) array
	(
		"id" => $result["idTweet"],
		"text" => $result["content"],
		"date" => $result["dateTweet"],
		"author" => \Model\User\get($result["idUser"])
	);
	$post->responds_to = get($result["idTweet_to"]);
	$post->likes = get_likes($post->id);
	$post->hashtags = [];
  
    return $post;
}
 
/**
 * Create a post in db
 * @param author_id the author user's id
 * @param text the message
 * @param mentioned_authors the array of ids of users who are mentioned in the post
 * @param response_to the id of the post which the creating post responds to
 * @return the id which was assigned to the created post
 * @warning this function computes the date
 * @warning this function adds the mentions (after checking the users' existence)
 * @warning this function adds the hashtags
 * @warning this function takes care to rollback if one of the queries comes to fail.
 */
function create($author_id, $text, $response_to=null) {
	$db = \Db::dbc();
	$query = $db->prepare("INSERT INTO tweet(idUser, idTweet_to, content, dateTweet) VALUES(:author_id, :response_to, :text, now())");
	$query->bindValue(":author_id", $author_id);
	$query->bindValue(":response_to", $response_to);
	$query->bindValue(":text", $text);
	$query->execute();
	$idPost = $db->lastInsertId();
	
	$userMentionned = extract_mentions($text);
	foreach ($userMentionned as $username) 
	{
		$user = \Model\User\get_by_username(ltrim($username, '@'));
		if($user && $user->id)
			mention_user($idPost, $user->id);
	}
	$hashtags = extract_hashtags($text);
	foreach ($hashtags as $hashtag) 
	{
		\Model\Hashtag\attach($idPost, $hashtag);
	}
	
	return $idPost;
}

/**
 * Mention a user in a post
 * @param pid the post id
 * @param uid the user id to mention
 */
function mention_user($pid, $uid) {
	$db = Db::dbc();

    $query = $db->prepare("INSERT INTO mention (idUser, idTweet) VALUES(:uid, :pid)");
    $query->bindValue(":uid", $uid);
    $query->bindValue(":pid", $pid);
    $query->execute();
    return true;
}

/**
 * Get mentioned user in post
 * @param pid the post id
 * @return the array of user objects mentioned
 */
function get_mentioned($pid) {
    $db = \Db::dbc();
	
	$users = [];
	$query = $db->prepare("SELECT * FROM mention m INNER JOIN user u ON u.idUser = m.idUser WHERE m.idTweet = :pid");
	$query->bindValue(":pid", $pid);
	$query->execute();
	while ($result = $query->fetch()) 
	{
		$user = (object)array
		(
			"id" => $result['idUser'],
			"username" => $result['username'],
			"name" => $result['name'],
			"password" => $result['password'],
			"email" => $result['mail'],
			"avatar" => $result['avatar']
		);
		$users[] = $user;
	}
	
	return $users;
}

/**
 * Delete a post in db
 * @param id the id of the post to delete
 */
function destroy($id) {
	$db = Db::dbc();

    $query = $db->prepare("DELETE FROM tweet WHERE idTweet = :id");
    $query->bindValue(":id", $id);
    $query->execute();
    return true;
}

/**
 * Search for posts
 * @param string the string to search in the text
 * @return an array of find objects
 */
function search($string) {
    $db = Db::dbc();

    $posts = [];
    $query = $db->prepare("SELECT * FROM tweet WHERE content LIKE :string");
    $query->bindValue(":string", "%".$string."%");
    $query->execute();
    while ($result = $query->fetch()) 
    {
   		$post = (object) array
     	(
        	"id" => $result["idTweet"],
            "text" => $result["content"],
            "date" => $result["dateTweet"],
            "author" => \Model\User\get($result["idUser"])
     	);
     	$posts[] = $post;
    }
    return $posts;
}

/**
 * List posts
 * @param date_sorted the type of sorting on date (false if no sorting asked), "DESC" or "ASC" otherwise
 * @return an array of the objects of each post
 */
function list_all($date_sorted=false) {
    $db = \Db::dbc();
    if($date_sorted == "ASC")
        $query = $db->prepare("SELECT * FROM tweet ORDER BY dateTweet ASC");
    else if($date_sorted == "DESC")
        $query = $db->prepare("SELECT * FROM tweet ORDER BY dateTweet DESC");
    else
        $query = $db->prepare("SELECT * FROM tweet ORDER BY dateTweet");

    $query->execute();

    while ($result = $query->fetch())
    {
      $post = (object) array
      (
            "id" => $result["idTweet"],
            "text" => $result["content"],
            "date" => $result["dateTweet"],
            "author" => \Model\User\get($result["idUser"])
          );
          $posts[] = $post;
    }
    return $posts;
}

/**
 * Get a user's posts
 * @param id the user's id
 * @param date_sorted the type of sorting on date (false if no sorting asked), "DESC" or "ASC" otherwise
 * @return the list of posts objects
 */
function list_user_posts($id, $date_sorted="DESC") {
	$db = \Db::dbc();

	
  	if($date_sorted == 'ASC')
  		$query = $db->prepare("SELECT * FROM tweet WHERE idUser = :id ORDER BY dateTweet ASC");
	else if($date_sorted == 'DESC')
    	$query = $db->prepare("SELECT * FROM tweet WHERE idUser = :id ORDER BY dateTweet DESC");
  	else
    	$query = $db->prepare("SELECT * FROM tweet WHERE idUser = :id ORDER BY dateTweet");

  	$query->execute();

 	while ($result = $query->fetch())
  	{
    	$post = (object) array
    	(
            "id" => $result["idTweet"],
            "text" => $result["content"],
            "date" => $result["dateTweet"],
            "author" => \Model\User\get($result["idUser"])
    	);
    	$posts[] = $post;
  	}
  	return $posts;
}

/**
 * Get a post's likes
 * @param pid the post's id
 * @return the users objects who liked the post
 */
function get_likes($pid) {
    $db = \Db::dbc();
    
    $users = [];
    $query = $db->prepare("SELECT * FROM likeTweet NATURAL JOIN user WHERE likeTweet.idTweet = :pid");
    $query->bindValue(':pid', $pid);
    $query->execute();
    
    while($result = $query->fetch())
    {
    	$user = (object)array
    	(
        	"id" => $result['idUser'],
            "username" => $result['username'],
            "name" => $result['name'],
            "password" => $result['password'],
            "email" => $result['mail'],
            "avatar" => $result['avatar']
        );
    	$users[] = $user;
    }
    return $users;
}

/**
 * Get a post's responses
 * @param pid the post's id
 * @return the posts objects which are a response to the actual post
 */
function get_responses($pid) {
    $db = Db::dbc();
    
    $posts = [];
    $query = $db->prepare("SELECT * FROM tweet WHERE idTweet_To = :pid");
    $query->bindValue(":pid", $pid);
    $query->execute();
    while ($result = $query->fetch()) 
    {
		$post = (object) array
		(
			"id" => $result["idTweet"],
			"text" => $result["content"],
			"date" => $result["dateTweet"],
			"author" => \Model\User\get($result["idUser"])
		);
		$posts[] = $post;
    }
    return $posts;
}

/**
 * Get stats from a post (number of responses and number of likes
 */
function get_stats($pid) {
    return (object) array(
        "nb_likes" => 10,
        "nb_responses" => 40
    );
}

/**
 * Like a post
 * @param uid the user's id to like the post
 * @param pid the post's id to be liked
 */
function like($uid, $pid) {
    $db = \Db::dbc();

    $query = $db->prepare("INSERT INTO likeTweet (idUser, idTweet) VALUES(:uid, :pid)");
    $query->bindValue(':uid', $uid);
    $query->bindValue(':pid', $pid);

    $query->execute();

    return true;
}

/**
 * Unlike a post
 * @param uid the user's id to unlike the post
 * @param pid the post's id to be unliked
 */
function unlike($uid, $pid) {
    $db = \Db::dbc();

    $query = $db->prepare("DELETE FROM likeTweet WHERE idUser = :uid AND idTweet = :pid");
    $query->bindValue(':uid', $uid);
    $query->bindValue(':pid', $pid);
    
    $query->execute();
    
    return true;
}

