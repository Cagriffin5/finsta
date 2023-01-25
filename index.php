<?php require_once('config.php');
require_once('includes/functions.php');
require('includes/header.php');?>
		<main class="content">
			<div class="posts-container flex one two-600 three-900">
				<?php 
				//write it 
				//get the 20 most recent published post
				$result = $DB->prepare('SELECT posts.*, categories.*, users.profile_pic, users.	username, users.user_id
										FROM posts, users, categories
										WHERE posts.is_published = 1
										AND posts.user_id = users.user_id
										AND posts.category_id = categories.category_id
										ORDER BY posts.date DESC
										LIMIT 21'); 
				//run it 
				$result->execute();
				//check it 
				if($result->rowCount() > 0 ){
				//loop it 
				while( $post = $result->fetch() ){
					// print_r($post);
				
				?>
				<article class="post">
					<div class="card">
						<div class="post-image-header">
							<a href="single.php?post_id=<?php echo $post['post_id'];?>">
								<img src="<?php echo $post ['image']; ?>" alt='<?php echo $post ['title']; ?>' class='post-image'>
							</a>
						</div>
						<footer>
							<div class="post-header flex two">
								<div class="user four-fifth flex">
									<img src="<?php echo $post['profile_pic'] ?>">
									<span><?php echo $post['username']; ?><span>
								</div>

							</div>
							<h3 class="post-title clamp"><?php echo $post ['title']; ?></h3>
							<p class="post-excerpt clamp"><?php echo $post ['body']; ?></p>
							<div class="flex post-info">
								<span class="category"><?php echo $post['name']; ?></span>
								<div class="comment-count">
									<?php count_comments ($post['post_id']);?></div>							
								<span class="date"><?php echo time_ago( $post ['date']); ?></span>			
							</div>
						</footer>
					</div><!-- .card -->
				</article>
				<?php 
				}//end while
				}else{
					//empy state
					echo '<h2> NO Posts found!</h2>';
			}//end of post query
				?> <!-- .post -->

			</div><!-- .posts-container -->
		</main>
		<?php 
		include('includes/sidebar.php');	
		include('includes/footer.php');