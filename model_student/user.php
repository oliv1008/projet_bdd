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
function get($id) {

  SELECT * FROM UTILISATEUR WHERE $id = IDUTILISATEUR;
    return (object) array(
        "id" => ,
        "username" => ,
        "name" => ,
        "password" => ,
        "email" => ,
        "avatar" =>
    );
}

function get($id) {

    $db = \Db::dbc();
    $query = $db->prepare('SELECT * FROM User WHERE IDUTILISATEUR = :idParam');

    $query->bindValue(':idParam', $id);

    try {
        $query->execute();
        $data = $query->fetchAll(PDO::FETCH_ASSOC);

        if ($data == NULL)
            return NULL;

        $o = (object) array(
            "id" => $data[0]['idUser'],
            "username" => $data[0]['username'],
            "name" => $data[0]['displayName'],
            "password" => $data[0]['pwd'],
            "email" => $data[0]['email'],
            "avatar" => $data[0]['avatar']
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
    return 1337;
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param username the user's username
 * @param name the user's name
 * @param email the user's email
 * @warning this function doesn't need to check whether a user with a similar username exists
 */
function modify($uid, $username, $name, $email) {
}

/**
 * Modify a user in db
 * @param uid the user's id to modify
 * @param new_password the new password
 * @warning this function has to hash the password
 */
function change_password($uid, $new_password) {
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
function destroy($id) {
}

/**
 * Hash a user password
 * @param password the clear password to hash
 * @return the hashed password
 */
function hash_password($password) {
    return $password;
}

/**
 * Search a user
 * @param string the string to search in the name or username
 * @return an array of find objects
 */
function search($string) {
    return [get(1)];
}

/**
 * List users
 * @return an array of the objects of every users
 */
function list_all() {
    return [get(1)];
}

/**
 * Get a user from its username
 * @param username the searched user's username
 * @return the user object or null if the user doesn't exist
 */
function get_by_username($username) {
    return get(1);
}

/**
 * Get a user's followers
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followers($uid) {
    return [get(2)];
}

/**
 * Get the users our user is following
 * @param uid the user's id
 * @return a list of users objects
 */
function get_followings($uid) {
    return [get(2)];
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
    return null;
}

/**
 * Verify the user authentification based on id
 * @param id the user's id
 * @param password the user's password (already hashed)
 * @return the user object or null if authentification failed
 */
function check_auth_id($id, $password) {
    return null;
}

/**
 * Follow another user
 * @param id the current user's id
 * @param id_to_follow the user's id to follow
 */
function follow($id, $id_to_follow) {
}

/**
 * Unfollow a user
 * @param id the current user's id
 * @param id_to_follow the user's id to unfollow
 */
function unfollow($id, $id_to_unfollow) {
}
