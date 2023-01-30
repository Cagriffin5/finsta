<?php 
$errors = array();
$feedback = '';
$feedback_class = '';
//if the user submitted the form, parse it
if ( isset($_POST['did_comment'])){
    //sanitize everything.
    $body = clean_string ( $_POST['body'] );
    //validate.
    $valid = true;
    //if the comment is blank or longer than var char limit no validate.  
    if( $body == '' OR strlen($body) > 300 ){
        $valid = false;
        $errors[] = 'Comments must be between 300 characters long.';
    }
    //if valid, add the comment to the database.
    if( $valid ){
        //ADD TO DB
        $result = $DB->prepare('INSERT INTO comments
                                ( body, post_id, user_id, date, is_approved )
                                VALUES
                                (:body, :post_id, :user_id, now(), 1 )
                                ');
        //run it 
        // TODO make user id real
        $result->execute( array( 
                'body' => $body,
                'post_id' => $post_id,
                'user_id' => $logged_in_user['user_id'] 
        ) );

//debug_statement($result);

        if( $result->rowCount() > 0 ){ 
            //success
            $feedback = 'Thank you for your comment';
            $feedback_class = 'success';
        }else{
            //error
            $feedback = 'Your comment was not posted';
            $feedback_class = 'error';
        }

    }else{
        //error. invalid
        $feedback = 'Invalid Comment';
        $feedback_class = 'error';
    }
    //show user feedback. 
}//end parser