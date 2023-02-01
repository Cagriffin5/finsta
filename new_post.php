<?php 
require_once('config.php');
require_once('includes/functions.php');
if( ! check_login() ){
    header('Location:404.php');
}

require('includes/header.php');
require('includes/parse-upload.php');?>
		<main class="content">
            <h2>New Post</h2>
            <?php show_feedback( $feedback, $errors, $feedback_class); ?>
            <form method="post" action="new_post.php" enctype="multipart/form-data">
                <label class="dropimage">Upload a .jpg, .gif or .png image
                    <input type="file" name="uploadedfile" accept="image/*" required>
                </label>
                    <input type="submit" value="upload Image">
                    <input type="hidden" name="did_upload" value="1">
            </form>
			
		</main>

        <!--script from picnic.css -->
        <script type="text/javascript">
          document.addEventListener("DOMContentLoaded", function() {
  [].forEach.call(document.querySelectorAll('.dropimage'), function(img){
    img.onchange = function(e){
      var inputfile = this, reader = new FileReader();
      reader.onloadend = function(){
        inputfile.style['background-image'] = 'url('+reader.result+')';
      }
      reader.readAsDataURL(e.target.files[0]);
    }
  });
});  
        </script>
		<?php 
		include('includes/sidebar.php');
		include('includes/footer.php');