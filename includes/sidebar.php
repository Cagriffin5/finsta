<aside class="sidebar flex grow one two-500 three-800">
		<?php
		//get the five most recently registerd Users	
		$result = $DB->prepare('SELECT username, profile_pic
								FROM users
								ORDER BY join_date DESC
								LIMIT 5');
		$result->execute();
		if( $result->rowCount() > 0 ){
		?>
		<section class="users">
				<h2>Newest Users</h2>

				<?php while( $row = $result->fetch() ){ ?>
				<a href="#">
					<img class="profile-pic" src="<?php echo $row['profile_pic']; ?>" alt="USERNAME" width="50" height="50">
					<?php echo $row['username']; ?>
				</a>
				<?php }//end while?>
			</section>

			<section class="categories">
				<?php  //get all category names and a count of how many posts
			
				$result = $DB->prepare('SELECT categories.*, COUNT(*) AS post_count
										FROM categories, posts
										WHERE categories.category_id = posts.category_id
										GROUP BY posts.category_id
										ORDER BY RAND() ');
				$result->execute();
				if( $result->rowCount() > 0 ){
					?>
				<h2>Categories</h2>
				<?php while( $row = $result->fetch()){
					extract($row);
					 ?>
				<a href='#' class='pseudo button'><?php echo "$name ($post_count)"; $name ?></a> 
				<?php } ?>
				<?php } //end of categories?>
			</section>
			<?php }// end of users  ?>
			<section class="meta">
				<h2>Fine Print</h2>
				<div>
					<a href="#" class="pseudo button">Terms of Service</a>
					<a href="#" class="pseudo button">About Finsta</a>
					<a href="#" class="pseudo button">Contact</a>
				</div>
			</section>

		</aside>	