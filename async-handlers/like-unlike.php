<?php  
//
require('../config.php');
require_once('../includes/functions.php');


$post_id = filter_var($_POST['postId'], FILTER_SANITIZE_NUMBER_INT );
$user_id = filter_var( $_POST['userId'], FILTER_SANITIZE_NUMBER_INT );

//does that user like that post or not?
$result = $DB->prepare("SELECT * FROM likes
                                WHERE user_id = ?
                                AND post_id = ?
                                LIMIT 1");
$result->execute( array( $user_id, $post_id ) );
if( $result->rowCount() >= 1 ){
	//the user previously liked this post. DELETE the like
	$query = "DELETE FROM likes
				WHERE user_id = :user_id
				AND post_id = :post_id";
}else{
	//the user didn't previously like it. ADD the like
	$query = "INSERT INTO likes
				(user_id, post_id, date)
				VALUES
				( :user_id, :post_id, now() )";
}

//run the resulting query
$result = $DB->prepare( $query );
$result->execute( array(
					'user_id' => $user_id,
					'post_id' => $post_id
				) );

//if it worked, update the like interface

if( $result->rowCount() >= 1 ){
	like_interface( $post_id, $user_id );
}else{
	//TODO: remove this after testing
	echo 'failed.';
}