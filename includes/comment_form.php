<section class="comment_form">
    <h2>Leave a Comment.</h2>
    <?php show_feedback( $feedback, $errors, $feedback_class); ?>
    <form action="single.php?post_id=<?php echo $post_id; ?>" method="post">
        <label>Your Comment</label>
        <textarea name="body"></textarea>


        <input type="submit">
        <input type="hidden" name="did_comment" value="1">
    </form>
</section>