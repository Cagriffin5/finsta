<?php 
require_once('config.php');
require_once('includes/functions.php');


//which post are we trying to show and is it valid. 
$post_id = filter_var ($_GET['post_id'], FILTER_SANITIZE_NUMBER_INT );
//make sure we get a posotive integer
if( $post_id < 0 ){
    $post_id = 0;
}

require('includes/header.php');
require('includes/parse-comment.php');?>
<main class="content">
    <?php 
    //get the one post we are viewing
    //WRITE IT
    $result = $DB->prepare('SELECT posts.*, categories.*, users.profile_pic, users.	username, users.user_id
    FROM posts, users, categories
    WHERE posts.is_published = 1
    AND posts.user_id = users.user_id
    AND posts.category_id = categories.category_id
    AND posts.post_id = ?
    LIMIT 1');
    //RUN IT 
    $result->execute( array( $post_id ) );
    //CHECK IT 
    if( $result->rowCount() == 1){
            while( $row = $result->fetch() ){
                extract($row);
            
        ?>

    <article class="post">
        <div class="card flex one two-700">
            <div class="post-image-header two-third-700">
                <img src="<?php echo $image; ?>">
            </div>

            <footer class="third-700">
                <div class="flex two post-header">
                    <div class="user four-fifth flex">
                            <img src="<?php echo $profile_pic; ?>">
                            <span><?php echo $username; ?></span>
                        </div>
                    
                </div>
                <h3><?php echo $title; ?></h3>
                <p><?php echo $body; ?></p>


                <div class="flex">
                    <span class="category"><?php echo $name; ?></span>
                    <span class="comment-count"><?php echo count_comments( $post_id ); ?></span>
                    <span class="date"><?php echo time_ago($date); ?></span>          
                </div>
            </footer>
        </div><!-- .card -->
    </article> <!-- .post -->
    <?php 
        include('includes/comments.php');
        //comment form IF this post allows comments and the user is logged in  
        if( $allow_comments AND $logged_in_user ){
            include('includes/comment_form.php');
        }
            }//end while
    }else{
        echo '<h2>Sorry, no post found.</h2>';
    }
    ?>
</main>
<?php 
include('includes/sidebar.php');	
include('includes/footer.php');      