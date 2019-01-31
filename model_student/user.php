<?php
namespace Model\User;
use \Db;
use \PDOException;
use \PDO;
/**
 * User model
 *
 * This file contains every db action regarding the users
 */

/**
 * Get a user in db
 * @param id the id of the user in db
 * @return an object containing the attributes of the user or null if error or the user doesn't exist
 */
function get($id) 
{

    $db = \Db::dbc();
    $query = $db->prepare('SELECT * FROM user WHERE idUser = :idParam');

    $query->bindValue(':idParam', $id);

    try {
        $query->execute();
        $data = $query->fetch();

        if ($data == NULL)
            return NULL;

        $o = (object) array(
            "id" => $data['idUser'],
            "username" => $data['username'],
            "name" => $data['name'],
            "password" => $data['password'],
            "email" => $data['mail'],
            "avatar" => $data['avatar']
        );
        return $o;
    }
    catch(PDOException $e) {
        echo $e;
        return NULL;
    }
}

/**
 * Create a user in db
 * @param username the user's username
 * @param name the user's name
 * @param password the user's password
 * @param email the user's email
 * @param avatar_path the temporary path to the user's avatar
 * @return the id which was assigned to the created user
 * @warning this function doesn't check whether a user with a similar username exists
 * @warning this function hashes the password
 */
