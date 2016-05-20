<?php get_header(); ?>

<?php do_action( 'bppc_before_main_content' ); ?>

<?php while( have_posts() ): the_post(); ?>
	<article <?php post_class(); ?>>
		<header class="entry-header">
			<h1 class="entry-title"><?php the_title(); ?></h1>
		</header>

		<p class="bppc-photo">
			<?php the_post_thumbnail( 'full' ); ?>
		</p>
		
		<div class="voting-form">
			<?php echo do_shortcode( '[voting_form]' ); ?>
			
			<small class="vote-count"><?php
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
		
		<div class="entry-content" style="text-align: center;">
			<?php the_content(); ?>
		</div>
	</article>
<?php endwhile; ?>

<?php do_action( 'bppc_after_main_content' ); ?>

<?php get_footer(); ?>