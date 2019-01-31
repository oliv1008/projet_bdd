<?php
namespace Model\Hashtag;
use \Db;
use \PDOException;
use \PDO;
/**
 * Hashtag model
 *
 * This file contains every db action regarding the hashtags
 */

/**
 * Attach a hashtag to a post
 * @param pid the post id to which attach the hashtag
 * @param hashtag_name the name of the hashtag to attach
 */
function attach($pid, $hashtag_name) {
	$db = Db::dbc();

	$query = $db->prepare("INSERT INTO hashtag(nameHashtag) VALUES(:hashtag_name)");
	$query->bindValue(":hashtag_name", $hashtag_name);
	$query->execute();
		
	$idHashtag = $db->lastInsertId();
		
	$query = $db->prepare("INSERT INTO useHashtag(idHashtag, idTweet) VALUES(:idHashtag, :pid)");
	$query->bindValue(":idHashtag", $idHashtag);
	$query->bindValue(":pid", $pid);
	$query->execute();
	return true;
}

/**
 * List hashtags
 * @return a list of hashtags names
 */
function list_hashtags() {
	$db = \Db::dbc();

    $hashtags = [];

    $query = $db->prepare("SELECT DISTINCT nameHashtag FROM hashtag");
    $query->execute();

    while ($result = $query->fetch())
    {
      $hashtags[] = $result["nameHashtag"];
    }

    return $hashtags;
}

/**
 * List hashtags sorted per popularity (number of posts using each)
 * @param length number of hashtags to get at most
 * @return a list of hashtags
 */
function list_popular_hashtags($length) {
	$db = \Db::dbc();

    $hashtags = [];
  
    $query = $db->prepare("SELECT nameHashtag FROM hashtag GROUP BY nameHashtag ORDER BY count(nameHashtag) DESC");
    $query->execute();
  
    while (($result = $query->fetch()) && $length > 0) 
    {
    	$length = $length - 1;
   		$hashtags[] = $result["nameHashtag"];
    }
    var_dump($hashtags);            
    return $hashtags;
}

/**
 * Get posts for a hashtag
 * @param hashtag the hashtag name
 * @return a list of posts objects or null if the hashtag doesn't exist
 */
function get_posts($hashtag_name) {
	$db = \Db::dbc();

    $posts = [];

    $query = $db->prepare("SELECT DISTINCT t.* FROM hashtag NATURAL JOIN tweet t WHERE nameHashtag LIKE :hashtag_name");
    $query->bindValue(":hashtag_name", "%".$hashtag_name."%");
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

/** Get related hashtags
 * @param hashtag_name the hashtag name
 * @param length the size of the returned list at most
 * @return an array of hashtags names
 */
function get_related_hashtags($hashtag_name, $length) {
    return ["Hello"];
}