function create($username, $name, $password, $email, $avatar_path) {
    $db = \Db::dbc();
    
    $query_str = "INSERT INTO user
    			  (username, name, password, mail, avatar, dateSign) VALUES
    			  (:username, :name, :password, :mail, :avatar, :dateSign)";
	$query = $db->prepare($query_str);
	$date = new \DateTime("NOW");
	
	if ($query->execute(array(':username'=>$username,
							  ':name'=>$name,
							  ':password'=>hash_password($password),
							  ':mail' => $email,
							  ':avatar' =>$avatar_path,
							  ':dateSign' => $date->format('Y-m-d H:i:s'))) == FALSE )
	{
		return NULL;
	} 
	
	$last_id = $db->lastInsertId();		  
	return $last_id; 
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param username the user's username
 * @param name the user's name
 * @param email the user's email
 * @warning this function doesn't need to check whether a user with a similar username exists
 */
function modify($uid, $username, $name, $email) 
{

	$db = \Db::dbc();
    
    $query_str = "UPDATE user
    			  SET username = :username, name = :name, mail = :mail
    			  WHERE idUser = :idUser;";

	$query = $db->prepare($query_str);
	$query->bindValue(":username", $username);
	$query->bindValue(":name", $name);
	$query->bindValue(":mail", $email);
	$query->bindValue(":idUser", $uid);
	$query->execute();
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param new_password the new password
 * @warning this function has to hash the password
 */

function change_password($uid, $new_password) 
{

	$db = \Db::dbc();
    
    $query_str = "UPDATE user
    			  SET password = :password
    			  WHERE idUser = :idUser;";
    $new_hashed_pwd = hash_password($new_password);

	$query = $db->prepare($query_str);
	$query->bindValue(":password", $new_hashed_pwd);
	$query->bindValue(":idUser", $uid);
	$query->execute();

}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param avatar_path the temporary path to the user's avatar
 */
function change_avatar($uid, $avatar_path) {
}

/**
 * Delete a user in db
 * @param id the id of the user to delete
 * @return true if the user has been correctly deleted, false else
 */
function destroy($id) 
{

	$db = \Db::dbc();
    
    $query_str = "DELETE FROM user
    			  WHERE idUser = :idUser;";

	$query = $db->prepare($query_str);
	$query->bindValue(":idUser", $id);
	try
	{
		$query->execute();
		return TRUE;
	}
	catch(PDOException $e) {
        echo $e;
        return FALSE;
    }
}

/**
 * Hash a user password
 * @param password the clear password to hash
 * @return the hashed password
 */
function hash_password($password) {
    return md5($password);
}

/**
 * Search a user
 * @param string the string to search in the name or username
 * @return an array of find objects
 */
function search($string) {
    $db = \Db::dbc();
    $query = $db->prepare('SELECT * FROM user WHERE name LIKE :string1 OR username LIKE :string2');
    $query->bindValue(":string1", '%'.$string.'%');
    $query->bindValue(":string2", '%'.$string.'%');
    $users = [];

    $query->execute();
    while($row = $query->fetch())
    {
    	$user = (object)array
		(
			"id" => $row['idUser'],
			"username" => $row['username'],
			"name" => $row['name'],
			"password" => $row['password'],
			"email" => $row['mail'],
			"avatar" => $row['avatar']
		);
    	$users[] = $user;
    }

	return $users;
}

/**
 * List users
 * @return an array of the objects of every users
 */
function list_all() 
{
    $db = \Db::dbc();
    $query = $db->prepare('SELECT * FROM user');
    $users = [];

    $query->execute();
    while($row = $query->fetch())
    {
    	$user = (object)array
		(
			"id" => $row['idUser'],
			"username" => $row['username'],
			"name" => $row['name'],
			"password" => $row['password'],
			"email" => $row['mail'],
			"avatar" => $row['avatar']
		);
    	$users[] = $user;
    }

	return $users;
}

/**
 * Get a user from its username
 * @param username the searched user's username
 * @return the user object or null if the user doesn't exist
 */
function get_by_username($username) 
{
	$db = \Db::dbc();
    $query = $db->prepare('SELECT * FROM user WHERE username = :username');
    $query->bindValue(":username", $username);
    $query->execute();
    $result = $query->fetch();
    if ($result == NULL) return NULL;
    $user = (object)array
    (
    	"id" => $result["idUser"],
    	"username" => $result["username"],
    	"name" => $result["name"],
    	"password" => $result["password"],
    	"email" => $result["mail"],
    	"avatar" => $result["avatar"]
    );
    
    return $user;
}

/**
 * Get a user's followers
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followers($uid) {
   	$db = \Db::dbc();
    $query = $db->prepare("SELECT u.* FROM user u INNER JOIN follow f ON u.idUser = f.idUser_to WHERE f.idUser_from = :uid");
    $query->bindValue(":uid", $uid);
    $query->execute();
    $users = [];
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
 * Get the users our user is following
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followings($uid) {
   	$db = \Db::dbc();
    $query = $db->prepare("SELECT u.* FROM user u INNER JOIN follow f ON u.idUser = f.idUser_from WHERE f.idUser_to = :uid");
    $query->bindValue(":uid", $uid);
    $query->execute();
    $users = [];
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
 * Get a user's stats
 * @param uid the user's id
 * @return an object which describes the stats
 */
function get_stats($uid) {
    return (object) array(
        "nb_posts" => 10,
        "nb_followers" => 50,
        "nb_following" => 66
    );
}

/**
 * Verify the user authentification
 * @param username the user's username
 * @param password the user's password
 * @return the user object or null if authentification failed
 * @warning this function must perform the password hashing
 */
function check_auth($username, $password) {
    $db = \Db::dbc();
    
    $hashed_pwd = hash_password($password);
    $query_str = "SELECT idUser, username, name, mail, password, avatar
    			  FROM user
    			  WHERE username = :username AND password = :password;";
    
	try {
		$query = $db->prepare($query_str);
		$query->bindValue(':username', $username, PDO::PARAM_STR);
		$query->bindValue(':password', $hashed_pwd, PDO::PARAM_STR);
        $query->execute();
        $data = $query->fetch();
      
        if ($data == NULL)
            return NULL;

        $o = (object) array(
            "id" => $data['idUser'],
            "username" => $data['username'],
            "name" => $data['name'],
            "email" => $data['mail'],
            "password" => $data['password'],
            "avatar" => $data['avatar']
        );
        return $o;
    }
    catch(PDOException $e) {
        echo $e;
        return NULL;
    }
}

/**
 * Verify the user authentification based on id
 * @param id the user's id
 * @param password the user's password (already hashed)
 * @return the user object or null if authentification failed
 */
function check_auth_id($id, $password) {
    $db = \Db::dbc();
    
    $query_str = "SELECT idUser, username, name, mail, password, avatar
    			  FROM user
    			  WHERE idUser = :idUser AND password = :password;";
    
	try {
		$query = $db->prepare($query_str);
		$query->bindValue(':idUser', $id, PDO::PARAM_INT);
		$query->bindValue(':password', $password, PDO::PARAM_STR);
        $query->execute();
        $data = $query->fetch();
        

        if ($data == NULL)
            return NULL;

        $o = (object) array(
            "id" => $data['idUser'],
            "username" => $data['username'],
            "name" => $data['name'],
            "email" => $data['mail'],
            "password" => $data['password'],
            "avatar" => $data['avatar']
        );
        return $o;
    }
    catch(PDOException $e) {
        echo $e;
        return NULL;
    }
}

/**
 * Follow another user
 * @param id the current user's id
 * @param id_to_follow the user's id to follow
 */
function follow($id, $id_to_follow) {
	$db = \Db::dbc();
	$query = $db->prepare("INSERT INTO follow(idUser_to, idUser_from) VALUES(:id, :id_to_follow)");
	$query->bindValue(":id", $id);
	$query->bindValue(":id_to_follow", $id_to_follow);
	$query->execute();
}

/**
 * Unfollow a user
 * @param id the current user's id
 * @param id_to_follow the user's id to unfollow
 */
function unfollow($id, $id_to_unfollow) {
	$db = \Db::dbc();
	$query = $db->prepare("DELETE FROM follow WHERE idUser_to = :id AND idUser_from = :id_to_unfollow");
	$query->bindValue(":id", $id);
	$query->bindValue(":id_to_unfollow", $id_to_unfollow);
	$query->execute();
}
