<?php 
require_once('config.php');
require_once('includes/functions.php');
if( ! check_login() ){
    header('Location:404.php');
}

require('includes/header.php');
require('includes/parse-edit-post.php');?>
	<main class="content">
	<div class="flex one two-700 reverse">
        <section class="preview-image">     
           <img src="uploads/<?php echo $image; ?>_medium.jpg"> 
        </section>
        <section class="edit-form">
            <h2>Edit Post</h2>
        <?php show_feedback( $feedback, $errors, $feedback_class ); ?>
            <form method="post" action="edit-post.php?post_id=<?php echo $post_id; ?>">
                <label>Title</label>
                <input type="text" name="title" value="<?php echo $title; ?>">
                <label>Caption</label>
                <textarea name="body"><?php echo $body; ?></textarea>
                <label>Category</label>

				<?php category_dropdown(); ?>
                
                <label>
                    <input type="checkbox" name="allow_comments" value="1">
                    <span class="checkable">Allow Comments</span>
                    
                </label>
            
                <label>
                    <input type="checkbox" name="is_published" value="1" >
                    <span class="checkable">Make this post public</span>
                </label>

                <input type="submit" value="Save Post">
                <input type="hidden" name="did_edit" value="1">
            </form>
        </section>
    </div>    
			
	</main>

      
		<?php 
		include('includes/sidebar.php');
		include('includes/footer.php');