<?php 
//pre define vars
$feedback = '';
$feedback_class = '';
$errors = array();

//which post are we editing. 
//URL will be edit-post.php?post_id=x
if( isset($_GET['post_id'])){
    $post_id = filter_var($_GET['post_id'], FILTER_SANITIZE_NUMBER_INT );
}else{
  header('Location:404.php');  
}

//if they submitted, parse it!
if( isset($_POST['did_edit'])){
    //sanitize everything
    $title = clean_string( $_POST['title'] );
    $body =  clean_string( $_POST['body'] );
    $category_id = filter_var( $_POST['category_id'], FILTER_SANITIZE_NUMBER_INT );
    $allow_comments = isset( $_POST['allow_comments'] ) ? 1 : 0 ;
    $is_published = isset( $_POST['is_published'] ) ? 1 : 0 ;

    //validate
    $valid = true;

    //title blank or too long
    if( $title == '' OR strlen($title) > 100 ){
        $valid = false;
        $errors['title'] = 'Title must be between 1 - 100 characters long.';
    }
    //body too long
    if( strlen($body) > 300 ){
        $valid = false;
        $errors['body'] = 'Body must be fewer than 300 characters long.';
    }
    //category must be a posotive INT
    if( $category_id < 1 ){
        $valid = false;
        $errors['category_id'] = 'Choose a category.';

    }
    //if valid, update the row in the DB
    if( $valid ){
        $result = $DB->prepare( 'UPDATE posts
                                 SET 
                                    title = :title,
                                    body = :body,
                                    category_id = :cat,
                                    allow_comments = :ac,
                                    is_published = :ip
                                WHERE post_id = :post_id
                                LIMIT 1
                                 ');
        $result->execute( array(
            'title' => $title,
            'body' => $body,
            'cat' => $category_id,
            'ip' => $is_published,
            'ac' => $allow_comments,
            'post_id' => $post_id
        ) );
        if( $result->rowCount() ){
            $feedback = 'Successfully Saved';
            $feedback_class = 'success';
        }else{
            $feedback = 'No changes were made.';
            $feedback_class = 'info'; 
        }

    }else{
        //not valid
        $feedback = 'Fix the following';
        $feedback_class = 'error';
    }


}//end of form parser. 


//pre-fill the current values of this content. 
$result = $DB->prepare('SELECT * FROM posts
                        WHERE post_id = :post_id
                        AND user_id = :user_id
                        LIMIT 1
                        ');
$result->execute( array( 'post_id' => $post_id,
                         'user_id' => $logged_in_user['user_id'],
                          ));
if( $result->rowCount() ){
    $row = $result->fetch();
    extract($row);
}else{
    //error - invalid post or not the author. 
    exit('You are not allowed to edit this post.');
}