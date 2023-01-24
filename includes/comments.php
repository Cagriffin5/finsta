<?php
 //get all the approved comments on this post. New comments first. 
 $result = $DB->prepare('SELECT comments.body, comments.date, users.profile_pic,                users.username, users.user_id
                         FROM comments, users
                         WHERE comments.user_id = users.user_id
                         AND comments.post_id = ?
                         AND comments.is_approved = 1
                         ORDER BY date DESC');
$result->execute( array ( $post_id ) );
$total = $result->rowCount();
if( $total > 0 ){



 ?>
<section class="comments container">
        <h2><?php echo $total == 1 ? '1 Comment' : "$total Comments"; ?> on this post.</h2>

        <?php while( $row = $result->fetch()){ 
            extract($row);
        ?>
        <div class="card">
            <div>
                <img src="<?php echo $profile_pic; ?>">
                <span><?php echo $username; ?></span>
            </div>
            <footer>
                <p><?php echo $body; ?></p>
                <span class="date"><?php nice_date( $date ); ?></span>
            </footer>
        </div>
        <?php }//end while ?>
    </section>
<?php }//end if ?>