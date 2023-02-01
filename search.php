<?php require_once('config.php');
require_once('includes/functions.php');

 //how many pages will it take 
 $per_page = 10;

$phrase = '';

//sanitize the search phrase 
if( isset($_GET['phrase']) ){
    $phrase = clean_string( $_GET['phrase'] );
}

require('includes/header.php');
?>
<main class="content">
	<div class="posts-container flex three four-600 five-900">
		<?php 
				//write it 
				//get 10 public posts that matched the phrase
                $query = 'SELECT * FROM posts
                WHERE is_published = 1
                AND
                ( title LIKE :phrase
                OR body LIKE :phrase )
                ORDER BY date DESC';
				$result = $DB->prepare( $query ); 
				//run it 
				$result->execute( array( 'phrase' => "%$phrase%" ) );
                $total = $result->rowCount();

               
                // ceil = allways round up
                $total_pages = ceil( $total / $per_page );
                //what page are we on 
                $current_page = 1;
                //if there is a page set in the URL, use it!
                if( isset($_GET['page']) ){
                    $current_page = filter_var( $_GET['page'], FILTER_SANITIZE_NUMBER_INT );
                }
                //validate the page number 
                if( $current_page < 1 OR $current_page > $total_pages  ){
                    $current_page = 1;
                }

                //calculate the offset for the limit 
                $offset = ( $current_page - 1 ) * $per_page;

                //write the query again but with a limit applied
                $query .= ' LIMIT :offset, :per_page';
                
                $result = $DB->prepare($query);
                //binding the params because LIMIT requires integers and not strings
                $wildcard_phrase = "%$phrase%";

                $result->bindParam( 'phrase', $wildcard_phrase, PDO::PARAM_STR );
                $result->bindParam( 'offset', $offset,          PDO::PARAM_INT );
                $result->bindParam( 'per_page', $per_page,      PDO::PARAM_INT );
                //run it again
                $result->execute();

                ?>

                <section class="full">
                    <h2>Search Results for <?php echo $phrase; ?></h2>
                    <h3><?php echo $total == 1 ? '1 post found' : "$total posts found"; ?> </h3>
                    <h3>Showing page <?php echo $current_page ?> of <?php echo $total_pages; ?>.</h3>
                </section>

                <?php
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
                             <?php show_post_image( $post['image'], 'small', $post['title'] ); ?>
						  </a>
						</div>
						<footer>
							
							<h3 class="post-title clamp"><?php echo $post ['title']; ?></h3>
							<p class="post-excerpt clamp"><?php echo $post ['body']; ?></p>
							<div class="flex post-info">
								
								<div class="comment-count">
									<?php count_comments ($post['post_id']);?></div>							
								<span class="date"><?php echo time_ago( $post ['date']); ?></span>			
							</div>
						</footer>
					</div><!-- .card -->
				</article>
				<?php 
				}//end while
                $prev = $current_page - 1;
                $next = $current_page + 1;
                ?>
                <section class="pagination full">
                    <?php if( $current_page != 1 ){ ?>
                    <a href="search.php?phrase=<?php echo $phrase; ?>&amp;page=<?php echo $prev; ?>" class="button">&larr; PREVIOUS</a>
                    <?php } ?>

                        <?php if($current_page != $total_pages){ ?>
                    <a href="search.php?phrase=<?php echo $phrase; ?>&amp;page=<?php echo $next; ?>" class="button">NEXT &rarr;</a>
                        <?php } ?>

                </section>
                <?php
			}//end if
				?> <!-- .post -->

			</div><!-- .posts-container -->
		</main>
		<?php 
		include('includes/sidebar.php');	
		include('includes/footer.php');