<?php 
$feedback = '';
$feedback_class = '';
 //logout behavior
if( isset($_GET['action']) AND $_GET['action'] == 'logout'){
	//expire all cookies
	setcookie( 'logged_in', 0, time() - 9999 );
	setcookie('username', 0, time() - 9999);

	//unset all sessions vars
	$_SESSION = array();

	if (ini_get("session.use_cookies")) {
		$params = session_get_cookie_params();
		setcookie(session_name(), '', time() - 42000,
			$params["path"], $params["domain"],
			$params["secure"], $params["httponly"]
		);
	}

	//take care of seesion id

	session_destroy();
}//end of logout



//if the login  is filled in, parse the form.
if( isset($_POST['did_login'])){
	$username =  clean_string($_POST['username']);
	$password = clean_string($_POST['password']);

	//validate
	$valid = true; 
	//username wrong length 
	if( strlen($username) < USERNAME_MIN or strlen($username) > USERNAME_MAX){
		$valid = false;
	}
	//password wrong length 
	if( strlen($password) < PASSWORD_MIN){
		$valid = false;
	}
	//if valid look up in the DB
	if( $valid ){
		//look up the username
		$result = $DB -> prepare('SELECT user_id, password
								FROM users
								WHERE username = ?
								LIMIT 1');
		$result->execute( array( $username) );
		//check it - if one row is found check the password. 
		if( $result->rowCount() > 0 ){
			$row = $result->fetch();
			//check the hashed pasword
			if( password_verify( $password, $row['password'] ) ){
				//success
				$feedback = 'Success';
				$feedback_class = 'success';

				//remember user for the next two weeks
				//generate a secret key 
				$access_token = bin2hex( randon_bytes(30) );
				$expire = time() + 60 * 60 *24 * 14;
				setcookie( 'access_token', $access_token, $expire  );
				//@TODO! 
			}else{
				//bad password
				$feedback = 'Incorrect Password';
				$feedback_class = 'error';
			}
		}else{
			//username not found 
			$feedback = 'User does not exist';
			$feedback_class = 'error';
		}
	}else{
		//invalid
		$feedback = 'Invalid log-in';
		$feedback_class = 'error';
	}

}//end form parse