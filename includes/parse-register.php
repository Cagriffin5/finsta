<?php  
$feedback = '';
$feedback_class = '';
$errors = array();

//if the form was submitted, parse it. 
if( isset($_POST['did_register']) ){
    //sanitize everything
    $username = clean_string( $_POST['username'] );
    $email = filter_var( $_POST['email'], FILTER_SANITIZE_EMAIL );
    $password = clean_string( $_POST['password']);
    if( isset( $_POST['policy'] ) ){
        $policy = 1;
    }else{
        $policy = 0;
    }
    //validate
    $valid = true;
    //username isnt 5-30 chars long
    if( strlen($username) < USERNAME_MIN OR strlen($username) > USERNAME_MAX ){
        $valid = false;
        $errors['username'] = 'Username must be between ' . USERNAME_MIN . ' and ' . USERNAME_MAX . ' characters long';
    }else{
      //username must be unique \
      $result = $DB ->prepare('SELECT username
                                FROM users
                                WHERE username = ?
                                LIMIT 1 
                                ');
     $result->execute( array( $username));
    //if found username is not valid 
    if($result->rowCount() > 0 ){
        $valid = false;
        $errors['username'] = 'Username is already taken. ';
        }
                                  
    }//end of username check 

    //invalid email
    if( ! filter_var( $email, FILTER_VALIDATE_EMAIL)){
        $valid = false;
        $errors['email'] = 'Invalid Email Address';
    }else{
        //email must be unique 
        $result = $DB ->prepare('SELECT email
                                FROM users
                                WHERE email = ?
                                LIMIT 1 
                                ');
     $result->execute( array( $email));
    //if found email is not valid 
    if($result->rowCount() > 0 ){
        $valid = false;
        $errors['email'] = 'Email is already taken. ';
        }

    }//end of email check 
    
    //password to short ( < 8 )
    if( strlen($password) < PASSWORD_MIN ){
        $valid = false; 
        $errors['password'] = 'Your password needs to be atleast ' . PASSWORD_MIN . ' characters.';
    }
    //policy not checked
    if( ! $policy ){
        $valid = false; 
        $errors['policy'] = 'You must agree to the terms of service to sign up.';
    }
    //if valid add the user to the database 
    if( $valid ){
        $letter = $username[0];
        $profile_pic = make_letter_avatar($letter, 100);


        $result = $DB->prepare('INSERT INTO users
                                (username, email, password, profile_pic, is_admin, join_date) 
                                VALUES
                                ( :username, :email, :pass, :pic, 0, now() )
                                ');
        $result->execute( array(
                            'username' => $username,
                            'email'    => $email,
                            'pass'     => password_hash( $password, PASSWORD_DEFAULT ),
                            'pic'      => $profile_pic
                                ) );
            if( $result->rowCount() > 0  ){
                $feedback = 'Success!';
                $feedback_class = 'success';
            }else{
                $feedback = 'Database Error';
                $feedback_class = 'error'; 
            }
    }//end if valid
    else{
        $feeback = 'There were problems, Fix the following.';
        $feedback_class = 'error';
    }

   
}//end parse