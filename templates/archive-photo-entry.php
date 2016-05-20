<?php get_header(); ?>

<?php do_action( 'bppc_before_main_content' ); ?>

<div class="hentry">
	<header class="entry-header">
		<?php the_archive_title( '<h1>', '</h1>' ); ?>
	</header>
	
	<div class="entry-content">
		<?php if( have_posts() ): ?>
			<p style="text-align: center;"><?php _e( 'Click on the photo below to see details & vote.' ); ?></p>
		
			<div class="bppc-grid"><!--
				<?php while( have_posts() ): the_post(); ?>
					--><div class="bppc-photo-entry">
						<a href="<?php the_permalink(); ?>" class="bppc-photo-link">
							<?php 
								the_post_thumbnail( 'medium', array(
									'class'	=> 'bppc-photo',
								) ); 
							?>
						</a>
						
						<h4 class="bppc-photo-title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h4>
						
						<small><?php
							$vote_count = get_post_meta( get_the_ID(), 'bppc_votes', true );
							
							if( 0 == $vote_count ) {
								_e( '(No votes yet)' );
							}
							elseif( 1 == $vote_count ) {
								_e( '(1 vote)' );
							}
							else {
								printf( __( '(%s votes)' ), $vote_count );
							}
						?></small>
					</div><!--
				<?php endwhile; ?>
			--></div>
			
			<?php 
				the_posts_pagination( array(
					'prev_text'	=> __( '&larr;' ),
					'next_text'	=> __( '&rarr;' ),
				) );
			?>
		<?php else: ?>
			<p><?php _e( 'No photo entries found for this month.' ); ?></p>
		<?php endif; ?>
	</div>
</div>

<?php do_action( 'bppc_after_main_content' ); ?>

<?php get_footer(); ?>