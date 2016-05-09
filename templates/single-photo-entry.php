<?php get_header(); ?>

<?php do_action( 'bppc_before_main_content' ); ?>

<?php while( have_posts() ): the_post(); ?>
	<article <?php post_class(); ?>>
		<p class="bppc-photo">
			<?php the_post_thumbnail(); ?>
		</p>

		<div class="bppc-grid">
			<div class="bppc-main">
				<h1><?php the_title(); ?></h1>
				
				<div class="bppc-description">
					<?php the_content(); ?>
				</div>
			</div>
			
			<div class="bppc-actions">
				<div class="bppc-voting">
					<?php echo do_shortcode( '[voting_form]' ); ?>
					
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

				</div>
			</div>
		</div>
	</article>
<?php endwhile; ?>

<?php do_action( 'bppc_after_main_content' ); ?>

<?php get_footer(); ?>